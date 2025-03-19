<?php

use App\Http\Controllers\SubscriberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::controller(SubscriberController::class)->group(function () {
    Route::post('/subscribers', 'subscribe')->name('subscribe');

    //using get to let users unsubscribe by visiting a link
    Route::get('/subscribers/{id}/unsubscribe', 'unsubscribe')->name('unsubscribe'); 
});
