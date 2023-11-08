<?php

use BeyondCode\LaravelWebSockets\Apps\AppProvider;
use BeyondCode\LaravelWebSockets\Dashboard\DashboardLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register'=>false]);




Route::get('chat',function (AppProvider $appProvider){
//    Log::info(DashboardLogger::LOG_CHANNEL_PREFIX);
    return view('chat-app',[
        "port"=>env("LARAVEL_WEBSOCKETS_PORT"),
        "host"=>env("LARAVEL_WEBSOCKETS_HOST"),
        'authEndpoint'=>"/api/sockets/connect",
        "logChannel"=>DashboardLogger::LOG_CHANNEL_PREFIX,
        "apps"=>$appProvider->all()
    ]);
});
