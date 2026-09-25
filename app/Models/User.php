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
        if (is_array($roles)) {
            return in_array($this->role, $roles, true);
        }

        return $this->role === $roles;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isClinicalStaff(): bool
    {
        return in_array($this->role, ['admin', 'nurse', 'midwife'], true);
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
