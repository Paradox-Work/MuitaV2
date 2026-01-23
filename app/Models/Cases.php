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
    
    // Add these accessors (computed properties)
    protected $appends = ['risk_level', 'risk_score', 'is_high_risk'];
    
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
    
    /**
     * Calculate risk level based on flags
     */
    public function getRiskLevelAttribute()
    {
        if (!$this->risk_flags || !is_array($this->risk_flags)) {
            return 'none';
        }
        
        $count = count($this->risk_flags);
        
        if ($count >= 3) {
            return 'high';
        } elseif ($count == 2) {
            return 'medium';
        } elseif ($count == 1) {
            return 'low';
        }
        
        return 'none';
    }
    
    /**
     * Get risk score (1-10)
     */
    public function getRiskScoreAttribute()
    {
        if (!$this->risk_flags) {
            return 1;
        }
        
        $baseScore = count($this->risk_flags) * 2;
        $score = min($baseScore, 10); // Max score 10
        
        // Add bonus for specific high-risk flags
        $highRiskFlags = ['suspicious_history', 'drug_related', 'weapons', 'sanctions'];
        foreach ($this->risk_flags as $flag) {
            if (in_array($flag, $highRiskFlags)) {
                $score += 2;
            }
        }
        
        return min($score, 10);
    }
    
    /**
     * Check if high risk
     */
    public function getIsHighRiskAttribute()
    {
        return $this->risk_level === 'high' || $this->risk_score >= 7;
    }
}