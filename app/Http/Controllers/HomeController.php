<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;

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
        $query = DB::table('users')
            ->where('users.status', 0)
            ->select(
                'users.*',
                DB::raw('(SELECT COUNT(*) FROM general_ledgers WHERE general_ledgers.gl_code_company = users.id) as general_ledger_count'),
                DB::raw('(SELECT COUNT(*) FROM general_ledger_subs WHERE general_ledger_subs.gls_code_company = users.id) as general_ledger_sub_count'),
                DB::raw('(SELECT COUNT(*) FROM account__codes WHERE account__codes.acc_code_company = users.id) as account_code_count')
            )
            ->get();

        return view('home', compact('query'));
    }
    public function selectCard($id)
    {

        session(['company_id' => $id]);
        $query =  User::find($id);
        session(['company_name' =>  $query->company]);

        return redirect()->back()->with('success', "เลือก $query->company เรียบร้อย");
    }
    public function google_sheet()
    {

        $query = DB::table('users')
            ->where('status', 0)
            ->get();
        return view('update.index', compact('query'));
    }

    public function importData($id)
    {
        $query =  User::find($id);

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

        $query =  User::find($id);

        $queryAccount = DB::table('account__codes')->where('acc_code_company', $query->id)->count();
        $queryGeneral = DB::table('general_ledgers')->where('gl_code_company', $query->id)->count();
        $queryGeneralSub = DB::table('general_ledger_subs')->where('gls_code_company', $query->id)->count();


        // เช็คว่าทั้ง 3 ตารางเป็น true หรือไม่

        return response()->json(["queryAccount" => $queryAccount, "queryGeneral" =>  $queryGeneral, "queryGeneralSub" => $queryGeneralSub]);
    }
}