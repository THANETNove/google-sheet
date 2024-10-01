<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Company;
use App\Models\GeneralLedger;
use App\Models\GeneralLedgerSub;
use App\Models\Account_Code;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = DB::table('companies')->get();/* ->appends($request->all()) */
        return view('company.index', compact('query'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('company.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([

            'code_company' => ['required', 'string', 'max:255', 'unique:companies'],
            'company' => ['required', 'string', 'max:255', 'unique:companies'],
            'branch' => ['required', 'string', 'max:255'],
            'tax_id' => ['required', 'string', 'max:255'],
            'id_sheet' => ['required', 'string', 'max:255'],

        ]);

        $data = new Company;

        $data->code_company = $request['code_company'];
        $data->company = $request['company'];
        $data->branch = $request['branch'];
        $data->tax_id = $request['tax_id'];
        $data->id_sheet = $request['id_sheet'];

        $data->save();

        return redirect('company')->with('message', "บันทึกสำเร็จ");
    }

    public function saveCompanyData(Request $request)
    {
        // ตรวจสอบว่ามีข้อมูลใน sheets หรือไม่
        if (!isset($request->sheets[0]['General_Ledger'])) {
            return response()->json(['success' => false, 'message' => 'No data found for General Ledger'], 400);
        }

        // ดึงข้อมูลจาก request
        $dataGeneralLedger = $request->sheets[0]['General_Ledger'];

        // บันทึก General Ledger เป็น row
        foreach ($dataGeneralLedger as $item) {
            GeneralLedger::create([
                'gl_code_company' => $request->code_company,
                'gl_code' => $item['GL_Code'] ?? null,
                'gl_refer' => $item['GL_Refer'] ?? null,
                'gl_report_vat' => $item['GL_Report_VAT'] ?? null,
                'gl_date' => $item['GL_Date'] ?? null,
                'gl_document' => $item['GL_Document'] ?? null,
                'gl_date_check' => $item['GL_Date_Check'] ?? null,
                'gl_document_check' => $item['GL_Document_Check'] ?? null,
                'gl_company' => $item['GL_Company'] ?? null,
                'gl_taxid' => $item['GL_TaxID'] ?? null,
                'gl_branch' => $item['GL_Branch'] ?? null,
                'gl_code_acc' => $item['GL_Code_Acc'] ?? null,
                'gl_description' => $item['GL_Description'] ?? null,
                'gl_code_acc_pay' => $item['GL_Code_Acc_Pay'] ?? null,
                'gl_date_pay' => $item['GL_Date_Pay'] ?? null,
                'gl_vat' => $item['GL_Vat'] ?? null,
                'gl_rate' => $item['GL_Rate'] ?? null,
                'gl_taxmonth' => $item['GL_TaxMonth'] ?? null,
                'gl_amount_no_vat' => $item['GL_AmountNoVat'] ?? null,
                'gl_amount' => $item['GL_Amount'] ?? null,
                'gl_tax' => $item['GL_Tax'] ?? null,
                'gl_total' => $item['GL_Total'] ?? null,
                'gl_url' => $item['GL_URL'] ?? null,
                'gl_page' => $item['GL_Page'] ?? null,
                'gl_remark' => $item['GL_Remark'] ?? null,
                'gl_email' => $item['GL_Email'] ?? null,
            ]);
        }

        // ตรวจสอบว่ามีข้อมูลใน General Ledger Sub หรือไม่
        if (isset($request->sheets[0]['General_Ledger_Sub'])) {
            $dataGeneralLedgerSub = $request->sheets[0]['General_Ledger_Sub'];

            // บันทึก General Ledger Sub เป็น row
            foreach ($dataGeneralLedgerSub as $subItem) {
                GeneralLedgerSub::create([
                    'gls_code_company' => $request->code_company,
                    'gls_code' => $subItem['GLS_Code'] ?? null,
                    'gls_id' => $subItem['GLS_ID'] ?? null,
                    'gls_gl_code' => $subItem['GLS_GL_Code'] ?? null,
                    'gls_gl_document' => $subItem['GLS_GL_Document'] ?? null,
                    'gls_account_code' => $subItem['GLS_Account_Code'] ?? null,
                    'gls_debit' => $subItem['GLS_Debit'] ?? null,
                    'gls_credit' => $subItem['GLS_Credit'] ?? null,
                ]);
            }
        }

        // ตรวจสอบว่ามีข้อมูลใน Account Code หรือไม่
        if (isset($request->sheets[0]['Account_Code'])) {
            $dataAccountCode = $request->sheets[0]['Account_Code'];

            // บันทึก Account Code เป็น row
            foreach ($dataAccountCode as $accountItem) {
                Account_Code::create([
                    'acc_code_company' => $request->code_company,
                    'acc_code' => $accountItem['ACC_Code'] ?? null,
                    'acc_name' => $accountItem['ACC_Name'] ?? null,
                    'acc_type' => $accountItem['ACC_Type'] ?? null,
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Data saved successfully']);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $query =  Company::find($id);
        return view('company.edit', compact('query'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {


        $request->validate([
            'code_company' => ['required', 'string', 'max:255', 'unique:companies,code_company,' . $id],
            'company' => ['required', 'string', 'max:255', 'unique:companies,company,' . $id],
            'branch' => ['required', 'string', 'max:255'],
            'tax_id' => ['required', 'string', 'max:255'],
            'id_sheet' => ['required', 'string', 'max:255'],
            'id_apps_script' => ['required', 'string', 'max:255'],
        ]);

        $data = Company::find($id);

        $data->code_company = $request['code_company'];
        $data->company = $request['company'];
        $data->branch = $request['branch'];
        $data->tax_id = $request['tax_id'];
        $data->id_sheet = $request['id_sheet'];
        $data->id_apps_script = $request['id_apps_script'];

        $data->save();

        return redirect('company')->with('message', "update สำเร็จ");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Company::find($id);
        $data->delete();
        return redirect('company')->with('message', "ลบข้อมูลสำเร็จ");
    }
}
