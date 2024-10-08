@extends('layouts.appHome')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">

                <div class="card ">
                    <div class="table-responsive m-3">
                        <table class="table">
                            <thead>
                                <tr class="table-secondary">
                                    <th>วันที่</th>
                                    <th>เลขที่เอกสาร</th>
                                    <th>บริษัท</th>
                                    <th>คำอธิบาย</th>
                                    <th>เดบิต</th>
                                    <th>เครดิต</th>


                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">

                                @foreach ($query as $que)
                                    <tr>
                                        <th scope="row">{{ date('d-m-Y', strtotime($que->gl_date)) }}</th>
                                        <td>{{ $que->gl_document }}</td>
                                        <td>{{ $que->gl_company }}</td>
                                        <td>{{ $que->gl_description }}</td>
                                        <td>{{ $que->gls_account_name }}</td>
                                        <td>{{ $que->gls_debit }}</td>
                                        <td>{{ $que->gls_credit }}</td>



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
