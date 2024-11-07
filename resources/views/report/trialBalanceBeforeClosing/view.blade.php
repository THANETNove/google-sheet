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

                                <p><strong>-- งบทดลองก่อนปิดบัญชี --</strong></p>
                                <p><strong>หมายเลขผู้เสียภาษี {{ $user->tax_id }}<strong></p>
                                <p><strong> ตั้งแต่วันที่ &nbsp; {{ date('d-m-Y', strtotime($startDate)) }}
                                        &nbsp;จนถึงวันที่&nbsp;
                                        {{ date('d-m-Y', strtotime($endDate)) }}</strong></p>
                            </div>
                        </div>
                        <form action="{{ route('report/search-trial-balance-before-closing') }}" method="POST"
                            class="container-date">
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
                        <div class="date">
                            <p> วันเริ่มรอบบัญชี {{ $day }} {{ $monthThai }} {{ $currentYear }}</p>
                            {{--  <a href="{{ url('/profit-statement-pdf/' . $id . '/' . urlencode($startDate) . '/' . urlencode($endDate)) }}"
                                target="_blank" class="btn btn-primary">
                                <i class='bx bxs-file-pdf'></i>&nbsp; PDF
                            </a>
                            <a href="{{ url('/profit-statement-excel/' . $id . '/' . urlencode($startDate) . '/' . urlencode($endDate)) }}"
                                class="btn btn-primary">
                                <i class='bx bxs-file'></i>&nbsp; Excel
                            </a> --}}
                        </div>
                        <div class="table-responsive m-3">
                            <table class="table">
                                <thead class="text-center">
                                    <tr class="table-secondary">
                                        <th class="text-center-vertical" rowspan="2">รหัสบัญชี</th>
                                        <th class="text-center-vertical" rowspan="2">ชื่อบัญชี</th>
                                        <th colspan="2">ยอดสะสมต้นงวด</th>
                                        <th colspan="2">ยอดสะสมงวดนี้</th>
                                        <th colspan="2">ยอดสะสมยกไป </th>

                                    </tr>
                                    <tr class="table-secondary">
                                        <th>เดบิต</th>
                                        <th>เครดิต</th>
                                        <th>เดบิต</th>
                                        <th>เครดิต</th>
                                        <th>เดบิต</th>
                                        <th>เครดิต</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $before_total_1 = 0;
                                        $after_total_1 = 0;
                                        $total_1 = 0;
                                        $before_total_2 = 0;
                                        $after_total_2 = 0;
                                        $total_2 = 0;
                                        $before_total_3 = 0;
                                        $total_result_3 = 0;
                                        $after_total_3 = 0;
                                        $total_3 = 0;
                                        $before_total_4 = 0;
                                        $after_total_4 = 0;
                                        $total_4 = 0;
                                        $before_total_5 = 0;
                                        $after_total_5 = 0;
                                        $total_5 = 0;
                                    @endphp
                                    {{-- 1% --}}
                                    <tr>

                                        <th colspan="8" class="center" style="border: none;">
                                            สินทรัพย์
                                        </th>

                                    </tr>



                                    @foreach ($date_query as $entry)
                                        @if (Str::startsWith($entry->gls_account_code, '1'))
                                            @php
                                                $before_total_1 += $entry->before_total;
                                                $after_total_1 += $entry->after_total;
                                                $total_1 += $entry->total;
                                            @endphp
                                            <tr>
                                                <td class="center">{{ $entry->gls_account_code }}</td>
                                                <td class="center">{{ $entry->gls_account_name }}</td>
                                                <td
                                                    class="text-end color-yellow {{ $entry->before_total < 0 ? 'error-message' : '' }}">
                                                    {{ $entry->before_total != 0 ? number_format($entry->before_total, 2) : '' }}
                                                </td>
                                                <td class="text-end color-yellow">
                                                </td>

                                                <td
                                                    class="text-end color-green {{ $entry->after_total < 0 ? 'error-message' : '' }}">


                                                    {{ $entry->after_total != 0 ? number_format($entry->after_total, 2) : '' }}

                                                </td>
                                                <td class="text-end color-green">
                                                </td>

                                                <td
                                                    class="text-end color-blue {{ $entry->total < 0 ? 'error-message' : '' }}">
                                                    {{ $entry->total != 0 ? number_format($entry->total, 2) : '' }}
                                                </td>
                                                <td class="text-end color-blue"></td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr>
                                        <td colspan="2" class="text-end text-bold">รวมสินทรัพย์
                                        </td>

                                        <td
                                            class="text-end color-yellow text-bold {{ $before_total_1 < 0 ? 'error-message' : '' }}">
                                            {{ $before_total_1 != 0 ? number_format($before_total_1, 2) : '' }}</td>
                                        <td class="text-end color-yellow">
                                        </td>

                                        <td
                                            class="text-end color-green text-bold {{ $after_total_1 < 0 ? 'error-message' : '' }}">
                                            {{ $after_total_1 != 0 ? number_format($after_total_1, 2) : '' }}</td>
                                        <td class="text-end color-green">
                                        </td>

                                        <td
                                            class="text-end color-blue text-bold {{ $total_1 < 0 ? 'error-message' : '' }}">
                                            {{ $total_1 != 0 ? number_format($total_1, 2) : '' }}
                                        </td>
                                        <td class="text-end color-blue"></td>
                                    </tr>

                                    {{-- 2% --}}
                                    <tr>

                                        <th colspan="8" class="center" style="border: none;">
                                            หนี้สิน
                                        </th>

                                    </tr>



                                    @foreach ($date_query as $entry)
                                        @if (Str::startsWith($entry->gls_account_code, '2'))
                                            @php
                                                $before_total_2 += $entry->before_total;
                                                $after_total_2 += $entry->after_total;
                                                $total_2 += $entry->total;
                                            @endphp
                                            <tr>
                                                <td class="center">{{ $entry->gls_account_code }}</td>
                                                <td class="center">{{ $entry->gls_account_name }}</td>
                                                <td class="text-end color-yellow">
                                                </td>
                                                <td
                                                    class="text-end color-yellow {{ $entry->before_total < 0 ? 'error-message' : '' }}">
                                                    {{ $entry->before_total != 0 ? number_format($entry->before_total, 2) : '' }}
                                                </td>

                                                <td class="text-end color-green">
                                                </td>
                                                <td
                                                    class="text-end color-green {{ $entry->after_total < 0 ? 'error-message' : '' }}">
                                                    {{ $entry->after_total != 0 ? number_format($entry->after_total, 2) : '' }}
                                                </td>

                                                <td class="text-end color-blue"></td>
                                                <td
                                                    class="text-end color-blue {{ $entry->total < 0 ? 'error-message' : '' }}">
                                                    {{ $entry->total != 0 ? number_format($entry->total, 2) : '' }}
                                                </td>

                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr>
                                        <td colspan="2" class="text-end text-bold">รวมหนี้สิน

                                        </td>
                                        <td class="text-end color-yellow">
                                        </td>

                                        <td
                                            class="text-end color-yellow text-bold {{ $before_total_2 < 0 ? 'error-message' : '' }}">
                                            {{ $before_total_2 != 0 ? number_format($before_total_2, 2) : '' }}</td>
                                        <td class="text-end color-green">
                                        </td>
                                        <td
                                            class="text-end color-green text-bold {{ $after_total_2 < 0 ? 'error-message' : '' }}">
                                            {{ $after_total_2 != 0 ? number_format($after_total_2, 2) : '' }}</td>

                                        <td class="text-end color-blue"></td>
                                        <td
                                            class="text-end color-blue text-bold {{ $total_2 < 0 ? 'error-message' : '' }}">
                                            {{ $total_2 != 0 ? number_format($total_2, 2) : '' }}
                                        </td>

                                    </tr>
                                    {{-- 3% --}}

                                    <tr>
                                        <th colspan="8" class="center" style="border: none;">
                                            ส่วนของผู้ถือหุ้น/ผู้เป็นหุ้นส่วน
                                        </th>
                                    </tr>



                                    @foreach ($date_query as $entry)
                                        @if (Str::startsWith($entry->gls_account_code, '3'))
                                            @php
                                                $before_total_3 += $entry->before_total;
                                                $total_result_3 += $entry->total_result;
                                                $after_total_3 += $entry->after_total;
                                                $total_3 += $entry->total;
                                            @endphp
                                            <tr>
                                                <td class="center">{{ $entry->gls_account_code }}</td>
                                                <td class="center">{{ $entry->gls_account_name }}</td>
                                                <td class="text-end color-yellow">
                                                </td>
                                                <td
                                                    class="text-end color-yellow {{ $entry->before_total < 0 || $entry->total_result < 0 ? 'error-message' : '' }}">
                                                    @if ($entry->gls_account_code == '32-1001-01')
                                                        {{-- แสดง total_result เฉพาะเมื่อ gls_account_code เป็น 32-1001-01 --}}
                                                        {{ isset($entry->total_result) && $entry->total_result != 0 ? number_format($entry->total_result, 2) : '' }}
                                                    @else
                                                        {{-- แสดง before_total สำหรับบัญชีอื่น --}}
                                                        {{ isset($entry->before_total) && $entry->before_total != 0 ? number_format($entry->before_total, 2) : '' }}
                                                    @endif

                                                </td>

                                                <td class="text-end color-green">
                                                </td>
                                                <td
                                                    class="text-end color-green {{ $entry->after_total < 0 ? 'error-message' : '' }}">
                                                    {{ $entry->after_total != 0 ? number_format($entry->after_total, 2) : '' }}
                                                </td>

                                                <td class="text-end color-blue"></td>
                                                <td
                                                    class="text-end color-blue {{ $entry->total < 0 ? 'error-message' : '' }}">
                                                    {{ $entry->total != 0 ? number_format($entry->total, 2) : '' }}
                                                </td>

                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr>
                                        <td colspan="2" class="text-end text-bold">รวมส่วนของผู้ถือหุ้น/ผู้เป็นหุ้นส่วน
                                        </td>
                                        <td class="text-end color-yellow">
                                        </td>

                                        <td
                                            class="text-end color-yellow text-bold {{ $before_total_3 + $total_result_3 < 0 ? 'error-message' : '' }}">
                                            {{ $before_total_3 + $total_result_3 != 0 ? number_format($before_total_3 + $total_result_3, 2) : '' }}
                                        </td>
                                        <td class="text-end color-green">
                                        </td>
                                        <td
                                            class="text-end color-green text-bold {{ $after_total_3 < 0 ? 'error-message' : '' }}">
                                            {{ $after_total_3 != 0 ? number_format($after_total_3, 2) : '' }}</td>

                                        <td class="text-end color-blue"></td>
                                        <td
                                            class="text-end color-blue text-bold {{ $total_3 < 0 ? 'error-message' : '' }}">
                                            {{ $total_3 != 0 ? number_format($total_3, 2) : '' }}
                                        </td>

                                    </tr>

                                    {{-- 4% --}}
                                    <tr>
                                        <td style="border: none;"></td>
                                        <th colspan="7" class="center" style="border: none;">
                                            รายได้จากการดำเนินงาน</th>

                                    </tr>



                                    @foreach ($date_query as $entry)
                                        @if (Str::startsWith($entry->gls_account_code, '4'))
                                            @php
                                                $before_total_4 += $entry->before_total;
                                                $after_total_4 += $entry->after_total;
                                                $total_4 += $entry->total;
                                            @endphp
                                            <tr>
                                                <td class="center">{{ $entry->gls_account_code }}</td>
                                                <td class="center">{{ $entry->gls_account_name }}</td>
                                                <td class="text-end color-yellow">
                                                </td>
                                                <td
                                                    class="text-end color-yellow {{ $entry->before_total < 0 ? 'error-message' : '' }}">
                                                    {{ $entry->before_total != 0 ? number_format($entry->before_total, 2) : '' }}
                                                </td>
                                                <td class="text-end color-green">
                                                </td>
                                                <td
                                                    class="text-end color-green {{ $entry->after_total < 0 ? 'error-message' : '' }}">


                                                    {{ $entry->after_total != 0 ? number_format($entry->after_total, 2) : '' }}

                                                </td>
                                                <td class="text-end color-blue"></td>
                                                <td
                                                    class="text-end color-blue {{ $entry->total < 0 ? 'error-message' : '' }}">
                                                    {{ $entry->total != 0 ? number_format($entry->total, 2) : '' }}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr>
                                        <td colspan="2" class="text-end text-bold">รวมรายได้จากการดำเนินงาน</td>
                                        <td class="text-end color-yellow">
                                        </td>
                                        <td
                                            class="text-end color-yellow text-bold {{ $before_total_4 < 0 ? 'error-message' : '' }}">
                                            {{ $before_total_4 != 0 ? number_format($before_total_4, 2) : '' }}</td>
                                        <td class="text-end color-green">
                                        </td>
                                        <td
                                            class="text-end color-green text-bold {{ $after_total_4 < 0 ? 'error-message' : '' }}">
                                            {{ $after_total_4 != 0 ? number_format($after_total_4, 2) : '' }}</td>
                                        <td class="text-end color-blue"></td>
                                        <td
                                            class="text-end color-blue text-bold {{ $total_4 < 0 ? 'error-message' : '' }}">
                                            {{ $total_4 != 0 ? number_format($total_4, 2) : '' }}
                                        </td>
                                    </tr>


                                    {{-- 5% --}}
                                    <tr>
                                        <td style="border: none;"></td>
                                        <th colspan="7" class="center" style="border: none;">
                                            ค่าใช้จ่ายในการขายเเละบริหาร</th>
                                    </tr>
                                    @foreach ($date_query as $entry)
                                        @if (Str::startsWith($entry->gls_account_code, '5'))
                                            @php
                                                $before_total_5 += $entry->before_total;
                                                $after_total_5 += $entry->after_total;
                                                $total_5 += $entry->total;
                                            @endphp
                                            <tr>
                                                <td class="center">{{ $entry->gls_account_code }}</td>
                                                <td class="center">{{ $entry->gls_account_name }}</td>

                                                <td
                                                    class="text-end color-yellow {{ $entry->before_total < 0 ? 'error-message' : '' }}">
                                                    {{ $entry->before_total != 0 ? number_format($entry->before_total, 2) : '' }}
                                                </td>
                                                <td class="text-end color-yellow">
                                                </td>

                                                <td
                                                    class="text-end color-green  {{ $entry->after_total < 0 ? 'error-message' : '' }}">
                                                    {{ $entry->after_total != 0 ? number_format($entry->after_total, 2) : '' }}
                                                </td>
                                                <td class="text-end color-green">
                                                </td>
                                                <td
                                                    class="text-end color-blue {{ $entry->total < 0 ? 'error-message' : '' }}">
                                                    {{ $entry->total != 0 ? number_format($entry->total, 2) : '' }}
                                                </td>
                                                <td class="text-end color-blue"></td>
                                            </tr>
                                        @endif
                                    @endforeach

                                    <tr>
                                        <td colspan="2" class="text-end text-bold">รวมค่าใช้จ่ายในการขายและบริหาร</td>

                                        <td
                                            class="text-end color-yellow text-bold {{ $before_total_5 < 0 ? 'error-message' : '' }}">
                                            {{ $before_total_5 != 0 ? number_format($before_total_5, 2) : '' }}</td>
                                        <td class="text-end color-yellow">
                                        </td>

                                        <td
                                            class="text-end color-green text-bold  {{ $after_total_5 < 0 ? 'error-message' : '' }}">
                                            {{ $after_total_5 != 0 ? number_format($after_total_5, 2) : '' }}</td>
                                        <td class="text-end color-green">
                                        </td>

                                        <td
                                            class="text-end color-blue text-bold {{ $total_5 < 0 ? 'error-message' : '' }}">
                                            {{ $total_5 != 0 ? number_format($total_5, 2) : '' }}
                                        </td>
                                        <td class="text-end color-blue"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-end text-bold">ยอดรวมกำไร(ขาดทุน)สุทธิของงวดนี้
                                        </td>
                                        <td class="text-end color-yellow">
                                        </td>
                                        <td
                                            class="text-end color-yellow text-bold {{ $before_total_4 - $before_total_5 < 0 ? 'error-message' : '' }}">
                                            {{ $before_total_4 - $before_total_5 != 0 ? number_format($before_total_4 - $before_total_5, 2) : '' }}
                                        </td>

                                        <td class="text-end color-green">
                                        </td>
                                        <td
                                            class="text-end color-green text-bold  {{ $after_total_4 - $after_total_5 < 0 ? 'error-message' : '' }}">
                                            {{ $after_total_4 - $after_total_5 != 0 ? number_format($after_total_4 - $after_total_5, 2) : '' }}
                                        </td>

                                        <td class="text-end color-blue"></td>
                                        <td
                                            class="text-end color-blue text-bold {{ $total_4 - $total_5 < 0 ? 'error-message' : '' }}">
                                            {{ $total_4 - $total_5 != 0 ? number_format($total_4 - $total_5, 2) : '' }}
                                        </td>

                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-end text-bold">กำไร(ขาดทุน)สะสมยกไป</td>
                                        <td class="text-end color-yellow">
                                        </td>
                                        <td
                                            class="text-end color-yellow text-bold {{ $before_total_3 + ($before_total_4 - $before_total_5) < 0 ? 'error-message' : '' }}">

                                            {{ $before_total_3 + ($before_total_4 - $before_total_5) != 0 ? number_format($before_total_3 + ($before_total_4 - $before_total_5), 2) : '' }}
                                        </td>

                                        <td class="text-end color-green">
                                        </td>
                                        <td
                                            class="text-end color-green text-bold  {{ $after_total_3 + ($after_total_4 - $after_total_5) < 0 ? 'error-message' : '' }}">
                                            {{ $after_total_3 + ($total_1 + $total_5) != 0 ? number_format($after_total_3 + ($after_total_4 - $after_total_5), 2) : '' }}
                                        </td>

                                        <td class="text-end color-blue"></td>
                                        <td
                                            class="text-end color-blue text-bold {{ $total_3 + ($total_4 - $total_5) < 0 ? 'error-message' : '' }}">
                                            {{ $total_3 + ($total_4 - $total_5) != 0 ? number_format($total_3 + ($total_4 - $total_5), 2) : '' }}
                                        </td>

                                    </tr>
                                    <tr>
                                        <td colspan="8" style="border: none; height: 32px;"></td>
                                        <!-- ใช้ height เพิ่มช่องว่าง -->
                                    </tr>


                                    <tr style="border: none; margin-top: 64px;">

                                        <th class="text-end" style="border: none;" colspan="2">
                                        </th>
                                        <td style="border: none;"
                                            class="text-end color-yellow  text-bold {{ $before_total_2 + $before_total_5 < 0 ? 'error-message' : '' }}">
                                            {{ $before_total_2 + $before_total_5 != 0 ? number_format($before_total_2 + $before_total_5, 2) : '' }}
                                        </td>
                                        <td style="border: none;"
                                            class="text-end color-yellow  text-bold {{ $before_total_2 + $before_total_3 + $before_total_4 < 0 ? 'error-message' : '' }}">
                                            {{ $before_total_2 + $before_total_3 + $before_total_4 != 0 ? number_format($before_total_2 + $before_total_3 + $before_total_4, 2) : '' }}
                                        </td>
                                        <td style="border: none;"
                                            class="text-end color-green  text-bold  {{ $after_total_1 + ($after_total_4 - $after_total_5) < 0 ? 'error-message' : '' }}">
                                            {{ $after_total_1 + ($after_total_4 - $after_total_5) != 0 ? number_format($after_total_1 + ($after_total_4 - $after_total_5), 2) : '' }}
                                        </td>
                                        <td style="border: none;"
                                            class="text-end color-green  text-bold  {{ $after_total_2 + $after_total_3 + $after_total_4 < 0 ? 'error-message' : '' }}">
                                            {{ $after_total_2 + $after_total_3 + $after_total_4 != 0 ? number_format($after_total_2 + $after_total_3 + $after_total_4, 2) : '' }}
                                        </td>
                                        <td style="border: none;"
                                            class="text-end color-blue  text-bold {{ $total_1 + $total_5 < 0 ? 'error-message' : '' }}">
                                            {{ number_format($total_1 + $total_5, 2) }}</td>
                                        <td class="text-end color-blue  text-bold {{ $total_2 + $total_3 + $total_4 < 0 ? 'error-message' : '' }}"
                                            style="border: none;">
                                            {{ number_format($total_2 + $total_3 + $total_4, 2) }}
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
