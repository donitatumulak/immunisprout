<?php

namespace App\Observers;

use App\Models\VaccineInventory;
use App\Services\NotificationService;

class VaccineInventoryObserver
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the VaccineInventory "created" event.
     */
    public function created(VaccineInventory $inventory): void
    {
        // When new inventory is added, check if it needs notifications
        $this->notificationService->generateInventoryNotifications($inventory);
    }

    /**
     * Handle the VaccineInventory "updated" event.
     */
    public function updated(VaccineInventory $inventory): void
    {
        // When inventory quantity changes or expiry date is updated
        $this->notificationService->generateInventoryNotifications($inventory);
    }

    /**
     * Handle the VaccineInventory "deleted" event.
     */
    public function deleted(VaccineInventory $inventory): void
    {
        // When inventory is deleted, remove its notifications
        \App\Models\Notification::where('notif_inventory_id', $inventory->inv_id)->delete();
    }
}