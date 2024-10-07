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

                    <h5 class="card-header"></h5>

                    <div class="container">
                        <div class="row">
                            <div class="col-md-8  order-2 order-md-1">

                                <h4 class="mb-3">บริษัท {{ $query->company }} </h4>
                                {{-- <div class="border p-3 rounded shadow-sm">
                                    <p><strong>รหัสบริษัท:</strong> {{ $query->code_company }}</p>
                                    <p><strong>ชื่อบริษัท:</strong> {{ $query->company }}</p>
                                    <p><strong>สาขา:</strong> {{ $query->branch }}</p>
                                    <p><strong>เลขผู้เสียภาษี:</strong> {{ $query->tax_id }}</p>
                                    <p><strong>จำนวน General Ledger DB:</strong> <span id="gl_db"> </span></p>
                                    <p><strong>จำนวน General Ledger Sub DB:</strong> <span id="gl_db_sub"> </span></p>
                                    <p><strong>จำนวน Account_Code DB:</strong> <span id="ac_db"> </span></p>
                                </div> --}}
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
                        <h5 class="card-header">ข้อมูลใน Google Sheet </h5>
                        {{--   <button id="importBtn3" type="button" class="btn btn-primary" onclick="confirmImport('add_new')">
                            <i class='bx bx-import'></i>&nbsp; นำข้อมูลที่เลือก
                        </button>
                        <div class="col m-3">
                            <input type="text" class="form-control" id="defaultFormControlInput"
                                oninput="searchData(this.value)" placeholder="Search"
                                aria-describedby="defaultFormControlHelp">
                        </div> --}}
                        <div class="col m-3">
                            <input type="text" class="form-control" id="defaultFormControlInput"
                                oninput="searchData(this.value)" placeholder="Search"
                                aria-describedby="defaultFormControlHelp">
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="m-3">
                                <button id="importBtn3" type="button" class="btn btn-primary" style="display: none;"
                                    onclick="confirmImport('add_choose')">
                                    <i class='bx bx-import'></i>&nbsp; นำข้อมูลที่เลือก
                                </button>
                            </div>
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

        // สร้าง array ของ Promise จากการเรียก axios
        // เรียกใช้งานฟังก์ชันทันที






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
            document.getElementById('importBtn3').style.display = 'inline-block';
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





            const selectedItems = Array.from(document.querySelectorAll('input[name="selectedItems[]"]:checked'))
                .map(checkbox => checkbox.value);

            console.log("selectedItems", selectedItems);
            let selectedData = {
                GeneralLedger: [],
                GeneralLedgerSub: [],
                Account_Code: []
            };

            if (e == "add_choose") {
                const selectedDataGl = responseData.GeneralLedger.filter(item =>
                    selectedItems.includes(item.GL_Code)
                );
                const selectedDataGlSub = responseData.GeneralLedgerSub.filter(item =>
                    selectedItems.includes(item.GLS_GL_Code)
                );
                const selectedDataAcc = responseData.Account_Code.filter(item =>
                    selectedItems.includes(item.acc_Code_Com)
                );
                selectedData = {
                    GeneralLedger: selectedDataGl,
                    GeneralLedgerSub: selectedDataGlSub,
                    Account_Code: selectedDataAcc
                };


            }
            // แสดง Progress Bar
            document.getElementById('uploading').style.display = 'inline-block';
            document.getElementById('importBtn').style.display = 'none'; // ซ่อนปุ่ม
            document.getElementById('importBtn2').style.display = 'none'; // ซ่อนปุ่ม
            document.getElementById('importBtn3').style.display = 'none'; // ซ่อนปุ่ม


            // ส่งข้อมูลไปยัง Laravel API

            // ส่งข้อมูลทั้งหมดใน responseData ไปยัง Laravel API ในคำขอเดียว
            const promise = axios.post('/save-company-data', {
                code_company: queryData.id, // รหัสบริษัท
                status: e, // รหัสบริษัท
                sheets: e != "add_choose" ? [responseData] : [selectedData] // ข้อมูลในทุก sheet
            });

            // ใช้ Promise.all() หากมีคำขอเพิ่มเติมในอนาคต
            Promise.all([promise])
                .then(() => {

                    var checkboxes = document.querySelectorAll('input[name="selectedItems[]"]:checked');
                    checkboxes.forEach(function(checkbox) {
                        checkbox.checked = false; // เปลี่ยนค่าเป็น false
                    });

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
