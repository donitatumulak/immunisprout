<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    //manages the addresses table
    protected $table = 'addresses';
    protected $primaryKey = 'addr_id';

    protected $fillable = [
        'addr_line_1',
        'addr_line_2',
        'addr_barangay',
        'addr_city_municipality',
        'addr_province',
        'addr_zip_code'
    ];

    public function workers()
    {
        return $this->hasMany(HealthWorker::class, 'wrk_addr_id', 'addr_id');
    }

    public function children()
    {
        return $this->hasMany(Child::class, 'chd_current_addr_id', 'addr_id');
    }

    public function currentGuardian()
    {
        return $this->hasMany(Guardian::class, 'grd_current_addr_id', 'addr_id');
    }

    public function permanentGuardian()
    {
        return $this->hasMany(Guardian::class, 'grd_permanent_addr_id', 'addr_id');
    }

    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->addr_line_1,
            $this->addr_line_2,
            $this->addr_barangay,
            $this->addr_city_municipality,
            $this->addr_province,
            $this->addr_zip_code
        ]);

        return implode(', ', $parts);
    }


    public function getFormattedLinesAttribute()
    {
        return [
            'line1' => trim("{$this->addr_line_1} {$this->addr_line_2}"),
            'line2' => "{$this->addr_barangay}, {$this->addr_city_municipality}",
            'line3' => "{$this->addr_province} {$this->addr_zip_code}",
        ];
    }
}
