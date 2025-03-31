<!DOCTYPE html>
<html lang="th">

<head>
    <title>บัญชีแยกประเภท</title>
    @include('layouts.head_pdf')

</head>

<body>
    <div id="printableArea">


        @foreach ($date_query as $accountCode => $queries)
            @php
                $beforeTotal = !empty($queries->first()) ? $queries->first()->before_total : 0;
                $totalDebit = $queries->sum('gls_debit');
                $totalCredit = $queries->sum('gls_credit');
                $totalAmount = $beforeTotal + $totalDebit + $totalCredit;
                $hasTransactionsInDateRange = $queries
                    ->filter(function ($query) use ($startDate, $endDate) {
                        return $query->gls_gl_date &&
                            $query->gls_gl_date >= $startDate->copy()->startOfDay() &&
                            $query->gls_gl_date <= $endDate->copy()->endOfDay();
                    })
                    ->isNotEmpty();

                // ✅ ถ้าไม่มีรายการ ให้เช็คยอดยกมาต้นงวด หรือยอดรวมบัญชี
                /*   if (!$hasTransactionsInDateRange) {
                    $hasTransactionsInDateRange =
                        $beforeTotal != 0 || $totalDebit != 0 || $totalCredit != 0;
                } */

                // 53-1001-09  53-1001-10 ,53-1001-12 53-1001-13 53-1001-15  54-1000-01 54-5001-02 54-5001-11 58-1000-01 58-1002-01
                //41-1001-01 42-1001-01 51-1001-01 53-1001-07 53-1001-11 ,53-1001-16 54-5001-04 54-5001-05  54-5001-15 59-2001-01 59-2001-02

            @endphp
            @if (($totalAmount != 0 && $hasTransactionsInDateRange) || $beforeTotal != 0)
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
                                            $isInDateRange =
                                                $query->gls_gl_date >= $startDate->copy()->startOfDay() &&
                                                $query->gls_gl_date <= $endDate->copy()->endOfDay();

                                            $isCategory234 = in_array(substr($accountCode, 0, 1), ['2', '3', '4']);

                                            //  dd( $query->gls_gl_date , $startDate->toDateString(), $endDate->toDateString());

                                            if ($isFirst) {
                                                if ($isInDateRange) {
                                                    $delta = $isCategory234
                                                        ? $query->gls_credit - $query->gls_debit
                                                        : $query->gls_debit - $query->gls_credit;
                                                    $gls_credit_sum += $query->gls_credit;
                                                    $gls_debit_sum += $query->gls_debit;
                                                    $accumulatedTotal += $delta;
                                                    $beginning_accumulation += $queries->first()->before_total + $delta;
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
                                                    <td>{{ date('d-m-Y', strtotime($query->gls_gl_date)) }}
                                                    </td>
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
                                                @endif
                                                @if ($accountCode == '32-1001-01')
                                                    <td>{{ $query->gls_account_code }}
                                                    </td>
                                                @endif

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
</body>

</html>
