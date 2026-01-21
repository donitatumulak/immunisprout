<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $user->load(['worker.address']);
        return view('profile.index', compact('user'));
    }

    /**
     * Update General Profile and Address Information
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $worker = $user->worker;
        $address = $worker->address;

        // Validate using helper method
        $validated = $this->validateProfileUpdate($request, $user->id);

        // Use a Transaction to ensure all 3 tables update or none at all
        DB::transaction(function () use ($validated, $user, $worker, $address) {
            
            // 1. Update Address
            $address->update([
                'addr_line_1'            => $validated['addr_line_1'],
                'addr_line_2'            => $validated['addr_line_2'] ?? null,
                'addr_barangay'          => $validated['addr_barangay'],
                'addr_city_municipality' => $validated['addr_city_municipality'],
                'addr_province'          => $validated['addr_province'],
                'addr_zip_code'          => $validated['addr_zip_code'],
            ]);

            // 2. Update Worker
            $worker->update([
                'wrk_first_name'     => $validated['wrk_first_name'],
                'wrk_middle_name'    => $validated['wrk_middle_name'] ?? null,
                'wrk_last_name'      => $validated['wrk_last_name'],
                'wrk_contact_number' => $validated['wrk_contact_number'],
                'wrk_role'           => $validated['wrk_role'],
            ]);

            // 3. Update User Account (Username)
            $user->update([
                'username' => $validated['username'],
            ]);
        });

        return back()->with('success', 'Profile and Address updated successfully!');
    }

    /**
     * Update Password
     */
    public function updatePassword(Request $request)
    {
        // Validate using helper method
        $validated = $this->validatePasswordUpdate($request);

        /** @var User $user */
        $user = Auth::user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    /**
     * Validation helper for profile update
     * 
     * @param Request $request
     * @param int $userId
     * @return array
     */
    private function validateProfileUpdate(Request $request, int $userId): array
    {
        return $request->validate([
            // Health Worker Validation
            'wrk_first_name'     => 'required|string|max:150',
            'wrk_middle_name'    => 'nullable|string|max:150',
            'wrk_last_name'      => 'required|string|max:150',
            'wrk_contact_number' => 'required|string|max:20',
            'wrk_role'           => ['required', Rule::in(['nurse', 'midwife', 'admin', 'bhw'])],
            
            // Username Validation (Ignoring current user)
            'username' => [
                'required', 
                'string', 
                'max:50',
                Rule::unique('users', 'username')->ignore($userId)
            ],

            // Address Validation
            'addr_line_1'            => 'required|string|max:150',
            'addr_line_2'            => 'nullable|string|max:150',
            'addr_barangay'          => 'required|string|max:150',
            'addr_city_municipality' => 'required|string|max:150',
            'addr_province'          => 'required|string|max:150',
            'addr_zip_code'          => 'required|string|max:20',
        ]);
    }

    /**
     * Validation helper for password update
     * 
     * @param Request $request
     * @return array
     */
    private function validatePasswordUpdate(Request $request): array
    {
        return $request->validate([
            'current_password' => 'required|current_password',
            'password'         => 'required|min:8|confirmed',
        ]);
    }
}