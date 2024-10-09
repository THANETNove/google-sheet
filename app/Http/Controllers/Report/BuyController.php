<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;


class BuyController extends Controller
{

    public function index()
    {


        $query = DB::table('users')
            ->where('status', 0)
            ->get();

        return view('report.buy.index', compact('query'));
    }


    public function getDataGlAndGl($id)
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

            ->select(
                'general_ledgers.id',
                'general_ledgers.gl_document',
                'general_ledgers.gl_date',
                'general_ledgers.gl_company',
                'general_ledgers.gl_description',
            )
            ->orderBy('general_ledgers.gl_date', 'ASC')
            ->get();

        $query = $query->map(function ($item) {
            // ปรับโซนเวลาให้เป็นโซนเวลาของท้องถิ่น (ถ้าจำเป็น)
            $item->gl_date = Carbon::parse($item->gl_date)->timezone('Asia/Bangkok')->format('Y-m-d H:i:s');
            return $item;
        });
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
}