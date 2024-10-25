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



    function calculateFiscalYear($accounting_period)
    {
        // วันที่ปัจจุบัน
        $currentDate = Carbon::now();
        $currentYear = $currentDate->format('Y');
        $currentMonth = $currentDate->format('m');
        $currentDay = $currentDate->format('d');

        // แยกวันและเดือนออกจาก accounting_period
        $accountingDate = Carbon::createFromFormat('j/n', $accounting_period);

        if ($accountingDate === false) {
            // ถ้าไม่สามารถแปลงวันที่ได้ ให้ส่งข้อความแจ้งเตือน
            return [
                'fiscalYearStart' => 'รูปแบบวันที่ไม่ถูกต้อง',
                'fiscalYearEnd' => 'รูปแบบวันที่ไม่ถูกต้อง'
            ];
        }

        // วันและเดือนของรอบบัญชี
        $accountingMonth = $accountingDate->format('m');
        $accountingDay = $accountingDate->format('d');

        // กำหนดปีเริ่มต้นและสิ้นสุดตามรอบบัญชี
        $accountingDateThisYear = (clone $accountingDate)->setDate(
            $currentYear,
            $accountingMonth,
            $accountingDay
        );

        if ($accountingDateThisYear < $currentDate) {
            // ถ้ารอบบัญชีน้อยกว่าวันปัจจุบัน (แปลว่าเป็นรอบปีที่แล้ว)
            $fiscalYearStart = (clone $accountingDateThisYear)->setDate(
                $currentYear,
                $accountingMonth,
                $accountingDay
            );
            $fiscalYearEnd = (clone $fiscalYearStart)
                ->modify('last day of this month')
                ->setDate($currentYear + 1, $accountingMonth, $accountingDay - 1);
        } else {
            // ถ้าวันบัญชีมากกว่าวันปัจจุบัน (แปลว่าเป็นรอบบัญชีปีนี้ถึงปีถัดไป)
            $fiscalYearStart = (clone $accountingDateThisYear)->setDate(
                $currentYear - 1,
                $accountingMonth,
                $accountingDay
            );
            $fiscalYearEnd = (clone $fiscalYearStart)
                ->modify('last day of this month')
                ->setDate($currentYear, $accountingMonth, $accountingDay - 1);
        }

        // แปลงเป็นรูปแบบที่ต้องการ
        $fiscalYearStartFormatted = $fiscalYearStart->format('d/m/Y');
        $fiscalYearEndFormatted = $fiscalYearEnd->format('d/m/Y');

        return [
            'fiscalYearStart' => $fiscalYearStartFormatted,
            'fiscalYearEnd' => $fiscalYearEndFormatted
        ];
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
        $endDate = $endDate ?? $startDate->copy()->addYear()->subDay();
        $endDate = $endDate->endOfDay();


        ///  dd($acc, $startDate, $endDate);
        $carryForwardDate =  $startDate->copy()->endOfDay()->subDay(); // Subtract 1 day from the carryForwardDate

        // ก่อน starts date
        $before_date_query = DB::table('general_ledger_subs') // GLS_Credit = 108995
            ->where('gls_code_company', $id)
            ->whereBetween(DB::raw('DATE(gls_gl_date)'), [$startPeriod->toDateString(), $carryForwardDate->toDateString()])
            ->where(function ($q) {
                $q->where('gls_account_code', 'like', '4%')
                    ->orWhere('gls_account_code', 'like', '5%');
            })
            ->select(
                'general_ledger_subs.*',
                DB::raw('SUM(gls_debit) as total_debit'),
                DB::raw('SUM(gls_credit) as total_credit'),
                DB::raw('SUM(gls_credit) - SUM(gls_debit) as quoted_net_balance')
            )
            ->groupBy('gls_account_code')
            ->orderBy('gls_account_code', 'ASC')
            ->get();

        // ตั้งเเต่ starts date 262,845.00	
        $after_query = DB::table('general_ledger_subs') //  GLS_Credit = 153850
            ->where('gls_code_company', $id)
            ->whereBetween(DB::raw('DATE(gls_gl_date)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->where(function ($q) {
                $q->where('gls_account_code', 'like', '4%')
                    ->orWhere('gls_account_code', 'like', '5%');
            })
            ->select(
                'general_ledger_subs.*',
                DB::raw('SUM(gls_debit) as total_debit'),
                DB::raw('SUM(gls_credit) as total_credit'),
                DB::raw('SUM(gls_credit) - SUM(gls_debit) as net_balance')
            )
            ->groupBy('gls_account_code')
            ->orderBy('gls_account_code', 'ASC')
            ->get();
        // dd($before_date_query, $after_query, $startPeriod, $carryForwardDate, $startDate, $endDate);

        $mergedData = $after_query->map(function ($item) use ($before_date_query) {
            // หาค่า quoted_net_balance จาก query22 ที่มี gls_account_code ตรงกัน
            $carryForward = $before_date_query->firstWhere('gls_account_code', $item->gls_account_code);

            // ถ้ามียอดยก quoted_net_balance เพิ่มเข้าไป
            if ($carryForward) {
                $item->quoted_net_balance = $carryForward->quoted_net_balance;
            } else {
                $item->quoted_net_balance = 0; // กรณีไม่มียอดยก
            }

            return $item;
        });





        return [
            'query' => $mergedData,
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



        return view('report.profit_statement.view', [
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


    public function search(Request $request)
    {

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $data = $this->getData($request->id, $startDate, $endDate);

        return view('report.profit_statement.view', [
            'query' => $data['query'],
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