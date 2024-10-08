<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->


    @include('layouts.head')
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            @include('layouts.manuAside')
            <!-- / Menu -->
            <div class="layout-page">

                <!-- Navbar -->
                @include('layouts.navbar')


                <!-- / Navbar -->
                @yield('content')
            </div>
        </div>
        <!-- / Layout page -->
    </div>
    <script>
        // แสดงปุ่มเมื่อเลื่อนลง
        window.onscroll = function() {
            const button = document.getElementById('scrollToTop');
            if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
                button.style.display = "block";
            } else {
                button.style.display = "none";
            }
        };

        // เมื่อคลิกปุ่มเลื่อนกลับไปยังด้านบน
        document.getElementById('scrollToTop').onclick = function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        };
    </script>
    @include('layouts.footer')
</body>

</html>
