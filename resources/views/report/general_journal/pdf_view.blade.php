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
                                @php
                                    $i = 1;

                                @endphp
                                <tbody class="table-border-bottom-0">
                                    @foreach ($query as $ledger)
                                        @php

                                            $totalDebit = 0;
                                            $totalCredit = 0;
                                        @endphp

                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ date('d-m-Y', strtotime($ledger->gl_date)) }}</td>
                                            <td>
                                                {{ $ledger->gl_document }}
                                            </td>
                                            <td>{{ $ledger->gl_company }}&nbsp;-&nbsp;{{ $ledger->gl_description }}
                                            </td>
                                            <td></td>
                                            <td class="hide-column"></td> <!-- Placeholder for subs -->


                                        </tr>

                                        <!-- Now loop through the related subs for each gl_code -->

                                        @foreach ($ledger->subs->unique() as $sub)
                                            <tr>
                                                <td class="hide-column"></td>
                                                <td class="hide-column"></td>
                                                <td class="hide-column"></td>
                                                <td> &nbsp; {{ $sub->gls_account_code }}&nbsp; &nbsp;
                                                    {{ $sub->gls_account_name }}</td>

                                                <td class="text-end">{{ number_format($sub->gls_debit, 2) }}</td>
                                                <td class="text-end">{{ number_format($sub->gls_credit, 2) }}</td>
                                            </tr>

                                            @php
                                                // สะสมผลรวม
                                                $totalDebit += $sub->gls_debit;
                                                $totalCredit += $sub->gls_credit;
                                            @endphp
                                        @endforeach

                                        @php
                                            // ตรวจสอบว่าผลรวมเท่ากันหรือไม่
                                            $isEqual = number_format($totalDebit, 2) == number_format($totalCredit, 2);

                                        @endphp

                                        <tr @if (!$isEqual) style="background-color: #ffcccc;" @endif>
                                            <td colspan="4" class="text-end"><strong>รวม</strong>
                                            </td>
                                            <td class="text-end"><strong>{{ number_format($totalDebit, 2) }}</strong>
                                            </td>
                                            <td class="text-end"><strong>{{ number_format($totalCredit, 2) }}</strong>
                                            </td>
                                        </tr>
                                        @php
                                            $previousId = $ledger->id;
                                        @endphp
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
