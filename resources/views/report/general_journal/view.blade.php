@extends('layouts.appHome')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">

                <div class="card">
                    <div class="table-responsive m-3">


                        {{--  <table class="table">
                            <thead>
                                <tr class="table-secondary">
                                    <th class="col-2">วันที่</th>
                                    <th>เลขที่เอกสาร</th>
                                    <th>บริษัท</th>
                                    <th>คำอธิบาย</th>
                                    <th>เดบิต</th>
                                    <th>เครดิต</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @php
                                    $previousId = null;
                                    $groupedQuery = $query->groupBy('id'); // Group the data by id
                                @endphp

                                @foreach ($groupedQuery as $id => $groupedData)
                                    @php

                                        $rowspan = count($groupedData); // Calculate the number of rows for the current id
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
                                            <td>{{ $que->gls_debit }}</td>
                                            <td>{{ $que->gls_credit }}</td>

                                        </tr>
                                    @endforeach
                                @endforeach


                            </tbody>
                        </table> --}}

                        <table class="table">
                            <thead>
                                <tr class="table-secondary">
                                    <th class="col-2">วันที่</th>
                                    <th>เลขที่เอกสาร</th>
                                    <th>บริษัท</th>
                                    <th>คำอธิบาย</th>
                                    <th>เดบิต</th>
                                    <th>เครดิต</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @php
                                    $previousId = null;
                                    $groupedQuery = $query->groupBy('id'); // Group the data by id
                                @endphp

                                @foreach ($groupedQuery as $id => $groupedData)
                                    @php
                                        $rowspan = count($groupedData); // Calculate the number of rows for the current id
                                        // คำนวณค่ารวมของ gls_debit และ gls_credit สำหรับแต่ละกลุ่ม
                                        $totalDebit = $groupedData->sum('gls_debit');
                                        $totalCredit = $groupedData->sum('gls_credit');
                                    @endphp

                                    @foreach ($groupedData as $index => $que)
                                        <tr>
                                            @if ($index === 0)
                                                <!-- Display rowspan for the first row of each group -->
                                                <td rowspan="{{ $rowspan + 1 }}">
                                                    {{ date('d-m-Y', strtotime($que->gl_date)) }}</td>
                                                <td rowspan="{{ $rowspan + 1 }}">{{ $que->gl_document }}</td>
                                                <td rowspan="{{ $rowspan + 1 }}">{{ $que->gl_company }}</td>
                                            @endif
                                            <td>{{ $que->gls_account_name }}</td>
                                            <td>{{ $que->gls_debit }}</td>
                                            <td>{{ $que->gls_credit }}</td>
                                        </tr>
                                    @endforeach

                                    <!-- เพิ่มแถวสำหรับผลรวมใต้ข้อมูล -->
                                    <tr>
                                        <td><strong>รวม</strong></td>
                                        <td><strong>{{ $totalDebit }}</strong></td>
                                        <td> <strong>{{ $totalCredit }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>




                    </div>
                </div>
            </div>


        </div>

    </div>
@endsection
