<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    use HasFactory;
    //manages the inventory_logs table
    protected $table = 'inventory_logs';
    protected $primaryKey = 'log_id';
    protected $fillable = [
        'log_id',
        'log_user_id',
        'log_inventory_id',
        'log_change_type',
        'log_quantity_changed',
        'logable_type',
        'logable_id',
    ];

    public function inventory()
    {
        return $this->belongsTo(VaccineInventory::class, 'log_inventory_id', 'inv_id');
    }

    public function logable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'log_user_id');
    }
}
