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

    @include('layouts.footer')
</body>

</html>
