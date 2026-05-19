<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('brelue:install')]
#[Description('Configura o .env e o docker-compose: nome do projeto vem do diretório de instalação; banco é perguntado.')]
class InstallBrelue extends Command
{
    private const DEFAULT_DB = 'brelue';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $envPath = base_path('.env');

        if (! file_exists($envPath)) {
            copy(base_path('.env.example'), $envPath);
            $this->components->info('Arquivo .env criado a partir do .env.example.');
        }

        $projectName = $this->resolveProjectName();
        $this->components->info('Nome do projeto: '.$projectName);

        $database = text(
            label: 'Nome do banco de dados',
            placeholder: self::DEFAULT_DB,
            hint: 'Deixe em branco para usar "'.self::DEFAULT_DB.'".',
        ) ?: self::DEFAULT_DB;

        $username = text(
            label: 'Usuário do banco de dados',
            placeholder: self::DEFAULT_DB,
            hint: 'Deixe em branco para usar "'.self::DEFAULT_DB.'".',
        ) ?: self::DEFAULT_DB;

        $dbPassword = password(
            label: 'Senha do banco de dados',
            hint: 'Deixe em branco para usar "'.self::DEFAULT_DB.'".',
        ) ?: self::DEFAULT_DB;

        $this->writeEnv($envPath, [
            'APP_NAME' => $projectName,
            'DB_CONNECTION' => 'pgsql',
            'DB_HOST' => 'postgres',
            'DB_PORT' => '5432',
            'DB_DATABASE' => $database,
            'DB_USERNAME' => $username,
            'DB_PASSWORD' => $dbPassword,
        ]);
        $this->components->info('.env atualizado.');

        $this->writeDockerCompose(base_path('docker-compose.yml'), $database, $username, $dbPassword);
        $this->components->info('docker-compose.yml atualizado.');

        $this->printNextSteps();

        return self::SUCCESS;
    }

    /**
     * Deriva o nome do projeto do diretório de instalação (ex.: "meu-projeto" -> "Meu Projeto").
     * Fallback: "Brelue".
     */
    private function resolveProjectName(): string
    {
        $dir = basename(base_path());

        if ($dir === '' || $dir === '.' || $dir === '/') {
            return 'Brelue';
        }

        $name = trim((string) preg_replace('/[-_]+/', ' ', $dir));
        $name = preg_replace('/\s+/', ' ', $name);

        if ($name === '' || strcasecmp($name, 'brelue') === 0) {
            return 'Brelue';
        }

        return mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Define ou substitui chaves no arquivo .env (inclusive linhas comentadas).
     *
     * @param  array<string, string>  $values
     */
    private function writeEnv(string $path, array $values): void
    {
        $contents = file_get_contents($path);

        foreach ($values as $key => $value) {
            $line = $key.'='.$this->escapeEnvValue($value);
            $pattern = '/^#?\s*'.preg_quote($key, '/').'=.*$/m';

            if (preg_match($pattern, $contents)) {
                $contents = preg_replace($pattern, $line, $contents, 1);
            } else {
                $contents = rtrim($contents, "\n")."\n".$line."\n";
            }
        }

        file_put_contents($path, $contents);
    }

    /**
     * Envolve o valor em aspas duplas quando contém espaços ou caracteres especiais.
     */
    private function escapeEnvValue(string $value): string
    {
        if (preg_match('/\s|["\'#=]/', $value)) {
            return '"'.str_replace('"', '\"', $value).'"';
        }

        return $value;
    }

    /**
     * Sincroniza as credenciais do banco no serviço postgres e no serviço php.
     */
    private function writeDockerCompose(string $path, string $database, string $username, string $dbPassword): void
    {
        if (! file_exists($path)) {
            return;
        }

        $contents = file_get_contents($path);

        $contents = preg_replace('/POSTGRES_DB:\s*.*/', 'POSTGRES_DB: '.$database, $contents, 1);
        $contents = preg_replace('/POSTGRES_USER:\s*.*/', 'POSTGRES_USER: '.$username, $contents, 1);
        $contents = preg_replace('/POSTGRES_PASSWORD:\s*.*/', 'POSTGRES_PASSWORD: '.$dbPassword, $contents, 1);

        $phpDbEnv = <<<YAML
      # DB (PostgreSQL)
      DB_CONNECTION: pgsql
      DB_HOST: postgres
      DB_PORT: 5432
      DB_DATABASE: {$database}
      DB_USERNAME: {$username}
      DB_PASSWORD: {$dbPassword}
YAML;

        $contents = preg_replace(
            '/ {6}# DB \(PostgreSQL\)\n( {6}#?\s*DB_[A-Z_]+:.*\n)+/',
            $phpDbEnv."\n",
            $contents,
            1
        );

        file_put_contents($path, $contents);
    }

    private function printNextSteps(): void
    {
        $this->newLine();
        $this->components->info('Instalação concluída. O migrate NÃO foi executado.');
        $this->line('  Próximos passos:');
        $this->newLine();
        $this->line('  <fg=cyan>1.</> Suba os containers:        <fg=yellow>docker compose up -d</>');
        $this->line('  <fg=cyan>2.</> Rode as migrations:        <fg=yellow>docker compose exec php php artisan migrate</>');
        $this->line('  <fg=cyan>3.</> Instale o front-end:       <fg=yellow>pnpm install</>');
        $this->line('  <fg=cyan>4.</> Suba o Vite (dev):         <fg=yellow>pnpm run dev</>');
        $this->newLine();
        $this->line('  App: <fg=green>http://localhost:8000</>  •  Vite: <fg=green>http://localhost:5173</>');
        $this->newLine();
    }
}
