<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ManageUsersController;
use App\Http\Controllers\Admin\FrontendController;
use App\Http\Controllers\Admin\PageBuilderController;
use App\Http\Controllers\Admin\RankingController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


// ...

Route::get('/', [HomeController::class, 'index'])->name('home');

// Route::middleware('admin')->group(function () {

    Route::controller(AdminController::class)->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile', 'profileUpdate')->name('profile.update');
        Route::get('password', 'password')->name('password');
        Route::post('password', 'passwordUpdate')->name('password.update');
    });


    Route::controller(RankingController::class)->name('ranking.')->prefix('ranking')->group(function(){
        Route::get('list', 'list')->name('list');
        Route::get('docList', 'docList')->name('docList');
        Route::get('docList/{id}', 'docListDetails')->name('docListDetails');
        Route::post('store/{id?}', 'store')->name('store');
        Route::post('status/{id}', 'status')->name('status');
    });

// });