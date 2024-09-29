<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CompanyController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/company', [CompanyController::class, 'index'])->name('company');
Route::get('/company-create', [CompanyController::class, 'create'])->name('company-create');




Route::fallback(function () {
    return view('404');
});