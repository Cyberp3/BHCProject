<?php

use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    $this->admin = User::firstOrCreate(
        ['email' => 'admin@tacunan.gov.ph'],
        [
            'name' => 'Admin Staff',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]
    );
});

test('inventory stores incoming deliveries as distinct batches and isolates expiration dates', function () {
    $item = InventoryItem::firstOrCreate(
        ['item_code' => 'MED-PAR-TEST'],
        [
            'name' => 'Paracetamol Test 500mg',
            'category' => 'medicine',
            'unit_of_measure' => 'tablets',
            'minimum_stock_alert' => 50,
        ]
    );

    // Receive Batch 1: Expiring in 6 months
    $batch1Expiry = Carbon::today()->addMonths(6)->format('Y-m-d');
    $res1 = $this->actingAs($this->admin)->post(route('inventory.batches.store'), [
        'inventory_item_id' => $item->id,
        'batch_number' => 'LOT-PARA-EARLY-'.uniqid(),
        'date_received' => Carbon::today()->format('Y-m-d'),
        'expiration_date' => $batch1Expiry,
        'quantity_received' => 100,
        'supplier_or_source' => 'CHO Davao',
    ]);
    $res1->assertSessionHasNoErrors();

    // Receive Batch 2: Expiring in 24 months (Newer batch)
    $batch2Expiry = Carbon::today()->addMonths(24)->format('Y-m-d');
    $res2 = $this->actingAs($this->admin)->post(route('inventory.batches.store'), [
        'inventory_item_id' => $item->id,
        'batch_number' => 'LOT-PARA-LATER-'.uniqid(),
        'date_received' => Carbon::today()->format('Y-m-d'),
        'expiration_date' => $batch2Expiry,
        'quantity_received' => 200,
        'supplier_or_source' => 'DOH Central',
    ]);
    $res2->assertSessionHasNoErrors();

    // Check that two separate batches exist, not merged into one
    $batches = InventoryBatch::where('inventory_item_id', $item->id)
        ->where('status', 'active')
        ->orderBy('expiration_date', 'asc')
        ->get();
    expect($batches->count())->toBeGreaterThanOrEqual(2);

    // Verify FEFO: First batch in queue is the earlier expiration
    expect($batches->first()->expiration_date->format('Y-m-d'))->toBe($batch1Expiry);

    // View Item screen loads both batches
    $response = $this->actingAs($this->admin)->get(route('inventory.items.show', $item));
    $response->assertStatus(200);
    $response->assertSee('Paracetamol Test 500mg');
    $response->assertSee('Current Dispensing Order (FEFO Queue)');
});
