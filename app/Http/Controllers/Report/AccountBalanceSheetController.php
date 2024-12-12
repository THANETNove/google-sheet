<?php

namespace App\Http\Controllers\report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AccountBalanceSheetController extends Controller
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
        $startDate = $startDate ?? Carbon::createFromDate(date('Y'), $month, $day);
        $startPeriod =  Carbon::createFromDate(date('Y'), $month, $day)->startOfDay();
        $startPeriod2 = Carbon::createFromDate(date('Y'), $month, $day)->subYear()->startOfDay(); // ย้อนหลัง 1 ปี

        $endDate = $endDate ?? $startDate->copy()->addYear()->subDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        $startDate = Carbon::parse($startDate); // Convert startDate to a Carbon instance
        $carryForwardDate = $startDate->copy()->endOfDay()->subDay(); // Now you can call copy(), endOfDay(), and subDay() on it
        //ปีก่อนหน้า
        $startOfYearDate = $startDate->copy()->subYear()->startOfYear()->startOfDay();
        $endOfYearDate = $endDate->copy()->subYear()->endOfYear()->endOfDay();

        // ก่อน start date
        $before_date_query = DB::table('general_ledger_subs')
            ->where('gls_code_company', $id)
            ->whereBetween(DB::raw('DATE(gls_gl_date)'), [$startPeriod->toDateString(), $carryForwardDate->toDateString()])
            ->where(function ($q) {
                $q->where('gls_account_code', 'like', '4%')
                    ->orWhere('gls_account_code', 'like', '5%');
            })
            ->select(
                'gls_account_code',
                'gls_account_name',
                'gls_gl_date',
                DB::raw("CASE 
                WHEN gls_account_code LIKE '4%' THEN SUM(gls_credit - gls_debit)
                WHEN gls_account_code LIKE '5%' THEN SUM(gls_debit - gls_credit)
                ELSE 0
             END as before_total")
            )
            ->groupBy('gls_account_code')
            ->get();

        // ก่อน start date
        $before_date_query1_3 = DB::table('general_ledger_subs')
            ->where('gls_code_company', $id)
            ->whereDate('gls_gl_date', '<=', $carryForwardDate->toDateString())
            ->where(function ($q) {
                $q->where('gls_account_code', 'like', '1%')
                    ->orWhere('gls_account_code', 'like', '2%')
                    ->orWhere('gls_account_code', 'like', '3%');
            })
            ->select(
                'gls_account_code',
                'gls_account_name',
                'gls_gl_date',
                DB::raw("CASE 
            WHEN gls_account_code LIKE '1%' THEN SUM(gls_debit - gls_credit)
            WHEN gls_account_code LIKE '2%' THEN SUM(gls_credit - gls_debit)
            WHEN gls_account_code LIKE '3%' THEN SUM(gls_credit - gls_debit)
            ELSE 0
         END as before_total"),
                DB::raw("SUM(CASE WHEN gls_account_code = '32-1001-01' THEN gls_credit ELSE 0 END) as before_total_result"),
            )
            ->groupBy('gls_account_code')
            ->get();










        // หลัง start date
        $after_query = DB::table('general_ledger_subs')
            ->where('gls_code_company', $id)
            ->whereBetween(DB::raw('DATE(gls_gl_date)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->where(function ($q) {
                $q->where('gls_account_code', 'like', '4%')
                    ->orWhere('gls_account_code', 'like', '5%');
            })
            ->select(
                'gls_account_code',
                'gls_account_name',
                'gls_gl_date',
                DB::raw("CASE 
                WHEN gls_account_code LIKE '4%' THEN SUM(gls_credit - gls_debit)
                WHEN gls_account_code LIKE '5%' THEN SUM(gls_debit - gls_credit)
                ELSE 0
             END as after_total")
            )
            ->groupBy('gls_account_code')
            ->get();

        // dd($startOfYearDate->toDateString(), $endOfYearDate->toDateString());
        $after_date_query1_3 = DB::table('general_ledger_subs')
            ->where('gls_code_company', $id)
            ->whereBetween(DB::raw('DATE(gls_gl_date)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->where(function ($q) {
                $q->where('gls_account_code', 'like', '1%')
                    ->orWhere('gls_account_code', 'like', '2%')
                    ->orWhere('gls_account_code', 'like', '3%');
            })
            ->select(
                'gls_account_code',
                'gls_account_name',
                'gls_gl_date',
                DB::raw("CASE 
            WHEN gls_account_code LIKE '1%' THEN SUM(gls_debit - gls_credit)
            WHEN gls_account_code LIKE '2%' THEN SUM(gls_credit - gls_debit)
            WHEN gls_account_code LIKE '3%' THEN SUM(gls_credit - gls_debit)
            ELSE 0
         END as after_total"),
                DB::raw("SUM(CASE WHEN gls_account_code = '32-1001-01' THEN gls_credit ELSE 0 END) as after_total_result"),
            )
            ->groupBy('gls_account_code')
            ->orderBy('gls_account_code', 'ASC')
            ->get();






        $query = DB::table('general_ledger_subs')
            ->where('gls_code_company', $id)
            ->whereBetween(DB::raw('DATE(gls_gl_date)'), [$startOfYearDate->toDateString(), $endOfYearDate->toDateString()])
            ->where(function ($q) {
                $q->where('gls_account_code', 'like', '32-1001-01')
                    ->orWhere('gls_account_code', 'like', '4%')
                    ->orWhere('gls_account_code', 'like', '5%');
            })
            ->select(
                DB::raw("SUM(CASE WHEN gls_account_code = '32-1001-01' THEN gls_credit ELSE 0 END) as acc_total_32"),
                DB::raw("SUM(CASE WHEN gls_account_code LIKE '4%' THEN (gls_credit - gls_debit) ELSE 0 END) as acc_total_4"),
                DB::raw("SUM(CASE WHEN gls_account_code LIKE '5%' THEN (gls_debit - gls_credit) ELSE 0 END) as acc_total_5")
            )
            ->get();

        $sum_after_total_4 = $after_query
            ->filter(fn($item) => Str::startsWith($item->gls_account_code, '4'))
            ->sum('after_total');
        $sum_after_total_5 = $after_query
            ->filter(fn($item) => Str::startsWith($item->gls_account_code, '5'))
            ->sum('after_total');
        // dd($sum_after_total_4 - $sum_after_total_5);


        $sum_before_total_4 = $before_date_query
            ->filter(fn($item) => Str::startsWith($item->gls_account_code, '4'))
            ->sum('before_total');
        $sum_before_total_5 = $before_date_query
            ->filter(fn($item) => Str::startsWith($item->gls_account_code, '5'))
            ->sum('before_total');
        $totalResult = $query->first()->acc_total_32 + ($query->first()->acc_total_4 - $query->first()->acc_total_5);
        $totalResult = ($sum_before_total_4 - $sum_before_total_5) + $totalResult;
        $afterTotalResult = $sum_after_total_4 - $sum_after_total_5;

        // รวมผลลัพธ์

        //dd(number_format($query->first()->acc_total_4, 2), $query->first()->acc_total_5);
        // 2. สร้าง $finalResult และตรวจสอบว่ามี total_result จริง
        $finalResult = collect([
            (object) [
                'gls_account_code' => '32-1001-01',
                'gls_account_name' => 'กำไร(ขาดทุน)สะสม',
                'gls_gl_date' => '2023-12-31 00:00:00',
                'after_total_credit' => 0,
                'before_total' => 0,
                'after_total' => 0,
                'total' => 0,
                'total_result' => $totalResult,
                'after_total_result' =>  $afterTotalResult,
            ]
        ]);




        // รวม array เข้าด้วยกัน
        $combined_query = $before_date_query
            ->merge($before_date_query1_3)
            ->merge($after_query)
            ->merge($finalResult)
            ->merge($after_date_query1_3);


        // จัดกลุ่มตาม gls_account_code และรวมยอด
        $combined_result = $combined_query
            ->groupBy('gls_account_code')
            ->map(function ($items) {

                $firstItem = $items->first();



                return (object) [
                    'gls_account_code' => $firstItem->gls_account_code,
                    'gls_account_name' => $firstItem->gls_account_name,
                    'gls_gl_date' => $firstItem->gls_gl_date,
                    'after_total_credit' => $firstItem->after_total_credit ?? 0,
                    'before_total' => $items->sum(fn($item) => $item->before_total ?? 0),
                    'after_total' => $items->sum(fn($item) => $item->after_total ?? 0),
                    'total' => $items->sum(fn($item) => ($item->before_total ?? 0) + ($item->after_total ?? 0)),
                    'before_total_result' => $items->sum(fn($item) => $item->total_result ?? 0), // ตรวจสอบค่า total_result จาก $firstItem
                    'after_total_result' => $items->sum(fn($item) => $item->after_total_result ?? 0), // ตรวจสอบค่า after_total_result จาก $firstItem
                ];
            })
            ->values()
            ->sortBy('gls_account_code');






        return [
            'date_query' => $combined_result,
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


        return view('report.account_balance_sheet.view', [
            'date_query' => $data['date_query'],
            'user' => $data['user'],
            'startDate' => $data['startDate'],
            'endDate' => $data['endDate'],
            'day' => $data['day'],
            'monthThai' => $data['monthThai'],
            'currentYear' => $data['currentYear'],
            'id' => $id
        ]);
    }

    public function search(Request $request)
    {

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $data = $this->getData($request->id, $startDate, $endDate);

        return view('report.account_balance_sheet.view', [
            'date_query' => $data['date_query'],
            'user' => $data['user'],
            'startDate' => $data['startDate'],
            'endDate' => $data['endDate'],
            'day' => $data['day'],
            'monthThai' => $data['monthThai'],
            'currentYear' => $data['currentYear'],
            'id' => $request->id
        ]);
    }

    public function exportPDF($id, $start_date, $end_date)
    {



        $data = $this->getData($id, $start_date, $end_date); // รับค่ากลับมา

        $pdf = PDF::loadView('report.account_balance_sheet.pdf_view', [
            'date_query' => $data['date_query'],
            'user' => $data['user'],
            'startDate' => $data['startDate'],
            'endDate' => $data['endDate'],
            'day' => $data['day'],
            'monthThai' => $data['monthThai'],
            'currentYear' => $data['currentYear'],
        ]);
        $pdf->setPaper('a4', 'landscape') // ขนาดกระดาษ A4
            ->setOption('margin-top', 15)
            ->setOption('margin-bottom', 15)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10);
        return $pdf->stream('exportPDF.pdf');
    }
}