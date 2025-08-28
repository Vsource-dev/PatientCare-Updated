<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MiscellaneousCharge extends Model
{
    protected $table = 'miscellaneous_charges';
    protected $guarded = [];    // all columns mass‐assignable
    public $timestamps = true;  // uses created_at/updated_at

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(HospitalService::class, 'service_id', 'service_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by', 'user_id');
    }
}
