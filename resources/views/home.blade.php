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
                <div class="col-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <i class='bx bxs-buildings'
                                    style="font-size: 32px; color: {{ $colors[$index % count($colors)] }};"></i>
                                <div class="dropdown">
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
                            <a href="{{ url('select-card', $que->id) }}">
                                <div class="card-text">
                                    <span class="fw-semibold d-block mb-1">{{ $que->company }}</span>
                                    <small class="d-block">General Ledger <span
                                            style="float: right;">{{ $que->general_ledger_count }}</span></small>
                                    <small class="d-block">General Ledger Sub <span
                                            style="float: right;">{{ $que->general_ledger_sub_count }}</span></small>
                                    <small class="d-block">Account_Code <span
                                            style="float: right;">{{ $que->account_code_count }}</span></small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const cards = document.querySelectorAll('.col-3');

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
    </script>
@endsection
