<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;  // ← add this

class Bill extends Model
{
    protected $table      = 'bills';
    protected $primaryKey = 'billing_id';
    public $timestamps    = false;
    protected $guarded    = [];
    protected $casts      = [
        'billing_date' => 'date'
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function admissionDetail(): BelongsTo
    {
        return $this->belongsTo(AdmissionDetail::class, 'admission_id', 'admission_id');
    }

    // Now correctly imported
    public function items(): HasMany
    {
        return $this->hasMany(BillItem::class, 'billing_id', 'billing_id');
    }
}
