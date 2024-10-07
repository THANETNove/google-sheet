@extends('layouts.appHome')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">

                <div class="card ">
                    <h5 class="card-header">รายชื่อ บริษัท</h5>
                    <div class="table-responsive m-3">
                        <table class="table table table-bordered">
                            <thead>
                                <tr class="table-secondary" id="layout-menu4">
                                    <th>วันที่</th>
                                    <th>เลขที่เอกสาร</th>
                                    <th>คำอธิบาย</th>
                                    <th>เดบิต</th>
                                    <th>เครดิต</th>
                                    <th>สะสมงวดนี้</th>
                                    <th>สะสมต้นงวด</th>

                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @php
                                    $i = 1;
                                @endphp
                                @foreach ($query as $que)
                                    <tr>
                                        <th scope="row">{{ $i++ }}</th>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>


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
