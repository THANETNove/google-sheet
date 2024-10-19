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
        $endDate = $endDate->endOfDay();

        // Join the two tables (general_ledgers and general_ledger_subs) in one query

        $generalLedgers = DataGeneralLedgerSub::where('gl_code_company', $id)
            ->whereBetween('gl_date', [$startDate, $endDate])
            ->get();

        // For each general ledger, fetch the related subs
        foreach ($generalLedgers as $ledger) {
            $subs = $ledger->getSubsByGlCode($ledger->gl_code, $id);  // Call the function from the model
            $ledger->subs = $subs;  // Attach the subs to the ledger for easy use in the view
        }



        // แสดงผล
        //dd($generalLedgers, $glCodes);




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
        ini_set('memory_limit', '1024M'); // เพิ่มหน่วยความจำเป็น 1GB
        $data = session()->get('generalLedgers');



        $pdf = PDF::loadView('report.general_journal.pdf_view', [
            'query' => $data['query'],
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
            ->setOption('isRemoteEnabled', true); // อนุญาตให้ใช้ไฟล์จากภายนอก เช่น รูปภาพ

        return $pdf->stream(); // โหลดไฟล์ PDF
    }

    public function exportExcel($id, $start_date, $end_date)
    {

        $data = session()->get('generalLedgers');

        // Map the query data to include subs information
        $mappedData = $data['query']->map(function ($ledger) {
            $rows = [];

            // แปลงข้อมูลหลักของแต่ละ general ledger
            $formattedDate = Carbon::parse($ledger->gl_date)->format('d-m-Y');
            $rows[] = [
                'id' => $ledger->id,
                'gl_document' => $ledger->gl_document,
                'gl_date' => $formattedDate,
                'gl_company' => $ledger->gl_company,
                'gl_description' => $ledger->gl_description,
                'gls_account_name' => '', // Leave empty for the main row
                'gls_debit' => '', // Leave empty for the main row
                'gls_credit' => '', // Leave empty for the main row
            ];

            // Loop through subs and add them to the rows
            foreach ($ledger->subs as $sub) {
                $rows[] = [
                    'id' => '', // Leave empty for subs rows
                    'gl_document' => '', // Leave empty for subs rows
                    'gl_date' => '', // Leave empty for subs rows
                    'gl_company' => '', // Leave empty for subs rows
                    'gl_description' => '', // Leave empty for subs rows
                    'gls_account_name' => $sub->gls_account_name,
                    'gls_debit' => $sub->gls_debit,
                    'gls_credit' => $sub->gls_credit,
                ];
            }

            return $rows;
        });

        // Flatten the mapped data (because each ledger has multiple rows)
        $flattenedData = collect($mappedData)->flatten(1);

        // Define an inline class for export
        $export = new class($flattenedData) implements FromArray, WithHeadings {
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
                    'Account Name',
                    'Debit',
                    'Credit',
                ];
            }
        };

        // Download the Excel file
        return Excel::download($export, 'general_ledger.xlsx');
    }
}
