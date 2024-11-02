<!DOCTYPE html>
<html lang="th">

<head>
    <title>สมุดรายวันทั่วไป</title>
    @include('layouts.head_pdf')

</head>

<body>
    <div id="printableArea">
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

        @php $i = 1; @endphp

        @foreach ($chunks as $chunk)
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>วันที่</th>
                        <th>เลขที่เอกสาร</th>
                        <th>บริษัท</th>
                        <th>เดบิต</th>
                        <th>เครดิต</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($chunk as $ledger)
                        @php
                            $totalDebit = 0;
                            $totalCredit = 0;
                        @endphp

                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ date('d-m-Y', strtotime($ledger->gl_date)) }}</td>
                            <td>{{ $ledger->gl_document }}</td>
                            <td>{{ $ledger->gl_company }} - {{ $ledger->gl_description }}</td>
                            <td></td>
                            <td></td>
                        </tr>

                        @foreach ($ledger->subs->unique() as $sub)
                            <tr>
                                <td colspan="3"></td>
                                <td>&nbsp;{{ $sub->gls_account_code }} - {{ $sub->gls_account_name }}</td>
                                <td class="text-end">{{ number_format($sub->gls_debit, 2) }}</td>
                                <td class="text-end">{{ number_format($sub->gls_credit, 2) }}</td>
                            </tr>

                            @php
                                $totalDebit += $sub->gls_debit;
                                $totalCredit += $sub->gls_credit;
                            @endphp
                        @endforeach

                        <tr style="background-color: {{ $totalDebit !== $totalCredit ? '#ffcccc' : '#ffffff' }}">
                            <td colspan="4" class="text-end"><strong>รวม</strong></td>
                            <td class="text-end"><strong>{{ number_format($totalDebit, 2) }}</strong></td>
                            <td class="text-end"><strong>{{ number_format($totalCredit, 2) }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="page-break"></div> <!-- แยกหน้าแต่ละ chunk -->
        @endforeach
    </div>
</body>

</html>
