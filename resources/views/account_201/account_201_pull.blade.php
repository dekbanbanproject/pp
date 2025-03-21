@extends('layouts.accountpk')
@section('title', 'PK-OFFICE || ACCOUNT')

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
        #button{
               display:block;
               margin:20px auto;
               padding:30px 30px;
               background-color:#eee;
               border:solid #ccc 1px;
               cursor: pointer;
               }
               #overlay{
               position: fixed;
               top: 0;
               z-index: 100;
               width: 100%;
               height:100%;
               display: none;
               background: rgba(0,0,0,0.6);
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
               .is-hide{
               display:none;
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
        <form action="{{ URL('account_201_pull') }}" method="GET" >
            @csrf
        <div class="row">
            <div class="col-md-4">
                <h5 class="card-title" style="color:rgb(248, 28, 83)">Detail 1102050101.201</h5>
                <p class="card-title-desc">รายละเอียดข้อมูล ผัง 1102050101.201</p>
            </div>
            <div class="col"></div>
            <div class="col-md-1 text-end mt-2">วันที่</div>
            <div class="col-md-4 text-end">
                <div class="input-daterange input-group" id="datepicker1" data-date-format="dd M, yyyy" data-date-autoclose="true" data-provide="datepicker" data-date-container='#datepicker1'>
                    <input type="text" class="form-control-sm cardacc" name="startdate" id="datepicker" placeholder="Start Date" data-date-container='#datepicker1' data-provide="datepicker" data-date-autoclose="true" autocomplete="off"
                        data-date-language="th-th" value="{{ $startdate }}" required/>
                    <input type="text" class="form-control-sm cardacc" name="enddate" placeholder="End Date" id="datepicker2" data-date-container='#datepicker1' data-provide="datepicker" data-date-autoclose="true" autocomplete="off"
                        data-date-language="th-th" value="{{ $enddate }}"/>
                        <button type="submit" class="ladda-button btn-pill btn btn-sm btn-info cardacc" data-style="expand-left">
                            <span class="ladda-label"><i class="fa-solid fa-magnifying-glass text-white me-2"></i>ค้นหา</span>
                            <span class="ladda-spinner"></span>
                        </button>
                    </form>
                        <button type="button" class="ladda-button me-2 btn-pill btn btn-sm btn-primary cardacc" data-style="expand-left" id="Pulldata">
                            <span class="ladda-label"> <i class="fa-solid fa-file-circle-plus text-white me-2"></i>ดึงข้อมูล</span>
                            <span class="ladda-spinner"></span>
                        </button>
                {{-- <button type="button" class="btn-icon btn-shadow btn-dashed btn btn-outline-primary" id="Pulldata">
                    <i class="fa-solid fa-file-circle-plus text-primary me-2"></i>
                    ดึงข้อมูล
                </button>     --}}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card card_audit_4c" style="background-color: rgb(246, 235, 247)">
                    <div class="card-body">
                        <div class="row mb-2">
                            {{-- <div class="col-md-4">
                                <h5 class="card-title">Detail 1102050101.201</h5>
                                <p class="card-title-desc">รายละเอียดข้อมูล ผัง 1102050101.201</p>
                            </div> --}}
                            <div class="col"></div>
                            <div class="col-md-3 text-end">
                                {{-- <button type="button" class="ladda-button me-2 btn-pill btn btn-sm btn-info cardacc" id="Check_sitipd">
                                    <i class="fa-solid fa-user me-2"></i>
                                    ตรวจสอบสิทธิ์
                                </button> --}}

                                <button type="button" class="ladda-button me-2 btn-pill btn btn-info btn-sm input_new Check_sit" data-url="{{url('account_201_checksit')}}">
                                    <img src="{{ asset('images/Check_sitwhite.png') }}" class="me-2 ms-2" height="18px" width="18px">
                                    ตรวจสอบสิทธิ์
                                </button>

                                <button type="button" class="ladda-button me-2 btn-pill btn btn-sm btn-primary cardacc Savestamp" data-url="{{url('account_201_stam')}}">
                                    <i class="fa-solid fa-file-waveform me-2"></i>
                                    ตั้งลูกหนี้
                                </button>
                                <button type="button" class="ladda-button me-2 btn-pill btn btn-sm btn-danger cardacc Destroystamp" data-url="{{url('account_201_destroy')}}">
                                    <i class="fa-solid fa-trash-can me-2"></i>
                                    ลบ
                                </button>
                            </div>
                        </div>

                        <p class="mb-0">
                            <div class="table-responsive">
                                <table id="example" class="table table-sm table-hover table-striped table-bordered dt-responsive nowrap" style=" border-spacing: 0; width: 100%;">
                                    <thead>
                                        <tr>

                                            <th width="5%" class="text-center">ลำดับ</th>
                                            <th width="5%" class="text-center"><input type="checkbox" class="dcheckbox_" name="stamp" id="stamp"> </th>
                                            <th class="text-center" width="5%">stamp</th>
                                            <th class="text-center" width="5%">vn</th>
                                            <th class="text-center" >hn</th>
                                            <th class="text-center" >cid</th>
                                            <th class="text-center">ptname</th>
                                            <th class="text-center">vstdate</th>
                                            <th class="text-center">pttype</th>
                                            <th class="text-center">spsch</th>
                                            <th class="text-center">income</th>
                                            <th class="text-center">ชำระเงิน</th>
                                            <th class="text-center">ลูกหนี้</th>
                                            <th class="text-center">imc</th>
                                            <th class="text-center">ucep</th>
                                            <th class="text-center">ins</th>
                                            <th class="text-center">drug</th>
                                            {{-- <th class="text-center">เลนส์</th> --}}
                                            <th class="text-center">refer</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1;$total1 = 0; $total2 = 0; $total3 = 0; $total4 = 0; $total5 = 0; $total6 = 0;$total7 = 0;$total8 = 0;$total9 = 0; ?>
                                        @foreach ($acc_debtor as $item)
                                            <tr id="tr_{{$item->acc_debtor_id}}">
                                                <td class="text-center" width="5%">{{ $i++ }}</td>
                                                @if ($item->debit_total == '')
                                                    <td class="text-center" width="5%">
                                                        <input class="form-check-input" type="checkbox" id="flexCheckDisabled" disabled>
                                                    </td>
                                                @else
                                                    <td class="text-center" width="5%"><input type="checkbox" class="dcheckbox_ sub_chk" data-id="{{$item->acc_debtor_id}}"> </td>
                                                @endif
                                                {{-- <td class="text-center" width="5%"><input type="checkbox" class="sub_chk dcheckbox" data-id="{{$item->acc_debtor_id}}"> </td>  --}}
                                                {{-- <td class="text-center" width="5%">{{ $item->stamp }}</td> --}}

                                                <td class="text-start" width="5%">
                                                    @if ($item->stamp =='N')
                                                        <img src="{{ asset('images/Cancel_new2.png') }}" height="23px" width="23px">
                                                      
                                                    @else
                                                    
                                                    <img src="{{ asset('images/check_trueinfo3.png') }}" height="23px" width="23px">
                                                    @endif
                                                </td>

                                                <td class="text-center" width="5%">{{ $item->vn }}</td>
                                                <td class="text-center" width="5%">{{ $item->hn }}</td>
                                                <td class="text-center" width="10%">{{ $item->cid }}</td>
                                                <td class="text-start" >{{ $item->ptname }}</td>
                                                <td class="text-center" width="10%">{{ $item->vstdate }}</td>

                                                <td class="text-center" style="color:rgb(73, 147, 231)" width="5%">{{ $item->pttype }}</td>
                                                <td class="text-center" style="color:rgb(216, 95, 14)" width="5%">{{ $item->subinscl }}</td>

                                                <td class="text-center" width="10%">{{ number_format($item->income, 2) }}</td>
                                                <td class="text-center" width="10%">{{ number_format($item->rcpt_money, 2) }}</td>
                                                <td class="text-center" width="10%">{{ number_format($item->debit_total, 2) }}</td>
                                                <td class="text-center" width="5%">{{ number_format($item->debit_imc, 2) }}</td>
                                                <td class="text-center" width="5%">{{ number_format($item->debit_ucep, 2) }}</td>
                                                <td class="text-center" width="5%">{{ number_format($item->debit_instument, 2) }}</td>
                                                <td class="text-center" width="5%">{{ number_format($item->debit_drug, 2) }}</td>
                                                {{-- <td class="text-center" width="5%">{{ number_format($item->debit_toa, 2) }}</td> --}}
                                                <td class="text-center" width="5%">{{ number_format($item->debit_refer, 2) }}</td>

                                            </tr>
                                            <?php
                                                    $total1 = $total1 + $item->income;
                                                    $total2 = $total2 + $item->debit_total;
                                                    $total3 = $total3 + $item->debit_imc;
                                                    $total4 = $total4 + $item->debit_ucep;
                                                    $total5 = $total5 + $item->debit_instument;
                                                    $total6 = $total6 + $item->debit_drug;
                                                    // $total7 = $total7 + $item->debit_toa;
                                                    $total8 = $total8 + $item->debit_refer;
                                                    $total9 = $total9 + $item->rcpt_money;
                                            ?>
                                        @endforeach
                                    </tbody>
                                    <tr style="background-color: #f3fca1">
                                        <td colspan="10" class="text-end" style="background-color: #fca1a1"></td>
                                        <td class="text-center" style="background-color: #0eccda"><label for="" style="color: #069b8e">{{ number_format($total1, 2) }}</label></td>
                                        <td class="text-center" style="background-color: #0eccda"><label for="" style="color: #069b8e">{{ number_format($total9, 2) }}</label></td>
                                        <td class="text-center" style="background-color: #47A4FA"><label for="" style="color: #069b8e">{{ number_format($total2, 2) }}</label></td>
                                        <td class="text-center" style="background-color: #197cd8"><label for="" style="color: #069b8e">{{ number_format($total3, 2) }}</label></td>
                                        <td class="text-center" style="background-color: #11cea5"><label for="" style="color: #069b8e">{{ number_format($total4, 2) }}</label></td>
                                        <td class="text-center" style="background-color: #9d69fc"><label for="" style="color: #069b8e">{{ number_format($total5, 2) }}</label></td>
                                        <td class="text-center" style="background-color: #87e211"><label for="" style="color: #069b8e">{{ number_format($total6, 2) }}</label></td>
                                        {{-- <td class="text-center" style="background-color: #e09f12"><label for="" style="color: #069b8e">{{ number_format($total7, 2) }}</label></td> --}}
                                        <td class="text-center" style="background-color: #e09f12"><label for="" style="color: #069b8e">{{ number_format($total8, 2) }}</label></td>
                                    </tr>
                                </table>
                            </div>
                        </p>
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
                "lengthMenu": [10,25,50,100,150,200,300,400,500],
        });
            $('#example').DataTable();
            $('#example2').DataTable();
            $('#datepicker').datepicker({
                format: 'yyyy-mm-dd'
            });
            $('#datepicker2').datepicker({
                format: 'yyyy-mm-dd'
            });
            $('#stamp').on('click', function(e) {
            if($(this).is(':checked',true))
            {
                $(".sub_chk").prop('checked', true);
            } else {
                $(".sub_chk").prop('checked',false);
            }
            });

            $('.Savestamp').on('click', function(e) {
                // alert('oo');
                var allValls = [];
                $(".sub_chk:checked").each(function () {
                    allValls.push($(this).attr('data-id'));
                });
                if (allValls.length <= 0) {
                    // alert("SSSS");
                    Swal.fire({
                        title: 'คุณยังไม่ได้เลือกรายการ ?',
                        text: "กรุณาเลือกรายการก่อน",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        }).then((result) => {

                        })
                } else {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "คุณต้องการตั้งลูกหนี้รายการนี้ใช่ไหม!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Debtor it.!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                var check = true;
                                if (check == true) {
                                    var join_selected_values = allValls.join(",");
                                    // alert(join_selected_values);
                                    $("#overlay").fadeIn(300);　
                                    $("#spinner").show(); //Load button clicked show spinner

                                    $.ajax({
                                        url:$(this).data('url'),
                                        type: 'POST',
                                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                        data: 'ids='+join_selected_values,
                                        success:function(data){
                                                if (data.status == 200) {
                                                    $(".sub_chk:checked").each(function () {
                                                        $(this).parents("tr").remove();
                                                    });
                                                    Swal.fire({
                                                        title: 'ตั้งลูกหนี้สำเร็จ',
                                                        text: "You Debtor data success",
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


                                            // } else {
                                            //     alert("Whoops Something went worng all");
                                            // }
                                        }
                                    });
                                    $.each(allValls,function (index,value) {
                                        $('table tr').filter("[data-row-id='"+value+"']").remove();
                                    });
                                }
                            }
                        })
                    // var check = confirm("Are you want ?");
                }
            });


            $("#spinner-div").hide(); //Request is complete so hide spinner


            $('#Pulldata').click(function() {
                var datepicker = $('#datepicker').val();
                var datepicker2 = $('#datepicker2').val();
                Swal.fire({
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
                                    url: "{{ route('acc.account_201_pulldata') }}",
                                    type: "POST",
                                    dataType: 'json',
                                    data: {datepicker,datepicker2},
                                    success: function(data) {
                                        if (data.status == 200) {
                                            Swal.fire({
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

            $('#Check_sitipd').click(function() {
                var datepicker = $('#datepicker').val();
                var datepicker2 = $('#datepicker2').val();
                //    alert(datepicker);
                Swal.fire({
                        title: 'ต้องการตรวจสอบสอทธิ์ใช่ไหม ?',
                        text: "You Check Sit Data!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, pull it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $("#overlay").fadeIn(300);　
                                $("#spinner-div").show(); //Load button clicked show spinner
                            $.ajax({
                                url: "{{ route('acc.account_201_checksit') }}",
                                type: "POST",
                                dataType: 'json',
                                data: {
                                    datepicker,
                                    datepicker2
                                },
                                success: function(data) {
                                    if (data.status == 200) {
                                        Swal.fire({
                                            title: 'เช็คสิทธิ์สำเร็จ',
                                            text: "You Check sit success",
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
                                                $('#spinner-div').hide();//Request is complete so hide spinner
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

            $('.Check_sit').click(function() {
                var allValls = [];
                $(".sub_chk:checked").each(function () {
                    allValls.push($(this).attr('data-id'));
                });
                if (allValls.length <= 0) {
                    // alert("SSSS");
                    Swal.fire({
                        title: 'คุณยังไม่ได้เลือกรายการ ?',
                        text: "กรุณาเลือกรายการก่อน",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        }).then((result) => {

                        })
                } else {

                    Swal.fire({
                        position: "top-end",
                        title: 'Are you sure?',
                        text: "ต้องการตรวจสอบสอทธิ์ใช่ไหม!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'You Check Sit Data!.!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                var check = true;
                                if (check == true) {
                                    var join_selected_values = allValls.join(",");
                                    // alert(join_selected_values);
                                    $("#overlay").fadeIn(300);　
                                    $("#spinner").show(); //Load button clicked show spinner

                                    $.ajax({
                                        url:$(this).data('url'),
                                        type: 'POST',
                                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                        data: 'ids='+join_selected_values,
                                        success:function(data){
                                                if (data.status == 200) {
                                                    $(".sub_chk:checked").each(function () {
                                                        $(this).parents("tr").remove();
                                                    });
                                                    Swal.fire({
                                                        position: "top-end",
                                                        title: 'เช็คสิทธิ์สำเร็จ',
                                                        text: "You Check sit success",
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

                                        }
                                    });
                                    $.each(allValls,function (index,value) {
                                        $('table tr').filter("[data-row-id='"+value+"']").remove();
                                    });
                                }
                            }
                        })


                    }
            });

            $('.Destroystamp').on('click', function(e) {
                // alert('oo');
                var allValls = [];
                $(".sub_chk:checked").each(function () {
                    allValls.push($(this).attr('data-id'));
                });
                if (allValls.length <= 0) {
                    // alert("SSSS");
                    Swal.fire({
                        title: 'คุณยังไม่ได้เลือกรายการ ?',
                        text: "กรุณาเลือกรายการก่อน",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        }).then((result) => {

                        })
                } else {
                    Swal.fire({
                        title: 'Are you Want Delete sure?',
                        text: "คุณต้องการลบรายการนี้ใช่ไหม!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Delete it.!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                var check = true;
                                if (check == true) {
                                    var join_selected_values = allValls.join(",");
                                    // alert(join_selected_values);
                                    $("#overlay").fadeIn(300);　
                                    $("#spinner").show(); //Load button clicked show spinner

                                    $.ajax({
                                        url:$(this).data('url'),
                                        type: 'POST',
                                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                        data: 'ids='+join_selected_values,
                                        success:function(data){
                                                if (data.status == 200) {
                                                    $(".sub_chk:checked").each(function () {
                                                        $(this).parents("tr").remove();
                                                    });
                                                    Swal.fire({
                                                        title: 'ลบข้อมูลสำเร็จ',
                                                        text: "You Delete data success",
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

                                        }
                                    });
                                    $.each(allValls,function (index,value) {
                                        $('table tr').filter("[data-row-id='"+value+"']").remove();
                                    });
                                }
                            }
                        })
                    // var check = confirm("Are you want ?");
                }
            });

        });
    </script>
    @endsection
