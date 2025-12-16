export enum AssetSymbol {
    BTC = 'BTC',
    ETH = 'ETH',
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
