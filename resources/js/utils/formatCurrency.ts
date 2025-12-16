export const formatCurrency = (
    amount: number,
    options: CurrencyFormatOptions = {},
) => {
    amount = options.negate && amount !== 0 ? -amount : amount

    return new Intl.NumberFormat('en-GB', {
        style: 'currency',
        currency: 'GBP',
        minimumFractionDigits: options.hideDecimals ? 0 : 2,
        maximumFractionDigits: options.hideDecimals ? 0 : 2,
    }).format(amount)
}
