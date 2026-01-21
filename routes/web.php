<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\VaccinationController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;

// 1. PUBLIC ROUTES (No changes needed)
Route::get('/', function () { return view('home'); })->name('home');
Route::get('/public', [VaccinationController::class, 'publicSearch'])->name('public.search-record');
Route::post('/public', [VaccinationController::class, 'publicSearchResults'])->name('public.search-results');

// 2. GUEST ROUTES
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// 3. PROTECTED ROUTES (Must be logged in)
Route::middleware(['auth'])->group(function () {
    
    // Dashboard & Profile (Everyone can access)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // --- BHW RESTRICTIONS START HERE ---

    // USER MANAGEMENT: Only Admin, Nurse, and Midwife
    Route::resource('user-management', UserManagementController::class)
        ->middleware('role:admin,nurse,midwife');
    
    Route::put('user-management/{user}/password', [UserManagementController::class, 'resetPassword'])
        ->name('users.password.update')
        ->middleware('role:admin,nurse,midwife');
    Route::patch('/users/{id}/toggle-status', [UserManagementController::class, 'toggleStatus'])
        ->name('users.toggle')
        ->middleware('role:admin,nurse,midwife');

    // DELETE PROTECTION: Prevent BHW from using any Delete/Force-Delete routes
    Route::middleware('role:admin,nurse,midwife')->group(function () {
        Route::delete('/children/{id}/force-delete', [ChildController::class, 'forceDelete'])->name('children.forceDelete');
        // If your resources have delete routes, you can specifically protect them:
        Route::delete('/children/{child}', [ChildController::class, 'destroy'])->name('children.destroy');
        Route::delete('/inventory/{inventory}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    });

    // --- SHARED ACCESS (BHW can Add, Edit, View, but NOT Delete) ---

    // Child Records (Excluding destroy so BHW can't delete)
    Route::resource('children', ChildController::class)->except(['destroy']);
    Route::put('/children/{id}/restore', [ChildController::class, 'restore'])->name('children.restore');

    // Inventory (Excluding destroy)
    Route::resource('inventory', InventoryController::class)->except(['destroy']);
    Route::post('/inventory/{id}/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');

    // Vaccination Records (Everyone can add/edit/view)
    Route::resource('vaccinations', VaccinationController::class);
    Route::get('/vaccinations/{child}/immunization-card', [VaccinationController::class, 'showImmunizationCard'])->name('vaccinations.immunization-card');
    Route::patch('/vaccinations/{record}/quick-update', [VaccinationController::class, 'quickUpdate'])->name('vaccinations.quick-update');

    // Notifications (Everyone can access)
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('markRead');
        Route::post('/mark-all', [NotificationController::class, 'markAllAsRead'])->name('markAll');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unreadCount');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
