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
<link
    href="https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap"
    rel="stylesheet">

<style>
    body {
        font-size: 9pt !important;
        margin: 0;
        font-family: 'Sarabun', sans-serif;
        line-height: 1;
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
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10pt;
    }

    .table th,
    .table td {
        border: none;
        padding: 8px;
    }

    .table th {
        border-bottom: 1px solid #ddd;
        background-color: #f2f2f2;
        text-align: left;
    }

    tr {
        page-break-inside: avoid !important;
    }

    /* สไตล์แถวของตาราง */
    .table tbody tr:hover {
        background-color: #e9ecef;
        /* เปลี่ยนสีเมื่อชี้เมาส์ไปที่แถว */
    }

    /* ให้ข้อความอยู่กลางในเซลล์ */
    .table td {
        text-align: left;
        /* จัดกลางข้อความในเซลล์ */
    }

    /* ระยะห่างระหว่างตาราง */
    .table-responsive {
        overflow: auto;
        /* ยอมให้เลื่อน */
    }

    .mt-32 {
        margin-bottom: 32px;
        /* ระยะห่างด้านล่าง */
    }

    /* สไตล์แถวรวม */
    .table tfoot {
        font-weight: bold;
        /* ทำให้ข้อความเป็นตัวหนา */
        background-color: #f9f9f9;
        /* สีพื้นหลังของแถวรวม */
    }

    /* เส้นขอบแนวนอนสำหรับแถวข้อมูล */
    .table tr {
        border-bottom: 1px solid #ddd;
        /* เส้นขอบแนวนอนสำหรับแต่ละแถว */
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

    table {
        width: 100%;
        table-layout: fixed;
    }

    th,
    td {
        padding: 10px;
        text-align: left;
        vertical-align: top;
    }

    th:nth-child(1),
    td:nth-child(1) {
        width: 150px !important;
        word-wrap: break-word !important;
        white-space: normal !important;
        /* กำหนดความกว้างของคอลัมน์วันที่ */
    }

    th:nth-child(2),
    td:nth-child(2) {
        width: 15% !important;
        word-wrap: break-word !important;
        white-space: normal !important;
        /* กำหนดความกว้างของคอลัมน์เลขที่เอกสาร */
    }

    th:nth-child(3),
    td:nth-child(3) {
        width: 20% !important;
        /* กำหนดความกว้างของคอลัมน์บริษัท */
        word-wrap: break-word !important;
        white-space: normal !important;
    }

    th:nth-child(4),
    td:nth-child(4) {
        width: 15% !important;
        /* กำหนดความกว้างของคอลัมน์หมายเลขผู้เสียภาษี */
        word-wrap: break-word !important;
        white-space: normal !important;
    }

    th:nth-child(5),
    td:nth-child(5) {
        width: 15% !important;
        word-break: keep-all !important;
        word-wrap: break-word !important;
        white-space: normal !important;
    }

    th:nth-child(6),
    td:nth-child(6),
    th:nth-child(7),
    td:nth-child(7),
    th:nth-child(8),
    td:nth-child(8) {
        width: 10%;
        /* กำหนดความกว้างของคอลัมน์จำนวน ภาษี และรวม */
        text-align: right;
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

    .table td,
    .table th {
        padding: 10px;
        vertical-align: top;
    }

    .text-spacing {
        margin-top: 10px;
        /* เพิ่มความห่างด้านบน */
        margin-bottom: 10px;
        /* เพิ่มความห่างด้านล่าง */
        line-height: 1.5;
        /* เพิ่มระยะห่างระหว่างบรรทัด */
    }
</style>
