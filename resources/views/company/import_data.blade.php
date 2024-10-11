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
                        <div class="col-md-12 ">

                            <h4 class="mb-3">บริษัท {{ $query->company }} </h4>
                            <div id="error-message" class="alert alert-danger mb-4" style="display: none;"> </div>


                            <div class="justify-importBtn">
                                <button id="importBtn" type="button" onclick="confirmImport('add_delete')"
                                    class="btn rounded-pill btn-icon btn btn-outline-primary  mt-3 importBtn"
                                    style="display: none;">
                                    <i class='bx bx-import'></i>
                                    <span class="tooltip-message">
                                        นำข้อมูลเข้าและลบข้อมูลเก่า
                                    </span>
                                </button>

                                <button id="importBtn2" type="button" onclick="confirmImport('add_new')"
                                    class="btn rounded-pill btn-icon btn btn-outline-primary mt-3 importBtn"
                                    style="display: none;">
                                    <i class='bx bx-import'></i>
                                    <span class="tooltip-message">
                                        นำข้อมูลเข้าแค่อันใหม่
                                    </span>
                                </button>
                            </div>



                            <button id="spinner" class="rounded-pill btn btn-outline-primary" type="button">
                                <span class=" spinner-border spinner-border-sm" aria-hidden="true"></span>
                                <span role="status">Loading...</span>
                            </button>
                            <button id="uploading" class="rounded-pill btn btn-outline-primary" type="button"
                                style="display: none;">
                                <span class="spinner-border text-success  spinner-border-sm" aria-hidden="true"></span>
                                <span role="status">Uploading...</span>
                            </button>


                        </div>
                    </div>


                    <div class="table-responsive  table-min-height">
                        <h5 class="card-header">ข้อมูลใน Google Sheet </h5>
                        <div class="col m-3">
                            <input type="text" class="form-control" id="defaultFormControlInput"
                                oninput="searchData(this.value)" placeholder="Search"
                                aria-describedby="defaultFormControlHelp">
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="m-3">
                                {{--  <button id="importBtn3" type="button" class="btn btn-primary" style="display: none;"
                                    onclick="confirmImport('add_choose')">
                                    <i class='bx bx-import'></i>&nbsp; นำข้อมูลที่เลือก
                                </button> --}}
                                <button id="importBtn3" type="button" onclick="confirmImport('add_choose')"
                                    class="btn rounded-pill btn-icon btn btn-outline-primary importBtn"
                                    style="display: none;">
                                    <i class='bx bx-import'></i>
                                    <span class="tooltip-message">
                                        นำข้อมูลทเฉพาะที่เลือก
                                    </span>
                                </button>
                            </div>
                        </div>
                        <table class="table" id="dataTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ลำดับ</th>
                                    <th>GL_Code</th>
                                    <th>GL_Report_VAT</th>
                                    <th>GL_Date</th>
                                    <th>GL_Document</th>
                                    <th>GL_Company</th>
                                    <th>GL_TaxID</th>
                                    <th>GL_Branch</th>
                                    <th>GL_Amount</th>
                                    <th>GL_Tax</th>
                                    <th>GL_Total</th>
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

    <!-- เพิ่มปุ่มเลื่อนกลับไปยังด้านบน -->
    <button id="scrollToTop" class="btn btn-primary" style="position: fixed; bottom: 20px; right: 20px; display: none;">
        <i class='bx bxs-arrow-to-top'></i>
        &nbsp;
        return to top
    </button>

    @include('layouts.scrollToTop')

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

                    console.log("response.data", response.data);


                    endImport();
                    displayData(responseData.GeneralLedger)
                })
                .catch(error => {

                    console.error(`Error in request:`, error.response ? error.response.data : error.message);
                    const errorMessageElement = document.getElementById('error-message');
                    errorMessageElement.textContent = error.response ? error.response.data.message : error
                        .message; // ใช้ข้อความจาก server หรือข้อความ error ทั่วไป
                    errorMessageElement.style.display = 'block'; // แสดง element

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

            data.forEach((item, index) => {
                const row = document.createElement('tr');

                // สร้างเซลล์ GL_Code
                const glCodeCell = document.createElement('td');
                glCodeCell.textContent = item.GL_Code;

                // ตรวจสอบความยาวของ GL_Code
                if (item.GL_Code.length > 8) {
                    glCodeCell.style.backgroundColor = '#ff3933'; // เปลี่ยนพื้นหลังเป็นสีแดง
                    glCodeCell.style.color = '#FFFFFF'; // เปลี่ยนพื้นหลังเป็นสีแดง
                }

                // สร้างแถวในตาราง
                row.innerHTML = `
            <td>
                <input type="checkbox" name="selectedItems[]" value="${item.GL_Code}" id="check_${index}">
            </td>
            <td>${index + 1}</td>
        `;

                // เพิ่มเซลล์ GL_Code และเซลล์อื่นๆ
                row.appendChild(glCodeCell);
                row.appendChild(createCell(item.GL_Report_VAT));
                row.appendChild(createCell(item.GL_Date));
                row.appendChild(createCell(item.GL_Document));
                row.appendChild(createCell(item.GL_Company));
                row.appendChild(createCell(item.GL_TaxID));
                row.appendChild(createCell(item.GL_Branch));
                row.appendChild(createCell(item.GL_Amount));
                row.appendChild(createCell(item.GL_Tax));
                row.appendChild(createCell(item.GL_Total));


                // A,C,D,E,H,I,J,S,T,U
                // เพิ่มแถวไปยัง tableBody
                tableBody.appendChild(row);
            });
        }

        function createCell(content) {
            const cell = document.createElement('td');
            cell.textContent = content;
            return cell;
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


                console.log("selectedData", selectedData);
            }
            // แสดง Progress Bar
            document.getElementById('uploading').style.display = 'inline-block';
            document.getElementById('importBtn').style.display = 'none'; // ซ่อนปุ่ม
            document.getElementById('importBtn2').style.display = 'none'; // ซ่อนปุ่ม
            document.getElementById('importBtn3').style.display = 'none'; // ซ่อนปุ่ม


            // ส่งข้อมูลไปยัง Laravel API


            // ส่งข้อมูลทั้งหมดใน responseData ไปยัง Laravel API ในคำขอเดียว
            const promise = axios.post('{{ route('save-company-data') }}', {
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
