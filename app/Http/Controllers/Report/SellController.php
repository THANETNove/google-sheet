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
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class SellController extends Controller
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

        // ตรวจสอบและแปลง $startDate และ $endDate ให้เป็น Carbon object หากยังไม่ใช่
        if (is_null($endDate)) {
            $endDate = Carbon::now()->subMonth()->endOfMonth(); // วันสุดท้ายของเดือนก่อนหน้า
            $startDate = Carbon::now()->subMonth()->startOfMonth(); // วันที่ 1 ของเดือนก่อนหน้า
        } else {
            // ตรวจสอบว่าถูกส่งมาเป็น string หรือไม่ ถ้าใช่ให้แปลงเป็น Carbon object
            if (!($startDate instanceof Carbon)) {
                $startDate = Carbon::parse($startDate);
            }
            if (!($endDate instanceof Carbon)) {
                $endDate = Carbon::parse($endDate);
            }
            $startDate = $startDate ?? Carbon::createFromDate(date('Y'), $month, $day);
            $endDate = $endDate ?? $startDate->copy()->addYear()->subDay();
        }

        // ใช้ endOfDay ได้อย่างถูกต้องหลังจากแปลงเป็น Carbon
        $endDate = $endDate->endOfDay();


        $query = DB::table('general_ledgers')
            ->where('gl_code_company', $id)
            ->whereRaw('LOWER(gl_report_vat) = ?', ['sell'])
            ->whereBetween(DB::raw('DATE(gl_date)'), [$startDate->toDateString(), $endDate->toDateString()])

            ->select(
                'id',
                'gl_date',
                'gl_document',
                'gl_company',
                'gl_taxid',
                'gl_branch',
                'gl_amount',
                'gl_tax',
                'gl_total',
                'gl_url',
                'gl_page'

            )
            //D,E,H,I,J,S,T,U
            ->orderBy('gl_date', 'ASC')
            ->get();


        return [
            'query' => $query,
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

        return view('report.sell.index', compact('query'));
    }


    public function show(string $id)
    {
        $data = $this->getData($id); // รับค่ากลับมา



        return view('report.sell.view', [
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

        return view('report.sell.view', [
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

        $data = $this->getData($id, $start_date, $end_date); // รับค่ากลับมา
        $pdf = PDF::loadView('report.sell.pdf_view', [
            'query' => $data['query'],
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

        // Map the query data to match the Excel export structure and format numbers with commas
        $mappedData = $data['query']->map(function ($item) {
            $formattedDate = Carbon::parse($item->gl_date)->format('d-m-Y');

            return [
                'id' => $item->id,
                'gl_document' => $item->gl_document,
                'gl_date' => $formattedDate,
                'gl_company' => $item->gl_company,
                'gl_taxid' => $item->gl_taxid,
                'gl_branch' => $item->gl_branch,
                'gl_amount' => number_format($item->gl_amount, 2),
                'gl_tax' => number_format($item->gl_tax, 2),
                'gl_total' => number_format($item->gl_total, 2),
            ];
        });

        // Calculate totals for summary rows
        $totalAmount = $mappedData->where('gl_tax', '>', 0)->sum(fn($item) => str_replace(',', '', $item['gl_amount']));
        $totalTax = $mappedData->where('gl_tax', '>', 0)->sum(fn($item) => str_replace(',', '', $item['gl_tax']));
        $totalTaxSum = $mappedData->where('gl_tax', '>', 0)->sum(fn($item) => str_replace(',', '', $item['gl_total']));

        $totalAmountNoTax = $mappedData->where('gl_tax', '=', 0)->sum(fn($item) => str_replace(',', '', $item['gl_amount']));
        $totalNoTax = $mappedData->where('gl_tax', '=', 0)->sum(fn($item) => str_replace(',', '', $item['gl_tax']));
        $totalNoTaxSum = $mappedData->where('gl_tax', '=', 0)->sum(fn($item) => str_replace(',', '', $item['gl_total']));

        $totalSum = $totalAmount + $totalAmountNoTax;
        $totalSumTax = $totalTax + $totalNoTax;
        $totalSumNoTax = $totalSum + $totalSumTax;

        // Append summary rows with formatted values
        $mappedData->push([
            'id' => '',
            'gl_document' => '',
            'gl_date' => '',
            'gl_company' => '',
            'gl_taxid' => '',
            'gl_branch' => 'รวมภาษี', // Label for "รวมภาษี"
            'gl_amount' => number_format($totalAmount, 2),
            'gl_tax' => number_format($totalTax, 2),
            'gl_total' => number_format($totalTaxSum, 2),
        ]);

        $mappedData->push([
            'id' => '',
            'gl_document' => '',
            'gl_date' => '',
            'gl_company' => '',
            'gl_taxid' => '',
            'gl_branch' => 'รวมภาษี 0%', // Label for "รวมภาษี 0%"
            'gl_amount' => number_format($totalAmountNoTax, 2),
            'gl_tax' => number_format($totalNoTax, 2),
            'gl_total' => number_format($totalNoTaxSum, 2),
        ]);

        $mappedData->push([
            'id' => '',
            'gl_document' => '',
            'gl_date' => '',
            'gl_company' => '',
            'gl_taxid' => '',
            'gl_branch' => 'รวมทั้งสิ้น', // Label for "รวมทั้งสิ้น"
            'gl_amount' => number_format($totalSum, 2),
            'gl_tax' => number_format($totalSumTax, 2),
            'gl_total' => number_format($totalSumNoTax, 2),
        ]);

        // Define an inline class for export with column widths and styling
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
                // Center align headers and right-align monetary values
                $sheet->getStyle('A1:I1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Right-align monetary values and set number format for currency columns
                $sheet->getStyle('G2:I' . ($this->data->count() + 1))
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
        };

        // Download the Excel file
        return Excel::download($export, 'sell.xlsx');
    }
}