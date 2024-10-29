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

                                <p><strong>-- งบกำไร(ขาดทุน) --</strong></p>
                                <p><strong>หมายเลขผู้เสียภาษี {{ $user->tax_id }}<strong></p>
                                <p><strong> ตั้งแต่วันที่ &nbsp; {{ date('d-m-Y', strtotime($startDate)) }}
                                        &nbsp;จนถึงวันที่&nbsp;
                                        {{ date('d-m-Y', strtotime($endDate)) }}</strong></p>
                            </div>
                        </div>
                        <form action="{{ route('report/search-profit-statement') }}" method="POST" class="container-date">
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
                            <a href="{{ url('/profit-statement-pdf/' . $id . '/' . urlencode($startDate) . '/' . urlencode($endDate)) }}"
                                target="_blank" class="btn btn-primary">
                                <i class='bx bxs-file-pdf'></i>&nbsp; PDF
                            </a>
                            <a href="{{ url('/profit-statement-excel/' . $id . '/' . urlencode($startDate) . '/' . urlencode($endDate)) }}"
                                class="btn btn-primary">
                                <i class='bx bxs-file'></i>&nbsp; Excel
                            </a>
                        </div>
                        <div class="table-responsive m-3">


                            <table class="table">
                                <thead class="text-center">
                                    <tr class="table-secondary">
                                        <th class="text-center-vertical" rowspan="2">รหัสบัญชี</th>
                                        <th class="text-center-vertical" rowspan="2">ชื่อบัญชี</th>
                                        <th colspan="2">ยอดยกมาต้นงวด</th>
                                        <th colspan="2">ยอดยกมางวดนี้</th>
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
                                        // ฟังก์ชันสำหรับสะสมผลรวม
                                        $totals = [
                                            'before_total_debit_4' => 0,
                                            'before_total_credit_4' => 0,
                                            'after_total_debit_4' => 0,
                                            'after_total_credit_4' => 0,
                                            'before_total_debit_5' => 0,
                                            'before_total_credit_5' => 0,
                                            'after_total_debit_5' => 0,
                                            'after_total_credit_5' => 0,
                                        ];

                                        // ฟิลเตอร์และสะสมผลรวมสำหรับรหัสบัญชีที่ขึ้นต้นด้วย '4'
                                        $filteredEntries4 = $date_query->filter(
                                            fn($entry) => Str::startsWith($entry->gls_account_code, '4'),
                                        );
                                        foreach ($filteredEntries4 as $entry) {
                                            $totals['before_total_debit_4'] += $entry->before_total_debit ?? 0;
                                            $totals['before_total_credit_4'] += $entry->before_total_credit ?? 0;
                                            $totals['after_total_debit_4'] += $entry->after_total_debit ?? 0;
                                            $totals['after_total_credit_4'] += $entry->after_total_credit ?? 0;
                                        }

                                        // ฟิลเตอร์และสะสมผลรวมสำหรับรหัสบัญชีที่ขึ้นต้นด้วย '5'
                                        $filteredEntries5 = $date_query->filter(
                                            fn($entry) => Str::startsWith($entry->gls_account_code, '5'),
                                        );
                                        foreach ($filteredEntries5 as $entry) {
                                            $totals['before_total_debit_5'] += $entry->before_total_debit ?? 0;
                                            $totals['before_total_credit_5'] += $entry->before_total_credit ?? 0;
                                            $totals['after_total_debit_5'] += $entry->after_total_debit ?? 0;
                                            $totals['after_total_credit_5'] += $entry->after_total_credit ?? 0;
                                        }
                                    @endphp

                                    <tr>
                                        <td style="border: none;"></td>
                                        <th colspan="7" class="center" style="border: none;">
                                            รายได้จากการดำเนินงาน</th>

                                    </tr>
                                    @foreach ($filteredEntries4 as $entry)
                                        <tr>
                                            <td class="center">{{ $entry->gls_account_code }}</td>
                                            <td class="center">{{ $entry->gls_account_name }}</td>
                                            <td class="text-end color-back">
                                                {{ number_format($entry->before_total_debit, 2) }}</td>
                                            <td class="text-end color-back">
                                                {{ number_format($entry->before_total_credit, 2) }}</td>
                                            <td class="text-end color-green">
                                                {{ number_format($entry->after_total_debit, 2) }}</td>
                                            <td class="text-end color-green">
                                                {{ number_format($entry->after_total_credit, 2) }}</td>
                                            <td class="text-end color-blue"></td>
                                            <td class="text-end color-blue">
                                                {{ number_format($entry->before_total_credit - $entry->after_total_debit, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="2" class="text-end">รวมรายได้จากการดำเนินงาน</td>
                                        <td class="text-end color-back">
                                            {{ number_format($totals['before_total_debit_4'], 2) }}</td>
                                        <td class="text-end color-back">
                                            {{ number_format($totals['before_total_credit_4'], 2) }}</td>
                                        <td class="text-end color-green">
                                            {{ number_format($totals['after_total_debit_4'], 2) }}</td>
                                        <td class="text-end color-green">
                                            {{ number_format($totals['after_total_credit_4'], 2) }}</td>
                                        <td class="text-end color-blue"></td>
                                        <td class="text-end color-blue">
                                            {{ number_format(
                                                array_sum([
                                                    $totals['before_total_debit_4'],
                                                    $totals['before_total_credit_4'],
                                                    $totals['after_total_debit_4'],
                                                    $totals['after_total_credit_4'],
                                                ]),
                                                2,
                                            ) }}
                                        </td>
                                    </tr>


                                    {{-- 5% --}}
                                    <tr>
                                        <td style="border: none;"></td>
                                        <th colspan="7" class="center" style="border: none;">
                                            ค่าใช้จ่ายในการขายเเละบริหาร</th>
                                    </tr>
                                    @foreach ($filteredEntries5 as $entry)
                                        <tr>
                                            <td class="center">{{ $entry->gls_account_code }}</td>
                                            <td class="center">{{ $entry->gls_account_name }}</td>
                                            <td class="text-end color-back">
                                                {{ number_format($entry->before_total_debit, 2) }}</td>
                                            <td class="text-end color-back">
                                                {{ number_format($entry->before_total_credit, 2) }}</td>
                                            <td class="text-end color-green">
                                                {{ number_format($entry->after_total_debit, 2) }}</td>
                                            <td class="text-end color-green">
                                                {{ number_format($entry->after_total_credit, 2) }}</td>
                                            <td class="text-end color-blue">
                                                {{ number_format($entry->after_total_debit - $entry->before_total_credit, 2) }}
                                            </td>
                                            <td class="text-end color-blue"></td>
                                        </tr>
                                    @endforeach

                                    <tr>
                                        <td colspan="2" class="text-end">รวมค่าใช้จ่ายในการขายและบริหาร</td>
                                        <td class="text-end color-back">
                                            {{ number_format($totals['before_total_debit_5'], 2) }}</td>
                                        <td class="text-end color-back">
                                            {{ number_format($totals['before_total_credit_5'], 2) }}</td>
                                        <td class="text-end color-green">
                                            {{ number_format($totals['after_total_debit_5'], 2) }}</td>
                                        <td class="text-end color-green">
                                            {{ number_format($totals['after_total_credit_5'], 2) }}</td>

                                        <td class="text-end color-blue">
                                            {{ number_format(
                                                array_sum([
                                                    $totals['before_total_debit_5'],
                                                    $totals['before_total_credit_5'],
                                                    $totals['after_total_debit_5'],
                                                    $totals['after_total_credit_5'],
                                                ]),
                                                2,
                                            ) }}
                                        </td>
                                        <td class="text-end color-blue"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="8" style="border: none; height: 32px;"></td>
                                        <!-- ใช้ height เพิ่มช่องว่าง -->
                                    </tr>


                                    <tr style="border: none; margin-top: 64px;">
                                        <td class="text-end" style="border: none;"></td>
                                        <th class="text-end" style="border: none;">ยอดรวมกำไร(ขาดทุน)สุทธิของงวดนี้</th>
                                        <td style="border: none;"></td>
                                        <td style="border: none;"></td>
                                        <td style="border: none;"></td>
                                        <td class="text-end color-green text-bold" style="border: none;">
                                            {{ number_format(
                                                array_sum([
                                                    $totals['before_total_debit_4'],
                                                    $totals['before_total_credit_4'],
                                                    $totals['after_total_debit_4'],
                                                    $totals['after_total_credit_4'],
                                                ]),
                                                2,
                                            ) }}
                                        </td>
                                        <td style="border: none;"></td>
                                        <td style="border: none;" class="text-end color-blue text-bold">
                                            {{ number_format(
                                                array_sum([
                                                    $totals['before_total_debit_4'],
                                                    $totals['before_total_credit_4'],
                                                    $totals['after_total_debit_4'],
                                                    $totals['after_total_credit_4'],
                                                ]) -
                                                    array_sum([
                                                        $totals['before_total_debit_5'],
                                                        $totals['before_total_credit_5'],
                                                        $totals['after_total_debit_5'],
                                                        $totals['after_total_credit_5'],
                                                    ]),
                                                2,
                                            ) }}
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
