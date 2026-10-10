<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'contact_number',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function hasRole(string|array $roles): bool
    {
        $currentRole = strtolower(trim((string) $this->role));

        if (is_array($roles)) {
            $normalizedRoles = array_map(fn ($r) => strtolower(trim((string) $r)), $roles);

            return in_array($currentRole, $normalizedRoles, true);
        }

        return $currentRole === strtolower(trim((string) $roles));
    }

    public function isAdmin(): bool
    {
        return strtolower(trim((string) $this->role)) === 'admin';
    }

    public function isClinicalStaff(): bool
    {
        return in_array(strtolower(trim((string) $this->role)), ['nurse', 'midwife'], true);
    }

    public function isHealthPersonnel(): bool
    {
        return in_array(strtolower(trim((string) $this->role)), ['nurse', 'midwife', 'bns', 'bhw', 'bhv'], true);
    }

    public function isBhw(): bool
    {
        return strtolower(trim((string) $this->role)) === 'bhw';
    }

    public function isNurse(): bool
    {
        return strtolower(trim((string) $this->role)) === 'nurse';
    }

    public function isMidwife(): bool
    {
        return strtolower(trim((string) $this->role)) === 'midwife';
    }

    public function canRecordClinicalCare(): bool
    {
        return ! $this->isAdmin() && $this->isHealthPersonnel();
    }

    public function canDispenseMedicine(): bool
    {
        return $this->isClinicalStaff();
    }

    public function canAccessReports(): bool
    {
        return ! $this->isBhw() && ! $this->hasRole(['bhw', 'bhv']);
    }

    /**
     * Get the health service programs this user is authorized to conduct.
     *
     * @return array<string, string>
     */
    public function allowedHealthServices(): array
    {
        if ($this->isAdmin()) {
            return [];
        }

        if ($this->isClinicalStaff()) {
            return HealthServiceRecord::SERVICES;
        }

        if ($this->isBhw() || $this->hasRole(['bhw', 'bhv'])) {
            return [
                'purok_kalusugan' => HealthServiceRecord::SERVICES['purok_kalusugan'] ?? 'Purok Kalusugan',
            ];
        }

        return [];
    }

    public function canConductService(string $serviceType): bool
    {
        return array_key_exists($serviceType, $this->allowedHealthServices());
    }

    public function getRoleDisplayNameAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Administrator',
            'nurse' => 'Barangay Health Nurse',
            'midwife' => 'Barangay Midwife',
            'bns' => 'Barangay Nutrition Scholar (BNS)',
            'bhw' => 'Barangay Health Worker (BHW)',
            'bhv' => 'Barangay Health Volunteer (BHV)',
            default => strtoupper($this->role),
        };
    }

    public function patientsCreated(): HasMany
    {
        return $this->hasMany(Patient::class, 'created_by');
    }

    public function healthAssessments(): HasMany
    {
        return $this->hasMany(HealthAssessment::class, 'user_id');
    }

    public function healthServiceRecords(): HasMany
    {
        return $this->hasMany(HealthServiceRecord::class, 'user_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'scheduled_by');
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'user_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }
}
