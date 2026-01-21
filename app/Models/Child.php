<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Child extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'children';
    protected $primaryKey = 'chd_id';

    protected $fillable = [
        'chd_first_name',
        'chd_middle_name',
        'chd_last_name',
        'chd_date_of_birth',
        'chd_sex',
        'chd_residency_status',
        'chd_current_addr_id',
        'chd_guardian_id',
        'chd_status'
    ];

    public function guardian()
    {
        return $this->belongsTo(Guardian::class, 'chd_guardian_id', 'grd_id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'chd_current_addr_id', 'addr_id');
    }

    public function vaccinationRecords()
    {
        return $this->hasMany(VaccinationRecord::class, 'rec_child_id', 'chd_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'notif_child_id', 'chd_id');
    }

    public function getFullNameAttribute()
    {
        $middle = $this->chd_middle_name ? " {$this->chd_middle_name} " : " ";
        
        return "{$this->chd_first_name}{$middle}{$this->chd_last_name}";
    }

    public function getStatusDetailsAttribute()
    {
        return match($this->chd_status) {
            'active'      => ['color' => 'success', 'icon' => 'fa-play-circle'],
            'complete'   => ['color' => 'info',    'icon' => 'fa-check-circle'],
            'transferred' => ['color' => 'warning', 'icon' => 'fa-exchange-alt'],
            'inactive'    => ['color' => 'secondary', 'icon' => 'fa-archive'],
            default       => ['color' => 'light',   'icon' => 'fa-question'],
        };
    }
}
