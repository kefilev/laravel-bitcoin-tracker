<?php

use App\Http\Controllers\SubscriberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::controller(SubscriberController::class)->group(function () {
    Route::get('/subscribe', 'subscribe')->name('subscribe'); //use get to let users subscribe even from the browser
    Route::get('/unsubscribe', 'unsubscribe')->name('unsubscribe'); //using get to let users unsubscribe by visiting a link
});
