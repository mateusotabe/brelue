# brelue

> Esqueleto **Laravel 13 + Inertia/Vue 3** limpo, dockerizado e **localizado para o Brasil** (pt-BR), pronto para iniciar novos projetos.

**PHP 8.5 · Nginx · PostgreSQL 16 · Vite · pnpm**

---

## ✨ O que já vem configurado

- **Localização pt-BR** — `APP_LOCALE=pt_BR`, `APP_TIMEZONE=America/Sao_Paulo`, `faker` pt-BR e traduções em `lang/pt_BR/` (validação, auth, senhas, paginação) + `lang/pt_BR.json`.
- **Formatação no frontend** — `resources/js/lib/format.ts` e o composable `useFormat` para datas, números, moeda (R$), porcentagem e tempo relativo no padrão brasileiro.
- **Docker Compose** — nginx + PHP-FPM 8.5 (Dockerfile próprio, com Xdebug em dev) + PostgreSQL 16.
- **Base enxuta** — layouts, páginas e componentes do starter-kit padrão removidos; página inicial em branco e apenas os primitivos de UI reutilizáveis mantidos.
- **Fortify** instalado (rotas de autenticação registradas, sem views — recrie conforme o projeto).

---

## 📦 Stack

- PHP 8.5 (FPM) · Xdebug (dev)
- Nginx (Alpine)
- PostgreSQL 16
- Node.js / pnpm (Vite)
- Composer 2

---

## 🚀 Criar um novo projeto

```bash
composer create-project mateusotabe/brelue meu-app
cd meu-app
```

Os scripts `post-create-project-cmd` já geram a `APP_KEY`, criam o SQLite e rodam as migrações.

> Caso o pacote não esteja no Packagist, adicione o repositório VCS no `composer.json` de destino:
> ```json
> "repositories": [
>   { "type": "vcs", "url": "git@github.com:mateusotabe/brelue.git" }
> ]
> ```

---

## ▶️ Subindo o ambiente com Docker

Requisitos: Docker `>= 24` e Docker Compose `>= 2.0`.

```bash
# 1. Build e start dos containers
docker compose up -d --build

# 2. Arquivo de ambiente (se ainda não existir)
cp .env.example .env

# 3. Dependências PHP
docker compose exec php composer install

# 4. Dependências frontend
docker compose exec php pnpm install

# 5. Chave da aplicação e migrations
docker compose exec php php artisan key:generate
docker compose exec php php artisan migrate

# 6. Vite (hot reload)
docker compose exec php pnpm run dev
```

| Serviço     | URL / Porta                                     |
|-------------|-------------------------------------------------|
| Aplicação   | http://localhost:8000                           |
| Vite (HMR)  | http://localhost:5173                           |
| PostgreSQL  | `localhost:5433` (db/usuário/senha: `laravel`)  |

Para usar PostgreSQL, descomente as variáveis `DB_*` no serviço `php` do `docker-compose.yml` (ou ajuste o `.env`).

---

## 💻 Desenvolvimento sem Docker

```bash
composer install
cp .env.example .env
php artisan key:generate
pnpm install
composer run dev   # serve + queue + logs + vite
```

---

## 🇧🇷 Localização

A configuração regional vive no `.env.example` (replicada em cada novo projeto). As traduções ficam em `lang/pt_BR/`. No frontend:

```ts
import { useFormat } from '@/composables/useFormat';

const { formatCurrency, formatDateTime } = useFormat();

formatCurrency(1234.5);      // "R$ 1.234,50"
formatDateTime(new Date());  // "19/05/2026 15:46"
```

---

## 🧪 Comandos úteis

```bash
docker compose exec php sh                       # shell no container PHP
docker compose logs -f                           # logs
docker compose up -d --build --force-recreate    # rebuild completo
docker compose down                              # parar
docker compose down -v                           # parar e remover volumes
```

---

## 📄 Licença

MIT.
