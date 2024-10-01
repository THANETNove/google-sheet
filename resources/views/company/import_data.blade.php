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

        console.log('PHP query:', queryData.id_apps_script); // แสดงข้อมูลจาก $query

        sheets.forEach(sheet => {
            const url =
                `https://script.google.com/macros/s/${queryData.id_apps_script}/exec?action=${action}&sheet=${sheet}&id_sheet=${id_sheet}`;
            axios.get(url)
                .then(response => {
                    console.log(`${sheet}:`, response.data);
                    // สามารถใช้ queryData ในการประมวลผลข้อมูลเพิ่มเติมได้ที่นี่
                })
                .catch(error => console.error(`Error in ${sheet}:`, error));
        });
    </script>
@endsection
