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
        /*  $startDate = $startDate ?? Carbon::createFromDate(date('Y'), $month, $day);
        $endDate = $endDate ?? $startDate->copy()->addYear()->subDay(); */
        if (is_null($startDate) && is_null($endDate)) {
            $startDate = Carbon::now()->subMonth()->startOfMonth();  // เดือนที่แล้ว วันที่ 1
            $endDate = Carbon::now()->subMonth()->endOfMonth();  // เดือนที่แล้ว วันสุดท้าย
        } else {
            $startDate = $startDate ?? Carbon::createFromDate(date('Y'), $month, $day);
            $endDate = $endDate ?? $startDate->copy()->addYear()->subDay();
        }


        $query = DB::table('general_ledgers')
            ->where('gl_code_company', $id)
            ->where('gl_report_vat', "Sell")
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

    public function exportPDF($id)
    {

        $data = $this->getData($id); // รับค่ากลับมา
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


    public function exportExcel($id)
    {
        $data = $this->getData($id);

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
                'gl_description' => $item->gl_description,
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
                    'Description',
                    'TaxID',
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