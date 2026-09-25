<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'scheduled_by',
        'appointment_date',
        'appointment_time',
        'service_type',
        'purpose',
        'status',
        'status_notes',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function scheduledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scheduled_by');
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('status', 'scheduled')
            ->whereDate('appointment_date', '>=', Carbon::today())
            ->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc');
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->where('status', 'scheduled')
            ->whereDate('appointment_date', Carbon::today())
            ->orderBy('appointment_time', 'asc');
    }

    public function scopeMissedOrOverdue(Builder $query): Builder
    {
        return $query->where('status', 'scheduled')
            ->whereDate('appointment_date', '<', Carbon::today());
    }
}
