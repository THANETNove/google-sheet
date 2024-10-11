<!DOCTYPE html>
<html lang="th">

<head>
    <title>สมุดรายวันทั่วไป</title>

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
                                <p><strong>{{ $user->company }}</strong></p>
                                <p><strong>-- สมุดรายวันทั่วไป --</strong></p>
                                <p><strong> ตั้งแต่วันที่ {{ date('d-m-Y', strtotime($startDate)) }} จนถึงวันที่
                                        {{ date('d-m-Y', strtotime($endDate)) }}</strong></p>
                            </div>

                        </div>
                        <div class="date">
                            <p> วันเริ่มรอบบัญชี {{ $day }} {{ $monthThai }} {{ $currentYear }}</p>
                            <p>หมายเลขผู้เสียภาษี {{ $user->tax_id }}</p>
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
                                                '<br>',
                                                array_map(function ($name) {
                                                    return '&nbsp;-&nbsp;' . $name; // เพิ่ม - ก่อนหน้าชื่อบัญชี
                                                }, $accountNames),
                                            ); // รวมชื่อบัญชีเป็น string
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
                                                        &nbsp;-&nbsp;{{ $que->gl_description }}<br>{!! $accountNamesStr !!}
                                                    </td>
                                                @endif


                                                <td class="text-end">{!! number_format($que->gls_debit, 2) !!}</td>
                                                <td class="text-end">{!! number_format($que->gls_credit, 2) !!}</td>
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
</body>

</html>
