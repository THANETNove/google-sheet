<!DOCTYPE html>
<html lang="th">

<head>
    <title>สมุดรายวันทั่วไป</title>
    @include('layouts.head_pdf')

</head>

<body>
    <div id="printableArea">
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
                    $before_total_result_3 = 0;
                    $after_total_result_3 = 0;
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
                        @if (number_format($entry->before_total + $entry->after_total) != 0)
                            <tr>

                                <td class="center">{{ $entry->gls_account_code }}</td>
                                <td class="center">{{ $entry->gls_account_name }}</td>
                                <td
                                    class="text-end color-yellow {{ number_format($entry->before_total) < 0 ? 'error-message' : '' }}">
                                    {{ number_format($entry->before_total) != 0 ? number_format($entry->before_total, 2) : '' }}
                                </td>
                                <td class="text-end color-yellow">
                                </td>

                                <td
                                    class="text-end color-green {{ number_format($entry->after_total) < 0 ? 'error-message' : '' }}">


                                    {{ number_format($entry->after_total) != 0 ? number_format($entry->after_total, 2) : '' }}


                                </td>
                                <td class="text-end color-green">
                                </td>

                                <td
                                    class="text-end color-blue {{ number_format($entry->total) < 0 ? 'error-message' : '' }}">
                                    {{ number_format($entry->total) != 0 ? number_format($entry->total, 2) : '' }}
                                </td>
                                <td class="text-end color-blue"></td>
                            </tr>
                        @endif
                    @endif
                @endforeach
                <tr>
                    <td colspan="2" class="text-end text-bold">รวมสินทรัพย์
                    </td>

                    <td
                        class="text-end color-yellow text-bold {{ number_format($before_total_1) < 0 ? 'error-message' : '' }}">
                        {{ number_format($before_total_1) != 0 ? number_format($before_total_1, 2) : '' }}
                    </td>
                    <td class="text-end color-yellow">
                    </td>

                    <td
                        class="text-end color-green text-bold {{ number_format($after_total_1) < 0 ? 'error-message' : '' }}">
                        {{ number_format($after_total_1) != 0 ? number_format($after_total_1, 2) : '' }}
                    </td>
                    <td class="text-end color-green">
                    </td>

                    <td class="text-end color-blue text-bold {{ number_format($total_1) < 0 ? 'error-message' : '' }}">
                        {{ number_format($total_1) != 0 ? number_format($total_1, 2) : '' }}
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
                        @if (number_format($entry->before_total + $entry->after_total) != 0)
                            <tr>
                                <td class="center">{{ $entry->gls_account_code }}</td>
                                <td class="center">{{ $entry->gls_account_name }}</td>
                                <td class="text-end color-yellow">
                                </td>
                                <td
                                    class="text-end color-yellow {{ number_format($entry->before_total) < 0 ? 'error-message' : '' }}">
                                    {{ number_format($entry->before_total) != 0 ? number_format($entry->before_total, 2) : '' }}
                                </td>

                                <td class="text-end color-green">
                                </td>
                                <td
                                    class="text-end color-green {{ number_format($entry->after_total) < 0 ? 'error-message' : '' }}">
                                    {{ number_format($entry->after_total) != 0 ? number_format($entry->after_total, 2) : '' }}
                                </td>

                                <td class="text-end color-blue"></td>
                                <td
                                    class="text-end color-blue {{ number_format($entry->total) < 0 ? 'error-message' : '' }}">
                                    {{ number_format($entry->total) != 0 ? number_format($entry->total, 2) : '' }}
                                </td>

                            </tr>
                        @endif
                    @endif
                @endforeach
                <tr>
                    <td colspan="2" class="text-end text-bold">รวมหนี้สิน

                    </td>
                    <td class="text-end color-yellow">
                    </td>

                    <td
                        class="text-end color-yellow text-bold {{ number_format($before_total_2) < 0 ? 'error-message' : '' }}">
                        {{ number_format($before_total_2) != 0 ? number_format($before_total_2, 2) : '' }}
                    </td>
                    <td class="text-end color-green">
                    </td>
                    <td
                        class="text-end color-green text-bold {{ number_format($after_total_2) < 0 ? 'error-message' : '' }}">
                        {{ number_format($after_total_2) != 0 ? number_format($after_total_2, 2) : '' }}
                    </td>

                    <td class="text-end color-blue"></td>
                    <td class="text-end color-blue text-bold {{ number_format($total_2) < 0 ? 'error-message' : '' }}">
                        {{ number_format($total_2) != 0 ? number_format($total_2, 2) : '' }}
                    </td>

                </tr>
                {{-- 3% --}}

                <tr>
                    <th colspan="8" class="center" style="border: none;">
                        ส่วนของผู้ถือหุ้น/ผู้เป็นหุ้นส่วน
                    </th>
                </tr>


                @php

                @endphp
                @foreach ($date_query as $entry)
                    @if (Str::startsWith($entry->gls_account_code, '3'))
                        @php
                            $before_total_3 += $entry->before_total;
                            $before_total_result_3 += $entry->before_total_result;
                            $after_total_result_3 += $entry->after_total_result;
                            $after_total_3 += $entry->after_total;
                            $total_3 += $entry->total;
                            //303053.72
                        @endphp
                        <tr>
                            <td class="center">{{ $entry->gls_account_code }}</td>
                            <td class="center">{{ $entry->gls_account_name }}</td>
                            <td class="text-end color-yellow">
                            </td>
                            <td
                                class="text-end color-yellow {{ number_format($entry->before_total) < 0 || number_format($before_total_result_3 + $before_total_1 - $before_total_2 - $before_total_3) < 0 ? 'error-message' : '' }}">
                                @if ($entry->gls_account_code == '32-1001-01')
                                    {{-- แสดง before_total_result เฉพาะเมื่อ gls_account_code เป็น 32-1001-01 --}}
                                    {{ number_format($before_total_result_3 + $before_total_1 - $before_total_2 - $before_total_3) != 0 ? number_format($before_total_result_3 + $before_total_1 - $before_total_2 - $before_total_3, 2) : '' }}
                                @else
                                    {{-- แสดง before_total สำหรับบัญชีอื่น --}}
                                    {{ isset($entry->before_total) && number_format($entry->before_total) != 0 ? number_format($entry->before_total, 2) : '' }}
                                @endif

                            </td>

                            <td class="text-end color-green">
                            </td>
                            <td
                                class="text-end color-green {{ number_format($entry->after_total) < 0 || number_format($entry->after_total_result) < 0 ? 'error-message' : '' }}">
                                @if ($entry->gls_account_code == '32-1001-01')
                                    {{ number_format($entry->after_total_result) != 0 ? number_format($entry->after_total_result + $before_total_1 - $before_total_2 - $before_total_3, 2) : '' }}
                                @else
                                    {{ number_format($entry->after_total) != 0 ? number_format($entry->after_total, 2) : '' }}
                                @endif



                            </td>

                            <td class="text-end color-blue">

                            </td>
                            <td
                                class="text-end color-blue {{ number_format($entry->total) < 0 || number_format($before_total_result_3) < 0 || number_format($before_total_result_3 + $before_total_1 - $before_total_2 - $before_total_3) < 0 ? 'error-message' : '' }}">

                                @if ($entry->gls_account_code == '32-1001-01')
                                    {{-- แสดง before_total_result เฉพาะเมื่อ gls_account_code เป็น 32-1001-01 --}}
                                    {{ number_format($before_total_result_3 + $before_total_1 - $before_total_2 - $before_total_3) != 0 ? number_format($before_total_result_3 + $before_total_1 - $before_total_2 - $before_total_3, 2) : '' }}
                                @else
                                    {{-- แสดง before_total สำหรับบัญชีอื่น --}}
                                    {{ $entry->total != 0 ? number_format($entry->total, 2) : '' }}
                                @endif

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
                        class="text-end color-yellow text-bold {{ number_format($before_total_3 + $before_total_1 - $before_total_2 - $before_total_3) < 0 ? 'error-message' : '' }}">
                        {{ number_format($before_total_3 + $before_total_1 - $before_total_2 - $before_total_3) != 0 ? number_format($before_total_3 + $before_total_1 - $before_total_2 - $before_total_3, 2) : '' }}

                    </td>
                    <td class="text-end color-green">
                    </td>
                    <td
                        class="text-end color-green text-bold {{ number_format($after_total_3) < 0 ? 'error-message' : '' }}">

                        {{ number_format($after_total_3) != 0 ? number_format($after_total_3, 2) : '' }}
                    </td>

                    <td class="text-end color-blue">

                    </td>
                    @php
                        $total_result = $before_total_1 - $before_total_2 + $after_total_3;
                    @endphp
                    <td
                        class="text-end color-blue text-bold {{ number_format($total_result) < 0 ? 'error-message' : '' }}">

                        {{ number_format($total_result) != 0 ? number_format($total_result, 2) : '' }}

                    </td>

                </tr>

                {{-- 4% --}}
                <tr>

                    <th colspan="8" class="center" style="border: none;">
                        รายได้จากการดำเนินงาน</th>

                </tr>



                @foreach ($date_query as $entry)
                    @if (Str::startsWith($entry->gls_account_code, '4'))
                        @php
                            $before_total_4 += $entry->before_total;
                            $after_total_4 += $entry->after_total;
                            $total_4 += $entry->total;
                        @endphp
                        @if (number_format($entry->before_total + $entry->after_total) != 0)
                            <tr>
                                <td class="center">{{ $entry->gls_account_code }}</td>
                                <td class="center">{{ $entry->gls_account_name }}</td>
                                <td class="text-end color-yellow">
                                </td>
                                <td
                                    class="text-end color-yellow {{ number_format($entry->before_total) < 0 ? 'error-message' : '' }}">
                                    {{ number_format($entry->before_total) != 0 ? number_format($entry->before_total, 2) : '' }}
                                </td>
                                <td class="text-end color-green">
                                </td>
                                <td
                                    class="text-end color-green {{ number_format($entry->after_total) < 0 ? 'error-message' : '' }}">


                                    {{ number_format($entry->after_total) != 0 ? number_format($entry->after_total, 2) : '' }}

                                </td>
                                <td class="text-end color-blue"></td>
                                <td
                                    class="text-end color-blue {{ number_format($entry->total) < 0 ? 'error-message' : '' }}">
                                    {{ number_format($entry->total) != 0 ? number_format($entry->total, 2) : '' }}
                                </td>
                            </tr>
                        @endif
                    @endif
                @endforeach
                <tr>
                    <td colspan="2" class="text-end text-bold">รวมรายได้จากการดำเนินงาน</td>
                    <td class="text-end color-yellow">
                    </td>
                    <td
                        class="text-end color-yellow text-bold {{ number_format($before_total_4) < 0 ? 'error-message' : '' }}">
                        {{ number_format($before_total_4) != 0 ? number_format($before_total_4, 2) : '' }}
                    </td>
                    <td class="text-end color-green">
                    </td>
                    <td
                        class="text-end color-green text-bold {{ number_format($after_total_4) < 0 ? 'error-message' : '' }}">
                        {{ number_format($after_total_4) != 0 ? number_format($after_total_4, 2) : '' }}
                    </td>
                    <td class="text-end color-blue"></td>
                    <td class="text-end color-blue text-bold {{ number_format($total_4) < 0 ? 'error-message' : '' }}">
                        {{ number_format($total_4) != 0 ? number_format($total_4, 2) : '' }}
                    </td>
                </tr>


                {{-- 5% --}}
                <tr>

                    <th colspan="8" class="center" style="border: none;">
                        ค่าใช้จ่ายในการขายเเละบริหาร</th>
                </tr>
                @foreach ($date_query as $entry)
                    @if (Str::startsWith($entry->gls_account_code, '5'))
                        @php
                            $before_total_5 += $entry->before_total;
                            $after_total_5 += $entry->after_total;
                            $total_5 += $entry->total;
                        @endphp
                        @if (number_format($entry->before_total + $entry->after_total) != 0)
                            <tr>
                                <td class="center">{{ $entry->gls_account_code }}</td>
                                <td class="center">{{ $entry->gls_account_name }}</td>

                                <td
                                    class="text-end color-yellow {{ number_format($entry->before_total) < 0 ? 'error-message' : '' }}">
                                    {{ number_format($entry->before_total) != 0 ? number_format($entry->before_total, 2) : '' }}
                                </td>
                                <td class="text-end color-yellow">
                                </td>

                                <td
                                    class="text-end color-green  {{ number_format($entry->after_total) < 0 ? 'error-message' : '' }}">
                                    {{ number_format($entry->after_total) != 0 ? number_format($entry->after_total, 2) : '' }}
                                </td>
                                <td class="text-end color-green">
                                </td>
                                <td
                                    class="text-end color-blue {{ number_format($entry->total) < 0 ? 'error-message' : '' }}">
                                    {{ number_format($entry->total) != 0 ? number_format($entry->total, 2) : '' }}
                                </td>
                                <td class="text-end color-blue"></td>
                            </tr>
                        @endif
                    @endif
                @endforeach

                <tr>
                    <td colspan="2" class="text-end text-bold">รวมค่าใช้จ่ายในการขายและบริหาร</td>

                    <td
                        class="text-end color-yellow text-bold {{ number_format($before_total_5) < 0 ? 'error-message' : '' }}">
                        {{ number_format($before_total_5) != 0 ? number_format($before_total_5, 2) : '' }}
                    </td>
                    <td class="text-end color-yellow">
                    </td>

                    <td class="text-end color-green text-bold  {{ $after_total_5 < 0 ? 'error-message' : '' }}">
                        {{ $after_total_5 != 0 ? number_format($after_total_5, 2) : '' }} </td>
                    <td class="text-end color-green">
                    </td>

                    <td class="text-end color-blue text-bold {{ $total_5 < 0 ? 'error-message' : '' }}">
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
                        class="text-end color-green text-bold  {{ number_format($after_total_4 - $after_total_5) < 0 ? 'error-message' : '' }}">
                        {{ number_format($after_total_4 - $after_total_5) != 0 ? number_format($after_total_4 - $after_total_5, 2) : '' }}
                    </td>
                    @php

                        $totalPeriod = $before_total_4 - $before_total_5 + $after_total_4 - $after_total_5; //
                    @endphp
                    <td class="text-end color-blue"></td>
                    <td class="text-end color-blue text-bold {{ $totalPeriod < 0 ? 'error-message' : '' }}">
                        {{ $totalPeriod != 0 ? number_format($totalPeriod, 2) : '' }}
                    </td>

                </tr>
                <tr>
                    <td colspan="2" class="text-end text-bold">กำไร(ขาดทุน)สะสมยกไป</td>
                    <td class="text-end color-yellow">
                    </td>
                    <td
                        class="text-end color-yellow text-bold {{ $before_total_result_3 + $before_total_1 - $before_total_2 - $before_total_3 < 0 ? 'error-message' : '' }}">

                        {{ $before_total_result_3 + $before_total_1 - $before_total_2 - $before_total_3 != 0 ? number_format($before_total_result_3 + $before_total_1 - $before_total_2 - $before_total_3, 2) : '' }}
                    </td>

                    <td class="text-end color-green">
                    </td>
                    <td
                        class="text-end color-green text-bold  {{ number_format($after_total_4 - $after_total_5) < 0 ? 'error-message' : '' }}">
                        {{ number_format($after_total_4 - $after_total_5) != 0 ? number_format($after_total_4 - $after_total_5, 2) : '' }}
                    </td>

                    <td class="text-end color-blue"></td>
                    @php

                        $totalProfitLoss =
                            $before_total_result_3 +
                            $before_total_1 -
                            $before_total_2 -
                            $before_total_3 +
                            $after_total_4 -
                            $after_total_5; //
                    @endphp
                    <td class="text-end color-blue text-bold {{ $totalProfitLoss < 0 ? 'error-message' : '' }}">
                        {{ $totalProfitLoss != 0 ? number_format($totalProfitLoss, 2) : '' }}
                    </td>

                </tr>
                <tr>
                    <td colspan="8" style="border: none; height: 32px;"></td>
                    <!-- ใช้ height เพิ่มช่องว่าง -->
                </tr>
                @php

                    $toatalSum_1 = $before_total_1 + $before_total_5;
                    $toatalSum_2 =
                        $before_total_2 +
                        $before_total_3 +
                        $before_total_1 -
                        $before_total_2 -
                        $before_total_3 +
                        $before_total_5;
                    $toatalSum_3 = $after_total_1 + $after_total_5;
                    $toatalSum_4 = $after_total_2 + $after_total_3 + $after_total_4;
                    $toatalSum_5 = $total_1 + $total_5;
                    $toatalSum_6_1 =
                        $total_2 + $before_total_1 - $before_total_2 - $before_total_3 + $total_3 + $total_4;
                    $toatalSum_6_2 = $before_total_4 - $before_total_5;
                    $toatalSum_6 = $toatalSum_6_1 - $toatalSum_6_2;
                    // กำหนดทศนิยม 10 ตำแหน่ง
                @endphp

                <tr style="border: none; margin-top: 64px;">

                    <th class="text-end" style="border: none;" colspan="2">
                    </th>
                    <td style="border: none;"
                        class="text-end color-yellow  text-bold {{ $toatalSum_1 < 0 ? 'error-message' : '' }}">
                        {{ $toatalSum_1 != 0 ? number_format($toatalSum_1, 2) : '' }}
                    </td>
                    <td style="border: none;"
                        class="text-end color-yellow  text-bold {{ $toatalSum_2 < 0 ? 'error-message' : '' }}">
                        {{ $toatalSum_2 != 0 ? number_format($toatalSum_2, 2) : '' }}
                    </td>
                    <td style="border: none;"
                        class="text-end color-green  text-bold  {{ $toatalSum_3 < 0 ? 'error-message' : '' }}">

                        {{ $toatalSum_3 != 0 ? number_format($toatalSum_3, 2) : '' }}
                    </td>
                    <td style="border: none;"
                        class="text-end color-green  text-bold  {{ $toatalSum_4 < 0 ? 'error-message' : '' }}">

                        {{ $toatalSum_4 != 0 ? number_format($toatalSum_4, 2) : '' }}
                    </td>
                    <td style="border: none;"
                        class="text-end color-blue  text-bold {{ $toatalSum_5 < 0 ? 'error-message' : '' }}">
                        {{ $toatalSum_5 != 0 ? number_format($toatalSum_5, 2) : '' }}</td>
                    <td class="text-end color-blue  text-bold {{ $toatalSum_6 < 0 ? 'error-message' : '' }}"
                        style="border: none;">
                        {{ $toatalSum_6 != 0 ? number_format($toatalSum_6, 2) : '' }}
                    </td>

                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
