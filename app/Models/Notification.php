<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    //manages the notifications table
    protected $table = 'notifications';
    protected $primaryKey = 'notif_id';
    protected $fillable = [
        'notif_child_id',
        'notif_inventory_id',
        'notif_notification_type',
        'notif_message',
        'notif_is_read'
    ];

    public function child()
    {
        return $this->belongsTo(Child::class, 'notif_child_id', 'chd_id');
    }

    public function inventory()
    {
        return $this->belongsTo(VaccineInventory::class, 'notif_inventory_id', 'inv_id');
    }
}
