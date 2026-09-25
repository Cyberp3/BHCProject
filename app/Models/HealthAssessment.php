<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthAssessment extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'user_id',
        'assessment_date',
        'weight_kg',
        'height_cm',
        'bmi',
        'systolic_bp',
        'diastolic_bp',
        'pulse_rate',
        'respiratory_rate',
        'temperature_celsius',
        'nutritional_status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'assessment_date' => 'date',
            'weight_kg' => 'decimal:2',
            'height_cm' => 'decimal:2',
            'bmi' => 'decimal:1',
            'temperature_celsius' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (HealthAssessment $assessment) {
            if ($assessment->weight_kg && $assessment->height_cm && $assessment->height_cm > 0) {
                $heightInMeters = $assessment->height_cm / 100;
                $assessment->bmi = round($assessment->weight_kg / ($heightInMeters * $heightInMeters), 1);

                if (empty($assessment->nutritional_status)) {
                    if ($assessment->bmi < 18.5) {
                        $assessment->nutritional_status = 'Underweight';
                    } elseif ($assessment->bmi <= 24.9) {
                        $assessment->nutritional_status = 'Normal';
                    } elseif ($assessment->bmi <= 29.9) {
                        $assessment->nutritional_status = 'Overweight';
                    } else {
                        $assessment->nutritional_status = 'Obese';
                    }
                }
            }
        });
    }

    public function getBloodPressureAttribute(): ?string
    {
        if ($this->systolic_bp && $this->diastolic_bp) {
            return "{$this->systolic_bp}/{$this->diastolic_bp} mmHg";
        }

        return null;
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
