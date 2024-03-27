<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\LoginController;
use App\Http\Controllers\admin\RegisterController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\AdminReservationController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\PaymentController;
use App\Http\Middleware\AdminMiddleware;

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
    return view('/reservation.index');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
*/
Route::view('/admin/login', 'admin/login');
Route::post('/admin/login', [LoginController::class, 'login']);
Route::post('admin/logout', [LoginController::class,'logout'])->name('admin.logout');
Route::view('/admin/register', 'admin/register');
Route::post('/admin/register', [RegisterController::class, 'register']);
Route::view('/admin/home', 'admin/home')->middleware('auth:admin');

/*
|--------------------------------------------------------------------------
| 予約
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::resource('reservation', ReservationController::class)
        ->except([
            'create',
            'index'
        ]);

    Route::get('/reservation/create/{date}',[ReservationController::class, 'create']);

    Route::post('/reservation/showroom',[ReservationController::class, 'showroom'])->name('reservation.showroom');

    Route::post('/reservation/{reservation}/editshowroom',[ReservationController::class, 'editshowroom'])->name('reservation.editshowroom');

    Route::post('/reservation/confirm',[ReservationController::class, 'confirm'])->name('reservation.confirm');

    Route::post('/reservation/{reservation}/editconfirm',[ReservationController::class, 'editconfirm'])->name('reservation.editconfirm');

    Route::get('/thanks',[ReservationController::class, 'thanks'])->name('thanks.index');

});

Route::get('/',[ReservationController::class, 'index'])->name('reservation.index');

Route::get('/reservation',[ReservationController::class, 'calendar']);

Route::get('/admin',[ReservationController::class, 'adminCalendar']);

/*
|--------------------------------------------------------------------------
| 部屋
|--------------------------------------------------------------------------
*/
Route::middleware([AdminMiddleware::class])->group(function () {

    Route::resource('room',RoomController::class)
        ->except([
            'show'
        ]);

});

Route::get('room/{room}', [RoomController::class, 'show'])->name('room.show')->withoutMiddleware([AdminMiddleware::class]);
/*
|--------------------------------------------------------------------------
| ダッシュボード
|--------------------------------------------------------------------------
*/
Route::middleware([AdminMiddleware::class])->group(function () {

    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard.index');

    //予約機能ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー

    Route::get('/dashboard/reservation',[AdminReservationController::class,'index'])->name('dashboard.reservation.index');

    Route::get('/dashboard/reservation/{reservation}',[AdminReservationController::class, 'show'])->name('dashboard.reservation.show');

    Route::get('/dashboard/reservation/{reservation}/edit',[AdminReservationController::class,'edit'])->name('dashboard.reservation.edit');

    Route::post('/dashboard/reservation/{reservation}/editshowroom',[AdminReservationController::class, 'editshowroom'])->name('dashboard.reservation.editshowroom');

    Route::delete('/dashboard/reservation/{reservation}', [AdminReservationController::class, 'destroy'])->name('dashboard.reservation.destroy');

    Route::post('/dashboard/reservation/{reservation}/editconfirm',[AdminReservationController::class, 'editconfirm'])->name('dashboard.reservation.editconfirm');

    Route::patch('/dashboard/reservation/{reservation}', [AdminReservationController::class, 'update'])->name('dashboard.reservation.update');


    //ユーザー編集機能-------------------------------------------------------------------------

    Route::get('/dashboard/user',[AdminUserController::class,'index'])->name('dashboard.user.index');

    Route::get('/dashboard/user/{user}/edit',[AdminUserController::class,'edit'])->name('dashboard.user.edit');
    
    Route::patch('/dashboard/user/{user}', [AdminUserController::class, 'update'])->name('dashboard.user.update');

    Route::delete('/dashboard/user/{user}', [AdminUserController::class, 'destroy'])->name('dashboard.user.destroy');


});
/*
|--------------------------------------------------------------------------
| マイページ
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::resource('mypage',MypageController::class)
    ->except([
        'create',
        'show',
        'store',
    ]);
    Route::get('/mypage/change_password',[MypageController::class,'change_password'])->name('mypage.change_password');
    Route::patch('/mypage/change_password/{id}', [MypageController::class, 'update_password'])->name('mypage.update_password');

});

/*
|--------------------------------------------------------------------------
| 決済
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/create/{reservation}', [PaymentController::class, 'create'])->name('create');
        Route::post('/store', [PaymentController::class, 'store'])->name('store');
    });

});