<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NipSchedule extends Model
{
    use HasFactory;
    //manages nip_schedule table
    protected $table = 'nip_schedule';
    protected $primaryKey = 'nip_schedule_id';

    protected $fillable = [
        'nip_vaccine_id',
        'nip_dose_number',
        'nip_recommended_age_days',
        'nip_minimum_age_days',
        'nip_maximum_age_days',
    ];

    public function vaccine()
    {
        return $this->belongsTo(Vaccine::class, 'nip_vaccine_id', 'vacc_id');
    }

}
