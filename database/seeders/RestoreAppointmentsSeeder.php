<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RestoreAppointmentsSeeder extends Seeder
{
    /**
     * Restore backed-up appointment records into the appointments table.
     */
    public function run(): void
    {
        $backupPath = database_path('backups/appointments_backup.json');

        if (! File::exists($backupPath)) {
            $this->command->error("Appointments backup file not found at: {$backupPath}");

            return;
        }

        $records = json_decode(File::get($backupPath), true);

        if (empty($records)) {
            $this->command->info('No appointment records to restore.');

            return;
        }

        foreach ($records as $record) {
            DB::table('appointments')->updateOrInsert(
                ['id' => $record['id']],
                $record
            );
        }

        $this->command->info('Successfully restored '.count($records).' appointment records.');
    }
}
