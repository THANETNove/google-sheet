<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Company;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $query = DB::table('companies')->get();
        return view('home', compact('query'));
    }

    public function importData($id)
    {
        $query =  Company::find($id);

        $queryAccount = DB::table('account__codes')->where('acc_code_company', $query->code_company)->count();
        $queryGeneral = DB::table('general_ledgers')->where('gl_code_company', $query->code_company)->count();
        $queryGeneralSub = DB::table('general_ledger_subs')->where('gls_code_company', $query->code_company)->count();
        $isAccountDataPresent = $queryAccount > 0;
        $isGeneralDataPresent = $queryGeneral > 0;
        $isGeneralSubDataPresent = $queryGeneralSub > 0;

        // เช็คว่าทั้ง 3 ตารางเป็น true หรือไม่
        $isAllDataPresent = $isAccountDataPresent && $isGeneralDataPresent && $isGeneralSubDataPresent;

        return view('company.import_data', compact('query', 'isAllDataPresent'));
    }
    public function countData($id)
    {

        $query =  Company::find($id);

        $queryAccount = DB::table('account__codes')->where('acc_code_company', $query->id)->count();
        $queryGeneral = DB::table('general_ledgers')->where('gl_code_company', $query->id)->count();
        $queryGeneralSub = DB::table('general_ledger_subs')->where('gls_code_company', $query->id)->count();


        // เช็คว่าทั้ง 3 ตารางเป็น true หรือไม่

        return response()->json(["queryAccount" => $queryAccount, "queryGeneral" =>  $queryGeneral, "queryGeneralSub" => $queryGeneralSub]);
    }
}
