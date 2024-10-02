@extends('layouts.appHome')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">

                <div class="card">
                    @php
                        // ตรวจสอบว่าตัวแปร $query ถูกตั้งค่าแล้วหรือไม่
                        $query = isset($query) ? $query : [];
                    @endphp

                    <h5 class="card-header">รายชื่อ บริษัท</h5>

                    <div class="container">
                        <div class="row">
                            <div class="col-md-4  order-2 order-md-1">
                                <p>{{ $query->code_company }}</p>
                                <p>{{ $query->company }}</p>
                                <p>{{ $query->branch }}</p>
                                <p>{{ $query->tax_id }}</p>
                                <p>{{ $query->id_sheet }}</p>
                                <p>{{ $query->id_apps_script }}</p>
                            </div>
                            <div class="col-md-8  text-mt--2 order-1 order-md-2">
                                <div class="text-end  justify-content-end align-items-center">
                                    <div class="row">

                                        <div class="col-12 mb-3">
                                            <button id="importBtn" type="button" class="btn btn-primary"
                                                style="display: none;" onclick="confirmImport('add_delete')">
                                                <i class='bx bx-import'></i>&nbsp; นำข้อมูลเข้าเเละลบข้อมูลเก่า
                                            </button>
                                        </div>
                                        <div class="col-12">
                                            <button id="importBtn2" type="button" class="btn btn-primary"
                                                style="display: none;" onclick="confirmImport('add_new')">
                                                <i class='bx bx-import'></i>&nbsp; นำข้อมูลเเค่อันใหม่เข้า
                                            </button>
                                        </div>

                                    </div>


                                    <button id="spinner" class="btn btn-primary" type="button">
                                        <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                                        <span role="status">Loading...</span>
                                    </button>
                                    <button id="uploading" class="btn btn-primary" type="button" style="display: none;">
                                        <span class="spinner-border text-success  spinner-border-sm"
                                            aria-hidden="true"></span>
                                        <span role="status">Uploading...</span>
                                    </button>
                                </div>


                                <div class="card">
                                    <h5 class="card-header">Table Caption</h5>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Project</th>
                                                    <th>Client</th>
                                                    <th>Users</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        Angular Project
                                                    </td>
                                                    <td>Albert Cook</td>
                                                    <td>Albert Cook</td>
                                                </tr>

                                                <tr>
                                                    <td>

                                                        Bootstrap Project
                                                    </td>
                                                    <td>Jerry Milton</td>
                                                    <td>Jerry Milton</td>


                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        // นำค่าตัวแปร $query จาก PHP มาใช้ใน JavaScript
        const queryData = @json($query);
        const action = 'getUsers';
        const sheets = ['General Ledger', 'General Ledger Sub', 'Account_Code'];
        const id_sheet = queryData.id_sheet;
        const id_apps_script = queryData.id_apps_script;
        let responseData = {}; // ใช้ let เพื่อให้สามารถเปลี่ยนค่าได้

        /* console.log('PHP query:', queryData); // แสดงข้อมูลจาก $query
        console.log('id_sheet:', id_sheet);
        console.log('id_apps_script:', id_apps_script); */
        // สร้าง array ของ Promise จากการเรียก axios


        if (id_sheet || id_apps_script) {
            /*  const url =
                 `https://script.google.com/macros/s/${id_apps_script}/exec?action=getUsers&id_sheet=${id_sheet}`; */

            const url =
                `https://script.google.com/macros/s/AKfycby4n-SqnsusWwlxpPp5Z-FhZ7uH-kOp5DChrBA-yxulWLKAbUShnNeuSA1KJf4Iv2UT/exec?action=getUsers&id_sheet=1NwqH-EqZsGTGCmB79m491aNd8l_Ib7kQHAUDFbhNrk8`;

            axios.get(url)
                .then(response => {
                    /*  console.log(`response status:`, response.status);
                     console.log(`response data:`, response.data); */
                    responseData = response.data;
                    endImport();
                })
                .catch(error => {
                    console.error(`Error in request:`, error.response ? error.response.data : error.message);

                });
        }





        // ฟังก์ชันเพื่อซ่อน spinner และแสดงปุ่ม
        function endImport() {
            document.getElementById('importBtn').style.display = 'inline-block';
            document.getElementById('importBtn2').style.display = 'inline-block';
            document.getElementById('spinner').style.display = 'none';
            document.getElementById('uploading').style.display = 'none';


        }



        function confirmImport(type) {
            let message = type === 'add_delete' ?
                "คุณแน่ใจหรือไม่ว่าต้องการนำข้อมูลเข้าและลบข้อมูลเก่า?" :
                "คุณแน่ใจหรือไม่ว่าต้องการนำเข้าเฉพาะข้อมูลใหม่?";

            Swal.fire({
                title: 'ยืนยันการนำข้อมูลเข้า',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ใช่, ดำเนินการต่อ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // ถ้าผู้ใช้กดยืนยัน (OK) เรียกใช้ฟังก์ชัน importDB
                    importDB(type);
                }
            });
        }

        function importDB(e) {


            // แสดง Progress Bar
            document.getElementById('uploading').style.display = 'inline-block';
            document.getElementById('importBtn').style.display = 'none'; // ซ่อนปุ่ม
            document.getElementById('importBtn2').style.display = 'none'; // ซ่อนปุ่ม


            // ส่งข้อมูลไปยัง Laravel API

            // ส่งข้อมูลทั้งหมดใน responseData ไปยัง Laravel API ในคำขอเดียว
            const promise = axios.post('save-company-data', {
                code_company: queryData.id, // รหัสบริษัท
                status: e, // รหัสบริษัท
                sheets: [responseData] // ข้อมูลในทุก sheet
            });

            // ใช้ Promise.all() หากมีคำขอเพิ่มเติมในอนาคต
            Promise.all([promise])
                .then(() => {

                    Swal.fire({
                        icon: 'success',
                        title: 'นำเข้าข้อมูลสำเร็จ!',
                        text: 'ข้อมูลได้ถูกนำเข้าฐานข้อมูลเรียบร้อยแล้ว',
                        confirmButtonText: 'ตกลง'
                    });
                    endImport(); // เรียก endImport เมื่อคำสั่งเสร็จสิ้น
                })
                .catch(error => {

                    endImport(); // แม้มีข้อผิดพลาดก็เรียก endImport เพื่อคืนสถานะ
                });
        }
    </script>
@endsection
