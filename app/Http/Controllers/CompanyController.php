<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\GeneralLedger;
use App\Models\GeneralLedgerSub;
use App\Models\Account_Code;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
        $query = DB::table('users')
            ->where('status', 0)
            ->get();
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

            'code_company' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255',],
            'branch' => ['required', 'string', 'max:255'],
            'tax_id' => ['required', 'string', 'max:255'],
            'id_sheet' => ['required', 'string', 'max:255'],
            'id_apps_script' => ['required', 'string', 'max:255'],
            'accounting_period' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'max:255'],

        ]);

        $data = new User;

        $data->code_company = $request['code_company'];
        $data->company = $request['company'];
        $data->branch = $request['branch'];
        $data->tax_id = $request['tax_id'];
        $data->id_sheet = $request['id_sheet'];
        $data->id_apps_script = $request['id_apps_script'];
        $data->accounting_period = $request['accounting_period'];
        $data->email = $request['email'];
        $data->password =  Hash::make($request['password']);
        $data->status =  "0";

        $data->save();

        return redirect('home')->with('message', "บันทึกสำเร็จ");
    }

    public function saveCompanyData(Request $request)
    {


        if ($request->status == "add_delete") {
            $this->addDelete($request);
        }
        if ($request->status == "add_new") {
            $this->addNew($request);
        }
        if ($request->status == "add_choose") {
            $this->addChoose($request);
        }
    }


    /**
     * Display the specified resource.
     */
    public function addDelete($request)
    {


        // ตรวจสอบว่ามีข้อมูลใน sheets หรือไม่
        if (!isset($request->sheets[0]['GeneralLedger'])) {
            return response()->json(['success' => false, 'message' => 'No data found for General Ledger'], 400);
        }


        // เช็คว่ามีข้อมูลใน GeneralLedger หรือไม่ก่อนทำการลบ
        if (GeneralLedger::where('gl_code_company', $request->code_company)->exists()) {
            GeneralLedger::where('gl_code_company', $request->code_company)->delete();
        }

        // เช็คว่ามีข้อมูลใน GeneralLedgerSub หรือไม่ก่อนทำการลบ
        if (GeneralLedgerSub::where('gls_code_company', $request->code_company)->exists()) {
            GeneralLedgerSub::where('gls_code_company', $request->code_company)->delete();
        }

        // เช็คว่ามีข้อมูลใน Account_Code หรือไม่ก่อนทำการลบ
        if (Account_Code::where('acc_code_company', $request->code_company)->exists()) {
            Account_Code::where('acc_code_company', $request->code_company)->delete();
        }



        // ดึงข้อมูลจาก request
        $dataGeneralLedger = $request->sheets[0]['GeneralLedger'];




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
        if (isset($request->sheets[0]['GeneralLedgerSub'])) {
            $dataGeneralLedgerSub = $request->sheets[0]['GeneralLedgerSub'];

            // บันทึก General Ledger Sub เป็น row
            foreach ($dataGeneralLedgerSub as $subItem) {
                GeneralLedgerSub::create([
                    'gls_code_company' => $request->code_company,
                    'gls_code' => $subItem['GLS_Code'] ?? null,
                    'gls_id' => $subItem['GLS_ID'] ?? null,
                    'gls_gl_code' => $subItem['GLS_GL_Code'] ?? null,
                    'gls_gl_document' => $subItem['GLS_GL_Document'] ?? null,
                    'gls_gl_date' => $subItem['GLS_GL_Date'] ?? null,
                    'gls_account_code' => $subItem['GLS_Account_Code'] ?? null,
                    'gls_account_name' => $subItem['GLS_Account_Name'] ?? null,
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


    public function addNew($request)
    {


        // ตรวจสอบว่ามีข้อมูลใน sheets หรือไม่
        if (!isset($request->sheets[0]['GeneralLedger'])) {
            return response()->json(['success' => false, 'message' => 'No data found for General Ledger'], 400);
        }

        // ดึงข้อมูลจาก request
        $dataGeneralLedger = $request->sheets[0]['GeneralLedger'];




        // บันทึก General Ledger เป็น row
        foreach ($dataGeneralLedger as $item) {
            // เช็คว่ามี GL_Code ในฐานข้อมูลหรือไม่
            $existingEntry = GeneralLedger::where('gl_code', $item['GL_Code'])->first();

            if (!$existingEntry) {
                // ถ้าไม่มี GL_Code นี้ในฐานข้อมูล ให้ทำการบันทึกข้อมูลใหม่
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
        }

        // ตรวจสอบว่ามีข้อมูลใน General Ledger Sub หรือไม่
        // ตรวจสอบว่ามีข้อมูลใน General Ledger Sub หรือไม่
        if (isset($request->sheets[0]['GeneralLedgerSub'])) {
            $dataGeneralLedgerSub = $request->sheets[0]['GeneralLedgerSub'];

            // บันทึก General Ledger Sub เป็น row
            foreach ($dataGeneralLedgerSub as $subItem) {
                // เช็คว่ามี gls_code ในฐานข้อมูลหรือไม่
                $existingSubEntry = GeneralLedgerSub::where('gls_code', $subItem['GLS_Code'])->first();

                if (!$existingSubEntry) {
                    // ถ้าไม่มี gls_code นี้ในฐานข้อมูล ให้ทำการบันทึกข้อมูลใหม่
                    GeneralLedgerSub::create([
                        'gls_code_company' => $request->code_company,
                        'gls_code' => $subItem['GLS_Code'] ?? null,
                        'gls_id' => $subItem['GLS_ID'] ?? null,
                        'gls_gl_code' => $subItem['GLS_GL_Code'] ?? null,
                        'gls_gl_document' => $subItem['GLS_GL_Document'] ?? null,
                        'gls_gl_date' => $subItem['GLS_GL_Date'] ?? null,
                        'gls_account_code' => $subItem['GLS_Account_Code'] ?? null,
                        'gls_account_name' => $subItem['GLS_Account_Name'] ?? null,
                        'gls_debit' => $subItem['GLS_Debit'] ?? null,
                        'gls_credit' => $subItem['GLS_Credit'] ?? null,
                    ]);
                }
            }
        }


        // ตรวจสอบว่ามีข้อมูลใน Account Code หรือไม่
        // ตรวจสอบว่ามีข้อมูลใน Account Code หรือไม่
        if (isset($request->sheets[0]['Account_Code'])) {
            $dataAccountCode = $request->sheets[0]['Account_Code'];

            // บันทึก Account Code เป็น row
            foreach ($dataAccountCode as $accountItem) {
                // เช็คว่ามี acc_code ในฐานข้อมูลหรือไม่
                $existingAccountEntry = Account_Code::where('acc_code', $accountItem['ACC_Code'])->first();

                if (!$existingAccountEntry) {
                    // ถ้าไม่มี acc_code นี้ในฐานข้อมูล ให้ทำการบันทึกข้อมูลใหม่
                    Account_Code::create([
                        'acc_code_company' => $request->code_company,
                        'acc_code' => $accountItem['ACC_Code'] ?? null,
                        'acc_name' => $accountItem['ACC_Name'] ?? null,
                        'acc_type' => $accountItem['ACC_Type'] ?? null,
                    ]);
                }
            }
        }


        return response()->json(['success' => true, 'message' => 'Data saved successfully']);
    }


    public function addChoose($request)
    {

        // ตรวจสอบว่ามีข้อมูลใน sheets หรือไม่
        if (!isset($request->sheets[0]['GeneralLedger'])) {
            return response()->json(['success' => false, 'message' => 'No data found for General Ledger'], 400);
        }

        // ดึงข้อมูลจาก request
        $dataGeneralLedger = $request->sheets[0]['GeneralLedger'];

        // บันทึก General Ledger
        foreach ($dataGeneralLedger as $item) {
            // ตรวจสอบว่า GL_Code มีอยู่ในฐานข้อมูลหรือไม่
            $generalLedger = GeneralLedger::where('gl_code', $item['GL_Code'])->first();

            if ($generalLedger) {
      
                // ถ้ามีอยู่แล้วให้ทำการอัปเดต
                $generalLedger->update([
                    'gl_code_company' => $request->code_company,
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
            } else {
               
                // ถ้าไม่มีก็ทำการเพิ่มข้อมูลใหม่
                GeneralLedger::create([
                    'gl_code_company' => $request->code_company,
                    'gl_code' => $item['GL_Code'],
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
        }

        // ตรวจสอบว่ามีข้อมูลใน General Ledger Sub หรือไม่
        if (isset($request->sheets[0]['GeneralLedgerSub'])) {
            $dataGeneralLedgerSub = $request->sheets[0]['GeneralLedgerSub'];

            // บันทึกหรืออัปเดต General Ledger Sub
            foreach ($dataGeneralLedgerSub as $subItem) {
                $generalLedgerSub = GeneralLedgerSub::where('gls_code', $subItem['GLS_Code'])->first();

                if ($generalLedgerSub) {
                    // ถ้ามีอยู่แล้วให้ทำการอัปเดต
                    $generalLedgerSub->update([
                        'gls_code_company' => $request->code_company,
                        'gls_id' => $subItem['GLS_ID'] ?? null,
                        'gls_gl_code' => $subItem['GLS_GL_Code'] ?? null,
                        'gls_gl_document' => $subItem['GLS_GL_Document'] ?? null,
                        'gls_gl_date' => $subItem['GLS_GL_Date'] ?? null,
                        'gls_account_code' => $subItem['GLS_Account_Code'] ?? null,
                        'gls_account_name' => $subItem['GLS_Account_Name'] ?? null,
                        'gls_debit' => $subItem['GLS_Debit'] ?? null,
                        'gls_credit' => $subItem['GLS_Credit'] ?? null,
                    ]);
                } else {
                    // ถ้าไม่มีก็ทำการเพิ่มข้อมูลใหม่
                    GeneralLedgerSub::create([
                        'gls_code_company' => $request->code_company,
                        'gls_code' => $subItem['GLS_Code'],
                        'gls_id' => $subItem['GLS_ID'] ?? null,
                        'gls_gl_code' => $subItem['GLS_GL_Code'] ?? null,
                        'gls_gl_document' => $subItem['GLS_GL_Document'] ?? null,
                        'gls_gl_date' => $subItem['GLS_GL_Date'] ?? null,
                        'gls_account_code' => $subItem['GLS_Account_Code'] ?? null,
                        'gls_account_name' => $subItem['GLS_Account_Name'] ?? null,
                        'gls_debit' => $subItem['GLS_Debit'] ?? null,
                        'gls_credit' => $subItem['GLS_Credit'] ?? null,
                    ]);
                }
            }
        }

        // ตรวจสอบว่ามีข้อมูลใน Account Code หรือไม่
        if (isset($request->sheets[0]['Account_Code'])) {
            $dataAccountCode = $request->sheets[0]['Account_Code'];

            // บันทึกหรืออัปเดต Account Code
            foreach ($dataAccountCode as $accountItem) {
                $accountCode = Account_Code::where('acc_code', $accountItem['ACC_Code'])->first();

                if ($accountCode) {
                    // ถ้ามีอยู่แล้วให้ทำการอัปเดต
                    $accountCode->update([
                        'acc_code_company' => $request->code_company,
                        'acc_name' => $accountItem['ACC_Name'] ?? null,
                        'acc_type' => $accountItem['ACC_Type'] ?? null,
                    ]);
                } else {
                    // ถ้าไม่มีก็ทำการเพิ่มข้อมูลใหม่
                    Account_Code::create([
                        'acc_code_company' => $request->code_company,
                        'acc_code' => $accountItem['ACC_Code'],
                        'acc_name' => $accountItem['ACC_Name'] ?? null,
                        'acc_type' => $accountItem['ACC_Type'] ?? null,
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Data saved successfully']);
    }



    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $query =  User::find($id);
        return view('company.edit', compact('query'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {


        $request->validate([
            'code_company' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255',],
            'branch' => ['required', 'string', 'max:255'],
            'tax_id' => ['required', 'string', 'max:255'],
            'id_sheet' => ['required', 'string', 'max:255'],
            'id_apps_script' => ['required', 'string', 'max:255'],
            'accounting_period' => ['required', 'string', 'max:255'],
        ]);

        $data = User::find($id);

        $data->code_company = $request['code_company'];
        $data->company = $request['company'];
        $data->branch = $request['branch'];
        $data->tax_id = $request['tax_id'];
        $data->id_sheet = $request['id_sheet'];
        $data->id_apps_script = $request['id_apps_script'];
        $data->accounting_period = $request['accounting_period'];


        $data->save();

        return redirect('home')->with('message', "update สำเร็จ");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = User::find($id);
        $data->delete();
        return redirect('company')->with('message', "ลบข้อมูลสำเร็จ");
    }
}