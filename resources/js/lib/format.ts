/**
 * Helpers de formatação localizados para o Brasil (pt-BR).
 *
 * Mantêm o frontend alinhado ao backend (APP_LOCALE=pt_BR,
 * APP_TIMEZONE=America/Sao_Paulo).
 */

export const LOCALE = 'pt-BR';
export const TIME_ZONE = 'America/Sao_Paulo';
export const CURRENCY = 'BRL';

type DateInput = Date | string | number;

function toDate(value: DateInput): Date {
    return value instanceof Date ? value : new Date(value);
}

/**
 * Formata uma data: 19/05/2026
 */
export function formatDate(
    value: DateInput,
    options: Intl.DateTimeFormatOptions = {},
): string {
    return new Intl.DateTimeFormat(LOCALE, {
        dateStyle: 'short',
        timeZone: TIME_ZONE,
        ...options,
    }).format(toDate(value));
}

/**
 * Formata data e hora: 19/05/2026 15:46
 */
export function formatDateTime(
    value: DateInput,
    options: Intl.DateTimeFormatOptions = {},
): string {
    return new Intl.DateTimeFormat(LOCALE, {
        dateStyle: 'short',
        timeStyle: 'short',
        timeZone: TIME_ZONE,
        ...options,
    }).format(toDate(value));
}

/**
 * Formata apenas a hora: 15:46
 */
export function formatTime(
    value: DateInput,
    options: Intl.DateTimeFormatOptions = {},
): string {
    return new Intl.DateTimeFormat(LOCALE, {
        timeStyle: 'short',
        timeZone: TIME_ZONE,
        ...options,
    }).format(toDate(value));
}

/**
 * Formata um número: 1.234,56
 */
export function formatNumber(
    value: number,
    options: Intl.NumberFormatOptions = {},
): string {
    return new Intl.NumberFormat(LOCALE, options).format(value);
}

/**
 * Formata um valor monetário em Reais: R$ 1.234,56
 */
export function formatCurrency(
    value: number,
    options: Intl.NumberFormatOptions = {},
): string {
    return new Intl.NumberFormat(LOCALE, {
        style: 'currency',
        currency: CURRENCY,
        ...options,
    }).format(value);
}

/**
 * Formata uma porcentagem: 12,5%
 */
export function formatPercent(
    value: number,
    options: Intl.NumberFormatOptions = {},
): string {
    return new Intl.NumberFormat(LOCALE, {
        style: 'percent',
        maximumFractionDigits: 2,
        ...options,
    }).format(value);
}

/**
 * Formata uma data relativa: "há 3 dias", "em 2 horas"
 */
export function formatRelativeTime(
    value: DateInput,
    base: DateInput = new Date(),
): string {
    const diffMs = toDate(value).getTime() - toDate(base).getTime();
    const rtf = new Intl.RelativeTimeFormat(LOCALE, { numeric: 'auto' });

    const divisions: Array<{
        amount: number;
        unit: Intl.RelativeTimeFormatUnit;
    }> = [
        { amount: 60, unit: 'second' },
        { amount: 60, unit: 'minute' },
        { amount: 24, unit: 'hour' },
        { amount: 7, unit: 'day' },
        { amount: 4.34524, unit: 'week' },
        { amount: 12, unit: 'month' },
        { amount: Number.POSITIVE_INFINITY, unit: 'year' },
    ];

    let duration = diffMs / 1000;

    for (const division of divisions) {
        if (Math.abs(duration) < division.amount) {
            return rtf.format(Math.round(duration), division.unit);
        }

        duration /= division.amount;
    }

    return rtf.format(Math.round(duration), 'year');
}
