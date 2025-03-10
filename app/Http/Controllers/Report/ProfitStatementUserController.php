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

class ProfitStatementUserController extends Controller
{
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

    private function getData($id, $startDate = null, $endDate = null, $search = 'no')
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


        // ตรวจสอบว่ามีค่า $startDate และ $endDate หรือไม่
        $year = $startDate ? Carbon::parse($startDate)->year : Carbon::now()->year;

        // กำหนดวันที่เริ่มต้นเป็น 1 มกราคมของปีที่ได้มา
        $startDate45 = Carbon::createFromDate($year, $month, $day)->toDateString();

        // กำหนด `$endDate45` เป็นวันสุดท้ายของเดือนที่แล้วของ `$startDate`
        $endDate45 = $startDate->copy()->subMonth()->endOfMonth()->toDateString();
        if ($search == 'no' && ((int)$day != 1 || (int)$month != 1)) {
            $startDate = $startPeriod2;
            $endDate = Carbon::createFromDate($year, $month - 1, 1)->endOfMonth();
        }

        // ก่อน start date
        $before_date_query = DB::table('general_ledger_subs')
            ->where('gls_code_company', $id)
            ->whereBetween(DB::raw('DATE(gls_gl_date)'), [$startDate45, $endDate45])
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
            ->values() // รีเซ็ต key ของ Collection ให้เป็นตัวเลขเรียงลำดับ
            ->sortBy('gls_account_code');


        // ตรวจสอบผลลัพธ์
        //dd($combined_result, $before_date_query, $after_query);






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


        return view('report.profit_statement.view', [
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
        $search = 'yes';
        $data = $this->getData($request->id, $startDate, $endDate, $search);

        return view('report.profit_statement.view', [
            'date_query' => $data['date_query'],
            'user' => $request,
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
        $pdf = PDF::loadView('report.profit_statement.pdf_view', [
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

        // สร้างข้อมูลที่จัดกลุ่มและคำนวณค่า
        $combined_result = $data['date_query']
            ->groupBy('gls_account_code')
            ->map(function ($items) {
                return (object) [
                    'gls_account_code' => $items->first()->gls_account_code,
                    'gls_account_name' => $items->first()->gls_account_name,
                    'before_total' => $items->sum(fn($item) => $item->before_total ?? 0),
                    'after_total' => $items->sum(fn($item) => $item->after_total ?? 0),
                    'total' => $items->sum(fn($item) => ($item->before_total ?? 0) + ($item->after_total ?? 0)),
                ];
            })
            ->values();

        // แบ่งข้อมูลเป็นกลุ่มรายได้และค่าใช้จ่าย
        $group4Data = $combined_result->filter(fn($item) => Str::startsWith($item->gls_account_code, '4'));
        $group5Data = $combined_result->filter(fn($item) => Str::startsWith($item->gls_account_code, '5'));

        // คำนวณยอดรวมสำหรับแต่ละกลุ่ม
        $before_total_4 = $group4Data->sum('before_total');
        $after_total_4 = $group4Data->sum('after_total');
        $total_4 = $group4Data->sum('total');

        $before_total_5 = $group5Data->sum('before_total');
        $after_total_5 = $group5Data->sum('after_total');
        $total_5 = $group5Data->sum('total');

        // สร้างข้อมูลแบบ Excel
        $mappedData = collect();

        // รายได้จากการดำเนินงาน
        $mappedData->push(['', 'รายได้จากการดำเนินงาน', '', '', '', '', '', '']);
        foreach ($group4Data as $entry) {
            $mappedData->push([
                $entry->gls_account_code,
                $entry->gls_account_name,
                '', // Initial Debit
                number_format($entry->before_total, 2),
                '', // Current Debit
                number_format($entry->after_total, 2),
                '', // Cumulative Debit
                number_format($entry->total, 2)
            ]);
        }
        $mappedData->push([
            '',
            'รวมรายได้จากการดำเนินงาน',
            '',
            number_format($before_total_4, 2),
            '',
            number_format($after_total_4, 2),
            '',
            number_format($total_4, 2)
        ]);

        // ค่าใช้จ่ายในการขายและบริหาร
        $mappedData->push(['', 'ค่าใช้จ่ายในการขายและบริหาร', '', '', '', '', '', '']);
        foreach ($group5Data as $entry) {
            $mappedData->push([
                $entry->gls_account_code,
                $entry->gls_account_name,
                number_format($entry->before_total, 2), // Initial Debit
                '',
                number_format($entry->after_total, 2), // Current Debit
                '',
                number_format($entry->total, 2), // Cumulative Debit
                ''
            ]);
        }
        $mappedData->push([
            '',
            'รวมค่าใช้จ่ายในการขายและบริหาร',
            number_format($before_total_5, 2),
            '',
            number_format($after_total_5, 2),
            '',
            number_format($total_5, 2),
            ''
        ]);

        // ยอดรวมกำไร(ขาดทุน)สุทธิของงวดนี้
        $mappedData->push([
            '',
            'ยอดรวมกำไร(ขาดทุน)สุทธิของงวดนี้',
            '',
            number_format($before_total_4 - $before_total_5, 2),
            '',
            number_format($after_total_4 - $after_total_5, 2),
            '',
            number_format($total_4 - $total_5, 2)
        ]);

        // การ Export ข้อมูลไปยัง Excel
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
                    [
                        'รหัสบัญชี',
                        'ชื่อบัญชี',
                        'ยอดสะสมต้นงวด',
                        '',
                        'ยอดสะสมงวดนี้',
                        '',
                        'ยอดสะสมยกไป',
                        ''
                    ],
                    [
                        '',
                        '',
                        'เดบิต',
                        'เครดิต',
                        'เดบิต',
                        'เครดิต',
                        'เดบิต',
                        'เครดิต'
                    ]
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
                // ตั้งค่าการจัดตรงกลางสำหรับหัวข้อ
                $sheet->getStyle('A1:H2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1:H2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A1:H2')->getFont()->setBold(true);

                // รวมเซลล์สำหรับหัวข้อหลัก
                $sheet->mergeCells('A1:A2'); // รหัสบัญชี
                $sheet->mergeCells('B1:B2'); // ชื่อบัญชี
                $sheet->mergeCells('C1:D1'); // ยอดสะสมต้นงวด
                $sheet->mergeCells('E1:F1'); // ยอดสะสมงวดนี้
                $sheet->mergeCells('G1:H1'); // ยอดสะสมยกไป

                // ตั้งค่า Auto Filter
                $sheet->setAutoFilter('A2:H' . ($this->data->count() + 2));

                // การจัดตำแหน่งตัวเลข
                $sheet->getStyle('C3:H' . ($this->data->count() + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }

            public function registerEvents(): array
            {
                return [
                    AfterSheet::class => function (AfterSheet $event) {
                        // การจัดขอบเขตของตารางหัวข้อ
                        $event->sheet->getStyle('A1:H2')->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                ],
                            ],
                        ]);
                    },
                ];
            }
        };

        return Excel::download($export, 'profit_statement.xlsx');
    }
}
