<?php

namespace App\Http\Controllers;

use App\Models\HealthWorker;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class HealthWorkerController extends Controller
{
    public function index()
    {
        // Eager load the address for each worker
        $workers = HealthWorker::with('address')->latest()->get();
        return view('health_workers.index', compact('workers'));
    }

    public function create()
    {
        return view('health_workers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'wrk_first_name'     => 'required|string|max:150',
            'wrk_middle_name'    => 'nullable|string|max:150',
            'wrk_last_name'      => 'required|string|max:150',
            'wrk_contact_number' => 'required|digits:11|starts_with:09',
            'wrk_role'           => ['required', Rule::in(['nurse', 'midwife', 'admin', 'bhw'])],
            
            // Address fields for the worker
            'addr_line_1'            => 'required|string|max:150',
            'addr_line_2'            => 'nullable|string|max:150',
            'addr_barangay'          => 'required|string|max:150',
            'addr_city_municipality' => 'required|string|max:150',
            'addr_province'          => 'required|string|max:150',
            'addr_zip_code'          => 'required|string|max:20',
        ]);

        DB::transaction(function () use ($validated) {
            // 1. Create the Address record
            $address = Address::create([
                'addr_line_1'            => $validated['addr_line_1'],
                'addr_line_2'            => $validated['addr_line_2'],
                'addr_barangay'          => $validated['addr_barangay'],
                'addr_city_municipality' => $validated['addr_city_municipality'],
                'addr_province'          => $validated['addr_province'],
                'addr_zip_code'          => $validated['addr_zip_code'],
            ]);

            // 2. Create the Health Worker and link the address
            HealthWorker::create([
                'wrk_first_name'     => $validated['wrk_first_name'],
                'wrk_middle_name'    => $validated['wrk_middle_name'],
                'wrk_last_name'      => $validated['wrk_last_name'],
                'wrk_contact_number' => $validated['wrk_contact_number'],
                'wrk_role'           => $validated['wrk_role'],
                'wrk_addr_id'        => $address->addr_id, // Link to the address we just made
            ]);
        });

        return redirect()->route('health-workers.index')->with('success', 'Health Worker added!');
    }

    public function show(HealthWorker $healthWorker)
    {
        // Eager load the address so we can display it on the profile
        $healthWorker->load('address');
        
        return view('health_workers.show', compact('healthWorker'));
    }

    public function edit(HealthWorker $healthWorker)
    {
        // Load the address so the form fields (like addr_line_1) can be pre-filled
        $healthWorker->load('address');
        
        // We pass the allowed roles so the dropdown in the view stays consistent
        $roles = ['nurse', 'midwife', 'admin', 'bhw'];

        return view('health_workers.edit', compact('healthWorker', 'roles'));
    }

    public function update(Request $request, HealthWorker $healthWorker)
    {
        $validated = $request->validate([
            'wrk_first_name'     => 'required|string|max:150',
            'wrk_middle_name'    => 'nullable|string|max:150',
            'wrk_last_name'      => 'required|string|max:150',
            'wrk_contact_number' => 'required|digits:11|starts_with:09',
            'wrk_role'           => ['required', Rule::in(['nurse', 'midwife', 'admin', 'bhw'])],
            
            'addr_line_1'            => 'required|string|max:150',
            'addr_line_2'            => 'nullable|string|max:150',
            'addr_barangay'          => 'required|string|max:150',
            'addr_city_municipality' => 'required|string|max:150',
            'addr_province'          => 'required|string|max:150',
            'addr_zip_code'          => 'required|string|max:20',
        ]);

        DB::transaction(function () use ($validated, $healthWorker) {
            // Update the linked address
            $healthWorker->address->update([
                'addr_line_1'            => $validated['addr_line_1'],
                'addr_line_2'            => $validated['addr_line_2'],
                'addr_barangay'          => $validated['addr_barangay'],
                'addr_city_municipality' => $validated['addr_city_municipality'],
                'addr_province'          => $validated['addr_province'],
                'addr_zip_code'          => $validated['addr_zip_code'],
            ]);

            // Update worker details
            $healthWorker->update([
                'wrk_first_name'     => $validated['wrk_first_name'],
                'wrk_middle_name'    => $validated['wrk_middle_name'],
                'wrk_last_name'      => $validated['wrk_last_name'],
                'wrk_contact_number' => $validated['wrk_contact_number'],
                'wrk_role'           => $validated['wrk_role'],
            ]);
        });

        return redirect()->route('health-workers.index')->with('success', 'Worker record updated!');
    }

    public function destroy(HealthWorker $healthWorker)
    {
        try {
            // Option A: Hard Delete (Only if they haven't recorded any vaccinations yet)
            $healthWorker->delete();
            return redirect()->route('health-workers.index')->with('success', 'Health Worker removed.');
            
        } catch (\Illuminate\Database\QueryException $e) {
            // Option B: If they have records, you can't delete them. 
            // You should suggest "Deactivating" them instead.
            return redirect()->back()->with('error', 'Cannot delete worker. They have existing medical records. Try deactivating them instead.');
        }
    }
}