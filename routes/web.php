<?php

use App\Http\Controllers\URLShortenerController;
use Illuminate\Support\Facades\Route;

Route::get('/{short_url}', [URLShortenerController::class, 'redirect']);
