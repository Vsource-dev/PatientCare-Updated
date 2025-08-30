<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUsersController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Billing\DepositController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\ResourceController;
use App\Http\Controllers\Pharmacy\ChargeController as PharmacyChargeController;
use App\Http\Controllers\Billing\ChargeController   as BillingChargeController;
use App\Http\Controllers\OperatingRoomController;
use App\Http\Controllers\Billing\DischargeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PatientBillingController;
use App\Http\Controllers\PatientDisputeController;
use App\Http\Controllers\DisputeController;
use App\Http\Controllers\PatientNotificationController;
use App\Http\Controllers\BillingDashboardController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\HospitalServiceController;

// PHARMACY MODULE ---------------------------------------------------------------------------------------
Route::middleware(['auth'])
     ->prefix('pharmacy')->name('pharmacy.')
     ->group(function () {
         Route::get('dashboard', [PharmacyController::class, 'index'])
              ->name('dashboard');

         // ← dispense route must NOT repeat the 'pharmacy/' prefix
         Route::patch(
             'charges/{charge}/dispense',
             [App\Http\Controllers\PharmacyController::class,'dispense']
         )->name('charges.dispense');

         Route::resource('charges', PharmacyChargeController::class);
         Route::get('queue', [PharmacyController::class, 'queue'])->name('queue');
         Route::get('charges/{charge}/select', [PharmacyController::class, 'selectItems'])->name('charges.select');
         Route::post('charges/{charge}/dispense', [PharmacyController::class, 'partialDispense'])->name('charges.partial-dispense');
         Route::get('history', [PharmacyController::class, 'history'])->name('history');
     });

// OR MODULE ----------------------------------------------------------------------------------------------
Route::middleware(['auth','role:operating_room'])
     ->prefix('operating')
     ->name('operating.')
     ->group(function () {
         Route::get('dashboard',    [OperatingRoomController::class, 'dashboard'])->name('dashboard');
         Route::get('queue',        [OperatingRoomController::class, 'queue'])    ->name('queue');
         Route::get('create',       [OperatingRoomController::class, 'create'])   ->name('create');
         Route::post('store',       [OperatingRoomController::class, 'store'])    ->name('store');
         Route::get('{assignment}', [OperatingRoomController::class, 'show'])     ->name('details');

 });

Route::post('notifications/mark-all-read', [PatientNotificationController::class, 'markAllRead'])
     ->name('notifications.markAllRead')
     ->middleware('auth');

     Route::middleware(['auth','role:billing'])
     ->prefix('billing')
     ->name('billing.')
     ->group(function () {
    // 1) Dashboard ("Home" for billing users)
    Route::get('dashboard', [BillingDashboardController::class, 'index'])
         ->name('dashboard');
         Route::patch('patients/{patient}/toggle-lock', [BillingChargeController::class, 'toggleLock'])
         ->name('patients.toggleLock');
         Route::patch('dispute/{dispute}', [DisputeController::class, 'update'])->name('disputes.update');

    // 2) Patient Bills / Manual Charges list
    Route::get('main', [PatientBillingController::class, 'index'])
         ->name('main');
         Route::get(
          '/my-billing/charge/{billItem}/dispute',
          [PatientBillingController::class,'disputeRequest']
       )->name('patient.dispute.form');
       

    // 3) Notifications
    Route::get ('notifications',               [NotificationController::class, 'index'])
         ->name('notifications');
   Route::post('notifications/mark-all-read', [PatientNotificationController::class,'markAllRead'])
     ->name('notifications.markAllRead');


    // 4) Dispute queue & detail
    Route::get('dispute/queue',     [DisputeController::class, 'queue'])
         ->name('dispute.queue');
    Route::get('dispute/{dispute}', [DisputeController::class, 'show'])
         ->name('dispute.show');

    Route::get   ('charges',             [BillingChargeController::class,'index'])   ->name('charges.index');
    Route::get   ('charges/create',      [BillingChargeController::class,'create'])  ->name('charges.create');
    Route::post  ('charges',             [BillingChargeController::class,'store'])   ->name('charges.store');
    Route::get   ('charges/{item}',      [BillingChargeController::class,'show'])    ->name('charges.show');
    Route::get   ('charges/{item}/edit', [BillingChargeController::class,'edit'])    ->name('charges.edit');
    Route::put   ('charges/{item}',      [BillingChargeController::class,'update'])  ->name('charges.update');
    Route::delete('charges/{item}',      [BillingChargeController::class,'destroy']) ->name('charges.destroy');
    Route::get   ('charges/{item}/audit',[BillingChargeController::class,'audit'])   ->name('charges.audit');

    // 6) Deposits
    Route::get  ('deposits/create', [DepositController::class, 'create'])
         ->name('deposits.create');
    Route::post ('deposits',        [DepositController::class, 'store'])
         ->name('deposits.store');

    // 7) Print statement & lock bill
    Route::get  ('print/{patient}', [BillingDashboardController::class, 'print'])
         ->name('print');
    Route::post ('lock/{patient}',  [BillingDashboardController::class, 'lock'])
         ->name('lock');
         Route::get('patient/billing/charge-trace/{billItem}', 
    [PatientBillingController::class, 'chargeTrace'])
    ->name('patient.billing.chargeTrace');
   /* Patient discharge dashboard */
    Route::get ('/discharge',             [DischargeController::class,'index'])
         ->name('billing.discharge.index');
  Route::post('discharge/{patient}/settle',
                      [DischargeController::class,'settle'])
                ->name('discharge.settle');

        Route::get('discharge', [\App\Http\Controllers\Billing\DischargeController::class, 'index'])
         ->name('discharge.index');
     Route::post('discharge/{patient}/finish', [DischargeController::class, 'finish'])
              ->name('discharge.finish');
    // actually mark one patient finished
    Route::post('discharge/{patient}', [\App\Http\Controllers\Billing\DischargeController::class, 'store'])
         ->name('discharge.store');
         
});  

Route::prefix('patient')
     ->name('patient.')
   ->middleware(['auth', 'role:patient'])  
     ->group(function(){
         Route::get('dashboard', [PatientController::class, 'dashboard'])
              ->name('dashboard');
          Route::get ('account',           [ProfileController::class,'edit'])           ->name('account');
          Route::patch('account',          [ProfileController::class,'update'])         ->name('account.update');
          Route::patch('account/password', [ProfileController::class,'updatePassword']) ->name('account.password');
    Route::get ('billing',          [PatientBillingController::class,'index'])->name('billing');
Route::get ('billing/{bill}',   [PatientBillingController::class,'show'])->name('billing.show');   // "Details"
    Route::get('billing/statement/pdf', [PatientBillingController::class,'downloadStatement'])
         ->name('billing.statement');
Route::get(
    'billing/charge-history/{billItem}',
    [PatientBillingController::class, 'chargeTrace']
)->name('billing.chargeTrace');

   // POST /patient/disputes
   Route::post('disputes',
   [DisputeController::class,'store']
)->name('disputes.store');      // becomes patient.disputes.store

// GET  /patient/disputes
Route::get('disputes',
   [DisputeController::class,'myDisputes']
)->name('disputes.mine');       // becomes patient.disputes.mine

  Route::get('notifications', [PatientNotificationController::class, 'index'])->name('notification');  
  Route::patch('notifications/{notification}', [PatientNotificationController::class, 'update'])
              ->name('notifications.update');

   Route::prefix('items')->name('items.')->group(function () {
        Route::post('/',            [HospitalServiceController::class, 'store' ])->name('store');
        Route::put('{service}',     [HospitalServiceController::class, 'update'])->name('update');
        Route::delete('{service}',  [HospitalServiceController::class, 'destroy'])->name('destroy');
    });
     });


// LABORATORY MODULE -----------------------------------

Route::prefix('laboratory')->name('laboratory.')
     ->middleware('auth')
     ->group(function () {
         Route::get('dashboard',   [LabController::class, 'dashboard'])->name('dashboard');
         Route::get('queue',       [LabController::class, 'queue'])->name('queue');
         Route::get('history',     [LabController::class, 'history'])->name('history');

         // handle the form POST
         Route::post('store', [LabController::class, 'store'])
              ->name('store');
         // Viewing & completing existing *requests*
         Route::get('details/{assignment}',       [LabController::class, 'show'])
              ->name('details');
         Route::post('details/{assignment}/complete',
              [LabController::class, 'markCompleted'])
              ->name('details.complete');

      

     });

// DASHBOARD ROUTING ------------------------------------------------------------------------------
Route::middleware('auth')->get('/dashboard', function () {
    $role = Auth::user()->role;
    return match ($role) {
        'admin'     => redirect()->route('admin.dashboard'),
        'admission' => redirect()->route('admission.dashboard'),
        'pharmacy'  => redirect()->route('pharmacy.dashboard'),
        'laboratory' => redirect()->route('laboratory.dashboard'),
        default     => redirect()->route('home'),
    };
});

// HOME PAGE ---------------------------------------------------------------------------------------
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/login',   [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class,'login'])
     ->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('patient/login', [LoginController::class,'showPatientLoginForm'])
     ->name('patient.login');
// process patient login
Route::post('patient/login', [LoginController::class,'patientLogin'])
     ->name('patient.login.attempt');
// routes/web.php


// CHARGE TRACEBACK ----------------------------------------------------------------------------------
Route::middleware('auth')
     ->prefix('patient/billing')
     ->name('patient.billing.')
     ->group(function() {
         // … existing routes …
         Route::get('charge/{item}/trace', [PatientBillingController::class,'chargeTrace'])
              ->name('charge.trace');
     });


// ADMISSION MODULE ----------------------------------------------------------------------------------
Route::middleware(['auth'])
     ->prefix('admission')->name('admission.')
     ->group(function () {
         Route::get('departments/{department}/doctors',
                    [PatientController::class,'getDoctorsByDepartment'])
              ->name('departments.doctors');
         Route::get('departments/{department}/rooms',
                    [PatientController::class,'getRoomsByDepartment'])
              ->name('departments.rooms');
         Route::get('rooms/{room}/beds',
                    [PatientController::class,'getBedsByRoom'])
              ->name('rooms.beds');
         Route::get('dashboard',[AdmissionController::class,'dashboard'])->name('dashboard');
         Route::resource('patients', PatientController::class) // PatientController is used for CRUD of Admission
              ->only(['index','create','store','show']);
     });


// ADMIN MODULE -------------------------------------------------------------------------------------
Route::middleware(['auth'])
      ->prefix('admin')->name('admin.')
      ->group(function(){
          Route::get('dashboard',[AdminController::class,'dashboard'])->name('dashboard');
          Route::resource('users', AdminUsersController::class);
          Route::get('users/{user}/assign',
    [AdminUsersController::class,'showAssignment'])
    ->name('users.assign');

Route::post('users/{user}/assign',
    [AdminUsersController::class,'updateAssignment'])
    ->name('users.assign.update');
   Route::get  ('resources',              [ResourceController::class,'index'])->name('resources.index');
    Route::get  ('resources/create',       [ResourceController::class,'create'])->name('resources.create');
    Route::post ('resources',              [ResourceController::class,'store'])->name('resources.store');

    // edit/update/destroy for both rooms & beds:
    Route::get    ('resources/{type}/{id}/edit',   [ResourceController::class,'edit'])
           ->where('type','room|bed')->name('resources.edit');
    Route::put    ('resources/{type}/{id}',        [ResourceController::class,'update'])
           ->where('type','room|bed')->name('resources.update');
    Route::delete ('resources/{type}/{id}',        [ResourceController::class,'destroy'])
           ->where('type','room|bed')->name('resources.destroy');


      });
              

// DOCTOR MODULE -------------------------------------------------------------------------------------
Route::prefix('doctor')
    ->name('doctor.')
    ->middleware('auth')
    ->group(function(){
        Route::get('/dashboard', [DoctorController::class,'dashboard'])->name('dashboard');
        Route::get('/orders/{patient}', [DoctorController::class, 'patientOrders'])->name('orders.show');
        Route::get('/patients/{patient}', [DoctorController::class,'show'])->name('patient.show');
        Route::get('/order-entry/{patient}', [DoctorController::class,'orderEntry'])->name('order');
        Route::post('/orders/{patient}', [DoctorController::class,'storeOrder'])->name('orders.store');
        Route::get('/orders', [DoctorController::class,'ordersIndex'])->name('orders.index');
        // Removed duplicate: Route::get('/patient-orders/{patient}', ...)
    });

Route::get('/laboratory/history/{assignment}', [App\Http\Controllers\LabController::class, 'show'])->name('laboratory.history.show');
