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
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LedgerController extends Controller
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

        // ก่อน start date 16,238,926.78 +13764.23+ 389774.68 +30585.03+ 14450.09
        //  16,687,500.81


        $before_date_query = DB::table('general_ledger_subs')
            ->leftJoin('general_ledgers', 'general_ledger_subs.gls_gl_code', '=', 'general_ledgers.gl_code')
            ->where('gls_code_company', $id)
            ->whereDate('gls_gl_date', '<=', $carryForwardDate->toDateString())
            ->where(function ($q) {
                $q->where('gls_account_code', 'like', '1%')
                    ->orWhere('gls_account_code', 'like', '2%')
                    ->orWhere('gls_account_code', 'like', '3%');
            })
            ->select(
                'general_ledgers.gl_company',
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

        //   dd($before_date_query);

        $before_date_query_2 = DB::table('general_ledger_subs')
            ->leftJoin('general_ledgers', 'general_ledger_subs.gls_gl_code', '=', 'general_ledgers.gl_code')

            ->where('gls_code_company', $id)
            ->whereBetween(DB::raw('DATE(gls_gl_date)'), [$startPeriod->toDateString(), $carryForwardDate->toDateString()])
            ->where(function ($q) {
                $q->where('gls_account_code', 'like', '4%')
                    ->orWhere('gls_account_code', 'like', '5%');
            })
            ->select(
                'general_ledgers.gl_company',
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


        $combined_result = $before_date_query->merge($before_date_query_2);
        $combined_result = $combined_result->sortBy('gls_account_code');

        // หลัง start date




        $date_query = DB::table('general_ledger_subs')
            ->leftJoin('general_ledgers', 'general_ledger_subs.gls_gl_code', '=', 'general_ledgers.gl_code')

            ->where('gls_code_company', $id)
            ->whereBetween(DB::raw('DATE(gls_gl_date)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->select(
                'general_ledgers.gl_company',
                'gls_gl_date',
                'gls_account_code',
                'gls_gl_document',
                'gls_account_name',
                'gls_debit',
                'gls_credit'
                /*       DB::raw('SUM(gls_debit) as total_debit'),
                DB::raw('SUM(gls_credit) as total_credit') */
            )
            // ->groupBy('gls_account_code', 'gls_gl_date') // Group by account code and date
            ->orderBy('gls_gl_date', 'ASC')
            ->get()
            ->groupBy('gls_account_code'); // Group results by account code for easy access in Blade


        // เรียง $date_query ตาม gls_account_code
        $date_query = $date_query->sortKeys(); // เรียงลำดับตาม gls_account_code

        // เพิ่ม before_total ให้กับแต่ละรายการของ account code
        foreach ($date_query as $accountCode => $transactions) {
            // ตรวจสอบว่าใน $combined_result มีค่า for $accountCode นี้หรือไม่
            $before_total = $combined_result->firstWhere('gls_account_code', $accountCode)->before_total ?? 0;

            // เพิ่ม before_total ให้กับแต่ละรายการของ account code นั้นๆ
            foreach ($transactions as $transaction) {
                $transaction->before_total = $before_total;
            }
        }

        return [
            'date_query' => $date_query,
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


        return view('report.ledger.view', [
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

        return view('report.ledger.view', [
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
}
