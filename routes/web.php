<?php

use App\Http\Controllers\Api\MyFatoorahController;
use App\Models\Order;
use Illuminate\Support\Facades\Route;

// MyFatoorah Web Routes (for redirects)
Route::prefix('myfatoorah')->group(function () {
    Route::get('/callback', [MyFatoorahController::class, 'callback'])
    ->name('myfatoorah.callback');
});

