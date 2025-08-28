<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsuranceProvider extends Model
{
    protected $table      = 'insurance_providers';
    protected $primaryKey = 'insurance_provider_id';
    public $timestamps    = true;
    protected $fillable   = ['name'];
}