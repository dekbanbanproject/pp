@extends('layouts.accountpk')
@section('title', 'PK-OFFICE || ACCOUNT')
@section('content')
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
            border-top: 10px #1fdab1 solid;
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
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h5 class="card-title" style="color:rgb(248, 28, 83)">Detail STM 1102050101.309</h5>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Detail STM </a></li>
                                <li class="breadcrumb-item active">1102050101.309</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->
        </div> <!-- container-fluid -->

        <div class="row">
            <div class="col-xl-12">
                <div class="card cardacc">
                    {{-- <div class="card-header">
                        รายละเอียด 1102050101.309 STM
                        <div class="btn-actions-pane-right">
                            <button type="button" class="me-2 btn-icon btn-shadow btn-dashed btn btn-outline-danger PulldataAll" >
                                <i class="fa-solid fa-arrows-rotate text-danger me-2"></i>
                                Sync Data All
                            </button>
                        </div>
                    </div> --}}
                    <div class="card-body">


                        {{-- <table id="example" class="table table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;"> --}}
                            <div class="table-responsive">
                                {{-- <h4 class="card-title mb-2" style="color:rgb(10, 151, 85)">  รายละเอียด 1102050101.309 STM</h4> --}}
                                <table id="datatable-buttons" class="table table-sm table-striped table-bordered dt-responsive nowrap"
                                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center">ลำดับ</th>
                                            <th class="text-center">vn</th>
                                            <th class="text-center">hn</th>
                                            <th class="text-center">cid</th>
                                            <th class="text-center">ptname</th>
                                            <th class="text-center">vstdate</th>
                                            <th class="text-center">pttype</th>
                                            <th class="text-center">Sync Data / เลขหนังสือ </th>
                                            <th class="text-center">ลูกหนี้</th>
                                            <th class="text-center">เบิกจริง</th>
                                            <th class="text-center">รับชำระ</th>
                                            <th class="text-center">ส่วนต่าง</th>
                                            <th class="text-center">เลขที่ใบเสร็จ</th>
                                            <th class="text-center">วันที่รับใบเสร็จ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $number = 1;
                                        $total1 = 0;
                                        $total2 = 0;
                                        $total3 = 0;
                                        $total4 = 0;
                                        ?>
                                        @foreach ($data as $item)

                                            <tr height="20" style="font-size: 14px;">
                                                <td class="text-font" style="text-align: center;" width="4%">{{ $number++ }} </td>
                                                <td class="text-center" width="10%">{{ $item->vn }}</td>
                                                <td class="text-center" width="10%">{{ $item->hn }}</td>
                                                <td class="text-center" width="10%">{{ $item->cid }}</td>
                                                <td class="p-2">{{ $item->ptname }}</td>
                                                <td class="text-center" width="10%">{{ $item->vstdate }}</td>
                                                <td class="text-center" width="10%">{{ $item->pttype }}</td>
                                                <td class="text-center" width="5%">
                                                    <button type="button" class="me-2 btn-icon btn-sm btn-shadow btn-dashed btn btn-outline-success">
                                                        <i class="fa-solid fa-book-open text-success me-2"></i>
                                                        {{$item->nhso_docno}}
                                                    </button>
                                                </td>
                                                <td class="text-end" style="color:rgb(73, 147, 231)" width="7%"> {{ number_format($item->debit_total, 2) }}</td>
                                                <td class="text-end" style="color:rgb(243, 157, 27)" width="7%"> {{ $item->nhso_ownright_pid }}</td>
                                                <td class="text-end text-success"  width="7%" style="color:#44E952"> {{ $item->stm }}</td>
                                                <td class="text-end" style="color:rgb(231, 73, 134)" width="7%"> {{ $item->difference }}</td>

                                                    @if ($item->recieve_no =='')
                                                    <td class="text-center">{{ $item->stm_no }} </td>
                                                    <td class="text-center">{{ $item->date_save }}</td>
                                                    @else

                                                    <td class="text-center">{{ $item->recieve_no }} </td>
                                                    <td class="text-center">{{ $item->recieve_date }}</td>
                                                    @endif



                                            </tr>
                                            <?php
                                                    $total1 = $total1 + $item->debit_total;
                                                    $total2 = $total2 + $item->nhso_ownright_pid;
                                                    $total3 = $total3 + $item->stm;
                                                    $total4 = $total4 + $item->difference;
                                            ?>
                                        @endforeach

                                    </tbody>
                                    <tr style="background-color: #f3fca1">
                                        <td colspan="8" class="text-end" style="background-color: #fca1a1"></td>
                                        <td class="text-center" style="background-color: #47A4FA"><label for="" style="color: #f80f5d">{{ number_format($total1, 2) }}</label></td>
                                        <td class="text-center" style="background-color: #FCA533" ><label for="" style="color: #f80f5d">{{ number_format($total2, 2) }}</label></td>
                                        <td class="text-center" style="background-color: #44E952"><label for="" style="color: #f80f5d">{{ number_format($total3, 2) }}</label> </td>
                                        <td class="text-center" style="background-color: #FC7373"><label for="" style="color: #f80f5d">{{ number_format($total4, 2) }}</label></td>
                                        <td colspan="2" class="text-end" style="background-color: #fca1a1"></td>
                                    </tr>
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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
        $('.Pulldata').click(function() {
                var vn = $(this).val();
                // alert(vn);
                Swal.fire({
                        title: 'ต้องการซิ้งค์ข้อมูลใช่ไหม ?',
                        text: "You Sync Data!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Sync it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $("#overlay").fadeIn(300);　
                                $("#spinner").show();

                                $.ajax({
                                    url: "{{ url('account_307_sync') }}",
                                    type: "POST",
                                    dataType: 'json',
                                    data: {vn},
                                    success: function(data) {
                                        if (data.status == 200) {
                                            Swal.fire({
                                                title: 'ดึงข้อมูลสำเร็จ',
                                                text: "You Sync data success",
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

                                        } else if (data.status == 100) {
                                            Swal.fire({
                                                title: 'ยังไม่ได้ลงเลขที่หนังสือ',
                                                text: "Please enter the number of the book.",
                                                icon: 'warning',
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

                            }
                })
        });

        $('.PulldataAll').click(function() {
                var months = $('#months').val();
                var year = $('#year').val();
                // alert(startdate);
                Swal.fire({
                        title: 'ต้องการซิ้งค์ข้อมูลใช่ไหม ?',
                        text: "You Sync Data!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Sync it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $("#overlay").fadeIn(300);　
                                $("#spinner").show();

                                $.ajax({
                                    url: "{{ url('account_307_syncall') }}",
                                    type: "POST",
                                    dataType: 'json',
                                    data: {months,year},
                                    success: function(data) {
                                        if (data.status == 200) {
                                            Swal.fire({
                                                title: 'ซิ้งค์ข้อมูลสำเร็จ',
                                                text: "You Sync data success",
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

                                        } else if (data.status == 100) {
                                            Swal.fire({
                                                title: 'ยังไม่ได้ลงเลขที่หนังสือ',
                                                text: "Please enter the number of the book.",
                                                icon: 'warning',
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

                            }
                })
        });


    </script>
@endsection
