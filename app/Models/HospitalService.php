<?php
// app/Models/HospitalService.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalService extends Model
{
    protected $table = 'hospital_services';
    protected $primaryKey = 'service_id';
    public $timestamps = false; // adjust if you have created_at/updated_at

    protected $fillable = [
        'service_name',
        'price',
        'quantity',
        'description',
        'service_type',
    ];

    public function department()
{
    return $this->belongsTo(\App\Models\Department::class, 'department_id', 'department_id');
}

}
