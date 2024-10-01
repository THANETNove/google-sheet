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
Route::get('/import-data/{id}', [HomeController::class, 'importData'])->name('import-data');
Route::get('/company', [CompanyController::class, 'index'])->name('company');
Route::get('/company-create', [CompanyController::class, 'create'])->name('company-create');
Route::post('/company-store', [CompanyController::class, 'store'])->name('company-store');
Route::get('/company-edit/{id}', [CompanyController::class, 'edit'])->name('company-edit');
Route::put('/company-update/{id}', [CompanyController::class, 'update'])->name('company-update');
Route::get('/company-delete/{id}', [CompanyController::class, 'destroy'])->name('company-delete');
Route::post('/import-data/save-company-data', [CompanyController::class, 'saveCompanyData'])->name('import-data/save-company-data');





Route::fallback(function () {
    return view('404');
});
