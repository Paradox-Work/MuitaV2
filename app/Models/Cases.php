<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cases extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'id', 'external_ref', 'status', 'priority', 'arrival_ts',
        'checkpoint_id', 'origin_country', 'destination_country',
        'risk_flags', 'vehicle_id', 'declarant_id', 'consignee_id'
    ];
    
    protected $casts = [
        'risk_flags' => 'array',
        'arrival_ts' => 'datetime'
    ];
    
    // FIXED: Remove the stray # and add opening brace
    public function vehicle() {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function declarant() {
        return $this->belongsTo(Party::class, 'declarant_id');
    }

    public function consignee() {
        return $this->belongsTo(Party::class, 'consignee_id');
    }

    public function documents() {
        return $this->hasMany(Document::class, 'case_id');
    }

    public function inspections() {
        return $this->hasMany(Inspection::class, 'case_id');
    }
}