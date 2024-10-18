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

                                <p><strong>-- รายงานภาษีซื้อ --</strong></p>
                                <p><strong>หมายเลขผู้เสียภาษี {{ $user->tax_id }}<strong></p>
                                <p><strong>เดือนภาษี {{ $vat_month }}<strong></p>
                            </div>
                        </div>
                        <form action="{{ route('report/search-buy') }}" method="POST" class="container-date">
                            @csrf
                            <div class="container-date">

                                <div class="col-8">

                                    <!-- แถวสำหรับเลือกเดือนและปี -->
                                    <div class="row">
                                        <!-- เลือกเดือน -->
                                        <div class="col-6">
                                            <small class="text-light fw-semibold d-block mb-1" for="month">เดือน:</small>
                                            <select id="month" name="month" class="form-control">
                                                <option value="1" {{ $month == '01' ? 'selected' : '' }}>มกราคม
                                                </option>
                                                <option value="2" {{ $month == '02' ? 'selected' : '' }}>
                                                    กุมภาพันธ์</option>
                                                <option value="3" {{ $month == '03' ? 'selected' : '' }}>มีนาคม
                                                </option>
                                                <option value="4" {{ $month == '04' ? 'selected' : '' }}>เมษายน
                                                </option>
                                                <option value="5" {{ $month == '05' ? 'selected' : '' }}>พฤษภาคม
                                                </option>
                                                <option value="6" {{ $month == '06' ? 'selected' : '' }}>มิถุนายน
                                                </option>
                                                <option value="7" {{ $month == '07' ? 'selected' : '' }}>กรกฎาคม
                                                </option>
                                                <option value="8" {{ $month == '08' ? 'selected' : '' }}>สิงหาคม
                                                </option>
                                                <option value="9" {{ $month == '09' ? 'selected' : '' }}>กันยายน
                                                </option>
                                                <option value="10" {{ $month == '10' ? 'selected' : '' }}>ตุลาคม
                                                </option>
                                                <option value="11" {{ $month == '11' ? 'selected' : '' }}>
                                                    พฤศจิกายน</option>
                                                <option value="12" {{ $month == '12' ? 'selected' : '' }}>ธันวาคม
                                                </option>
                                            </select>
                                        </div>

                                        <!-- เลือกปี -->
                                        <div class="col-6">
                                            <small class="text-light fw-semibold d-block mb-1" for="year">ปี:</small>
                                            <input id="year" name="year" class="form-control" type="number"
                                                value="{{ $year }}" placeholder="ป้อนปี" min="1900"
                                                max="2100">
                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden input สำหรับส่งค่าอื่นๆ เช่น ID -->
                                <input class="form-control" type="text" name="id" style="display: none"
                                    value="{{ $id }}">

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">ค้นหา</button>
                                </div>
                            </div>

                            {{-- <div class="container-date">
                                <div class="col-8">
                                    <small class="text-light fw-semibold d-block mb-1">วันที่</small>
                                    <div class="input-group input-group-merge speech-to-text">
                                        <input class="form-control" type="date" id="start-date"
                                            value="{{ date('Y-m-d', strtotime($startDate)) }}" name="start_date">
                                    </div>
                                </div>

                                <input class="form-control" type="text" name="id" style="display: none"
                                    value="{{ $id }}">
                                <div>
                                    <button type="submit" class="btn btn-primary">ค้นหา</button>
                                </div>
                            </div> --}}
                        </form>
                        <div class="date">
                            <p> วันเริ่มรอบบัญชี {{ $day }} {{ $monthThai }} {{ $currentYear }}</p>
                            <a href="{{ url('/buy-pdf/' . $id . '/' . urlencode($month) . '/' . urlencode($year)) }}"
                                target="_blank" class="btn btn-primary">
                                <i class='bx bxs-file-pdf'></i>&nbsp; PDF
                            </a>
                            <a href="{{ url('/buy-excel/' . $id . '/' . urlencode($month) . '/' . urlencode($year)) }}"
                                class="btn btn-primary">
                                <i class='bx bxs-file'></i>&nbsp; Excel
                            </a>
                        </div>
                        <div class="table-responsive m-3">
                            <table class="table">

                                <thead>

                                    <tr class="table-secondary">
                                        <th class="child-1 text-center-vertical" rowspan="2">#</th>
                                        <th class="text-center" colspan="2">ใบกำกับภาษี</th>
                                        <th class="text-center-vertical" rowspan="2">ชื่อผู้ขายสินค้า/ผู้ให้บริการ</th>
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
                                            <td>{{ date('d-m-Y', strtotime($que->gl_date)) }} <br>
                                                {{--  {{ date('d-m-Y', strtotime($que->gl_taxmonth)) }} --}}</td>
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
