<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cases;

class Document extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'id', 'case_id', 'filename', 'mime_type', 'category', 'pages', 'uploaded_by'
    ];
    public function case()
{
    return $this->belongsTo(Cases::class, 'case_id');
}
}