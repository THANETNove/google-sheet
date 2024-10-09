@extends('layouts.appHome')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">

                <div id="printableArea">
                    <div class="card">

                        <div class="container-company">
                            <div class="company">
                                <p><strong>{{ $user[0]->company }}</strong></p>
                                <p><strong>-- สมุดบัญชีรายงานซื้อ --</strong></p>
                                <p><strong> ตั้งแต่วันที่ {{ date('d-m-Y', strtotime($startDate)) }} จนถึงวันที่
                                        {{ date('d-m-Y', strtotime($endDate)) }}</strong></p>
                            </div>

                        </div>
                        <div class="date">
                            <p> วันเริ่มรอบบัญชี {{ $day }} {{ $monthThai }} {{ $currentYear }}</p>

                            <a href="{{ url('/export-pdf', $id) }}" target="_blank" class="btn btn-primary">
                                <i class='bx bxs-file-pdf'></i>&nbsp; PDF
                            </a>
                            <a href="{{ url('/export-excel', $id) }}" class="btn btn-primary">
                                <i class='bx bxs-file'></i>&nbsp; Excel
                            </a>
                        </div>
                        <div class="table-responsive m-3">
                            <table class="table">
                                <thead>
                                    <tr class="table-secondary">
                                        <th class="col-2">วันที่</th>
                                        <th class="col-1">เลขที่เอกสาร</th>
                                        <th class="col-4">บริษัท</th>
                                        <th class="col-3">คำอธิบาย</th>
                                        <th class="col-2">เดบิต</th>
                                        <th class="col-2">เครดิต</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    {{--  @php
                                        $previousId = null;
                                        $groupedQuery = $query->groupBy('id'); // Group the data by id
                                    @endphp

                                    @foreach ($groupedQuery as $id => $groupedData)
                                        @php
                                            $rowspan = count($groupedData) + 1; // Calculate the number of rows for the current id
                                            // คำนวณค่ารวมของ gls_debit และ gls_credit สำหรับแต่ละกลุ่ม
                                            $totalDebit = $groupedData->sum('gls_debit');
                                            $totalCredit = $groupedData->sum('gls_credit');
                                        @endphp

                                        @foreach ($groupedData as $index => $que)
                                            <tr>
                                                @if ($index === 0)
                                                    <!-- Display rowspan for the first row of each group -->
                                                    <td rowspan="{{ $rowspan }}">
                                                        {{ date('d-m-Y', strtotime($que->gl_date)) }}</td>
                                                    <td rowspan="{{ $rowspan }}">{{ $que->gl_document }}</td>
                                                    <td rowspan="{{ $rowspan }}">{{ $que->gl_company }}</td>
                                                @endif
                                                <td>{{ $que->gls_account_name }}</td>
                                                <td class="text-end">{{ number_format($que->gls_debit, 2) }}</td>
                                                <td class="text-end">{{ number_format($que->gls_credit, 2) }}</td>
                                            </tr>
                                        @endforeach

                                        <!-- เพิ่มแถวสำหรับผลรวมใต้ข้อมูล -->
                                        <tr>
                                            <td><strong>รวม</strong></td>
                                            <td class="text-end"><strong>{{ number_format($totalDebit, 2) }}</strong></td>
                                            <td class="text-end"> <strong>{{ number_format($totalCredit, 2) }}</strong>
                                            </td>
                                        </tr>
                                    @endforeach --}}
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
        return to top
    </button>
    <script>
        // ส่งค่า $user ไปยัง JavaScript
        const user = @json($user);

        // สมมุติว่าคุณต้องการแสดงค่า company ของผู้ใช้คนแรก
        document.getElementById('navbar-company').textContent = "บริษัท " + user[0].company; // แสดงค่าใน <strong> tag
    </script>
@endsection
