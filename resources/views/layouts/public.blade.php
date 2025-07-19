<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'منصة التطوع')</title>
    <link rel="icon" type="image/png" href="{{ URL::to('assets/images/favicon.png') }}">
    <link rel="stylesheet" href="{{ URL::to('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ URL::to('assets/css/toastr.min.css') }}">
    <script src="{{ URL::to('assets/js/toastr_jquery.min.js') }}"></script>
    <script src="{{ URL::to('assets/js/toastr.min.js') }}"></script>
    <link href="{{ URL::to('assets/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap') }}" rel="stylesheet">
    <script>
        localStorage.getItem("_x_darkMode_on") === "true" && document.documentElement.classList.add("dark");
    </script>
</head>
<body class="bg-slate-50 dark:bg-navy-900 min-h-screen flex flex-col justify-center items-center">
    <div class="w-full max-w-2xl mx-auto p-4">
        <div class="flex flex-col items-center mb-6">
            <img src="{{ URL::to('assets/images/app-logo.png') }}" alt="logo" class="h-16 mb-2">
            <h1 class="text-2xl font-bold text-slate-700 dark:text-navy-100">@yield('page_title', 'منصة التطوع')</h1>
        </div>
        {!! Toastr::message() !!}
        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mb-4">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </div>
    <div id="x-teleport-target"></div>
    <script src="{{ URL::to('assets/js/app.js') }}"></script>
</body>
</html> 