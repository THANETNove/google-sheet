<!DOCTYPE html>
<html lang="th">

<head>
    <title>รายงานภาษีซื้อ</title>

    @include('layouts.head_pdf')
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
                                <p><strong>-- รายงานภาษีซื้อ --</strong></p>
                                <p><strong> เดือนภาษี {{ $vat_month }}</strong></p>

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
                                        <th class="child-1 text-center-vertical" rowspan="2">#</th>
                                        <th class="text-center" colspan="2">ใบกำกับภาษี</th>
                                        <th class="text-center-vertical" rowspan="2">ชื่อผู้ขายสินค้า/ผู้ให้บริการ
                                        </th>
                                        <th class="text-center-vertical child-4" rowspan="2">
                                            เลขประจำตัวผู้เสียภาษีอากรของ
                                            ผู้ขายสินค้า/ผู้ให้บริการ</th>
                                        <th class="text-center">สถานประกอบการ</th>
                                        <th class="text-center">มูลค่าสินค้า</th>
                                        <th class="text-center">จำนวนเงิน</th>
                                        <th class="text-center-vertical" rowspan="2">รวม</th>
                                    </tr>
                                    <tr class="table-secondary">
                                        <th class="child-2 text-center">วัน เดือน ปี</th>
                                        <th class="child-3 text-center">เล่มที่/เลขที่</th>
                                        <th class=" text-center">สำนักงานใหญ่ / สาขา</th>
                                        <th class="text-center">หรือบริการ</th>
                                        <th class="text-center">ภาษีมูลค่าเพิ่ม</th>
                                    </tr>
                                </thead>

                                @php
                                    $i = 1;
                                    $totalAmount = 0;
                                    $totalTax = 0;
                                    $totalSum = 0;

                                @endphp

                                <tbody class="table-border-bottom-0">
                                    @foreach ($query as $index => $que)
                                        @php
                                            // คำนวณผลรวม
                                            $totalAmount += $que->gl_amount;
                                            $totalTax += $que->gl_tax;

                                        @endphp

                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ date('d-m-Y', strtotime($que->gl_date)) }} {{-- <br>
                                                {{ date('d-m-Y', strtotime($que->gl_taxmonth)) }} --}}</td>
                                            <td style="max-width: 80px; word-wrap: break-word; white-space: normal;">
                                                @if ($que->gl_url)
                                                    <a href="{{ $que->gl_url }}" target="_blank" class="opan-message"
                                                        rel="noopener noreferrer">
                                                        {{ $que->gl_document }}
                                                        <span class="id-message">หน้า {{ $que->gl_page }}</span>
                                                    </a>
                                                @else
                                                    {{ $que->gl_document }}
                                                @endif
                                            </td>
                                            <td>{{ $que->gl_company }}</td>
                                            <td class="monospace">{{ $que->gl_taxid }}</td>

                                            <td>{{ $que->gl_branch }}</td>
                                            <td class="text-end">{{ number_format($que->gl_amount, 2) }}</td>
                                            <td class="text-end">{{ number_format($que->gl_tax, 2) }}</td>
                                            <td class="text-end">{{ number_format($que->gl_total, 2) }}</td>
                                        </tr>
                                    @endforeach

                                    <tr>
                                        <td colspan="5"></td>
                                        <td class="text-end"><strong>รวมทั้งสิ้น</strong></td>
                                        <td class="text-end">
                                            @php
                                                $totalAll = $totalAmount + $totalTax;

                                            @endphp

                                            <strong>{{ number_format($totalAmount, 2) }}</strong>
                                        </td>
                                        <td class="text-end"><strong>{{ number_format($totalTax, 2) }}</strong></td>
                                        <td class="text-end"><strong>{{ number_format($totalAll, 2) }}</strong></td>
                                    </tr>
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
