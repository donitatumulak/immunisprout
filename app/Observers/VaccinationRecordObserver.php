<?php

namespace App\Observers;

use App\Models\VaccinationRecord;
use App\Services\NotificationService;

class VaccinationRecordObserver
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the VaccinationRecord "created" event.
     */
    public function created(VaccinationRecord $vaccinationRecord): void
    {
        // When a new vaccination record is created, update notifications for that child
        if ($vaccinationRecord->child) {
            $this->notificationService->generateChildVaccineNotifications($vaccinationRecord->child);
        }
    }

    /**
     * Handle the VaccinationRecord "updated" event.
     */
    public function updated(VaccinationRecord $vaccinationRecord): void
    {
        // When a vaccination record is updated (e.g., status changed to completed)
        // Update notifications for that child
        if ($vaccinationRecord->child) {
            $this->notificationService->generateChildVaccineNotifications($vaccinationRecord->child);
        }
    }

    /**
     * Handle the VaccinationRecord "deleted" event.
     */
    public function deleted(VaccinationRecord $vaccinationRecord): void
    {
        // When a vaccination record is deleted, regenerate notifications for that child
        if ($vaccinationRecord->child) {
            $this->notificationService->generateChildVaccineNotifications($vaccinationRecord->child);
        }
    }
}