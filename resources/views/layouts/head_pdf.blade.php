<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<meta http-equiv="Content-Language" content="th" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


<meta http-equiv="Content-Language" content="th" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="{{ URL::asset('/assets/img/icons/icon-2.png') }}" />

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
        background-color: transparent;
        width: 100%;
        margin-bottom: 1rem;
        color: #697a8d;
        border: 1px solid #d9dee3;
        border-collapse: collapse;

        font-size: 12px !important;
    }

    .table th,
    .table td {
        padding: 0.625rem 0.5rem;
        border-bottom: 1px solid #d9dee3;
    }

    .table>thead {
        vertical-align: bottom;
        background-color: #f9fafb;
        /* ใช้สีสำหรับหัวตาราง */
        color: #697a8d;
    }

    .table-striped>tbody>tr:nth-of-type(odd) {
        background-color: #f9fafb;
        /* สีพื้นหลังแถวคู่ */
    }

    .table-hover>tbody>tr:hover {
        background-color: rgba(67, 89, 113, 0.06);
        /* สีเมื่อ hover */
        color: #697a8d;
    }

    .table-active {
        background-color: rgba(67, 89, 113, 0.1);
        /* สีแถวที่ active */
        color: #697a8d;
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid #d9dee3;
        /* เส้นขอบตาราง */
    }

    .table-borderless th,
    .table-borderless td {
        border: none;
        /* ตารางแบบไม่มีเส้นขอบ */
    }

    .caption-top {
        caption-side: top;
    }

    .table-sm th,
    .table-sm td {
        padding: 0.3125rem 0.625rem;
        /* ลดขนาด padding สำหรับ table-sm */
    }




    thead,
    tbody,
    tfoot,
    tr,
    td,
    th {
        border-color: inherit !important;
        border-style: solid !important;
        border-width: thin !important;

    }

    .table-secondary {
        background-color: #e7e9ed;
        /* สีพื้นหลัง */
        color: #435971;
        /* สีข้อความ */
        border-color: #d7dbe1;
        /* สีขอบ */
    }

    .table-secondary tr:hover {
        background-color: #dde0e6;
        /* สีเมื่อ hover */
        color: #435971;
        /* สีข้อความเมื่อ hover */
    }

    .table-secondary tr.active {
        background-color: #d7dbe1;
        /* สีแถวที่ active */
        color: #435971;
    }

    .table-secondary tr:nth-child(even) {
        background-color: #e2e5e9;
        /* สีแถวที่ striped */
        color: #435971;
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
        width: 80px !important;
        word-wrap: break-word !important;
        white-space: normal !important;

    }


    .child-3 {
        width: 100px !important;
        word-wrap: break-word !important;
        white-space: normal !important;
    }

    .child-4 {
        width: 180px !important;
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

    .text-center-vertical {
        text-align: center;
        vertical-align: middle;
    }

    .error-message {
        background-color: #f8d7da !important;
    }

    .table th {
        text-transform: uppercase;
        font-size: 0.90rem;
        letter-spacing: 0px;
    }
</style>
