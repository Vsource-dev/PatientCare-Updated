<?php
// app/Models/Prescription.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $table      = 'prescriptions';
    protected $primaryKey = 'prescription_id';
    public $timestamps    = false;

    protected $fillable = ['patient_id','doctor_id'];

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class, 'prescription_id', 'prescription_id');
    }
}
