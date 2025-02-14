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

class TrialBalanceBeforeClosingUserController extends Controller
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
            ->whereBetween(DB::raw('DATE(gls_gl_date)'), [$startPeriod2->toDateString(), $carryForwardDate->toDateString()])
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
        // รวมผลลัพธ์
        $totalResult = $query->first()->acc_total_32 + ($query->first()->acc_total_4 - $query->first()->acc_total_5);

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


    public function show(Request $request)
    {
        $id = $request->user_id;
        $data = $this->getData($id); // รับค่ากลับมา


        return view('report.trial_balance_before_closing.view', [
            'date_query' => $data['date_query'],
            'user' => $request,
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
        $id = $request->user_id;
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $data = $this->getData($id, $startDate, $endDate);

        return view('report.trial_balance_before_closing.view', [
            'date_query' => $data['date_query'],
            'user' => $request,
            'startDate' => $data['startDate'],
            'endDate' => $data['endDate'],
            'day' => $data['day'],
            'monthThai' => $data['monthThai'],
            'currentYear' => $data['currentYear'],
            'id' => $id
        ]);
    }

    public function exportPDF($id, $start_date, $end_date)
    {



        $data = $this->getData($id, $start_date, $end_date); // รับค่ากลับมา

        $pdf = PDF::loadView('report.trial_balance_before_closing.pdf_view', [
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



    public function exportExcel($id, $start_date, $end_date)
    {
        $data = $this->getData($id, $start_date, $end_date);

        // Process and organize data based on gls_account_code prefix
        // Process and organize data based on gls_account_code prefix

        $before_total_1 = $data['date_query']->filter(fn($item) => Str::startsWith($item->gls_account_code, '1'))->sum('before_total');
        $before_total_2 = $data['date_query']->filter(fn($item) => Str::startsWith($item->gls_account_code, '2'))->sum('before_total');

        $before_total_3 = $data['date_query']->filter(fn($item) => Str::startsWith($item->gls_account_code, '3'))->sum('before_total');
        $before_total_result_3 =  $data['date_query']->filter(fn($item) => $item->gls_account_code == '32-1001-01')->sum('before_total_result');

        $combined_result = $data['date_query']
            ->groupBy('gls_account_code')
            ->map(function ($items) use ($before_total_1, $before_total_2, $before_total_3) {
                return (object) [
                    'gls_account_code' => $items->first()->gls_account_code,
                    'gls_account_name' => $items->first()->gls_account_name,
                    'before_total' => $items->sum(fn($item) => $item->before_total ?? 0),
                    'before_total_result' =>  $before_total_1 - $before_total_3 - $before_total_2 ?? 0,
                    'after_total' => $items->sum(fn($item) => $item->after_total ?? 0),
                    'after_total_result' => $items->sum(fn($item) => $item->after_total_result ?? 0),
                    'total' => $items->sum(fn($item) => ($item->before_total ?? 0) + ($item->after_total ?? 0)),
                ];
            })
            ->values();

        $mappedData = collect();

        // Add sections and calculate totals for each account group
        $this->addGroupToExcel($mappedData, $combined_result, '1', 'สินทรัพย์');
        $this->addGroupToExcel($mappedData, $combined_result, '2', 'หนี้สิน');
        $this->addGroupToExcel($mappedData, $combined_result, '3', 'ส่วนของผู้ถือหุ้น/ผู้เป็นหุ้นส่วน');
        $this->addGroupToExcel($mappedData, $combined_result, '4', 'รายได้จากการดำเนินงาน');
        $this->addGroupToExcel($mappedData, $combined_result, '5', 'ค่าใช้จ่ายในการขายและบริหาร');

        // Calculate overall totals for summary rows

        $before_total_4 = $combined_result->filter(fn($item) => Str::startsWith($item->gls_account_code, '4'))->sum('before_total');
        $before_total_5 = $combined_result->filter(fn($item) => Str::startsWith($item->gls_account_code, '5'))->sum('before_total');

        $after_total_1 = $combined_result->filter(fn($item) => Str::startsWith($item->gls_account_code, '1'))->sum('after_total');
        $after_total_2 = $combined_result->filter(fn($item) => Str::startsWith($item->gls_account_code, '2'))->sum('after_total');
        $after_total_3 = $combined_result->filter(fn($item) => Str::startsWith($item->gls_account_code, '3'))->sum('after_total');
        $after_total_result_3 = $combined_result->filter(fn($item) => $item->gls_account_code == '32-1001-01')->sum('after_total_result');
        $after_total_4 = $combined_result->filter(fn($item) => Str::startsWith($item->gls_account_code, '4'))->sum('after_total');
        $after_total_5 = $combined_result->filter(fn($item) => Str::startsWith($item->gls_account_code, '5'))->sum('after_total');

        $total_1 = $combined_result->filter(fn($item) => Str::startsWith($item->gls_account_code, '1'))->sum('total');
        $total_2 = $combined_result->filter(fn($item) => Str::startsWith($item->gls_account_code, '2'))->sum('total');
        $total_3 = $combined_result->filter(fn($item) => Str::startsWith($item->gls_account_code, '3'))->sum('total');
        $total_4 = $combined_result->filter(fn($item) => Str::startsWith($item->gls_account_code, '4'))->sum('total');
        $total_5 = $combined_result->filter(fn($item) => Str::startsWith($item->gls_account_code, '5'))->sum('total');
        // dd(number_format($after_total_2 + $after_total_3 + $after_total_4 + $before_total_2 + $before_total_3 + $before_total_4, 2));
        // Add "ยอดรวมกำไร(ขาดทุน)สุทธิของงวดนี้" row

        $mappedData->push([
            '',
            'ยอดรวมกำไร(ขาดทุน)สุทธิของงวดนี้',
            '',
            number_format($before_total_4 - $before_total_5, 2), // Debit and Credit for 'ยอดสะสมต้นงวด'
            '',
            number_format($after_total_4 - $after_total_5, 2), // Debit and Credit for 'ยอดสะสมงวดนี้'
            '',
            number_format($total_4 - $total_5, 2) // Debit and Credit for 'ยอดสะสมยกไป'
        ]);

        // Calculate accumulated profit/loss "กำไร(ขาดทุน)สะสมยกไป"
        $accumulated_profit_loss_before = ($before_total_4 - $before_total_5 + $before_total_result_3);
        $accumulated_profit_loss_after = ($after_total_4 - $after_total_5 + $after_total_result_3);
        $accumulated_total_profit_loss = $accumulated_profit_loss_before + $accumulated_profit_loss_after;

        // Add "กำไร(ขาดทุน)สะสมยกไป" row
        $mappedData->push([
            '',
            'กำไร(ขาดทุน)สะสมยกไป',
            '',
            number_format($accumulated_profit_loss_before, 2), // Debit and Credit for 'ยอดสะสมต้นงวด'
            '',
            number_format($accumulated_profit_loss_after, 2), // Debit and Credit for 'ยอดสะสมงวดนี้'
            '',
            number_format($accumulated_total_profit_loss, 2) // Debit and Credit for 'ยอดสะสมยกไป'
        ]);

        // Add final cumulative total row

        $toatalSum_6_1 =
            $total_2 +
            $before_total_1 -
            $before_total_2 -
            $before_total_3 +
            $total_3 +
            $total_4 +
            $after_total_result_3;
        $toatalSum_6_2 = $before_total_4 - $before_total_5;

        $cumulativeTotal = $toatalSum_6_1 - $toatalSum_6_2;

        $mappedData->push([
            '',
            'ยอดรวมทั้งหมด',
            number_format($before_total_1 + $before_total_5, 2),
            number_format($before_total_2 +
                $before_total_3 +
                $before_total_1 -
                $before_total_2 -
                $before_total_3 +
                $before_total_5, 2),
            number_format($after_total_1 + $after_total_5, 2),
            number_format($after_total_2 + $after_total_3 + $after_total_4 + $after_total_result_3, 2),
            number_format($total_1 + $total_5, 2),
            number_format($cumulativeTotal, 2),

        ]);

        /*  $toatalSum_1 = $before_total_1 + $before_total_5;
        $toatalSum_2 =
            $before_total_2 +
            $before_total_3 +
            $before_total_1 -
            $before_total_2 -
            $before_total_3 +
            $before_total_5;
        $toatalSum_3 = $after_total_1 + $after_total_5;
        $toatalSum_4 = $after_total_2 + $after_total_3 + $after_total_4 + $after_total_result_3;
        $toatalSum_5 = $total_1 + $total_5;
        $toatalSum_6_1 =
            $total_2 +
            $before_total_1 -
            $before_total_2 -
            $before_total_3 +
            $total_3 +
            $total_4 +
            $after_total_result_3;
        $toatalSum_6_2 = $before_total_4 - $before_total_5;
        $toatalSum_6 = $toatalSum_6_1 - $toatalSum_6_2; */
        // Export to Excel
        $export = new class($mappedData) implements FromArray, WithHeadings, WithColumnWidths, WithStyles {
            protected $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data->values()->toArray();
            }

            public function headings(): array
            {
                return [
                    ['รหัสบัญชี', 'ชื่อบัญชี', 'ยอดสะสมต้นงวด', '', 'ยอดสะสมงวดนี้', '', 'ยอดสะสมยกไป', ''],
                    ['', '', 'เดบิต', 'เครดิต', 'เดบิต', 'เครดิต', 'เดบิต', 'เครดิต']
                ];
            }

            public function columnWidths(): array
            {
                return [
                    'A' => 20,
                    'B' => 40,
                    'C' => 15,
                    'D' => 15,
                    'E' => 15,
                    'F' => 15,
                    'G' => 20,
                    'H' => 20,
                ];
            }

            public function styles(Worksheet $sheet)
            {
                $sheet->getStyle('A1:H2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1:H2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A1:H2')->getFont()->setBold(true);
                $sheet->mergeCells('A1:A2'); // รหัสบัญชี
                $sheet->mergeCells('B1:B2'); // ชื่อบัญชี
                $sheet->mergeCells('C1:D1'); // ยอดสะสมต้นงวด
                $sheet->mergeCells('E1:F1'); // ยอดสะสมงวดนี้
                $sheet->mergeCells('G1:H1'); // ยอดสะสมยกไป
                $sheet->setAutoFilter('A2:H' . ($this->data->count() + 2));
                $sheet->getStyle('C3:H' . ($this->data->count() + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
        };

        return Excel::download($export, 'trialBalanceBeforeClosing.xlsx');
    }

    private function addGroupToExcel(&$mappedData, $combined_result, $prefix, $title)
    {
        // Filter entries by account prefix and calculate group totals
        $groupData = $combined_result->filter(fn($item) => Str::startsWith($item->gls_account_code, $prefix));
        $before_total_result_3 = $combined_result->filter(fn($item) => $item->gls_account_code == '32-1001-01')->sum('before_total_result');
        $after_total_result_3 = $combined_result->filter(fn($item) => $item->gls_account_code == '32-1001-01')->sum('after_total_result');

        $before_total = $groupData->sum('before_total');
        $after_total = $groupData->sum('after_total');
        $total = $groupData->sum('total');
        /*   dd($total); */
        // Add header for each group
        $mappedData->push(['', $title, '', '', '', '', '', '']);


        $displayed_before_total = 0;
        $displayed_after_total = 0;
        // Add rows for each entry in the group

        /* foreach ($groupData as $entry) {

            $displayed_before  = ($entry->gls_account_code == '32-1001-01') ? $before_total_result_3 : $entry->before_total;
            $displayed_after  = ($entry->gls_account_code == '32-1001-01') ? $after_total_result_3 : $entry->after_total;
            $displayed_before_total  += $displayed_before;
            $displayed_after_total  += $displayed_after;

            $mappedData->push([
                $entry->gls_account_code,
                $entry->gls_account_name,
                (Str::startsWith($entry->gls_account_code, '1') || Str::startsWith($entry->gls_account_code, '5')) ? number_format($displayed_before, 2) : '', // แสดงเฉพาะเมื่อขึ้นต้นด้วย 1 หรือ 5
                (Str::startsWith($entry->gls_account_code, '2') || Str::startsWith($entry->gls_account_code, '3') || Str::startsWith($entry->gls_account_code, '4')) ? number_format($displayed_before, 2) : '', // Debit and Credit for 'ยอดสะสมต้นงวด'
                (Str::startsWith($entry->gls_account_code, '1') || Str::startsWith($entry->gls_account_code, '5')) ? number_format($displayed_after, 2) : '', // แสดงเฉพาะเมื่อขึ้นต้นด้วย 1 หรือ 5
                (Str::startsWith($entry->gls_account_code, '2') || Str::startsWith($entry->gls_account_code, '3') || Str::startsWith($entry->gls_account_code, '4')) ? number_format($displayed_after, 2) : '', // Debit an
                (Str::startsWith($entry->gls_account_code, '1') || Str::startsWith($entry->gls_account_code, '5')) ? number_format($displayed_before + $displayed_after, 2) : '', // แสดงเฉพาะเมื่อขึ้นต้นด้วย 1 หรือ 5
                (Str::startsWith($entry->gls_account_code, '2') || Str::startsWith($entry->gls_account_code, '3') || Str::startsWith($entry->gls_account_code, '4')) ? number_format($displayed_before + $displayed_after, 2) : '',  // Debit and Credit for 'ยอดสะสมยกไป'
            ]);
        }

        // Add subtotal row for each group
        $mappedData->push([
            '',
            "รวม $title",
            (Str::startsWith($entry->gls_account_code, '1') || Str::startsWith($entry->gls_account_code, '5')) ? number_format($displayed_before_total, 2) : '', // แสดงเฉพาะเมื่อขึ้นต้นด้วย 1 หรือ 5
            (Str::startsWith($entry->gls_account_code, '2') || Str::startsWith($entry->gls_account_code, '3') || Str::startsWith($entry->gls_account_code, '4')) ? number_format($displayed_before_total, 2) : '',
            (Str::startsWith($entry->gls_account_code, '1') || Str::startsWith($entry->gls_account_code, '5')) ? number_format($displayed_after_total, 2) : '', // แสดงเฉพาะเมื่อขึ้นต้นด้วย 1 หรือ 5
            (Str::startsWith($entry->gls_account_code, '2') || Str::startsWith($entry->gls_account_code, '3') || Str::startsWith($entry->gls_account_code, '4')) ? number_format($displayed_after_total, 2) : '',
            (Str::startsWith($entry->gls_account_code, '1') || Str::startsWith($entry->gls_account_code, '5')) ? number_format($displayed_after_total + $displayed_before_total, 2) : '', // แสดงเฉพาะเมื่อขึ้นต้นด้วย 1 หรือ 5
            (Str::startsWith($entry->gls_account_code, '2') || Str::startsWith($entry->gls_account_code, '3') || Str::startsWith($entry->gls_account_code, '4')) ? number_format($displayed_after_total + $displayed_before_total, 2) : '',
        ]); */

        if ($groupData->isNotEmpty()) {
            foreach ($groupData as $entry) {
                $displayed_before = ($entry->gls_account_code == '32-1001-01') ? $before_total_result_3 : $entry->before_total;
                $displayed_after = ($entry->gls_account_code == '32-1001-01') ? $after_total_result_3 : $entry->after_total;

                $displayed_before_total += $displayed_before;
                $displayed_after_total += $displayed_after;

                $mappedData->push([
                    $entry->gls_account_code,
                    $entry->gls_account_name,
                    (Str::startsWith($entry->gls_account_code, '1') || Str::startsWith($entry->gls_account_code, '5')) ? number_format($displayed_before, 2) : '',
                    (Str::startsWith($entry->gls_account_code, '2') || Str::startsWith($entry->gls_account_code, '3') || Str::startsWith($entry->gls_account_code, '4')) ? number_format($displayed_before, 2) : '',
                    (Str::startsWith($entry->gls_account_code, '1') || Str::startsWith($entry->gls_account_code, '5')) ? number_format($displayed_after, 2) : '',
                    (Str::startsWith($entry->gls_account_code, '2') || Str::startsWith($entry->gls_account_code, '3') || Str::startsWith($entry->gls_account_code, '4')) ? number_format($displayed_after, 2) : '',
                    (Str::startsWith($entry->gls_account_code, '1') || Str::startsWith($entry->gls_account_code, '5')) ? number_format($displayed_before + $displayed_after, 2) : '',
                    (Str::startsWith($entry->gls_account_code, '2') || Str::startsWith($entry->gls_account_code, '3') || Str::startsWith($entry->gls_account_code, '4')) ? number_format($displayed_before + $displayed_after, 2) : '',
                ]);
            }
        }

        // Add subtotal row for each group
        $mappedData->push([
            '',
            "รวม $title",
            number_format($displayed_before_total, 2),
            number_format($displayed_before_total, 2),
            number_format($displayed_after_total, 2),
            number_format($displayed_after_total, 2),
            number_format($displayed_after_total + $displayed_before_total, 2),
            number_format($displayed_after_total + $displayed_before_total, 2),
        ]);
    }
}