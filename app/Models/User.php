<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use SoftDeletes;

    /**
     * Use your actual table name
     */
    protected $table = 'users';
    protected $primaryKey = 'id';

    /**
     * Updated fillable for your NIP project
     */
    protected $fillable = [
        'worker_id',
        'username', // Changed from email
        'password',
        'last_login',
        'user_status'
    ];

    /**
     * Hidden attributes (standard for security)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts for data integrity
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed', // Laravel will automatically hash the password when saved
        ];
    }

    /**
     * Relationship: Link to the Health Worker profile
     */
    public function worker()
    {
        return $this->belongsTo(HealthWorker::class, 'worker_id', 'wrk_id');
    }

    public function hasRole($role)
    {
        return optional($this->worker)->wrk_role === $role;
    }

    public function canDelete()
    {
        return in_array(optional($this->worker)->wrk_role, ['admin', 'nurse', 'midwife']);
    }

}