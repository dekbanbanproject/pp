@extends('layouts.audit')
@section('title', 'PK-OFFICE || Audit')
@section('content')
    <script>
        function TypeAdmin() {
            window.location.href = '{{ route('index') }}';
        }
    </script>
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
 
    
    ?>
    <style>
        #button {
            display: block;
            margin: 20px auto;
            padding: 30px 30px;
            background-color: #eee;
            border: solid #ccc 1px;
            cursor: pointer;
        }

        #overlay {
            position: fixed;
            top: 0;
            z-index: 100;
            width: 100%;
            height: 100%;
            display: none;
            background: rgba(0, 0, 0, 0.6);
        }

        .cv-spinner {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .spinner {
            width: 250px;
            height: 250px;
            border: 10px #ddd solid;
            border-top: 10px #0dc79f solid;
            border-radius: 50%;
            animation: sp-anime 0.8s infinite linear;
        }

        @keyframes sp-anime {
            100% {
                transform: rotate(390deg);
            }
        }

        .is-hide {
            display: none;
        }

        .modal-dis {
            width: 1350px;
            margin: auto;
        }

        @media (min-width: 1200px) {
            .modal-xlg {
                width: 90%;
            }
        }
    </style>

    <div class="tabs-animation">

        <div class="row text-center">
            <div id="overlay">
                <div class="cv-spinner">
                    <span class="spinner"></span>
                </div>
            </div>
        </div>
        <div id="preloader">
            <div id="status">
                <div class="spinner">
                </div>
            </div>
        </div>
        <form action="{{ URL('authen_excel') }}" method="GET">
            @csrf
            <div class="row">
                <div class="col-md-2">
                    <h4 class="card-title" style="color:rgb(250, 128, 124)">Detail Pre-Audit Authen</h4>
                    <p class="card-title-desc">รายละเอียดข้อมูล Pre-Audit Authen</p>
                </div>
                <div class="col-md-1 text-start"> 
                    <button type="button" class="ladda-button btn-pill btn btn-sm btn-secondary bt_prs me-2" data-bs-toggle="modal" data-bs-target="#exampleModal"> 
                        <i class="fa-solid fa-book-open-reader text-white me-2"></i>คู่มือ 
                    </button>
                </div>
                <div class="col"></div>
                <div class="col-md-5 text-end">
                    <div class="input-daterange input-group" id="datepicker1" data-date-format="dd M, yyyy"
                        data-date-autoclose="true" data-provide="datepicker" data-date-container='#datepicker1'>
                        <input type="text" class="form-control-sm card_audit_4" name="startdate" id="datepicker"
                            placeholder="Start Date" data-date-container='#datepicker1'
                            data-provide="datepicker" data-date-autoclose="true" autocomplete="off"
                            data-date-language="th-th" value="{{ $startdate }}" required />
                        <input type="text" class="form-control-sm card_audit_4" name="enddate"
                            placeholder="End Date" id="datepicker2" data-date-container='#datepicker1'
                            data-provide="datepicker" data-date-autoclose="true" autocomplete="off"
                            data-date-language="th-th" value="{{ $enddate }}" />
                            <button type="submit" class="ladda-button btn-pill btn btn-sm btn-primary cardacc" > 
                                <img src="{{ asset('images/Search02.png') }}" class="ms-2 me-2" height="23px" width="23px"> 
                                ค้นหา
                           </button>
                        </form> 
                           <button type="button" class="ladda-button me-2 btn-pill btn btn-sm btn-info cardacc" data-style="expand-left" id="Pulldata">
                                <span class="ladda-label"> 
                                    <img src="{{ asset('images/pull_datawhite.png') }}" class="me-2 ms-2" height="18px" width="18px"> 
                                    ดึงข้อมูล</span> 
                            </button>
                    </div>
                </div>
            </div>
    
            
        <div class="row">
            @if ($startdate =='')
                <div class="col-xl-6">
                    <div class="card card_audit_4" style="height: 165px">
                        <div class="row mt-5">
                            <div class="col"></div>
                            <div class="col-md-6 text-center">
                                @php
                                    $date_nows    = date('Y-m-d');
                                    $no_authencc_ = DB::connection('mysql10')->select(
                                        'SELECT count(DISTINCT c.vn) as noauth_code 
                                            FROM ovst c
                                            LEFT JOIN visit_pttype vp ON vp.vn = c.vn 
                                            WHERE c.vstdate = "'.$date_nows.'"
                                            AND vp.pttype NOT IN("M1","M2","M3","M4","M5","M6","13","23","91","X7","10","11","12","06","C4","L1","L2","L3","L4","l5","l6","O1","O2","O3","O4","O5","O6")  
                                            AND (vp.auth_code IS NULL OR vp.auth_code = "") 
                                    ');
                                    foreach ($no_authencc_ as $key => $valscc) {
                                        $no_authen_count = $valscc->noauth_code;
                                    }
                                @endphp
                                <h1 style="color:rgb(247, 31, 95)">ไม่ AUTHEN {{$no_authen_count}} Visit</h1>
                            
                            </div>
                            <div class="col-md-4 text-center">
                                <img src="{{ asset('images/Cloud_Phone100.png') }}" class="me-2 ms-2" height="70px" width="70px"> 
                            </div>
                            <div class="col"></div>
                        </div>
                    
                    </div>
                </div>
            @else
                <div class="col-xl-6">
                    <div class="card card_audit_4" style="height: 165px">
                        <div class="row mt-5">
                            <div class="col"></div>
                            <div class="col-md-6 text-center">
                                @php
                                  
                                    $no_authencc_ = DB::connection('mysql10')->select(
                                        'SELECT count(DISTINCT c.vn) as noauth_code 
                                            FROM ovst c
                                            LEFT JOIN visit_pttype vp ON vp.vn = c.vn 
                                            WHERE c.vstdate BETWEEN "'.$startdate.'" AND "'.$enddate.'"
                                            AND vp.pttype NOT IN("M1","M2","M3","M4","M5","M6","13","23","91","X7","10","11","12","06","C4","L1","L2","L3","L4","l5","l6","O1","O2","O3","O4","O5","O6")  
                                            AND (vp.auth_code IS NULL OR vp.auth_code = "") 
                                    ');
                                    foreach ($no_authencc_ as $key => $valscc) {
                                        $no_authen_count = $valscc->noauth_code;
                                    }
                                @endphp
                                <h1 style="color:rgb(247, 31, 95)">ไม่ AUTHEN {{$no_authen_count}} Visit</h1>
                            
                            </div>
                            <div class="col-md-4 text-center">
                                <img src="{{ asset('images/Cloud_Phone100.png') }}" class="me-2 ms-2" height="70px" width="70px"> 
                            </div>
                            <div class="col"></div>
                        </div>
                    
                    </div>
                </div>
            @endif
            
       
            <div class="col-xl-6">
                <div class="card card_audit_4">
                    <div class="card-body text-center">
                      
                            <form action="{{ route('audit.authen_excel_save') }}" method="POST" enctype="multipart/form-data"> 
                                @csrf
                                    <div class="row">
                                        <div class="mb-3 mt-2">
                                            <label for="formFileLg" class="form-label">UP AUTHEN EXCEL => ส่งข้อมูล</label>
                                            <input class="form-control form-control-lg" id="formFileLg" name="file"
                                                type="file" required>
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        </div>
                                        <div class="col-md-6 text-end">                                           
                                            <button type="submit" class="ladda-button me-2 btn-pill btn btn-warning cardacc" data-style="expand-left">
                                                <span class="ladda-label"> 
                                                     <img src="{{ asset('images/Download_white.png') }}" class="ms-2 me-2" height="23px" width="23px"> 
                                                    Import</span> 
                                            </button>  
                                        </div>
                                    </form> 
                                        <div class="col-md-6 text-start">
                                            <button type="button" class="ladda-button me-2 btn-pill btn btn-success card_fdh_4" id="Updatedata"> 
                                                <img src="{{ asset('images/Memory_sd.png') }}" class="ms-2 me-2" height="23px" width="23px"> 
                                                Update Authen 
                                            </button>
                                        </div>
                                </div>
                                
                     
                    </div>
                </div>
            </div>
           
        </div>
        <div class="row">
            @if ($startdate =='')
                <div class="col-xl-6">
                    <div class="card card_audit_4">
                        <div class="card-body">
                            <h4 class="card-title ms-2" style="color:rgb(241, 137, 155)">แยกตามรายชื่อเจ้าหน้าที่</h4>  
                                <div class="table-responsive">  
                                        <table id="scroll-vertical-datatable" class="table table-sm table-striped dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">ลำดับ</th> 
                                                    <th class="text-center">Staff</th>
                                                    <th class="text-center">Visit</th>
                                                    <th class="text-center">ขอ Authen Code</th>
                                                    <th class="text-center">ไม่ขอ Authen Code</th> 
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $jj = 1; ?>
                                                @foreach ($staff_new as $item2)
                                                    <?php   
                                                        $date_now   = date('Y-m-d');
                                                        $authen_ = DB::connection('mysql10')->select(
                                                            'SELECT count(DISTINCT vp.vn) as auth_code 
                                                                FROM ovst c
                                                                LEFT JOIN visit_pttype vp ON vp.vn = c.vn 
                                                                WHERE c.staff = "'.$item2->staff.'" AND c.vstdate = "'.$date_now.'"
                                                                AND vp.pttype NOT IN("M1","M2","M3","M4","M5","M6","13","23","91","X7","10","11","12","06","C4","L1","L2","L3","L4","l5","l6","O1","O2","O3","O4","O5","O6")  
                                                                AND (vp.auth_code IS NOT NULL OR vp.auth_code <> "") 
                                                        ');
                                                        foreach ($authen_ as $key => $val) {
                                                            $authen = $val->auth_code;
                                                        }
                                                        $no_authen_ = DB::connection('mysql10')->select(
                                                            'SELECT count(DISTINCT c.vn) as noauth_code 
                                                                FROM ovst c
                                                                LEFT JOIN visit_pttype vp ON vp.vn = c.vn 
                                                                WHERE c.staff = "'.$item2->staff.'" AND c.vstdate = "'.$date_now.'"
                                                                AND vp.pttype NOT IN("M1","M2","M3","M4","M5","M6","13","23","91","X7","10","11","12","06","C4","L1","L2","L3","L4","l5","l6","O1","O2","O3","O4","O5","O6")  
                                                                AND (vp.auth_code IS NULL OR vp.auth_code = "") 
                                                        ');
                                                        foreach ($no_authen_ as $key => $vals2) {
                                                            $no_authen = $vals2->noauth_code;
                                                        }
                                                        $Authenper_s = 100 * $authen / $item2->countvn;
                                                        $noAuthenper_s = 100 * $no_authen / $item2->countvn; 
                                                    ?>
                                                    
                                                    <tr > <td class="text-center" style="width: 5%">{{ $jj++ }}</td>
                                                        <td class="p-2">{{ $item2->staff_name }}</td>
                                                        <td class="text-center">{{ $item2->countvn }}</td>
                                                        <td class="text-center text-success">  
                                                            <a class="btn-icon btn-sm btn-shadow btn-dashed btn btn-outline-success">
                                                                {{ $authen }}
                                                                Visit
                                                            </a>
                                                            => {{ number_format($Authenper_s, 2) }}%
                                                        </td> 
                                                        <td class="text-center text-danger">
                                                            {{-- check_dashboard_staffno --}}
                                                            <a class="btn-icon btn-sm btn-shadow btn-dashed btn btn-outline-danger" href="{{ url('authen_excel_detail/' . $item2->staff.'/'. $item2->day.'/'. $item2->month.'/'. $item2->year) }}"  target="_blank">
                                                                {{ $no_authen }} 
                                                                Visit
                                                            </a>   
                                                            => {{ number_format($noAuthenper_s, 2) }}%
                                                        </td> 
                                                    </tr>
                                                @endforeach
                    
                                            </tbody>
                                        </table>
                                </div>

                        </div>
                    </div>
                </div>
            @else
                <div class="col-xl-6">
                    <div class="card card_audit_4">
                        <div class="card-body">
                            <h4 class="card-title ms-2" style="color:rgb(241, 137, 155)">แยกตามรายชื่อเจ้าหน้าที่</h4>  
                                <div class="table-responsive">  
                                        <table id="scroll-vertical-datatable" class="table table-sm table-striped dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">ลำดับ</th> 
                                                    <th class="text-center">Staff</th>
                                                    <th class="text-center">Visit</th>
                                                    <th class="text-center">ขอ Authen Code</th>
                                                    <th class="text-center">ไม่ขอ Authen Code</th> 
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $jj = 1; ?>
                                                @foreach ($staff_new as $item2)
                                                    <?php   
                                                       
                                                        $authen_ = DB::connection('mysql10')->select(
                                                            'SELECT count(DISTINCT vp.vn) as auth_code 
                                                                FROM ovst c
                                                                LEFT JOIN visit_pttype vp ON vp.vn = c.vn 
                                                                WHERE c.staff = "'.$item2->staff.'" AND c.vstdate BETWEEN "'.$startdate.'" AND "'.$enddate.'"
                                                                AND vp.pttype NOT IN("M1","M2","M3","M4","M5","M6","13","23","91","X7","10","11","12","06","C4","L1","L2","L3","L4","l5","l6","O1","O2","O3","O4","O5","O6")  
                                                                AND (vp.auth_code IS NOT NULL OR vp.auth_code <> "") 
                                                        ');
                                                        foreach ($authen_ as $key => $val) {
                                                            $authen = $val->auth_code;
                                                        }
                                                        $no_authen_ = DB::connection('mysql10')->select(
                                                            'SELECT count(DISTINCT c.vn) as noauth_code 
                                                                FROM ovst c
                                                                LEFT JOIN visit_pttype vp ON vp.vn = c.vn 
                                                                WHERE c.staff = "'.$item2->staff.'" AND c.vstdate BETWEEN "'.$startdate.'" AND "'.$enddate.'"
                                                                AND vp.pttype NOT IN("M1","M2","M3","M4","M5","M6","13","23","91","X7","10","11","12","06","C4","L1","L2","L3","L4","l5","l6","O1","O2","O3","O4","O5","O6")  
                                                                AND (vp.auth_code IS NULL OR vp.auth_code = "") 
                                                        ');
                                                        foreach ($no_authen_ as $key => $vals2) {
                                                            $no_authen = $vals2->noauth_code;
                                                        }
                                                        $Authenper_s = 100 * $authen / $item2->countvn;
                                                        $noAuthenper_s = 100 * $no_authen / $item2->countvn; 
                                                    ?>
                                                    
                                                    <tr > <td class="text-center" style="width: 5%">{{ $jj++ }}</td>
                                                        <td class="p-2">{{ $item2->staff_name }}</td>
                                                        <td class="text-center">{{ $item2->countvn }}</td>
                                                        <td class="text-center text-success">  
                                                            <a class="btn-icon btn-sm btn-shadow btn-dashed btn btn-outline-success">
                                                                {{ $authen }}
                                                                Visit
                                                            </a>
                                                            => {{ number_format($Authenper_s, 2) }}%
                                                        </td> 
                                                        <td class="text-center text-danger"> 
                                                            <a class="btn-icon btn-sm btn-shadow btn-dashed btn btn-outline-danger" href="{{ url('authen_excel_detail/' . $item2->staff.'/'. $item2->day.'/'. $item2->month.'/'. $item2->year) }}"  target="_blank">
                                                                {{ $no_authen }} 
                                                                Visit
                                                            </a>   
                                                            => {{ number_format($noAuthenper_s, 2) }}%
                                                        </td> 
                                                    </tr>
                                                @endforeach
                    
                                            </tbody>
                                        </table>
                                </div>

                        </div>
                    </div>
                </div>
            @endif
            

            <div class="col-xl-6">
                <div class="card card_audit_4">
                    <div class="card-body">

                        @if ($startdate =='')
                            <h4 class="card-title ms-2" style="color:rgb(241, 137, 155)">รายการที่ไม่ขอ Authen Code วันนี้({{DateThai($date)}}) </h4>  
                        @else
                            <h4 class="card-title ms-2" style="color:rgb(241, 137, 155)">รายการที่ไม่ขอ Authen Code วันที่ {{DateThai($startdate)}} - {{DateThai($enddate)}}</h4>  
                        @endif
                        


                            <div class="table-responsive">  
                                    <table id="example" class="table table-sm table-striped dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th class="text-center">ลำดับ</th> 
                                                <th class="text-center">hn</th>  
                                                <th class="text-center">cid</th>
                                                {{-- <th class="text-center">vstdate</th> --}}
                                                <th class="text-center">pttype</th>
                                                <th class="text-center">ชื่อ-สกุล</th>
                                                <th class="text-center">claimcode</th> 
                                                {{-- <th class="text-center">debit</th>  --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $jj = 1; ?>
                                            @foreach ($authen_excel as $item_n) 
                                                <tr > <td class="text-center" style="width: 5%">{{ $jj++ }}</td>
                                                    <td class="text-center" style="width: 5%">{{ $item_n->hn }}</td> 
                                                    <td class="text-center" style="width: 7%">{{ $item_n->cid }}</td>
                                                    {{-- <td class="text-center" style="width: 10%">{{ $item_n->vstdate }}</td> --}}
                                                    <td class="text-center" style="width: 5%">{{ $item_n->pttype }}</td>
                                                    <td class="p-2">{{ $item_n->ptname }}</td>  
                                                    <td class="text-center" style="width: 15%">
                                                        @if ($item_n->claim_code =='')
                                                            <span class="bg-danger badge me-2">*_*</span> 
                                                        @else
                                                            <span class="bg-success badge me-2">{{ $item_n->claim_code }}</span> 
                                                        @endif 
                                                    </td>
                                                    {{-- <td class="text-center" style="width: 5%">{{ $item_n->debit }}</td>  --}}
                                                </tr>
                                            @endforeach
                
                                        </tbody>
                                    </table>
                            </div>

                    </div>
                </div>
            </div> 
        </div>
    </div>

 
@endsection
@section('footer')

    <script>
        $(document).ready(function() {
            var table = $('#example').DataTable({
                scrollY: '60vh',
                scrollCollapse: true,
                scrollX: true,
                "autoWidth": false,
                "pageLength": 10,
                "lengthMenu": [10, 100, 150, 200, 300, 400, 500],
            });
            var table = $('#example2').DataTable({
                scrollY: '60vh',
                scrollCollapse: true,
                scrollX: true,
                "autoWidth": false,
                "pageLength": 10,
                "lengthMenu": [10, 100, 150, 200, 300, 400, 500],
            });
            var table = $('#example3').DataTable({
                scrollY: '60vh',
                scrollCollapse: true,
                scrollX: true,
                "autoWidth": false,
                "pageLength": 10,
                "lengthMenu": [10, 100, 150, 200, 300, 400, 500],
            });

            $('#datepicker').datepicker({
                format: 'yyyy-mm-dd'
            });
            $('#datepicker2').datepicker({
                format: 'yyyy-mm-dd'
            });

            $('#example').DataTable();
            $('#hospcode').select2({
                placeholder: "--เลือก--",
                allowClear: true
            });
            $('#stamp').on('click', function(e) {
                if ($(this).is(':checked', true)) {
                    $(".sub_chk").prop('checked', true);
                } else {
                    $(".sub_chk").prop('checked', false);
                }
            });
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $("#spinner-div").hide(); //Request is complete so hide spinner

            $('#Updatedata').click(function() {
                var startdate = $('#datepicker').val();
                var enddate = $('#datepicker2').val();
                Swal.fire({
                    title: 'ต้องการอัพเดทข้อมูลใช่ไหม ?',
                    text: "You Warn Update Data!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Update it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#overlay").fadeIn(300);
                        $("#spinner").show(); //Load button clicked show spinner 

                        $.ajax({
                            url: "{{ route('audit.authen_update') }}",
                            type: "POST",
                            dataType: 'json',
                            data: {
                                startdate,
                                enddate
                            },
                            success: function(data) {
                                if (data.status == 200) {
                                    Swal.fire({
                                        position: "top-end",
                                        title: 'อัพเดทข้อมูลสำเร็จ',
                                        text: "You Update data success",
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
                                            $('#spinner')
                                                .hide(); //Request is complete so hide spinner
                                            setTimeout(function() {
                                                $("#overlay").fadeOut(
                                                    300);
                                            }, 500);
                                        }
                                    })
                                } else {

                                    // Swal.fire({
                                    //     position: "top-end",
                                    //     icon: "warning",
                                    //     title: "ยังไม่ได้เลือกวันที่",
                                    //     showCancelButton: false,
                                    //     confirmButtonColor: '#ed8d29',
                                    //     confirmButtonText: 'เลือกใหม่'
                                    //     // timer: 1500
                                    // }).then((result) => {
                                    //     if (result
                                    //         .isConfirmed) {
                                    //         window.location.reload();
                                    //     }
                                    // })

                                }
                            },
                        });

                    }
                })
            });

            $('#Pulldata').click(function() {
                var datepicker    = $('#datepicker').val(); 
                var datepicker2   = $('#datepicker2').val(); 
                Swal.fire({ position: "top-end",
                        title: 'ต้องการดึงข้อมูลใช่ไหม ?',
                        text: "You Warn Pull Data!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, pull it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $("#overlay").fadeIn(300);　
                                $("#spinner").show(); //Load button clicked show spinner 
                                
                                $.ajax({
                                    url: "{{ route('claim.authen_excel_process') }}",
                                    type: "POST",
                                    dataType: 'json',
                                    data: {datepicker,datepicker2},
                                    success: function(data) {
                                        if (data.status == 200) { 
                                            Swal.fire({ position: "top-end",
                                                title: 'ดึงข้อมูลสำเร็จ',
                                                text: "You Pull data success",
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
                                                    $('#spinner').hide();//Request is complete so hide spinner
                                                        setTimeout(function(){
                                                            $("#overlay").fadeOut(300);
                                                        },500);
                                                }
                                            })
                                        } else {
                                            
                                        }
                                    },
                                });
                                
                            }
                })
            });

           
        });




   
    </script>
@endsection
