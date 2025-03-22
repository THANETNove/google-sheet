<!DOCTYPE html>
<html lang="th">

<head>
    <title>งบกำไร(ขาดทุน)</title>

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
                                <p><strong>-- งบกำไร(ขาดทุน) --</strong></p>
                                <p><strong>หมายเลขผู้เสียภาษี {{ $user->tax_id }}<strong></p>
                                <p><strong> ตั้งแต่วันที่ &nbsp; {{ date('d-m-Y', strtotime($startDate)) }}
                                        &nbsp;จนถึงวันที่&nbsp;
                                        {{ date('d-m-Y', strtotime($endDate)) }}</strong></p>
                            </div>

                        </div>
                        <div class="date">
                            <p> วันเริ่มรอบบัญชี {{ $day }} {{ $monthThai }} {{ $currentYear }}</p>
                            <p>หมายเลขผู้เสียภาษี {{ $user->tax_id }}</p>
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
                                        $before_total_4 = 0;
                                        $after_total_4 = 0;
                                        $total_4 = 0;
                                        $before_total_5 = 0;
                                        $after_total_5 = 0;
                                        $total_5 = 0;
                                    @endphp

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
                                        <td colspan="2" class="text-end text-bold">รวมค่าใช้จ่ายในการขายและบริหาร
                                        </td>

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
                                        <td colspan="8" style="border: none; height: 32px;"></td>
                                        <!-- ใช้ height เพิ่มช่องว่าง -->
                                    </tr>


                                    <tr style="border: none; margin-top: 64px;">

                                        <th class="text-end" style="border: none;" colspan="2">
                                            ยอดรวมกำไร(ขาดทุน)สุทธิของงวดนี้</th>
                                        <td style="border: none;"></td>
                                        <td style="border: none;"
                                            class="text-end color-yellow  text-bold {{ $before_total_4 - $before_total_5 < 0 ? 'error-message' : '' }}">
                                            {{ $before_total_4 - $before_total_5 != 0 ? number_format($before_total_4 - $before_total_5, 2) : '' }}
                                        </td>
                                        <td style="border: none;"></td>
                                        <td style="border: none;"
                                            class="text-end color-green  text-bold  {{ $after_total_4 - $after_total_5 < 0 ? 'error-message' : '' }}">
                                            {{ $after_total_4 - $after_total_5 != 0 ? number_format($after_total_4 - $after_total_5, 2) : '' }}
                                        </td>
                                        <td style="border: none;"></td>
                                        <td class="text-end color-blue  text-bold {{ $total_4 - $total_5 < 0 ? 'error-message' : '' }}"
                                            style="border: none;">
                                            {{ number_format($total_4 - $total_5, 2) }}
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
