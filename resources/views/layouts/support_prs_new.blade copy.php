<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    <!-- Font Awesome -->
    <link href="{{ asset('assets/fontawesome/css/all.css') }}" rel="stylesheet">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('pkclaim/images/logo150.ico') }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai+Looped:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- <link href="{{ asset('pkclaim/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css"> --}}
    <link href="{{ asset('pkclaim/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('pkclaim/libs/spectrum-colorpicker2/spectrum.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('pkclaim/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet">

    <!-- jquery.vectormap css -->
    <link href="{{ asset('pkclaim/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css') }}"
        rel="stylesheet" type="text/css" />

    <!-- DataTables -->
    <link href="{{ asset('pkclaim/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('pkclaim/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('pkclaim/libs/datatables.net-select-bs4/css//select.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="{{ asset('pkclaim/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}"
        rel="stylesheet" type="text/css" />

    <!-- Bootstrap Css -->
    <link href="{{ asset('pkclaim/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('pkclaim/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('pkclaim/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />

    <link href="{{ asset('css/fullcalendar.css') }}" rel="stylesheet">
   <!-- select2 -->
    <link rel="stylesheet" href="{{asset('asset/js/plugins/select2/css/select2.min.css')}}">
   <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <link rel="stylesheet"
   href="{{ asset('disacc/vendors/pixeden-stroke-7-icon-master/pe-icon-7-stroke/dist/pe-icon-7-stroke.css') }}">
<!-- Plugins css -->
<link href="{{ asset('assets/libs/dropzone/min/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/loginheader.css') }}" rel="stylesheet" />
<link href="{{ asset('acccph/styles/css/base.css') }}" rel="stylesheet">
</head>
 <style>
    body{
        background:
            /* url(/pkbackoffice/public/images/bg2.jpg); */
            /* -webkit-background-size: cover; */
        background-repeat: no-repeat;
		background-attachment: fixed;
		/* background-size: cover; */
        background-size: 100% 100%;
        }
        /* div {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%,-50%);
            } */

                #container_spin {
                    width: 200px;
                    height: 200px;
                }

                @keyframes animation {
                0% {
                    stroke-dasharray: 1 98;
                    stroke-dashoffset: -105;
                }
                50% {
                    stroke-dasharray: 80 10;
                    stroke-dashoffset: -160;
                }
                100% {
                    stroke-dasharray: 1 98;
                    stroke-dashoffset: -300;
                }
                }

                #spinner {
                    transform-origin: center;
                    animation-name: animation;
                    animation-duration: 1.2s;
                    animation-timing-function: cubic-bezier;
                    animation-iteration-count: infinite;
                }

 </style>


<style>

    *{
        margin: 0;
        padding: 0;
    }
    .headerZ{
        z-index: 1;
        background:linear-gradient(-45deg,red,rgb(201, 241, 154),rgb(184, 230, 226),rgb(238, 238, 107));
        background-size: 400% 400%;
        width: 100%;
        height: 100vh;
        animation: animate 5s ease infinite;
    }
    @keyframes animate{
        0%{
            background-position: 0 50%;
        }
        50%{
            background-position: 100% 50%;
        }
        0%{
            background-position: 0 50%;
        }
    }



    @keyframes colorShift {
        0% {
            background-color: #22dcdf
        }
        50% {
            background-color: #2ed82e
        }
        100% {
            background-color: #e95a5a
        }
    }
    .loadingIcon {
        width: 40px;
        height: 40px;
        border: 2px solid rgb(255, 255, 255);
        /* border-bottom: 4px solid transparent; */
        border-radius: 100%;
        animation: 6s infinite linear spin;
    }
    .loadingIcon2 {
        width: 40px;
        height: 40px;
        border: 2px solid rgb(255, 255, 255);
        /* border-bottom: 4px solid transparent; */
        border-radius: 100%;
        animation: 3s infinite linear spin;
    }
    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    .card_prs_2b{
        border-radius: 0em 0em 2em 2em;
        box-shadow: 0 0 15px rgb(124, 225, 250);
        /* border-color: #0583cc */
        border:solid 1px #0583cc;
    }
    .card_prs_4{
        border-radius: 2em 2em 2em 2em;
        box-shadow: 0 0 15px rgb(124, 225, 250);
        /* border-color: #0583cc */
        border:solid 1px #0583cc;
    }
    .prscheckbox{         
        width: 20px;
        height: 20px;       
        /* border-radius: 2em 2em 2em 2em; */
        border: 10px solid rgb(250, 128, 124);
        /* color: teal; */
        /* border-color: teal; */
        box-shadow: 0 0 10px rgb(250, 128, 124);
        /* box-shadow: 0 0 10px teal; */
    }
    .dcheckbox{         
        width: 25px;
        height: 25px;       
        /* border-radius: 2em 2em 2em 2em; */
        border: 2px solid rgb(250, 128, 124);
        /* color: teal; */
        /* border-color: teal; */
        box-shadow: 0 0 5px rgb(250, 128, 124);
        /* box-shadow: 0 0 10px teal; */
    }


</style>

 <body data-topbar="dark">

    <!-- Begin page -->
    <div id="layout-wrapper">

        <header id="page-topbar">
            {{-- <div class="navbar-header shadow-lg" style="background-color: rgb(252, 252, 252)"> --}}
                <div class="navbar-header shadow" style="background-color: #8be2df">

                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box">
                            <a href="{{url('support_system_dashboard')}}" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="assets/images/p.png" alt="logo-sm" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="assets/images/logo-dark.png" alt="logo-dark" height="20">
                                </span>
                            </a>
    
                            <a href="{{url('support_system_dashboard')}}" class="logo logo-light">
                                <span class="logo-sm me-2">
                                    <img src="{{ asset('images/pk_smal.png') }}" alt="logo-sm-light" height="40">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('images/p.png') }}" class="loadingIcon2" alt="logo-sm-light" height="30">
                                    <img src="{{ asset('images/k.png') }}" class="loadingIcon" alt="logo-sm-light" height="30">
                                    <img src="{{ asset('images/officer5.png') }}" class="" alt="logo-sm-light" height="30">
                                    {{-- <h4 style="color:rgb(41, 41, 41)" class="mt-4 loadingIcon">PK-OFFICE</h4> --}}
                                    {{-- <div class = "loadingIcon"></div> --}}
                                    {{-- <i class="fa-solid fa-p fa-w-16 fa-spin fa-2x text-info"></i> --}}
                                </span>
                                {{-- <div class="header-btn-lg"> 
                                    <a href="{{url("user/home")}}" id="TooltipDemo" class="btn-open-options btn hamburger hamburger--elastic open-right-drawer text-danger" target="_blank">
                                      
                                        <i class="fa-solid fa-universal-access fa-w-16 fa-spin fa-2x text-info" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="ผู้ใช้งานทั่วไป"></i>
                                    </a>  
                                </div> --}}
                            </a>
                        </div>
    
                        <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect" id="vertical-menu-btn">
                            <i class="ri-menu-2-line align-middle" style="color: black"></i>
                        </button>
                        <?php
                            $org = DB::connection('mysql')->select(                                                            '
                                    select * from orginfo
                                    where orginfo_id = 1                                                                                                                      ',
                            );
                        ?>
                        <form class="app-search d-none d-lg-block">
                            <div class="position-relative">
                                @foreach ($org as $item)
                                <h3 style="color:rgb(255, 255, 255)" class="mt-2 noto-sans-thai-looped-light">Support-System</h3>
                                @endforeach
    
                            </div>
                        </form>
                    </div>
     
                    <div class="d-flex">                    
                      
                            <div class="header-btn-lg"> 
                                <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                                    <i class="ri-fullscreen-line" style="color: rgb(9, 75, 129);font-size:30px;" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Fullscreen"></i>
                                </button>
                            </div>
                         
                           
                            <div class="header-btn-lg">
                                <div class="dropdown">
                                    <button type="button" aria-haspopup="true" aria-expanded="false" data-bs-toggle="dropdown" class="p-0 me-2 btn btn-link">
                                        <span class="icon-wrapper icon-wrapper-alt rounded-circle">
                                            <span class="icon-wrapper-bg bg-danger"></span>
                                         
                                            <i class="fa-regular fa-bell text-danger"></i>
                                        </span>  
                                    </button>
                                </div> 
                            </div>
                          
                            <div class="header-btn-lg pe-0">
                                <div class="widget-content p-0">
                                    <div class="widget-content-wrapper">
                                        <div class="widget-content-left">
                                            <div class="btn-group"> 
                                                <a data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="p-0 btn"> 
                                                    @if (Auth::user()->img == null)
                                                        <img src="{{ asset('assets/images/default-image.jpg') }}" width="42" class="rounded-circle loadingIcon" alt="">
                                                    @else
                                                        <img src="{{ asset('storage/person/' . Auth::user()->img) }}"width="42" class="rounded-circle loadingIcon" alt="">
                                                    @endif 
                                                    <i class="fa fa-angle-down ms-2 opacity-8"></i>
                                                </a>
                                                <div tabindex="-1" role="menu" aria-hidden="true" class="rm-pointers dropdown-menu-lg dropdown-menu dropdown-menu-right">
                                                    <div class="dropdown-menu-header">
                                                        <div class="dropdown-menu-header-inner bg-primary">
                                                          
                                                            <div class="menu-header-content text-start">
                                                                <div class="widget-content p-0">
                                                                    <div class="widget-content-wrapper">
                                                                        <div class="widget-content-left me-3"> 
                                                                                @if (Auth::user()->img == null)
                                                                                    <img src="{{ asset('assets/images/default-image.jpg') }}" width="42" class="rounded-circle" alt="">
                                                                                @else
                                                                                    <img src="{{ asset('storage/person/' . Auth::user()->img) }}"width="42" class="rounded-circle" alt="">
                                                                                @endif
                                                                        </div>
                                                                        <div class="widget-content-left">
                                                                            <div class="widget-heading"> {{ Auth::user()->fname }} {{ Auth::user()->lname }}</div>
                                                                            <div class="widget-subheading opacity-8">{{ Auth::user()->position_name }}</div>
                                                                        </div>
                                                                        <div class="widget-content-right me-2"> 
                                                                            {{-- <a class="btn-pill btn-shadow btn-shine btn btn-focus" href="{{ route('logout') }}"
                                                                                class="text-reset notification-item"
                                                                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                                                                                    class="ri-shut-down-line align-middle me-2 text-white"></i> Logout
                                                                            </a>
                                                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                                                                @csrf
                                                                            </form> --}}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="scroll-area-xs" style="height: 100px;">
                                                        <div class="scrollbar-container ps">
                                                            <ul class="nav flex-column"> 
                                                                <li class="nav-item">
                                                                    <a href="javascript:void(0);" class="nav-link">
                                                                        Messages
                                                                        <div class="ms-auto badge rounded-pill bg-primary">2</div>
                                                                    </a>
                                                                </li>
                                                                <li class="nav-item">
                                                                    <a href="javascript:void(0);" class="nav-link" data-bs-toggle="modal" data-bs-target="#Keypassword">
                                                                        Change Password<i class="fa-solid fa-key ms-4" style="color: rgb(207, 34, 115)"></i>
                                                                        <div class="ms-auto badge rounded-pill bg-info">1</div>
                                                                    </a>
                                                                </li>
                                                              
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <ul class="nav flex-column">
                                                        <li class="nav-item-divider mb-0 nav-item"></li>
                                                    </ul>
                                                    <div class="grid-menu grid-menu-2col">
                                                        <div class="g-0 row">
                                                            <div class="col-sm-6">
                                                                {{-- <button class="btn-icon-vertical btn-transition btn-transition-alt pt-2 pb-2 btn btn-outline-warning">
                                                                    <i class="pe-7s-chat icon-gradient bg-amy-crisp btn-icon-wrapper mb-2"></i>
                                                                    Message Inbox
                                                                </button> --}}
                                                                <a class="btn-icon-vertical btn-transition btn-transition-alt btn btn-outline-warning"  href="{{ url('admin_profile_edit/' . Auth::user()->id) }}"> 
                                                                    <button class="btn-icon-vertical btn-transition btn-transition-alt pb-2 btn btn-outline-warning">
                                                                        <i class="ri-user-line btn-icon-wrapper mb-2"></i>
                                                                        Profile
                                                                    </button>
                                                                </a>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                                                    @csrf
                                                                </form>
                                                                <a class="btn-icon-vertical btn-transition btn-transition-alt btn btn-outline-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> 
                                                                    <button class="btn-icon-vertical btn-transition btn-transition-alt pb-2 btn btn-outline-danger">
                                                                        <i class="ri-shut-down-line align-middle btn-icon-wrapper mb-2"></i>
                                                                        Logout
                                                                    </button>
                                                                </a>
                                                             
                                                            </div>
                                                            
    
                                                        </div>
                                                    </div> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="widget-content-left  ms-3 header-user-info">
                                            <div class="widget-heading"> {{ Auth::user()->fname }} {{ Auth::user()->lname }}</div>
                                            <div class="widget-subheading"> {{ Auth::user()->position_name }}</div>
                                        </div>
                                         
                                    </div>
                                </div>
                            </div>
                            <div class="header-btn-lg">
                                <button type="button" class="hamburger hamburger--elastic open-right-drawer">
                                    <span class="hamburger-box">
                                        <span class="hamburger-inner"></span>
                                    </span>
                                </button>
                            </div>
    
                    </div>
            </div>
        </header>

         <!--  Modal content for the Keypassword example -->
         <div class="modal fade" id="Keypassword" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="myExtraLargeModalLabel">เปลี่ยนรหัสผ่าน </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 text-end"><label for="">รหัสผ่าน New</label></div>
                            <div class="col-md-7">
                                <div class="form-group text-center">
                                    <input type="password" class="form-control form-control-sm" id="password" name="password">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12 text-end">
                            <div class="form-group">
                                <button type="button" id="SaveChang" class="btn btn-outline-info btn-sm" >
                                    <i class="fa-solid fa-floppy-disk me-1 text-info"></i>
                                    เปลี่ยน
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal"><i
                                        class="fa-solid fa-xmark text-danger me-2"></i>ปิด</button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

         <div class="mt-5">
            {{-- <div class="mt-5 headerZ"> --}}
            @yield('content')
         </div>



        {{-- <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <script>
                            document.write(new Date().getFullYear())
                        </script> © โรงพยาบาลภูเขียวเฉลิมพระเกียรติ
                    </div>
                    <div class="col-sm-6">
                        <div class="text-sm-end d-none d-sm-block">
                            Created with <i class="mdi mdi-heart text-danger"></i> by ประดิษฐ์ ระหา - งานสุขภาพดิจิทัล
                        </div>
                    </div>
                </div>
            </div>
        </footer> --}}


    </div>
    <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->



    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>


    <!-- JAVASCRIPT -->
    <script src="{{ asset('pkclaim/libs/jquery/jquery.min.js') }}"></script>
    {{-- <script type="text/javascript" src="{{ asset('vendors/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script> --}}
    <script src="{{ asset('pkclaim/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('pkclaim/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('pkclaim/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('pkclaim/libs/node-waves/waves.min.js') }}"></script>

    <script src="{{ asset('js/select2.min.js') }}"></script>
    {{-- <script src="{{ asset('pkclaim/libs/select2/js/select2.min.js') }}"></script> --}}
    <script src="{{ asset('pkclaim/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('pkclaim/libs/spectrum-colorpicker2/spectrum.min.js') }}"></script>
    <script src="{{ asset('pkclaim/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js') }}"></script>
    <script src="{{ asset('pkclaim/libs/admin-resources/bootstrap-filestyle/bootstrap-filestyle.min.js') }}"></script>
    <script src="{{ asset('pkclaim/libs/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>

    <script src="{{ asset('pkclaim/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js" integrity="sha512-cp+S0Bkyv7xKBSbmjJR0K7va0cor7vHYhETzm2Jy//ZTQDUvugH/byC4eWuTii9o5HN9msulx2zqhEXWau20Dg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- jquery.vectormap map -->
    <script src="{{ asset('pkclaim/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
    <script src="{{ asset('pkclaim/libs/admin-resources/jquery.vectormap/maps/jquery-jvectormap-us-merc-en.js') }}">
    </script>

    <!-- Required datatable js -->
    <script src="{{ asset('pkclaim/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('pkclaim/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Buttons examples -->
    <script src="{{ asset('pkclaim/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('pkclaim/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('pkclaim/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('pkclaim/libs/pdfmake/build/pdfmake.min.js') }}"></script>
    <script src="{{ asset('pkclaim/libs/pdfmake/build/vfs_fonts.js') }}"></script>
    <script src="{{ asset('pkclaim/libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('pkclaim/libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('pkclaim/libs/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>

    <script src="{{ asset('pkclaim/libs/datatables.net-keytable/js/dataTables.keyTable.min.js') }}"></script>
    <script src="{{ asset('pkclaim/libs/datatables.net-select/js/dataTables.select.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('pkclaim/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('pkclaim/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

    <!-- Datatable init js -->
    <script src="{{ asset('pkclaim/js/pages/datatables.init.js') }}"></script>
    <script src="{{ asset('pkclaim/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js') }}"></script>

    <script src="{{ asset('pkclaim/libs/twitter-bootstrap-wizard/prettify.js') }}"></script>


    <script src="{{ asset('pkclaim/js/pages/form-wizard.init.js') }}"></script>
    <script type="text/javascript" src="{{ asset('fullcalendar/lib/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('fullcalendar/fullcalendar.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('fullcalendar/lang/th.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- App js -->
    <script src="{{ asset('pkclaim/js/app.js') }}"></script>
 
    @yield('footer')

    <script type="text/javascript">
        $(document).ready(function() {
            $('#example').DataTable();
            $('#example2').DataTable();
            $('#example3').DataTable();
            $('#example4').DataTable();
            $('#example5').DataTable();
            $('#example_user').DataTable();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#SaveChang').click(function() {
                var password = $('#password').val();  
                $.ajax({
                    url: "{{ route('user.password_update') }}",
                    type: "POST",
                    dataType: 'json',
                    data: { 
                        password                       
                    },
                    success: function(data) {
                        if (data.status == 200) {
                            Swal.fire({
                                title: 'เปลี่ยนรหัสผ่านสำเร็จ',
                                text: "You Chang password success",
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonColor: '#06D177',
                                confirmButtonText: 'เรียบร้อย'
                            }).then((result) => {
                                if (result
                                    .isConfirmed) {
                                    console.log(
                                        data);
                                    window.location.reload();
                                     
                                }
                            })
                        } else {
                             
                        }

                    },
                });
            });
        });

    </script>
</body>

</html>
