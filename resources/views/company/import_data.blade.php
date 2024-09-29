@extends('layouts.appHome')

@section('content')
    <script>
        const action = 'getUsers';
        const sheet = 'General Ledger Sub';
        const id_sheet = '1NwqH-EqZsGTGCmB79m491aNd8l_Ib7kQHAUDFbhNrk8';

        // URL ที่จะใช้ดึงข้อมูล
        const url =
            `https://script.google.com/macros/s/AKfycbyrlSPW6xRtrPCe1rHpgSUMLCL0XAXETfoPVLK5OuvYGo8SeyDV2YukV6PCcQ35tPm5/exec?action=${action}&sheet=${sheet}&id_sheet=${id_sheet}`;

        // ดึงข้อมูลโดยใช้ fetch
        /*  fetch(url)
             .then(response => response.json()) // แปลง response เป็น JSON
             .then(data => console.log(data)) // แสดงข้อมูลที่ได้
             .catch(error => console.error('Error:', error)); */


        // ดึงข้อมูลโดยใช้ axios
        axios.get(url)
            .then(response => console.log(response.data)) // แสดงข้อมูลที่ได้
            .catch(error => console.error('Error:', error)); // จัดการกรณี error
    </script>
@endsection
