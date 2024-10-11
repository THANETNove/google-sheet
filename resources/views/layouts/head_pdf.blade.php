<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<meta http-equiv="Content-Language" content="th" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


<meta http-equiv="Content-Language" content="th" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- Bootstrap 3.x only : DOMPDF support float, not flexbox -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css"
    integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
<!-- thai font -->

<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">

<style>
    body {
        font-size: 9pt !important;
        font-family: 'Sarabun', sans-serif;

        font-weight: 400;
        font-style: normal !important;
    }


    .container-company {
        display: flex;
        justify-content: space-between;
        /* จัดให้อยู่ทั้งสองด้าน */
        align-items: center;
        /* จัดให้อยู่ในแนวเดียวกัน */
        padding: 10px;
        /* ระยะห่างรอบๆ */
    }

    .company {

        flex: 1;
        /* ให้ company มีพื้นที่มากที่สุด */
        text-align: center;
        /* จัดกึ่งกลาง */
    }

    .date {

        position: absolute;
        right: 16px;
        top: 16px;
    }



    .table {
        --bs-table-bg: transparent;
        --bs-table-accent-bg: transparent;
        --bs-table-striped-color: #697a8d;
        --bs-table-striped-bg: #f9fafb;
        --bs-table-active-color: #697a8d;
        --bs-table-active-bg: rgba(67, 89, 113, 0.1);
        --bs-table-hover-color: #697a8d;
        --bs-table-hover-bg: rgba(67, 89, 113, 0.06);
        width: 100%;
        margin-bottom: 1rem;
        color: #697a8d;
        vertical-align: middle;
        border-color: #d9dee3;

    }

    .table> :not(caption)>*>* {
        padding: 0.625rem 16px;
        background-color: var(--bs-table-bg);
        border-bottom-width: 1px;
        box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);
    }

    .table>tbody {
        vertical-align: inherit;
    }

    .table>thead {
        vertical-align: bottom;
    }

    .table> :not(:first-child) {
        border-top: 2px solid #d9dee3;
    }

    .caption-top {
        caption-side: top;
    }

    .table-sm> :not(caption)>*>* {
        padding: 0.3125rem 0.625rem;
    }

    .table-bordered> :not(caption)>* {
        border-width: 1px 0;
    }

    .table-bordered> :not(caption)>*>* {
        border-width: 0 1px;
    }

    .table-borderless> :not(caption)>*>* {
        border-bottom-width: 0;
    }

    .table-borderless> :not(:first-child) {
        border-top-width: 0;
    }

    .table-striped>tbody>tr:nth-of-type(odd)>* {
        --bs-table-accent-bg: var(--bs-table-striped-bg);
        color: var(--bs-table-striped-color);
    }

    .table-active {
        --bs-table-accent-bg: var(--bs-table-active-bg);
        color: var(--bs-table-active-color);
    }

    .table-hover>tbody>tr:hover>* {
        --bs-table-accent-bg: var(--bs-table-hover-bg);
        color: var(--bs-table-hover-color);
    }

    .table-primary {
        --bs-table-bg: #e1e2ff;
        --bs-table-striped-bg: #dcdefb;
        --bs-table-striped-color: #435971;
        --bs-table-active-bg: #d1d4f1;
        --bs-table-active-color: #435971;
        --bs-table-hover-bg: #d8daf6;
        --bs-table-hover-color: #435971;
        color: #435971;
        border-color: #d1d4f1;
    }

    .table-secondary {
        --bs-table-bg: #e7e9ed;
        --bs-table-striped-bg: #e2e5e9;
        --bs-table-striped-color: #435971;
        --bs-table-active-bg: #d7dbe1;
        --bs-table-active-color: #435971;
        --bs-table-hover-bg: #dde0e6;
        --bs-table-hover-color: #435971;
        color: #435971;
        border-color: #d7dbe1;
    }

    .table-success {
        --bs-table-bg: #e3f8d7;
        --bs-table-striped-bg: #def3d4;
        --bs-table-striped-color: #435971;
        --bs-table-active-bg: #d3e8cd;
        --bs-table-active-color: #435971;
        --bs-table-hover-bg: #d9eed1;
        --bs-table-hover-color: #435971;
        color: #435971;
        border-color: #d3e8cd;
    }

    .table-info {
        --bs-table-bg: #cdf3fb;
        --bs-table-striped-bg: #c9eef7;
        --bs-table-striped-color: #435971;
        --bs-table-active-bg: #bfe4ed;
        --bs-table-active-color: #435971;
        --bs-table-hover-bg: #c5eaf3;
        --bs-table-hover-color: #435971;
        color: #435971;
        border-color: #bfe4ed;
    }

    .table-warning {
        --bs-table-bg: #ffeecc;
        --bs-table-striped-bg: #f9eac9;
        --bs-table-striped-color: #435971;
        --bs-table-active-bg: #ecdfc3;
        --bs-table-active-color: #435971;
        --bs-table-hover-bg: #f4e5c7;
        --bs-table-hover-color: #435971;
        color: #435971;
        border-color: #ecdfc3;
    }

    .table-danger {
        --bs-table-bg: #ffd8d2;
        --bs-table-striped-bg: #f9d4cf;
        --bs-table-striped-color: #435971;
        --bs-table-active-bg: #eccbc8;
        --bs-table-active-color: #435971;
        --bs-table-hover-bg: #f4d0cc;
        --bs-table-hover-color: #435971;
        color: #435971;
        border-color: #eccbc8;
    }

    .table-light {
        --bs-table-bg: #fcfdfd;
        --bs-table-striped-bg: #f6f8f9;
        --bs-table-striped-color: #435971;
        --bs-table-active-bg: #eaedef;
        --bs-table-active-color: #435971;
        --bs-table-hover-bg: #f1f3f5;
        --bs-table-hover-color: #435971;
        color: #435971;
        border-color: #eaedef;
    }

    .table-dark {
        --bs-table-bg: #233446;
        --bs-table-striped-bg: #2a3a4c;
        --bs-table-striped-color: #fff;
        --bs-table-active-bg: #394859;
        --bs-table-active-color: #fff;
        --bs-table-hover-bg: #304051;
        --bs-table-hover-color: #fff;
        color: #fff;
        border-color: #394859;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;

    }

    .table-min-height {
        min-height: 100vh;
    }

    @media (max-width: 575.98px) {
        .table-responsive-sm {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }

    @media (max-width: 767.98px) {
        .table-responsive-md {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }

    @media (max-width: 991.98px) {
        .table-responsive-lg {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }

    @media (max-width: 1199.98px) {
        .table-responsive-xl {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }

    @media (max-width: 1399.98px) {
        .table-responsive-xxl {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }


    .table td,
    .table th {
        vertical-align: top;
    }

    /* กำหนดขนาดคอลัมน์โดยตรง */
    .col-1 {
        width: 8.33%;
        /* 1/12 ของความกว้างเต็ม */
    }

    .col-2 {
        width: 16.66%;
        /* 2/12 ของความกว้างเต็ม */
    }

    .col-3 {
        width: 25%;
        /* 3/12 ของความกว้างเต็ม */
    }

    .col-4 {
        width: 33.33%;
        /* 4/12 ของความกว้างเต็ม */
    }

    .col-5 {
        width: 41.66%;
        /* 5/12 ของความกว้างเต็ม */
    }

    .col-6 {
        width: 50%;
        /* 6/12 ของความกว้างเต็ม */
    }



    .child-1 {
        width: auto !important;
        word-wrap: break-word !important;
        white-space: normal !important;
        /* กำหนดความกว้างของคอลัมน์วันที่ */
    }

    .child-2 {
        width: 125px !important;
        word-wrap: break-word !important;
        white-space: normal !important;

    }


    .child-3 {
        width: 100px !important;
        word-wrap: break-word !important;
        white-space: normal !important;
    }

    .text-end {
        text-align: right !important;
    }

    .text-start {
        text-align: left !important;
    }

    .text-initial {
        text-align: right !important;
    }

    .mt-16 {
        margin-top: 64px !important;
    }
</style>
