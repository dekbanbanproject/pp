<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    <!-- Font Awesome -->
    <link href="{{ asset('assets/fontawesome/css/all.css') }}" rel="stylesheet">
    {{-- <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Srisakdi&display=swap" rel="stylesheet"> --}}
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('pkclaim/images/logo150.ico') }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
        integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

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
    <link rel="stylesheet" href="{{ asset('asset/js/plugins/select2/css/select2.min.css') }}">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- <link rel="stylesheet"
        href="{{ asset('disacc/vendors/pixeden-stroke-7-icon-master/pe-icon-7-stroke/dist/pe-icon-7-stroke.css') }}">
    <link href="{{ asset('acccph/styles/css/base.css') }}" rel="stylesheet"> --}}

    <link rel="stylesheet" href="{{ asset('disacc/vendors/@fortawesome/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('disacc/vendors/ionicons-npm/css/ionicons.css') }}">
    <link rel="stylesheet" href="{{ asset('disacc/vendors/linearicons-master/dist/web-font/style.css') }}">
    <link rel="stylesheet"
        href="{{ asset('disacc/vendors/pixeden-stroke-7-icon-master/pe-icon-7-stroke/dist/pe-icon-7-stroke.css') }}">
    <link href="{{ asset('disacc/styles/css/base.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dacccss.css') }}">

</head>
<style>
    body {
        /* background: */
        /* url(/pkbackoffice/public/images/bg7.png);  */
        /* -webkit-background-size: cover; */
        background-color: rgb(245, 240, 240);
        background-repeat: no-repeat;
        background-attachment: fixed;
        /* background-size: cover; */
        background-size: 100% 100%;
        /* display: flex; */
        /* align-items: center; */
        /* justify-content: center; */
        /* width: 100vw;   ให้เต็มพอดี */
        /* height: 100vh; ให้เต็มพอดี  */
    }

    .Bgsidebar {
        background-image: url('/pkbackoffice/public/images/bgside.jpg');
        background-repeat: no-repeat;
    }

    .Bgheader {
        background-image: url('/pkbackoffice/public/images/bgheader.jpg');
        background-repeat: no-repeat;
    }
    .myTable tbody tr{
        font-size:13px;
        height: 13px;
    }

    .input_new{
                border-radius: 2em 2em 2em 2em;
                box-shadow: 0 0 10px #c069d1;
                border:solid 1px #ca0adb;
            }
            .input_border{
                /* border-radius: 2em 2em 2em 2em; */
                box-shadow: 0 0 20px #c069d1;
                border:solid 1px #ca0adb;
            }
            .buttom_border{
                border-radius: 2em 2em 2em 2em;
                box-shadow: 0 0 15px #c069d1;
                border:solid 1px #ca0adb;
            }
            .card_pink{
                border-radius: 3em 3em 3em 3em;
                box-shadow: 0 0 30px #c069d1;
            }
            .card_audit_2b{
                border-radius: 0em 0em 3em 3em;
                box-shadow: 0 0 30px #c069d1;
            }
            .card_audit_4c{
                border-radius: 2em 2em 2em 2em;
                box-shadow: 0 0 30px #c069d1;
                border:solid 1px #ca0adb;
            }
            .card_audit_4{
                border-radius: 3em 3em 3em 3em;
                box-shadow: 0 0 30px #c069d1;
            }
            .dcheckbox_{
                width: 20px;
                height: 20px;
                border: 10px solid #c069d1;
                box-shadow: 0 0 10px #c069d1;
            }
            .select2-selection {
                border-color: green; /* example */
                }
            .select2-drop.select2-drop-above.select2-drop-active {
                border-top: 1px solid #ffff80;
            }
            .f12{
                font
            }

</style>
<?php
if (Auth::check()) {
    $type = Auth::user()->type;
    $iduser = Auth::user()->id;
} else {
    echo "<body onload=\"TypeAdmin()\"></body>";
    exit();
}
$url = Request::url();
$pos = strrpos($url, '/') + 1;

use App\Http\Controllers\StaticController;
use App\Models\Products_request_sub;
use App\Http\Controllers\RongController;
$checkhn               = StaticController::checkhn($iduser);
$checkhnshow           = StaticController::checkhnshow($iduser);
$orginfo_headep        = StaticController::orginfo_headep($iduser);
$orginfo_po            = StaticController::orginfo_po($iduser);
$countadmin            = StaticController::countadmin($iduser);
$permiss_account       = StaticController::permiss_account($iduser);
$permiss_setting_upstm = StaticController::permiss_setting_upstm($iduser);
$permiss_ucs           = StaticController::permiss_ucs($iduser);
$permiss_sss           = StaticController::permiss_sss($iduser);
$permiss_ofc           = StaticController::permiss_ofc($iduser);
$permiss_lgo           = StaticController::permiss_lgo($iduser);
$permiss_prb           = StaticController::permiss_prb($iduser);
$permiss_ti            = StaticController::permiss_ti($iduser);
$permiss_rep_money     = StaticController::permiss_rep_money($iduser);

?>
 {{-- <body data-sidebar="white" data-keep-enlarged="true" class="vertical-collpsed"> --}}
<body data-topbar="dark">
    {{-- <body style="background-image: url('my_bg.jpg');"> --}}
    <!-- Begin page -->
    <div id="layout-wrapper">

        <header id="page-topbar">
            {{-- <div class="navbar-header shadow-lg" style="background-color: rgb(252, 252, 252)"> --}}
                <div class="navbar-header shadow" style="background-color: rgb(107, 18, 158)">

                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box" style="background-color: rgb(255, 255, 255)">
                        <a href="" class="logo logo-dark">
                            <span class="logo-sm">
                                <img src="{{ asset('pkclaim/images/logo150.png') }}" alt="logo-sm" height="37">
                            </span>
                            <span class="logo-lg">
                                {{-- <img src="{{ asset('assets/images/logo-dark.png') }}" alt="logo-dark" height="20"> --}}
                                <h4 style="color:pink" class="mt-4">PK-OFFICE</h4>
                            </span>
                        </a>

                        <a href="" class="logo logo-light">
                            <span class="logo-sm mt-3">
                                <img src="{{ asset('pkclaim/images/logo150.png') }}" alt="logo-sm-light"
                                    height="40">
                            </span>
                            <span class="logo-lg">
                                <h4 style="color:rgb(107, 18, 158)" class="mt-4">PK-OFFICE</h4>
                            </span>
                        </a>
                    </div>

                    <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect"
                        id="vertical-menu-btn">
                        <i class="ri-menu-2-line align-middle" style="color: rgb(255, 255, 255)"></i>
                    </button>
                    <a href="{{url('account_pk_dash')}}">
                        <h4 style="color:rgb(255, 255, 255)" class="mt-4">ADMIN CONFIG</h4>
                    </a>

                    <?php
                    $org = DB::connection('mysql')->select('
                                                    select * from orginfo
                                                    where orginfo_id = 1                                                                                                                      ');
                    ?>
                    {{-- <form class="app-search d-none d-lg-block">
                        <div class="position-relative">
                            @foreach ($org as $item)
                            <h4 style="color:rgb(255, 255, 255)" class="mt-2">{{$item->orginfo_name}}</h4>
                            @endforeach

                        </div>
                    </form>                                          --}}
                </div>

                <div class="d-flex">
                    <div class="dropdown d-none d-lg-inline-block ms-1">
                        <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                            <i class="ri-fullscreen-line" style="color: rgb(54, 53, 53)"></i>
                        </button>
                    </div>
                    <div class="dropdown d-inline-block user-dropdown">
                        <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            @if (Auth::user()->img == null)
                                <img src="{{ asset('assets/images/default-image.jpg') }}" height="32px"
                                    width="32px" alt="Header Avatar" class="rounded-circle header-profile-user">
                            @else
                                <img src="{{ asset('storage/person/' . Auth::user()->img) }}" height="32px"
                                    width="32px" alt="Header Avatar" class="rounded-circle header-profile-user">
                            @endif
                            <span class="d-none d-xl-inline-block ms-1" style="font-size: 12px;color:black">
                                {{ Auth::user()->fname }} {{ Auth::user()->lname }}
                            </span>
                            <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a class="dropdown-item" href="{{ url('admin_profile_edit/' . Auth::user()->id) }}"
                                style="font-size: 12px"><i class="ri-user-line align-middle me-1"></i> Profile</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}" {{-- class="text-reset notification-item" --}}
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                                    class="ri-shut-down-line align-middle me-1 text-danger"></i>
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        {{-- <style>
            .nom6{
                background: linear-gradient(to right,#ffafbd);

            }
        </style> --}}

        <!-- ========== Left Sidebar Start ========== -->
        {{-- <div class="vertical-menu "> --}}
        <div class="vertical-menu">
            {{-- <div class="vertical-menu" style="background-color: rgb(128, 216, 209)"> --}}
            {{-- <div data-simplebar class="h-100"> --}}
                <div data-simplebar class="h-100 nom6">
                <!--- Sidemenu -->
                <div id="sidebar-menu">
                    <!-- Left Menu Start -->
                    <ul class="metismenu list-unstyled" id="side-menu">
                        <li class="menu-title">Menu</li>


                            <li>
                                <a href="javascript: void(0);" class="has-arrow waves-effect">
                                    <i class="fa-solid fa-gear text-danger" style="color: rgb(250, 124, 187)"></i>
                                    <span>ตั้งค่าทั่วไป</span>
                                </a>
                                <ul class="sub-menu" aria-expanded="true">
                                    <li><a href="{{ url('setting/setting_index') }}">กลุ่มภารกิจ</a> </li>
                                    <li><a href="{{ url('setting/depsub_index') }}">กลุ่มงาน</a> </li>
                                    <li><a href="{{ url('setting/depsubsub_index') }}">หน่วยงาน</a> </li>
                                    <li><a href="{{ url('setting/orginfo') }}">องค์กร</a> </li>
                                    <li><a href="{{ url('setting/leader') }}">กำหนดสิทธิ์การเห็นชอบ</a> </li>
                                    <li><a href="{{ url('setting/permiss') }}">กำหนดสิทธิ์การใช้งาน</a> </li>
                                    <li><a href="{{ url('setting/line_token') }}">Line Token ผู้ดูแลงาน</a> </li>
                                </ul>
                            </li>

                    </ul>
                </div>
                <!-- Sidebar -->
            </div>
        </div>
        <!-- Left Sidebar End -->



        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            {{-- background:url(/pkbackoffice/public/sky16/images/logo250.png)no-repeat 50%; --}}
            {{-- <div class="page-content Backgroupbody"> --}}
            <div class="page-content Backgroupbody">
                {{-- <div class="page-content"> --}}
                {{-- <div class="page-content" style="background-color: rgba(247, 244, 244, 0.911)"> --}}
                @yield('content')

            </div>
            <!-- End Page-content -->

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>
                                document.write(new Date().getFullYear())
                            </script> © โรงพยาบาลภูเขียวเฉลิมพระเกียรติ
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Created with <i class="mdi mdi-heart text-danger"></i> by ทีมพัฒนา PK-OFFICE
                            </div>
                        </div>
                    </div>
                </div>
            </footer>


        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->



    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('pkclaim/libs/jquery/jquery.min.js') }}"></script>

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

    <script type="text/javascript" src="{{ asset('acccph/vendors/jquery-circle-progress/dist/circle-progress.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('acccph/vendors/perfect-scrollbar/dist/perfect-scrollbar.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('acccph/vendors/toastr/build/toastr.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('acccph/vendors/jquery.fancytree/dist/jquery.fancytree-all-deps.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('acccph/vendors/apexcharts/dist/apexcharts.min.js') }}"></script>

    <script src="{{ asset('pkclaim/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js"
        integrity="sha512-cp+S0Bkyv7xKBSbmjJR0K7va0cor7vHYhETzm2Jy//ZTQDUvugH/byC4eWuTii9o5HN9msulx2zqhEXWau20Dg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

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

    {{-- <script type="text/javascript" src="{{ asset('acccph/vendors/@chenfengyuan/datepicker/dist/datepicker.min.js') }}"></script> --}}
    <script type="text/javascript" src="{{ asset('acccph/vendors/daterangepicker/daterangepicker.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/jquery-tabledit/jquery.tabledit.min.js') }}"></script>


    {{-- <script type="text/javascript" src="{{ asset('acccph/js/form-components/toggle-switch.js') }}"></script> --}}
    {{-- <script type="text/javascript" src="{{ asset('acccph/js/form-components/datepicker.js') }}"></script> --}}
    {{-- <script type="text/javascript" src="{{ asset('acccph/js/circle-progress.js') }}"></script> --}}
    <!-- App js -->
    <script src="{{ asset('pkclaim/js/app.js') }}"></script>
    {{-- <link href="{{ asset('acccph/styles/css/base.css') }}" rel="stylesheet"> --}}

    <script type="text/javascript" src="{{ asset('acccph/js/charts/apex-charts.js') }}"></script>
    <script type="text/javascript" src="{{ asset('acccph/js/circle-progress.js') }}"></script>
    <script type="text/javascript" src="{{ asset('acccph/js/demo.js') }}"></script>
    <script type="text/javascript" src="{{ asset('acccph/js/scrollbar.js') }}"></script>
    <script type="text/javascript" src="{{ asset('acccph/js/toastr.js') }}"></script>
    {{-- <script type="text/javascript" src="{{ asset('acccph/js/treeview.js') }}"></script> --}}
    <script type="text/javascript" src="{{ asset('acccph/js/form-components/toggle-switch.js') }}"></script>
    {{-- <script type="text/javascript" src="{{ asset('acccph/js/tables.js') }}"></script> --}}
    {{-- <script type="text/javascript" src="{{ asset('acccph/js/carousel-slider.js') }}"></script> --}}
    {{-- <script type="text/javascript" src="{{ asset('disacc/js/charts/chartjs.js') }}"></script> --}}
    {{-- <script type="text/javascript" src="{{ asset('acccph/js/app.js') }}"></script> --}}
    {{-- <script src="{{ asset('js/ladda.js') }}"></script>  --}}
    @yield('footer')


    <script type="text/javascript">

      $(document).ready(function () {
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
          // $('#saveBtn').click(function() {
          //         var dss_color = $('#DEPARTMENT_SUB_SUB_COLOR').val();
          //         var dss_id = $('#dss_id').val();
          //         $.ajax({
          //             url: "{{ route('setting.depsubsub_updatecolor') }}",
          //             type: "POST",
          //             dataType: 'json',
          //             data: {
          //               dss_color,
          //                 dss_id
          //             },
          //             success: function(data) {
          //                 if (data.status == 200) {
          //                     Swal.fire({
          //                         title: 'แก้ไขข้อมูลสำเร็จ',
          //                         text: "You edit data success",
          //                         icon: 'success',
          //                         showCancelButton: false,
          //                         confirmButtonColor: '#06D177',
          //                         confirmButtonText: 'เรียบร้อย'
          //                     }).then((result) => {
          //                         if (result
          //                             .isConfirmed) {
          //                             console.log(
          //                                 data);

          //                             window.location
          //                                 .reload();
          //                         }
          //                     })
          //                 } else {

          //                 }

          //             },
          //         });
          // });

      });

      $(document).ready(function(){


            $('#insert_depForm').on('submit',function(e){
                  e.preventDefault();
                  var form = this;
                  // alert('OJJJJOL');
                  $.ajax({
                        url:$(form).attr('action'),
                        method:$(form).attr('method'),
                        data:new FormData(form),
                        processData:false,
                        dataType:'json',
                        contentType:false,
                        beforeSend:function(){
                          $(form).find('span.error-text').text('');
                        },
                        success:function(data){
                          if (data.status == 0 ) {

                          } else {
                            Swal.fire({
                              title: 'บันทึกข้อมูลสำเร็จ',
                              text: "You Insert data success",
                              icon: 'success',
                              showCancelButton: false,
                              confirmButtonColor: '#06D177',
                              confirmButtonText: 'เรียบร้อย'
                            }).then((result) => {
                              if (result.isConfirmed) {
                                window.location="{{url('setting/setting_index')}}";
                              }
                            })
                          }
                        }
                  });
            });

            $('#update_depForm').on('submit',function(e){
                  e.preventDefault();

                  var form = this;
                  $.ajax({
                          url:$(form).attr('action'),
                          method:$(form).attr('method'),
                          data:new FormData(form),
                          processData:false,
                          dataType:'json',
                          contentType:false,
                          beforeSend:function(){
                            $(form).find('span.error-text').text('');
                          },
                          success:function(data){
                            if (data.status == 0 ) {

                            } else {
                              Swal.fire({
                                title: 'แก้ไขข้อมูลสำเร็จ',
                                text: "You edit data success",
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonColor: '#06D177',
                                confirmButtonText: 'เรียบร้อย'
                              }).then((result) => {
                                if (result.isConfirmed) {
                                  window.location="{{url('setting/setting_index')}}";
                                }
                              })
                            }
                          }
                    });
            });
      });

      $(document).ready(function(){
            $('#insert_depsubForm').on('submit',function(e){
                  e.preventDefault();
                  var form = this;
                  $.ajax({
                        url:$(form).attr('action'),
                        method:$(form).attr('method'),
                        data:new FormData(form),
                        processData:false,
                        dataType:'json',
                        contentType:false,
                        beforeSend:function(){
                          $(form).find('span.error-text').text('');
                        },
                        success:function(data){
                          if (data.status == 0 ) {

                          } else {
                            Swal.fire({
                              title: 'บันทึกข้อมูลสำเร็จ',
                              text: "You Insert data success",
                              icon: 'success',
                              showCancelButton: false,
                              confirmButtonColor: '#06D177',
                              confirmButtonText: 'เรียบร้อย'
                            }).then((result) => {
                              if (result.isConfirmed) {
                                window.location="{{url('setting/depsub_index')}}";
                              }
                            })
                          }
                        }
                  });
            });

            $('#update_depsubForm').on('submit',function(e){
                  e.preventDefault();

                  var form = this;
                  $.ajax({
                          url:$(form).attr('action'),
                          method:$(form).attr('method'),
                          data:new FormData(form),
                          processData:false,
                          dataType:'json',
                          contentType:false,
                          beforeSend:function(){
                            $(form).find('span.error-text').text('');
                          },
                          success:function(data){
                            if (data.status == 0 ) {

                            } else {
                              Swal.fire({
                                title: 'แก้ไขข้อมูลสำเร็จ',
                                text: "You edit data success",
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonColor: '#06D177',
                                confirmButtonText: 'เรียบร้อย'
                              }).then((result) => {
                                if (result.isConfirmed) {
                                  window.location="{{url('setting/depsub_index')}}";
                                }
                              })
                            }
                          }
                    });
            });
      });

      $(document).ready(function(){
            $('#insert_depsubsubForm').on('submit',function(e){
                  e.preventDefault();
                  var form = this;
                  $.ajax({
                        url:$(form).attr('action'),
                        method:$(form).attr('method'),
                        data:new FormData(form),
                        processData:false,
                        dataType:'json',
                        contentType:false,
                        beforeSend:function(){
                          $(form).find('span.error-text').text('');
                        },
                        success:function(data){
                          if (data.status == 0 ) {

                          } else {
                            Swal.fire({
                              title: 'บันทึกข้อมูลสำเร็จ',
                              text: "You Insert data success",
                              icon: 'success',
                              showCancelButton: false,
                              confirmButtonColor: '#06D177',
                              confirmButtonText: 'เรียบร้อย'
                            }).then((result) => {
                              if (result.isConfirmed) {
                                window.location="{{url('setting/depsubsub_index')}}";
                              }
                            })
                          }
                        }
                  });
            });

            $('#update_depsubsubForm').on('submit',function(e){
                  e.preventDefault();

                  var form = this;
                  $.ajax({
                          url:$(form).attr('action'),
                          method:$(form).attr('method'),
                          data:new FormData(form),
                          processData:false,
                          dataType:'json',
                          contentType:false,
                          beforeSend:function(){
                            $(form).find('span.error-text').text('');
                          },
                          success:function(data){
                            if (data.status == 0 ) {

                            } else {
                              Swal.fire({
                                title: 'แก้ไขข้อมูลสำเร็จ',
                                text: "You edit data success",
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonColor: '#06D177',
                                confirmButtonText: 'เรียบร้อย'
                              }).then((result) => {
                                if (result.isConfirmed) {
                                  window.location="{{url('setting/depsubsub_index')}}";
                                }
                              })
                            }
                          }
                    });
            });
      });

      $(document).ready(function(){
            $('#insert_leaderForm').on('submit',function(e){
                  e.preventDefault();
                  var form = this;
                  $.ajax({
                        url:$(form).attr('action'),
                        method:$(form).attr('method'),
                        data:new FormData(form),
                        processData:false,
                        dataType:'json',
                        contentType:false,
                        beforeSend:function(){
                          $(form).find('span.error-text').text('');
                        },
                        success:function(data){
                          if (data.status == 0 ) {

                          } else {
                            Swal.fire({
                              title: 'บันทึกข้อมูลสำเร็จ',
                              text: "You Insert data success",
                              icon: 'success',
                              showCancelButton: false,
                              confirmButtonColor: '#06D177',
                              confirmButtonText: 'เรียบร้อย'
                            }).then((result) => {
                              if (result.isConfirmed) {
                                window.location="{{url('setting/leader')}}";
                              }
                            })
                          }
                        }
                  });
            });

            $('#insert_leader2Form').on('submit',function(e){
                  e.preventDefault();
                  var form = this;
                  $.ajax({
                        url:$(form).attr('action'),
                        method:$(form).attr('method'),
                        data:new FormData(form),
                        processData:false,
                        dataType:'json',
                        contentType:false,
                        beforeSend:function(){
                          $(form).find('span.error-text').text('');
                        },
                        success:function(data){
                          if (data.status == 0 ) {

                          } else {
                            Swal.fire({
                              title: 'บันทึกข้อมูลสำเร็จ',
                              text: "You Insert data success",
                              icon: 'success',
                              showCancelButton: false,
                              confirmButtonColor: '#06D177',
                              confirmButtonText: 'เรียบร้อย'
                            }).then((result) => {
                              if (result.isConfirmed) {
                                window.location.reload();

                              }
                            })
                          }
                        }
                  });
            });

            $('#insert_leadersubForm').on('submit',function(e){
                  e.preventDefault();
                  var form = this;
                  $.ajax({
                        url:$(form).attr('action'),
                        method:$(form).attr('method'),
                        data:new FormData(form),
                        processData:false,
                        dataType:'json',
                        contentType:false,
                        beforeSend:function(){
                          $(form).find('span.error-text').text('');
                        },
                        success:function(data){
                          if (data.status == 0 ) {

                          } else {
                            Swal.fire({
                              title: 'บันทึกข้อมูลสำเร็จ',
                              text: "You Insert data success",
                              icon: 'success',
                              showCancelButton: false,
                              confirmButtonColor: '#06D177',
                              confirmButtonText: 'เรียบร้อย'
                            }).then((result) => {
                              if (result.isConfirmed) {
                                window.location.reload();
                                // window.location="{{url('setting/leader')}}";
                              }
                            })
                          }
                        }
                  });
            });

      });

      $(document).ready(function(){
              $('#LEADER_ID').select2({
                  placeholder:"หัวหน้ากลุ่มงาน",
                  allowClear:true
              });
              $('#LEADER_ID2').select2({
                  placeholder:"หัวหน้าฝ่าย/แผนก",
                  allowClear:true
              });
              $('#DEPARTMENT_ID').select2({
                  placeholder:"กลุ่มงาน",
                  allowClear:true
              });
              $('#LEADER_ID3').select2({
                  placeholder:"หัวหน้าหน่วยงาน",
                  allowClear:true
              });
              $('#LEADER_ID4').select2({
                  placeholder:"ผู้อนุมัติเห็นชอบ",
                  allowClear:true
              });
              $('#USER_ID').select2({
                  placeholder:"ผู้ถูกเห็นชอบ",
                  allowClear:true
              });
              $('#DEPARTMENT_SUB_ID').select2({
                  placeholder:"ฝ่าย/แผนก",
                  allowClear:true
              });
              $('#orginfo_manage_id').select2({
                  placeholder:"--เลือก--",
                  allowClear:true
              });
              $('#orginfo_po_id').select2({
                placeholder:"--เลือก--",
                  allowClear:true
              });
      });

      $(document).on('click','.edit_line',function(){
              var line_token_id = $(this).val();
              // alert(line_token_id);
                      $('#linetokenModal').modal('show');
                      $.ajax({
                      type: "GET",
                      url:"{{url('setting/line_token_edit')}}" +'/'+ line_token_id,
                      success: function(data) {
                          console.log(data.line_token.line_token_name);
                          $('#line_token_name').val(data.line_token.line_token_name)
                          $('#line_token_code').val(data.line_token.line_token_code)
                          $('#line_token_id').val(data.line_token.line_token_id)
                      },
              });
      });

      $(document).ready(function(){
          $('#insert_lineForm').on('submit',function(e){
              e.preventDefault();

              var form = this;
            //   alert('OJJJJOL');
              $.ajax({
                url:$(form).attr('action'),
                method:$(form).attr('method'),
                data:new FormData(form),
                processData:false,
                dataType:'json',
                contentType:false,
                beforeSend:function(){
                  $(form).find('span.error-text').text('');
                },
                success:function(data){
                  if (data.status == 0 ) {

                  } else {
                    Swal.fire({
                      title: 'แก้ไขข้อมูลสำเร็จ',
                      text: "You Update data success",
                      icon: 'success',
                      showCancelButton: false,
                      confirmButtonColor: '#06D177',
                      confirmButtonText: 'เรียบร้อย'
                    }).then((result) => {
                      if (result.isConfirmed) {
                        window.location.reload();
                      }
                    })
                  }
                }
              });
          });

          $('#Insert_permissForm').on('submit',function(e){
                  e.preventDefault();
                  var form = this;
                  $.ajax({
                        url:$(form).attr('action'),
                        method:$(form).attr('method'),
                        data:new FormData(form),
                        processData:false,
                        dataType:'json',
                        contentType:false,
                        beforeSend:function(){
                          $(form).find('span.error-text').text('');
                        },
                        success:function(data){
                          if (data.status == 0 ) {

                          } else {
                            Swal.fire({
                              title: 'บันทึกข้อมูลสำเร็จ',
                              text: "You Insert data success",
                              icon: 'success',
                              showCancelButton: false,
                              confirmButtonColor: '#06D177',
                              confirmButtonText: 'เรียบร้อย'
                            }).then((result) => {
                              if (result.isConfirmed) {
                                // window.location="{{url('setting/permiss')}}";
                                window.location.reload();
                              }
                            })
                          }
                        }
                  });
          });
          $('#update_infoorgForm').on('submit',function(e){
                e.preventDefault();
                var form = this;
                $.ajax({
                      url:$(form).attr('action'),
                      method:$(form).attr('method'),
                      data:new FormData(form),
                      processData:false,
                      dataType:'json',
                      contentType:false,
                      beforeSend:function(){
                        $(form).find('span.error-text').text('');
                      },
                      success:function(data){
                        if (data.status == 0 ) {

                        } else {
                          Swal.fire({
                            title: 'บันทึกข้อมูลสำเร็จ',
                            text: "You Insert data success",
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: '#06D177',
                            confirmButtonText: 'เรียบร้อย'
                          }).then((result) => {
                            if (result.isConfirmed) {
                              window.location.reload();
                            }
                          })
                        }
                      }
                });
          });
      });

</script>

</body>

</html>
