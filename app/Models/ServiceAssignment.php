<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ServiceAssignment extends Model
{
    protected $table        = 'service_assignments';
    protected $primaryKey   = 'assignment_id';
    public    $timestamps   = false;            // you’re using your own datetime
    protected $guarded      = [];               // or switch to $fillable if you prefer

    // Tell Eloquent to cast your `datetime` column to a Carbon instance
    protected $casts = [
        'datetime' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────
    public function disputes(): MorphMany
    {
        return $this->morphMany(Dispute::class, 'disputable');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(HospitalService::class, 'service_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }
}
