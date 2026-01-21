<?php

namespace App\Http\Controllers;

use App\Models\VaccineInventory;
use App\Models\InventoryLog;
use App\Models\Vaccine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class InventoryController extends Controller
{
    /**
     * Centralized validation helper
     */
    private function validateInventory(Request $request, $isUpdate = false, $inventoryId = null)
    {
        $rules = [
            'inv_vaccine_id'         => 'required|exists:vaccines,vacc_id',
            'inv_quantity_available' => 'required|integer|min:0',
            'inv_expiry_date'        => 'required|date|after:today',
            'inv_received_date'      => 'nullable|date|before_or_equal:today',
            'inv_source'             => 'nullable|string|max:150',
        ];

        // Batch number validation
        if ($isUpdate && $inventoryId) {
            $rules['inv_batch_number'] = 'required|string|max:100|unique:vaccine_inventory,inv_batch_number,' . $inventoryId . ',inv_id';
        } else {
            $rules['inv_batch_number'] = 'required|string|max:100|unique:vaccine_inventory,inv_batch_number';
        }

        if ($isUpdate) {
            $rules['inv_expiry_date'] = 'required|date';
        }

        return $request->validate($rules);
    }

    /**
     * INDEX: Display all inventory with statistics
     */
    public function index(Request $request)
    {
        $query = VaccineInventory::with('vaccine')->latest('inv_received_date');

        // Filter by vaccine type
        if ($request->filled('vaccine_id') && $request->vaccine_id != 'all') {
            $query->where('inv_vaccine_id', $request->vaccine_id);
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'critical':
                    $query->where('inv_quantity_available', '<=', 10);
                    break;
                case 'low':
                    $query->whereBetween('inv_quantity_available', [11, 50]);
                    break;
                case 'good':
                    $query->where('inv_quantity_available', '>', 50);
                    break;
            }
        }

        // Filter by expiry status
        if ($request->filled('expiry_status')) {
            switch ($request->expiry_status) {
                case 'expired':
                    $query->where('inv_expiry_date', '<', now());
                    break;
                case 'expiring_soon':
                    $query->whereBetween('inv_expiry_date', [now(), now()->addDays(30)]);
                    break;
                case 'good':
                    $query->where('inv_expiry_date', '>', now()->addDays(30));
                    break;
            }
        }

        $inventory = $query->paginate(10)->withQueryString();
        $vaccineList = Vaccine::all();

        // Calculate statistics
        $criticalStockCount = VaccineInventory::where('inv_quantity_available', '<=', 10)->count();
        $expiringSoonCount = VaccineInventory::where('inv_expiry_date', '<=', now()->addDays(30))
                                             ->where('inv_expiry_date', '>', now())
                                             ->sum('inv_quantity_available');
        $expiredCount = VaccineInventory::where('inv_expiry_date', '<', now())
                                        ->sum('inv_quantity_available');

        return view('inventory.index', compact(
            'inventory', 
            'vaccineList', 
            'criticalStockCount', 
            'expiringSoonCount',
            'expiredCount'
        ));
    }

    /**
     * STORE: Create new inventory batch with restock log
     */
    public function store(Request $request)
    {
        try {
            $validated = $this->validateInventory($request);

            DB::beginTransaction();

            // Create inventory record
            $inventory = VaccineInventory::create([
                'inv_vaccine_id'         => $validated['inv_vaccine_id'],
                'inv_batch_number'       => $validated['inv_batch_number'],
                'inv_quantity_available' => $validated['inv_quantity_available'],
                'inv_expiry_date'        => $validated['inv_expiry_date'],
                'inv_received_date'      => $validated['inv_received_date'] ?? now(),
                'inv_source'             => $validated['inv_source'] ?? null,
            ]);

            // Create restock log with user tracking
            InventoryLog::create([
                'log_inventory_id'     => $inventory->inv_id,
                'log_change_type'      => 'restock',
                'log_quantity_changed' => $validated['inv_quantity_available'],
                'log_user_id'          => Auth::id(), 
                'log_remarks'          => 'Initial stock added',
            ]);

            DB::commit();

            return redirect()->route('inventory.index')
                ->with('success', 'Vaccine batch added successfully and logged!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to add inventory: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add inventory: ' . $e->getMessage());
        }
    }

    /**
     * SHOW: Display inventory details with logs
     */
    public function show($id)
    {
        try {
            $inventory = VaccineInventory::with([
                'vaccine', 
                'logs' => function($query) {
                    $query->with('user')->latest();
                }
            ])->findOrFail($id);

            return view('inventory.show', compact('inventory'));

        } catch (Exception $e) {
            return redirect()->route('inventory.index')
                ->with('error', 'Inventory record not found.');
        }
    }

    /**
     * UPDATE: Edit batch details and log adjustments
     */
    public function update(Request $request, $id)
    {
        try {
            $inventory = VaccineInventory::findOrFail($id);
            $oldQty = $inventory->inv_quantity_available;
            $oldBatch = $inventory->inv_batch_number;
            $oldExpiry = $inventory->inv_expiry_date;

            $validated = $this->validateInventory($request, true, $id);

            DB::beginTransaction();

            $newQty = $validated['inv_quantity_available'];

            // Update inventory
            $inventory->update([
                'inv_vaccine_id'         => $validated['inv_vaccine_id'],
                'inv_batch_number'       => $validated['inv_batch_number'],
                'inv_quantity_available' => $validated['inv_quantity_available'],
                'inv_expiry_date'        => $validated['inv_expiry_date'],
                'inv_received_date'      => $validated['inv_received_date'] ?? $inventory->inv_received_date,
                'inv_source'             => $validated['inv_source'] ?? $inventory->inv_source,
            ]);

            // Log quantity changes
            if ($oldQty != $newQty) {
                $difference = $newQty - $oldQty;
                $changeType = $difference > 0 ? 'correction_add' : 'correction_sub';
                
                InventoryLog::create([
                    'log_inventory_id'     => $inventory->inv_id,
                    'log_change_type'      => $changeType,
                    'log_quantity_changed' => abs($difference),
                    'log_user_id'          => Auth::id(),
                    'log_remarks'          => $difference > 0 
                        ? "Manual adjustment: Added {$difference} doses" 
                        : "Manual adjustment: Removed " . abs($difference) . " doses",
                ]);
            }

            // Log other significant changes
            $changes = [];
            if ($oldBatch != $validated['inv_batch_number']) {
                $changes[] = "Batch number changed from {$oldBatch} to {$validated['inv_batch_number']}";
            }
            if ($oldExpiry != $validated['inv_expiry_date']) {
                $changes[] = "Expiry date changed from {$oldExpiry} to {$validated['inv_expiry_date']}";
            }

            if (!empty($changes) && $oldQty == $newQty) {
                InventoryLog::create([
                    'log_inventory_id'     => $inventory->inv_id,
                    'log_change_type'      => 'info_update',
                    'log_quantity_changed' => 0,
                    'log_user_id'          => Auth::id(),
                    'log_remarks'          => implode('; ', $changes),
                ]);
            }

            DB::commit();

            return redirect()->route('inventory.index')
                ->with('success', 'Inventory updated successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update inventory: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update inventory: ' . $e->getMessage());
        }
    }

    /**
     * DESTROY: Delete inventory batch and its logs
     */
    public function destroy($id)
    {
        try {
            $inventory = VaccineInventory::findOrFail($id);

            DB::beginTransaction();

            // Create a final log before deletion
            InventoryLog::create([
                'log_inventory_id'     => $inventory->inv_id,
                'log_change_type'      => 'deletion',
                'log_quantity_changed' => $inventory->inv_quantity_available,
                'log_user_id'          => Auth::id(),
                'log_remarks'          => "Batch {$inventory->inv_batch_number} deleted by user",
            ]);
            
            $inventory->delete();

            DB::commit();

            return redirect()->route('inventory.index')
                ->with('success', 'Inventory batch deleted successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete inventory: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to delete inventory: ' . $e->getMessage());
        }
    }

    /**
     * ADJUST: Quick quantity adjustment (for usage/wastage)
     */
    public function adjust(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'adjustment_type' => 'required|in:usage,wastage,return,found',
                'quantity'        => 'required|integer|min:1',
                'remarks'         => 'nullable|string|max:255',
            ]);

            $inventory = VaccineInventory::findOrFail($id);

            DB::beginTransaction();

            $quantity = $validated['quantity'];
            $adjustmentType = $validated['adjustment_type'];

            // Calculate new quantity
            if (in_array($adjustmentType, ['usage', 'wastage'])) {
                $newQty = $inventory->inv_quantity_available - $quantity;
                $changeType = $adjustmentType;
            } else {
                $newQty = $inventory->inv_quantity_available + $quantity;
                $changeType = $adjustmentType;
            }

            // Validate we don't go negative
            if ($newQty < 0) {
                throw new Exception('Cannot reduce quantity below zero. Current stock: ' . $inventory->inv_quantity_available);
            }

            // Update inventory
            $inventory->update(['inv_quantity_available' => $newQty]);

            // Log the adjustment
            InventoryLog::create([
                'log_inventory_id'     => $inventory->inv_id,
                'log_change_type'      => $changeType,
                'log_quantity_changed' => $quantity,
                'log_user_id'          => Auth::id(),
                'log_remarks'          => $validated['remarks'] ?? ucfirst($adjustmentType) . ' recorded',
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Stock adjusted successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to adjust inventory: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}