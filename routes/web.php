<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AnimeMerchController;

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/anime_merch', function () {
    return view('pages.plp');
})->name('plp');

Route::get('/anime_merch/{i}', function () {
    return view('pages.pdp');
})->name('pdp');