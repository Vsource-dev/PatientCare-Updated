<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    protected $table = 'medicines';       // or whatever your table is
    protected $primaryKey = 'medicine_id';
    public $timestamps = false;           // adjust if you have created_at/updated_at

    protected $fillable = ['generic_name','brand_name','price','description'];
}
