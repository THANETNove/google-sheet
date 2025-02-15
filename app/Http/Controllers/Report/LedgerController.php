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
use App\Models\DataGeneralLedgerSubModel;

use App\Models\GeneralLedgerSub;
use App\Models\GeneralLedger;

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


        $query = GeneralLedgerSub::with('ledger') // โหลดข้อมูลจากตาราง general_ledgers
            ->where('gls_code_company', $id);

        $before_date_query = $query->clone()
            ->whereDate('gls_gl_date', '<=', $carryForwardDate->toDateString())
            ->where(function ($q) {
                $q->where('gls_account_code', 'like', '1%')
                    ->orWhere('gls_account_code', 'like', '2%')
                    ->orWhere('gls_account_code', 'like', '3%');
            })
            ->selectRaw("
        gls_account_code,
        gls_account_name,
        gls_gl_date,
        CASE 
            WHEN gls_account_code LIKE '1%' THEN SUM(gls_debit - gls_credit)
            WHEN gls_account_code LIKE '2%' THEN SUM(gls_credit - gls_debit)
            WHEN gls_account_code LIKE '3%' THEN SUM(gls_credit - gls_debit)
            ELSE 0
        END as before_total,
        SUM(CASE WHEN gls_account_code = '32-1001-01' THEN gls_credit ELSE 0 END) as before_total_result
    ")
            ->groupBy('gls_account_code')
            ->get();

        $before_date_query_2 = $query->clone()
            ->whereBetween(DB::raw('DATE(gls_gl_date)'),  [$startPeriod->toDateString(), $carryForwardDate->toDateString()])

            ->where(function ($q) {
                $q->where('gls_account_code', 'like', '4%')
                    ->orWhere('gls_account_code', 'like', '5%');
            })
            ->selectRaw("
                gls_account_code,
                gls_account_name,
                gls_gl_date,
                CASE 
                    WHEN gls_account_code LIKE '4%' THEN SUM(gls_credit - gls_debit)
                    WHEN gls_account_code LIKE '5%' THEN SUM(gls_debit - gls_credit)
                    ELSE 0
                END as before_total,
                SUM(CASE WHEN gls_account_code LIKE '4%' THEN (gls_credit - gls_debit) ELSE 0 END) as gls_credit,
                SUM(CASE WHEN gls_account_code LIKE '5%' THEN (gls_debit - gls_credit) ELSE 0 END) as gls_debit
            ")
            ->groupBy('gls_account_code')
            ->get();



        $combined_result = $before_date_query->merge($before_date_query_2);
        $combined_result = $combined_result->sortBy('gls_account_code');




        $after_date_query = $query->clone()
            ->whereBetween(DB::raw('DATE(gls_gl_date)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->where(function ($q) {
                $q->where('gls_account_code', 'like', '4%')
                    ->orWhere('gls_account_code', 'like', '5%');
            })
            ->selectRaw("
                gls_gl_document,
                gls_account_code,
                gls_account_name,
                gls_gl_date,
                SUM(CASE WHEN gls_account_code LIKE '4%' THEN (gls_credit - gls_debit) ELSE 0 END) as gls_credit,
                SUM(CASE WHEN gls_account_code LIKE '5%' THEN (gls_debit - gls_credit) ELSE 0 END) as gls_debit
            ")
            ->groupBy('gls_account_code')
            ->get();




        $after_date_query = $after_date_query->map(function ($beforeItem) use ($before_date_query_2) {
            $matchingItem = $before_date_query_2->firstWhere('gls_account_code', $beforeItem->gls_account_code);

            return (object) [
                'gls_account_code' => $beforeItem->gls_account_code,
                'gls_account_name' => $beforeItem->gls_account_name,
                'gls_gl_date' => $beforeItem->gls_gl_date,
                'gls_credit' => $matchingItem ? $matchingItem->gls_credit - ($beforeItem->gls_credit ?? 0) : ($beforeItem->gls_credit ?? 0),
                'gls_debit' => $matchingItem ? $matchingItem->gls_debit - ($beforeItem->gls_debit ?? 0) : ($beforeItem->gls_debit ?? 0),
            ];
        });



        $date_query1 = $query->clone()
            ->whereBetween(DB::raw('DATE(gls_gl_date)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw("
                gls_gl_date,
                gls_account_code,
                gls_gl_document,
                gls_account_name,
                gls_debit,
                gls_credit
            ")

            ->orderBy('gls_gl_date', 'ASC')
            ->get()
            ->groupBy('gls_account_code'); // Group results by account code for easy access in Blade


        $existingAccountCodes1 = $date_query1->keys()->unique();
        $date_query2 = $query->clone()
            ->whereBetween(DB::raw('DATE(gls_gl_date)'),  [$startPeriod->toDateString(), $carryForwardDate->toDateString()])
            ->whereNotIn('gls_account_code', $existingAccountCodes1)
            ->selectRaw("
                gls_gl_date,
                gls_account_code,
                gls_gl_document,
                gls_account_name,
                gls_debit,
                gls_credit
            ")
            ->orderBy('gls_gl_date', 'ASC')
            ->get()
            ->groupBy('gls_account_code'); // Group results by account code for easy access in Blade


        $existingAccountCodes2 = $date_query1->keys()->merge($date_query2->keys())->unique();


        // กรอง gls_account_code ใน $date_query3 ที่ไม่มีใน $date_query1 และ $date_query2
        $date_query3 = $query->clone()
            ->whereDate('gls_gl_date', '<=', $carryForwardDate->toDateString())
            ->whereNotIn('gls_account_code', $existingAccountCodes2) // ตรวจสอบว่ารหัสนี้ไม่มีใน $date_query1 และ $date_query2
            ->selectRaw("
                gls_gl_date,
                gls_account_code,
                gls_gl_document,
                gls_account_name,
                gls_debit,
                gls_credit
            ")
            ->orderBy('gls_gl_date', 'ASC')
            ->get()
            ->groupBy('gls_account_code');

        $date_query = collect($date_query1)
            ->merge($date_query2)
            ->merge($date_query3);


        $query = $query->clone()
            ->whereBetween(DB::raw('DATE(gls_gl_date)'), [$startOfYearDate->toDateString(), $endOfYearDate->toDateString()])
            ->where(function ($q) {
                $q->where('gls_account_code', 'like', '32-1001-01')
                    ->orWhere('gls_account_code', 'like', '4%')
                    ->orWhere('gls_account_code', 'like', '5%');
            })
            ->selectRaw("
                SUM(CASE WHEN gls_account_code = '32-1001-01' THEN gls_credit ELSE 0 END) as acc_total_32,
                SUM(CASE WHEN gls_account_code LIKE '4%' THEN (gls_credit - gls_debit) ELSE 0 END) as acc_total_4,
                SUM(CASE WHEN gls_account_code LIKE '5%' THEN (gls_debit - gls_credit) ELSE 0 END) as acc_total_5,
                SUM(CASE WHEN gls_account_code LIKE '4%' THEN (gls_credit - gls_debit) ELSE 0 END) as gls_credit,
                SUM(CASE WHEN gls_account_code LIKE '5%' THEN (gls_debit - gls_credit) ELSE 0 END) as gls_debit
            ")
            ->get();
        // รวมผลลัพธ์
        $totalResult = $query->first()->acc_total_32 + ($query->first()->acc_total_4 - $query->first()->acc_total_5);

        // เรียง $date_query ตาม gls_account_code
        // เรียงลำดับตาม gls_account_code

        // ตรวจสอบว่าหมวด 32-1001-01 มีอยู่ใน $date_query หรือไม่
        if (!$date_query->has('32-1001-01')) {
            $date_query['32-1001-01'] = collect([
                (object)[
                    'gls_gl_date' =>  null,
                    'gls_account_code' => '32-1001-01',
                    'gls_gl_document' => null,
                    'gls_account_name' => 'กำไร(ขาดทุน)สะสม',
                    'gls_debit' => 0,
                    'gls_credit' => 0,
                    'before_total' => $totalResult, // กำหนด before_total เป็น $totalResult
                ]
            ]);
        }

        $date_query = collect($date_query->map(function ($item) {
            return collect((array)$item);
        }));

        //  dd($date_query['32-1001-01'], $after_date_query);

        $date_query['32-1001-01'] = $date_query['32-1001-01']->merge($after_date_query);




        // เพิ่ม before_total ให้กับแต่ละรายการของ account code
        // เพิ่ม before_total ให้กับแต่ละรายการของ account code
        foreach ($date_query as $accountCode => $transactions) {
            foreach ($transactions as &$transaction) { // ใช้ reference (&) เพื่อให้ค่าเปลี่ยนแปลงจริง
                // แปลง array เป็น object ก่อนแก้ไขค่า
                $transaction = (object) $transaction;

                // ตั้งค่า before_total
                if ($accountCode === '32-1001-01') {
                    $transaction->before_total = $totalResult;
                } else {
                    $transaction->before_total = $combined_result->firstWhere('gls_account_code', $accountCode)->before_total ?? 0;
                }
            }
        }

        $dateQueries = $date_query->sortKeys();

        //  dd($dateQueries);

        return [
            'date_query' => $dateQueries,
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