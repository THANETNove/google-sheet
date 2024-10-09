<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;





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
    public function getDataGlAndGls($id)
    {
        $user = DB::table('users')
            ->where('id', $id)
            ->get();
        $accounting_period = $user[0]->accounting_period;

        // แยกวันที่และเดือนออกจาก $accounting_period
        list($day, $month) = explode('/', $accounting_period);


        $months = [
            '1' => 'มกราคม',
            '2' => 'กุมภาพันธ์',
            '3' => 'มีนาคม',
            '4' => 'เมษายน',
            '5' => 'พฤษภาคม',
            '6' => 'มิถุนายน',
            '7' => 'กรกฎาคม',
            '8' => 'สิงหาคม',
            '9' => 'กันยายน',
            '10' => 'ตุลาคม',
            '11' => 'พฤศจิกายน',
            '12' => 'ธันวาคม',
        ];

        // ตรวจสอบว่าเดือนที่ได้อยู่ในอาร์เรย์ไหม และแสดงเดือนภาษาไทย
        $monthThai = isset($months[$month]) ? $months[$month] : 'เดือนไม่ถูกต้อง';

        // สร้างวันที่เริ่มต้น
        $startDate = Carbon::createFromDate(date('Y'), $month, $day);
        $currentYear = date('Y'); // ดึงปีปัจจุบัน
        // สร้างวันที่สิ้นสุด (เช่นสิ้นปีหรือสิ้นรอบถัดไป)
        $endDate = $startDate->copy()->addYear()->subDay();


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
        return [
            'query' => $query,
            'user' => $user,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'day' => $day,
            'monthThai' => $monthThai,
            'currentYear' => $currentYear,
        ];
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
        $data = $this->getDataGlAndGls($id); // รับค่ากลับมา



        return view('report.general_journal.view', [
            'query' => $data['query'],
            'user' => $data['user'],
            'startDate' => $data['startDate'],
            'endDate' => $data['endDate'],
            'day' => $data['day'],
            'monthThai' => $data['monthThai'],
            'currentYear' => $data['currentYear'],
            'id' => $id
        ]);
    }

    public function exportPDF($id)
    {


        $data = $this->getDataGlAndGls($id); // รับค่ากลับมา



        $pdf = PDF::loadView('report.general_journal.pdf_view', [
            'query' => $data['query'],
            'user' => $data['user'],
            'startDate' => $data['startDate'],
            'endDate' => $data['endDate'],
            'day' => $data['day'],
            'monthThai' => $data['monthThai'],
            'currentYear' => $data['currentYear'],
        ]);
        $pdf->setPaper('a4', 'portrait') // ขนาดกระดาษ A4
            ->setOption('margin-top', 15)
            ->setOption('margin-bottom', 15)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10);
        return $pdf->stream('exportPDF.pdf');
    }
}