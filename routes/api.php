<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'App\Http\Controllers\Auth\AuthController@login');
    Route::group(['middleware' => ['auth:api']], function() {
        Route::get('logout', 'App\Http\Controllers\Auth\AuthController@logout');
    });
});

Route::group(['prefix' => 'singup'], function () {

    /*Tipo de Documento*/
    Route::get('documenttype', 'App\Http\Controllers\Common\DocumentTypeController@index');

    /*Tipos de cliente*/
    Route::get('customertype', 'App\Http\Controllers\Scheduling\SignUpController@customerType');

    /*Clientes*/
    Route::get('customer/filter/identification/{identification}', 'App\Http\Controllers\Scheduling\SignUpController@validateIdentification');

    /*User*/
    Route::post('user/filter/email', 'App\Http\Controllers\Scheduling\SignUpController@validateEmail');

    /*Singup*/
    Route::post('/', 'App\Http\Controllers\Scheduling\SignUpController@store');

    /*Restablecer contraseña*/
    Route::patch('/resetpassword', 'App\Http\Controllers\Scheduling\SignUpController@resetPassword');
});

Route::group(['prefix' => 'payment'], function () {

    /*Confirmación*/
    Route::post('confirmation', 'App\Http\Controllers\Finance\PaymentController@confirmation');
});


Route::group(['middleware' => 'auth:api'], function() {
    /*Usuarios*/
    Route::get('user/permissions', 'App\Http\Controllers\Auth\UserController@permissions');
    Route::post('user/filter/email', 'App\Http\Controllers\Auth\UserController@validateEmail');
    Route::patch('user/changepassword', 'App\Http\Controllers\Auth\UserController@changePassword');
    Route::resource('user', 'App\Http\Controllers\Auth\UserController', ['except' => ['create', 'edit']]);

    /*Roles*/
    Route::get('roles/filter/name/{name}', 'App\Http\Controllers\Admin\RolController@validateName');
    Route::resource('roles', 'App\Http\Controllers\Admin\RolController', ['except' => ['create', 'edit']]);

    /*Profesionales*/
    Route::post('professional/filter/availability', 'App\Http\Controllers\Admin\ProfessionalController@checkAvailability');
    Route::get('professional/filter/identification/{identification}', 'App\Http\Controllers\Admin\ProfessionalController@validateIdentification');
    Route::post('professional/filter/email', 'App\Http\Controllers\Admin\ProfessionalController@validateEmail');
    Route::resource('professional', 'App\Http\Controllers\Admin\ProfessionalController', ['except' => ['create', 'edit']]);

    /*Permisos*/
    Route::resource('permissions', 'App\Http\Controllers\Auth\PermissionController', ['except' => ['create', 'edit']]);

    /*Tipo de Documento*/
    Route::get('documenttype', 'App\Http\Controllers\Common\DocumentTypeController@index');

    /*Cargos*/
    Route::resource('position', 'App\Http\Controllers\Admin\PositionController', ['except' => ['create', 'edit']]);

    /*Tipo de servicio*/
    Route::resource('servicetype', 'App\Http\Controllers\Admin\ServiceTypeController', ['except' => ['create', 'edit']]);

    /*Jornadas*/
    Route::get('workingday/filter/servicetype/{id}', 'App\Http\Controllers\Admin\WorkingDayController@findByServiceType');
    Route::resource('workingday', 'App\Http\Controllers\Admin\WorkingDayController', ['except' => ['create', 'edit']]);

    /*Servicios*/
    Route::get('service/filter/type/{type}/workingday/{working}', 'App\Http\Controllers\Admin\ServiceController@findByTypeAndWorking');
    Route::resource('service', 'App\Http\Controllers\Admin\ServiceController', ['except' => ['create', 'edit']]);

    /*Tipos de cliente*/
    Route::resource('customertype', 'App\Http\Controllers\Scheduling\CustomerTypeController', ['except' => ['create', 'edit']]);

    /*Clientes*/
    Route::get('customer/filter/identification/{identification}', 'App\Http\Controllers\Scheduling\CustomerController@validateIdentification');
    Route::post('customer/find', 'App\Http\Controllers\Scheduling\CustomerController@findCustomer');
    Route::resource('customer', 'App\Http\Controllers\Scheduling\CustomerController', ['except' => ['create', 'edit']]);

    /*Festivos*/
    Route::resource('holiday', 'App\Http\Controllers\Admin\HolidayController', ['except' => ['create', 'edit']]);

    /*Reservas*/
    Route::get('reserve/filter/customer/{id}', 'App\Http\Controllers\Scheduling\ReserveController@findByCustomer');
    Route::get('reserve/filter/reference/{reference}', 'App\Http\Controllers\Scheduling\ReserveController@findByReference');
    Route::get('reserve/filter/schedule/customer/{id}', 'App\Http\Controllers\Scheduling\ReserveController@findScheduleByCustomer');
    Route::get('reserve/filter/status/{id}', 'App\Http\Controllers\Scheduling\ReserveController@filterByStatus');
    Route::resource('reserve', 'App\Http\Controllers\Scheduling\ReserveController', ['except' => ['create', 'edit']]);

    /*Agendamientos*/
    Route::resource('schedule', 'App\Http\Controllers\Scheduling\ScheduleController', ['except' => ['create', 'edit']]);

    /*Estados*/
    Route::resource('status', 'App\Http\Controllers\Admin\StatusController', ['except' => ['create', 'edit']]);

    /*Codigos promocionales*/
    Route::patch('promocodes/disable', 'App\Http\Controllers\Finance\PromocodesController@disable');
    Route::patch('promocodes/check', 'App\Http\Controllers\Finance\PromocodesController@check');
    Route::resource('promocodes', 'App\Http\Controllers\Finance\PromocodesController', ['except' => ['create', 'edit']]);

    /*Reprogramaciones*/
    Route::resource('reschedule', 'App\Http\Controllers\Scheduling\RescheduleController', ['except' => ['create', 'edit']]);

    /*Historial de pagos*/
    Route::post('payment/filter/user', 'App\Http\Controllers\Finance\PaymentController@byCustomer');

    /*Reportes*/
    Route::post('report/schedule', 'App\Http\Controllers\Scheduling\ReserveController@reportSchedule');
    Route::get('report/expiration', 'App\Http\Controllers\Scheduling\ReserveController@reportExpiration');
    Route::post('report/history', 'App\Http\Controllers\Scheduling\ReserveController@reportHistory');
    Route::get('report/pending', 'App\Http\Controllers\Scheduling\ReserveController@reportPendingPayments');
    Route::post('report/professional', 'App\Http\Controllers\Scheduling\ReserveController@reportProfessional');

    /*Registro de Actividades*/
    Route::post('activitylog', 'App\Http\Controllers\Admin\ActivityLogController@index');

    /*Dashboard*/
    Route::get('dashboard/admin', 'App\Http\Controllers\Dashboard\DashboardAdminController@index');
    Route::get('dashboard/customer/{id}', 'App\Http\Controllers\Dashboard\DashboardCustomerController@index');

    /*Novedades de servicio*/
    Route::post('novelty/schedule', 'App\Http\Controllers\Admin\NoveltiesController@scheduleAffected');
    Route::resource('novelty', 'App\Http\Controllers\Admin\NoveltiesController', ['except' => ['create', 'edit']]);
});
