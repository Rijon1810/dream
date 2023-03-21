<?php

use App\Http\Controllers\ArtisanController;
use App\Http\Controllers\PseudoNameController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Trackers\Prayer\DailyController as PrayerTrackerDailyController;
use Illuminate\Support\Facades\Route;

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
Route::view('/', 'dashboard')->middleware('auth')->name('dashboard');

Route::prefix('/pseudo-names')->group(function () {
  Route::controller(PseudoNameController::class)->group(function () {
    Route::get('/available', 'available');
  }
  );
});

Route::controller(UserController::class)->group(function () {
  Route::get('/register', 'register')->middleware('guest')->name('register.show');
  Route::post('/users', 'store')->middleware('guest')->name('register');
  Route::get('/login', 'login')->middleware('guest')->name('login.show');
  Route::post('/users/authenticate', 'authenticate')->middleware('guest')->name('login');
  Route::get('/logout', 'destroy')->middleware('auth')->name('logout');
  Route::get('/edit-profile', 'showEditProfile')->middleware('auth')->name('edit_profile.show');
  Route::put('/edit-profile', 'editProfile')->middleware('auth')->name('edit_profile');
});

Route::middleware('auth')->group(function () {
  Route::prefix('/trackers')->group(function () {
    Route::prefix('/prayer')->group(function () {
      Route::get('daily/{date}', [PrayerTrackerDailyController::class, 'show'])
        ->name('prayer_tracker_daily.show');
      Route::put('daily/{date}', [PrayerTrackerDailyController::class, 'update'])
        ->name('prayer_tracker_daily.update');
    });
  });
});

Route::prefix('/artisan')->group(function () {
  Route::controller(ArtisanController::class)->group(function () {
    Route::get('/config-clear', 'configClear');
    Route::get('/config-cache', 'configCache');
    Route::get('/cache-clear', 'cacheClear');
    Route::get('/view-clear', 'viewClear');
    Route::get('/storage-link', 'storageLink');
    Route::get('/migrate', 'migrate');
    Route::get('/seed-pseudo-name', 'seedPseudoName');
  });
});
