<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\VaccinationRecord;
use App\Models\Vaccine;
use App\Models\NipSchedule;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::now();
        
        // Load ALL records. We will filter 'completed' in memory to be safe.
        $children = Child::with('vaccinationRecords')->get();
        $schedules = NipSchedule::all();
        $vaccines = Vaccine::all();

        $stats = [
            'total_children' => $children->count(),
            'newly_registered' => $children->where('created_at', '>=', $today->copy()->subDays(7))->count(),
            'completed' => 0,
            'upcoming' => 0,
            'overdue' => 0,
        ];

        $vaccineStats = [];

        foreach ($children as $child) {
            $ageInDays = (int)Carbon::parse($child->chd_birthdate)->diffInDays($today);
            
            $childIsFullyImmunized = true; 
            $childHasUpcoming = false;
            $childHasOverdue = false;

            foreach ($schedules as $schedule) {
                // Force IDs to strings for reliable matching
                $isDone = $child->vaccinationRecords->contains(function ($rec) use ($schedule) {
                    return (string)$rec->rec_vaccine_id === (string)$schedule->nip_vaccine_id &&
                        (int)$rec->rec_dose_number === (int)$schedule->nip_dose_number &&
                        strtolower($rec->rec_status) === 'completed'; // Case-insensitive check
                });

                if (!$isDone) {
                    $childIsFullyImmunized = false;

                    if ($ageInDays > (int)$schedule->nip_maximum_age_days) {
                        $childHasOverdue = true;
                        $vaccineStats[$schedule->nip_vaccine_id]['overdue'] = ($vaccineStats[$schedule->nip_vaccine_id]['overdue'] ?? 0) + 1;
                    } elseif ($ageInDays >= (int)$schedule->nip_minimum_age_days) {
                        $childHasUpcoming = true;
                        $vaccineStats[$schedule->nip_vaccine_id]['upcoming'] = ($vaccineStats[$schedule->nip_vaccine_id]['upcoming'] ?? 0) + 1;
                    }
                }
            }

            // Apply Priority for the Donut Chart
            if ($childIsFullyImmunized && $schedules->count() > 0) {
                $stats['completed']++;
            } elseif ($childHasOverdue) {
                $stats['overdue']++;
            } elseif ($childHasUpcoming) {
                $stats['upcoming']++;
            }
        }

        // Prepare Priority List for UI
        $vaccinePriorityList = $vaccines->map(function($v) use ($vaccineStats) {
            return [
                'vaccine_name' => $v->vacc_vaccine_name,
                'overdue' => $vaccineStats[$v->vacc_id]['overdue'] ?? 0,
                'upcoming' => $vaccineStats[$v->vacc_id]['upcoming'] ?? 0,
                'total' => ($vaccineStats[$v->vacc_id]['overdue'] ?? 0) + ($vaccineStats[$v->vacc_id]['upcoming'] ?? 0)
            ];
        })->sortByDesc('total')->take(5)->values();

        // Fix the Performance Query: Last 6 months (more visible)
        $monthlyPerformance = VaccinationRecord::select(
                DB::raw('EXTRACT(MONTH FROM rec_date_administered) AS month'),
                DB::raw("TRIM(TO_CHAR(rec_date_administered, 'Month')) AS month_name"),
                DB::raw('COUNT(*) AS total')
            )
            ->whereRaw("LOWER(rec_status) = 'completed'")
            ->where('rec_date_administered', '>=', $today->copy()->subMonths(6))
            ->groupBy('month', 'month_name')
            ->orderBy('month')
            ->get();

        $statusDistribution = $stats; 
        $liveAlerts = Notification::with(['child'])->latest()->take(5)->get();

        return view('dashboard', compact('stats', 'monthlyPerformance', 'statusDistribution', 'vaccinePriorityList', 'liveAlerts'));
    }
}