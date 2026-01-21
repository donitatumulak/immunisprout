<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GuardianController extends Controller
{
    /**
     * 1. INDEX: List all guardians with their current addresses.
     */
    public function index()
    {
        // Eager load both current and permanent address relationships
        $guardians = Guardian::with(['currentAddress', 'permanentAddress'])->latest()->get();
        return view('guardians.index', compact('guardians'));
    }

    /**
     * 2. CREATE: Form to add a new guardian and their address.
     */
    public function create()
    {
        return view('guardians.create');
    }

    /**
     * 3. STORE: Save both Address and Guardian.
     */
    public function store(Request $request)
    {
       $validated = $request->validate([
            'grd_first_name'     => 'required|string|max:150',
            'grd_middle_name'    => 'nullable|string|max:150',
            'grd_last_name'      => 'required|string|max:150',
            'grd_contact_number' => 'required|digits:11|starts_with:09',

            // Current Address Fields
            'curr_addr_line_1'   => 'required|string|max:150',
            'curr_addr_line_2'   => 'nullable|string|max:150',
            'curr_addr_barangay' => 'required|string|max:150',
            'curr_addr_city'     => 'required|string|max:150',
            'curr_addr_province' => 'required|string|max:150',
            'curr_addr_zip'      => 'required|string|max:20',

            // Permanent Address Toggle
            'same_as_current'    => 'nullable|boolean',

            // Permanent Address Fields (only required if NOT same_as_current)
            'perm_addr_line_1'   => 'required_without:same_as_current|nullable|string|max:150',
            'perm_addr_barangay' => 'required_without:same_as_current|nullable|string|max:150',
            'perm_addr_city'     => 'required_without:same_as_current|nullable|string|max:150',
            'perm_addr_province' => 'required_without:same_as_current|nullable|string|max:150',
            'perm_addr_zip'      => 'required_without:same_as_current|nullable|string|max:20',

            'grd_relationship'   => ['required', Rule::in(['mother', 'father', 'guardian', 'grandparent', 'other'])],
        ]);

        DB::transaction(function () use ($request, $validated) {
            // 1. Create Current Address
            $currentAddress = Address::create([
                'addr_line_1'            => $validated['curr_addr_line_1'],
                'addr_line_2'            => $validated['curr_addr_line_2'],
                'addr_barangay'          => $validated['curr_addr_barangay'],
                'addr_city_municipality' => $validated['curr_addr_city'],
                'addr_province'          => $validated['curr_addr_province'],
                'addr_zip_code'          => $validated['curr_addr_zip'],
            ]);

            $permAddressId = null;

            // 2. Determine Permanent Address
            if ($request->has('same_as_current')) {
                // Point to the same ID
                $permAddressId = $currentAddress->addr_id;
            } else {
                // Create a different Address record
                $permAddress = Address::create([
                    'addr_line_1'            => $validated['perm_addr_line_1'],
                    'addr_barangay'          => $validated['perm_addr_barangay'],
                    'addr_city_municipality' => $validated['perm_addr_city'],
                    'addr_province'          => $validated['perm_addr_province'],
                    'addr_zip_code'          => $validated['perm_addr_zip'],
                ]);
                $permAddressId = $permAddress->addr_id;
            }

            // 3. Create Guardian
            Guardian::create([
                'grd_first_name'      => $validated['grd_first_name'],
                'grd_middle_name'     => $validated['grd_middle_name'],
                'grd_last_name'       => $validated['grd_last_name'],
                'grd_contact_number'  => $validated['grd_contact_number'],
                'grd_current_addr_id' => $currentAddress->addr_id,
                'grd_permanent_addr_id'    => $permAddressId,
                'grd_relationship'   => $validated['grd_relationship'],
            ]);
        });

        return redirect()->route('guardians.index')->with('success', 'Guardian and addresses registered!');
    }

    /**
     * 4. SHOW: View guardian profile and all their registered children.
     */
    public function show(Guardian $guardian)
    {
        // Note: Ensure your Guardian model has 'currentAddress' and 'permanentAddress' relationships defined
        $guardian->load(['currentAddress', 'permanentAddress', 'children']);
        return view('guardians.show', compact('guardian'));
    }

    /**
     * 5. EDIT: Show form with current data.
     */
    public function edit(Guardian $guardian)
    {
        $guardian->load('address');
        return view('guardians.edit', compact('guardian'));
    }

    /**
     * 6. UPDATE: Update both the Guardian and their Address.
     */
    public function update(Request $request, Guardian $guardian)
    {
        $validated = $request->validate([
            'grd_first_name'      => 'required|string|max:150',
            'grd_middle_name'     => 'nullable|string|max:150',
            'grd_last_name'       => 'required|string|max:150',
            'grd_contact_number'  => 'required|digits:11|starts_with:09',

            // Current Address Validation
            'curr_addr_line_1'    => 'required|string|max:150',
            'curr_addr_barangay'  => 'required|string|max:150',
            'curr_addr_city'      => 'required|string|max:150',
            'curr_addr_province' => 'required|string|max:150',
            'curr_addr_zip'      => 'required|string|max:20',

            // Toggle and Permanent Address Validation
            'same_as_current'     => 'nullable|boolean',

            // Permanent Address Fields (only required if NOT same_as_current)
            'perm_addr_line_1'   => 'required_without:same_as_current|nullable|string|max:150',
            'perm_addr_barangay' => 'required_without:same_as_current|nullable|string|max:150',
            'perm_addr_city'     => 'required_without:same_as_current|nullable|string|max:150',
            'perm_addr_province' => 'required_without:same_as_current|nullable|string|max:150',
            'perm_addr_zip'      => 'required_without:same_as_current|nullable|string|max:20',

            'grd_relationship'   => ['required', Rule::in(['mother', 'father', 'guardian', 'grandparent', 'other'])],
        ]);

        DB::transaction(function () use ($request, $validated, $guardian) {
            // 1. Update the Current Address record directly
            $guardian->currentAddress->update([
                'addr_line_1'            => $validated['curr_addr_line_1'],
                'addr_barangay'          => $validated['curr_addr_barangay'],
                'addr_city_municipality' => $validated['curr_addr_city'],
                'addr_province'          => $validated['curr_addr_province'],
                'addr_zip'               => $validated['curr_addr_zip'],
            ]);

            // 2. Handle Permanent Address Logic
            if ($request->has('same_as_current')) {
                // If they are the same, point the Permanent ID to the Current ID
                $guardian->grd_perm_addr_id = $guardian->grd_current_addr_id;
            } else {
                // If they are different:
                // Check if we need to create a new record or update an existing separate one
                if ($guardian->grd_perm_addr_id == $guardian->grd_current_addr_id) {
                    // They WERE the same, but now they are different. Create a NEW address row.
                    $newPerm = Address::create([
                        'addr_line_1'            => $validated['perm_addr_line_1'],
                        'addr_barangay'          => $validated['perm_addr_barangay'],
                        'addr_city_municipality' => $validated['perm_addr_city'],
                        'addr_province'          => $validated['perm_addr_province'],
                        'addr_zip_code'          => $validated['perm_addr_zip'],
                    ]);
                    $guardian->grd_perm_addr_id = $newPerm->addr_id;
                } else {
                    // They were ALREADY different. Just update the existing permanent row.
                    $guardian->permanentAddress->update([
                        'addr_line_1'            => $validated['perm_addr_line_1'],
                        'addr_barangay'          => $validated['perm_addr_barangay'],
                        'addr_city_municipality' => $validated['perm_addr_city'],
                        'addr_province'          => $validated['perm_addr_province'],
                        'addr_zip_code'          => $validated['perm_addr_zip'],
                    ]);
                }
            }

            // 3. Save Guardian Info
            $guardian->update([
                'grd_first_name'      => $validated['grd_first_name'],
                'grd_middle_name'     => $validated['grd_middle_name'],
                'grd_last_name'       => $validated['grd_last_name'],
                'grd_contact_number'  => $validated['grd_contact_number'],
                'grd_permanent_addr_id'    => $guardian->grd_perm_addr_id, // Sync the ID
                'grd_relationship'   => $validated['grd_relationship'],
            ]);
        });

        return redirect()->route('guardians.index')->with('success', 'Information updated!');
    }

    /**
     * 7. DESTROY: Delete Guardian (and potentially their address).
     */
    public function destroy(Guardian $guardian)
    {
        // Note: You should be careful here if children are still linked to this guardian
        $guardian->delete();
        return redirect()->route('guardians.index')->with('success', 'Guardian record removed.');
    }
}