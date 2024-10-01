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
                            <div class="col-md-6">
                                <p>{{ $query->code_company }}</p>
                                <p>{{ $query->company }}</p>
                                <p>{{ $query->branch }}</p>
                                <p>{{ $query->tax_id }}</p>
                                <p>{{ $query->id_sheet }}</p>
                                <p>{{ $query->id_apps_script }}</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <div>
                                    <button id="importBtn" type="button" class="btn btn-primary" style="display: none;"
                                        onclick="importDB()">
                                        <i class='bx bx-import'></i>&nbsp; นำข้อมมูลเข้า
                                    </button>

                                    <!-- Spinner ที่ซ่อนอยู่ตอนเริ่มต้น -->
                                    <div id="spinner" class="spinner-border text-info" role="status"
                                        style="display:block;">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div class="progress" style="display: none;" id="progressContainer">
                                        <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%"
                                            aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
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
        let responseData = {}; // ใช้ let เพื่อให้สามารถเปลี่ยนค่าได้

        console.log('PHP query:', queryData.code_company); // แสดงข้อมูลจาก $query

        // สร้าง array ของ Promise จากการเรียก axios
        const axiosPromises = sheets.map(sheet => {
            const sanitizedSheet = sheet.replace(/\s+/g, '_'); // แทนที่ช่องว่างด้วย '_'

            const url =
                `https://script.google.com/macros/s/${queryData.id_apps_script}/exec?action=${action}&sheet=${sheet}&id_sheet=${id_sheet}`;
            return axios.get(url)
                .then(response => {
                    console.log(`${sheet}:`, response.data);
                    responseData[sanitizedSheet] = response.data;
                })
                .catch(error => {
                    console.error(`Error in ${sheet}:`, error);
                });
        });

        // ใช้ Promise.all เพื่อตรวจสอบว่าทุก axios เสร็จแล้ว
        Promise.all(axiosPromises)
            .then(() => {
                endImport(); // เรียก endImport เมื่อทุกคำสั่งเสร็จสิ้น
            })
            .catch(error => {
                console.error('Error in one of the requests:', error);
                endImport(); // แม้มีข้อผิดพลาดก็เรียก endImport เพื่อคืนสถานะ
            });

        // ฟังก์ชันเพื่อซ่อน spinner และแสดงปุ่ม
        function endImport() {
            document.getElementById('importBtn').style.display = 'block';
            document.getElementById('spinner').style.display = 'none';
            document.getElementById('progressContainer').style.display = 'none';

        }

        function importDB() {
            // แสดง Progress Bar
            document.getElementById('progressContainer').style.display = 'block';
            document.getElementById('importBtn').style.display = 'none'; // ซ่อนปุ่ม

            // ส่งข้อมูลไปยัง Laravel API
            console.log("responseData", responseData);

            // ส่งข้อมูลทั้งหมดใน responseData ไปยัง Laravel API ในคำขอเดียว
            const promise = axios.post('save-company-data', {
                code_company: queryData.code_company, // รหัสบริษัท
                sheets: [responseData] // ข้อมูลในทุก sheet
            });

            // ใช้ Promise.all() หากมีคำขอเพิ่มเติมในอนาคต
            Promise.all([promise])
                .then(() => {
                    alert("Data imported successfully!");
                    endImport(); // เรียก endImport เมื่อคำสั่งเสร็จสิ้น
                })
                .catch(error => {
                    console.error('Error importing data:', error);
                    endImport(); // แม้มีข้อผิดพลาดก็เรียก endImport เพื่อคืนสถานะ
                });
        }
    </script>
@endsection
