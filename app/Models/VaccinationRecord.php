<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaccinationRecord extends Model
{
    use HasFactory;
    //manages the vaccination_records table
    protected $table = 'vaccination_records';
    protected $primaryKey = 'rec_id';

    protected $fillable = [
        'rec_child_id',
        'rec_vaccine_id',
        'rec_dose_number',
        'rec_date_administered',
        'rec_administered_by',
        'rec_remarks',
        'rec_status',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class, 'rec_child_id', 'chd_id');
    }

    public function vaccine()
    {
        return $this->belongsTo(Vaccine::class, 'rec_vaccine_id', 'vacc_id');
    }

    public function administrator()
    {
        return $this->belongsTo(HealthWorker::class, 'rec_administered_by', 'wrk_id');
    }
}
