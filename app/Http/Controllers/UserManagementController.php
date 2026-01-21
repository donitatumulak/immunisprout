<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\HealthWorker;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users with search and filter
     */
    public function index(Request $request)
    {
        $query = User::with(['worker.address']);

        // 1. Search by name or username
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'ilike', "%{$search}%")
                ->orWhereHas('worker', function($q2) use ($search) {
                    $q2->where('wrk_first_name', 'ilike', "%{$search}%")
                        ->orWhere('wrk_last_name', 'ilike', "%{$search}%")
                        ->orWhereRaw("CONCAT(wrk_first_name, ' ', wrk_last_name) ilike ?", ["%{$search}%"]);
                });
            });
        }

        // 2. Filter by role (via Relationship)
        if ($request->filled('role')) {
            $query->whereHas('worker', function($q) use ($request) {
                $q->where('wrk_role', $request->role);
            });
        }

        // 3. Filter by status (Directly on User table)
        if ($request->filled('status')) {
            $query->where('user_status', $request->status);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        $roles = HealthWorker::distinct()->whereNotNull('wrk_role')->pluck('wrk_role');
        $statuses = User::distinct()->whereNotNull('user_status')->pluck('user_status');

        return view('user-management.index', compact('users', 'roles', 'statuses'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = ['Admin', 'Nurse', 'Midwife', 'BHW']; 
        return view('user-management.create', compact('roles'));
    }

    /**
     * Store a newly created user with health worker profile and address
     */
    public function store(Request $request)
    {
        $validated = $this->validateUserData($request);

        try {
            DB::beginTransaction();

            // 1. Create Address
            $address = Address::create([
                'addr_line_1' => $validated['addr_line_1'],
                'addr_line_2' => $validated['addr_line_2'] ?? null,
                'addr_barangay' => $validated['addr_barangay'],
                'addr_city_municipality' => $validated['addr_city_municipality'],
                'addr_province' => $validated['addr_province'],
                'addr_zip_code' => $validated['addr_zip_code'] ?? null,
            ]);

            // 2. Create Health Worker
            $healthWorker = HealthWorker::create([
                'wrk_first_name' => $validated['wrk_first_name'],
                'wrk_middle_name' => $validated['wrk_middle_name'] ?? null,
                'wrk_last_name' => $validated['wrk_last_name'],
                'wrk_contact_number' => $validated['wrk_contact_number'],
                'wrk_addr_id' => $address->addr_id,
                'wrk_role' => $validated['wrk_role'],
            ]);

            // 3. Create User Account
            User::create([
                'worker_id' => $healthWorker->wrk_id,
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
            ]);

            DB::commit();

            return redirect()->route('user-management.index')
                           ->with('success', 'User account created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user
     */
    public function show($id)
    {
        $user = User::with(['worker.address'])->findOrFail($id);
        return view('user-management.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit($id)
    {
        $user = User::with(['worker.address'])->findOrFail($id);
        $roles = ['Admin', 'Nurse', 'Midwife', 'BHW'];
        
        return view('user-management.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user with health worker and address
     */
    public function update(Request $request, $id)
    {
        $user = User::with(['worker.address'])->findOrFail($id);
        $validated = $this->validateUserData($request, $id);

        try {
            DB::beginTransaction();

            // 1. Update Address
            $user->worker->address->update([
                'addr_line_1' => $validated['addr_line_1'],
                'addr_line_2' => $validated['addr_line_2'] ?? null,
                'addr_barangay' => $validated['addr_barangay'],
                'addr_city_municipality' => $validated['addr_city_municipality'],
                'addr_province' => $validated['addr_province'],
                'addr_zip_code' => $validated['addr_zip_code'] ?? null,
            ]);

            // 2. Update Health Worker
            $user->worker->update([
                'wrk_first_name' => $validated['wrk_first_name'],
                'wrk_middle_name' => $validated['wrk_middle_name'] ?? null,
                'wrk_last_name' => $validated['wrk_last_name'],
                'wrk_contact_number' => $validated['wrk_contact_number'],
                'wrk_role' => $validated['wrk_role'],
            ]);
            
            DB::commit();

            return redirect()->route('user-management.index')
                           ->with('success', 'User account updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user 
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $user = User::with(['worker.address'])->findOrFail($id);
            
            // Store IDs before the records vanish
            $worker = $user->worker;
            $address = $worker ? $worker->address : null;

            // 1. Delete User Account
            $user->delete();

            // 2. Delete Worker Profile
            if ($worker) {
                $worker->delete(); // This triggers soft delete if configured
            }

            // 3. Clean up the Address manually (since it's now set to null on the worker)
            if ($address) {
                $address->delete();
            }

            DB::commit();
            return redirect()->route('user-management.index')->with('success', 'User and associated records deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user account status (activate/deactivate)
     */
    public function toggleStatus($id)
    {
        // Find the user from the users table
        $user = User::findOrFail($id);

        // Toggle the user_status column
        $user->user_status = ($user->user_status === 'active') ? 'inactive' : 'active';

        $user->save();

        return redirect()->back()
                        ->with('success', 'User status updated successfully!');
    }

    /**
     * Helper function to validate user data for create and edit
     */
    private function validateUserData(Request $request, $userId = null)
    {
        $isUpdate = $userId !== null;

        $rules = [
            // Health Worker Rules
            'wrk_first_name' => 'required|string|max:255',
            'wrk_middle_name' => 'nullable|string|max:255',
            'wrk_last_name' => 'required|string|max:255',
            'wrk_contact_number' => 'required|string|max:20',
            'wrk_role' => 'required|string|max:100',

            // Address Rules
            'addr_line_1' => 'required|string|max:255',
            'addr_barangay' => 'required|string|max:255',
            'addr_city_municipality' => 'required|string|max:255',
            'addr_province' => 'required|string|max:255',

            // Account Rules - Make conditional!
            // If updating, username must be unique EXCEPT for the current user
            'username' => [
                $isUpdate ? 'nullable' : 'required', 
                'string', 
                'max:255', 
                Rule::unique('users', 'username')->ignore($userId),
            ],
            // Password only required on creation
            'password' => [
                $isUpdate ? 'nullable' : 'required', 
                'string', 
                'min:8'
            ],
        ];

        $messages = [
            'username.required' => 'Username is required',
            'username.unique' => 'This username is already taken',
            'username.alpha_dash' => 'Username can only contain letters, numbers, dashes and underscores',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'wrk_first_name.required' => 'First name is required',
            'wrk_last_name.required' => 'Last name is required',
            'wrk_contact_number.required' => 'Contact number is required',
            'wrk_role.required' => 'Role is required',
            'addr_line_1.required' => 'Address line 1 is required',
            'addr_barangay.required' => 'Barangay is required',
            'addr_city_municipality.required' => 'City/Municipality is required',
            'addr_province.required' => 'Province is required',
        ];

        return $request->validate($rules, $messages);
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $updateData = [
            'username' => $request->username,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->back()
                        ->with('success', 'Account credentials updated successfully!');
    }
}