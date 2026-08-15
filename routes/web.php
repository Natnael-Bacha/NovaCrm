<?php

use App\Http\Controllers\ActionController;
use App\Http\Controllers\ActionControllerGet;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\DealControllerGet;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadControllerGet;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectControllerGet;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UnitControllerGet;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserControllerGet;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLoginPage'])
    ->name('home');

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('adminDashboard');
});

// User Controllers
Route::middleware(['auth', 'role:admin'])->controller(UserController::class)->group(function () {

    Route::post('/createUser', 'createUser')->name('createUser');
    Route::put('/updateUser/{user}', 'updateUser')->name('updateUser');
    Route::put('/updateRole/{user}', 'updateRole')->name('updateRole');
    Route::put('/deleteUser/{user}', 'deleteUser')->name('deleteUser');
    Route::put('/changeSupervisors', 'changeSupervisors')->name('changeSupervisors');
});

Route::middleware(['auth', 'role:admin'])->controller(UserControllerGet::class)->group(function () {
    Route::get('/admin/team', 'index')->name('team.index');
});

// Auth Controllers
Route::middleware('guest')->controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginPage')->name('show.loginPage');
    Route::post('/login', 'handleLogin')->name('login');
    Route::get('/createUser', 'showCreatePage')->name('show.createPage');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Lead Controllers
Route::middleware(['auth', 'role:admin'])->controller(LeadControllerGet::class)->group(function () {
    Route::get('/getLeads', 'getLeads')->name('admin.pipeline');
    Route::get('/admin/leads', 'index')->name('leads');
});

Route::middleware(['auth', 'role:admin'])->controller(LeadController::class)->group(function () {
    Route::post('/createlead', 'createLead')->name('createLead');
    Route::put('/updateLead/{lead}', 'updateLead')->name('updateLead');
    Route::put('/updateLeadStatus/{lead}', 'updateLeadStatus')->name('updateLeadStatus');
    Route::delete('/deleteLead/{lead}', 'deleteLead')->name('deleteLead');
});

// Project Controllers
Route::middleware(['auth', 'role:admin'])->controller(ProjectControllerGet::class)->group(function () {
    Route::get('/getProjects', 'getProjects')->name('getProjects');
    Route::get('/projects/{project}/edit', 'edit')->name('projects.edit');
});

Route::middleware(['auth', 'role:admin'])->controller(ProjectController::class)->group(function () {
    Route::post('/createProject', 'createProject')->name('createProject');
    Route::put('/updateProject/{project}', 'updateProject')->name('updateProject');
    Route::delete('/deleteProject/{project}', 'deleteProject')->name('deleteProject');
});

// Unit Controllers
Route::middleware(['auth', 'role:admin'])->controller(UnitControllerGet::class)->group(function () {
    Route::get('/admin/unit', 'getUnits')->name('admin.units');
});

Route::middleware(['auth', 'role:admin'])->controller(UnitController::class)->group(function () {
    Route::post('/createUnit', 'createUnit')->name('createUnit');
    Route::put('/updateUnit/{unit}', 'updateUnit')->name('updateUnit');
    Route::delete('/deleteUnit/{unit}', 'deleteUnit')->name('deleteUnit');
});

// Deal Controllers
Route::middleware(['auth', 'role:admin'])->controller(DealController::class)->group(function () {
    Route::post('/createDeal/{lead}', 'createDeal')->name('createDeal');
    Route::put('/updateDeal/{deal}', 'updateDeal')->name('updateDeal');
    Route::delete('/deleteDeal/{deal}', 'deleteDeal')->name('deleteDeal');
    Route::put('/updateDealPaymentStatus/{deal}', 'updateDealPaymentStatus')->name('updateDealPaymentStatus');
});

Route::middleware(['auth', 'role:admin'])->controller(DealControllerGet::class)->group(function () {
    Route::get('/admin/deal', 'getDeals')->name('admin.deals');
});

// Action Controllers
Route::middleware(['auth', 'role:admin'])->controller(ActionController::class)->group(function () {
    Route::post('/createAction/{lead}', 'createAction')->name('createAction');
    Route::put('/updateAction/{action}', 'updateAction')->name('updateAction');
    Route::delete('/deleteAction/{action}', 'deleteAction')->name('deleteAction');
    Route::put('/updateActionActivity/{action}', 'updateActionActivity')->name('updateActionActivity');
    Route::put('/updateActionStatus/{action}', 'updateActionStatus')->name('updateActionStatus');
});

Route::middleware(['auth', 'role:admin'])->controller(ActionControllerGet::class)->group(function () {
    Route::get('/admin/actions', 'getActions')->name('admin.actions');
});

require __DIR__.'/settings.php';
