<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicineStockMovement extends Model
{
    protected $table = 'medicine_stock_movements';

    protected $fillable = [
        'service_id',
        'delta',
        'type',
        'notes',
    ];

    public $timestamps = false; // since you only have created_at

    /**
     * The medicine this stock‐movement belongs to.
     */
    public function medicine()
    {
        return $this->belongsTo(HospitalService::class, 'service_id', 'service_id');
    }
}
