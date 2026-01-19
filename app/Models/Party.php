<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
    'id', 'name', 'type', 'country', 'reg_code', 'vat', 'email', 'phone' 
    ];
}
