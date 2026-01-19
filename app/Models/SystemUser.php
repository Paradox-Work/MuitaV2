<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SystemUser extends Model  
{
    protected $table = 'system_users'; 
    
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'username', 'full_name', 'role', 'active'
    ];
}
