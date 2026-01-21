<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    // change of method looks
    public function isAdmin()
    {
        return $this->hasRole('admin') || $this->role === 'admin';
    }
    
    public function isInspector()
    {
        return $this->hasRole('inspector') || $this->role === 'inspector';
    }
    
    public function isBroker()
    {
        return $this->hasRole('broker') || $this->role === 'broker';
    }
    
    public function isAnalyst()
    {
        return $this->hasRole('analyst') || $this->role === 'analyst';
    }
}