<!DOCTYPE html>
<html lang="th">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>สมุดรายวันทั่วไป</title>
    <meta http-equiv="Content-Language" content="th" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />




    <!-- ฟอนต์ภาษาไทย -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">

    <style>
        @font-face {
            font-family: 'THSarabunNew';
            src: url("{{ asset('fonts/THSarabunNew.ttf') }}") format('truetype');
            font-style: normal;
            font-weight: normal;
        }

        body {
            font-size: 8pt;
            margin: 0;
            /*   font-family: 'THSarabunNew' !important; */
            font-family: 'Sarabun', 'Noto Sans Thai', sans-serif;
            /* ฟอนต์ภาษาไทย */
        }


        .container-company {
            display: flex;
            justify-content: space-between;
            /* จัดให้อยู่ทั้งสองด้าน */
            align-items: center;
            /* จัดให้อยู่ในแนวเดียวกัน */
            padding: 10px;
            /* ระยะห่างรอบๆ */
        }

        .company {

            flex: 1;
            /* ให้ company มีพื้นที่มากที่สุด */
            text-align: center;
            /* จัดกึ่งกลาง */
        }

        .date {

            position: absolute;
            right: 16px;
            top: 16px;
        }

        /* ตาราง */
        .table {
            width: 100%;
            border-collapse: collapse;
            /* ป้องกันไม่ให้เส้นขอบตารางทับซ้อนกัน */
            margin-bottom: 10pt;
            /* ระยะห่างระหว่างตาราง */
        }

        /* ซ่อนเส้นขอบแนวตั้ง */
        .table th,
        .table td {
            border: none;
            padding: 8px;
            /* ลบเส้นขอบ */
        }

        .table th {
            border-bottom: 1px solid #ddd;
            /* เส้นขอบแนวนอนด้านล่างของหัวตาราง */
            background-color: #f2f2f2;
            /* สีพื้นหลังของหัวตาราง */
            text-align: center;
            /* จัดกลางข้อความในหัวตาราง */
        }

        /* สไตล์แถวของตาราง */
        .table tbody tr:hover {
            background-color: #e9ecef;
            /* เปลี่ยนสีเมื่อชี้เมาส์ไปที่แถว */
        }

        /* ให้ข้อความอยู่กลางในเซลล์ */
        .table td {
            text-align: center;
            /* จัดกลางข้อความในเซลล์ */
        }

        /* ระยะห่างระหว่างตาราง */
        .table-responsive {
            overflow: auto;
            /* ยอมให้เลื่อน */
        }

        .mt-32 {
            margin-bottom: 32px;
            /* ระยะห่างด้านล่าง */
        }

        /* สไตล์แถวรวม */
        .table tfoot {
            font-weight: bold;
            /* ทำให้ข้อความเป็นตัวหนา */
            background-color: #f9f9f9;
            /* สีพื้นหลังของแถวรวม */
        }

        /* เส้นขอบแนวนอนสำหรับแถวข้อมูล */
        .table tr {
            border-bottom: 1px solid #ddd;
            /* เส้นขอบแนวนอนสำหรับแต่ละแถว */
        }

        /* กำหนดขนาดคอลัมน์โดยตรง */
        .col-1 {
            width: 8.33%;
            /* 1/12 ของความกว้างเต็ม */
        }

        .col-2 {
            width: 16.66%;
            /* 2/12 ของความกว้างเต็ม */
        }

        .col-3 {
            width: 25%;
            /* 3/12 ของความกว้างเต็ม */
        }

        .col-4 {
            width: 33.33%;
            /* 4/12 ของความกว้างเต็ม */
        }

        .col-5 {
            width: 41.66%;
            /* 5/12 ของความกว้างเต็ม */
        }

        .col-6 {
            width: 50%;
            /* 6/12 ของความกว้างเต็ม */
        }
    </style>


</head>

<body>
    <div class="container-xxl flex-grow-1 container-p-y ">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">

                <div id="printableArea">
                    <div class="card">

                        <div class="container-company">
                            <div class="company">
                                <p><strong>{{ $user[0]->company }}</strong></p>
                                <p><strong>-- สมุดรายวันทั่วไป --</strong></p>
                                <p><strong> ตั้งแต่วันที่ {{ date('d-m-Y', strtotime($startDate)) }} จนถึงวันที่
                                        {{ date('d-m-Y', strtotime($endDate)) }}</strong></p>
                            </div>

                        </div>
                        <div class="date">
                            <p> วันเริ่มรอบบัญชี {{ $day }} {{ $monthThai }} {{ $currentYear }}</p>
                        </div>
                        <div class="table-responsive m-3">
                            <table class="table">
                                <thead>
                                    <tr class="table-secondary">
                                        <th class="col-2">วันที่</th>
                                        <th class="col-1">เลขที่เอกสาร</th>
                                        <th class="col-5">บริษัท</th>
                                        <th class="col-2">คำอธิบาย</th>
                                        <th class="col-2">เดบิต</th>
                                        <th class="col-2">เครดิต</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @php
                                        $previousId = null;
                                        $groupedQuery = $query->groupBy('id'); // Group the data by id
                                    @endphp

                                    @foreach ($groupedQuery as $id => $groupedData)
                                        @php
                                            $rowspan = count($groupedData) + 1; // Calculate the number of rows for the current id
                                            // คำนวณค่ารวมของ gls_debit และ gls_credit สำหรับแต่ละกลุ่ม
                                            $totalDebit = $groupedData->sum('gls_debit');
                                            $totalCredit = $groupedData->sum('gls_credit');
                                        @endphp

                                        @foreach ($groupedData as $index => $que)
                                            <tr>
                                                @if ($index === 0)
                                                    <!-- Display rowspan for the first row of each group -->
                                                    <td rowspan="{{ $rowspan }}">
                                                        {{ date('d-m-Y', strtotime($que->gl_date)) }}</td>
                                                    <td rowspan="{{ $rowspan }}">{{ $que->gl_document }}</td>
                                                    <td rowspan="{{ $rowspan }}">{{ $que->gl_company }}</td>
                                                @endif
                                                <td>{{ $que->gls_account_name }}</td>
                                                <td>{{ number_format($que->gls_debit, 2) }}</td>
                                                <td>{{ number_format($que->gls_credit, 2) }}</td>
                                            </tr>
                                        @endforeach

                                        <!-- เพิ่มแถวสำหรับผลรวมใต้ข้อมูล -->
                                        <tr>
                                            <td><strong>รวม</strong></td>
                                            <td><strong>{{ number_format($totalDebit, 2) }}</strong></td>
                                            <td> <strong>{{ number_format($totalCredit, 2) }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>

</html>
