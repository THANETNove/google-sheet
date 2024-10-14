<!DOCTYPE html>
<html lang="th">

<head>
    <title>สมุดรายวันทั่วไป</title>

    {{--     @include('layouts.head_pdf') --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Language" content="th" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ URL::asset('/assets/img/icons/icon-2.png') }}" />

    <style>
        @font-face {
            font-family: 'Sarabun';
            src: url('/fonts/sarabun/Sarabun-Regular.woff2') format('woff2'),
                url('/fonts/sarabun/Sarabun-Regular.woff') format('woff'),
                url('/fonts/sarabun/Sarabun-Regular.ttf') format('truetype');
            font-weight: 400;
            font-style: normal;
        }

        @font-face {
            font-family: 'Sarabun';
            src: url('/fonts/sarabun/Sarabun-Bold.woff2') format('woff2'),
                url('/fonts/sarabun/Sarabun-Bold.woff') format('woff'),
                url('/fonts/sarabun/Sarabun-Bold.ttf') format('truetype');
            font-weight: 700;
            font-style: normal;
        }

        @font-face {
            font-family: 'Sarabun';
            src: url('/fonts/sarabun/Sarabun-Light.woff2') format('woff2'),
                url('/fonts/sarabun/Sarabun-Light.woff') format('woff'),
                url('/fonts/sarabun/Sarabun-Light.ttf') format('truetype');
            font-weight: 300;
            font-style: normal;
        }

        body {
            font-size: 9pt !important;
            font-family: 'Sarabun', sans-serif;

            font-weight: 400;
            font-style: normal !important;
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



        .table {
            background-color: transparent;
            width: 100%;
            margin-bottom: 1rem;
            color: #697a8d;
            border: 1px solid #d9dee3;
            border-collapse: collapse;

            font-size: 12px !important;
        }

        .table th,
        .table td {
            padding: 0.625rem 0.5rem;
            border-bottom: 1px solid #d9dee3;
        }

        .table>thead {
            vertical-align: bottom;
            background-color: #f9fafb;
            /* ใช้สีสำหรับหัวตาราง */
            color: #697a8d;
        }

        .table-striped>tbody>tr:nth-of-type(odd) {
            background-color: #f9fafb;
            /* สีพื้นหลังแถวคู่ */
        }

        .table-hover>tbody>tr:hover {
            background-color: rgba(67, 89, 113, 0.06);
            /* สีเมื่อ hover */
            color: #697a8d;
        }

        .table-active {
            background-color: rgba(67, 89, 113, 0.1);
            /* สีแถวที่ active */
            color: #697a8d;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #d9dee3;
            /* เส้นขอบตาราง */
        }

        .table-borderless th,
        .table-borderless td {
            border: none;
            /* ตารางแบบไม่มีเส้นขอบ */
        }

        .caption-top {
            caption-side: top;
        }

        .table-sm th,
        .table-sm td {
            padding: 0.3125rem 0.625rem;
            /* ลดขนาด padding สำหรับ table-sm */
        }




        thead,
        tbody,
        tfoot,
        tr,
        td,
        th {
            border-color: inherit !important;
            border-style: solid !important;
            border-width: thin !important;

        }

        .table-secondary {
            background-color: #e7e9ed;
            /* สีพื้นหลัง */
            color: #435971;
            /* สีข้อความ */
            border-color: #d7dbe1;
            /* สีขอบ */
        }

        .table-secondary tr:hover {
            background-color: #dde0e6;
            /* สีเมื่อ hover */
            color: #435971;
            /* สีข้อความเมื่อ hover */
        }

        .table-secondary tr.active {
            background-color: #d7dbe1;
            /* สีแถวที่ active */
            color: #435971;
        }

        .table-secondary tr:nth-child(even) {
            background-color: #e2e5e9;
            /* สีแถวที่ striped */
            color: #435971;
        }



        .table td,
        .table th {
            vertical-align: top;
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



        .child-1 {
            width: auto !important;
            word-wrap: break-word !important;
            white-space: normal !important;
            /* กำหนดความกว้างของคอลัมน์วันที่ */
        }

        .child-2 {
            width: 80px !important;
            word-wrap: break-word !important;
            white-space: normal !important;

        }


        .child-3 {
            width: 100px !important;
            word-wrap: break-word !important;
            white-space: normal !important;
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

        .mt-16 {
            margin-top: 64px !important;
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
                                <p><strong>{{ $user->company }}</strong></p>
                                <p><strong>-- สมุดรายวันทั่วไป --</strong></p>
                                <p><strong> ตั้งแต่วันที่ {{ date('d-m-Y', strtotime($startDate)) }} จนถึงวันที่
                                        {{ date('d-m-Y', strtotime($endDate)) }}</strong></p>
                            </div>

                        </div>
                        <div class="date">
                            <p> วันเริ่มรอบบัญชี {{ $day }} {{ $monthThai }} {{ $currentYear }}</p>
                            <p>หมายเลขผู้เสียภาษี {{ $user->tax_id }}</p>
                        </div>
                        <div class="table-responsive m-3">

                            <table class="table">
                                <thead>
                                    <tr class="table-secondary">
                                        <th class="child-1">#</th>
                                        <th class="child-2">วันที่</th>
                                        <th class="child-3">เลขที่เอกสาร</th>
                                        <th>บริษัท</th>
                                        <th>เดบิต</th>
                                        <th>เครดิต</th>
                                    </tr>
                                </thead>

                                <tbody style="page-break-inside: avoid;">
                                    <!-- ป้องกันการแบ่งข้อมูลที่เป็นกลุ่มออกจากกัน -->
                                    @php
                                        $i = 1;
                                    @endphp

                                    @foreach ($query as $ledger)
                                        @php
                                            $totalDebit = 0;
                                            $totalCredit = 0;
                                        @endphp

                                        <tr style="page-break-inside: avoid;"> <!-- ข้อมูลหลักของ id -->
                                            <td>{{ $i++ }}</td>
                                            <td>{{ date('d-m-Y', strtotime($ledger->gl_date)) }}</td>
                                            <td>{{ $ledger->gl_document }}</td>
                                            <td>{{ $ledger->gl_company }}&nbsp;-&nbsp;{{ $ledger->gl_description }}
                                            </td>
                                            <td class="text-end"></td> <!-- Placeholder for subs -->
                                            <td class="text-end"></td>
                                        </tr>

                                        <!-- Loop ข้อมูลย่อยที่เกี่ยวข้องกับ id เดียวกัน -->
                                        @foreach ($ledger->subs as $sub)
                                            <tr style="page-break-inside: avoid;">
                                                <td class="hide-column"></td>
                                                <td class="hide-column"></td>
                                                <td class="hide-column"></td>
                                                <td>{{ $sub->gls_account_name }}</td>
                                                <td class="text-end">{{ number_format($sub->gls_debit, 2) }}</td>
                                                <td class="text-end">{{ number_format($sub->gls_credit, 2) }}</td>
                                            </tr>

                                            @php
                                                // สะสมผลรวม
                                                $totalDebit += $sub->gls_debit;
                                                $totalCredit += $sub->gls_credit;
                                            @endphp
                                        @endforeach

                                        @php
                                            // ตรวจสอบว่าผลรวมเท่ากันหรือไม่
                                            $isEqual = number_format($totalDebit, 2) == number_format($totalCredit, 2);
                                        @endphp

                                        <!-- แสดงผลรวม -->
                                        <tr
                                            style="page-break-inside: avoid; @if (!$isEqual) background-color: #ffcccc; @endif">
                                            <td colspan="4" class="text-end"><strong>รวม</strong></td>
                                            <td class="text-end"><strong>{{ number_format($totalDebit, 2) }}</strong>
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
