<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model
{
    // Primary key of the Room table is "room_id" instead of default "id"
    protected $primaryKey = 'room_id';

    // Enable automatic created_at / updated_at timestamps
    public $timestamps = true;

    // Mass-assignable fields for Room
    protected $fillable = [
        'department_id',  // foreign key to Department
        'room_number',    // unique room number (e.g., 101, 202, etc.)
        'status',         // availability status (e.g., available, unavailable)
        'capacity',       // how many beds this room can hold
        'rate',           // base rate for the room (can also be inherited by beds)
    ];

    // Cast 'rate' to decimal with 2 decimal places when retrieved
    protected $casts = [
        'rate' => 'decimal:2',
    ];

    /**
     * Accessor for formatted rate (ex: 1500 -> "1,500.00")
     */
    public function getRateFormattedAttribute(): string
    {
        return number_format($this->rate, 2);
    }

    /**
     * Relationship: A Room has many Beds
     */
    public function beds()
    {
        return $this->hasMany(Bed::class, 'room_id', 'room_id');
    }

    /**
     * Relationship: A Room belongs to one Department
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    /**
     * Custom method: Count how many beds in this room are currently occupied
     */
    public function occupiedCount()
    {
        return $this->beds()->whereNotNull('patient_id')->count();
    }

    /**
     * Custom method: Check if the room is full
     * (true if occupied beds >= room capacity)
     */
    public function isFull(): bool
    {
        return $this->occupiedCount() >= $this->capacity;
    }

    /**
     * Model event hooks (booted lifecycle methods)
     */
    protected static function booted()
    {
        // When a Room is created, auto-generate its beds based on capacity
        static::created(function ($room) {
            for ($i = 1; $i <= $room->capacity; $i++) {
                $room->beds()->create([
                    'bed_number' => $room->room_number . '-B' . $i, // Ex: Room101-B1
                    'status'     => 'available',                   // default new bed status
                    'rate'       => $room->rate,                   // inherit rate from room
                ]);
            }
        });

        // When a Room is deleted, cascade delete its Beds
        static::deleting(function ($room) {
            $room->beds()->delete();
        });
    }
}
