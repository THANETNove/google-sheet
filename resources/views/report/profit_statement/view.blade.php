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
                            <a href="{{ url('/sell-pdf/' . $id . '/' . urlencode($startDate) . '/' . urlencode($endDate)) }}"
                                target="_blank" class="btn btn-primary">
                                <i class='bx bxs-file-pdf'></i>&nbsp; PDF
                            </a>
                            <a href="{{ url('/sell-excel/' . $id . '/' . urlencode($startDate) . '/' . urlencode($endDate)) }}"
                                class="btn btn-primary">
                                <i class='bx bxs-file'></i>&nbsp; Excel
                            </a>
                        </div>
                        <div class="table-responsive m-3">


                            <table>
                                <thead>
                                    <tr>
                                        <th class="center">รหัสบัญชี</th>
                                        <th class="center">เลขบัญชี</th>
                                        <th class="center">ชื่อบัญชี</th>
                                        <th>ยอดยกมาต้นงวด (เดบิต)</th>
                                        <th>ยอดยกมาต้นงวด (เครดิต)</th>
                                        <th>ยอดยกมางวดนี้ (เดบิต)</th>
                                        <th>ยอดยกมางวดนี้ (เครดิต)</th>
                                        <th>ยอดสะสมคงเหลือ (เดบิต)</th>
                                        <th>ยอดสะสมคงเหลือ (เครดิต)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($query as $entry)
                                        <tr>
                                            <td class="center">{{ date('d-m-Y', strtotime($entry->gls_gl_date)) }}</td>
                                            <td class="center">{{ $entry->gls_account_code }}</td>
                                            <td class="center">{{ $entry->gls_account_name }}</td>

                                            <td></td>
                                            <td>{{ number_format($entry->quoted_net_balance, 2) }}</td>
                                            <td></td>
                                            <td>{{ number_format($entry->net_balance, 2) }}</td>
                                            <td></td>
                                            <td>{{ number_format($entry->net_balance + $entry->quoted_net_balance, 2) }}
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <h3>รวมยอดทั้งหมด</h3>
                            <table>
                                <thead>
                                    <tr>
                                        <th>รวมเดบิต</th>
                                        <th>รวมเครดิต</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        {{--   <td>{{ number_format($query->sum('total_debit'), 2) }}</td>
                                            color: #acacac !important;
    font-size: 16px !important;
    font-weight: 500;
    cursor: pointer;
                                        <td>{{ number_format($query->sum('total_credit'), 2) }}</td> --}}
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
