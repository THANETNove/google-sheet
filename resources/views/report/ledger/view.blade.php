@extends('layouts.appHome')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">

                <div id="printableArea">

                    <div class="card2" style="margin-bottom: 32px;">
                        @php

                            $route = Auth::check()
                                ? route('report/search-ledger')
                                : route('user-report/search-ledger', [
                                    'username' => $user->username,
                                    'password' => $user->password,
                                ]);
                        @endphp
                        <form action="{{ $route }}" method="POST" class="container-date">
                            @csrf
                            <div class="container-date">
                                <div class="col-8">
                                    <small class="text-light fw-semibold d-block mb-1">วันที่</small>
                                    <div class="input-group input-group-merge speech-to-text">
                                        <input class="form-control" type="date" id="start-date" {{-- value="{{ $startDate }}" --}}
                                            value="{{ date('Y-m-d', strtotime($startDate)) }}" name="start_date">
                                    </div>
                                </div>
                                <div class="col-8">
                                    <small class="text-light fw-semibold d-block mb-1">ถึงวันที่</small>
                                    <div class="input-group input-group-merge speech-to-text">
                                        <input class="form-control" type="date" id="end-date"
                                            value="{{ date('Y-m-d', strtotime($endDate)) }}" name="end_date">
                                    </div>
                                </div>
                            </div>
                            <div class="container-date">
                                <!-- Input สำหรับ Start Code -->
                                <div class="col-8">
                                    <small class="text-light fw-semibold d-block mb-1">รหัสเริ่มต้น</small>
                                    <div class="input-group">
                                        <input class="form-control" type="text" id="startCode" name="start_code"
                                            value="{{ old('start_code', $startCode ?? '') }}" oninput="formatCode(this)">
                                    </div>
                                </div>

                                <!-- Input สำหรับ End Code -->
                                <div class="col-8">
                                    <small class="text-light fw-semibold d-block mb-1">รหัสสิ้นสุด</small>
                                    <div class="input-group">
                                        <input class="form-control" type="text" id="endCode" name="end_code"
                                            value="{{ old('end_code', $endCode ?? '') }}" oninput="formatCode(this)">
                                    </div>
                                </div>


                                <input class="form-control" type="text" name="id" style="display: none"
                                    value="{{ $id }}">
                                <div>
                                    <button type="submit" class="btn btn-primary">ค้นหา</button>
                                </div>
                            </div>
                        </form>

                    </div>
                    @foreach ($date_query as $accountCode => $queries)
                        @php
                            $beforeTotal = !empty($queries->first()) ? $queries->first()->before_total : 0;
                            $totalDebit = $queries->sum('gls_debit');
                            $totalCredit = $queries->sum('gls_credit');
                            $totalAmount = $beforeTotal + $totalDebit + $totalCredit;

                        @endphp
                        @if ($totalAmount != 0)
                            <div class="card2" style="margin-bottom: 32px;">
                                <div class="container-company">
                                    <div class="company">
                                        {{--   <p><strong>{{ $user->company }}</strong></p> --}}
                                        <p><strong>{{ session('company_name') }}</strong></p>
                                        <p><strong>-- บัญชีแยกประเภท {{ $accountCode }} :
                                                {{ $queries[0]->gls_account_name }}
                                                --</strong></p>
                                        <p><strong>หมายเลขผู้เสียภาษี {{ $user->tax_id }}</strong></p>
                                        <p><strong> ตั้งแต่วันที่ &nbsp; {{ date('d-m-Y', strtotime($startDate)) }}
                                                &nbsp;จนถึงวันที่&nbsp; {{ date('d-m-Y', strtotime($endDate)) }}</strong>
                                        </p>
                                        {{--   <h6>{{ $beforeTotal }}</h6>
                                        <h6>{{ $totalDebit }}</h6>
                                        <h6>{{ $totalCredit }}</h6> --}}
                                    </div>
                                </div>
                                <div class="date">
                                    <p> วันเริ่มรอบบัญชี {{ $day }} {{ $monthThai }} {{ $currentYear }}</p>
                                </div>
                                <div class="table-responsive m-3">
                                    <table class="table">
                                        <thead class="text-center">
                                            <tr class="table-secondary">
                                                @if ($accountCode != '32-1001-01')
                                                    <th class="child-2">วันที่</th>
                                                @endif

                                                <th>เลขที่เอกสาร</th>
                                                <th>คำอธิบาย</th>
                                                <th>เดบิต</th>
                                                <th>เครดิต</th>
                                                <th>สะสมงวดนี้</th>
                                                <th>สะสมต้นงวด</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $accumulatedTotal = 0;
                                                $beginning_accumulation = 0;
                                                $isFirst = true;
                                                $gls_credit_sum = 0;
                                                $gls_debit_sum = 0;
                                                $i = 1;
                                            @endphp

                                            <tr>
                                                @if ($accountCode != '32-1001-01')
                                                    <td></td>
                                                @endif

                                                <td></td>
                                                <th>ยอดยกมาต้นงวด </th>
                                                <!-- Display value if $accountCode starts with 1 or 5 -->

                                                @if (in_array(substr($accountCode, 0, 1), ['1', '5']))
                                                    <th
                                                        class="text-end {{ $queries->first()->before_total < 0 ? 'error-message' : '' }}">
                                                        {{ number_format($queries->first()->before_total, 2) }}
                                                    </th>
                                                    <td></td>
                                                @endif
                                                <!-- Display value if $accountCode starts with 2, 3, or 4 -->
                                                @if (in_array(substr($accountCode, 0, 1), ['2', '3', '4']))
                                                    <td> </td>

                                                    <th
                                                        class="text-end {{ $queries->first()->before_total < 0 ? 'error-message' : '' }}">
                                                        {{ number_format($queries->first()->before_total, 2) }}
                                                    </th>
                                                @endif
                                                <td></td>
                                                <th
                                                    class="text-end {{ $queries->first()->before_total < 0 ? 'error-message' : '' }}">
                                                    {{ number_format($queries->first()->before_total, 2) }}</th>


                                            </tr>

                                            @foreach ($queries as $query)
                                                @if ($query->gls_account_code != '32-1001-01')
                                                    @php
                                                        /* $isInDateRange =
                                                            $query->gls_gl_date >
                                                                $startDate->copy()->subDay()->toDateString() &&
                                                            $query->gls_gl_date <
                                                                $endDate->copy()->addDay()->toDateString(); */
                                                        $isInDateRange =
                                                            $query->gls_gl_date >= $startDate->copy()->startOfDay() &&
                                                            $query->gls_gl_date <= $endDate->copy()->endOfDay();

                                                        $isCategory234 = in_array(substr($accountCode, 0, 1), [
                                                            '2',
                                                            '3',
                                                            '4',
                                                        ]);

                                                        //  dd( $query->gls_gl_date , $startDate->toDateString(), $endDate->toDateString());

                                                        if ($isFirst) {
                                                            if ($isInDateRange) {
                                                                $delta = $isCategory234
                                                                    ? $query->gls_credit - $query->gls_debit
                                                                    : $query->gls_debit - $query->gls_credit;
                                                                $gls_credit_sum += $query->gls_credit;
                                                                $gls_debit_sum += $query->gls_debit;
                                                                $accumulatedTotal += $delta;
                                                                $beginning_accumulation +=
                                                                    $queries->first()->before_total + $delta;
                                                            } else {
                                                                $beginning_accumulation += $queries->first()
                                                                    ->before_total;
                                                            }
                                                            $isFirst = false;
                                                        } elseif ($isInDateRange) {
                                                            $delta = $isCategory234
                                                                ? $query->gls_credit - $query->gls_debit
                                                                : $query->gls_debit - $query->gls_credit;
                                                            $gls_credit_sum += $query->gls_credit;
                                                            $gls_debit_sum += $query->gls_debit;
                                                            $accumulatedTotal += $delta;
                                                            $beginning_accumulation += $delta;
                                                        }
                                                    @endphp

                                                    @if ($isInDateRange)
                                                        <tr>
                                                            @if ($accountCode != '32-1001-01')
                                                                <td>{{ date('d-m-Y', strtotime($query->gls_gl_date)) }}
                                                                </td>
                                                            @endif
                                                            <td style="max-width: 80px;">
                                                                @php

                                                                    $glUrl = $ledgers[$query->gls_gl_code] ?? null;

                                                                @endphp

                                                                @if ($glUrl->gl_url)
                                                                    <a href="{{ $glUrl->gl_url }}" target="_blank"
                                                                        class="opan-message" rel="noopener noreferrer">
                                                                        {{ $glUrl->gl_document }}
                                                                        <span class="id-message">หน้า
                                                                            {{ $glUrl->gl_page }}</span>
                                                                    </a>
                                                                @else
                                                                    {{ $glUrl->gl_document }}
                                                                @endif
                                                            </td>
                                                            <td>{{ $glUrl->gl_description }} -
                                                                {{ $glUrl->gl_company }}
                                                            </td>
                                                            <td
                                                                class="text-end {{ $query->gls_debit < 0 ? 'error-message' : '' }}">
                                                                {{ $query->gls_debit != 0 ? number_format($query->gls_debit, 2) : '' }}
                                                            </td>
                                                            <td
                                                                class="text-end {{ $query->gls_credit < 0 ? 'error-message' : '' }}">
                                                                {{ $query->gls_credit != 0 ? number_format($query->gls_credit, 2) : '' }}
                                                            </td>
                                                            <td
                                                                class="text-end {{ $accumulatedTotal < 0 ? 'error-message' : '' }}">
                                                                {{ $accumulatedTotal != 0 ? number_format($accumulatedTotal, 2) : '' }}
                                                            </td>
                                                            <td
                                                                class="text-end {{ $beginning_accumulation < 0 ? 'error-message' : '' }}">
                                                                {{ number_format($beginning_accumulation, 2) }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endif
                                            @endforeach

                                            <tr>

                                                @if ($accountCode != '32-1001-01')
                                                    <td></td>
                                                @endif
                                                <td> </td>
                                                <th>
                                                    @if (in_array(substr($accountCode, 0, 1), ['4', '5']))
                                                        โอนเข้าบัญชีกำไรขาดทุนสะสม
                                                    @else
                                                        ยอดสะสมยกไป
                                                    @endif


                                                </th>

                                                {{--  <th
                                                        class="text-end {{ $beginning_accumulation < 0 && $beginning_accumulation != 0 ? 'error-message' : '' }}">
                                                        {{ number_format($beginning_accumulation, 2) }}
                                                    </th> --}}

                                                @if (in_array(substr($accountCode, 0, 1), ['1', '5']))
                                                    <td></td>
                                                    <th
                                                        class="text-end {{ $beginning_accumulation < 0 && $beginning_accumulation != 0 ? 'error-message' : '' }}">
                                                        {{ number_format($beginning_accumulation, 2) }}
                                                    </th>
                                                @endif
                                                <!-- Display value if $accountCode starts with 2, 3, or 4 -->
                                                @if (in_array(substr($accountCode, 0, 1), ['2', '3', '4']))
                                                    <th
                                                        class="text-end {{ $beginning_accumulation < 0 && $beginning_accumulation != 0 ? 'error-message' : '' }}">
                                                        {{ number_format($beginning_accumulation, 2) }}
                                                    </th>
                                                    <td> </td>
                                                @endif

                                                <td> </td>

                                            </tr>
                                            <tr>
                                                @if ($accountCode != '32-1001-01')
                                                    <td></td>
                                                @endif

                                                <td> </td>
                                                <th>ยอดรวม </th>


                                                @if (in_array(substr($accountCode, 0, 1), ['1', '5']))
                                                    <th
                                                        class="text-end {{ $beginning_accumulation < 0 && $beginning_accumulation != 0 ? 'error-message' : '' }}">
                                                        {{ number_format($gls_debit_sum + $queries->first()->before_total, 2) }}

                                                    </th>
                                                    <th
                                                        class="text-end {{ $beginning_accumulation < 0 && $beginning_accumulation != 0 ? 'error-message' : '' }}">
                                                        {{ number_format($gls_credit_sum + $beginning_accumulation, 2) }}

                                                    </th>
                                                @endif
                                                <!-- Display value if $accountCode starts with 2, 3, or 4 -->
                                                @if (in_array(substr($accountCode, 0, 1), ['2', '3', '4']))
                                                    <th
                                                        class="text-end {{ $beginning_accumulation < 0 && $beginning_accumulation != 0 ? 'error-message' : '' }}">
                                                        {{ number_format($gls_debit_sum + $beginning_accumulation, 2) }}

                                                    </th>
                                                    <th
                                                        class="text-end {{ $beginning_accumulation < 0 && $beginning_accumulation != 0 ? 'error-message' : '' }}">
                                                        {{ number_format($gls_credit_sum + $queries->first()->before_total, 2) }}

                                                    </th>
                                                @endif
                                                <td> </td>
                                                <td> </td>

                                            </tr>


                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    @endforeach


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
    <script>
        // ส่งค่า $user ไปยัง JavaScript
        const user = @json($user);

        // สมมุติว่าคุณต้องการแสดงค่า company ของผู้ใช้คนแรก
        document.getElementById('navbar-company').textContent = "บริษัท " + user[0].company; // แสดงค่าใน <strong> tag


        // แสดงปุ่มเมื่อเลื่อนลง
        window.onscroll = function() {
            const button = document.getElementById('scrollToTop');
            if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
                button.style.display = "block";
            } else {
                button.style.display = "none";
            }
        };

        // เมื่อคลิกปุ่มเลื่อนกลับไปยังด้านบน
        document.getElementById('scrollToTop').onclick = function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        };


        function formatCode(input) {
            let value = input.value.replace(/\D/g, ""); // ลบอักขระที่ไม่ใช่ตัวเลขทั้งหมด
            let formatted = "";

            if (value.length > 0) {
                formatted += value.substring(0, 2); // 2 ตัวแรก
            }
            if (value.length > 2) {
                formatted += "-" + value.substring(2, 6); // 4 ตัวถัดไป
            }
            if (value.length > 6) {
                formatted += "-" + value.substring(6, 8); // 2 ตัวสุดท้าย
            }

            input.value = formatted; // อัปเดตค่าใน input
        }
    </script>
@endsection
