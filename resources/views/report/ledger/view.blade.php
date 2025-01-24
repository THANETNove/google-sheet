@extends('layouts.appHome')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">

                <div id="printableArea">

                    <div class="card" style="margin-bottom: 32px;">
                        @php

                            $route =
                                Auth::check() && Auth::user()->status == 1
                                    ? route('report/search-ledger')
                                    : isset($user->username) &&
                                        isset($user->password) &&
                                        route('user-report/search-ledger', [
                                            'username' => $user->username,
                                            'password' => $user->password,
                                        ]);
                            /* $url_export_pdf =
                                Auth::check() && Auth::user()->status == 1
                                    ? url('/sell-pdf/' . $id . '/' . urlencode($startDate) . '/' . urlencode($endDate))
                                    : url(
                                            'user-sell-pdf/' .
                                                $id .
                                                '/' .
                                                urlencode($startDate) .
                                                '/' .
                                                urlencode($endDate),
                                        ) .
                                        '?username=' .
                                        urlencode($user->username) .
                                        '&password=' .
                                        urlencode($user->password);
                            $url_export_excel =
                                Auth::check() && Auth::user()->status == 1
                                    ? url(
                                        '/sell-excel/' . $id . '/' . urlencode($startDate) . '/' . urlencode($endDate),
                                    )
                                    : url(
                                        '/user-sell-excel/' .
                                            $id .
                                            '/' .
                                            urlencode($startDate) .
                                            '/' .
                                            urlencode($endDate) .
                                            '?username=' .
                                            urlencode($user->username) .
                                            '&password=' .
                                            urlencode($user->password),
                                    ); */
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
                                <input class="form-control" type="text" name="id" style="display: none"
                                    value="{{ $id }}">
                                <div>
                                    <button type="submit" class="btn btn-primary">ค้นหา</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @foreach ($date_query as $accountCode => $queries)
                        <div class="card" style="margin-bottom: 32px;">
                            <div class="container-company">
                                <div class="company">
                                    <p><strong>{{ $user->company }}</strong></p>
                                    <p><strong>-- บัญชีแยกประเภท {{ $accountCode }} : {{ $queries[0]->gls_account_name }}
                                            --</strong></p>
                                    <p><strong>หมายเลขผู้เสียภาษี {{ $user->tax_id }}</strong></p>
                                    <p><strong> ตั้งแต่วันที่ &nbsp; {{ date('d-m-Y', strtotime($startDate)) }}
                                            &nbsp;จนถึงวันที่&nbsp; {{ date('d-m-Y', strtotime($endDate)) }}</strong></p>
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
                                        {{-- @foreach ($queries as $query)
                                            @if ($query->gls_account_code != '32-1001-01')
                                                @php
                                                    if ($isFirst) {
                                                        if (
                                                            $query->gls_gl_date >= $startDate->toDateString() &&
                                                            $query->gls_gl_date <= $endDate->toDateString()
                                                        ) {
                                                            if (in_array(substr($accountCode, 0, 1), ['2', '3', '4'])) {
                                                                $gls_credit_sum += $query->gls_credit;
                                                                $gls_debit_sum += $query->gls_debit;

                                                                $accumulatedTotal +=
                                                                    $query->gls_credit - $query->gls_debit;
                                                                $beginning_accumulation +=
                                                                    $queries->first()->before_total +
                                                                    $query->gls_credit -
                                                                    $query->gls_debit;
                                                            } else {
                                                                $gls_credit_sum += $query->gls_credit;
                                                                $gls_debit_sum += $query->gls_debit;
                                                                $accumulatedTotal +=
                                                                    $query->gls_debit - $query->gls_credit;
                                                                $beginning_accumulation +=
                                                                    $queries->first()->before_total +
                                                                    $query->gls_debit -
                                                                    $query->gls_credit;
                                                            }
                                                        } else {
                                                            if (in_array(substr($accountCode, 0, 1), ['2', '3', '4'])) {
                                                                $beginning_accumulation += $queries->first()
                                                                    ->before_total;
                                                            } else {
                                                                $beginning_accumulation += $queries->first()
                                                                    ->before_total;
                                                            }
                                                        }

                                                        $isFirst = false;
                                                    } else {
                                                        if (
                                                            $query->gls_gl_date >= $startDate->toDateString() &&
                                                            $query->gls_gl_date <= $endDate->toDateString()
                                                        ) {
                                                            if (in_array(substr($accountCode, 0, 1), ['2', '3', '4'])) {
                                                                $gls_credit_sum += $query->gls_credit;
                                                                $gls_debit_sum += $query->gls_debit;
                                                                $accumulatedTotal +=
                                                                    $query->gls_credit - $query->gls_debit;
                                                                $beginning_accumulation +=
                                                                    $query->gls_credit - $query->gls_debit;
                                                            } else {
                                                                $gls_credit_sum += $query->gls_credit;
                                                                $gls_debit_sum += $query->gls_debit;
                                                                $accumulatedTotal +=
                                                                    $query->gls_debit - $query->gls_credit;
                                                                $beginning_accumulation +=
                                                                    $query->gls_debit - $query->gls_credit;
                                                            }
                                                        }
                                                    }

                                                @endphp
                                                @if ($query->gls_gl_date >= $startDate->toDateString() && $query->gls_gl_date <= $endDate->toDateString())
                                                    <tr>
                                                        @if ($accountCode != '32-1001-01')
                                                            <td>{{ date('d-m-Y', strtotime($query->gls_gl_date)) }}</td>
                                                        @endif

                                                        <td>
                                                            @if ($query->gl_url)
                                                                <a href="{{ $query->gl_url }}" target="_blank"
                                                                    class="opan-message" rel="noopener noreferrer">
                                                                    {{ $query->gl_document }}
                                                                    <span class="id-message">
                                                                        หน้า {{ $query->gl_page }}
                                                                    </span>
                                                                </a>
                                                            @else
                                                                {{ $query->gl_document }}
                                                            @endif
                                                        </td>
                                                        <td>{{ $query->gl_description }} - {{ $query->gl_company }}</td>
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
                                                            class="text-end {{ $beginning_accumulation < 0 && $beginning_accumulation != 0 ? 'error-message' : '' }}">
                                                            {{ number_format($beginning_accumulation, 2) }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endif
                                        @endforeach --}}
                                        @foreach ($queries as $query)
                                            @if ($query->gls_account_code != '32-1001-01')
                                                @php
                                                    $isInDateRange =
                                                        $query->gls_gl_date >= $startDate->toDateString() &&
                                                        $query->gls_gl_date <= $endDate->toDateString();
                                                    $isCategory234 = in_array(substr($accountCode, 0, 1), [
                                                        '2',
                                                        '3',
                                                        '4',
                                                    ]);

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
                                                            $beginning_accumulation += $queries->first()->before_total;
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
                                                            <td>{{ date('d-m-Y', strtotime($query->gls_gl_date)) }}</td>
                                                        @endif
                                                        <td>
                                                            @if ($query->gl_url)
                                                                <a href="{{ $query->gl_url }}" target="_blank"
                                                                    class="opan-message" rel="noopener noreferrer">
                                                                    {{ $query->gl_document }}
                                                                    <span class="id-message">หน้า
                                                                        {{ $query->gl_page }}</span>
                                                                </a>
                                                            @else
                                                                {{ $query->gl_document }}
                                                            @endif
                                                        </td>
                                                        <td>{{ $query->gl_description }} - {{ $query->gl_company }}</td>
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

                                            {{-- <th
                                                class="text-end {{ $beginning_accumulation < 0 && $beginning_accumulation != 0 ? 'error-message' : '' }}">
                                                {{ number_format($gls_debit_sum + $queries->first()->before_total, 2) }}
                                            </th>
                                            <th
                                                class="text-end {{ $beginning_accumulation < 0 && $beginning_accumulation != 0 ? 'error-message' : '' }}">
                                                {{ number_format($gls_credit_sum + $beginning_accumulation, 2) }}
                                            </th> --}}

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
    </script>
@endsection
