<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function getUsers()
    {
        $query = DB::table('users')
            ->where('status', 0)
            ->get();

        return $query;
    }

    /**
     * ! สมุดรายวันทั่วไป */
    public function indexGeneralJournal()
    {


        $query = $this->getUsers();
        return view('report.general_journal.index', compact('query'));
    }

    public function showGeneralJournal(string $id)
    {

        $user = DB::table('users')
            ->where('id', $id)
            ->get();
        $accounting_period = $user[0]->accounting_period;

        // แยกวันที่และเดือนออกจาก $accounting_period
        list($day, $month) = explode('/', $accounting_period);

        // สร้างวันที่เริ่มต้น
        $startDate = Carbon::createFromDate(date('Y'), $month, $day);

        // สร้างวันที่สิ้นสุด (เช่นสิ้นปีหรือสิ้นรอบถัดไป)
        $endDate = $startDate->copy()->addYear()->subDay();


        /* $ledgers = DB::table('general_ledgers')
            ->where('general_ledgers.gl_code_company', $id)
            ->whereBetween('general_ledgers.gl_date', [$startDate, $endDate])
            ->leftJoin('general_ledger_subs', 'general_ledgers.gl_code', 'general_ledger_subs.gls_gl_code')
            ->select(
                'general_ledgers.id',
                'general_ledgers.gl_document',
                'general_ledgers.gl_date',
                'general_ledgers.gl_company',
                'general_ledgers.gl_description',
                'general_ledger_subs.gls_account_name',
                'general_ledger_subs.gls_debit',
                'general_ledger_subs.gls_credit',
            )
            ->orderBy('general_ledgers.id')
            ->get(); */

        $query = DB::table('general_ledgers')
            ->where('general_ledgers.gl_code_company', $id)
            ->whereBetween('general_ledgers.gl_date', [$startDate, $endDate])
            ->leftJoin('general_ledger_subs', 'general_ledgers.gl_code', 'general_ledger_subs.gls_gl_code')
            ->select(
                'general_ledgers.id',
                'general_ledgers.gl_document',
                'general_ledgers.gl_date',
                'general_ledgers.gl_company',
                'general_ledgers.gl_description',
                'general_ledger_subs.gls_account_name',
                'general_ledger_subs.gls_debit',
                'general_ledger_subs.gls_credit',
            )
            ->orderBy('general_ledgers.id')
            ->orderBy('general_ledger_subs.gls_id')
            ->get();





        return view('report.general_journal.view', compact('query'));
    }
}