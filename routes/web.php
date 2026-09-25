<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HealthAssessmentController;
use App\Http\Controllers\HealthServiceController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'active_user'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Module 1 & 2: Patients & Vitals/Assessments
    Route::resource('patients', PatientController::class);
    Route::get('patients/{patient}/assessments/create', [HealthAssessmentController::class, 'create'])->name('assessments.create');
    Route::post('patients/{patient}/assessments', [HealthAssessmentController::class, 'store'])->name('assessments.store');

    // Module 1: The 8 Health Center Services
    Route::get('services', [HealthServiceController::class, 'index'])->name('services.index');
    Route::get('services/create', [HealthServiceController::class, 'create'])->name('services.create');
    Route::post('services', [HealthServiceController::class, 'store'])->name('services.store');
    Route::get('services/{serviceRecord}', [HealthServiceController::class, 'show'])->name('services.show');

    // Module 3: Appointments & Follow-up Scheduling
    Route::resource('appointments', AppointmentController::class)->except(['show', 'destroy']);
    Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');

    // Module 4: Medicine & Vaccine Inventory with FIFO
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/items/create', [InventoryController::class, 'createItem'])->name('items.create');
        Route::post('/items', [InventoryController::class, 'storeItem'])->name('items.store');
        Route::get('/items/{item}/edit', [InventoryController::class, 'editItem'])->name('items.edit');
        Route::put('/items/{item}', [InventoryController::class, 'updateItem'])->name('items.update');

        Route::get('/batches/receive', [InventoryController::class, 'receiveBatchForm'])->name('batches.receive');
        Route::post('/batches', [InventoryController::class, 'storeBatch'])->name('batches.store');

        Route::get('/dispense', [InventoryController::class, 'dispenseForm'])->name('dispense');
        Route::post('/dispense', [InventoryController::class, 'processDispense'])->name('dispense.process');
    });

    // Module 5: Basic Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/patients', [ReportController::class, 'patients'])->name('patients');
        Route::get('/services', [ReportController::class, 'services'])->name('services');
        Route::get('/appointments', [ReportController::class, 'appointments'])->name('appointments');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
    });

    // Module 6 & Security: Admin Only
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });
});

require __DIR__.'/auth.php';
