<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Guardian;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ChildController extends Controller
{
    /**
     * Helper to keep validation centralized.
     */
    private function validateChild(Request $request, $isUpdate = false)
    {
        $rules = [
            'chd_first_name'       => 'required|string|max:150',
            'chd_middle_name'      => 'nullable|string|max:150',
            'chd_last_name'        => 'required|string|max:150',
            'chd_date_of_birth'    => 'required|date|before_or_equal:today',
            'chd_sex'              => ['required', Rule::in(['male', 'female'])],
            'chd_residency_status' => 'required|string',
        ];

        if ($isUpdate) {
            $rules['chd_status']         = 'required|string';
            $rules['grd_first_name']     = 'required|string|max:150';
            $rules['grd_last_name']      = 'required|string|max:150';
            $rules['grd_contact_number'] = 'required|string|max:20';
            $rules['grd_relationship']   = 'nullable|string'; 
            $rules['addr_line_1']        = 'required|string|max:255';
            $rules['addr_line_2']        = 'nullable|string|max:255';
            $rules['addr_barangay']      = 'required|string|max:150';
            $rules['addr_city_municipality'] = 'nullable|string|max:150';
            $rules['addr_province']      = 'nullable|string|max:150';
            $rules['addr_zipcode']       = 'nullable|string|max:10';
        } else {
            // For creating new child
            if ($request->has('new_guardian')) {
                $rules['grd_first_name']     = 'required|string|max:150';
                $rules['grd_last_name']      = 'required|string|max:150';
                $rules['grd_contact_number'] = 'nullable|string|max:20';
                $rules['grd_relationship']   = 'nullable|string';
                $rules['addr_line_1']        = 'required|string|max:255';
                $rules['addr_line_2']        = 'nullable|string|max:255';
                $rules['addr_barangay']      = 'required|string|max:150';
                $rules['addr_city_municipality'] = 'nullable|string|max:150';
                $rules['addr_province']      = 'nullable|string|max:150';
                $rules['addr_zipcode']       = 'nullable|string|max:10';
            } else {
                $rules['chd_guardian_id'] = 'required|exists:guardians,grd_id';
            }
        }

        return $request->validate($rules);
    }

    /**
     * INDEX: Handles Search, Sex, Residency, and Status filters.
     */
    public function index(Request $request)
    {
        // 1. Initialize the query
        if ($request->status === 'trashed') {
            $query = Child::onlyTrashed();
        } else {
            $query = Child::query();
        }

        $query->with(['guardian', 'address']);

        // 2. Filter: Search (Name or Guardian)
        if ($request->filled('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('chd_first_name', 'ilike', "%{$search}%")
                ->orWhere('chd_last_name', 'ilike', "%{$search}%")
                ->orWhereHas('guardian', function($g) use ($search) {
                    $g->where('grd_last_name', 'ilike', "%{$search}%")
                        ->orWhere('grd_first_name', 'ilike', "%{$search}%");
                });
            });
        }

        // 3. Filter: Sex
        if ($request->filled('sex') && $request->sex != '') {
            $query->where('chd_sex', $request->sex);
        }

        // 4. Filter: Residency
        if ($request->filled('residency') && $request->residency != '') {
            $query->where('chd_residency_status', $request->residency);
        }

        // 5. Filter: Status 
        if ($request->filled('status') && $request->status != '' && !in_array($request->status, ['all', 'trashed'])) {
            $query->where('chd_status', $request->status);
        }

        // 6. Execute Pagination
        $children = $query->latest()->paginate(6)->withQueryString();

        $guardians = Guardian::all();
        $addresses = Address::all();

        return view('children.index', compact('children', 'guardians', 'addresses'));
    }

    /**
     * Show the form for creating a new child.
     */
    public function create()
    {
        $guardians = Guardian::orderBy('grd_last_name', 'asc')->get();

        return view('children.create', compact('guardians'));
    }

    /**
     * STORE: Create new child with guardian and address handling.
     */
    public function store(Request $request)
    {
        try {
            $validated = $this->validateChild($request);

            DB::beginTransaction();

            $guardianId = null;
            $addrId = null;

            // Scenario 1: New Guardian (which requires new address)
            if ($request->has('new_guardian') || 
                $request->input('new_guardian') == '1' || 
                $request->input('new_guardian') == 'on' ||
                (!$request->filled('chd_guardian_id') && $request->filled('grd_first_name'))) {
                
                // Create new address
                $address = Address::create([
                    'addr_line_1'   => $request->input('addr_line_1'),
                    'addr_line_2'   => $request->input('addr_line_2'),
                    'addr_barangay' => $request->input('addr_barangay'),
                    'addr_city_municipality' => $request->input('addr_city', 'Lapu-Lapu City'),
                    'addr_province' => $request->input('addr_province', 'Cebu'),
                    'addr_zip_code'  => $request->input('addr_zip_code'),
                ]);
                
                $addrId = $address->addr_id;

                // Create new guardian with the new address
                $guardian = Guardian::create([
                    'grd_first_name'      => $request->input('grd_first_name'),
                    'grd_middle_name'      => $request->input('grd_middle_name'),
                    'grd_last_name'       => $request->input('grd_last_name'),
                    'grd_contact_number'  => $request->input('grd_contact_number'),
                    'grd_relationship'    => $request->input('grd_relationship'),
                    'grd_current_addr_id' => $addrId,
                ]);
                
                $guardianId = $guardian->grd_id;

            } else {
                // Scenario 2: Existing Guardian
                $guardianId = $request->input('chd_guardian_id');
                
                $guardian = Guardian::findOrFail($guardianId);
                $addrId = $guardian->grd_current_addr_id;
            }

            // Create the child record
            $child = Child::create([
                'chd_first_name'       => $validated['chd_first_name'],
                'chd_middle_name'      => $validated['chd_middle_name'] ?? null,
                'chd_last_name'        => $validated['chd_last_name'],
                'chd_date_of_birth'    => $validated['chd_date_of_birth'],
                'chd_sex'              => $validated['chd_sex'],
                'chd_residency_status' => $validated['chd_residency_status'],
                'chd_guardian_id'      => $guardianId,
                'chd_current_addr_id'  => $addrId,
                'chd_status'           => 'active',
            ]);

            DB::commit();

           return redirect()->route('vaccinations.immunization-card', $child->chd_id)
            ->with('success', 'Child registered successfully! You can now record birth doses.');

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to register child: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to register child: ' . $e->getMessage());
        }
    }

    /**
     * EDIT: Show edit form
     */
    public function edit(Child $child)
    {
        $child->load(['guardian', 'address']);
        $guardians = Guardian::all();
        $addresses = Address::all();
        
        return view('children.edit', compact('child', 'guardians', 'addresses'));
    }

    /**
     * UPDATE: Updates existing child, guardian, and address.
     */
    public function update(Request $request, Child $child)
    {
        try {
            $validated = $this->validateChild($request, true);

            DB::beginTransaction();

            // Update Address if it exists
            if ($child->address) {
                $child->address->update([
                    'addr_line_1'   => $validated['addr_line_1'],
                    'addr_line_2'   => $request->addr_line_2,
                    'addr_barangay' => $validated['addr_barangay'],
                    'addr_city_municipality' => $request->addr_city_municipality ?? 'Lapu-Lapu City',
                    'addr_province' => $request->addr_province ?? 'Cebu',
                    'addr_zipcode'  => $request->addr_zipcode,
                ]);
            }

            // Update Guardian if it exists
            if ($child->guardian) {
                $child->guardian->update([
                    'grd_first_name'     => $validated['grd_first_name'],
                    'grd_last_name'      => $validated['grd_last_name'],
                    'grd_contact_number' => $request->grd_contact_number,
                    'grd_relationship'   => $request->grd_relationship,
                ]);
            }

            // Update Child
            $child->update([
                'chd_first_name'       => $validated['chd_first_name'],
                'chd_middle_name'      => $validated['chd_middle_name'] ?? null,
                'chd_last_name'        => $validated['chd_last_name'],
                'chd_date_of_birth'    => $validated['chd_date_of_birth'],
                'chd_sex'              => $validated['chd_sex'],
                'chd_residency_status' => $validated['chd_residency_status'],
                'chd_status'           => $validated['chd_status'],
            ]);

            DB::commit();

            return redirect()->route('children.index')->with('success', 'Child record updated successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update child record: ' . $e->getMessage());
        }
    }

    /**
     * DESTROY: Soft delete child record.
     */
    public function destroy(Child $child)
    {
        try {
            $child->delete();
            return redirect()->route('children.index')->with('success', 'Child record moved to trash.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete child record: ' . $e->getMessage());
        }
    }

    /**
     * RESTORE: Restore soft-deleted child record.
     */
    public function restore($id)
    {
        try {
            $child = Child::withTrashed()->findOrFail($id);
            $child->restore();
            
            return redirect()->route('children.index')->with('success', 'Child record restored successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to restore child record: ' . $e->getMessage());
        }
    }

    /**
     * FORCE DELETE: Permanently delete child record.
     */
    public function forceDelete($id)
    {
        try {
            $child = Child::withTrashed()->findOrFail($id);
            $child->forceDelete();
            
            return redirect()->route('children.index')->with('success', 'Child record permanently deleted!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to permanently delete child record: ' . $e->getMessage());
        }
    }
}