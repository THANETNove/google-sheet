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
                                        <th>วันที่</th>
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
                                            $totalDebit = $groupedData->sum('gls_debit'); // คำนวณค่ารวมของ gls_debit
                                            $totalCredit = $groupedData->sum('gls_credit'); // คำนวณค่ารวมของ gls_credit

                                            // รวมข้อมูลในแต่ละรายการของคำอธิบาย เดบิต และ เครดิต
                                            $accountNames = implode(
                                                '<br>',
                                                $groupedData->pluck('gls_account_name')->toArray(),
                                            );
                                            $debits = implode(
                                                '<br>',
                                                $groupedData
                                                    ->pluck('gls_debit')
                                                    ->map(function ($value) {
                                                        return number_format($value, 2);
                                                    })
                                                    ->toArray(),
                                            );
                                            $credits = implode(
                                                '<br>',
                                                $groupedData
                                                    ->pluck('gls_credit')
                                                    ->map(function ($value) {
                                                        return number_format($value, 2);
                                                    })
                                                    ->toArray(),
                                            );
                                        @endphp

                                        <tr class="group-row avoid-break">
                                            <td>{{ date('d-m-Y', strtotime($groupedData[0]->gl_date)) }}</td>
                                            <td>{{ $groupedData[0]->gl_document }}</td>
                                            <td>{{ $groupedData[0]->gl_company }}</td>
                                            <td class="text-start text-spacing">{!! $accountNames !!}
                                                <br>
                                                <strong>รวม</strong>
                                            </td> <!-- แสดงข้อมูลคำอธิบายแต่ละรายการในบรรทัดเดียวกัน -->
                                            <td class="text-end text-spacing">{!! $debits !!}
                                                <br>
                                                <strong> {{ number_format($totalDebit, 2) }}</strong>
                                            </td> <!-- แสดงเดบิตแต่ละรายการในบรรทัดเดียวกัน -->
                                            <td class="text-end text-spacing">{!! $credits !!}
                                                <br>
                                                <strong>{{ number_format($totalCredit, 2) }}</strong>
                                            </td> <!-- แสดงเครดิตแต่ละรายการในบรรทัดเดียวกัน -->
                                        </tr>
                                    @endforeach
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
