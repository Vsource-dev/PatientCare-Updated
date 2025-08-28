<?php
// app/Models/User.php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'user_id';
    public $timestamps   = false;
    protected $keyType   = 'int';
    public $incrementing = true;

    protected $fillable = [
        'patient_id',
        'username',
        'email',
        'password',
        'role',
        'department_id',
        'room_id',
        'doctor_id',
        'bed_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function setPasswordAttribute($value)
    {
        if (!\Illuminate\Support\Str::startsWith($value, '$2y$')) {
            $value = Hash::make($value);
        }
        $this->attributes['password'] = $value;
    }

    public function patient()
    {
        return $this->belongsTo(\App\Models\Patient::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(\App\Models\Doctor::class, 'doctor_id', 'doctor_id');
    }

    public function admissionDetail()
    {
        return $this->hasOne(\App\Models\AdmissionDetail::class, 'patient_id', 'patient_id');
    }
}
