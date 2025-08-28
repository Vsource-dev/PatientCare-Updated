<?php
// app/Models/Patient.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use App\Models\MedicalDetail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;  
use App\Models\Prescription;
use App\Models\BillingInformation; 


class Patient extends Authenticatable
{
    use HasFactory;

    protected $table = 'patients';
    protected $guarded    = [];    

    use Notifiable;

    protected $primaryKey = 'patient_id';
    public $timestamps = true; 
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'patient_first_name',
        'patient_last_name',
        'patient_birthday',
              'middle_initial',
        'civil_status',
        'email',
        'sex',     
        'phone_number',
        'address',
        'password',
        'profile_photo', 
       
    ];

 public function user(): HasOne   // <-- add
{
    return $this->hasOne(User::class, 'patient_id');
}
protected $casts = [
    'patient_birthday'  => 'date',
    'birthday'          => 'date',
    'billing_closed_at' => 'datetime', // This is the important line

];

  public function medicalDetail(): HasOne
    {
        return $this->hasOne(MedicalDetail::class, 'patient_id');
    }


    public function admissionDetail(): HasOne
    {
        return $this->hasOne(AdmissionDetail::class, 'patient_id');
    }
public function setPasswordAttribute($plain)
{
    $this->attributes['password'] = Hash::make($plain);
}
    /**
     * One-to-one: a patient has one billing information record.
     */
    public function billingInformation(): HasOne
    {
        return $this->hasOne(BillingInformation::class, 'patient_id');
    }
 public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class, 'patient_id', 'patient_id');
    }
 public function dashboard()
{
    // 1) Get the authenticated user’s Patient record
    $patient = Auth::user()->patient;

    // 2) Fetch prescriptions, schedules, assigned doctors (your existing code)…
    $prescriptions   = Prescription::where('patient_id', $patient->patient_id)->get();
    $schedules       = Schedule::where('patient_id', $patient->patient_id)->get();
    $assignedDoctors = DoctorAssignment::where('patient_id', $patient->patient_id)->get();

    // 3) Load the one-to-one billing information
    //    (you already have this relation defined on Patient) :contentReference[oaicite:0]{index=0}
    $billingInfo = $patient->billingInformation;

    // 4) Compute the real amount due
    $amountDue = 
        ($billingInfo->total_charges   ?? 0)
      - ($billingInfo->payments_made   ?? 0)
      - ($billingInfo->discount_amount ?? 0);

    // 5) Pass it all to the view
    return view('patient.dashboard', compact(
        'patient',
        'prescriptions',
        'schedules',
        'assignedDoctors',
        'amountDue'
    ));
}
    public function bills()
    {
        return $this->hasMany(Bill::class,'patient_id');
    }

  public function serviceAssignments()
    {
        return $this->hasMany(ServiceAssignment::class, 'patient_id', 'patient_id');
    }

   public function prescriptions(): HasMany
{
    return $this->hasMany(Prescription::class, 'patient_id', 'patient_id');
}

}
