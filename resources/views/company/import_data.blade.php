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

                    <h5 class="card-header">ข้อมูลบริษัท</h5>

                    <div class="container">
                        <div class="row">
                            <div class="col-md-8  order-2 order-md-1">

                                <p><span>รหัสบริษัท: &nbsp; </span>{{ $query->code_company }}</p>
                                <p><span>ชื่อบริษัท: &nbsp; </span>{{ $query->company }}</p>
                                <p><span>สาขา: &nbsp; </span>{{ $query->branch }}</p>
                                <p><span>เลขผู้เสียภาษี: &nbsp; </span>{{ $query->tax_id }}</p>
                                <p> จำนวน General Ledger DB: &nbsp; &nbsp;<span id="gl_db"> </span></p>
                                <p>จำนวน General Ledger Sub DB: &nbsp; &nbsp;<span id="gl_db_sub"> </span>
                                </p>
                                <p>จำนวน Account_Code DB: &nbsp; &nbsp;<span id="ac_db"> </span></p>
                            </div>
                            <div class="col-md-4  text-mt--2 order-1 order-md-2">
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

                            </div>
                        </div>
                    </div>


                    <div class="table-responsive mt-5 table-min-height">
                        <h5 class="card-header">รายชื่อบริษใน Google Sheet </h5>
                        <div class="col m-3">
                            <input type="text" class="form-control" id="defaultFormControlInput"
                                oninput="searchData(this.value)" placeholder="Search"
                                aria-describedby="defaultFormControlHelp">
                        </div>
                        <table class="table" id="dataTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>GL_Code</th>
                                    <th>GL_Company</th>
                                    <th>GL_Amount</th>
                                    <th>GL_Tax</th>
                                    <th>GL_Total</th>
                                    <th>GL_Date</th>
                                    <th>GL_Report_VAT</th>
                                    <th>GL_Document</th>
                                    <th>GL_Date_Check</th>
                                    <th>GL_Document_Check</th>
                                    <th>GL_TaxID</th>
                                    <th>GL_Code_Acc</th>
                                    <th>GL_Description</th>
                                    <th>GL_Code_Acc_Pay</th>
                                    <th>GL_Date_Pay</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <!-- Rows will be added dynamically here -->
                            </tbody>
                        </table>
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
        // เรียกใช้งานฟังก์ชันทันที
        queryCountData();

        function queryCountData() {
            if (queryData.id) {
                const urlCount = `/count-data/${queryData.id}`;

                axios.get(urlCount)
                    .then(response => {
                        console.log("response", response.data); // แสดง response data ที่ได้

                        // ตั้งค่า innerHTML สำหรับ General Ledger DB
                        const glDb = document.getElementById('gl_db');
                        glDb.innerHTML =
                            `${(response.data.queryGeneral).toLocaleString()}`;

                        // ตั้งค่า innerHTML สำหรับ General Ledger Sub DB
                        const glDbSub = document.getElementById('gl_db_sub');
                        glDbSub.innerHTML =
                            `${(response.data.queryGeneralSub).toLocaleString()}`;

                        // ตั้งค่า innerHTML สำหรับ Account_Code DB
                        const acDb = document.getElementById('ac_db');
                        acDb.innerHTML =
                            `${(response.data.queryAccount).toLocaleString()}`;
                    })
                    .catch(error => {
                        console.error(`Error in request:`, error.response ? error.response.data : error.message);
                    });
            }
        }




        if (id_sheet || id_apps_script) {
            const url =
                `https://script.google.com/macros/s/${id_apps_script}/exec?action=getUsers&id_sheet=${id_sheet}`;



            axios.get(url)
                .then(response => {

                    responseData = response.data;
                    endImport();
                    displayData(responseData.GeneralLedger)
                })
                .catch(error => {
                    console.error(`Error in request:`, error.response ? error.response.data : error.message);

                });
        }


        function searchData(query) {
            const rows = document.querySelectorAll('#tableBody tr'); // เลือกทุกแถวใน tbody

            rows.forEach(row => {
                const cells = row.getElementsByTagName('td');
                let found = false;

                // วนลูปตรวจสอบข้อมูลในแต่ละคอลัมน์ของแถว
                for (let i = 0; i < cells.length; i++) { // เปลี่ยนเริ่มต้นเป็น 0
                    if (cells[i].textContent.toLowerCase().includes(query
                            .toLowerCase())) { // ใช้ includes แทน indexOf
                        found = true; // หากพบข้อมูลที่ตรงกัน
                        break; // ไม่ต้องตรวจสอบคอลัมน์อื่นอีก
                    }
                }

                // แสดงหรือซ่อนแถวตามผลการค้นหา
                if (found) {
                    row.style.display = ''; // แสดงแถว
                } else {
                    row.style.display = 'none'; // ซ่อนแถว
                }
            });
        }

        function displayData(data) {
            const tableBody = document.getElementById('tableBody');
            tableBody.innerHTML = ''; // ล้างข้อมูลเดิมในตารางก่อน

            // สมมุติว่า responseData มีข้อมูลเป็น array ของ object
            data.forEach((item, index) => {
                const row = document.createElement('tr');

                row.innerHTML = `
                <td>
                    <input type="checkbox" name="selectedItems[]" value="${item.GL_Code}" id="check_${index}">
                </td>
                <td>${item.GL_Code}</td>
                <td>${item.GL_Company}</td>
                <td>${item.GL_Amount}</td>
                <td>${item.GL_Tax}</td>
                <td>${item.GL_Total}</td>
                <td>${item.GL_Date}</td>
                <td>${item.GL_Report_VAT}</td>
                
                <td>${item.GL_Document}</td>
                <td>${item.GL_Date_Check}</td>
                <td>${item.GL_Document_Check}</td>
            
                <td>${item.GL_TaxID}</td>
                <td>${item.GL_Code_Acc}</td>
                <td>${item.GL_Description}</td>
                <td>${item.GL_Code_Acc_Pay}</td>
                <td>${item.GL_Date_Pay}</td>
      
            `;

                tableBody.appendChild(row);
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
                    queryCountData();
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
