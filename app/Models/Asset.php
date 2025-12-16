<?php
declare(strict_types=1);

namespace App\Models;

use App\Enums\AssetSymbol;
use Database\Factories\AssetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    /** @use HasFactory<AssetFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'symbol' => AssetSymbol::class,
        'amount' => 'float',
        'locked_amount' => 'float',
    ];

    /**
     * @return BelongsTo<User, self>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
