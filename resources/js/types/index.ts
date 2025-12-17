export enum AssetSymbol {
    BTC = 'BTC',
    ETH = 'ETH',
    XRP = 'XRP',
    LTC = 'LTC',
    BCH = 'BCH',
}

export enum OrderStatus {
    OPEN = 1,
    FILLED = 2,
    CANCELLED = 3,
}

export interface Asset {
    symbol: AssetSymbol
    amount: number
    locked_amount: number
}

export interface User {
    id: number
    name: string
    email: string
    assets: Asset[]
    balance: number
}

export interface Order {
    id: number
    user_id: number
    symbol: AssetSymbol
    amount: number
    price: number
    side: 'buy' | 'sell'
    status: OrderStatus
    created_at: string
    updated_at: string
}
