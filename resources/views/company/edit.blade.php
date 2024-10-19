@extends('layouts.appHome')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">เเก้ไขข้อมูลบริษัท</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('company-update', $query->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-fullname">รหัสบริษัท</label>
                                <input type="text" class="form-control @error('code_company') is-invalid @enderror"
                                    id="basic-default-fullname" name="code_company" placeholder="รหัสบริษัท"
                                    value="{{ $query->code_company }}">
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
                                    value="{{ $query->company }}">
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
                                    value="{{ $query->branch }}">
                                @error('branch')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="tax-id">เลขผู้เสียภาษี</label>
                                <input type="text" class="form-control @error('tax_id') is-invalid @enderror"
                                    id="tax-id" name="tax_id" placeholder="เลขผู้เสียภาษี" value="{{ $query->tax_id }}"
                                    required maxlength="17" inputmode="numeric"
                                    title="กรุณากรอกเลขผู้เสียภาษีในรูปแบบ 0-1055-49137-97-5">
                                <div class="invalid-feedback">
                                    กรุณากรอกเลขผู้เสียภาษีในรูปแบบ 0-1055-49137-97-5
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="basic-default-company">id Sheet</label>
                                <input type="text" class="form-control @error('id_sheet') is-invalid @enderror"
                                    id="basic-default-company" name="id_sheet" placeholder="id Sheet"
                                    value="{{ $query->id_sheet }}">
                                @error('id_sheet')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="basic-default-company">Id Apps Script Sheet</label>
                                <input type="text" class="form-control @error('id_apps_script') is-invalid @enderror"
                                    id="basic-default-company" name="id_apps_script" placeholder="id Sheet"
                                    value="{{ $query->id_apps_script }}">
                                @error('id_sheet')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>


                            <div class="mb-3">
                                <label class="form-label" for="accounting_period">รอบบัญชี</label>
                                <input type="text" class="form-control @error('accounting_period') is-invalid @enderror"
                                    id="accounting_period" name="accounting_period" placeholder="รอบบัญชี"
                                    value="{{ $query->accounting_period }}" required inputmode="numeric" maxlength="3"
                                    title="กรุณากรอกตัวเลขและเครื่องหมาย / เช่น 1/1">
                                <div class="invalid-feedback">
                                    กรุณากรอกตัวเลขและเครื่องหมาย / ในรูปแบบที่ถูกต้อง เช่น 1/1
                                </div>
                            </div>


                            <button type="submit" class="btn btn-primary mt-3">บันทัก</button>
                            <a type="button" href="{{ url('home') }}" class="btn btn-warning mt-3"
                                style="margin-left: 10px !important;">ยกเลิก</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // รหัสผู้เสียภาษี
            const taxIdInput = document.getElementById('tax-id');

            // ตรวจสอบว่าพบองค์ประกอบหรือไม่
            if (taxIdInput) {
                taxIdInput.addEventListener('input', function(event) {
                    // ลบตัวอักษรที่ไม่ใช่ตัวเลข
                    let value = event.target.value.replace(/\D/g, '');

                    // เพิ่มเครื่องหมาย '-' ในตำแหน่งที่ถูกต้อง
                    let formattedValue = '';
                    for (let i = 0; i < value.length; i++) {
                        if (i > 0 && (i === 1 || i === 5 || i === 10 || i === 12)) {
                            formattedValue += '-';
                        }
                        formattedValue += value[i];
                    }

                    // ตั้งค่าฟิลด์ด้วยค่าที่มีเครื่องหมาย '-'
                    event.target.value = formattedValue;

                    // ตรวจสอบรูปแบบความถูกต้อง
                    if (!/^\d{1}-\d{4}-\d{5}-\d{2}-\d{1}$/.test(formattedValue)) {
                        taxIdInput.setCustomValidity("กรุณากรอกเลขผู้เสียภาษีในรูปแบบ 0-1055-49137-97-5");
                    } else {
                        taxIdInput.setCustomValidity(""); // รีเซ็ตข้อผิดพลาด
                    }
                });
            } else {
                console.error("Element with ID 'tax-id' not found.");
            }

            // รอบัญชี
            const periodInput = document.getElementById('accounting_period');

            // ตรวจสอบว่าพบองค์ประกอบหรือไม่
            if (periodInput) {
                periodInput.addEventListener('input', function(event) {
                    // ลบตัวอักษรที่ไม่ใช่ตัวเลข
                    let value = event.target.value.replace(/\D/g, '');

                    // เพิ่มเครื่องหมาย '/' ในตำแหน่งที่ถูกต้อง
                    let formattedValue = '';
                    for (let i = 0; i < value.length; i++) {
                        if (i > 0 && (i === 1)) {
                            formattedValue += '/';
                        }
                        formattedValue += value[i];
                    }

                    // ตั้งค่าฟิลด์ด้วยค่าที่มีเครื่องหมาย '/'
                    event.target.value = formattedValue;

                    // ตรวจสอบรูปแบบความถูกต้อง
                    if (!/^\d{1,2}\/\d{1,4}$/.test(formattedValue)) {
                        periodInput.setCustomValidity("กรุณากรอกในรูปแบบที่ถูกต้อง เช่น 1/1");
                    } else {
                        periodInput.setCustomValidity(""); // รีเซ็ตข้อผิดพลาด
                    }
                });
            } else {
                console.error("Element with ID 'accounting_period' not found.");
            }

        });
    </script>
@endsection
