<?php

use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
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

    $this->nurse = User::firstOrCreate(
        ['email' => 'nurse@tacunan.gov.ph'],
        [
            'name' => 'Nurse Maria Santos',
            'password' => bcrypt('password123'),
            'role' => 'nurse',
            'is_active' => true,
        ]
    );
});

test('dispensing strictly rejects items when all available stock is expired', function () {
    $item = InventoryItem::firstOrCreate(
        ['item_code' => 'MED-EXP-ONLY'],
        [
            'name' => 'Expired Only Amoxicillin 500mg',
            'category' => 'medicine',
            'unit_of_measure' => 'capsules',
            'minimum_stock_alert' => 10,
        ]
    );

    // Clean any prior batches
    InventoryBatch::where('inventory_item_id', $item->id)->delete();

    // Expired batch: expired 5 days ago
    $expiredBatch = InventoryBatch::create([
        'inventory_item_id' => $item->id,
        'batch_number' => 'LOT-EXP-'.uniqid(),
        'date_received' => Carbon::today()->subMonths(6)->format('Y-m-d'),
        'expiration_date' => Carbon::today()->subDays(5)->format('Y-m-d'),
        'quantity_received' => 50,
        'current_quantity' => 50,
        'supplier_or_source' => 'CHO Davao',
        'status' => 'active',
    ]);

    $response = $this->actingAs($this->nurse)->post(route('inventory.dispense.process'), [
        'inventory_item_id' => $item->id,
        'quantity' => 10,
        'remarks' => 'Attempt to dispense expired medication',
    ]);

    $response->assertSessionHasErrors(['quantity']);

    // Ensure the expired batch was NOT deducted
    $expiredBatch->refresh();
    expect($expiredBatch->current_quantity)->toBe(50);
    expect($expiredBatch->status)->toBe('expired');

    // Ensure no dispense transaction was recorded
    $hasDispensedTx = InventoryTransaction::where('inventory_item_id', $item->id)
        ->where('transaction_type', 'dispensed')
        ->exists();
    expect($hasDispensedTx)->toBeFalse();
});

test('fefo dispensing bypasses expired batches and strictly deducts from unexpired batches', function () {
    $item = InventoryItem::firstOrCreate(
        ['item_code' => 'MED-MIXED-EXP'],
        [
            'name' => 'Mixed Expiry Paracetamol 500mg',
            'category' => 'medicine',
            'unit_of_measure' => 'tablets',
            'minimum_stock_alert' => 15,
        ]
    );

    InventoryBatch::where('inventory_item_id', $item->id)->delete();

    // Batch 1: Expired 10 days ago (earliest date, would normally be chosen by naive FEFO)
    $batchExpired = InventoryBatch::create([
        'inventory_item_id' => $item->id,
        'batch_number' => 'LOT-OLD-EXP-'.uniqid(),
        'date_received' => Carbon::today()->subMonths(12)->format('Y-m-d'),
        'expiration_date' => Carbon::today()->subDays(10)->format('Y-m-d'),
        'quantity_received' => 40,
        'current_quantity' => 40,
        'supplier_or_source' => 'CHO Davao',
        'status' => 'active',
    ]);

    // Batch 2: Valid unexpired batch (expires in 6 months)
    $batchValid = InventoryBatch::create([
        'inventory_item_id' => $item->id,
        'batch_number' => 'LOT-FRESH-VALID-'.uniqid(),
        'date_received' => Carbon::today()->subDays(2)->format('Y-m-d'),
        'expiration_date' => Carbon::today()->addMonths(6)->format('Y-m-d'),
        'quantity_received' => 60,
        'current_quantity' => 60,
        'supplier_or_source' => 'DOH Central',
        'status' => 'active',
    ]);

    // Dispense 25 tablets
    $response = $this->actingAs($this->nurse)->post(route('inventory.dispense.process'), [
        'inventory_item_id' => $item->id,
        'quantity' => 25,
        'remarks' => 'Dispensed to outreach client',
    ]);

    $response->assertRedirect(route('inventory.index'));
    $response->assertSessionHas('success');

    // Expired batch must remain untouched at 40 tablets and flagged as expired
    $batchExpired->refresh();
    expect($batchExpired->current_quantity)->toBe(40);
    expect($batchExpired->status)->toBe('expired');

    // Valid batch should have exactly 35 tablets left (60 - 25)
    $batchValid->refresh();
    expect($batchValid->current_quantity)->toBe(35);
    expect($batchValid->status)->toBe('active');

    // The transaction recorded must reference the valid batch, not the expired one
    $tx = InventoryTransaction::where('inventory_item_id', $item->id)
        ->where('transaction_type', 'dispensed')
        ->latest('id')
        ->first();
    expect($tx)->not->toBeNull();
    expect($tx->inventory_batch_id)->toBe($batchValid->id);
    expect($tx->quantity)->toBe(25);
});

test('dispensing fails if requested quantity exceeds unexpired stock even if expired stock exists', function () {
    $item = InventoryItem::firstOrCreate(
        ['item_code' => 'MED-EXCEED-UNEXP'],
        [
            'name' => 'Cefalexin 500mg Exceed Check',
            'category' => 'medicine',
            'unit_of_measure' => 'capsules',
            'minimum_stock_alert' => 10,
        ]
    );

    InventoryBatch::where('inventory_item_id', $item->id)->delete();

    // 50 expired tablets
    $batchExpired = InventoryBatch::create([
        'inventory_item_id' => $item->id,
        'batch_number' => 'LOT-EXP-CEF-'.uniqid(),
        'date_received' => Carbon::today()->subMonths(12)->format('Y-m-d'),
        'expiration_date' => Carbon::today()->subDays(2)->format('Y-m-d'),
        'quantity_received' => 50,
        'current_quantity' => 50,
        'status' => 'active',
    ]);

    // 20 valid tablets
    $batchValid = InventoryBatch::create([
        'inventory_item_id' => $item->id,
        'batch_number' => 'LOT-VAL-CEF-'.uniqid(),
        'date_received' => Carbon::today()->format('Y-m-d'),
        'expiration_date' => Carbon::today()->addMonths(8)->format('Y-m-d'),
        'quantity_received' => 20,
        'current_quantity' => 20,
        'status' => 'active',
    ]);

    // Request 25 tablets (total stock is 70, but unexpired is only 20)
    $response = $this->actingAs($this->nurse)->post(route('inventory.dispense.process'), [
        'inventory_item_id' => $item->id,
        'quantity' => 25,
        'remarks' => 'Request exceeding unexpired stock',
    ]);

    $response->assertSessionHasErrors(['quantity']);

    // Valid batch should remain intact at 20 tablets
    $batchValid->refresh();
    expect($batchValid->current_quantity)->toBe(20);
});

test('dispense form marks items with only expired batches as quarantined and disabled', function () {
    $item = InventoryItem::firstOrCreate(
        ['item_code' => 'MED-FORM-DIS'],
        [
            'name' => 'Expired Form Display Test 250mg',
            'category' => 'medicine',
            'unit_of_measure' => 'tablets',
            'minimum_stock_alert' => 5,
        ]
    );

    InventoryBatch::where('inventory_item_id', $item->id)->delete();

    InventoryBatch::create([
        'inventory_item_id' => $item->id,
        'batch_number' => 'LOT-EXP-FORM-'.uniqid(),
        'date_received' => Carbon::today()->subMonths(10)->format('Y-m-d'),
        'expiration_date' => Carbon::today()->subDays(1)->format('Y-m-d'),
        'quantity_received' => 30,
        'current_quantity' => 30,
        'status' => 'active',
    ]);

    $response = $this->actingAs($this->nurse)->get(route('inventory.dispense'));
    $response->assertStatus(200);
    $response->assertSee('[EXPIRED / QUARANTINED - CANNOT DISPENSE]');
});

test('item show page displays quarantined expired batches separately from fefo queue', function () {
    $item = InventoryItem::firstOrCreate(
        ['item_code' => 'MED-SHOW-SEPARATE'],
        [
            'name' => 'Separation Test Item 100mg',
            'category' => 'medicine',
            'unit_of_measure' => 'vials',
            'minimum_stock_alert' => 5,
        ]
    );

    InventoryBatch::where('inventory_item_id', $item->id)->delete();

    $batchExpired = InventoryBatch::create([
        'inventory_item_id' => $item->id,
        'batch_number' => 'LOT-QUAR-001',
        'date_received' => Carbon::today()->subMonths(8)->format('Y-m-d'),
        'expiration_date' => Carbon::today()->subDays(15)->format('Y-m-d'),
        'quantity_received' => 20,
        'current_quantity' => 20,
        'status' => 'active',
    ]);

    $batchValid = InventoryBatch::create([
        'inventory_item_id' => $item->id,
        'batch_number' => 'LOT-VALID-002',
        'date_received' => Carbon::today()->format('Y-m-d'),
        'expiration_date' => Carbon::today()->addMonths(12)->format('Y-m-d'),
        'quantity_received' => 45,
        'current_quantity' => 45,
        'status' => 'active',
    ]);

    $response = $this->actingAs($this->admin)->get(route('inventory.items.show', $item));
    $response->assertStatus(200);
    $response->assertSee('Quarantined / Expired Batches (Blocked from Dispensation)');
    $response->assertSee('BLOCKED / QUARANTINED');
    $response->assertSee('LOT-QUAR-001');
    $response->assertSee('LOT-VALID-002');
});
