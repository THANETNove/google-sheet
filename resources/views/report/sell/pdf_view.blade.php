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
                                <p><strong>-- สมุดบัญชีรายงานขาย --</strong></p>
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
                                        <th>หมายเลขผู้เสียภาษี</th>
                                        <th>คำอธิบาย</th>
                                        <th>จำนวน</th>
                                        <th>ภาษี</th>
                                        <th>รวม</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @foreach ($query as $que)
                                        <tr>
                                            <td>{{ date('d-m-Y', strtotime($que->gl_date)) }}</td>
                                            <td>{{ $que->gl_document }}</td>
                                            <td>{{ $que->gl_company }}</td>
                                            <td>{{ $que->gl_taxid }}</td>
                                            <td>{{ $que->gl_description }}</td>
                                            <td class="text-end">{{ number_format($que->gl_amount, 2) }}</td>
                                            <td class="text-end">{{ number_format($que->gl_tax, 2) }}</td>
                                            <td class="text-end">{{ number_format($que->gl_total, 2) }}</td>
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
