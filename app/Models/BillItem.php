<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BillItem extends Model
{
    protected $table      = 'bill_items';
    protected $primaryKey = 'billing_item_id';
    public    $timestamps = false;
    protected $guarded    = [];

    protected $casts = [
        'billing_date' => 'date',
    ];
    public function disputes(): MorphMany
    {
        return $this->morphMany(Dispute::class, 'disputable');
    }
    /** The parent Bill */
    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class, 'billing_id');
    }

    public function service()
    {
        return $this->belongsTo(HospitalService::class, 'service_id', 'service_id');
    }
    /** Optional link back to a pharmacy or lab prescription item */
    public function prescriptionItem(): BelongsTo
    {
        return $this->belongsTo(PrescriptionItem::class, 'prescription_item_id');
    }

    /** Optional link back to a service assignment */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(ServiceAssignment::class, 'assignment_id');
    }

    public function logs()
    {
        return $this->hasMany(AuditLog::class, 'bill_item_id', 'billing_item_id');
    }
    public function dispute(): MorphOne
    {
        return $this->morphOne(Dispute::class, 'disputable');
    }

    

  public function getRouteKeyName()
    {
        return 'billing_item_id';
    }

    

    public function getProviderLabelAttribute(): string
{
    return match ($this->service?->service_type) {
        'medication'         => 'Pharmacy',
        'lab', 'imaging'     => 'Lab',
        'surgery'            => 'Surgery',
        default              => 'Misc',
    };
}

}
