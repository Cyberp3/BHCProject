<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'patient_control_number',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'sex',
        'date_of_birth',
        'civil_status',
        'purok_id',
        'street_address',
        'contact_number',
        'philhealth_number',
        'blood_type',
        'emergency_contact_name',
        'emergency_contact_number',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function getFullNameAttribute(): string
    {
        $name = "{$this->last_name}, {$this->first_name}";

        if ($this->middle_name) {
            $name .= ' '.substr($this->middle_name, 0, 1).'.';
        }

        if ($this->suffix) {
            $name .= " {$this->suffix}";
        }

        return $name;
    }

    public function getAgeAttribute(): int
    {
        return $this->date_of_birth ? Carbon::parse($this->date_of_birth)->age : 0;
    }

    public function purok(): BelongsTo
    {
        return $this->belongsTo(Purok::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function healthAssessments(): HasMany
    {
        return $this->hasMany(HealthAssessment::class)->latest('assessment_date');
    }

    public function healthServiceRecords(): HasMany
    {
        return $this->hasMany(HealthServiceRecord::class)->latest('service_date');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class)->orderBy('appointment_date', 'asc');
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('patient_control_number', 'like', "%{$term}%")
                ->orWhere('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('middle_name', 'like', "%{$term}%");
        });
    }

    public function scopeFilterPurok(Builder $query, mixed $purokId): Builder
    {
        if (blank($purokId)) {
            return $query;
        }

        return $query->where('purok_id', $purokId);
    }
}
