<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionCustomFieldValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'custom_field_id',
        'value',
    ];

    // Relationships
    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function customField()
    {
        return $this->belongsTo(CustomField::class);
    }
}
