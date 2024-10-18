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
        /*   $startDate = $startDate ?? Carbon::createFromDate(date('Y'), $month, $day); */


        // ตรวจสอบว่า $startDate และ $endDate เป็น null หรือไม่
        if (is_null($endDate)) {

            // ถ้าเป็น null, ตั้ง $endDate ให้เป็นวันที่สิ้นสุดของเดือนที่แล้ว
            $endDate = Carbon::now()->subMonth()->endOfMonth(); // วันสุดท้ายของเดือนก่อนหน้า

            // ตั้ง $startDate ให้เป็นวันที่ 1 ของเดือนก่อนหน้า
            $startDate = Carbon::now()->subMonth()->startOfMonth(); // วันที่ 1 ของเดือนก่อนหน้า
        } else {
            // ถ้า $startDate หรือ $endDate ถูกส่งมา ให้ทำงานตามลอจิกเดิม
            $startDate = $startDate ?? Carbon::createFromDate(date('Y'), $month, $day);
            $endDate = $endDate ?? $startDate->copy()->addYear()->subDay();
        }

       
        $query = DB::table('general_ledgers')
            ->where('gl_code_company', $id)
            ->whereRaw('LOWER(gl_report_vat) = ?', ['sell'])
            ->whereBetween('gl_date', [$startDate, $endDate])
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

        // Map the query data to match the Excel export structure
        $mappedData = $data['query']->map(function ($item) {
            // แปลงวันที่ให้เป็นรูปแบบ dd-mm-yyyy
            $formattedDate = Carbon::parse($item->gl_date)->format('d-m-Y');

            return [
                'id' => $item->id,
                'gl_document' => $item->gl_document,
                'gl_date' => $formattedDate,
                'gl_company' => $item->gl_company,
                'gl_taxid' => $item->gl_taxid,
                'gl_branch' => $item->gl_branch,
                'gl_amount' => $item->gl_amount,
                'gl_tax' => $item->gl_tax,
                'gl_total' => $item->gl_total,
            ];
        });


        // Define an inline class for export
        $export = new class($mappedData) implements FromArray, WithHeadings {
            protected $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data->values()->toArray(); // Convert collection to array
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
        };

        // Download the Excel file
        return Excel::download($export, 'sell.xlsx');
    }
}