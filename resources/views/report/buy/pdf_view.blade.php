<!DOCTYPE html>
<html lang="th">

<head>
    <title>สมุดรายวันทั่วไป</title>

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
                                        <th class="child-1">#</th>
                                        <th class="text-center" colspan="2">ใบกำกับภาษี</th>
                                        <th></th> <!-- ลบเส้นขอบ -->
                                        <th class=""></th>
                                        <th class="text-center">สถานประกอบการ</th>
                                        <th class="text-center">มูลค่าสินค้า</th>
                                        <th class="text-center">จำนวนเงิน</th>
                                        <th class=""></th>
                                    </tr>
                                    <tr class="table-secondary">
                                        <th></th>
                                        <th class="child-2 text-center">วัน เดือน ปี </th>
                                        <th class="child-2 text-center">เล่มที่/เลขที่</th>
                                        <th class="text-center">ชื่อผู้ขายสินค้า/ผู้ให้บริการ</th> <!-- ลบเส้นขอบ -->
                                        <th class="child-4 text-center">เลขประจำตัวผู้เสียภาษีอากรของ
                                            ผู้ขายสินค้า/ผู้ให้บริการ</th> <!-- ลบเส้นขอบ -->
                                        <th class="text-center">สำนักงานใหญ่ / สาขา</th>
                                        <th class="text-center">หรือบริการ</th>
                                        <th class="text-center">ภาษีมูลค่าเพิ่ม
                                        </th>
                                        <th class="text-center">รวม</th>
                                    </tr>
                                </thead>

                                @php
                                    $i = 1;
                                    $totalAmount = 0;
                                    $totalTax = 0;
                                    $totalSum = 0;
                                    $totalAmountNoTax = 0; // ตัวแปรสำหรับผลรวม gl_amount ที่ gl_tax = 0

                                @endphp

                                <tbody class="table-border-bottom-0">
                                    @foreach ($query as $index => $que)
                                        @php
                                            // คำนวณผลรวม
                                            $totalAmount += $que->gl_amount;
                                            $totalTax += $que->gl_tax;
                                            $totalSum += $que->gl_total;

                                            // คำนวณผลรวมเฉพาะ gl_amount ที่ gl_tax = 0
                                            if ($que->gl_tax == 0) {
                                                $totalAmountNoTax += $que->gl_amount;
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
                                            <td class="text-end">{{ number_format($que->gl_amount, 2) }}</td>
                                            <td class="text-end">{{ number_format($que->gl_tax, 2) }}</td>
                                            <td class="text-end">{{ number_format($que->gl_total, 2) }}</td>
                                        </tr>
                                    @endforeach

                                    <tr>
                                        <td colspan="5"></td>
                                        <td><strong>รวมภาษี</strong></td>
                                        <td class="text-end"><strong>{{ number_format($totalAmountNoTax, 2) }}</strong>
                                        </td>
                                        <td class="text-end"></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5"></td>
                                        <td><strong>รวมภาษี 0%</strong></td>
                                        <!-- แสดงผลรวมของ gl_amount ที่ gl_tax = 0 -->
                                        <td class="text-end"><strong>{{ number_format($totalAmount, 2) }}</strong></td>

                                        <td class="text-end"></strong></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5"></td>
                                        <td class="text-end"><strong>รวมทั้งสิ้น</strong></td>
                                        <td class="text-end">
                                            @php
                                                $total = $totalAmount + $totalAmountNoTax;

                                            @endphp

                                            <strong>{{ number_format($total, 2) }}</strong>
                                        </td>
                                        <td class="text-end"><strong>{{ number_format($totalTax, 2) }}</strong></td>
                                        <td class="text-end"><strong>{{ number_format($totalSum, 2) }}</strong></td>
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
