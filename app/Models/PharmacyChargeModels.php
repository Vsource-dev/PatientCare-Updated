<?php
/**
 * PharmacyCharge + PharmacyChargeItem
 * -----------------------------------
 * Both Eloquent models live in ONE file per your request.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/* =========================================================
 |  PharmacyCharge  (header / parent)
 * ========================================================= */
class PharmacyCharge extends Model
{
    /* ---------- table / PK ---------- */
    protected $table      = 'pharmacy_charges';   // create via migration
    protected $primaryKey = 'id';

    /* ---------- mass-assign ---------- */
    protected $guarded = [];                      // allow all columns

    /* ---------- casts ---------- */
    protected $casts  = [
        'date' => 'datetime',
    ];

    /* ---------- relations ---------- */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PharmacyChargeItem::class, 'charge_id');
    }
}

/* =========================================================
 |  PharmacyChargeItem (line items)
 * ========================================================= */
class PharmacyChargeItem extends Model
{
    protected $table      = 'pharmacy_charge_items';
    protected $primaryKey = 'id';
    public    $timestamps = false;                // only parent has timestamps
    protected $guarded    = [];

    public function charge(): BelongsTo
    {
        return $this->belongsTo(PharmacyCharge::class, 'charge_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(HospitalService::class, 'service_id');
    }

    /* Convenience accessor for medication name */
    public function getMedicationNameAttribute(): string
    {
        return $this->service?->department?->department_name
             ?? '—';
    }
}
