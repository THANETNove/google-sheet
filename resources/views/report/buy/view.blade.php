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

                                <p><strong>-- สมุดบัญชีรายงานซื้อ --</strong></p>
                                <p><strong>หมายเลขผู้เสียภาษี {{ $user->tax_id }}<strong></p>
                                <p><strong>ตั้งแต่วันที่ {{ date('d-m-Y', strtotime($startDate)) }} จนถึงวันที่
                                        {{ date('d-m-Y', strtotime($endDate)) }}<strong></p>
                            </div>
                        </div>
                        <form action="{{ route('search-buy') }}" method="POST" class="container-date">
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
                            <a href="{{ url('/buy-pdf', $id) }}" target="_blank" class="btn btn-primary">
                                <i class='bx bxs-file-pdf'></i>&nbsp; PDF
                            </a>
                            <a href="{{ url('/buy-excel', $id) }}" class="btn btn-primary">
                                <i class='bx bxs-file'></i>&nbsp; Excel
                            </a>
                        </div>
                        <div class="table-responsive m-3">
                            <table class="table">
                                <thead>
                                    <tr class="table-secondary">
                                        <th class="col-3">วันที่</th>
                                        <th class="col-1">เลขที่เอกสาร</th>
                                        <th class="col-3">บริษัท</th>
                                        <th class="col-3">หมายเลขผู้เสียภาษี</th>
                                        <th class="col-2">คำอธิบาย</th>
                                        <th class="col-1">จำนวน</th>
                                        <th class="col-1">ภาษี</th>
                                        <th class="col-1">รวม</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @foreach ($query as $index => $que)
                                        <tr>
                                            <td>
                                                {{ date('d-m-Y', strtotime($que->gl_date)) }}</td>
                                            {{-- <td>{{ $que->gl_document }}</td> --}}
                                            <td>

                                                @if ($que->gl_url)
                                                    <a href="{{ $que->gl_url }}" target="_blank" class="opan-message"
                                                        rel="noopener noreferrer">
                                                        {{ $que->gl_document }}
                                                        <span class="id-message">
                                                            หน้า {{ $que->gl_page }}
                                                        </span>
                                                    </a>
                                                @else
                                                    {{ $que->gl_document }}
                                                @endif
                                            </td>
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
    </script>
@endsection
