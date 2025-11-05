<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomFieldValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'custom_field_id',
        'model_id',
        'model_type',
        'value',
    ];

    /**
     * Get the custom field definition.
     */
    public function customField()
    {
        return $this->belongsTo(CustomField::class);
    }

    /**
     * Get the owning model.
     */
    public function model()
    {
        return $this->morphTo();
    }
}
