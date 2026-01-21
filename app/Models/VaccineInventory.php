<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaccineInventory extends Model
{
    use HasFactory;
    //manages vaccine_inventory table
    protected $table = 'vaccine_inventory';
    protected $primaryKey = 'inv_id';

    protected $fillable = [
        'inv_vaccine_id',
        'inv_batch_number',
        'inv_quantity_available',
        'inv_expiry_date',
        'inv_received_date',
        'inv_source'
    ];

    public function vaccine()
    {
        return $this->belongsTo(Vaccine::class, 'inv_vaccine_id', 'vacc_id');
    }

    public function logs()
    {
        return $this->hasMany(InventoryLog::class, 'log_inventory_id', 'inv_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'notif_inventory_id', 'inv_id');
    }
}
