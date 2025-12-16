<?php
declare(strict_types=1);

namespace App\Enums;

enum AssetSymbol: string
{
    case BTC = 'BTC';
    case ETH = 'ETH';
    case XRP = 'XRP';
    case LTC = 'LTC';
    case BCH = 'BCH';
}
