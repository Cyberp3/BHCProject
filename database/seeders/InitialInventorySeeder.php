<?php

namespace Database\Seeders;

use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InitialInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $admin = User::first();
        $adminId = $admin ? $admin->id : 1;

        $items = [
            [
                'item_code' => 'MED-AMX-500',
                'name' => 'Amoxicillin 500mg',
                'generic_name' => 'Amoxicillin Trihydrate',
                'category' => 'medicine',
                'dosage_form' => 'Capsule',
                'unit_of_measure' => 'Capsule',
                'minimum_stock_alert' => 100,
                'is_cold_chain' => false,
                'storage_temperature_note' => 'Store at temperature not exceeding 30°C',
                'description' => 'First-line oral antibiotic for bacterial infections',
                'batches' => [
                    [
                        'batch_number' => 'AMX-2026-01',
                        'date_received' => Carbon::now()->subMonths(1),
                        'expiration_date' => Carbon::now()->addMonths(14),
                        'quantity_received' => 500,
                        'current_quantity' => 350,
                        'supplier_or_source' => 'City Health Office (CHO) Davao',
                    ],
                ],
            ],
            [
                'item_code' => 'MED-PCM-500',
                'name' => 'Paracetamol 500mg',
                'generic_name' => 'Paracetamol',
                'category' => 'medicine',
                'dosage_form' => 'Tablet',
                'unit_of_measure' => 'Tablet',
                'minimum_stock_alert' => 150,
                'is_cold_chain' => false,
                'storage_temperature_note' => 'Store below 30°C',
                'description' => 'Antipyretic and analgesic for pain and fever',
                'batches' => [
                    [
                        'batch_number' => 'PCM-2026-A1',
                        'date_received' => Carbon::now()->subMonths(2),
                        'expiration_date' => Carbon::now()->addDays(45), // Near expiry alert
                        'quantity_received' => 200,
                        'current_quantity' => 45, // Low stock alert
                        'supplier_or_source' => 'City Health Office (CHO) Davao',
                    ],
                ],
            ],
            [
                'item_code' => 'MED-LST-50',
                'name' => 'Losartan Potassium 50mg',
                'generic_name' => 'Losartan Potassium',
                'category' => 'medicine',
                'dosage_form' => 'Tablet',
                'unit_of_measure' => 'Tablet',
                'minimum_stock_alert' => 50,
                'is_cold_chain' => false,
                'storage_temperature_note' => 'Store at 15°C to 30°C',
                'description' => 'Antihypertensive maintenance medication',
                'batches' => [
                    [
                        'batch_number' => 'LST-2026-88',
                        'date_received' => Carbon::now()->subWeeks(3),
                        'expiration_date' => Carbon::now()->addMonths(18),
                        'quantity_received' => 300,
                        'current_quantity' => 260,
                        'supplier_or_source' => 'DOH Regional Office XI',
                    ],
                ],
            ],
            [
                'item_code' => 'MED-ORS-S1',
                'name' => 'Oral Rehydration Salts',
                'generic_name' => 'Oral Rehydration Salts',
                'category' => 'medicine',
                'dosage_form' => 'Powder for Oral Solution',
                'unit_of_measure' => 'Sachet',
                'minimum_stock_alert' => 50,
                'is_cold_chain' => false,
                'storage_temperature_note' => 'Store in dry place below 30°C',
                'description' => 'Electrolyte replenishment for acute diarrhea and dehydration',
                'batches' => [
                    [
                        'batch_number' => 'ORS-2026-B',
                        'date_received' => Carbon::now()->subMonths(1),
                        'expiration_date' => Carbon::now()->addMonths(12),
                        'quantity_received' => 150,
                        'current_quantity' => 120,
                        'supplier_or_source' => 'City Health Office (CHO) Davao',
                    ],
                ],
            ],
            [
                'item_code' => 'VAC-BCG-01',
                'name' => 'BCG Vaccine',
                'generic_name' => 'Bacillus Calmette-Guérin Vaccine',
                'category' => 'vaccine',
                'dosage_form' => 'Freeze-dried powder with diluent',
                'unit_of_measure' => 'Vial',
                'minimum_stock_alert' => 10,
                'is_cold_chain' => true,
                'storage_temperature_note' => 'Cold Chain: +2°C to +8°C (Protect from light)',
                'description' => 'Tuberculosis vaccine given at birth / early infancy',
                'batches' => [
                    [
                        'batch_number' => 'BCG-2026-09',
                        'date_received' => Carbon::now()->subWeeks(2),
                        'expiration_date' => Carbon::now()->addMonths(8),
                        'quantity_received' => 30,
                        'current_quantity' => 24,
                        'supplier_or_source' => 'DOH Cold Chain Facility',
                    ],
                ],
            ],
            [
                'item_code' => 'VAC-PENT-01',
                'name' => 'Pentavalent Vaccine',
                'generic_name' => 'DTP-HepB-Hib Vaccine',
                'category' => 'vaccine',
                'dosage_form' => 'Liquid suspension',
                'unit_of_measure' => 'Vial',
                'minimum_stock_alert' => 15,
                'is_cold_chain' => true,
                'storage_temperature_note' => 'Cold Chain: +2°C to +8°C (Do Not Freeze)',
                'description' => 'Protection against Diphtheria, Tetanus, Pertussis, Hepatitis B, and Hib',
                'batches' => [
                    [
                        'batch_number' => 'PENT-2026-44',
                        'date_received' => Carbon::now()->subMonths(1),
                        'expiration_date' => Carbon::now()->addMonths(10),
                        'quantity_received' => 40,
                        'current_quantity' => 32,
                        'supplier_or_source' => 'DOH Cold Chain Facility',
                    ],
                ],
            ],
            [
                'item_code' => 'VAC-MMR-01',
                'name' => 'MMR Vaccine',
                'generic_name' => 'Measles, Mumps, and Rubella Vaccine',
                'category' => 'vaccine',
                'dosage_form' => 'Lyophilized powder with diluent',
                'unit_of_measure' => 'Vial',
                'minimum_stock_alert' => 15,
                'is_cold_chain' => true,
                'storage_temperature_note' => 'Cold Chain: +2°C to +8°C',
                'description' => 'Routine childhood immunization for MMR',
                'batches' => [
                    [
                        'batch_number' => 'MMR-2026-12',
                        'date_received' => Carbon::now()->subMonths(2),
                        'expiration_date' => Carbon::now()->addMonths(6),
                        'quantity_received' => 25,
                        'current_quantity' => 18,
                        'supplier_or_source' => 'City Health Office (CHO) Davao',
                    ],
                ],
            ],
        ];

        foreach ($items as $itemData) {
            $batches = $itemData['batches'];
            unset($itemData['batches']);

            $item = InventoryItem::firstOrCreate(
                ['item_code' => $itemData['item_code']],
                $itemData
            );

            foreach ($batches as $batchData) {
                $batchData['inventory_item_id'] = $item->id;
                $batch = InventoryBatch::firstOrCreate(
                    [
                        'inventory_item_id' => $item->id,
                        'batch_number' => $batchData['batch_number'],
                    ],
                    $batchData
                );

                InventoryTransaction::firstOrCreate(
                    [
                        'inventory_item_id' => $item->id,
                        'inventory_batch_id' => $batch->id,
                        'transaction_type' => 'received',
                    ],
                    [
                        'user_id' => $adminId,
                        'quantity' => $batch->quantity_received,
                        'transaction_date' => $batch->date_received,
                        'remarks' => 'Initial stock intake from '.($batch->supplier_or_source ?? 'Supplier'),
                    ]
                );
            }
        }
    }
}
