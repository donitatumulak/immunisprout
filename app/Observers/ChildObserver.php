<?php

namespace App\Observers;

use App\Models\Child;
use App\Services\NotificationService;

class ChildObserver
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Child "created" event.
     */
    public function created(Child $child): void
    {
        // When a new child is registered, generate their vaccination notifications
        $this->notificationService->generateChildVaccineNotifications($child);
    }

    /**
     * Handle the Child "updated" event.
     */
    public function updated(Child $child): void
    {
        // When child status changes (e.g., from active to completed/transferred)
        // Update their notifications accordingly
        $this->notificationService->generateChildVaccineNotifications($child);
    }

    /**
     * Handle the Child "deleted" event.
     */
    public function deleted(Child $child): void
    {
        // When a child is soft deleted, remove their notifications
        \App\Models\Notification::where('notif_child_id', $child->chd_id)->delete();
    }
}