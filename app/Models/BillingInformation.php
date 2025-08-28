<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingInformation extends Model
{
    protected $table      = 'billing_information';
    protected $primaryKey = 'billing_info_id';
    public $timestamps    = true;
    protected $guarded    = [];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

}