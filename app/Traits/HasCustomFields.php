<?php

namespace App\Traits;

use App\Models\CustomField;
use App\Models\CustomFieldValue;

trait HasCustomFields
{
    /**
     * Get all custom field values for this model.
     */
    public function customFieldValues()
    {
        return $this->morphMany(CustomFieldValue::class, 'model');
    }

    /**
     * Get custom fields defined for this model type.
     */
    public function getCustomFieldsAttribute()
    {
        $modelType = $this->getCustomFieldModelType();
        
        return CustomField::forEvent($this->event_id)
            ->forModelType($modelType)
            ->ordered()
            ->get();
    }

    /**
     * Get a specific custom field value.
     */
    public function getCustomFieldValue($fieldId)
    {
        $value = $this->customFieldValues()
            ->where('custom_field_id', $fieldId)
            ->first();
            
        return $value ? $value->value : null;
    }

    /**
     * Set a custom field value.
     */
    public function setCustomFieldValue($fieldId, $value)
    {
        return $this->customFieldValues()->updateOrCreate(
            ['custom_field_id' => $fieldId],
            ['value' => $value]
        );
    }

    /**
     * Sync custom field values from array.
     */
    public function syncCustomFields(array $values)
    {
        foreach ($values as $fieldId => $value) {
            if ($value !== null && $value !== '') {
                $this->setCustomFieldValue($fieldId, $value);
            } else {
                // Remove empty values
                $this->customFieldValues()
                    ->where('custom_field_id', $fieldId)
                    ->delete();
            }
        }
    }

    /**
     * Get the model type for custom fields.
     * Override this method in models if needed.
     */
    protected function getCustomFieldModelType()
    {
        $className = class_basename($this);
        return strtolower($className);
    }
}
