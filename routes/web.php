<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/anime_merch', function () {
    return view('pages.plp');
})->name('plp');

Route::get('/anime_merch/{i}', function () {
    return view('pages.pdp');
})->name('pdp');