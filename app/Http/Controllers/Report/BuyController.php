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
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class BuyController extends Controller
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

    private function getData($id, $month = null, $year = null)
    {
        $user = DB::table('users')->find($id);

        // รับเดือนและปีจากฟอร์ม ถ้าไม่ได้ส่งมาจะใช้จาก $user->accounting_period
        $accounting_period = $user->accounting_period;
        list($day, $defaultMonth) = explode('/', $accounting_period);

        // ถ้าไม่ได้ส่ง month หรือ year มา ให้ใช้ค่าเริ่มต้นจาก accounting_period ของผู้ใช้
        if (is_null($month) || is_null($year)) {
            // ดึงวันที่ปัจจุบัน
            $currentDate = Carbon::now();

            // ลดลง 1 เดือนจากวันที่ปัจจุบัน
            $previousMonthDate = $currentDate->subMonth(); // ย้อน 1 เดือน (จากต.ค. เป็น ก.ย.)

            // กำหนดเดือนและปีให้เป็นเดือนและปีของเดือนก่อนหน้า
            $month = $previousMonthDate->format('m');  // ใช้เดือนของเดือนก่อนหน้า (กันยายน)
            $year = $previousMonthDate->format('Y');   // ใช้ปีของเดือนก่อนหน้า (2024)
        }
        // ใช้ Carbon เพื่อสร้างวันที่จากเดือนและปีที่กำหนด
        $startDate = Carbon::createFromDate($year, $month, 1); // วันที่ 1 ของเดือนที่เลือก
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth(); // วันที่สุดท้ายของเดือนที่เลือก

        // แปลงเดือนเป็นชื่อเดือนภาษาไทย
        $vat_month = $startDate->month;
        $monthName = $this->getMonths()[$vat_month];
        $monthName2 = "$monthName $year"; // เช่น 'มกราคม 2024'

        // ดึงข้อมูลตามเดือนและปีที่เลือก
        $query = DB::table('general_ledgers')
            ->where('gl_code_company', $id)
            ->whereRaw('LOWER(gl_report_vat) = ?', ['buy'])
            ->where('gl_vat', 1)
            ->whereMonth(DB::raw("CONVERT_TZ(gl_taxmonth, '+00:00', '+07:00')"), $month)  // แปลงเวลาจาก UTC เป็นเวลาไทย
            ->whereYear(DB::raw("CONVERT_TZ(gl_taxmonth, '+00:00', '+07:00')"), $year)
            ->select(
                'id',
                'gl_date',
                'gl_document',
                'gl_taxmonth',
                'gl_company',
                'gl_branch',
                'gl_taxid',
                'gl_amount',
                'gl_tax',
                'gl_total',
                'gl_url',
                'gl_page'
            )
            ->orderBy('gl_date', 'ASC')
            ->get();

        return [
            'query' => $query,
            'user' => $user,
            'startDate' => $startDate,
            'day' => $startDate->day,
            'vat_month' => $monthName2,
            'month' => $month,
            'year' => $year,
            'monthThai' => $monthName,
            'currentYear' => $year
        ];
    }


    public function index()
    {


        $query = DB::table('users')
            ->where('status', 0)
            ->get();

        return view('report.buy.index', compact('query'));
    }

    public function show(string $id)
    {
        $data = $this->getData($id); // รับค่ากลับมา


        return view('report.buy.view', [
            'query' => $data['query'],
            'user' => $data['user'],
            'startDate' => $data['startDate'],
            'day' => $data['day'],
            'month' => $data['month'],
            'year' => $data['year'],
            'vat_month' =>  $data['vat_month'],
            'monthThai' => $data['monthThai'],
            'currentYear' => $data['currentYear'],
            'id' => $id
        ]);
    }

    public function search(Request $request)
    {

        $month = $request->month;  // รับค่าจากฟอร์มเลือกเดือน
        $year = $request->year;    // รับค่าจากฟอร์มเลือกปี

        // เรียกฟังก์ชัน getData และส่งค่า month และ year ไป
        $data = $this->getData($request->id, $month, $year);

        return view('report.buy.view', [
            'query' => $data['query'],
            'user' => $data['user'],
            'startDate' => $data['startDate'],
            'day' => $data['day'],
            'month' => $data['month'],
            'year' => $data['year'],
            'vat_month' =>  $data['vat_month'],
            'monthThai' => $data['monthThai'],
            'currentYear' => $data['currentYear'],
            'id' => $request->id
        ]);
    }


    public function exportPDF($id, $month, $year)
    {

        $data = $this->getData($id, $month, $year); // รับค่ากลับมา

        $pdf = PDF::loadView('report.buy.pdf_view', [
            'query' => $data['query'],
            'user' => $data['user'],
            'startDate' => $data['startDate'],
            'day' => $data['day'],
            'vat_month' =>  $data['vat_month'],
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


    public function exportExcel($id, $month, $year)
    {


        $data = $this->getData($id, $month, $year);

        // Map the query data to match the Excel export structure
        $mappedData = $data['query']->map(function ($item) {
            $formattedDate = Carbon::parse($item->gl_taxmonth)->format('d-m-Y');

            return [
                'id' => $item->id,
                'gl_document' => $item->gl_document,
                'gl_taxmonth' => $formattedDate,
                'gl_company' => $item->gl_company,
                'gl_taxid' => $item->gl_taxid,
                'gl_branch' => $item->gl_branch,
                'gl_amount' => number_format($item->gl_amount, 2),
                'gl_tax' => number_format($item->gl_tax, 2),
                'gl_total' => number_format($item->gl_total, 2),
            ];
        });

        // Calculate totals
        $totalAmount = $mappedData->sum(fn($item) => str_replace(',', '', $item['gl_amount']));
        $totalTax = $mappedData->sum(fn($item) => str_replace(',', '', $item['gl_tax']));
        $totalAll = $mappedData->sum(fn($item) => str_replace(',', '', $item['gl_total']));

        // Append totals row
        $mappedData->push([
            'id' => '',
            'gl_document' => '',
            'gl_taxmonth' => '',
            'gl_company' => '',
            'gl_taxid' => '',
            'gl_branch' => 'รวมทั้งสิ้น', // Label for totals
            'gl_amount' => number_format($totalAmount, 2),
            'gl_tax' => number_format($totalTax, 2),
            'gl_total' => number_format($totalAll, 2),
        ]);

        // Define an inline class for export with column widths and styles
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
                    'ID',
                    'Document',
                    'Date',
                    'Company',
                    'TaxID',
                    'Branch',
                    'Amount',
                    'Tax',
                    'Total',
                ];
            }

            public function columnWidths(): array
            {
                return [
                    'A' => 10,  // ID
                    'B' => 20,  // Document
                    'C' => 15,  // Date
                    'D' => 30,  // Company
                    'E' => 20,  // TaxID
                    'F' => 25,  // Branch
                    'G' => 15,  // Amount
                    'H' => 15,  // Tax
                    'I' => 15,  // Total
                ];
            }

            public function styles(Worksheet $sheet)
            {
                // Center align headers
                $sheet->getStyle('A1:I1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Right-align monetary columns (Amount, Tax, Total)
                $sheet->getStyle('G2:I' . ($this->data->count() + 1))
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
        };

        // Download the Excel file
        return Excel::download($export, 'buy.xlsx');
    }
}
