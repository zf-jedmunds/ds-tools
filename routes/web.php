<?php

use App\Http\Controllers\RequestInspectorController;
use App\Http\Controllers\SpeedtestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/json-prettifier', function () {
    return view('prettifier');
})->name('tools.json-prettifier');

Route::get('/minifier', function () {
    return view('minifier');
})->name('tools.minifier');

Route::get('/html-formatter', function () {
    return view('html-formatter');
})->name('tools.html-formatter');

// Request Inspector
Route::get('/request-inspector', [RequestInspectorController::class, 'index'])->name('tools.request-inspector');
Route::get('/webhook/poll', [RequestInspectorController::class, 'poll']);
Route::delete('/webhook/clear', [RequestInspectorController::class, 'clear']);
Route::match(['post', 'put', 'patch'], '/webhook/{path?}', [RequestInspectorController::class, 'receive'])->where('path', '.*');

Route::get('/base64', function () {
    return view('base64');
})->name('tools.base64');

Route::get('/jwt-debugger', function () {
    return view('jwt-debugger');
})->name('tools.jwt-debugger');

Route::get('/regex-tester', function () {
    return view('regex-tester');
})->name('tools.regex-tester');

// Speedtest
Route::get('/speedtest', [SpeedtestController::class, 'index'])->name('tools.speedtest');
Route::get('/speedtest/download', [SpeedtestController::class, 'download']);
Route::post('/speedtest/upload', [SpeedtestController::class, 'upload']);
Route::get('/speedtest/ping', [SpeedtestController::class, 'ping']);


