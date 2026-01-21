<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    use HasFactory;
    
    //manages the guardians table
    protected $table = 'guardians';
    protected $primaryKey = 'grd_id';

    protected $fillable = [
        'grd_first_name',
        'grd_middle_name',
        'grd_last_name',
        'grd_contact_number',
        'grd_current_addr_id',
        'grd_relationship'
    ];

    public function currentAddress() {
        return $this->belongsTo(Address::class, 'grd_current_addr_id', 'addr_id');
    }

    public function children() {
        return $this->hasMany(Child::class, 'chd_guardian_id', 'grd_id');
    }

    public function getFullNameAttribute()
    {
        $middle = $this->grd_middle_name ? " {$this->grd_middle_name} " : " ";
        
        return "{$this->grd_first_name}{$middle}{$this->grd_last_name}";
    }
}