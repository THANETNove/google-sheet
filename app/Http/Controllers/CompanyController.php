<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Company;
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