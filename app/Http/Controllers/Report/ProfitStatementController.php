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

class ProfitStatementController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }



    private function getMonths()
    {
        return [
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
    }

    private function getData($id, $startDate = null, $endDate = null)
    {

        $user = DB::table('users')->find($id);

        $accounting_period = $user->accounting_period;
        list($day, $month) = explode('/', $accounting_period);

        // ตรวจสอบและแปลง $startDate และ $endDate ให้เป็น Carbon object หากยังไม่ใช่
        if (is_null($endDate)) {
            $endDate = Carbon::now()->subMonth()->endOfMonth(); // วันสุดท้ายของเดือนก่อนหน้า
            $startDate = Carbon::now()->subMonth()->startOfMonth(); // วันที่ 1 ของเดือนก่อนหน้า
        } else {
            // ตรวจสอบว่าถูกส่งมาเป็น string หรือไม่ ถ้าใช่ให้แปลงเป็น Carbon object
            if (!($startDate instanceof Carbon)) {
                $startDate = Carbon::parse($startDate);
            }
            if (!($endDate instanceof Carbon)) {
                $endDate = Carbon::parse($endDate);
            }
            $startDate = $startDate ?? Carbon::createFromDate(date('Y'), $month, $day);
            $endDate = $endDate ?? $startDate->copy()->addYear()->subDay();
        }

        // ใช้ endOfDay ได้อย่างถูกต้องหลังจากแปลงเป็น Carbon
        $endDate = $endDate->endOfDay();


        $query = DB::table('general_ledgers')
            ->where('gl_code_company', $id)
            ->whereRaw('LOWER(gl_report_vat) = ?', ['sell'])
            ->whereBetween(DB::raw('DATE(gl_date)'), [$startDate->toDateString(), $endDate->toDateString()])

            ->select(
                'id',
                'gl_date',
                'gl_document',
                'gl_company',
                'gl_taxid',
                'gl_branch',
                'gl_amount',
                'gl_tax',
                'gl_total',
                'gl_url',
                'gl_page'

            )
            //D,E,H,I,J,S,T,U
            ->orderBy('gl_date', 'ASC')
            ->get();


        return [
            'query' => $query,
            'user' => $user,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'day' => $day,
            'monthThai' => $this->getMonths()[$month] ?? 'เดือนไม่ถูกต้อง',
            'currentYear' => date('Y')
        ];
    }


    public function show(string $id)
    {
        $data = $this->getData($id); // รับค่ากลับมา



        return view('report.sell.view', [
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
}