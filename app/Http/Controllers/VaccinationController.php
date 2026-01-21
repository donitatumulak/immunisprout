<?php

namespace App\Http\Controllers;

use App\Models\VaccinationRecord;
use App\Models\Child;
use App\Models\Vaccine;
use App\Models\VaccineInventory;
use App\Models\InventoryLog;
use App\Models\HealthWorker;
use App\Models\NipSchedule;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class VaccinationController extends Controller
{
    public function index(Request $request)
    {
        $query = Child::query()->with(['vaccinationRecords.vaccine', 'guardian', 'address']);

        // Search filter
        if ($request->filled('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('chd_first_name','ilike', "%{$search}%")
                ->orWhere('chd_last_name', 'ilike', "%{$search}%")
                ->orWhere('chd_middle_name', 'ilike', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status') && $request->status != '') {
            $query->where('chd_status', $request->status);
        }

        // Age range filter
        if ($request->filled('age_range') && $request->age_range != '') {
            $ranges = [
                '0-6' => [0, 6],
                '7-12' => [7, 12],
                '13-24' => [13, 24],
                '25+' => [25, 1000]
            ];
            
            if (isset($ranges[$request->age_range])) {
                [$minMonths, $maxMonths] = $ranges[$request->age_range];
                $maxDate = Carbon::now()->subMonths($minMonths)->format('Y-m-d');
                $minDate = Carbon::now()->subMonths($maxMonths)->format('Y-m-d');
                
                $query->whereBetween('chd_date_of_birth', [$minDate, $maxDate]);
            }
        }

        // Sorting
        switch ($request->get('sort', 'name_asc')) {
            case 'name_desc':
                $query->orderBy('chd_last_name', 'desc');
                break;
            case 'age_asc': 
                $query->orderBy('chd_date_of_birth', 'desc'); 
                break;
            case 'age_desc': 
                $query->orderBy('chd_date_of_birth', 'asc'); 
                break;
            case 'latest':
                $query->withMax('vaccinationRecords as latest_vaccine_date', 'rec_date_administered')
                    ->orderByDesc('latest_vaccine_date');
                break;
            default:
                $query->orderBy('chd_last_name', 'asc');
                break;
        }

        $children = $query->paginate(10)->withQueryString();

        return view('vaccinations.index', compact('children'));
    }

    /**
     * Display the child's complete immunization card
     * Shows all vaccines with their statuses (completed, upcoming, overdue, not yet due)
     */
    public function showImmunizationCard(Child $child)
    {
        $childAgeDays = Carbon::parse($child->chd_date_of_birth)->diffInDays(Carbon::now());

        $vaccines = Vaccine::with(['schedule' => function($query) {
            $query->orderBy('nip_dose_number');
        }])->get();

        $immunizationData = [];

        foreach ($vaccines as $vaccine) {
            $vaccineInfo = [
                'vaccine' => $vaccine,
                'doses' => []
            ];

            foreach ($vaccine->schedule as $schedule) {
                $record = VaccinationRecord::where('rec_child_id', $child->chd_id)
                    ->where('rec_vaccine_id', $vaccine->vacc_id)
                    ->where('rec_dose_number', $schedule->nip_dose_number)
                    ->first();

                // Determine status
                $status = $this->determineVaccineStatus(
                    $childAgeDays, 
                    $schedule, 
                    $record,
                    $vaccine,
                    $schedule->nip_dose_number,
                    $child
                );

                $vaccineInfo['doses'][] = [
                    'schedule' => $schedule,
                    'record' => $record,
                    'status' => $status,
                    'status_label' => $this->getStatusLabel($status),
                    'status_color' => $this->getStatusColor($status),
                    'can_administer' => in_array($status, ['upcoming', 'overdue']) && !$record
                ];
            }

            $immunizationData[] = $vaccineInfo;
        }

        $totalDoses = NipSchedule::count();
        $completedDoses = VaccinationRecord::where('rec_child_id', $child->chd_id)
            ->where('rec_status', 'completed')
            ->count();
        $completionPercentage = $totalDoses > 0 ? round(($completedDoses / $totalDoses) * 100, 1) : 0;

        return view('vaccinations.immunization-card', compact(
            'child', 
            'immunizationData', 
            'childAgeDays',
            'completionPercentage',
            'completedDoses',
            'totalDoses'
        ));
    }

    /**
     * Determine the status of a vaccine dose based on child's age and schedule
     */
    private function determineVaccineStatus($childAgeDays, $schedule, $record, $vaccine, $doseNumber, $child)
    {
        // If already administered, return completed
        if ($record && $record->rec_status === 'completed') {
            return 'completed';
        }

        // Check if previous doses are completed (for multi-dose vaccines)
        if ($doseNumber > 1) {
            $previousDose = VaccinationRecord::where('rec_child_id', $child->chd_id)
                ->where('rec_vaccine_id', $vaccine->vacc_id)
                ->where('rec_dose_number', $doseNumber - 1)
                ->where('rec_status', 'completed')
                ->first();

            if (!$previousDose) {
                return 'not_yet_due'; // Can't give dose 2 before dose 1
            }
        }

        // Child hasn't reached minimum age yet
        if ($childAgeDays < $schedule->nip_minimum_age_days) {
            return 'not_yet_due';
        }

        // Child is within the valid window
        if ($childAgeDays >= $schedule->nip_minimum_age_days && 
            $childAgeDays <= $schedule->nip_maximum_age_days) {
            return 'upcoming';
        }

        // Child has passed the maximum age
        if ($childAgeDays > $schedule->nip_maximum_age_days) {
            return 'overdue';
        }

        return 'unknown';
    }

    /**
     * Get human-readable status label
     */
    private function getStatusLabel($status)
    {
        return match($status) {
            'completed' => 'Completed',
            'upcoming' => 'Due Now',
            'overdue' => 'Overdue',
            'not_yet_due' => 'Not Yet Due',
            default => 'Unknown'
        };
    }

    /**
     * Get status color for UI
     */
    private function getStatusColor($status)
    {
        return match($status) {
            'completed' => 'success',
            'upcoming' => 'warning',
            'overdue' => 'danger',
            'not_yet_due' => 'secondary',
            default => 'primary'
        };
    }

    /**
     * PUBLIC SEARCH: Search for child's immunization record (no login required)
     */
    public function publicSearch()
    {
        return view('public.search-record');
    }

    /**
     * PUBLIC SEARCH: Process the search and display results
     */
    public function publicSearchResults(Request $request)
    {
        $validated = $request->validate([
            'chd_first_name'    => 'required|string',
            'chd_middle_name'   => 'nullable|string',
            'chd_last_name'     => 'required|string',
            'chd_date_of_birth' => 'required|date',
            'reference_no'      => 'required|string',
        ]);

        // Extract numeric ID from reference number (e.g., "IS-2024-0001" -> "1")
        $parts = explode('-', $validated['reference_no']);
        $cleanId = (int) end($parts);

        // Find the child with matching details
        $child = Child::where('chd_id', $cleanId)
            ->where('chd_first_name', 'LIKE', $validated['chd_first_name'])
            ->where('chd_last_name', 'LIKE', $validated['chd_last_name'])
            ->where('chd_date_of_birth', $validated['chd_date_of_birth'])
            ->with(['guardian', 'address'])
            ->first();

        // Verify middle name if provided
        if ($child && $request->filled('chd_middle_name')) {
            if (strtolower($child->chd_middle_name ?? '') !== strtolower($validated['chd_middle_name'])) {
                $child = null;
            }
        }

        if (!$child) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'No record found. Please verify your information and reference number.');
        }

        // Get child's age in days
        $childAgeDays = Carbon::parse($child->chd_date_of_birth)->diffInDays(Carbon::now());

        // Get all vaccines with their records
        $vaccines = Vaccine::with(['schedule' => function($query) {
            $query->orderBy('nip_dose_number');
        }])->get();

        $immunizationData = [];

        foreach ($vaccines as $vaccine) {
            $vaccineInfo = [
                'vaccine' => $vaccine,
                'doses' => []
            ];

            foreach ($vaccine->schedule as $schedule) {
                $record = VaccinationRecord::where('rec_child_id', $child->chd_id)
                    ->where('rec_vaccine_id', $vaccine->vacc_id)
                    ->where('rec_dose_number', $schedule->nip_dose_number)
                    ->with(['administrator'])
                    ->first();

                $status = $this->determineVaccineStatus(
                    $childAgeDays, 
                    $schedule, 
                    $record,
                    $vaccine,
                    $schedule->nip_dose_number,
                    $child
                );

                $vaccineInfo['doses'][] = [
                    'schedule' => $schedule,
                    'record' => $record,
                    'status' => $status,
                    'status_label' => $this->getStatusLabel($status),
                    'status_color' => $this->getStatusColor($status)
                ];
            }

            $immunizationData[] = $vaccineInfo;
        }

        // Calculate completion
        $totalDoses = NipSchedule::count();
        $completedDoses = VaccinationRecord::where('rec_child_id', $child->chd_id)
            ->where('rec_status', 'completed')
            ->count();
        $completionPercentage = $totalDoses > 0 ? round(($completedDoses / $totalDoses) * 100, 1) : 0;

        return view('public.search-results', compact(
            'child',
            'immunizationData',
            'childAgeDays',
            'completionPercentage',
            'completedDoses',
            'totalDoses'
        ));
    }

    public function create()
    {
        $children = Child::whereIn('chd_status', ['active'])->get();
        $vaccines = Vaccine::all();
        $workers = HealthWorker::where('wrk_role', '!=', 'admin')->get();

        return view('vaccinations.create', compact('children', 'vaccines', 'workers'));
    }

    // Replace your store() method with this updated version:
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rec_child_id'          => 'required|exists:children,chd_id',
            'rec_vaccine_id'        => 'required|exists:vaccines,vacc_id',
            'rec_dose_number'       => 'required|integer|min:1',
            'rec_date_administered' => 'required|date|before_or_equal:today',
            'rec_administered_by'   => 'required|exists:health_workers,wrk_id',
            'rec_remarks'           => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            
            // Check inventory (FEFO - First Expiry, First Out)
            $inventory = VaccineInventory::where('inv_vaccine_id', $validated['rec_vaccine_id'])
                ->where('inv_quantity_available', '>', 0)
                ->orderBy('inv_expiry_date', 'asc')
                ->first();

            if (!$inventory) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Selected vaccine is out of stock!');
            }

            // Create vaccination record
            $validated['rec_status'] = 'completed';
            $record = VaccinationRecord::create($validated);

            // Deduct inventory
            $inventory->decrement('inv_quantity_available', 1);

            // Create inventory log
            InventoryLog::create([
                'log_inventory_id'     => $inventory->inv_id,
                'log_change_type'      => 'administered',
                'log_quantity_changed' => -1,
                'logable_id'           => $record->rec_id,
                'logable_type'         => VaccinationRecord::class,
                'log_user_id'          => Auth::id(), 
            ]);

            // Update child status
            $this->updateChildStatus($validated['rec_child_id']);

            // Create notification for completed vaccination
            Notification::create([
                'notif_child_id' => $validated['rec_child_id'],
                'notif_notification_type' => 'vaccination_completed',
                'notif_message' => 'Vaccination completed: ' . Vaccine::find($validated['rec_vaccine_id'])->vacc_vaccine_name . ' (Dose ' . $validated['rec_dose_number'] . ')',
                'notif_is_read' => false
            ]);

            // Redirect back to immunization card with success message
            return redirect()->route('vaccinations.immunization-card', $validated['rec_child_id'])
                ->with('success', 'Vaccination administered successfully!');
        });
    }

    /**
     * Quick update for inline editing (AJAX friendly)
     */
    public function quickUpdate(Request $request, VaccinationRecord $record)
    {
        $validated = $request->validate([
            'rec_date_administered' => 'nullable|date|before_or_equal:today',
            'rec_remarks'           => 'nullable|string|max:255',
            'rec_status'            => 'nullable|in:completed,upcoming,overdue',
        ]);

        $record->update(array_filter($validated));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Record updated successfully'
            ]);
        }

        return redirect()->back()->with('success', 'Record updated successfully.');
    }

    /**
     * Update child's overall immunization status
     */
    private function updateChildStatus($childId)
    {
        $child = Child::find($childId);
        $childAgeDays = (int) Carbon::parse($child->chd_date_of_birth)->diffInDays(Carbon::now());
        
        // Get all scheduled doses that the child should have received by now
        $dueSchedules = NipSchedule::where('nip_minimum_age_days', '<=', $childAgeDays)->get();
        
        $totalDue = $dueSchedules->count();
        $completed = 0;
        
        foreach ($dueSchedules as $schedule) {
            $record = VaccinationRecord::where('rec_child_id', $childId)
                ->where('rec_vaccine_id', $schedule->nip_vaccine_id)
                ->where('rec_dose_number', $schedule->nip_dose_number)
                ->where('rec_status', 'completed')
                ->first();
            
            if ($record) {
                $completed++;
            }
        }
        
        // Only update chd_status between 'active' and 'completed'
        // Don't override 'transferred' or 'inactive' statuses
        if (in_array($child->chd_status, ['active', 'completed'])) {
            if ($completed === $totalDue && $totalDue > 0) {
                // All vaccines completed - mark child as fully immunized
                $child->update(['chd_status' => 'completed']);
            } else {
                // Still has pending vaccines - keep as active
                $child->update(['chd_status' => 'active']);
            }
        }
        // Notifications are handled by VaccinationRecordObserver + NotificationService
    }

    public function show(VaccinationRecord $vaccination)
    {
        $vaccination->load(['child', 'vaccine', 'administrator']);
        
        return view('vaccinations.show', compact('vaccination'));
    }

    public function edit(VaccinationRecord $vaccination)
    {
        $children = Child::all();
        $vaccines = Vaccine::all();
        $workers = HealthWorker::where('wrk_role', '!=', 'admin')->get();

        return view('vaccinations.edit', compact('vaccination', 'children', 'vaccines', 'workers'));
    }

    public function update(Request $request, VaccinationRecord $vaccination)
    {
        $validated = $request->validate([
            'rec_dose_number'       => 'required|integer',
            'rec_date_administered' => 'required|date|before_or_equal:today',
            'rec_administered_by'   => 'required|exists:health_workers,wrk_id',
            'rec_remarks'           => 'nullable|string',
            'rec_status'            => 'required|in:completed,upcoming,overdue',
        ]);

        $vaccination->update($validated);
        $this->updateChildStatus($vaccination->rec_child_id);

        return redirect()->route('vaccinations.index')
            ->with('success', 'Record updated successfully.');
    }

    public function destroy(VaccinationRecord $vaccination)
    {
        return DB::transaction(function () use ($vaccination) {
            // Find and restore inventory
            $log = InventoryLog::where('logable_id', $vaccination->rec_id)
                ->where('logable_type', VaccinationRecord::class)
                ->first();

            if ($log) {
                $inventory = VaccineInventory::find($log->log_inventory_id);
                if ($inventory) {
                    $inventory->increment('inv_quantity_available', 1);
                }
                $log->delete();
            }

            $childId = $vaccination->rec_child_id;
            $vaccination->delete();

            // Update child status after deletion
            $this->updateChildStatus($childId);

            return redirect()->route('vaccinations.index')
                ->with('success', 'Record deleted and stock restored.');
        });
    }
}