@extends('layouts.appHome')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">เพิ่มข้อมูลบริษัท</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('company-store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-fullname">รหัสบริษัท</label>
                                <input type="text" class="form-control @error('code_company') is-invalid @enderror"
                                    id="basic-default-fullname" name="code_company" placeholder="รหัสบริษัท"
                                    value="{{ old('code_company') }}">
                                @error('code_company')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-company">ชื่อบริษัท</label>
                                <input type="text" class="form-control  @error('company') is-invalid @enderror"
                                    id="basic-default-company" name="company" placeholder="ACME Inc."
                                    value="{{ old('company') }}">
                                @error('company')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-fullname">สาขา</label>
                                <input type="text" class="form-control  @error('branch') is-invalid @enderror"
                                    id="basic-default-fullname" name="branch" placeholder="สาขา"
                                    value="{{ old('branch') }}">
                                @error('branch')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-company">เลขผู้เสียภาษี</label>
                                <input type="text" class="form-control  @error('tax_id') is-invalid @enderror"
                                    id="basic-default-company" name="tax_id" placeholder="เลขผู้เสียภาษี"
                                    value="{{ old('tax_id') }}">
                                @error('tax_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-company">เลขผู้เสียภาษี</label>
                                <input type="text" class="form-control  @error('tax_id') is-invalid @enderror"
                                    id="basic-default-company" name="tax_id" placeholder="เลขผู้เสียภาษี"
                                    value="{{ old('tax_id') }}">
                                @error('tax_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-company">id Sheet</label>
                                <input type="text" class="form-control @error('id_sheet') is-invalid @enderror"
                                    id="basic-default-company" name="id_sheet" placeholder="id Sheet"
                                    value="{{ old('id_sheet') }}">
                                @error('id_sheet')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-company">id Apps Script</label>
                                <input type="text" class="form-control @error('id_apps_script') is-invalid @enderror"
                                    id="basic-default-company" name="id_apps_script" placeholder="id apps script"
                                    value="{{ old('id_apps_script') }}">
                                @error('id_apps_script')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-company">รอบบัญชี</label>
                                <input type="text" class="form-control @error('accounting_period') is-invalid @enderror"
                                    id="basic-default-company" name="accounting_period" placeholder="รอบบัญชี"
                                    value="{{ old('accounting_period') }}">
                                @error('accounting_period')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-company">email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="basic-default-company" name="email" placeholder="Email"
                                    value="{{ old('email') }}" required>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-company">Password</label>
                                <input type="text" class="form-control @error('password') is-invalid @enderror"
                                    id="basic-default-company" name="password" placeholder="Password" required
                                    value="{{ old('password') }}">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary mt-3">Send</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
