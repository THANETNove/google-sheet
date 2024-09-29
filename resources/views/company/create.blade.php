@extends('layouts.appHome')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">เพิ่มบริษัท</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-fullname">รหัสบริษัท</label>
                                <input type="text" class="form-control" id="basic-default-fullname" name="code_company"
                                    placeholder="รหัสบริษัท">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-company">ชื่อบริษัท</label>
                                <input type="text" class="form-control" id="basic-default-company" name="company"
                                    placeholder="ACME Inc.">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-fullname">สาขา</label>
                                <input type="text" class="form-control" id="basic-default-fullname" name="branch"
                                    placeholder="สาขา">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-company">เลขผู้เสียภาษี</label>
                                <input type="text" class="form-control" id="basic-default-company" name="tax_id"
                                    placeholder="เลขผู้เสียภาษี">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-company">id Sheet</label>
                                <input type="text" class="form-control" id="basic-default-company" name="id_sheet"
                                    placeholder="id Sheet">
                            </div>

                            <button type="submit" class="btn btn-primary mt-3">Send</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
