<?php

use App\Http\Controllers\URLShortenerController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::post('encode', [URLShortenerController::class, 'encode']);
    Route::post('decode', [URLShortenerController::class, 'decode']);
});
