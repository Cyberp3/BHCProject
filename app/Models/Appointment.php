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

    public const SCHEDULE_DAYS = [
        'ntp' => [
            'days' => [Carbon::MONDAY, Carbon::TUESDAY],
            'session' => 'morning',
            'label' => 'Monday & Tuesday morning (NTP)',
        ],
        'family_planning' => [
            'days' => [Carbon::MONDAY, Carbon::TUESDAY],
            'session' => 'afternoon',
            'label' => 'Monday & Tuesday afternoon (Family Planning)',
        ],
        'prenatal_care' => [
            'days' => [Carbon::TUESDAY],
            'session' => 'all_day',
            'label' => 'Tuesday (Prenatal Care)',
        ],
        'immunization' => [
            'days' => [Carbon::WEDNESDAY],
            'session' => 'all_day',
            'label' => 'Wednesday (Immunization)',
        ],
        'purok_kalusugan' => [
            'days' => [Carbon::THURSDAY],
            'session' => 'all_day',
            'label' => 'Thursday (Purok Kalusugan)',
        ],
        'bns_program' => [
            'days' => [Carbon::THURSDAY],
            'session' => 'all_day',
            'label' => 'Thursday (BNS Program)',
        ],
        'cvd_screening' => [
            'days' => [Carbon::FRIDAY],
            'session' => 'all_day',
            'label' => 'Friday (CVD Screening)',
        ],
        'philpen' => [
            'days' => [Carbon::FRIDAY],
            'session' => 'all_day',
            'label' => 'Friday (PhilPEN)',
        ],
    ];

    public static function validateClinicSchedule(string $serviceType, Carbon $date, ?string $time = null): ?string
    {
        if ($date->isWeekend()) {
            return 'Barangay Tacunan Health Center is closed on weekends (Saturday and Sunday). Routine clinic services operate Monday through Friday.';
        }

        $key = strtolower(trim(str_replace([' ', '-'], '_', $serviceType)));

        if ($key === 'general_consultation' || $key === 'general') {
            return null;
        }

        $rule = self::SCHEDULE_DAYS[$key] ?? null;
        if (! $rule) {
            foreach (self::SCHEDULE_DAYS as $k => $cfg) {
                if (str_contains($key, $k) || str_contains($k, $key)) {
                    $rule = $cfg;
                    break;
                }
            }
        }

        if (! $rule) {
            return null;
        }

        $displayTitle = HealthServiceRecord::SERVICES[$serviceType]
            ?? HealthServiceRecord::SERVICES[$key]
            ?? ucwords(str_replace(['_', '-'], ' ', $serviceType));

        if (! in_array($date->dayOfWeek, $rule['days'], true)) {
            $dayNames = collect($rule['days'])->map(fn ($d) => match ($d) {
                Carbon::MONDAY => 'Monday',
                Carbon::TUESDAY => 'Tuesday',
                Carbon::WEDNESDAY => 'Wednesday',
                Carbon::THURSDAY => 'Thursday',
                Carbon::FRIDAY => 'Friday',
                default => 'Weekday',
            })->join(', ');

            return "{$displayTitle} is strictly scheduled on {$rule['label']}. Please select an authorized {$dayNames}.";
        }

        if ($time && isset($rule['session']) && $rule['session'] !== 'all_day') {
            $hour = (int) explode(':', $time)[0];
            if ($rule['session'] === 'morning' && $hour >= 12) {
                return "{$displayTitle} is only conducted during the morning session (8:00 AM - 12:00 PM).";
            }
            if ($rule['session'] === 'afternoon' && $hour < 12) {
                return "{$displayTitle} is only conducted during the afternoon session (1:00 PM - 5:00 PM).";
            }
        }

        return null;
    }

    public function scopeMissedOrOverdue(Builder $query): Builder
    {
        return $query->where('status', 'scheduled')
            ->whereDate('appointment_date', '<', Carbon::today());
    }
}
