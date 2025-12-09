<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 1;
    public const STATUS_FILLED = 2;
    public const STATUS_CANCELLED = 3;

    protected $fillable = [
        'user_id',
        'symbol',
        'side',
        'price',
        'amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'amount' => 'decimal:8',
            'status' => 'integer',
        ];
    }

    public function scopeForStatus($query, ?int $status)
    {
        if ($status) {
            $query->where('status', $status);
        }

        return $query;
    }

    public function scopeForSide($query, ?string $side)
    {
        if ($side) {
            $query->where('side', $side);
        }

        return $query;
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeForSymbol($query, ?string $symbol)
    {
        if ($symbol) {
            $query->where('symbol', $symbol);
        }

        return $query;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

