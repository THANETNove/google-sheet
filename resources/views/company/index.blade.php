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
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Project</th>
                                    <th>Client</th>
                                    <th>Users</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <tr>
                                    <th scope="row">1</th>
                                    <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>Angular
                                            Project</strong></td>
                                    <td>Albert Cook</td>
                                    <td>
                                        <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                                class="avatar avatar-xs pull-up" title=""
                                                data-bs-original-title="Lilian Fuller">
                                                <img src="../assets/img/avatars/5.png" alt="Avatar"
                                                    class="rounded-circle">
                                            </li>
                                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                                class="avatar avatar-xs pull-up" title=""
                                                data-bs-original-title="Sophia Wilkerson">
                                                <img src="../assets/img/avatars/6.png" alt="Avatar"
                                                    class="rounded-circle">
                                            </li>
                                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                                class="avatar avatar-xs pull-up" title=""
                                                data-bs-original-title="Christina Parker">
                                                <img src="../assets/img/avatars/7.png" alt="Avatar"
                                                    class="rounded-circle">
                                            </li>
                                        </ul>
                                    </td>
                                    <td><span class="badge bg-label-primary me-1">Active</span></td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow show"
                                                data-bs-toggle="dropdown" aria-expanded="true">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu show" data-popper-placement="bottom-end"
                                                style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate3d(-48px, 27px, 0px);">
                                                <a class="dropdown-item" href="javascript:void(0);"><i
                                                        class="bx bx-edit-alt me-1"></i> Edit</a>
                                                <a class="dropdown-item" href="javascript:void(0);"><i
                                                        class="bx bx-trash me-1"></i> Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <td><i class="fab fa-react fa-lg text-info me-3"></i> <strong>React Project</strong>
                                    </td>
                                    <td>Barry Hunter</td>
                                    <td>
                                        <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                                class="avatar avatar-xs pull-up" title=""
                                                data-bs-original-title="Lilian Fuller">
                                                <img src="../assets/img/avatars/5.png" alt="Avatar"
                                                    class="rounded-circle">
                                            </li>
                                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                                class="avatar avatar-xs pull-up" title=""
                                                data-bs-original-title="Sophia Wilkerson">
                                                <img src="../assets/img/avatars/6.png" alt="Avatar"
                                                    class="rounded-circle">
                                            </li>
                                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                                class="avatar avatar-xs pull-up" title=""
                                                data-bs-original-title="Christina Parker">
                                                <img src="../assets/img/avatars/7.png" alt="Avatar"
                                                    class="rounded-circle">
                                            </li>
                                        </ul>
                                    </td>
                                    <td><span class="badge bg-label-success me-1">Completed</span></td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="javascript:void(0);"><i
                                                        class="bx bx-edit-alt me-2"></i> Edit</a>
                                                <a class="dropdown-item" href="javascript:void(0);"><i
                                                        class="bx bx-trash me-2"></i> Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">3</th>
                                    <td><i class="fab fa-vuejs fa-lg text-success me-3"></i> <strong>VueJs Project</strong>
                                    </td>
                                    <td>Trevor Baker</td>
                                    <td>
                                        <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                data-bs-placement="top" class="avatar avatar-xs pull-up" title=""
                                                data-bs-original-title="Lilian Fuller">
                                                <img src="../assets/img/avatars/5.png" alt="Avatar"
                                                    class="rounded-circle">
                                            </li>
                                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                data-bs-placement="top" class="avatar avatar-xs pull-up" title=""
                                                data-bs-original-title="Sophia Wilkerson">
                                                <img src="../assets/img/avatars/6.png" alt="Avatar"
                                                    class="rounded-circle">
                                            </li>
                                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                data-bs-placement="top" class="avatar avatar-xs pull-up" title=""
                                                data-bs-original-title="Christina Parker">
                                                <img src="../assets/img/avatars/7.png" alt="Avatar"
                                                    class="rounded-circle">
                                            </li>
                                        </ul>
                                    </td>
                                    <td><span class="badge bg-label-info me-1">Scheduled</span></td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="javascript:void(0);"><i
                                                        class="bx bx-edit-alt me-2"></i> Edit</a>
                                                <a class="dropdown-item" href="javascript:void(0);"><i
                                                        class="bx bx-trash me-2"></i> Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">4</th>
                                    <td>
                                        <i class="fab fa-bootstrap fa-lg text-primary me-3"></i> <strong>Bootstrap
                                            Project</strong>
                                    </td>
                                    <td>Jerry Milton</td>
                                    <td>
                                        <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                data-bs-placement="top" class="avatar avatar-xs pull-up" title=""
                                                data-bs-original-title="Lilian Fuller">
                                                <img src="../assets/img/avatars/5.png" alt="Avatar"
                                                    class="rounded-circle">
                                            </li>
                                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                data-bs-placement="top" class="avatar avatar-xs pull-up" title=""
                                                data-bs-original-title="Sophia Wilkerson">
                                                <img src="../assets/img/avatars/6.png" alt="Avatar"
                                                    class="rounded-circle">
                                            </li>
                                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                data-bs-placement="top" class="avatar avatar-xs pull-up" title=""
                                                data-bs-original-title="Christina Parker">
                                                <img src="../assets/img/avatars/7.png" alt="Avatar"
                                                    class="rounded-circle">
                                            </li>
                                        </ul>
                                    </td>
                                    <td><span class="badge bg-label-warning me-1">Pending</span></td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="javascript:void(0);"><i
                                                        class="bx bx-edit-alt me-2"></i> Edit</a>
                                                <a class="dropdown-item" href="javascript:void(0);"><i
                                                        class="bx bx-trash me-2"></i> Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div>

    </div>
@endsection
