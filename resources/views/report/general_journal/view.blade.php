@extends('layouts.appHome')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y" id="printableArea">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">

                <div>
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
                        @php

                            $route =
                                Auth::check() 
                                    ? route('report/search-date')
                                    : isset($user->username) &&
                                        isset($user->password) &&
                                        route('user-report/search-date', [
                                            'username' => $user->username,
                                            'password' => $user->password,
                                        ]);
                            $url_export_pdf =
                                Auth::check() 
                                    ? url(
                                        '/export-pdf/' . $id . '/' . urlencode($startDate) . '/' . urlencode($endDate),
                                    )
                                    : isset($user->username) &&
                                        isset($user->password) &&
                                        url(
                                            '/user-export-pdf/' .
                                                $id .
                                                '/' .
                                                urlencode($startDate) .
                                                '/' .
                                                urlencode($endDate) .
                                                '?username=' .
                                                urlencode($user->username) .
                                                '&password=' .
                                                urlencode($user->password),
                                        );
                            $url_export_excel =
                                Auth::check() 
                                    ? url(
                                        '/export-excel/' .
                                            $id .
                                            '/' .
                                            urlencode($startDate) .
                                            '/' .
                                            urlencode($endDate),
                                    )
                                    : isset($user->username) &&
                                        isset($user->password) &&
                                        url(
                                            '/user-export-excel/' .
                                                $id .
                                                '/' .
                                                urlencode($startDate) .
                                                '/' .
                                                urlencode($endDate) .
                                                '?username=' .
                                                urlencode($user->username) .
                                                '&password=' .
                                                urlencode($user->password),
                                        );
                        @endphp
                        <form action="{{ $route }}" method="POST" class="container-date">
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


                            <a href="{{ $url_export_pdf }}" target="_blank" class="btn btn-primary">
                                <i class='bx bxs-file-pdf'></i>&nbsp; PDF
                            </a>

                            <a href="{{ $url_export_excel }}" class="btn btn-primary">
                                <i class='bx bxs-file'></i>&nbsp; Excel
                            </a>
                        </div>
                        <div class="table-responsive m-3">
                            @php
                                $previousId = null; // Variable to keep track of the previous id
                                $i = 1;
                            @endphp

                            <table class="table">
                                <thead class="text-center">
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
                                    @foreach ($query as $ledger)
                                        @php
                                            $totalDebit = 0;
                                            $totalCredit = 0;
                                        @endphp

                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ date('d-m-Y', strtotime($ledger->gl_date)) }}</td>
                                            <td>

                                                @if ($ledger->gl_url)
                                                    <a href="{{ $ledger->gl_url }}" target="_blank" class="opan-message"
                                                        rel="noopener noreferrer">
                                                        {{ $ledger->gl_document }}
                                                        <span class="id-message">
                                                            หน้า {{ $ledger->gl_page }}
                                                        </span>
                                                    </a>
                                                @else
                                                    {{ $ledger->gl_document }}
                                                @endif
                                            </td>
                                            <td>{{ $ledger->gl_company }}&nbsp;-&nbsp;{{ $ledger->gl_description }}</td>
                                            <td></td>
                                            <td class="hide-column"></td> <!-- Placeholder for subs -->


                                        </tr>

                                        <!-- Now loop through the related subs for each gl_code -->
                                        @foreach ($ledger->subs->sortBy('gls_id') as $sub)
                                            {{--   @foreach ($ledger->subs->unique() as $sub) --}}
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
                                            <td class="text-end"><strong>{{ number_format($totalDebit, 2) }}</strong></td>
                                            <td class="text-end"><strong>{{ number_format($totalCredit, 2) }}</strong></td>
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
    <!-- เพิ่มปุ่มเลื่อนกลับไปยังด้านบน -->
    <button id="scrollToTop" class="btn btn-primary" style="position: fixed; bottom: 20px; right: 20px; display: none;">
        <i class='bx bxs-arrow-to-top'></i>
        &nbsp;
        Top
    </button>

    @include('layouts.scrollToTop')
    <script>
        // ส่งค่า $user ไปยัง JavaScript
        const user = @json($user);

        // สมมุติว่าคุณต้องการแสดงค่า company ของผู้ใช้คนแรก
        document.getElementById('navbar-company').textContent = "บริษัท " + user[0].company; // แสดงค่าใน <strong> tag



        function viewPDF() {
            const {
                jsPDF
            } = window.jspdf;
            const content = document.getElementById('printableArea');

            html2canvas(content, {
                scale: 2,
                useCORS: true
            }).then((canvas) => {
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF('p', 'mm', 'a4');
                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

                pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                const pdfBlob = pdf.output('blob');
                const pdfUrl = URL.createObjectURL(pdfBlob);

                // เปิด PDF ในหน้าต่างใหม่
                window.open(pdfUrl);

                // สร้างปุ่มสำหรับพิมพ์และดาวน์โหลด
                const optionsDiv = document.createElement('div');
                optionsDiv.innerHTML = `
                    <button onclick="downloadPDF('${pdfUrl}')">Download PDF</button>
                    <button onclick="printPDF('${pdfUrl}')">Print PDF</button>
                `;
                document.body.appendChild(optionsDiv);
            }).catch((error) => {
                console.error("Error generating PDF:", error);
            });
        }

        function downloadPDF(pdfUrl) {
            const link = document.createElement('a');
            link.href = pdfUrl;
            link.download = 'document.pdf';
            link.click();
        }

        function printPDF(pdfUrl) {
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = pdfUrl;
            document.body.appendChild(iframe);
            iframe.contentWindow.print();
        }
    </script>
@endsection
