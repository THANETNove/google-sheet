@extends('layouts.appHome')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">

                <div class="card">
                    <a href="{{ url('company-create') }}" class="col-2   m-3 ms-auto">
                        <button type="button" class="btn btn-primary ">
                            <i class='bx bxs-add-to-queue'></i>&nbsp; เพิ่มบริษัท
                        </button>
                    </a>

                    <h5 class="card-header">รายชื่อ บริษัท</h5>
                    <div class="table-responsive text-nowrap">
                        <table class="table" style="min-height: 100hv">
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
                                                    <a class="dropdown-item" href="{{ url('company-edit', $que->id) }}"><i
                                                            class="bx bx-edit-alt me-2"></i> Edit</a>
                                                    <a class="dropdown-item" href="javascript:void(0);"><i
                                                            class="bx bx-trash me-2"></i> Delete</a>
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
