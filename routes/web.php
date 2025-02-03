<?php

use App\Http\Controllers\URLShortenerController;
use Illuminate\Support\Facades\Route;

Route::get('/{code}', [URLShortenerController::class, 'redirect']);
