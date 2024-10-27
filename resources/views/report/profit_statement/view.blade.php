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
                                        <th colspan="2">ยอดยกมางวดนี้ </th>
                                        <th colspan="2">ยอดสะสมคงเหลือ </th>

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
                                        $quoted_net_balance4 = 0;
                                        $net_balance4 = 0;
                                        $quoted_net_balance5 = 0;
                                        $net_balance5 = 0;
                                    @endphp

                                    <tr>
                                        <td style="border: none;"></td>
                                        <th colspan="7" class="center" style="border: none;">
                                            รายได้จากการดำเนินงาน</th>

                                    </tr>
                                    @foreach ($query as $entry)
                                        @if (Str::startsWith($entry->gls_account_code, '4'))
                                            @php
                                                $quoted_net_balance4 += $entry->quoted_net_balance;
                                                $net_balance4 += $entry->net_balance;
                                            @endphp

                                            <tr>

                                                <td class="center">{{ $entry->gls_account_code }}</td>
                                                <td class="center">{{ $entry->gls_account_name }}</td>
                                                <td class="text-end"></td>
                                                <td
                                                    class="text-end {{ $entry->quoted_net_balance < 0 ? 'error-message' : '' }}">
                                                    {{ number_format($entry->quoted_net_balance, 2) }}</td>
                                                <td class="text-end"></td>
                                                <td class="text-end {{ $entry->net_balance < 0 ? 'error-message' : '' }}">
                                                    {{ number_format($entry->net_balance, 2) }}</td>
                                                <td class="text-end"></td>
                                                <td
                                                    class="text-end {{ $entry->net_balance + $entry->quoted_net_balance < 0 ? 'error-message' : '' }}">
                                                    {{ number_format($entry->net_balance + $entry->quoted_net_balance, 2) }}
                                                </td>

                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr>
                                        <td style="border: none;"></td>
                                        <td style="border: none;"></td>
                                        <th class="center text-end" style="border: none;">
                                            รายได้จากการดำเนินงาน</th>
                                        <th class="text-end  {{ $quoted_net_balance4 < 0 ? 'error-message' : '' }}"
                                            style="border: none;">
                                            {{ number_format($quoted_net_balance4, 2) }}</th>
                                        <td style="border: none;"></td>
                                        <th class="text-end {{ $net_balance4 < 0 ? 'error-message' : '' }}"
                                            style="border: none;">
                                            {{ number_format($net_balance4, 2) }}</th>
                                        <td style="border: none;"></td>
                                        <th class="text-end  {{ $quoted_net_balance4 + $net_balance4 < 0 ? 'error-message' : '' }}"
                                            style="border: none;">
                                            {{ number_format($quoted_net_balance4 + $net_balance4, 2) }}</th>
                                    </tr>


                                    {{-- 5% --}}
                                    <tr>
                                        <td style="border: none;"></td>
                                        <th colspan="7" class="center" style="border: none;">
                                            รายได้จากการดำเนินงาน</th>
                                    </tr>
                                    @foreach ($query as $entry)
                                        @if (Str::startsWith($entry->gls_account_code, '5'))
                                            @php
                                                $quoted_net_balance5 += $entry->quoted_net_balance;
                                                $net_balance5 += $entry->net_balance;
                                            @endphp

                                            <tr>

                                                <td class="center">{{ $entry->gls_account_code }}</td>
                                                <td class="center">{{ $entry->gls_account_name }}</td>
                                                <td
                                                    class="text-end {{ $entry->quoted_net_balance < 0 ? 'error-message' : '' }}">
                                                    {{ number_format($entry->quoted_net_balance, 2) }}</td>
                                                <td class="text-end"></td>
                                                <td class="text-end {{ $entry->net_balance < 0 ? 'error-message' : '' }}">
                                                    {{ number_format($entry->net_balance, 2) }}</td>
                                                <td class="text-end"></td>
                                                <td
                                                    class="text-end {{ $entry->net_balance + $entry->quoted_net_balance < 0 ? 'error-message' : '' }}">
                                                    {{ number_format($entry->quoted_net_balance + $entry->net_balance, 2) }}
                                                </td>
                                                <td class="text-end"></td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr>
                                        <td style="border: none;"></td>
                                        <td style="border: none;"></td>
                                        <th class="center text-end" style="border: none;">
                                            รายได้จากการดำเนินงาน</th>
                                        <th class="text-end {{ $quoted_net_balance5 < 0 ? 'error-message' : '' }}"
                                            style="border: none;">
                                            {{ number_format($quoted_net_balance5, 2) }}</th>

                                        <th class="text-end {{ $net_balance5 < 0 ? 'error-message' : '' }}"
                                            style="border: none;">
                                            {{ number_format($net_balance5, 2) }}</th>
                                        <td style="border: none;"></td>
                                        <th class="text-end {{ $quoted_net_balance5 + $net_balance5 < 0 ? 'error-message' : '' }}"
                                            style="border: none;">
                                            {{ number_format($quoted_net_balance5 + $net_balance5, 2) }}</th>
                                        <td style="border: none;"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="8" style="border: none; height: 32px;"></td>
                                        <!-- ใช้ height เพิ่มช่องว่าง -->
                                    </tr>
                                    @php
                                        $total_balance4 = $quoted_net_balance4 + $net_balance4;
                                        $total_balance5 = $quoted_net_balance5 + $net_balance5;
                                        $overall_total = $total_balance4 + $total_balance5;
                                    @endphp

                                    <tr style="border: none; margin-top: 64px;">
                                        <td class="text-end" style="border: none;"></td>
                                        <td style="border: none;"></td>
                                        <th class="text-end" style="border: none;">ยอดรวมกำไร(ขาดทุน)สุทธิของงวดนี้</th>
                                        <td style="border: none;"></td>
                                        <th style="border: none;"
                                            class="text-end {{ $total_balance4 < 0 ? 'error-message' : '' }}">
                                            {{ number_format($total_balance4, 2) }}
                                        </th>
                                        <td style="border: none;"></td>
                                        <th style="border: none;"
                                            class="text-end {{ $total_balance5 < 0 ? 'error-message' : '' }}">
                                            {{ number_format($total_balance5, 2) }}
                                        </th>
                                        <th style="border: none;"
                                            class="text-end {{ $overall_total < 0 ? 'error-message' : '' }}">
                                            {{ number_format($overall_total, 2) }}
                                        </th>
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
