<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class HealthWorker extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'health_workers';
    protected $primaryKey = 'wrk_id';

    protected $fillable = [
        'wrk_first_name',
        'wrk_middle_name',
        'wrk_last_name',
        'wrk_contact_number',
        'wrk_addr_id',
        'wrk_role'
    ];

    public function user() {
        return $this->hasOne(User::class, 'worker_id', 'wrk_id');
    }

    public function address() {
        return $this->belongsTo(Address::class, 'wrk_addr_id', 'addr_id');
    }

    public function adminsteredVaccinations() {
        return $this->hasMany(VaccinationRecord::class, 'rec_administered_by', 'wrk_id');
    }

    public function getFullNameAttribute()
    {
        $middle = $this->wrk_middle_name ? " {$this->wrk_middle_name} " : " ";
        
        return "{$this->wrk_first_name}{$middle}{$this->wrk_last_name}";
    }

}
