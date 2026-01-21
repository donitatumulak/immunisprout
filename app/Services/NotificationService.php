<?php

namespace App\Services;

use App\Models\Child;
use App\Models\VaccineInventory;
use App\Models\VaccinationRecord;
use App\Models\NipSchedule;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Generate or update notifications for a specific child
     * Call this when: child is registered, vaccination record is updated
     */
    public function generateChildVaccineNotifications(Child $child)
    {
        // Skip if child is not active
        if ($child->chd_status !== 'active') {
            // Delete any existing notifications for this child
            Notification::where('notif_child_id', $child->chd_id)->delete();
            return;
        }

        $schedules = NipSchedule::with('vaccine')->orderBy('nip_recommended_age_days')->get();
        $today = Carbon::today();

        foreach ($schedules as $schedule) {
            // Check if this dose has been administered
            $existingRecord = VaccinationRecord::where('rec_child_id', $child->chd_id)
                ->where('rec_vaccine_id', $schedule->nip_vaccine_id)
                ->where('rec_dose_number', $schedule->nip_dose_number)
                ->whereIn('rec_status', ['completed', 'administered'])
                ->first();

            // Calculate due date
            $dueDate = Carbon::parse($child->chd_date_of_birth)
                ->addDays($schedule->nip_recommended_age_days);
            
            $daysUntilDue = $today->diffInDays($dueDate, false);

            // Create unique identifier for this notification
            $notificationKey = "child_{$child->chd_id}_vaccine_{$schedule->nip_vaccine_id}_dose_{$schedule->nip_dose_number}";

            // If dose is completed, remove any existing notification
            if ($existingRecord) {
                Notification::where('notif_child_id', $child->chd_id)
                    ->where('notif_notification_type', 'overdue')
                    ->where('notif_message', 'LIKE', "%{$schedule->vaccine->vacc_vaccine_name}%")
                    ->where('notif_message', 'LIKE', "%Dose {$schedule->nip_dose_number}%")
                    ->delete();

                Notification::where('notif_child_id', $child->chd_id)
                    ->where('notif_notification_type', 'upcoming')
                    ->where('notif_message', 'LIKE', "%{$schedule->vaccine->vacc_vaccine_name}%")
                    ->where('notif_message', 'LIKE', "%Dose {$schedule->nip_dose_number}%")
                    ->delete();
                
                continue;
            }

            // OVERDUE: Past the recommended date
            if ($daysUntilDue < 0) {
                $message = "{$child->getFullNameAttribute()} is " . abs($daysUntilDue) . " day(s) overdue for {$schedule->vaccine->vacc_vaccine_name} (Dose {$schedule->nip_dose_number}). Due: {$dueDate->format('M d, Y')}";
                
                Notification::updateOrCreate(
                    [
                        'notif_child_id' => $child->chd_id,
                        'notif_notification_type' => 'overdue',
                        'notif_message' => $message,
                    ],
                    [
                        'notif_is_read' => false,
                    ]
                );

                // Remove any upcoming notification for this vaccine
                Notification::where('notif_child_id', $child->chd_id)
                    ->where('notif_notification_type', 'upcoming')
                    ->where('notif_message', 'LIKE', "%{$schedule->vaccine->vacc_vaccine_name}%")
                    ->where('notif_message', 'LIKE', "%Dose {$schedule->nip_dose_number}%")
                    ->delete();
            }
            // UPCOMING: Within 7 days before due date
            elseif ($daysUntilDue >= 0 && $daysUntilDue <= 30) {
                $message = "{$child->getFullNameAttribute()} has an upcoming {$schedule->vaccine->vacc_vaccine_name} (Dose {$schedule->nip_dose_number}) in {$daysUntilDue} day(s). Due: {$dueDate->format('M d, Y')}";
                
                Notification::updateOrCreate(
                    [
                        'notif_child_id' => $child->chd_id,
                        'notif_notification_type' => 'upcoming',
                        'notif_message' => $message,
                    ],
                    [
                        'notif_is_read' => false,
                    ]
                );
            }
            // NOT DUE YET: Remove any existing notifications
            else {
                Notification::where('notif_child_id', $child->chd_id)
                    ->where('notif_notification_type', 'upcoming')
                    ->where('notif_message', 'LIKE', "%{$schedule->vaccine->vacc_vaccine_name}%")
                    ->where('notif_message', 'LIKE', "%Dose {$schedule->nip_dose_number}%")
                    ->delete();
            }
        }
    }

    /**
     * Generate notifications for all active children
     * Call this via scheduled task (daily cron job)
     */
    public function generateAllChildVaccineNotifications()
    {
        $children = Child::where('chd_status', 'active')->get();
        
        foreach ($children as $child) {
            $this->generateChildVaccineNotifications($child);
        }
    }

    /**
     * Generate or update notifications for specific inventory item
     * Call this when: inventory is updated, new stock added, stock used
     */
    public function generateInventoryNotifications(VaccineInventory $item)
    {
        $today = Carbon::today();
        $expiryDate = Carbon::parse($item->inv_expiry_date);
        $daysUntilExpiry = $today->diffInDays($expiryDate, false);

        // Remove all existing notifications for this inventory item
        Notification::where('notif_inventory_id', $item->inv_id)->delete();

        // EXPIRED
        if ($daysUntilExpiry < 0) {
            $message = "EXPIRED: {$item->vaccine->vacc_vaccine_name} (Batch: {$item->inv_batch_number}) expired " . abs($daysUntilExpiry) . " day(s) ago. Quantity: {$item->inv_quantity_available}";
            
            Notification::create([
                'notif_inventory_id' => $item->inv_id,
                'notif_notification_type' => 'inventory',
                'notif_message' => $message,
                'notif_is_read' => false,
            ]);
        }
        // EXPIRING SOON (within 30 days)
        elseif ($daysUntilExpiry >= 0 && $daysUntilExpiry <= 30) {
            $message = "{$item->vaccine->vacc_vaccine_name} (Batch: {$item->inv_batch_number}) expires in {$daysUntilExpiry} day(s). Quantity: {$item->inv_quantity_available}";
            
            Notification::create([
                'notif_inventory_id' => $item->inv_id,
                'notif_notification_type' => 'inventory',
                'notif_message' => $message,
                'notif_is_read' => false,
            ]);
        }

        // CRITICAL STOCK (≤ 10)
        if ($item->inv_quantity_available <= 10 && $item->inv_quantity_available > 0) {
            $message = "CRITICAL STOCK: {$item->vaccine->vacc_vaccine_name} (Batch: {$item->inv_batch_number}) has only {$item->inv_quantity_available} dose(s) remaining!";
            
            Notification::create([
                'notif_inventory_id' => $item->inv_id,
                'notif_notification_type' => 'inventory',
                'notif_message' => $message,
                'notif_is_read' => false,
            ]);
        }
        // LOW STOCK (11-50)
        elseif ($item->inv_quantity_available >= 11 && $item->inv_quantity_available <= 50) {
            $message = "Low Stock: {$item->vaccine->vacc_vaccine_name} (Batch: {$item->inv_batch_number}) has {$item->inv_quantity_available} dose(s) remaining.";
            
            Notification::create([
                'notif_inventory_id' => $item->inv_id,
                'notif_notification_type' => 'inventory',
                'notif_message' => $message,
                'notif_is_read' => false,
            ]);
        }
    }

    /**
     * Generate notifications for all inventory items
     * Call this via scheduled task (daily cron job)
     */
    public function generateAllInventoryNotifications()
    {
        $inventoryItems = VaccineInventory::with('vaccine')->get();
        
        foreach ($inventoryItems as $item) {
            $this->generateInventoryNotifications($item);
        }
    }

    /**
     * Clean up old read notifications
     * Call this via scheduled task (weekly cron job)
     */
    public function cleanupOldNotifications($daysOld = 30)
    {
        Notification::where('notif_is_read', true)
            ->where('updated_at', '<', Carbon::now()->subDays($daysOld))
            ->delete();
    }
}