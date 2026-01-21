<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vaccine extends Model
{
    use HasFactory;
    //manages the vaccines table
    protected $table = 'vaccines';
    protected $primaryKey = 'vacc_id';

    protected $fillable = [
        'vacc_vaccine_name',
        'vacc_description',
        'vacc_doses_required',
    ];

    public function schedule() {
        return $this->hasMany(NipSchedule::class, 'nip_vaccine_id', 'vacc_id');
    }

    public function inventory() {
        return $this->hasMany(VaccineInventory::class, 'inv_vaccine_id', 'vacc_id');
    }

    public function records() {
        return $this->hasMany(VaccinationRecord::class, 'rec_vaccine_id', 'vacc_id');
    }
}
