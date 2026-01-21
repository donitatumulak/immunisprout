<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\HealthWorker;
use App\Models\Guardian;
use App\Models\Child;
use App\Models\Vaccine;
use App\Models\NipSchedule;
use App\Models\VaccinationRecord;
use App\Models\VaccineInventory;
use App\Models\InventoryLog;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        /**
         * 1. Fixed reference data (Vaccines + NIP Schedules)
         */
        $this->call(NipSeeder::class);

        $totalChildren  = 50;
        $fullyImmunized = 15;

        /**
         * 2. Health workers & system users
         */
        $workers = HealthWorker::factory(5)->create();

        foreach ($workers as $worker) {
            User::factory()->create([
                'worker_id' => $worker->wrk_id,
                'username'  => strtolower($worker->wrk_last_name) . $worker->wrk_id,
                'password'  => bcrypt('password123'),
            ]);
        }

        $adminWorkerId = HealthWorker::first()->wrk_id;
        $schedules     = NipSchedule::orderBy('nip_recommended_age_days')->get();

        /**
         * 3. Create guardians and children
         */
        $guardians = Guardian::factory(15)->create();
        $childCounter = 0;

        foreach ($guardians as $guardian) {

            $childrenToCreate = rand(2, 4);

            for ($i = 0; $i < $childrenToCreate; $i++) {

                if ($childCounter >= $totalChildren) {
                    break 2;
                }

                $isFullyImmunized = $childCounter < $fullyImmunized;

                $birthdate = $isFullyImmunized
                    ? now()->subYears(2)
                    : now()->subMonths(5);

                $child = Child::factory()->create([
                    'chd_guardian_id'     => $guardian->grd_id,
                    'chd_current_addr_id' => $guardian->grd_current_addr_id,
                    'chd_date_of_birth'   => $birthdate,
                    'chd_status'          => $isFullyImmunized
                        ? 'complete'
                        : collect(['active', 'inactive', 'transferred'])->random(),
                ]);

                /**
                 * 4. Fully immunized children
                 */
                if ($isFullyImmunized) {

                    foreach ($schedules as $schedule) {

                        // prevent negative BCG date
                        $dateAdministered = Carbon::parse($birthdate)
                            ->addDays($schedule->nip_recommended_age_days)
                            ->addDays(
                                $schedule->nip_recommended_age_days === 0
                                    ? rand(0, 3)   // BCG at birth catch-up
                                    : rand(0, 5)   // small variation for other vaccines
                            );

                        if ($dateAdministered->isPast()) {

                            VaccinationRecord::create([
                                'rec_child_id'          => $child->chd_id,
                                'rec_vaccine_id'        => $schedule->nip_vaccine_id,
                                'rec_dose_number'       => $schedule->nip_dose_number,
                                'rec_date_administered' => $dateAdministered,
                                'rec_administered_by'   => $adminWorkerId,
                                'rec_remarks'           => 'Completed as scheduled',
                                'rec_status'            => 'completed',
                            ]);
                        }
                    }

                } 
                /**
                 * 5. Partially immunized children
                 * Only store doses already completed (e.g., early vaccines)
                 */
                else {
                    $partialSchedules = $schedules->filter(function ($schedule) {
                        return $schedule->nip_recommended_age_days <= 75; // up to ~10 weeks
                    });

                    foreach ($partialSchedules as $schedule) {

                        $dateAdministered = Carbon::parse($birthdate)
                            ->addDays($schedule->nip_recommended_age_days);

                        if ($dateAdministered->isPast()) {
                            VaccinationRecord::create([
                                'rec_child_id'          => $child->chd_id,
                                'rec_vaccine_id'        => $schedule->nip_vaccine_id,
                                'rec_dose_number'       => $schedule->nip_dose_number,
                                'rec_date_administered' => $dateAdministered,
                                'rec_administered_by'   => $adminWorkerId,
                                'rec_remarks'           => 'Partial immunization record',
                                'rec_status'            => 'completed',
                            ]);
                        }
                    }
                }

                $childCounter++;
            }
        }

        /**
         * 6. Seed vaccine inventory & logs
         */
        $this->seedInventory();
    }

    private function seedInventory(): void
    {
        $vaccines = Vaccine::all();
        $user     = User::first();

        foreach ($vaccines as $vaccine) {

            $inventory = VaccineInventory::create([
                'inv_vaccine_id'         => $vaccine->vacc_id,
                'inv_batch_number'       => 'LOT-' . rand(100, 999),
                'inv_quantity_available' => 100,
                'inv_expiry_date'        => now()->addYears(2),
                'inv_source'             => 'DOH Central',
            ]);

            InventoryLog::create([
                'log_inventory_id'     => $inventory->inv_id,
                'log_user_id'          => $user->id,
                'log_change_type'      => 'restock',
                'log_quantity_changed' => 100,
                'logable_type'         => VaccineInventory::class,
                'logable_id'           => $inventory->inv_id,
            ]);
        }
    }
}
