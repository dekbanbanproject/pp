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
    $datenow = date('Y-m-d');
    $ynow = date('Y') + 543;
    $yb = date('Y') + 542;
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

        <form action="{{ URL('account_201_detaildate') }}" method="GET">
            @csrf
            <div class="row">
                <div class="col-md-3 ">

                    <h4 class="card-title" style="color:rgb(247, 31, 95)">Search Detail 1102050101.201</h4>
                    <p class="card-title-desc">ค้าหาลูกหนี้ / ส่งลูกหนี้ ผัง 1102050101.201</p>
                </div>
                <div class="col"></div>
                <div class="col-md-1 text-end mt-2">วันที่</div>
                <div class="col-md-4 text-end">
                    <div class="input-daterange input-group" id="datepicker1" data-date-format="dd M, yyyy" data-date-autoclose="true" data-provide="datepicker" data-date-container='#datepicker6'>
                        <input type="text" class="form-control-sm cardacc" name="startdate" id="datepicker" placeholder="Start Date"
                            data-date-container='#datepicker1' data-provide="datepicker" data-date-autoclose="true" autocomplete="off"
                            data-date-language="th-th" value="{{ $startdate }}" required/>
                        <input type="text" class="form-control-sm cardacc" name="enddate" placeholder="End Date" id="datepicker2"
                            data-date-container='#datepicker1' data-provide="datepicker" data-date-autoclose="true" autocomplete="off"
                            data-date-language="th-th" value="{{ $enddate }}" required/>
                            {{-- <button type="submit" class="ladda-button me-2 btn-pill btn btn-sm btn-primary cardacc" data-style="expand-left">
                                <span class="ladda-label"> <i class="fa-solid fa-magnifying-glass text-white me-2"></i>ค้นหา</span>
                                <span class="ladda-spinner"></span>
                            </button> --}}
                            <button type="submit" class="ladda-button btn-pill btn btn-sm btn-info cardacc">
                                <span class="ladda-label">
                                    <img src="{{ asset('images/Search02.png') }}" class="me-2 ms-2" height="18px" width="18px">
                                    ค้นหา</span>
                            </button>
                        </form>
                            <button type="button" class="ladda-button me-2 btn-pill btn btn-primary btn-sm input_new Sendtamp" data-url="{{url('account_201_send')}}">
                                <img src="{{ asset('images/send_data.png') }}" class="me-2 ms-2" height="18px" width="18px">
                                ส่งลูกหนี้บัญชี
                            </button>
                </div>
                </div>

            </div>
   

        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card card_audit_4c" style="background-color: rgb(246, 235, 247)">

                    <div class="card-body">
                        <div class="table-responsive">
                        {{-- <table id="example" class="table table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;"> --}}
                            <table id="datatable-buttons" class="table table-sm table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> 
                                <thead>
                                    <tr>
                                        <th class="text-center">ลำดับ</th>
                                        <th width="5%" class="text-center"><input type="checkbox" class="dcheckbox_" name="stamp" id="stamp"> </th>
                                        <th class="text-center">ส่งลูกหนี้</th>
                                        <th class="text-center" width="5%">vn</th>
                                        {{-- <th class="text-center">an</th> --}}
                                        <th class="text-center" >hn</th>
                                        <th class="text-center" >cid</th>
                                        <th class="text-center">ptname</th>
                                        {{-- <th class="text-center">Adjrw</th>  --}}
                                        {{-- <th class="text-center">Adjrw*8350</th> --}}
                                        <th class="text-center">vstdate</th>
                                        <th class="text-center">vsttime</th>
                                        {{-- <th class="text-center">dchdate</th> --}}
                                        <th class="text-center">pttype</th>
                                        <th class="text-center">ลูกหนี้</th>
                                        <th class="text-center">ucep</th>
                                        <th class="text-center">ins</th>
                                        <th class="text-center">drug</th>
                                        <th class="text-center">เลนส์</th>
                                        <th class="text-center">refer</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $number = 0;$total1 = 0; $total2 = 0; $total3 = 0; $total4 = 0; $total5 = 0; $total6 = 0;$total7 = 0; ?>
                                    @foreach ($data as $item)
                                        <?php $number++; ?>
                                        <tr height="20">
                                            <td class="text-font" style="text-align: center;" width="5%">{{ $number }}</td>
                                            <td class="text-center" width="5%"><input type="checkbox" class="dcheckbox_ sub_chk" data-id="{{$item->acc_1102050101_201_id}}"> </td>
                                            <td class="text-center" width="5%">
                                                @if ($item->sendactive =='N') 
                                                    <img src="{{ asset('images/Cancel_new2.png') }}" height="23px" width="23px">
                                                @else
                                                <img src="{{ asset('images/check_trueinfo3.png') }}" height="23px" width="23px"> 
                                                @endif
                                            </td>
                                            <td class="text-center" width="10%">{{ $item->vn }}</td>
                                                    {{-- <td class="text-center" width="10%">{{ $item->an }}</td> --}}
                                                    <td class="text-center" width="5%">
                                                        {{ $item->hn }}
                                                        {{-- <button type="button" class="btn btn-icon btn-shadow btn-dashed btn-outline-primary" data-bs-toggle="modal" data-bs-target="#DetailModal{{ $item->an }}" data-bs-placement="right" title="ค่าใช้จ่าย"> {{ $item->hn }}</button> --}}
                                                    </td>
                                                    <td class="text-center" width="10%">{{ $item->cid }}</td>
                                                    <td class="p-2" >{{ $item->ptname }}</td>
                                                    {{-- <td class="text-center" width="7%">{{ $item->adjrw }}</td> --}}
                                                    {{-- <td class="text-center" width="7%">{{ $item->total_adjrw_income }}</td> --}}
                                                    <td class="text-center" width="7%">{{ $item->vstdate }}</td>
                                                    <td class="text-center" width="7%">{{ $item->vsttime }}</td>
                                                    {{-- <td class="text-center" width="7%">{{ $item->dchdate }}</td> --}}
                                                    <td class="text-center" style="color:rgb(73, 147, 231)" width="5%">{{ $item->pttype }}</td>
                                                    <td class="text-center" width="10%"> {{ number_format($item->debit_total,2)}}  </td>
                                                    <td class="text-center" width="7%">{{ number_format($item->debit_ucep, 2) }}</td>
                                                    <td class="text-center" width="5%">{{ number_format($item->debit_instument, 2) }}</td>
                                                    <td class="text-center" width="5%">{{ number_format($item->debit_drug, 2) }}</td>
                                                    <td class="text-center" width="5%">{{ number_format($item->debit_toa, 2) }}</td>
                                                    <td class="text-center" width="5%">{{ number_format($item->debit_refer, 2) }}</td>
                                        </tr>
                                        <?php
                                                    $total1 = $total1 + $item->debit_total;
                                                    $total2 = $total2 + $item->debit_ucep;
                                                    $total3 = $total3 + $item->debit_instument;
                                                    $total4 = $total4 + $item->debit_drug;
                                                    $total5 = $total5 + $item->debit_toa;
                                                    $total6 = $total6 + $item->debit_refer;
                                                    $total7 = $total7 + $item->income;
                                            ?>
                                    @endforeach

                                </tbody>
                                <tr style="background-color: #f3fca1">
                                    <td colspan="10" class="text-center" style="background-color: #fca1a1"></td>
                                    <td class="text-center" style="background-color: #47A4FA"><label for="" style="color: #099ea8">$ {{ number_format($total1, 2) }}</label></td>
                                    <td class="text-center" style="background-color: #197cd8"><label for="" style="color: #099ea8">$ {{ number_format($total2, 2) }}</label></td>
                                    <td class="text-center" style="background-color: #11cea5"><label for="" style="color: #099ea8">$ {{ number_format($total3, 2) }}</label></td>
                                    <td class="text-center" style="background-color: #9d69fc"><label for="" style="color: #099ea8">$ {{ number_format($total4, 2) }}</label></td>
                                    <td class="text-center" style="background-color: #87e211"><label for="" style="color: #099ea8">$ {{ number_format($total5, 2) }}</label></td>
                                    <td class="text-center" style="background-color: #e09f12"><label for="" style="color: #099ea8">$ {{ number_format($total6, 2) }}</label></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!--  Modal content Updte -->
    <div class="modal fade" id="updteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="invenModalLabel">ตัด STM พรบ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input id="editacc_1102050102_602_id" type="hidden" class="form-control form-control-sm">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="">vn</label>
                            <div class="form-group">
                                <input id="editvn" type="text" class="form-control form-control-sm" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="">hn</label>
                            <div class="form-group">
                                <input id="edithn" type="text" class="form-control form-control-sm" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="">cid</label>
                            <div class="form-group">
                                <input id="editcid" type="text" class="form-control form-control-sm" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="">ptname</label>
                            <div class="form-group">
                                <input id="editptname" type="text" class="form-control form-control-sm" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-3">
                            <label for="" style="color: red">รับแจ้ง</label>
                            <div class="form-group">
                                <input id="req_no" type="text" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="" style="color: red">เคลม</label>
                            <div class="form-group">
                                <input id="claim_no" type="text" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="">บริษัทประกันภัย</label>
                            <div class="form-group">
                                <input id="vendor" type="text" class="form-control form-control-sm">
                            </div>
                        </div>

                    </div>

                    <div class="row mt-2">
                        <div class="col-md-3">
                            <label for="" style="color: red">เลขที่ใบเสร็จรับเงิน</label>
                            <div class="form-group">
                                <input id="money_billno" type="text" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="">ประเภทการจ่าย</label>
                            <div class="form-group">
                                <input id="paytype" type="text" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{-- <label for="">ผู้ประสบภัย</label>
                            <div class="form-group">
                                <input id="ptname" type="text" class="form-control form-control-sm" >
                            </div> --}}
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-3">
                            <label for="">ครั้งที่</label>
                            <div class="form-group">
                                <input id="no" type="text" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="" style="color: red">จำนวนเงิน</label>
                            <div class="form-group">
                                <input id="payprice" type="text" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="" style="color: red">วันที่จ่าย</label>
                            <div class="form-group">
                                <input id="paydate" type="date" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="" style="color: red">วันที่บันทึก</label>
                            <div class="form-group">
                                <input id="savedate" type="date" class="form-control form-control-sm"
                                    value="{{ $datenow }}">
                            </div>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <div class="col-md-12 text-end">
                        <div class="form-group">
                            <button type="button" id="updateBtn" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-floppy-disk me-2"></i>
                                บันทึกข้อมูล
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"><i
                                    class="fa-solid fa-xmark me-2"></i>Close</button>

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
            $('.Sendtamp').on('click', function(e) {
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
                        text: "คุณต้องการส่งลูกหนี้รายการนี้ใช่ไหม!",
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
                                                        title: 'ส่งลูกหนี้สำเร็จ',
                                                        text: "You Send Debtor data success",
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
