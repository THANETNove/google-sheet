<!DOCTYPE html>
<html lang="th">

<head>
    <title>รายงานภาษีขาย</title>

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
                                <p><strong>-- รายงานภาษีขาย --</strong></p>
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
                                        <th class="child-1 text-center-vertical" rowspan="2">#</th>
                                        <th class="text-center" colspan="2">ใบกำกับภาษี</th>
                                        <th class="text-center-vertical" rowspan="2">
                                            ชื่อผู้ซื้อสินค้า/ผู้รับบริการ
                                        </th>
                                        <th class="text-center-vertical" rowspan="2">เลขประจำตัวผู้เสียภาษีอากรของ
                                            ผู้ซื้อสินค้า/ผู้รับบริการ</th>
                                        <th class="text-center">สถานประกอบการ</th>
                                        <th class="text-center">มูลค่าสินค้า</th>
                                        <th class="text-center">จำนวนเงิน</th>
                                        <th class="text-center-vertical" rowspan="2">รวม</th>
                                    </tr>
                                    <tr class="table-secondary">

                                        <th class="child-2 text-center">วันที่</th>
                                        <th class="child-2 text-center">เล่มที่/เลขที่</th>
                                        <th class="text-center">สำนักงานใหญ่ /สาขา </th>
                                        <th class="text-center">หรือบริการ </th>
                                        <th class="text-center">ภาษีมูลค่าเพิ่ม</th>
                                    </tr>
                                </thead>

                                @php
                                    $i = 1;
                                    $totalAmount = 0;
                                    $totalTax = 0;
                                    $totalNoTax = 0;
                                    $totalAmountNoTax = 0; // ตัวแปรสำหรับผลรวม gl_amount ที่ gl_tax = 0
                                    $totalNoTaxSum = 0;
                                    $totalTaxSum = 0;

                                @endphp

                                <tbody class="table-border-bottom-0">
                                    @foreach ($query as $index => $que)
                                        @php
                                            // คำนวณผลรวม

                                            // คำนวณผลรวมเฉพาะ gl_amount ที่ gl_tax = 0
                                            if ($que->gl_tax == 0) {
                                                $totalAmountNoTax += $que->gl_amount;
                                                $totalNoTax += $que->gl_tax;
                                                $totalNoTaxSum += $que->gl_total;
                                            }
                                            if ($que->gl_tax > 0) {
                                                $totalAmount += $que->gl_amount;
                                                $totalTax += $que->gl_tax;
                                                $totalTaxSum += $que->gl_total;
                                            }
                                        @endphp

                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ date('d-m-Y', strtotime($que->gl_date)) }}</td>
                                            <td>
                                                {{ $que->gl_document }}
                                            </td>
                                            <td>{{ $que->gl_company }}</td>
                                            <td class="monospace">{{ $que->gl_taxid }}</td>
                                            <td>{{ $que->gl_branch }}</td>
                                            <td class="text-end {{ $que->gl_amount < 0 ? 'error-message' : '' }}">
                                                {{ number_format($que->gl_amount, 2) }}
                                            </td>
                                            <td class="text-end">{{ number_format($que->gl_tax, 2) }}</td>
                                            <td class="text-end">{{ number_format($que->gl_total, 2) }}</td>
                                        </tr>
                                    @endforeach

                                    <tr>
                                        <td colspan="5"></td>
                                        <td class="text-end"><strong>รวมภาษี</strong></td>
                                        <td class="text-end {{ $totalAmount < 0 ? 'error-message' : '' }}">
                                            <strong>{{ number_format($totalAmount, 2) }}</strong>
                                        </td>
                                        <td class="text-end"><strong>{{ number_format($totalTax, 2) }}</strong></td>
                                        <td class="text-end"><strong>{{ number_format($totalTaxSum, 2) }}</strong></td>

                                    </tr>
                                    <tr>
                                        <td colspan="5"></td>
                                        <td class="text-end"><strong>รวมภาษี 0%</strong></td>
                                        <!-- แสดงผลรวมของ gl_amount ที่ gl_tax = 0 -->
                                        <td class="text-end {{ $totalAmountNoTax < 0 ? 'error-message' : '' }}">
                                            <strong>{{ number_format($totalAmountNoTax, 2) }}</strong>
                                        </td>

                                        <td class="text-end"><strong>{{ number_format($totalNoTax, 2) }}</strong></td>
                                        <td class="text-end"><strong>{{ number_format($totalNoTaxSum, 2) }}</strong>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td colspan="5"></td>
                                        <td class="text-end"><strong>รวมทั้งสิ้น</strong></td>
                                        <td class="text-end">
                                            @php
                                                $totalSum = $totalAmount + $totalAmountNoTax;
                                                $totalSumTax = $totalTax + $totalNoTax;
                                                $totalSumNoTax = $totalSum + $totalSumTax;

                                            @endphp

                                            <strong>{{ number_format($totalSum, 2) }}</strong>
                                        </td>
                                        <td class="text-end"><strong>{{ number_format($totalSumTax, 2) }}</strong></td>
                                        <td class="text-end"><strong>{{ number_format($totalSumNoTax, 2) }}</strong>
                                        </td>
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
