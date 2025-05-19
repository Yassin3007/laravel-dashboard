<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\YassinController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\AhmedController;
use App\Http\Controllers\CategoryController;

Route::get('/', function () {
    return view('dashboard.temp.index');
})->name('dashboard');



