<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'item_code',
        'name',
        'generic_name',
        'category',
        'dosage_form',
        'unit_of_measure',
        'minimum_stock_alert',
        'is_cold_chain',
        'storage_temperature_note',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'is_cold_chain' => 'boolean',
            'minimum_stock_alert' => 'integer',
        ];
    }

    public function batches(): HasMany
    {
        return $this->hasMany(InventoryBatch::class);
    }

    public function activeBatches(): HasMany
    {
        return $this->hasMany(InventoryBatch::class)
            ->where('status', 'active')
            ->where('current_quantity', '>', 0)
            ->orderBy('expiration_date', 'asc'); // FIFO
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class)->latest('transaction_date');
    }

    public function getTotalStockAttribute(): int
    {
        return (int) $this->batches()
            ->where('status', 'active')
            ->sum('current_quantity');
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->total_stock <= $this->minimum_stock_alert;
    }

    public function scopeMedicines(Builder $query): Builder
    {
        return $query->where('category', 'medicine');
    }

    public function scopeVaccines(Builder $query): Builder
    {
        return $query->where('category', 'vaccine');
    }
}
