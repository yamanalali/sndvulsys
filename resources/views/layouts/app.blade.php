<!doctype html>
<html lang="en">
<head>
    <!-- Meta tags -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>StarCodeKh - Sign In</title>
    <link rel="icon" type="image/png" href="{{ URL::to('assets/images/favicon.png') }}">
    <!-- CSS Assets -->
    <link rel="stylesheet" href="{{ URL::to('assets/css/app.css') }}">
    <!-- message toastr -->
	<link rel="stylesheet" href="{{ URL::to('assets/css/toastr.min.css') }}">
	<script src="{{ URL::to('assets/js/toastr_jquery.min.js') }}"></script>
	<script src="{{ URL::to('assets/js/toastr.min.js') }}"></script>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="assets/css2?family=Inter:wght@400;500;600;700&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <script>
        /**
        * THIS SCRIPT REQUIRED FOR PREVENT FLICKERING IN SOME BROWSERS
        */
        localStorage.getItem("_x_darkMode_on") === "true" && document.documentElement.classList.add("dark");
    </script>
</head>
<style>
    .ring-danger {
        --tw-ring-color: #ff5724;
    }
    .invalid-feedback {
        color: #ff5724;
    }
</style>
<body x-data="" class="is-header-blur" x-bind="$store.global.documentBody">
    <!-- App preloader-->
    <div class="app-preloader fixed z-50 grid h-full w-full place-content-center bg-slate-50 dark:bg-navy-900">
        <div class="app-preloader-inner relative inline-block size-48"></div>
    </div>
    <!-- message -->
    {!! Toastr::message() !!}
    <!-- Page Wrapper -->
    @yield('content')
    <div id="x-teleport-target"></div>
    <script>window.addEventListener("DOMContentLoaded", () => Alpine.start());</script>
    <!-- Javascript Assets -->
    <script src="{{ URL::to('assets/js/app.js') }}"></script>
    @yield('script')
</body>
</html>