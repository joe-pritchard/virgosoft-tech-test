export enum AssetSymbol {
    BTC = 'BTC',
    ETH = 'ETH',
    XRP = 'XRP',
    LTC = 'LTC',
    BCH = 'BCH',
}

export interface Asset {
    symbol: AssetSymbol
    amount: number
}

export interface User {
    name: string
    email: string
    assets: Asset[]
    balance: number
}
