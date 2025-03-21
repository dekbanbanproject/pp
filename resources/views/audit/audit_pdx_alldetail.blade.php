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
        <form action="{{ URL('audit_pdx_alldetail/'.$month.'/'.$year) }}" method="GET">
            @csrf
            <input type="hidden" name="month" value="{{$month}}">
            <input type="hidden" name="year" value="{{$year}}">

            <div class="row">
                <div class="col-md-3">
                    <h4 class="card-title" style="color:rgb(250, 128, 124)">Detail Pre-Audit All</h4>
                    <p class="card-title-desc">รายละเอียดข้อมูล Pre-Audit ทุก Visit</p>
                </div> 
                <div class="col"></div>
                <div class="col-md-1 text-end mt-2">วันที่</div>
                <div class="col-md-4 text-end">
                    <div class="input-daterange input-group" id="datepicker1" data-date-format="dd M, yyyy" data-date-autoclose="true" data-provide="datepicker" data-date-container='#datepicker1'>
                        <input type="text" class="form-control-sm card_audit_4" name="startdate" id="datepicker" placeholder="Start Date" data-date-container='#datepicker1' data-provide="datepicker" data-date-autoclose="true" autocomplete="off"
                            data-date-language="th-th" value="{{ $startdate }}" required style="font-size: 13px;"/>
                        <input type="text" class="form-control-sm card_audit_4" name="enddate" placeholder="End Date" id="datepicker2" data-date-container='#datepicker1' data-provide="datepicker" data-date-autoclose="true" autocomplete="off"
                            data-date-language="th-th" value="{{ $enddate }}" style="font-size: 13px;"/>
                            <button type="submit" class="ladda-button btn-pill btn btn-sm btn-info card_audit_4" data-style="expand-left">
                                <span class="ladda-label">
                                    <img src="{{ asset('images/Search02.png') }}" class="me-2 ms-2" height="18px" width="18px">
                                    ค้นหา</span>
                            </button>
                        </form>
     
                    </div>
                </div>
            </div>

            <div class="row"> 
                <div class="col-xl-12">
                  
                    <div class="card card_audit_4" style="background-color: rgb(247, 246, 235)">
                        <div class="card-body">
                            <h4 class="card-title ms-2" style="color:rgb(241, 137, 155)">รายการที่ไม่ลง DIAG เดือนนี้</h4>  
                            <p class="mb-0">
                                <div class="table-responsive">    
                                    <table id="example" class="table table-sm table-striped table-sm" style="width: 100%;"> 
                                        <thead style="border: 1px solid rgb(250, 214, 159);">
                                            <tr style="font-size: 13px;">
                                                <th class="text-center" width="2%">ลำดับ</th>
                                                <th class="text-center" width="7%">vn</th> 
                                                <th class="text-center" width="7%">hn</th> 
                                                <th class="text-center" width="7%">cid</th> 
                                                <th class="text-center" width="7%">vstdate</th>
                                                <th class="text-center">ptname</th> 
                                                <th class="text-center" width="5%">pttype</th>
                                                <th class="text-center" width="5%">pdx</th>                                                 
                                                <th class="text-center" width="10%">income</th>  
                                                <th class="text-center" width="10%">Approvecode||Authen</th> 
                                                <th class="text-center">แผนก</th> 
                                                <th class="text-center">Staff</th> 
                                            </tr>
                                        </thead>
                                        <tbody style="border: 1px solid rgb(250, 214, 159)">
                                            <?php $jj = 1; ?>
                                            @foreach ($datashow_momth as $item_m)
                                            <?php  ?>
                                            <tr style="font-size: 12px;">
                                                <td class="text-center" style="width: 2%">{{ $jj++ }}</td>
                                                <td class="text-start" width="7%">{{ $item_m->vn }} </td> 
                                                <td class="text-start" width="7%">{{ $item_m->hn }} </td> 
                                                <td class="text-start" width="7%">{{ $item_m->cid }} </td> 
                                                <td class="text-start" width="7%">{{ $item_m->vstdate }} </td>
                                                <td class="text-start">{{ $item_m->ptname }} </td>
                                                <td class="text-center" width="5%">{{ $item_m->pttype }}</td>
                                                <td class="text-center" width="5%">
                                                    @if ($item_m->pdx == '')
                                                    <span class="badge" style="background-color: #ff7bb2">ว่าง</span>
                                                    @else
                                                    {{ $item_m->pdx }}
                                                    @endif
                                                </td>                                              
                                                <td class="text-center" width="5%">{{ number_format($item_m->income, 2) }} </td> 
                                                <td class="text-center" width="7%">
                                                    @if ($item_m->pttype == 'O1' || $item_m->pttype == 'O2' || $item_m->pttype == 'O3' || $item_m->pttype == 'O4' || $item_m->pttype == 'O5')
                                                        {{ $item_m->sss_approval_code }}
                                                    @else
                                                        {{ $item_m->auth_code }}
                                                    @endif
                                                    
                                                 </td> 
                                                 <td class="text-start" width="15%">{{ $item_m->department }} </td>
                                                 <td class="text-start" width="5%">{{ $item_m->staff }} </td>
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


@endsection
@section('footer')

    <script>
        $(document).ready(function() {
            // var table = $('#example').DataTable({
            //     scrollY: '60vh',
            //     scrollCollapse: true,
            //     scrollX: true,
            //     "autoWidth": false,
            //     "pageLength": 100,
            //     "lengthMenu": [10, 100, 150, 200, 300, 400, 500],
            // });
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
