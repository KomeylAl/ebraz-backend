<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkShopController;
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

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth.jwt')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user-info', [AuthController::class, 'getUserInfo']);
    Route::patch('/user/{id}/edit', [UserController::class, 'editUser']);

    Route::get('/appointments', [ReferralController::class, 'getAllReferrals']);
    Route::get('/appointments/{id}', [ReferralController::class, 'getReferral']);
    Route::get('appointments/date/{date}', [ReferralController::class, 'getReferralByDate']);
    Route::post('/appointments/add', [ReferralController::class, 'addReferral']);
    Route::patch('/appointments/{id}/edit', [ReferralController::class, 'editReferral']);
    Route::delete('/appointments/{id}', [ReferralController::class, 'deleteReferral']);

    Route::get('payments', [PaymentController::class, 'getAllPayments']);

    Route::get('clients', [ClientController::class, 'getAllClients']);
    Route::get('doctors', [DoctorController::class, 'getAllDoctors']);
    Route::get('admins', [AdminController::class, 'getAllAdmins']);

    Route::get('clients/{id}', [ClientController::class, 'getClient']);
    Route::get('doctors/{id}', [DoctorController::class, 'getDoctor']);
    Route::get('admins/{id}', [AdminController::class, 'getAdmin']);

    Route::post('clients/add', [ClientController::class, 'addClient']);
    Route::patch('clients/{id}/edit', [ClientController::class, 'editClient']);
    Route::delete('clients/{id}', [ClientController::class, 'deleteClient']);

    Route::post('admins/add', [AdminController::class, 'addAdmin']);
    Route::patch('admins/{id}/edit', [AdminController::class, 'editAdmin']);
    Route::delete('admins/{id}', [AdminController::class, 'deleteAdmin']);

    Route::post('doctors/add', [DoctorController::class, 'addDoctor']);
    Route::patch('doctors/{id}/edit', [DoctorController::class, 'editDoctor']);
    Route::delete('doctors/{id}', [DoctorController::class, 'deleteDoctor']);

    Route::get('classes' ,[ClassController::class, 'getAllClasses']);
    Route::post('classes/add', [ClassController::class, 'addClass']);
    Route::patch('classes/{id}', [ClassController::class, 'editClass']);
    Route::get('classes/{id}', [ClassController::class, 'getClass']);
    Route::delete('classes/{id}', [ClassController::class, 'deleteClass']);

    Route::get('work-shops' ,[WorkShopController::class, 'getAllWorkShops']);
    Route::post('work-shops/add', [WorkShopController::class, 'addWorkShop']);
    Route::patch('work-shops/{id}', [WorkShopController::class, 'editWorkShop']);
    Route::get('work-shops/{id}', [WorkShopController::class, 'getWorkShop']);
    Route::delete('work-shops/{id}', [WorkShopController::class, 'deleteWorkShop']);
});
