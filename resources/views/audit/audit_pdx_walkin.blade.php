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
       
            <div class="row">
                <div class="col-md-3">
                    <h4 class="card-title" style="color:rgb(250, 128, 124)">Detail Pre-Audit WALKIN</h4>
                    <p class="card-title-desc">รายละเอียดข้อมูล Pre-Audit WALKIN</p>
                </div>
                <div class="col"></div> 
            </div>

            <div class="row">
                <div class="col-xl-6">
                    <div class="card card_audit_4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <h4 class="card-title" style="color:rgb(241, 137, 155)"">DIAG WALKIN</h4>
                                </div>
                                <div class="col"></div>
                                
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12"> 
                                    <div class="table-responsive">
                                        <table id="example5"
                                            class="table table-striped table-bordered dt-responsive nowrap"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">ลำดับ</th>
                                                    <th class="text-center">Year</th>
                                                    <th class="text-center">Month</th>
                                                    <th class="text-center">Visit ทั้งหมด</th>
                                                    <th class="text-center">Debit-ทั้งหมด</th>
                                                    <th class="text-center">Debit-ไม่ Diag</th>
                                                    <th class="text-center">Visit ไม่ Diag</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $jj = 1; ?>
                                                @foreach ($walkin as $item)
                                                <?php 
                                                    $no_app = DB::connection('mysql10')->select(
                                                        'SELECT year(v.vstdate) as years ,month(v.vstdate) as months,year(v.vstdate) as days 
                                                        ,count(DISTINCT v.vn) as countvn_no  ,sum(v.income)-sum(v.discount_money)-sum(v.rcpt_money) as sum_totalnopdx 
                                                        FROM ovst o
                                                        LEFT JOIN vn_stat v ON v.vn = o.vn 
                                                        LEFT JOIN visit_pttype vp ON vp.vn = o.vn 
                                                        WHERE v.pttype IN("W1") AND (v.pdx IS NULL OR v.pdx ="") AND (vp.claim_code IS NOT NULL OR vp.claim_code <>"")
                                                        AND (o.an IS NULL OR o.an ="") AND month(v.vstdate) = "'.$item->months.'" AND year(v.vstdate) = "'.$item->years.'" 
                                                        
                                                    ');  
                                               
                                                    foreach ($no_app as $key => $val_sdx) {
                                                        $sum_total_nodiag = $val_sdx->sum_totalnopdx;
                                                        $countallnovn_    = $val_sdx->countvn_no;
                                                    }
                                                   
                                                ?> 
                                                    <tr>
                                                        <td class="text-center" style="width: 5%">{{ $jj++ }}</td>
                                                        <td class="text-center" width="10%">{{ $item->years }}</td> 
                                                        @if ($item->months == '1')
                                                        <td class="text-start" width="15%">มกราคม</td> 
                                                        @elseif ($item->months == '2')
                                                            <td class="text-start" width="15%">กุมภาพันธ์</td> 
                                                        @elseif ($item->months == '3')
                                                            <td class="text-start" width="15%">มีนาคม</td> 
                                                        @elseif ($item->months == '4')
                                                            <td class="text-start" width="15%">เมษายน</td> 
                                                        @elseif ($item->months == '5')
                                                            <td class="text-start" width="15%">พฤษภาคม</td> 
                                                        @elseif ($item->months == '6')
                                                            <td class="text-start" width="15%">มิถุนายน</td> 
                                                        @elseif ($item->months == '7')
                                                            <td class="text-start" width="15%">กรกฎาคม</td> 
                                                        @elseif ($item->months == '8')
                                                            <td class="text-start" width="15%">สิงหาคม</td> 
                                                        @elseif ($item->months == '9')
                                                            <td class="text-start" width="15%">กันยายน</td> 
                                                        @elseif ($item->months == '10')
                                                            <td class="text-start" width="15%">ตุลาคม</td> 
                                                        @elseif ($item->months == '11')
                                                            <td class="text-start" width="15%">พฤษจิกายน</td> 
                                                        @else
                                                            <td class="text-start" width="15%">ธันวาคม</td> 
                                                        @endif
                                                        <td class="text-center text-primary" width="20%"> 
                                                            {{ $item->countvn }}  
                                                        </td>
                                                        <td class="text-center" width="20%" style="color:rgb(24, 103, 223)">{{ number_format($item->sum_total, 2) }} ฿</td> 
                                                        @foreach ($no_app as $item_sub)
                                                                @if ($item_sub->sum_totalnopdx > 0)
                                                                    <td class="text-center" width="20%" style="color:rgb(252, 73, 42)">
                                                                        <a class="btn-icon btn-sm btn-shadow btn-dashed btn btn-outline-danger" href="{{ url('audit_pdx_walkindetail/' . $item->months . '/' . $item->years) }}" target="_blank">
                                                                            {{ number_format($item_sub->sum_totalnopdx, 2) }}
                                                                        </a> 
                                                                    </td> 
                                                                    <td class="text-center" width="15%" style="color:rgb(168, 22, 83)">{{ $item_sub->countvn_no}}</td> 
                                                                @else
                                                                    <td class="text-center" width="20%">
                                                                        <a class="btn-icon btn-sm btn-shadow btn-dashed btn btn-outline-success">
                                                                            ซู๊ดดดดยอดอิหลี
                                                                        </a> 
                                                                    </td> 
                                                                    <td class="text-center" width="15%" style="color:rgb(168, 22, 83)">{{ $item_sub->countvn_no}}</td> 
                                                                @endif
                                                                
                                                        @endforeach
                                                        
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
                <div class="col-xl-6">
                    <div class="card card_audit_4">
                        <div class="card-body">
                            <h4 class="card-title ms-2" style="color:rgb(241, 137, 155)">รายการที่ไม่ลง DIAG เดือนนี้</h4>  
                                <div class="table-responsive">    
                                    <table id="example2" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">                       
                                        <thead>
                                            <tr>
                                                <th class="text-center">ลำดับ</th>
                                                <th class="text-center">hn</th> 
                                                <th class="text-center">vstdate</th>
                                                <th class="text-center">ptname</th> 
                                                <th class="text-center">Authen Code</th>
                                                <th class="text-center">pdx</th>  
                                                <th class="text-center">income</th>   
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $jj = 1; ?>
                                            @foreach ($walkin_momth as $item_m)
                                            <?php  ?>
                                            <tr>
                                                <td class="text-center" style="width: 5%">{{ $jj++ }}</td>
                                                <td class="text-center" width="10%">{{ $item_m->hn }} </td> 
                                                <td class="text-center" width="10%">{{ $item_m->vstdate }} </td>
                                                <td class="p-2">{{ $item_m->ptname }} </td>
                                                <td class="text-center" width="10%">{{ $item_m->claim_code }} </td>
                                                <td class="text-center" width="10%">{{ $item_m->pdx }} </td>
                                                {{-- <td class="text-center" style="width: 5%">{{ $item_m->error_c }}</td> --}}
                                                <td class="text-center" width="10%">{{ $item_m->income }} </td> 
                                            </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                        </div>
                    </div>
                </div> 
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card card_audit_4">
                        <div class="card-body">
                            <h4 class="card-title ms-2" style="color:rgb(241, 137, 155)">รายการที่ไม่ลง DIAG วันนี้ทั้งหมด</h4>  
                                <div class="table-responsive">                           
                                    <table id="example3" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th class="text-center">ลำดับ</th>
                                                <th class="text-center">hn</th> 
                                                <th class="text-center">cid</th> 
                                                <th class="text-center">vstdate</th>
                                                <th class="text-center">ptname</th> 
                                                <th class="text-center">Authen Code</th>
                                                <th class="text-center">pdx</th>  
                                                <th class="text-center">income</th>   
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $jd = 1; ?>
                                            @foreach ($walkin_today as $item_d) 
                                            <tr>
                                                <td class="text-center" style="width: 5%">{{ $jd++ }}</td>
                                                <td class="text-center" width="10%">{{ $item_d->hn }} </td> 
                                                <td class="text-center" width="10%">{{ $item_d->cid }} </td> 
                                                <td class="text-center" width="10%">{{ $item_d->vstdate }} </td>
                                                <td class="p-2">{{ $item_d->ptname }} </td>
                                                <td class="text-center" width="10%">{{ $item_d->claim_code }} </td>
                                                <td class="text-center" width="10%">{{ $item_d->pdx }} </td> 
                                                <td class="text-center" width="10%">{{ $item_d->income }} </td> 
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
                "pageLength": 100,
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

            $('.Process_A').click(function() {
                var startdate = $('#datepicker').val();
                var enddate = $('#datepicker2').val();
                Swal.fire({
                    title: 'ต้องการประมวลผลข้อมูลใช่ไหม ?',
                    text: "You Warn Process Data!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Process it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#overlay").fadeIn(300);
                        $("#spinner").show(); //Load button clicked show spinner 

                        $.ajax({
                            url: "{{ route('audit.pre_audit_process_a') }}",
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
                                            $('#spinner')
                                        .hide(); //Request is complete so hide spinner
                                            setTimeout(function() {
                                                $("#overlay").fadeOut(
                                                    300);
                                            }, 500);
                                        }
                                    })
                                } else {
                                   
                                    Swal.fire({
                                        position: "top-end",
                                        icon: "warning",
                                        title: "ยังไม่ได้เลือกวันที่",
                                        showCancelButton: false,
                                        confirmButtonColor: '#ed8d29',
                                        confirmButtonText: 'เลือกใหม่'
                                        // timer: 1500
                                    }).then((result) => {
                                        if (result
                                            .isConfirmed) {
                                            window.location.reload();
                                        }
                                    })

                                }
                            },
                        });

                    }
                })
            });

           


        });
    </script>
@endsection
