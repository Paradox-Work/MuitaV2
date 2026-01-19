<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cases;

class Inspection extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
   'id', 'case_id', 'type', 'result', 'checks', 
    'started_at', 'assigned_to', 'location', 'completed_at'
];
    
    protected $casts = [
        'checks' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];
    public function case()
{
    return $this->belongsTo(Cases::class, 'case_id');
}
}