<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vaccine;
use App\Models\NipSchedule;
use Exception;
use Illuminate\Support\Facades\DB;

class NipSeeder extends Seeder
{
    private const WEEK = 7;
    private const GRACE_PERIOD_DAYS = 30;
    private const EARLY_ALLOWANCE_DAYS = 7;

    public function run(): void
    {
        DB::table('nip_schedule')->truncate();
        DB::table('vaccines')->truncate();
        $nip = [
            [
                'name' => 'BCG',
                'desc' => 'Tuberculosis',
                'doses' => 1,
                'intervals' => [0], // Birth dose
            ],
            [
                'name' => 'Hepatitis B',
                'desc' => 'Hepatitis B (Birth Dose Only)',
                'doses' => 1,
                'intervals' => [0], // Birth dose
            ],
            [
                'name' => 'Pentavalent',
                'desc' => 'DPT-HepB-Hib',
                'doses' => 3,
                'intervals' => [
                    6 * self::WEEK,
                    10 * self::WEEK,
                    14 * self::WEEK,
                ],
            ],
            [
                'name' => 'OPV',
                'desc' => 'Oral Polio Vaccine',
                'doses' => 3,
                'intervals' => [
                    6 * self::WEEK,
                    10 * self::WEEK,
                    14 * self::WEEK,
                ],
            ],
            [
                'name' => 'PCV',
                'desc' => 'Pneumococcal Conjugate Vaccine',
                'doses' => 3,
                'intervals' => [
                    6 * self::WEEK,
                    10 * self::WEEK,
                    14 * self::WEEK,
                ],
            ],
            [
                'name' => 'IPV',
                'desc' => 'Inactivated Polio Vaccine',
                'doses' => 2,
                'intervals' => [
                    14 * self::WEEK,
                    9 * 30, // ~9 months
                ],
            ],
            [
                'name' => 'MMR',
                'desc' => 'Measles, Mumps, Rubella',
                'doses' => 2,
                'intervals' => [
                    9 * 30,   // ~9 months
                    12 * 30,  // ~12 months
                ],
            ],
        ];

        foreach ($nip as $data) {

            // Safety check: doses must match intervals
            if (count($data['intervals']) !== $data['doses']) {
                throw new Exception(
                    "Dose count mismatch for vaccine: {$data['name']}"
                );
            }

            // Create Vaccine
            $vaccine = Vaccine::create([
                'vacc_vaccine_name'   => $data['name'],
                'vacc_description'    => $data['desc'],
                'vacc_doses_required' => $data['doses'],
            ]);

            // Create Schedule per dose
            foreach ($data['intervals'] as $index => $recommendedDays) {

                $isBirthDose = $recommendedDays === 0;

                NipSchedule::create([
                    'nip_vaccine_id'          => $vaccine->vacc_id,
                    'nip_dose_number'         => $index + 1,
                    'nip_recommended_age_days'=> $recommendedDays,
                    'nip_minimum_age_days'    => $isBirthDose
                        ? 0
                        : max(0, $recommendedDays - self::EARLY_ALLOWANCE_DAYS),
                    'nip_maximum_age_days'    => $recommendedDays + self::GRACE_PERIOD_DAYS,
                ]);
            }
        }
    }
}
