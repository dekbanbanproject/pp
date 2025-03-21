@extends('layouts.support_prs_airback')
@section('title', 'PK-OFFICE || Air-Service')

@section('content')
    <script>
        function TypeAdmin() {
            window.location.href = '{{ route('index') }}';
        }
        function air_main_repaire_destroy(air_repaire_id) {
            Swal.fire({
                position: "top-end",
                title: 'ต้องการลบใช่ไหม?',
                text: "ข้อมูลนี้จะถูกลบไปเลย !!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ใช่, ลบเดี๋ยวนี้ !',
                cancelButtonText: 'ไม่, ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('air_main_repaire_destroy') }}" + '/' + air_repaire_id,
                        type: 'POST',
                        data: {
                            _token: $("input[name=_token]").val()
                        },
                        success: function(response) {
                            if (response.status == 200 ) {
                                Swal.fire({
                                    position: "top-end",
                                    title: 'ลบข้อมูล!',
                                    text: "You Delet data success",
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonColor: '#06D177',
                                    // cancelButtonColor: '#d33',
                                    confirmButtonText: 'เรียบร้อย'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $("#sid" + air_repaire_id).remove();
                                        window.location.reload();
                                        // window.location = "{{ url('air_main') }}";
                                    }
                                })
                            } else {
                            }
                        }
                    })
                }
            })
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

    {{-- <style>
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
            border: 5px #ddd solid;
            border-top: 10px #12c6fd solid;
            border-radius: 50%;
            animation: sp-anime 0.8s infinite linear;
        }

        @keyframes sp-anime {
            100% {
                transform: rotate(360deg);
            }
        }

        .is-hide {
            display: none;
        }
    </style> --}}

    <?php
    $ynow = date('Y') + 543;
    $yb = date('Y') + 542;
    ?>

<div class="tabs-animation">
    {{-- <div class="row text-center">
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
    </div> --}}
    <div id="preloader">
        <div id="status">
            <div id="container_spin">
                <svg viewBox="0 0 100 100">
                    <defs>
                        <filter id="shadow">
                        <feDropShadow dx="0" dy="0" stdDeviation="2.5"
                            flood-color="#fc6767"/>
                        </filter>
                    </defs>
                    <circle id="spinner" style="fill:transparent;stroke:#dd2476;stroke-width: 7px;stroke-linecap: round;filter:url(#shadow);" cx="50" cy="50" r="45"/>
                </svg>
            </div>
        </div>
    </div>
    {{-- <form action="{{ url('air_report_problems') }}" method="GET">
        @csrf --}}
        <div class="row">
            <div class="col-md-6">
                <h5 style="color:rgb(255, 255, 255)">รายงานปัญหาที่มีการแจ้งซ่อมเครื่องปรับอากาศ แยกตามปัญหาที่พบ(ไม่ใช่การล้างประจำปี) </h5>
            </div>

            <div class="col"></div>
            <div class="col-md-5 text-end">
                <div class="input-daterange input-group" id="datepicker1" data-date-format="dd M, yyyy" data-date-autoclose="true" data-provide="datepicker" data-date-container='#datepicker1'>
                    <input type="text" class="form-control bt_prs" name="startdate" id="datepicker" placeholder="Start Date" data-date-container='#datepicker1' data-provide="datepicker" data-date-autoclose="true" autocomplete="off"
                        data-date-language="th-th" required/>
                    <input type="text" class="form-control bt_prs" name="enddate" placeholder="End Date" id="datepicker2" data-date-container='#datepicker1' data-provide="datepicker" data-date-autoclose="true" autocomplete="off"
                        data-date-language="th-th" />
                        {{-- <button type="submit" class="ladda-button btn-pill btn btn-primary card_prs_4" data-style="expand-left">
                            <span class="ladda-label"> <i class="fa-solid fa-magnifying-glass text-white me-2"></i>ค้นหา</span>
                        </button>  --}}
                        <button type="button" class="ladda-button btn-pill btn btn-success bt_prs" id="Processdata">
                            <i class="fa-solid fa-spinner text-white me-2"></i>ประมวลผล
                        </button>
                        <button type="button" class="ladda-button btn-pill btn btn-secondary bt_prs me-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            <i class="fa-solid fa-book-open-reader text-white me-2"></i>คู่มือ
                        </button>
                        {{-- <a href="{{url('air_report_problems_excel')}}" class="ladda-button btn-pill btn btn-success card_prs_4">
                            <span class="ladda-label"> <i class="fa-solid fa-file-excel text-white me-2"></i>Excel</span>
                        </a> --}}
                </div>
            </div>
        </div>
    {{-- </form> --}}

<div class="row mt-3">
    <div class="col-xl-12">
        <div class="card card_prs_4" style="background-color: rgb(229, 253, 245)">
            <div class="card-body">
                <p class="mb-0">
                    <div class="table-responsive">
                        <table id="example" class="table table-hover table-sm dt-responsive nowrap myTable" style=" border-spacing: 0; width: 100%;">
                        {{-- <table id="example" class="table table-striped table-bordered dt-responsive myTable" style="border-collapse: collapse; border-spacing: 0; width: 100%;">   --}}
                            {{-- <table id="example2" class="table table-striped table-bordered dt-responsive nowrap myTable" style="border-collapse: collapse; border-spacing: 0; width: 100%;">                        --}}
                            <thead>
                                    <tr style="font-size:13px">
                                        <th width="3%" class="text-center">ลำดับ</th>
                                        <th class="text-center">รายการปัญหาที่พบ</th>
                                        <th class="text-center">จำนวน/เครื่อง</th>
                                        <th class="text-center">ปัญหาเดิม 2 ครั้งขึ้นไป</th>
                                    </tr>
                            </thead>
                            <tbody>
                                <?php $i = 0; ?>
                                @foreach ($datashow as $item)
                                <?php $i++ ?>
                                <?php
                                    $count_air = DB::select('SELECT COUNT(air_list_num) as air_list_num FROM air_report_ploblems_sub WHERE air_repaire_ploblem_id = "'.$item->air_repaire_ploblem_id.'"');
                                    foreach ($count_air as $key => $val) {$countair = $val->air_list_num;}
                                ?>
                                    <tr>
                                        <td class="text-center" style="font-size:13px;width: 5%;color: rgb(13, 134, 185)">{{$i}}</td>
                                        <td class="text-start" style="font-size:14px;color: rgb(2, 95, 182)">{{$item->air_repaire_ploblemname}}</td>
                                        <td class="text-center" style="font-size:13px;width: 15%;color: rgb(112, 5, 98)">
                                            {{-- <a href="{{url('air_report_problem_group/'.$item->repaire_date_start.'/'.$item->repaire_date_end.'/'.$item->air_repaire_ploblem_id)}}" class="ladda-button btn-pill btn btn-sm card_prs_4" style="background-color: rgb(209, 181, 236);width: 70%;" target="_blank">
                                                <span class="ladda-label"> <i class="fa-solid fa-fan me-2" style="color: #8c07c0"></i>{{$item->count_ploblems}}</span>
                                            </a> --}}
                                            <a href="{{url('air_report_problem_group/'.$item->air_repaire_ploblem_id)}}" class="ladda-button btn-pill btn btn-sm card_prs_4" style="background-color: rgb(209, 181, 236);width: 70%;" target="_blank">
                                                <span class="ladda-label"> <i class="fa-solid fa-fan me-2" style="color: #8c07c0"></i>{{$item->count_ploblems}}</span>
                                            </a>

                                        </td>
                                        <td class="text-center" style="font-size:13px;width: 20%;color: rgb(247, 209, 212)">
                                            <a href="{{url('air_report_problem_morone/'.$item->repaire_date_start.'/'.$item->repaire_date_end.'/'.$item->air_repaire_ploblem_id)}}" class="ladda-button btn-pill btn btn-sm card_prs_4" style="background-color: rgb(250, 195, 200);width: 50%;" target="_blank">
                                            {{-- <a href="{{url('air_report_problem_morone/'.$item->air_repaire_ploblem_id)}}" class="ladda-button btn-pill btn btn-sm card_prs_4" style="background-color: rgb(250, 195, 200);width: 50%;" target="_blank"> --}}
                                                <span class="ladda-label"> <i class="fa-solid fa-fan me-2" style="color: rgb(253, 65, 81)"></i>{{$countair}}</span>
                                            </a>

                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </p>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">คู่มือการใช้งาน</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">

            {{-- <p style="color: rgb(255, 255, 255);font-size: 17px;">คู่มือการนำเข้าแผนการบำรุงรักษา</p> --}}
            <br><br>
            <img src="{{ asset('images/doc/pl1.jpg') }}" class="rounded" alt="Image" width="auto" height="520px">
            <br><br><br>
            <hr style="color: rgb(255, 255, 255);border: blueviolet">
            <hr style="color: rgb(255, 255, 255);border: blueviolet">
            <br><br><br>

            <img src="{{ asset('images/doc/pl2.jpg') }}" class="rounded" alt="Image" width="auto" height="520px">
            <br><br><br>
            <hr style="color: rgb(255, 255, 255);border: blueviolet">
            <hr style="color: rgb(255, 255, 255);border: blueviolet">

            <img src="{{ asset('images/doc/pl3.jpg') }}" class="rounded" alt="Image" width="auto" height="520px">
            <br><br><br>
            <hr style="color: rgb(255, 255, 255);border: blueviolet">
            <hr style="color: rgb(255, 255, 255);border: blueviolet">

            <img src="{{ asset('images/doc/pl4.jpg') }}" class="rounded" alt="Image" width="auto" height="520px">
            <br><br><br>
            <hr style="color: rgb(255, 255, 255);border: blueviolet">
            <hr style="color: rgb(255, 255, 255);border: blueviolet">

            <img src="{{ asset('images/doc/pl5.jpg') }}" class="rounded" alt="Image" width="auto" height="520px">
            <br><br><br>
            <hr style="color: rgb(255, 255, 255);border: blueviolet">
            <hr style="color: rgb(255, 255, 255);border: blueviolet">

            <img src="{{ asset('images/doc/pl6.jpg') }}" class="rounded" alt="Image" width="auto" height="520px">
            <br><br><br>
            <hr style="color: rgb(255, 255, 255);border: blueviolet">
            <hr style="color: rgb(255, 255, 255);border: blueviolet">

        </div>
        <div class="modal-footer">
          <button type="button" class="btn-icon btn-shadow btn-dashed btn btn-outline-danger" data-bs-dismiss="modal">  <i class="fa-solid fa-xmark me-2"></i>Close</button>
        </div>
      </div>
    </div>
  </div>

@endsection
@section('footer')
    <script>
        $(document).ready(function() {

            // $('select').select2();


            // $('#example2').DataTable();
            // var table = $('#example').DataTable({
            //     scrollY: '60vh',
            //     scrollCollapse: true,
            //     scrollX: true,
            //     "autoWidth": false,
            //     "pageLength": 10,
            //     "lengthMenu": [10,25,30,31,50,100,150,200,300],
            // });

            $('#datepicker').datepicker({
                format: 'yyyy-mm-dd'
            });
            $('#datepicker2').datepicker({
                format: 'yyyy-mm-dd'
            });

            $('#datepicker3').datepicker({
                format: 'yyyy-mm-dd'
            });
            $('#datepicker4').datepicker({
                format: 'yyyy-mm-dd'
            });
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#Processdata').click(function() {
                var startdate    = $('#datepicker').val();
                var enddate      = $('#datepicker2').val();
                Swal.fire({
                        position: "top-end",
                        title: 'ต้องการประมวลผลข้อมูลใช่ไหม ?',
                        text: "You Warn Process Data!",
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
                                    url: "{{ route('prs.air_report_problem_process') }}",
                                    type: "POST",
                                    dataType: 'json',
                                    data: {startdate,enddate},
                                    success: function(data) {
                                        if (data.status == 200) {
                                            Swal.fire({
                                                position: "top-end",
                                                title: 'ประมวลผลข้อมูลสำเร็จ',
                                                text: "You Process data success",
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
