<!DOCTYPE html>
<html lang="en">
<head>
    <title>System_Management - Guest</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="Codedthemes" />
    <link rel="icon" href="{{ URL::to('files/assets/images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="{{ URL::to('files/assets/icon/icofont/css/icofont.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ URL::to('files/bower_components/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::to('files/assets/icon/feather/css/feather.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::to('files/assets/icon/themify-icons/themify-icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::to('files/assets/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::to('files/assets/css/jquery.mCustomScrollbar.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::to('files/assets/icon/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::to('files/assets/css/component.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::to('files/assets/pages/j-pro/css/demo.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::to('files/assets/pages/j-pro/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::to('files/assets/pages/j-pro/css/j-pro-modern.css') }}">
</head>
<body>
    @yield('content')
    <!-- JS Assets -->
    <script type="text/javascript" src="{{ URL::to('files/bower_components/jquery/js/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::to('files/bower_components/jquery-ui/js/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::to('files/bower_components/popper.js/js/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::to('files/bower_components/bootstrap/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::to('files/assets/js/pcoded.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::to('files/assets/js/vartical-layout.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::to('files/assets/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::to('files/assets/js/script.js') }}"></script>
    @yield('script')
</body>
</html> 