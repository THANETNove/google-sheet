@extends('layouts.appHome')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <a href="{{ url('company-create') }}" class="col-2 m-3 ms-auto">
            <button type="button" class="btn btn-primary mb-4">
                <i class='bx bxs-add-to-queue'></i>&nbsp; เพิ่มบริษัท
            </button>
        </a>

        @if (session('success') || session('message'))
            <div class="alert alert-info mb-3">
                {{ session('success') }} {{ session('message') }}
            </div>
        @endif
        <p class="alert alert-info mb-3" id="copy-url" style="display: none">คัดลอกลิงค์เรียบร้อย!</p>

        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="ค้นหาบริษัท..." />
        </div>

        <div class="row">
            @php
                $colors = [
                    '#007bff', // --bs-blue
                    '#6610f2', // --bs-indigo
                    '#696cff', // --bs-purple
                    '#e83e8c', // --bs-pink
                    '#ff3e1d', // --bs-red
                    '#fd7e14', // --bs-orange
                    '#ffab00', // --bs-yellow
                    '#71dd37', // --bs-green
                    '#20c997', // --bs-teal
                    '#03c3ec', // --bs-cyan
                    '#233446', // --bs-dark
                ];
            @endphp

            @foreach ($query as $index => $que)
                <div class="col-lg-4 col-md-4 col-sm-6  col-12 mb-4 search-name">
                    <div class="card  cursor-pointer">
                        <div class="card-body">
                            <div onclick="window.location='{{ url('select-card', $que->id) }}'">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <i class='bx bxs-buildings'
                                        style="font-size: 32px; color: {{ $colors[$index % count($colors)] }};"></i>
                                    <div class="dropdown" onclick="event.stopPropagation();">
                                        <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                            <a class="dropdown-item" href="{{ url('company-edit', $que->id) }}"><i
                                                    class="bx bx-edit-alt me-2"></i> Edit</a>
                                            <a class="dropdown-item" href="{{ url('company-delete', $que->id) }}"
                                                onclick="return confirm('คุณต้องการลบข้อมูลนี้หรือไม่?');"><i
                                                    class="bx bx-trash me-2"></i> Delete</a>
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $accounting_period = $que->accounting_period;

                                    // วันที่ปัจจุบัน
                                    $currentDate = new DateTime();
                                    $currentYear = $currentDate->format('Y');
                                    $currentMonth = $currentDate->format('m');
                                    $currentDay = $currentDate->format('d');

                                    // แยกวันและเดือนออกจาก accounting_period
                                    $accountingDate = DateTime::createFromFormat('j/n', $accounting_period);

                                    if ($accountingDate === false) {
                                        // ถ้าไม่สามารถแปลงวันที่ได้ ให้แสดงข้อความแจ้งเตือน
                                        $fiscalYearStart = 'รูปแบบวันที่ไม่ถูกต้อง';
                                        $fiscalYearEnd = 'รูปแบบวันที่ไม่ถูกต้อง';
                                    } else {
                                        // วันและเดือนของรอบบัญชี
                                        $accountingMonth = $accountingDate->format('m');
                                        $accountingDay = $accountingDate->format('d');

                                        // กำหนดปีเริ่มต้นและสิ้นสุดตามรอบบัญชี
                                        $accountingDateThisYear = (clone $accountingDate)->setDate(
                                            $currentYear,
                                            $accountingMonth,
                                            $accountingDay,
                                        );

                                        if ($accountingDateThisYear < $currentDate) {
                                            // ถ้ารอบบัญชีน้อยกว่าวันปัจจุบัน (แปลว่าเป็นรอบปีที่แล้ว)
                                            $fiscalYearStart = (clone $accountingDateThisYear)->setDate(
                                                $currentYear,
                                                $accountingMonth,
                                                $accountingDay,
                                            );
                                            $fiscalYearEnd = (clone $fiscalYearStart)
                                                ->modify('last day of this month')
                                                ->setDate($currentYear + 1, $accountingMonth, $accountingDay - 1);
                                        } else {
                                            // ถ้าวันบัญชีมากกว่าวันปัจจุบัน (แปลว่าเป็นรอบบัญชีปีนี้ถึงปีถัดไป)
                                            $fiscalYearStart = (clone $accountingDateThisYear)->setDate(
                                                $currentYear - 1,
                                                $accountingMonth,
                                                $accountingDay,
                                            );
                                            $fiscalYearEnd = (clone $fiscalYearStart)
                                                ->modify('last day of this month')
                                                ->setDate($currentYear, $accountingMonth, $accountingDay - 1);
                                        }

                                        // แปลงเป็นรูปแบบที่ต้องการ
                                        $fiscalYearStartFormatted = $fiscalYearStart->format('d/m/Y');
                                        $fiscalYearEndFormatted = $fiscalYearEnd->format('d/m/Y');
                                    }

                                @endphp
                                <div class="card-text">
                                    <span class="fw-semibold d-block mb-1">{{ $que->company }}</span>
                                    <span class="d-block mb-1">รอบบัญชี {{ $que->accounting_period }}</span>
                                    <span class="d-block mb-1">ตั้งเเต่ {{ $fiscalYearStartFormatted }} -
                                        {{ $fiscalYearEndFormatted }}</span>
                                    <small class="d-block">General Ledger <span
                                            style="float: right;">{{ $que->general_ledger_count }}</span></small>
                                    <small class="d-block">General Ledger Sub <span
                                            style="float: right;">{{ $que->general_ledger_sub_count }}</span></small>
                                    <small class="d-block">Account_Code <span
                                            style="float: right;">{{ $que->account_code_count }}</span></small>
                                </div>
                            </div>
                            <div class="mt-3 ">
                                <p>คัลอก </p>

                                <button class="btn btn-primary mb-3"
                                    onclick="copyToClipboard(`user-report/general-journal?username={{ $que->name }}&password={{ $que->password }}`)">รายงานทั่วไป</button>

                                <button class="btn btn-primary mb-3"
                                    onclick="copyToClipboard(`user-report/buy?username={{ $que->name }}&password={{ $que->password }}`)">รายงานซื้อ</button>
                                <button class="btn btn-primary mb-3"
                                    onclick="copyToClipboard(`user-report/sell?username={{ $que->name }}&password={{ $que->password }}`)">รายการขาย</button>
                                <button class="btn btn-primary mb-3"
                                    onclick="copyToClipboard(`user-report/profit-statement?username={{ $que->name }}&password={{ $que->password }}`)">งบกำไร(ขาดทุน)</button>
                                <button class="btn btn-primary mb-3"
                                    onclick="copyToClipboard(`user-report/trial-balance-before-closing?username={{ $que->name }}&password={{ $que->password }}`)">
                                    งบทดลองก่อนปิดบัญชี</button>
                                <button class="btn btn-primary mb-3"
                                    onclick="copyToClipboard(`user-report/account-balance-sheet?username={{ $que->name }}&password={{ $que->password }}`)">งบดุลบัญชี</button>
                                <button class="btn btn-primary mb-3"
                                    onclick="copyToClipboard(`user-report/ledger?username={{ $que->name }}&password={{ $que->password }}`)">บัญชีเเยกประเภท</button>
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach


        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const cards = document.querySelectorAll('.search-name');

            searchInput.addEventListener('input', function() {
                const searchValue = searchInput.value.toLowerCase();

                cards.forEach(card => {
                    const companyName = card.querySelector('.card-text .fw-semibold').textContent
                        .toLowerCase();

                    if (companyName.includes(searchValue)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });


        });

        function copyToClipboard(path) {


            // Define the URL to be copied (adjust as needed)
            const domain = 'http://localhost:8000/';
            const domainLocal = 'http://localhost:8000/';
            var url = domainLocal + path;


            console.log("url", url);

            // Create a temporary element to store the URL
            var tempInput = document.createElement("input");
            tempInput.value = url;
            document.body.appendChild(tempInput);

            // Select and copy the text in the element
            tempInput.select();
            document.execCommand("copy");

            // Delete the temporary element
            document.body.removeChild(tempInput);

            const copyUrl = document.getElementById('copy-url');
            // Notify user of copy success or failure
            if (document.execCommand("copy")) {
                copyUrl.style.display = 'block';
                setTimeout(() => {
                    copyUrl.style.display = 'none';
                }, 3000);
            } else {
                copyUrl.style.display = 'none';
            }
        }
    </script>
@endsection
