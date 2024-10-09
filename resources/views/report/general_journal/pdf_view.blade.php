<!DOCTYPE html>
<html lang="th">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>สมุดรายวันทั่วไป</title>
    <meta http-equiv="Content-Language" content="th" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


    <meta http-equiv="Content-Language" content="th" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Bootstrap 3.x only : DOMPDF support float, not flexbox -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <!-- thai font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-size: 8pt;
            margin: 0;
            font-family: 'Sarabun', sans-serif;
            line-height: 1;
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
        /* .table {
            width: 100%;
            border-collapse: collapse;

            margin-bottom: 10pt;
        }


        .table th,
        .table td {
            border: none;
            padding: 8px;

        }

        .table th {
            border-bottom: 1px solid #ddd;
            background-color: #f2f2f2;
            text-align: center;
        }
 */

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10pt;
        }

        .table th,
        .table td {
            border: none;
            padding: 8px;
        }

        .table th {
            border-bottom: 1px solid #ddd;
            background-color: #f2f2f2;
            text-align: center;
        }

        tr {
            page-break-inside: avoid !important;
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

        .text-end {
            text-align: right !important;
        }

        .text-start {
            text-align: left !important;
        }

        .text-initial {
            text-align: right !important;
        }

        .table td,
        .table th {
            padding: 10px;
            vertical-align: top;
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
                                            $rowspan = count($groupedData); // Calculate the number of rows for the current id
                                            // คำนวณค่ารวมของ gls_debit และ gls_credit สำหรับแต่ละกลุ่ม
                                            $totalDebit = $groupedData->sum('gls_debit');
                                            $totalCredit = $groupedData->sum('gls_credit');
                                        @endphp



                                        @foreach ($groupedData as $index => $que)
                                            <tr class="group-row">
                                                @if ($index == 0)
                                                    <!-- แสดงแค่แถวแรกของกลุ่มเดียวกัน -->
                                                    <td rowspan="{{ $rowspan }}">
                                                        {{ date('d-m-Y', strtotime($groupedData[0]->gl_date)) }}</td>
                                                    <td rowspan="{{ $rowspan }}">{{ $groupedData[0]->gl_document }}
                                                    </td>
                                                    <td rowspan="{{ $rowspan }}">
                                                        {{ $groupedData[0]->gl_company }}</td>
                                                @endif
                                                <td class="text-start">{{ $que->gls_account_name }}</td>
                                                <td class="text-end">{{ number_format($que->gls_debit, 2) }}</td>
                                                <td class="text-end">{{ number_format($que->gls_credit, 2) }}</td>
                                            </tr>
                                        @endforeach

                                        <!-- แถวสำหรับรวม -->
                                        <tr class="summary-row">
                                            <td colspan="4" class="text-end"><strong>รวม</strong></td>
                                            <td class="text-end">
                                                <strong>{{ number_format($totalDebit, 2) }}</strong>
                                            </td>
                                            <td class="text-end"><strong>{{ number_format($totalCredit, 2) }}</strong>
                                            </td>
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
