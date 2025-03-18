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

    private function getData($id, $startDate = null, $endDate = null, $startCode = null, $endCode = null, $search = 'no')
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

        // Debug ค่า
        // Debug ค่า
        $year = $startDate ? Carbon::parse($startDate)->year : Carbon::now()->year;

        // กำหนดวันที่เริ่มต้นเป็น 1 มกราคมของปีที่ได้มา
        $startDate45 = Carbon::createFromDate($year, $month, $day)->toDateString();

        // กำหนด `$endDate45` เป็นวันสุดท้ายของเดือนที่แล้วของ `$startDate`
        $endDate45 = $startDate->copy()->subMonth()->endOfMonth()->toDateString();

        // Debug ค่า
        // Debug ค่า
        if ($search == 'no' && ((int)$day != 1 || (int)$month != 1)) {


            $startDate = $startPeriod2;
            $endDate = Carbon::createFromDate($year, $month - 1, 1)->endOfMonth();
        }


        $query = DB::table('general_ledger_subs')
            ->where('gls_code_company', $id);

        if ($startCode && $endCode) {
            $query->whereBetween('gls_account_code', [$startCode, $endCode]);
        }


        $before_date_query = $query->clone()
            ->whereDate('gls_gl_date', '<=', $carryForwardDate->toDateString())
            //->whereBetween(DB::raw('DATE(gls_gl_date)'),  [$startPeriod->toDateString(), $carryForwardDate->toDateString()])
            ->where(function ($q) {
                $q->where('gls_account_code', 'like', '1%')
                    ->orWhere('gls_account_code', 'like', '2%')
                    ->orWhere('gls_account_code', 'like', '3%');
            })
            ->selectRaw("
                gls_gl_code,
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
        // dd($before_date_query);

        $before_date_query_2 = $query->clone()
            ->whereBetween(DB::raw('DATE(gls_gl_date)'),  [$startPeriod->toDateString(), $carryForwardDate->toDateString()])

            ->where(function ($q) {
                $q->where('gls_account_code', 'like', '4%')
                    ->orWhere('gls_account_code', 'like', '5%');
            })
            ->selectRaw("
            gls_gl_code,
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


        //dd($combined_result);


        $after_date_query = $query->clone()
            ->whereBetween(DB::raw('DATE(gls_gl_date)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->where(function ($q) {
                $q->where('gls_account_code', 'like', '4%')
                    ->orWhere('gls_account_code', 'like', '5%');
            })
            ->selectRaw("
            gls_gl_code,
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
                'gls_gl_code' => $beforeItem->gls_gl_code,
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
            gls_gl_code,
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
            gls_gl_code,
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

        $existingAccountCodes2 = $date_query2->keys()->merge($date_query2->keys())->unique();

        // กรอง gls_account_code ใน $date_query3 ที่ไม่มีใน $date_query1 และ $date_query2
        $date_query3 = $query->clone()
            //->whereDate('gls_gl_date', '<=', $carryForwardDate->toDateString())
            ->whereBetween(DB::raw('DATE(gls_gl_date)'),  [$startPeriod->toDateString(), $carryForwardDate->toDateString()])
            ->whereNotIn('gls_account_code', $existingAccountCodes2) // ตรวจสอบว่ารหัสนี้ไม่มีใน $date_query1 และ $date_query2
            ->selectRaw("
            gls_gl_code,
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

        $existingAccountCodes3 = $date_query1->keys()->merge($date_query2->keys())->merge($date_query3->keys())->unique();

        $date_query_code = $query->clone() // เอาเเค่หัว
            ->whereDate('gls_gl_date', '<=', $carryForwardDate->toDateString())
            ->whereNotIn('gls_account_code', $existingAccountCodes3) // ตรวจสอบว่ารหัสนี้ไม่มีใน $date_query1 และ $date_query2
            ->selectRaw("
                gls_gl_code,
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

        $date_query = $date_query1->merge($date_query2)->merge($date_query3)->merge($date_query_code);


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


        $date_query['32-1001-01'] = $date_query['32-1001-01']->merge($after_date_query);

        // เพิ่ม before_total ให้กับแต่ละรายการของ account code
        foreach ($date_query as $accountCode => $transactions) {
            foreach ($transactions as $transaction) {
                $transaction->gls_gl_code = $transaction->gls_gl_code ?? null;
                // ตั้งค่า before_total สำหรับ 32-1001-01
                if ($accountCode === '32-1001-01') {
                    $transaction->before_total = $totalResult;
                } else {
                    // สำหรับหมวดอื่นให้ตั้งค่า before_total เป็นค่า default
                    $transaction->before_total = $combined_result->firstWhere('gls_account_code', $accountCode)->before_total ?? 0;
                }
            }
        }
        $dateQueries = $date_query->sortKeys();
        // ดึง gls_gl_code ทั้งหมดที่ต้องใช้


        $glCodes = DB::table('general_ledger_subs')
            ->where('gls_code_company', $id)
            ->pluck('gls_gl_code')
            ->toArray();


        // ดึงข้อมูล Ledger ทั้งหมดในคำสั่งเดียว
        $ledgers = DB::table('general_ledgers')
            ->whereIn('gl_code', $glCodes)
            ->select('gl_code', 'gl_url', 'gl_page', 'gl_document', 'gl_description', 'gl_company')
            ->get()
            ->keyBy('gl_code'); // แปลงเป็น array เพื่อเรียกใช้เร็วขึ้น


        return [
            'date_query' => $dateQueries,
            'ledgers' => $ledgers,
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
            'ledgers' => $data['ledgers'],
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
        $startCode = $request->start_code;
        $endCode = $request->end_code;

        $search = "yes";
        $data = $this->getData($request->id, $startDate, $endDate, $startCode, $endCode, $search);



        return view('report.ledger.view', [
            'startCode' => $startCode,
            'endCode' => $endCode,
            'date_query' => $data['date_query'],
            'ledgers' => $data['ledgers'],
            'user' => $data['user'],
            'startDate' => $data['startDate'],
            'endDate' => $data['endDate'],
            'day' => $data['day'],
            'monthThai' => $data['monthThai'],
            'currentYear' => $data['currentYear'],
            'id' => $request->id
        ]);
    }


    public static function getGlUrl($query)
    {


        // ดึงรหัส gl_code ทั้งหมดจาก query
        $glCodes = $query->pluck('gls_gl_code')->toArray();

        // ดึงข้อมูล Ledger ทั้งหมดที่เกี่ยวข้องในครั้งเดียว
        $ledger = DB::table('general_ledgers')
            ->whereIn('gl_code', $glCodes) // ดึงข้อมูลที่เกี่ยวข้องทั้งหมด
            ->select('gl_code', 'gl_url', 'gl_page', 'gl_document', 'gl_description', 'gl_company')
            ->get()
            ->keyBy('gl_code'); // แปลงเป็น associative array ตาม gl_code


        return $ledger;
    }
}
