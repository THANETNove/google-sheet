<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Report\GeneralJournalController;
use App\Http\Controllers\Report\BuyController;
use App\Http\Controllers\Report\SellController;
use App\Http\Controllers\Report\ProfitStatementController;
use App\Http\Controllers\Report\TrialBalanceBeforeClosingController;
use App\Http\Controllers\Report\AccountBalanceSheetController;


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
Route::get('/export-pdf/{id}/{start_date}/{end_date}', [GeneralJournalController::class, 'exportPDF'])->name('export-pdf');
Route::get('/export-excel/{id}/{start_date}/{end_date}', [GeneralJournalController::class, 'exportExcel'])->name('export-excel');
Route::post('/report/search-date', [GeneralJournalController::class, 'search'])->name('report/search-date');

//รายงานซื้อ
Route::get('report/buy', [BuyController::class, 'index'])->name('report/buy');
Route::get('report/buy-view/{id}', [BuyController::class, 'show'])->name('report/buy-view');
Route::get('buy-pdf/{id}/{month}/{year}', [BuyController::class, 'exportPDF'])->name('buy-pdf');
Route::get('buy-excel/{id}/{month}/{year}', [BuyController::class, 'exportExcel'])->name('buy-excel');
Route::post('report/search-buy', [BuyController::class, 'search'])->name('report/search-buy');
// รายการขาย
Route::get('report/sell', [SellController::class, 'index'])->name('report/sell');
Route::get('report/sell-view/{id}', [SellController::class, 'show'])->name('report/sell-view');
Route::post('report/search-sell', [SellController::class, 'search'])->name('report/search-sell');
Route::get('sell-pdf/{id}/{start_date}/{end_date}', [SellController::class, 'exportPDF'])->name('sell-pdf');
Route::get('sell-excel/{id}/{start_date}/{end_date}', [SellController::class, 'exportExcel'])->name('sell-excel');

// งบกำไร(ขาดทุน)
Route::get('report/profit-statement/{id}', [ProfitStatementController::class, 'show'])->name('report/profit-statement');
Route::post('report/search-profit-statement', [ProfitStatementController::class, 'search'])->name('report/search-profit-statement');
Route::get('profit-statement-pdf/{id}/{month}/{year}', [ProfitStatementController::class, 'exportPDF'])->name('profit-statement-pdf');
Route::get('profit-statement-excel/{id}/{start_date}/{end_date}', [ProfitStatementController::class, 'exportExcel'])->name('profit-statement-excel');

// งบทดลองก่อนปิดบัญชี
Route::get('report/trial-balance-before-closing/{id}', [TrialBalanceBeforeClosingController::class, 'show'])->name('report/trial-balance-before-closing');
Route::post('report/search-trial-balance-before-closing', [TrialBalanceBeforeClosingController::class, 'search'])->name('report/search-trial-balance-before-closing');
Route::get('trial-balance-before-closing-pdf/{id}/{month}/{year}', [TrialBalanceBeforeClosingController::class, 'exportPDF'])->name('trial-balance-before-closing-pdf');
Route::get('trial-balance-before-closing-excel/{id}/{month}/{year}', [TrialBalanceBeforeClosingController::class, 'exportExcel'])->name('trial-balance-before-closing-pdf');

//งบดุลบัญชี
Route::get('report/account-balance-sheet/{id}', [AccountBalanceSheetController::class, 'show'])->name('report/account-balance-sheet');
Route::post('report/search-account-balance-sheet', [AccountBalanceSheetController::class, 'search'])->name('report/search-account-balance-sheet');




Route::fallback(function () {
    return view('404');
});