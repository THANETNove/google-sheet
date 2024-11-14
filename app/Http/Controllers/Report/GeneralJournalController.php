<?php

namespace App\Http\Controllers\Report;

use Illuminate\Support\Facades\Cache;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DataGeneralLedgerSub;
use App\Models\GeneralLedger;
use Carbon\Carbon;
use PDF;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class GeneralJournalController extends Controller
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

    private function getDataGlAndGls($id, $startDate = null, $endDate = null)
    {
        $user = DB::table('users')->find($id);

        $accounting_period = $user->accounting_period;
        list($day, $month) = explode('/', $accounting_period);
        $startDate = $startDate ?? Carbon::createFromDate(date('Y'), $month, $day);
        $endDate = $endDate ?? $startDate->copy()->addYear()->subDay();
        $endDate = Carbon::parse($endDate)->endOfDay();


        // Join the two tables (general_ledgers and general_ledger_subs) in one query

        // ใช้ Eager Loading เพื่อลดจำนวนการคิวรี
        $generalLedgers = DataGeneralLedgerSub::with('subs')
            ->where('gl_code_company', $id)
            ->whereBetween('gl_date', [$startDate, $endDate])
            ->get()
            ->sortBy('gls_account_code'); // เรียงลำดับหลังจากดึงข้อมูล




        // แสดงผล
        // dd($generalLedgers);




        session(['generalLedgers' => [
            'query' => $generalLedgers,
            'user' => $user,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'day' => $day,
            'monthThai' => $this->getMonths()[$month] ?? 'เดือนไม่ถูกต้อง',
            'currentYear' => date('Y')
        ]]);
        // Group by document

        return [
            'query' => $generalLedgers,
            'user' => $user,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'day' => $day,
            'monthThai' => $this->getMonths()[$month] ?? 'เดือนไม่ถูกต้อง',
            'currentYear' => date('Y')
        ];
    }

    public function index()
    {


        $query = DB::table('users')
            ->where('status', 0)
            ->get();



        return view('report.general_journal.index', compact('query'));
    }

    public function show(string $id)
    {
        $data = $this->getDataGlAndGls($id); // รับค่ากลับมา



        return view('report.general_journal.view', [
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
        $data = $this->getDataGlAndGls($request->id, $startDate, $endDate);

        return view('report.general_journal.view', [
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

    public function exportPDF($id, $start_date, $end_date)
    {


        set_time_limit(600); // เพิ่มเวลาในการทำงาน
        ini_set('memory_limit', '4096M');



        $data = session()->get('generalLedgers');

        $chunks = collect($data['query'])->chunk(50); // แบ่งข้อมูลเป็นชุดละ 100 รายการ

        $pdf = PDF::loadView('report.general_journal.pdf_view', [
            'chunks' => $chunks, // ส่ง chunks ของข้อมูล
            'user' => $data['user'],
            'startDate' => $data['startDate'],
            'endDate' => $data['endDate'],
            'day' => $data['day'],
            'monthThai' => $data['monthThai'],
            'currentYear' => $data['currentYear'],
        ]);
        $pdf->setPaper('a4', 'portrait')
            ->setOption('margin-top', 15)
            ->setOption('margin-bottom', 15)
            ->setOption('isHtml5ParserEnabled', false)  // ปิด HTML5 parser
            ->setOption('isPhpEnabled', false);
        return $pdf->stream(); // โหลดไฟล์ PDF
    }


    public function exportExcel($id, $start_date, $end_date)
    {

        $data = collect(session()->get('generalLedgers'));
        $query =  $data['query'];
        // ตรวจสอบว่า $query มีข้อมูลหรือไม่
        if ($query->isEmpty()) {
            return back()->with('error', 'ไม่มีข้อมูลสำหรับการส่งออก');
        }

        $data = collect();
        $i = 1;

        // จัดเตรียมข้อมูล
        foreach ($query as $ledger) {
            if (!isset($ledger->gl_date, $ledger->gl_document, $ledger->gl_company, $ledger->gl_description, $ledger->subs)) {
                continue; // ข้ามถ้าข้อมูลไม่ครบ
            }

            $totalDebit = 0;
            $totalCredit = 0;

            // ข้อมูลแถวหลักของแต่ละเอกสาร
            $data->push([
                $i++, // คอลัมน์ A
                date('d-m-Y', strtotime($ledger->gl_date)), // คอลัมน์ B
                $ledger->gl_document, // คอลัมน์ C
                $ledger->gl_company . ' - ' . $ledger->gl_description, // คอลัมน์ D
                '', // Placeholder for Debit (คอลัมน์ E)
                '', // Placeholder for Credit (คอลัมน์ F)
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '' // Placeholder for G-Z
            ]);

            // เพิ่มข้อมูล subs ที่เกี่ยวข้อง
            foreach ($ledger->subs->sortBy('gls_account_code') as $sub) {
                if (!isset($sub->gls_account_code, $sub->gls_account_name, $sub->gls_debit, $sub->gls_credit)) {
                    continue; // ข้ามถ้าข้อมูลไม่ครบ
                }

                $data->push([
                    '', // Placeholder for #
                    '', // Placeholder for Date
                    '', // Placeholder for Document Number
                    "{$sub->gls_account_code} {$sub->gls_account_name}", // Company - Description
                    number_format($sub->gls_debit, 2), // Debit
                    number_format($sub->gls_credit, 2), // Credit
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '' // Placeholder for G-Z
                ]);

                $totalDebit += $sub->gls_debit;
                $totalCredit += $sub->gls_credit;
            }

            // สรุปผลรวมสำหรับแต่ละ ledger
            $isEqual = number_format($totalDebit, 2) == number_format($totalCredit, 2);
            $data->push([
                '',
                '',
                '',
                'รวม', // รวมข้อความในคอลัมน์ D
                number_format($totalDebit, 2), // คอลัมน์ E
                number_format($totalCredit, 2), // คอลัมน์ F
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '' // Placeholder for G-Z
            ]);

            if (!$isEqual) {
                $data->last()[0] = 'style:background-color:#FFCCCC;';
            }
        }

        $export = new class($data) implements FromArray, WithHeadings, WithColumnWidths, WithStyles {
            protected $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data->toArray();
            }

            public function headings(): array
            {
                return [
                    ['#', 'วันที่', 'เลขที่เอกสาร', 'บริษัท', 'เดบิต', 'เครดิต', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '']
                ];
            }

            public function columnWidths(): array
            {
                return [
                    'A' => 10,
                    'B' => 15,
                    'C' => 20,
                    'D' => 50,
                    'E' => 15,
                    'F' => 15,
                    'G' => 10,
                    'H' => 10,
                    'I' => 10,
                    'J' => 10,
                    'K' => 10,
                    'L' => 10,
                    'M' => 10,
                    'N' => 10,
                    'O' => 10,
                    'P' => 10,
                    'Q' => 10,
                    'R' => 10,
                    'S' => 10,
                    'T' => 10,
                    'U' => 10,
                    'V' => 10,
                    'W' => 10,
                    'X' => 10,
                    'Y' => 10,
                    'Z' => 10
                ];
            }

            public function styles(Worksheet $sheet)
            {
                // ตั้งค่าการจัดตรงกลางและตัวหนา
                $sheet->getStyle('A1:Z1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1:Z1')->getFont()->setBold(true);
                $dataRowCount = $this->data->count() + 1; // จำนวนแถวของข้อมูลบวกกับหัวตาราง
                $sheet->getStyle("C2:C$dataRowCount")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("E2:E$dataRowCount")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("F2:F$dataRowCount")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);


                // กำหนดสีพื้นหลังสำหรับแถวที่มียอดไม่เท่ากัน
                foreach ($this->data as $rowNumber => $row) {
                    if (isset($row[0]) && strpos($row[0], 'style:background-color') !== false) {
                        $sheet->getStyle("A" . ($rowNumber + 2) . ":Z" . ($rowNumber + 2))
                            ->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setARGB('FFFFCCCC');
                    }
                }
            }
        };

        return Excel::download($export, 'GeneralLedger.xlsx');
    }
}