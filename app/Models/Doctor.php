<?php
//app/Models/Doctor.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $table      = 'doctors';
    protected $primaryKey = 'doctor_id';
    public $timestamps    = true;
    protected $fillable   = ['doctor_name', 'department_id', 'rate'];
     public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }
    public function admissions()
    {
        return $this->hasMany(AdmissionDetail::class,'doctor_id','doctor_id');
    }

    /** How many patients is this doctor handling today? */
    public function todaysLoad(): int
    {
        return $this->admissions()
            ->whereDate('admission_date', now()->toDateString())
            ->count();
    }
}