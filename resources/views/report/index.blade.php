ad@extends('layouts.appHome')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">

                <div class="card">
                    <h5 class="card-header">รายชื่อ บริษัท</h5>
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>รหัสบริษัท</th>
                                    <th>ชื่อบริษัท</th>
                                    <th>สาขาบริษัท</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @php
                                    $i = 1;
                                @endphp
                                @foreach ($query as $que)
                                    <tr>
                                        <th scope="row">{{ $i++ }}</th>
                                        <td>{{ $que->code_company }}</td>
                                        <td> <strong>{{ $que->company }}</strong></td>
                                        <td>{{ $que->branch }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                    data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item"
                                                        href="{{ url('update/import-data', $que->id) }}">
                                                        <i class='bx bxs-data me-2'></i>
                                                        นำเข้าข้อมูล</a>

                                                </div>
                                            </div>
                                        </td>
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
