<?php

// app/Models/AuditLog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table   = 'audit_log';
    protected $guarded = [];
    protected $casts   = ['created_at'=>'datetime','updated_at'=>'datetime'];

    public function billItem()
    {
        return $this->belongsTo(BillItem::class,'bill_item_id','billing_item_id');
    }
}
