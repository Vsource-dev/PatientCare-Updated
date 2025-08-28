<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;  // ← add this

class Dispute extends Model
{
    protected $table      = 'disputes';
    protected $primaryKey = 'dispute_id';
    public    $timestamps = false;

    protected $fillable = [
        'disputable_id',
        'disputable_type',
        'patient_id',
        'datetime',
        'reason',
        'status',
        'approved_by',
    ];

    protected $casts = [
        'datetime' => 'datetime',
    ];

    public function billItem(): BelongsTo
    {
        return $this->belongsTo(
            BillItem::class,
            'bill_item_id',       // FK on disputes table
            'billing_item_id'     // PK on bill_items table
        );
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(
            Patient::class,
            'patient_id',
            'patient_id'
        );
    }

    public function disputable(): MorphTo
    {
        // You can optionally be explicit about the column names:
        // return $this->morphTo(__FUNCTION__, 'disputable_type', 'disputable_id');
        return $this->morphTo();
    }
}
