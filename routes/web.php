<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Report\GeneralJournalController;
use App\Http\Controllers\Report\BuyController;
use App\Http\Controllers\Report\SellController;


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// admin สุด
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('select-card/{id}', [HomeController::class, 'selectCard'])->name('select-card/');
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

//รายงานทั่วไป
Route::get('/report/general_journal', [GeneralJournalController::class, 'index'])->name('report/general_journal');
Route::get('/report/general-journal-view/{id}', [GeneralJournalController::class, 'show'])->name('general-journal-view');
Route::get('/export-pdf/{id}', [GeneralJournalController::class, 'exportPDF'])->name('export-pdf');
Route::get('/export-excel/{id}', [GeneralJournalController::class, 'exportExcel'])->name('export-excel');
Route::post('/search-date', [GeneralJournalController::class, 'search'])->name('search-date');

//รายงานซื้อ
Route::get('report/buy', [BuyController::class, 'index'])->name('report/buy');
Route::get('report/buy-view/{id}', [BuyController::class, 'show'])->name('report/buy-view');
Route::get('buy-pdf/{id}', [BuyController::class, 'exportPDF'])->name('buy-pdf');
Route::get('buy-excel/{id}', [BuyController::class, 'exportExcel'])->name('buy-excel');
Route::post('search-buy', [BuyController::class, 'search'])->name('search-buy');

Route::get('report/sell', [SellController::class, 'index'])->name('report/sell');
Route::get('report/sell-view/{id}', [SellController::class, 'show'])->name('report/sell-view');
Route::post('search-sell', [SellController::class, 'search'])->name('search-sell');
Route::get('sell-pdf/{id}', [SellController::class, 'exportPDF'])->name('sell-pdf');
Route::get('sell-excel/{id}', [SellController::class, 'exportExcel'])->name('sell-excel');





Route::fallback(function () {
    return view('404');
});
