<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// admin สุด
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/update/google-sheet', [HomeController::class, 'google_sheet'])->name('update/google-sheet');
Route::get('/update/import-data/{id}', [HomeController::class, 'importData'])->name('update/import-data');
Route::get('/count-data/{id}', [HomeController::class, 'countData'])->name('count-data');
Route::get('/company', [CompanyController::class, 'index'])->name('company');
Route::get('/company-create', [CompanyController::class, 'create'])->name('company-create');
Route::post('/company-store', [CompanyController::class, 'store'])->name('company-store');
Route::get('/company-edit/{id}', [CompanyController::class, 'edit'])->name('company-edit');
Route::put('/company-update/{id}', [CompanyController::class, 'update'])->name('company-update');
Route::get('/company-delete/{id}', [CompanyController::class, 'destroy'])->name('company-delete');
Route::post('/save-company-data', [CompanyController::class, 'saveCompanyData'])->name('save-company-data');
Route::get('/report/general_journal', [ReportController::class, 'indexGeneralJournal'])->name('report/general_journal');
Route::get('/general-journal-view/{id}', [ReportController::class, 'showGeneralJournal'])->name('general-journal-view');
Route::get('export-pdf/{id}', [ReportController::class, 'exportPDF'])->name('export-pdf');





Route::fallback(function () {
    return view('404');
});