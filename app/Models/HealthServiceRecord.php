<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthServiceRecord extends Model
{
    //
    use HasFactory;

    public const SERVICES = [
        'family_planning' => 'Family Planning',
        'prenatal_care' => 'Prenatal Care',
        'immunization' => 'Immunization',
        'cvd_screening' => 'CVD Screening',
        'philpen' => 'PhilPEN',
        'ntp' => 'NTP (National Tuberculosis Program)',
        'purok_kalusugan' => 'Purok Kalusugan',
        'bns_program' => 'BNS Program',
    ];

    protected $fillable = [
        'patient_id',
        'user_id',
        'service_type',
        'service_date',
        'complaint_or_reason',
        'findings_and_notes',
        'service_specific_data',
        'next_follow_up_date',
    ];

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
            'next_follow_up_date' => 'date',
            'service_specific_data' => 'array',
        ];
    }

    public function getServiceTitleAttribute(): string
    {
        return self::SERVICES[$this->service_type] ?? ucwords(str_replace('_', ' ', $this->service_type));
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeServiceType(Builder $query, ?string $type): Builder
    {
        if (blank($type)) {
            return $query;
        }

        return $query->where('service_type', $type);
    }
}
