<!DOCTYPE html>
<html lang="en">
<head>
    <title>System_Management</title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="Codedthemes" />
    <!-- Favicon icon -->
    <link rel="icon" href="..\files\assets\images\favicon.ico" type="image/x-icon">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>


    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/icon/icofont/css/icofont.css') }}">

    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet">
    <!-- Required Fremwork -->
    <link rel="stylesheet" type="text/css" href="{{ asset('files/bower_components/bootstrap/css/bootstrap.min.css') }}">
    <!-- feather Awesome -->
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/icon/feather/css/feather.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/icon/themify-icons/themify-icons.css') }}">

    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/css/jquery.mCustomScrollbar.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/icon/font-awesome/css/font-awesome.min.css') }}">

    <!-- animation nifty modal window effects css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/css/component.css') }}">

    <!-- Select 2 css -->
    <link rel="stylesheet" href="{{ asset('files/bower_components/select2/css/select2.min.css') }}">

    <!-- notify js Fremwork -->
    <link rel="stylesheet" type="text/css" href="{{ asset('files/bower_components/pnotify/css/pnotify.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('files/bower_components/pnotify/css/pnotify.brighttheme.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('files/bower_components/pnotify/css/pnotify.buttons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('files/bower_components/pnotify/css/pnotify.history.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('files/bower_components/pnotify/css/pnotify.mobile.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/pages/pnotify/notify.css') }}">

    <!-- jpro forms css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/pages/j-pro/css/demo.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/pages/j-pro/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/pages/j-pro/css/j-pro-modern.css') }}">
    <!-- Bootstrap CSS fallback -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jquery file upload Frame work -->
    <link  rel="stylesheet" type="text/css"  href="{{ asset('files/assets/pages/jquery.filer/css/jquery.filer.css') }}">
    <link  rel="stylesheet" type="text/css"  href="{{ asset('files/assets/pages/jquery.filer/css/themes/jquery.filer-dragdropbox-theme.css') }}">

    <!-- Data Table Css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('files/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/pages/data-table/css/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('files/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}">

    <!-- Data Table Css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('files/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/pages/data-table/css/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('files/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}">

    <!-- CSRF Token for AJAX requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Volunteer Dashboard CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('files/assets/css/volunteer-dashboard.css') }}">

    <!-- Volunteer Dashboard JavaScript -->
    <script src="{{ asset('files/assets/js/volunteer-dashboard.js') }}" defer></script>

</head>
<style>
    /* The clock */
    #clock {
        background-color: transparent;
        color: black;
        display: inline-block;
        width: auto;
        padding: 0.25em 1em;
        border-radius: 1px;
    }

    /* Paragraph fix */
    #clock p {
        margin: 5px;
    }

    /* Show time units and seprarator in a line */
    #clock .time-unit, #clock .separator {
        display: inline-block;
        text-align: center;
        margin: 0 0.25em;
    }

    /* Show values using large text */
    #clock .time-unit .large {
        display: block;
        font-size: 1.2em;
    }

    /* Show values using smaller text */
    #clock .time-unit .small {
        display: block;
        font-size: 0.5em;
    }

    /* Align the separator with values */
    #clock .separator {
        font-size: 1.2em;
        vertical-align: top;
        margin-top: -0.1em;
    }
    .day {
        color: #23256F;
        padding-left: 10px;
        font-size: 14px;
    }
    .avatar-upload {
        position: relative;
        max-width: 205px;
        margin: 50px auto;
    }
    .avatar-upload .avatar-edit {
        position: absolute;
        right: 12px;
        z-index: 1;
        top: 10px;
    }
    .avatar-upload .avatar-edit input {
        display: none;
    }
    .avatar-upload .avatar-edit input + label {
        display: inline-block;
        margin-top: 3px;
        width: 34px;
        height: 34px;
        margin-bottom: 0;
        border-radius: 100%;
        background: #FFFFFF;
        border: 1px solid transparent;
        box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.12);
        cursor: pointer;
        font-weight: normal;
        transition: all 0.2s ease-in-out;
    }
    .avatar-upload .avatar-edit input + label:hover {
        background: #f1f1f1;
        border-color: #d6d6d6;
    }
    .avatar-upload .avatar-edit input + label:after {
        content: "\f040";
        font-family: 'FontAwesome';
        color: #757575;
        position: absolute;
        top: 10px;
        left: 0;
        right: 0;
        text-align: center;
        margin: auto;
    }
    .avatar-upload .avatar-preview {
        width: 120px;
        height: 120px;
        position: relative;
        border-radius: 100%;
        border: 1px solid #01a9ac;
        padding: 4px;
        box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.1);
    }
    .avatar-upload .avatar-preview > div {
        width: 100%;
        height: 100%;
        border-radius: 100%;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
    }

    /* logo image menu bar */
    .header-navbar .navbar-wrapper .navbar-container .nav-right .user-profile img {
        margin-right: 10px;
        width: 40px;
    }
    .img-radius {
        border-radius: 50%;
        border: 1px solid #23256F;
        padding: 2px;
    }
</style>
</head>
<body>
<!-- Pre-loader start -->
<div class="theme-loader">
    <div class="ball-scale">
        <div class='contain'>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
            <div class="ring">
                <div class="frame"></div>
            </div>
        </div>
    </div>
</div>
<!-- Pre-loader end -->
<div id="pcoded" class="pcoded">
    <div class="pcoded-overlay-box"></div>
    <div class="pcoded-container navbar-wrapper">
   @include('layouts.navbar')
        <!-- Modal large--> 
        <div class="modal fade" id="large-Modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <form action="/profile" method="post" enctype="multipart/form-data">
                @csrf
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="col-xl-12 col-md-12">
                                <div class="user-card-full">
                                    <div class="row m-l-0 m-r-0">
                                        <div class="col-sm-3 user-profile">
                                            <div class="user-card">
                                                <div class="card-block text-center">
                                                    <!-- <div class="usre-image"> -->
                                                    <div class="avatar-upload">
                                                        <div class="avatar-edit">
                                                            <input type='file' name="avatar" id="imageUpload" accept=".png, .jpg, .jpeg" />
                                                            <label for="imageUpload"></label>

                                                        </div>
                                                        <div class="avatar-preview">
                                                            <div id="imagePreview" style="background-image: url(../files/assets/images/{{ Auth::user()?->avatar ?? 'avatar-1.jpg' }});"></div>
                                                        </div>
                                                        <h6 class="m-t-25 m-b-10"></h6>
                                                        <p class="text-muted"></p>
                                                    </div>
                                                    <!-- </div> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <div class="card-block">
                                                <h6 class="m-b-20 p-b-5 b-b-default f-w-600">Information</h6>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <p class="m-b-10 f-w-600">Email</p>
                                                        <h6 class="text-muted f-w-400">{{ Auth::user()?->email ?? 'N/A' }}<a href="#" class="cf_email" data-cfemail="1379767d6a53747e727a7f3d707c7e"></a></h6>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <p class="m-b-10 f-w-600">Department</p>
                                                        <h6 class="text-muted f-w-400">{{ Auth::user()?->department ?? 'N/A' }}</h6>
                                                    </div>
                                                </div>
                                                <h6 class="m-b-20 m-t-40 p-b-5 b-b-default f-w-600">Section</h6>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <p class="m-b-10 f-w-600">Division</p>
                                                        <h6 class="text-muted f-w-400">{{ Auth::user()?->division ?? 'N/A' }}</h6>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <p class="m-b-10 f-w-600">Role</p>
                                                        <h6 class="text-muted f-w-400">{{ Auth::user()?->role_name ?? 'N/A' }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success"><i class="icofont icofont-check-circled"></i>Save</button>
                    </div>
                </form>
            </div>
        </div><!--end Modal large-->
        @include('layouts.sidebar')
    </div>
</div>

                        <div class="pcoded-content">
                    <div class="pcoded-inner-content">
                        <!-- Main-body start -->
                        <div class="main-body">
                            <div class="page-wrapper">
                            @yield('content')
                            </div>
                        </div>
                        <div id="styleSelector">

                        </div>
                    </div>
                </div>
            </div>
    <!-- Warning Section Ends -->
    <!-- Required Jquery -->
    <script data-cfasync="false" src="..\..\..\cdn-cgi\scripts\5c5dd728\cloudflare-static\email-decode.min.js"></script><script type="text/javascript" src="..\files\bower_components\jquery\js\jquery.min.js"></script>
    <script type="text/javascript" src="{{URL::to('files\bower_components\jquery-ui\js\jquery-ui.min.js')}}"></script>
    <script type="text/javascript" src="{{URL::to('files\bower_components\popper.js\js\popper.min.js')}}"></script>
    <script type="text/javascript" src="{{URL::to('files\bower_components\bootstrap\js\bootstrap.min.js')}}"></script>
    <!-- jquery slimscroll js -->
    <script type="text/javascript" src="{{URL::to('files\bower_components\jquery-slimscroll\js\jquery.slimscroll.js')}}"></script>
    <!-- modernizr js -->
    <script type="text/javascript" src="{{URL::to('files\bower_components\modernizr\js\modernizr.js')}}"></script>
    <!-- Chart js -->
    <script type="text/javascript" src="{{URL::to('files\bower_components\chart.js\js\Chart.js')}}"></script>
    <!-- amchart js -->
    <script src="{{ asset('files/assets/pages/widget/amchart/amcharts.js') }}"></script>
    <script src="{{ asset('files/assets/pages/widget/amchart/serial.js') }}"></script>
    <script src="{{ asset('files/assets/pages/widget/amchart/light.js') }}"></script>
    <script src="{{ asset('files/assets/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/js/SmoothScroll.js') }}"></script>
    <script src="{{ asset('files/assets/js/pcoded.min.js') }}"></script>
    <!-- custom js -->

    <!-- data-table js -->
    <script src="{{ asset('files/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('files/assets/pages/data-table/js/jszip.min.js') }}"></script>
    <script src="{{ asset('files/assets/pages/data-table/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('files/assets/pages/data-table/js/vfs_fonts.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

    <!-- pnotify js -->
    <script type="text/javascript" src="{{ asset('files/bower_components/pnotify/js/pnotify.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/pnotify/js/pnotify.desktop.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/pnotify/js/pnotify.buttons.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/pnotify/js/pnotify.confirm.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/pnotify/js/pnotify.callbacks.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/pnotify/js/pnotify.animate.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/pnotify/js/pnotify.history.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/pnotify/js/pnotify.mobile.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/bower_components/pnotify/js/pnotify.nonblock.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/pages/pnotify/notify.js') }}"></script>

    <!-- table js -->
    <script src="{{ asset('files/assets/pages/data-table/js/data-table-custom.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('files/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>

    <script src="{{ asset('files/assets/js/vartical-layout.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/pages/dashboard/custom-dashboard.js') }}"></script>
    <script type="text/javascript" src="{{ asset('files/assets/js/script.min.js') }}"></script>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async="" src="https://www.googletagmanager.com/gtag/js?id=UA-23581568-13"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-23581568-13');
</script>

<!-- js clock -->
<script>
    // The week days
    const weekDays = [ 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' ];
    // The Clock Ticker
    function clockTicker()
    {
        // Clock units
        var date    = new Date();
        var day     = date.getDay();
        var hrs     = date.getHours();
        var mins    = date.getMinutes();
        var secs    = date.getSeconds();

        // Update hours value if greater than 12
        if( hrs > 12 )
        {
            hrs = hrs - 12;
            const periodElement = document.querySelector( '#clock .period' );
            if (periodElement) periodElement.innerHTML = 'PM';
        }
        else
        {
            const periodElement = document.querySelector( '#clock .period' );
            if (periodElement) periodElement.innerHTML = 'AM';
        }
        // Pad the single digit units by 0
        hrs     = hrs < 10 ? "0" + hrs : hrs;
        mins    = mins < 10 ? "0" + mins : mins;
        secs    = secs < 10 ? "0" + secs : secs;

// Refresh the unit values
        const dayElement = document.querySelector( '#clock .day' );
        const hoursElement = document.querySelector( '#clock .hours' );
        const minutesElement = document.querySelector( '#clock .minutes' );
        const secondsElement = document.querySelector( '#clock .seconds' );
        
        if (dayElement) dayElement.innerHTML = weekDays[ day ];
        if (hoursElement) hoursElement.innerHTML = hrs;
        if (minutesElement) minutesElement.innerHTML = mins;
        if (secondsElement) secondsElement.innerHTML = secs;

    // Refresh the clock every 1 second
    requestAnimationFrame( clockTicker );
    }
    // Start the clock
    clockTicker();
</script>

    <script>
        // Real-time notification polling
        function updateNotificationCount() {
            fetch('/notifications/unread-count')
                .then(response => response.json())
                .then(data => {
                    const badge = document.querySelector('.pcoded-mtext:contains("Notifications")').closest('a').querySelector('.badge');
                    if (data.count > 0) {
                        if (badge) {
                            badge.textContent = data.count;
                        } else {
                            const link = document.querySelector('.pcoded-mtext:contains("Notifications")').closest('a');
                            const newBadge = document.createElement('span');
                            newBadge.className = 'badge badge-danger badge-pill ml-auto';
                            newBadge.textContent = data.count;
                            link.appendChild(newBadge);
                        }
                    } else {
                        if (badge) {
                            badge.remove();
                        }
                    }
                })
                .catch(error => console.error('Error updating notification count:', error));
        }

        // Update notification count every 30 seconds
        setInterval(updateNotificationCount, 30000);

        // Update on page load
        document.addEventListener('DOMContentLoaded', updateNotificationCount);
    </script>

 @yield('script')

</body>
</html>


