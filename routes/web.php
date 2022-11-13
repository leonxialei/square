<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Middleware\UserAccount;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ChannelCodeController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\UpstreamController;
use App\Http\Controllers\UpstreamAdvanceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdvanceController;
use App\Http\Controllers\PayTestController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalyzeController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/pay/test/create/', [PayTestController::class, 'order']);
Route::get('/pay/testcreate/{param}', [PayTestController::class, 'testcreate']);
Route::get('/', function () {
    return redirect("login");
});
Route::get('/user/image', [UserController::class, 'image']);
Route::get('/login', [UserController::class, 'login']);
Route::get('/user/logout', [UserController::class, 'logout']);
Route::resources([
    'user' => UserController::class,
]);
Route::middleware([UserAccount::class])->group(function () {
    Route::get('/user/change/password', [UserController::class, 'changePassword']);
    Route::post('/user/change/password', [UserController::class, 'storePassword']);
    Route::get('/order/statistics', [OrderController::class, 'statistics']);
    Route::get('/merchant/cash', [MerchantController::class, 'cash']);
    Route::get('/merchant/cash/{id}', [MerchantController::class, 'detail']);
    Route::post('/merchant/cash/{id}', [MerchantController::class, 'store_cash']);
    Route::get('/export/order', [ExportController::class, 'order']);
    Route::get('/export/today', [ExportController::class, 'today']);
    Route::get('/home/console', [HomeController::class, 'console']);
    Route::get('/home/total', [HomeController::class, 'total']);
    Route::get('/list/upstream', [UpstreamController::class, 'list']);
    Route::get('/channel/merchant/{id}', [ChannelController::class, 'merchant']);
    Route::resources([
        'home' => HomeController::class,
        'channel/code' => ChannelCodeController::class,
        'channel' => ChannelController::class,
        'merchant' => MerchantController::class,
        'upstream/advance' => UpstreamAdvanceController::class,
        'upstream' => UpstreamController::class,
        'order' => OrderController::class,
        'advance' => AdvanceController::class,
        'agency' => AgencyController::class,
        'dashboard' => DashboardController::class,
        'analyze' => AnalyzeController::class,
        'dashboard' => DashboardController::class
    ]);
});
