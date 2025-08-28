<?php
// app/Models/PharmacyCharge.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PharmacyCharge extends Model
{
    protected $fillable = [
        'patient_id','prescribing_doctor','rx_number','notes',
        'total_amount','status','dispensed_at',
    ];

    protected $casts = ['dispensed_at' => 'datetime'];

    /* scopes */
    public function scopePending($q)   { return $q->where('status','pending'); }
    public function scopeCompleted($q) { return $q->where('status','completed'); }

    public function items()  { return $this->hasMany(PharmacyChargeItem::class,'charge_id'); }
    public function patient(){ return $this->belongsTo(Patient::class,'patient_id'); }
}