@extends('layouts.appHome')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">

                <div id="printableArea">
                    <div class="card">

                        <div class="container-company">
                            <div class="company">
                                <p><strong>{{ $user->company }}</strong></p>

                                <p><strong>-- สมุดบัญชีรายงานซื้อ --</strong></p>
                                <p><strong>หมายเลขผู้เสียภาษี {{ $user->tax_id }}<strong></p>
                                <p><strong>เดือนภาษี {{ $vat_month }}<strong></p>
                            </div>
                        </div>
                        <form action="{{ route('report/search-buy') }}" method="POST" class="container-date">
                            @csrf
                            <div class="container-date">
                                <div class="col-8">
                                    <small class="text-light fw-semibold d-block mb-1">วันที่</small>
                                    <div class="input-group input-group-merge speech-to-text">
                                        <input class="form-control" type="date" id="start-date"
                                            value="{{ date('Y-m-d', strtotime($startDate)) }}" name="start_date">
                                    </div>
                                </div>
                                {{-- <div class="col-8">
                                    <small class="text-light fw-semibold d-block mb-1">ถึงวันที่</small>
                                    <div class="input-group input-group-merge speech-to-text">
                                        <input class="form-control" type="date" id="end-date"
                                            value="{{ date('Y-m-d', strtotime($endDate)) }}" name="end_date">
                                    </div>
                                </div> --}}
                                <input class="form-control" type="text" name="id" style="display: none"
                                    value="{{ $id }}">
                                <div>
                                    <button type="submit" class="btn btn-primary">ค้นหา</button>
                                </div>
                            </div>
                        </form>
                        <div class="date">
                            <p> วันเริ่มรอบบัญชี {{ $day }} {{ $monthThai }} {{ $currentYear }}</p>
                            <a href="{{ url('/buy-pdf/' . $id . '/' . urlencode($startDate)) }}" target="_blank"
                                class="btn btn-primary">
                                <i class='bx bxs-file-pdf'></i>&nbsp; PDF
                            </a>
                            <a href="{{ url('/buy-excel/' . $id . '/' . urlencode($startDate)) }}" class="btn btn-primary">
                                <i class='bx bxs-file'></i>&nbsp; Excel
                            </a>
                        </div>
                        <div class="table-responsive m-3">
                            <table class="table">
                                <thead>
                                    <tr class="table-secondary">
                                        <th class="child-1">#</th>
                                        <th class="text-center" colspan="2">ใบกำกับภาษี</th>
                                        <th class="no-border"></th> <!-- ลบเส้นขอบ -->
                                        <th class=""></th>
                                        <th class="text-center">สถานประกอบการ</th>
                                        <th class="text-center">มูลค่าสินค้า</th>
                                        <th class="text-center">จำนวนเงิน</th>
                                        <th class=""></th>
                                    </tr>
                                    <tr class="table-secondary">
                                        <th></th>
                                        <th class="child-2 text-center">วันที่</th>
                                        <th class="child-2 text-center">เลขที่เอกสาร</th>
                                        <th class="text-center">บริษัท</th> <!-- ลบเส้นขอบ -->
                                        <th class="text-center">หมายเลขผู้เสียภาษี</th> <!-- ลบเส้นขอบ -->
                                        <th class="text-center">สาขา</th>
                                        <th class="text-center">จำนวน</th>
                                        <th class="text-center">ภาษี</th>
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
                                            <td>{{ $que->gl_taxid }}</td>
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
                                        <td><strong>รวมภาษี 0%</strong></td> <!-- แสดงผลรวมของ gl_amount ที่ gl_tax = 0 -->
                                        <td class="text-end"><strong>{{ number_format($totalAmount, 2) }}</strong></td>

                                        <td class="text-end"></strong></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5"></td>
                                        <td><strong>รวมทั้งสิ้น</strong></td>
                                        <td class="text-end">
                                            @php
                                                $total = $totalAmount + $totalAmountNoTax;

                                            @endphp

                                            <strong>{{ number_format($total, 2) }}</strong>
                                        </td>
                                        <td><strong>{{ number_format($totalTax, 2) }}</strong></td>
                                        <td><strong>{{ number_format($totalSum, 2) }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- เพิ่มปุ่มเลื่อนกลับไปยังด้านบน -->
    <button id="scrollToTop" class="btn btn-primary" style="position: fixed; bottom: 20px; right: 20px; display: none;">
        <i class='bx bxs-arrow-to-top'></i>
        &nbsp;
        Top
    </button>
    @include('layouts.scrollToTop')
    <script>
        // ส่งค่า $user ไปยัง JavaScript
        const user = @json($user);

        // สมมุติว่าคุณต้องการแสดงค่า company ของผู้ใช้คนแรก
        document.getElementById('navbar-company').textContent = "บริษัท " + user[0].company; // แสดงค่าใน <strong> tag
    </script>
@endsection
