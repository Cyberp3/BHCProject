<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryBatch extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'inventory_item_id',
        'batch_number',
        'date_received',
        'expiration_date',
        'quantity_received',
        'current_quantity',
        'supplier_or_source',
        'status',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'date_received' => 'date',
            'expiration_date' => 'date',
            'quantity_received' => 'integer',
            'current_quantity' => 'integer',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function getDaysUntilExpirationAttribute(): int
    {
        return (int) Carbon::today()->diffInDays($this->expiration_date, false);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiration_date < Carbon::today();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')->where('current_quantity', '>', 0);
    }

    public function scopeNearExpiry(Builder $query, int $days = 90): Builder
    {
        return $query->where('status', 'active')
            ->where('current_quantity', '>', 0)
            ->whereDate('expiration_date', '>=', Carbon::today())
            ->whereDate('expiration_date', '<=', Carbon::today()->addDays($days))
            ->orderBy('expiration_date', 'asc');
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('current_quantity', '>', 0)
            ->whereDate('expiration_date', '<', Carbon::today());
    }
}
