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


Route::group(['middleware' => 'auth:api'], function() {
    /*Usuarios*/
    Route::get('user/permissions', 'App\Http\Controllers\Auth\UserController@permissions');
    Route::post('user/filter/email', 'App\Http\Controllers\Auth\UserController@validateEmail');
    Route::resource('user', 'App\Http\Controllers\Auth\UserController', ['except' => ['create', 'edit']]);

    /*Roles*/
    Route::get('roles/filter/name/{name}', 'App\Http\Controllers\Admin\RolController@validateName');
    Route::resource('roles', 'App\Http\Controllers\Admin\RolController', ['except' => ['create', 'edit']]);

    /*Profesionales*/
    Route::get('professional/filter/identification/{identification}', 'App\Http\Controllers\Admin\ProfessionalController@validateIdentification');
    Route::post('professional/filter/email', 'App\Http\Controllers\Admin\ProfessionalController@validateEmail');
    Route::resource('professional', 'App\Http\Controllers\Admin\ProfessionalController', ['except' => ['create', 'edit']]);

    /*Permisos*/
    Route::resource('permissions', 'App\Http\Controllers\Auth\PermissionController', ['except' => ['create', 'edit']]);

    /*Tipo de Documento*/
    Route::get('documenttype', 'App\Http\Controllers\Common\DocumentTypeController@index');

    /*Cargos*/
    Route::resource('position', 'App\Http\Controllers\Admin\PositionController', ['except' => ['create', 'edit']]);

    /*Jornadas*/
    Route::resource('workingday', 'App\Http\Controllers\Admin\WorkingDayController', ['except' => ['create', 'edit']]);

    /*Servicios*/
    Route::resource('service', 'App\Http\Controllers\Admin\ServiceController', ['except' => ['create', 'edit']]);

    /*Tipos de cliente*/
    Route::resource('customertype', 'App\Http\Controllers\Scheduling\CustomerTypeController', ['except' => ['create', 'edit']]);

    /*Clientes*/
    Route::get('customer/filter/identification/{identification}', 'App\Http\Controllers\Scheduling\CustomerController@validateIdentification');
    Route::resource('customer', 'App\Http\Controllers\Scheduling\CustomerController', ['except' => ['create', 'edit']]);
});
