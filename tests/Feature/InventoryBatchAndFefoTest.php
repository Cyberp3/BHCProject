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

test('dispensing automatically deducts from earliest expiring batch first using FEFO', function () {
    $item = InventoryItem::firstOrCreate(
        ['item_code' => 'MED-AMOX-FEFO'],
        [
            'name' => 'Amoxicillin 500mg FEFO',
            'category' => 'medicine',
            'unit_of_measure' => 'capsules',
            'minimum_stock_alert' => 20,
        ]
    );

    // Clean any prior batches for this item
    InventoryBatch::where('inventory_item_id', $item->id)->delete();

    // Early batch: 50 capsules expiring in 2 months
    $batchEarly = InventoryBatch::create([
        'inventory_item_id' => $item->id,
        'batch_number' => 'LOT-AMOX-EARLY-'.uniqid(),
        'date_received' => Carbon::today()->format('Y-m-d'),
        'expiration_date' => Carbon::today()->addMonths(2)->format('Y-m-d'),
        'quantity_received' => 50,
        'current_quantity' => 50,
        'supplier_or_source' => 'CHO Davao',
        'status' => 'active',
    ]);

    // Late batch: 100 capsules expiring in 12 months
    $batchLate = InventoryBatch::create([
        'inventory_item_id' => $item->id,
        'batch_number' => 'LOT-AMOX-LATE-'.uniqid(),
        'date_received' => Carbon::today()->format('Y-m-d'),
        'expiration_date' => Carbon::today()->addMonths(12)->format('Y-m-d'),
        'quantity_received' => 100,
        'current_quantity' => 100,
        'supplier_or_source' => 'DOH Central',
        'status' => 'active',
    ]);

    // Dispense 70 capsules: should take all 50 from Early batch and 20 from Late batch
    $res = $this->actingAs($this->admin)->post(route('inventory.dispense.process'), [
        'inventory_item_id' => $item->id,
        'quantity' => 70,
        'remarks' => 'FEFO test dispensing to community health outreach',
    ]);

    $res->assertRedirect(route('inventory.index'));
    $res->assertSessionHas('success');

    // Early batch should be depleted (0 left)
    $batchEarly->refresh();
    expect($batchEarly->current_quantity)->toBe(0);
    expect($batchEarly->status)->toBe('depleted');

    // Late batch should have 80 left (100 - 20)
    $batchLate->refresh();
    expect($batchLate->current_quantity)->toBe(80);
    expect($batchLate->status)->toBe('active');
});

test('inventory index displays multi-batch item counts and links to item view', function () {
    $item = InventoryItem::firstOrCreate(
        ['item_code' => 'MED-MULTI-BATCH'],
        [
            'name' => 'Multi Batch Medicine 100mg',
            'category' => 'medicine',
            'unit_of_measure' => 'tablets',
            'minimum_stock_alert' => 10,
        ]
    );

    // Ensure 2 active batches exist
    InventoryBatch::create([
        'inventory_item_id' => $item->id,
        'batch_number' => 'LOT-MB-01-'.uniqid(),
        'date_received' => Carbon::today()->format('Y-m-d'),
        'expiration_date' => Carbon::today()->addMonths(3)->format('Y-m-d'),
        'quantity_received' => 50,
        'current_quantity' => 50,
        'status' => 'active',
    ]);

    InventoryBatch::create([
        'inventory_item_id' => $item->id,
        'batch_number' => 'LOT-MB-02-'.uniqid(),
        'date_received' => Carbon::today()->format('Y-m-d'),
        'expiration_date' => Carbon::today()->addMonths(18)->format('Y-m-d'),
        'quantity_received' => 50,
        'current_quantity' => 50,
        'status' => 'active',
    ]);

    $res = $this->actingAs($this->admin)->get(route('inventory.index'));
    $res->assertStatus(200);
    $res->assertSee('Multi Batch Medicine 100mg');
    $res->assertSee('Batches');
});
