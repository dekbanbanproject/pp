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

        {{-- <form action="{{ URL('account_totalrep_detail') }}" method="GET">
            @csrf --}}
        <div class="row">
            <div class="col-md-4">
                <h3 class="card-title" style="color:rgb(10, 168, 134)">Detail Send Data {{$pang}}</h3>
                <p class="card-title-desc">ลูกหนี้ที่ส่งมาให้บัญชีเรียบร้อยแล้ว</p>
            </div>
            <div class="col"></div>
            <div class="col-md-1 text-end mt-2">วันที่</div>
            <div class="col-md-4 text-end">
                <div class="input-daterange input-group" id="datepicker1" data-date-format="dd M, yyyy" data-date-autoclose="true" data-provide="datepicker" data-date-container='#datepicker1'>
                    <input type="text" class="form-control-sm cardacc" name="startdate" id="datepicker" placeholder="Start Date" data-date-container='#datepicker1' data-provide="datepicker" data-date-autoclose="true" autocomplete="off"
                        data-date-language="th-th" value="{{ $startdate }}" required/>
                    <input type="text" class="form-control-sm cardacc" name="enddate" placeholder="End Date" id="datepicker2" data-date-container='#datepicker1' data-provide="datepicker" data-date-autoclose="true" autocomplete="off"
                        data-date-language="th-th" value="{{ $enddate }}"/>
                        <button type="submit" class="ladda-button btn-pill btn btn-sm btn-info cardacc">
                            <span class="ladda-label">
                                <img src="{{ asset('images/Search02.png') }}" class="me-2 ms-2" height="18px" width="18px">
                                ค้นหา</span>
                        </button>
                    {{-- </form> --}}

            </div>
        </div>
    </div>

        <div class="row ">
            <div class="col-md-12">
                <div class="card card_audit_4c" style="background-color: rgb(253, 249, 239)">

                    <div class="card-body">


                        
                        <div class="table-responsive">

                        <table id="example" class="table table-sm table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            {{-- <table id="example" class="table table-striped table-bordered "
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;"> --}}
                            <thead style="border: 1px solid rgb(250, 214, 159);">
                                <tr>
                                    <th class="text-center">ลำดับ</th> 
                                    <th class="text-center" >vn</th>
                                    <th class="text-center" >hn</th>
                                    <th class="text-center" >cid</th>
                                    <th class="text-center">ptname</th>
                                    <th class="text-center">vstdate</th>
                                    <th class="text-center">vsttime</th>
                                    <th class="text-center">pttype</th>
                                    <th class="text-center">ลูกหนี้</th>
                                    <th class="text-center">ส่วนต่าง</th> 
                                    <th class="text-center">Stm</th>
                                    <th class="text-center">STMdoc</th>
                                </tr>
                            </thead>
                            <tbody style="border: 1px solid rgb(250, 214, 159);">
                                <?php $number = 0;
                                $total1 = 0; $total2 = 0;$total3 = 0;$total4 = 0;$total5 = 0;$total6 = 0;$total7 = 0;$total8 = 0;$total9 = 0;
                                ?>
                                @foreach ($datashow as $item)
                                    <?php $number++; ?>
                                    <tr height="20" style="font-size: 14px;">
                                        <td class="text-font" style="text-align: center;" width="3%">{{ $number }}</td>
 
                                        <td class="text-center" width="7%">{{ $item->vn }}</td>
                                        <td class="text-center" width="4%">{{ $item->hn }}</td>
                                        <td class="text-start" width="8%">{{ $item->cid }}</td>
                                        <td class="text-start">{{ $item->ptname }}</td>
                                        <td class="text-center" width="6%">{{ $item->vstdate }}</td>
                                        <td class="text-center" width="6%">{{ $item->vsttime }}</td>
                                        <td class="text-center" width="6%">{{ $item->pttype }}</td>
                                        <td class="text-center" style="color:rgb(27, 118, 223)" width="6%">{{ number_format($item->debit_total,2)}}</td>
                                        <td class="text-center" style="color:rgb(184, 12, 169)" width="6%">{{ number_format(($item->debit_total-$item->stm_money),2)}}</td>
                                        {{-- <td class="text-end" style="color:rgb(4, 82, 172)" width="6%">{{ number_format($item->rep_pay,2)}}</td> --}}
                                        <td class="text-center" style="color:rgb(4, 132, 141)" width="6%">{{ number_format($item->stm_money,2)}}</td>
                                        <td class="text-start" width="10%">
                                            @if ($item->STMDoc =='') 
                                            @else
                                            {{ $item->STMDoc }}
                                            @endif

                                        </td>

                                    </tr>
                                        <?php
                                            // $total1 = $total1 + $item->debit_drug;
                                            // $total2 = $total2 + $item->debit_instument;
                                            // $total3 = $total3 + $item->debit_toa;
                                            // $total4 = $total4 + $item->debit_refer;
                                            // $total5 = $total5 + $item->debit_ucep;

                                            $total6 = $total6 + $item->debit_total;
                                            $total7 = $total7 + ($item->debit_total-$item->stm_money);
                                            // $total8 = $total8 + $item->rep_pay;
                                            $total9 = $total9 + $item->stm_money;
                                        ?>
                                @endforeach

                            </tbody>
                                        <tr style="background-color: #f3fca1">
                                            <td colspan="8" class="text-center" style="background-color: #ff9d9d"></td>
                                            {{-- <td class="text-end" style="background-color: #f58d73;color: #000000">{{ number_format($total1,2)}}</td>
                                            <td class="text-end" style="background-color: #f58d73;color: #000000">{{ number_format($total2,2)}}</td>
                                            <td class="text-end" style="background-color: #f58d73;color: #000000">{{ number_format($total3,2)}}</td>
                                            <td class="text-end" style="background-color: #f58d73;color: #000000">{{ number_format($total4,2)}}</td> --}}
                                            {{-- <td class="text-end" style="background-color: #ace5fc">{{ number_format($total5,2)}}</td>  --}}
                                            <td class="text-center" style="background-color: #276ed8;color: #1da7e7">{{ number_format($total6,2)}}</td>
                                            <td class="text-center" style="background-color: #8c3ee4;color: #af25e6">{{ number_format($total7,2)}}</td>
                                            {{-- <td class="text-end" style="background-color: #059b75;color: #078d9e">{{ number_format($total8,2)}}</td> --}}
                                            <td class="text-center" style="background-color: #bbf0e3">{{ number_format($total9,2)}}</td>
                                            <td class="text-center" style="background-color: #ff9d9d"></td>
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
                    Swal.fire({ position: "top-end",
                        title: 'คุณยังไม่ได้เลือกรายการ ?',
                        text: "กรุณาเลือกรายการก่อน",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        }).then((result) => {

                        })
                } else {
                    Swal.fire({ position: "top-end",
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
                                                    Swal.fire({ position: "top-end",
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

            $("#spinner-div").hide(); //Request is complete so hide spinner

        });
    </script>
@endsection
