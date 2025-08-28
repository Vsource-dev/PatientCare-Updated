<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Prescription;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrescriptionItem extends Model
{
 protected $table      = 'prescription_items';
    protected $primaryKey = 'prescription_item_id';
    public $timestamps = false;
    protected $guarded = [];
  protected $fillable = [
    'prescription_id','service_id','name','datetime',
    'quantity_given','quantity_asked','status',
    'dosage','frequency','route','duration','duration_unit',
    'instructions','refills','routing','priority','daw',
];
protected $casts = [
    'datetime' => 'datetime',
];
  public function prescription(): BelongsTo
{
    return $this->belongsTo(Prescription::class,
                            'prescription_id',   // FK on this table
                            'prescription_id');  // PK on prescriptions table
}

    public function service(): BelongsTo
    {
        return $this->belongsTo(HospitalService::class, 'service_id', 'service_id');
    }
}
