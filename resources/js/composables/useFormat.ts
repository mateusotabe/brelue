import {
    formatCurrency,
    formatDate,
    formatDateTime,
    formatNumber,
    formatPercent,
    formatRelativeTime,
    formatTime,
} from '@/lib/format';

export type UseFormatReturn = {
    formatDate: typeof formatDate;
    formatDateTime: typeof formatDateTime;
    formatTime: typeof formatTime;
    formatNumber: typeof formatNumber;
    formatCurrency: typeof formatCurrency;
    formatPercent: typeof formatPercent;
    formatRelativeTime: typeof formatRelativeTime;
};

/**
 * Expõe os helpers de formatação pt-BR para uso em componentes Vue.
 *
 * Exemplo:
 *   const { formatCurrency, formatDate } = useFormat();
 *   formatCurrency(1234.5); // R$ 1.234,50
 */
export function useFormat(): UseFormatReturn {
    return {
        formatDate,
        formatDateTime,
        formatTime,
        formatNumber,
        formatCurrency,
        formatPercent,
        formatRelativeTime,
    };
}
