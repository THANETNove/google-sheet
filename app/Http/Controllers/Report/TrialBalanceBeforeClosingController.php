<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TrialBalanceBeforeClosingController extends Controller
{

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
        $endDate = Carbon::parse($endDate)->endOfDay();

        $startDate = Carbon::parse($startDate); // Convert startDate to a Carbon instance
        $carryForwardDate = $startDate->copy()->endOfDay()->subDay(); // Now you can call copy(), endOfDay(), and subDay() on it


        // ก่อน start date
        $before_date_query = DB::table('general_ledger_subs')
            ->where('gls_code_company', $id)
            ->whereBetween(DB::raw('DATE(gls_gl_date)'), [$startPeriod->toDateString(), $carryForwardDate->toDateString()])
            ->where(function ($q) {
                $q->where('gls_account_code', 'like', '1%')
                    ->orWhere('gls_account_code', 'like', '2%')
                    ->orWhere('gls_account_code', 'like', '3%')
                    ->orWhere('gls_account_code', 'like', '4%')
                    ->orWhere('gls_account_code', 'like', '5%');
            })
            ->select(
                'gls_account_code',
                'gls_account_name',
                'gls_gl_date',
                DB::raw("CASE 
                WHEN gls_account_code LIKE '1%' THEN SUM(gls_debit - gls_credit)
                WHEN gls_account_code LIKE '2%' THEN SUM(gls_debit - gls_credit)
                WHEN gls_account_code LIKE '3%' THEN SUM(gls_credit - gls_debit)
                WHEN gls_account_code LIKE '4%' THEN SUM(gls_credit - gls_debit)
                WHEN gls_account_code LIKE '5%' THEN SUM(gls_debit - gls_credit)
                ELSE 0
             END as before_total")
            )
            ->groupBy('gls_account_code')
            ->get();
        // หลัง start date
        $after_query = DB::table('general_ledger_subs')
            ->where('gls_code_company', $id)
            ->whereBetween(DB::raw('DATE(gls_gl_date)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->where(function ($q) {
                $q->where('gls_account_code', 'like', '1%')
                    ->orWhere('gls_account_code', 'like', '2%')
                    ->orWhere('gls_account_code', 'like', '3%')
                    ->orWhere('gls_account_code', 'like', '4%')
                    ->orWhere('gls_account_code', 'like', '5%');
            })
            ->select(
                'gls_account_code',
                'gls_account_name',
                'gls_gl_date',
                DB::raw("CASE 
                WHEN gls_account_code LIKE '1%' THEN SUM(gls_debit - gls_credit)
                WHEN gls_account_code LIKE '2%' THEN SUM(gls_debit - gls_credit)
                WHEN gls_account_code LIKE '3%' THEN SUM(gls_credit - gls_debit)
                WHEN gls_account_code LIKE '4%' THEN SUM(gls_credit - gls_debit)
                WHEN gls_account_code LIKE '5%' THEN SUM(gls_debit - gls_credit)
                ELSE 0
             END as after_total")
            )
            ->groupBy('gls_account_code')
            ->get();

        // รวม array เข้าด้วยกัน
        $combined_query = $before_date_query->merge($after_query);

        // จัดกลุ่มตาม gls_account_code และรวมยอด
        $combined_result = $combined_query
            ->groupBy('gls_account_code')
            ->map(function ($items) {
                return (object) [
                    'gls_account_code' => $items->first()->gls_account_code,
                    'gls_account_name' => $items->first()->gls_account_name,
                    'gls_gl_date' => $items->first()->gls_gl_date,
                    'after_total_credit' => $items->first()->after_total_credit ?? 0,
                    'before_total' => $items->sum(fn($item) => $item->before_total ?? 0),
                    'after_total' => $items->sum(fn($item) => $item->after_total ?? 0),
                    'total' => $items->sum(fn($item) => ($item->before_total ?? 0) + ($item->after_total ?? 0))
                ];
            })
            ->values(); // รีเซ็ต key ของ Collection ให้เป็นตัวเลขเรียงลำดับ

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



        return view('report.trialBalanceBeforeClosing.view', [
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

        return view('report.trialBalanceBeforeClosing.view', [
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