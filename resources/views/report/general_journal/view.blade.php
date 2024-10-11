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
                                <p><strong>-- สมุดรายวันทั่วไป --</strong></p>
                                <p><strong>หมายเลขผู้เสียภาษี {{ $user->tax_id }}<strong></p>
                                <p><strong> ตั้งแต่วันที่ &nbsp; {{ date('d-m-Y', strtotime($startDate)) }}
                                        &nbsp;จนถึงวันที่&nbsp;
                                        {{ date('d-m-Y', strtotime($endDate)) }}</strong></p>
                            </div>

                        </div>
                        <form action="{{ route('search-date') }}" method="POST" class="container-date">
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

                            {{--      <a href="{{ url('/export-pdf', $id, [$startDate, $endDate]) }}" target="_blank"
                                class="btn btn-primary">
                                <i class='bx bxs-file-pdf'></i>&nbsp; PDF
                            </a> --}}
                            <a href="{{ url('/export-pdf/' . $id . '/' . urlencode($startDate) . '/' . urlencode($endDate)) }}"
                                target="_blank" class="btn btn-primary">
                                <i class='bx bxs-file-pdf'></i>&nbsp; PDF
                            </a>
                            <a href="{{ url('/export-excel/' . $id . '/' . urlencode($startDate) . '/' . urlencode($endDate)) }}"
                                class="btn btn-primary">
                                <i class='bx bxs-file'></i>&nbsp; Excel
                            </a>
                        </div>
                        <div class="table-responsive m-3">
                            <table class="table">
                                <thead>
                                    <tr class="table-secondary">
                                        <th class="child-1">#</th>
                                        <th class="child-2">วันที่</th>
                                        <th class="child-3">เลขที่เอกสาร</th>
                                        <th>บริษัท</th>
                                        <th>เดบิต</th>
                                        <th>เครดิต</th>
                                    </tr>
                                </thead>

                                <tbody class="table-border-bottom-0">
                                    @php
                                        $previousId = null;
                                        $groupedQuery = $query->groupBy('id'); // Group the data by id
                                        $i = 1;
                                    @endphp

                                    @foreach ($groupedQuery as $id => $groupedData)
                                        @php
                                            $rowspan = count($groupedData); // Calculate the number of rows for the current id

                                            // คำนวณค่ารวมของ gls_debit และ gls_credit สำหรับแต่ละกลุ่ม
                                            $totalDebit = $groupedData->sum('gls_debit');
                                            $totalCredit = $groupedData->sum('gls_credit');

                                            // รวม gls_account_name ทั้งหมด
                                            $accountNames = $groupedData
                                                ->pluck('gls_account_name')
                                                ->unique()
                                                ->toArray();
                                            $accountNamesStr = implode(
                                                '<br><br>',
                                                array_map(function ($name) {
                                                    return $name; // เพิ่ม - ก่อนหน้าชื่อบัญชี และเพิ่มสไตล์
                                                }, $accountNames),
                                            );
                                        @endphp

                                        @foreach ($groupedData as $index => $que)
                                            <tr>
                                                @if ($index === 0)
                                                    <!-- Display rowspan for the first row of each group -->
                                                    <td rowspan="{{ $rowspan }}">{!! $i++ !!}</td>
                                                    <td rowspan="{{ $rowspan }}">{!! date('d-m-Y', strtotime($que->gl_date)) !!}</td>
                                                    <td rowspan="{{ $rowspan }}">{!! $que->gl_document !!}</td>
                                                    <td rowspan="{!! $rowspan !!}">
                                                        {!! $que->gl_company !!}
                                                        &nbsp;-&nbsp;{{ $que->gl_description }}<br> {!! $accountNamesStr !!}
                                                    </td>
                                                @endif

                                                <!-- แสดงค่า debit และ credit ที่เลื่อนไปตรงกับ accountNamesStr -->
                                                <td class="text-end">
                                                    @if ($index === 0)
                                                        <br>
                                                    @endif
                                                    {!! number_format($que->gls_debit, 2) !!}
                                                </td>
                                                <td class="text-end">
                                                    @if ($index === 0)
                                                        <br>
                                                    @endif
                                                    {!! number_format($que->gls_credit, 2) !!}
                                                </td>
                                            </tr>
                                        @endforeach

                                        <!-- เพิ่มแถวสำหรับผลรวมใต้ข้อมูล -->


                                        <tr
                                            style="background-color: {{ $totalDebit != $totalCredit ? '#ff000026' : 'transparent' }};">
                                            <td colspan="3"></td>
                                            <td class="text-end"><strong>รวม</strong></td>
                                            <td class="text-end"><strong>{!! number_format($totalDebit, 2) !!}</strong></td>
                                            <td class="text-end"><strong>{!! number_format($totalCredit, 2) !!}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>

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

    @include('layouts.scrollToTop')
    <script>
        // ส่งค่า $user ไปยัง JavaScript
        const user = @json($user);

        // สมมุติว่าคุณต้องการแสดงค่า company ของผู้ใช้คนแรก
        document.getElementById('navbar-company').textContent = "บริษัท " + user[0].company; // แสดงค่าใน <strong> tag
    </script>
@endsection
