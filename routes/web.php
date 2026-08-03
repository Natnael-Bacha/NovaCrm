<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminControllerGet;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLoginPage'])
    ->name('home');


Route::middleware(['auth', 'role:admin'])->group(function(){

    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('adminDashboard');

    Route::get('/admin/team', function () {
        return view('admin.team');
    });



   

});


Route::middleware('guest')->controller(AuthController::class)->group(function(){
 Route::get('/login', 'showLoginPage')->name('show.loginPage');
 Route::post('/login', 'handleLogin')->name('login');
 Route::get('/createUser', 'showCreatePage')->name('show.createPage');
});




Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:admin'])->controller(AdminControllerGet::class)->group(function(){
Route::get('/admin/team',  'getSupervisors')->name('getSupervisors');
Route::get('/admin/leads', 'getAgents')->name('leads');
Route::get('/getLeads',  'getLeads')->name('getLeads');
Route::get('/getTeams',  'getTeams')->name('getTeams');
Route::get('/getProjects',  'getProjects')->name('getProjects');
Route::get('/getUsers',  'getUsers')->name('getUsers');
Route::get('/projects', 'showProjects')->name('projects.index');
Route::get('/admin/unit', 'getUnits')->name('admin.units');
Route::get('/admin/pipeline', 'getLeads')->name('admin.pipeline');
Route::get('/admin/deal', 'getDeals')->name('admin.deals');
Route::get('/admin/actions', 'getActions')->name('admin.actions');

});


Route::middleware(['auth', 'role:admin'])->controller(UserController::class)->group(function(){
 Route::get('/createUser', 'showCreateUser')->name('show.createUser');
});

Route::middleware(['auth', 'role:admin'])->controller(AdminController::class)->group(function(){
Route::post('/createlead', 'createLead')->name('createLead');
Route::post('/createUser', 'createUser')->name('createUser');
Route::put('/updateRole/{id}',  'updateRole')->name('updateRole');
Route::put('/changeSupervisors', 'changeSupervisors')->name('changeSupervisors');
Route::put('/updateLeadStatus/{id}',  'updateLeadStatus')->name('updateLeadStatus');
Route::post('/createProject', 'createProject')->name('createProject');
Route::put('/updateUser/{id}',  'updateUser')->name('updateUser');
Route::put('/deleteUser/{id}',  'deleteUser')->name('deleteUser');
Route::put('/updateLead/{id}',  'updateLead')->name('updateLead');
Route::delete('/deleteLead/{id}',  'deleteLead')->name('deleteLead');
Route::get('/getUserLeads/{id}',  'getUserLeads')->name('getUserLeads');
Route::get('/projects/{id}/edit', 'getProjectData')->name('projects.edit');
Route::put('/updateProject/{id}', 'updateProject')->name('updateProject');
Route::delete('/updateProject/{id}', 'deleteProject')->name('deleteProject'); 
Route::post('/createUnit', 'createUnit')->name('createUnit');
Route::put('/updateUnit/{id}', 'updateUnit')->name('updateUnit');
Route::delete('/deleteUnit/{id}',  'deleteUnit')->name('deleteUnit');
Route::put('/leads/{id}/stage', 'updateLeadStage')->name('updateLeadStage');
Route::post('/createDeal/{lead}', 'createDeal')->name('createDeal');
Route::put('/updateDeal/{id}', 'updateDeal')->name('updateDeal');
Route::delete('/deleteDeal/{deal}', 'deleteDeal')->name('deleteDeal');
Route::put('/updateDealPaymentStatus/{deal}', 'updateDealPaymentStatus')->name('updateDealPaymentStatus');
Route::post('/createAction/{lead}', 'createAction')->name('createAction');
Route::put('/updateAction/{action}', 'updateAction')->name('updateAction');
Route::delete('/actions/{action}', 'deleteAction')->name('deleteAction');
Route::put('/updateActionActivity/{action}', 'updateActionActivity')->name('updateActionActivity');
Route::put('/updateActionStatus/{action}', 'updateActionStatus')->name('updateActionStatus');
});



require __DIR__.'/settings.php';
