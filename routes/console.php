<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log; // Ensure this is here
use App\Services\NotificationService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// =============================================================================
// SCHEDULED TASKS FOR NOTIFICATIONS
// =============================================================================

Schedule::call(function () {
    try {
        // Use the container to make the service
        $notificationService = app(NotificationService::class);
        $notificationService->generateAllChildVaccineNotifications();
        
        Log::info('Daily vaccine notifications generated successfully.');
    } catch (\Exception $e) {
        Log::error('Vaccine Notification Error: ' . $e->getMessage());
    }
})
->dailyAt('00:00')->timezone('Asia/Manila')
->name('generate-vaccine-notifications')
->withoutOverlapping();

Schedule::call(function () {
    try {
        $notificationService = app(NotificationService::class);
        $notificationService->generateAllInventoryNotifications();
        
        Log::info('Daily inventory notifications generated successfully.');
    } catch (\Exception $e) {
        Log::error('Inventory Notification Error: ' . $e->getMessage());
    }
})
->dailyAt('01:00')->timezone('Asia/Manila')
->name('generate-inventory-notifications')
->withoutOverlapping();

Schedule::call(function () {
    try {
        $notificationService = app(NotificationService::class);
        $deletedCount = $notificationService->cleanupOldNotifications(30);
        
        Log::info("Cleaned up old notifications. Deleted: {$deletedCount}");
    } catch (\Exception $e) {
        Log::error('Cleanup Notification Error: ' . $e->getMessage());
    }
})
->weeklyOn(0, '02:00')->timezone('Asia/Manila')
->name('cleanup-old-notifications');