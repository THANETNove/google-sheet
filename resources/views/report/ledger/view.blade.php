@extends('layouts.appHome')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">

                <div id="printableArea">
                    @foreach ($date_query as $accountCode => $queries)
                        <div class="card">
                            <div class="container-company">
                                <div class="company">
                                    <p><strong>{{ $user->company }}</strong></p>
                                    <p><strong>-- บัญชีแยกประเภท {{ $accountCode }} --</strong></p>
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
                                            <th>วันที่</th>
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
                                            $accumulatedTotal = 0; // ตัวแปรเริ่มต้นสำหรับการสะสม
                                            $beginning_accumulation = 0; // ตัวแปรเริ่มต้นสำหรับการสะสม
                                        @endphp
                                        @foreach ($queries as $query)
                                            @php
                                                // เพิ่มค่าเดบิต และลดค่าเครดิตจากผลรวมสะสม
                                                $accumulatedTotal += $query->gls_debit - $query->gls_credit;
                                                $beginning_accumulation += $query->gls_debit - $query->gls_credit;
                                            @endphp
                                            <tr>
                                                <td>{{ date('d-m-Y', strtotime($query->gls_gl_date)) }}</td>
                                                <td>{{ $query->gls_gl_document }}</td>
                                                <td>{{ $query->gls_account_name }}</td>
                                                <td class="text-end  {{ $query->gls_debit < 0 ? 'error-message' : '' }}">
                                                    {{ number_format($query->gls_debit, 2) }}</td>
                                                <td class="text-end {{ $query->gls_credit < 0 ? 'error-message' : '' }}">
                                                    {{ number_format($query->gls_credit, 2) }}</td>
                                                <td class="text-end  {{ $accumulatedTotal < 0 ? 'error-message' : '' }}">
                                                    {{ number_format($accumulatedTotal, 2) }}</td>
                                                <td
                                                    class="text-end {{ $beginning_accumulation < 0 ? 'error-message' : '' }}">
                                                    {{ number_format($beginning_accumulation, 2) }}</td>


                                            </tr>
                                        @endforeach
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
