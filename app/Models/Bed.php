<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bed extends Model
{
    use HasFactory;

    protected $primaryKey = 'bed_id';
    protected $table = 'beds';
    public $timestamps = true;

    protected $fillable = [
        'room_id',
        'bed_number',
        'status',
        'rate',
        'patient_id',
    ];


     protected $casts = [
        'rate'     => 'decimal:2',
    ];

    /**
     * If this bed has no custom rate, fall back to its room’s rate.
     */
    public function getDailyRateAttribute(): float
    {
        return $this->rate > 0
            ? $this->rate
            : $this->room->rate;
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    /**
     * Is this bed currently occupied?
     */
    public function isOccupied(): bool
    {
        return $this->patient_id !== null;
    }
}
