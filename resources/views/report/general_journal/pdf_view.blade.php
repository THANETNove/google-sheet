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
                            {{--  <table class="table">
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
                                        $groupedQuery = $query->groupBy('id'); // Group the data by id
                                        $i = 1;
                                    @endphp

                                    @foreach ($groupedQuery as $id => $groupedData)
                                        @php
                                            $totalDebit = $groupedData->sum('gls_debit');
                                            $totalCredit = $groupedData->sum('gls_credit');

                                            // Combine gls_account_name with their respective debit and credit
                                            $accountDetails = $groupedData
                                                ->map(function ($que) {
                                                    return $que->gls_account_name .
                                                        '&nbsp;&nbsp;' .
                                                        number_format($que->gls_debit, 2) .
                                                        '&nbsp;&nbsp;' .
                                                        number_format($que->gls_credit, 2);
                                                })
                                                ->implode('<br>');

                                        @endphp

                                        <!-- Use only one <tr> for each group -->
                                        <tr style="border-bottom: 2px solid #000;"> <!-- Black border line -->
                                            <td>{!! $i++ !!}</td>
                                            <td>{!! date('d-m-Y', strtotime($groupedData->first()->gl_date)) !!}</td>
                                            <td>{!! $groupedData->first()->gl_document !!}</td>
                                            <td>
                                                {!! $groupedData->first()->gl_company !!}
                                                &nbsp;-&nbsp;{{ $groupedData->first()->gl_description }}
                                                <br>
                                                {!! $accountDetails !!}
                                                <!-- Display account names with debit and credit in one cell -->
                                            </td>

                                            <td colspan="4" class="text-end"><strong>{!! number_format($totalDebit, 2) !!}</strong>
                                            </td>
                                            <td class="text-end"><strong>{!! number_format($totalCredit, 2) !!}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table> --}}
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">GL Code</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Document</th>
                                        <th scope="col">Company</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i = 1; @endphp

                                    <!-- Loop through each group -->
                                    @foreach ($query as $gl_code => $group)
                                        <!-- Display the GL code as a header for each group -->
                                        <tr>
                                            <td colspan="5">
                                                <strong>GL Code: {{ $gl_code }}</strong>
                                            </td>
                                        </tr>

                                        <!-- Loop through the rows in each group -->
                                        @foreach ($group as $que)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $que->gl_code }}</td>
                                                <td>{{ date('d-m-Y', strtotime($que->gl_date)) }}</td>
                                                <td>{{ $que->gl_document }}</td>
                                                <td>{{ $que->gl_company }}</td>
                                            </tr>
                                        @endforeach
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
