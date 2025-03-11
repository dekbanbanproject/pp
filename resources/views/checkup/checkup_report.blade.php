@extends('layouts.checkup')
@section('title', 'PK-OFFICE || ระบบตรวจสุขภาพ')

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
            border: 5px #ddd solid;
            border-top: 10px rgb(250, 128, 124) solid;
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
    </style>
    <?php
    use Illuminate\Support\Facades\DB;
    use App\Http\Controllers\SuppliesController;
    use App\Http\Controllers\StaticController;
    
    
    $refnumber = SuppliesController::refnumber();
    $count_product = StaticController::count_product();
    $count_service = StaticController::count_service();
    
    ?>



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

        <div class="row ">
            <div class="col-md-12">
                <div class="card card_prs_4" >
    
                    <div class="card-header">
                        <div class="d-flex">
                            <div class="">
                                <label for=""style="font-size: 15px">ข้อมูลคนไข้</label>
                            </div>
                            <div class="ms-auto">    
                            </div>
                        </div>
                    </div>                  
    
                    <div class="card-body shadow-lg">
                        <form action="{{ route('ch.checkup_report') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                            <input type="hidden" name="store_id" id="store_id" value=" {{ Auth::user()->store_id }}">
                            <div class="row">
    
                                <div class="col-md-12">
                                    <div class="row text-center">

                                        <div class="row mt-2">
                                            <div class="col-md-1 text-end">
                                                <label for="chackup_date" style="font-size: 15px">วันที่ :</label>
                                            </div>
                                            <div class="col-md-3">
                                                @if ($datepicker =='')
                                                    <div class="form-group">
                                                        <div class="input-daterange input-group" id="datepicker1" data-date-format="dd M, yyyy" data-date-autoclose="true" data-provide="datepicker" data-date-container='#datepicker1'>
                                                            <input type="text" class="form-control input_new" name="datepicker" id="datepicker" placeholder="Start Date" data-date-autoclose="true" autocomplete="off"
                                                            data-date-language="th-th" value="{{ $date_now }}"/>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="form-group">
                                                        <div class="input-daterange input-group" id="datepicker1" data-date-format="dd M, yyyy" data-date-autoclose="true" data-provide="datepicker" data-date-container='#datepicker1'>
                                                            <input type="text" class="form-control input_new" name="datepicker" id="datepicker" placeholder="Start Date" data-date-autoclose="true" autocomplete="off"
                                                            data-date-language="th-th" value="{{ $datepicker }}"/>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                            </div>

                                            <div class="col-md-1 text-end">
                                                <label for="chackup_hn" style="font-size: 15px">HN :</label>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">   
                                                    @if ($chackup_hn =='')
                                                    <input type="text" class="form-control" id="chackup_hn" name="chackup_hn">
                                                    @else
                                                    <input type="text" class="form-control" id="chackup_hn" name="chackup_hn" value="{{$chackup_hn}}">
                                                    @endif                                             
                                                   
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="submit" class="btn btn-primary btn-sm Getchackup_hn">
                                                    <img src="{{ asset('images/Search02.png') }}" class="me-2 ms-2" height="18px" width="18px">
                                                    ค้นหา
                                                </button>
                                            </div>
                                            

                                        </div>  
                                                                                                                  
                                    </div>
                                </div>
                            </div>
                    </div> 
    
                    </form>
                </div>
            </div>
        </div>
        
        <div class="row ">
            <div class="col-md-12">
                <div class="card card_prs_4" >
    
                    <div class="card-body shadow-lg">
                        
                            <input type="hidden" name="store_id" id="store_id" value=" {{ Auth::user()->store_id }}">
                           

                        @if ($checks < 1)
                            
                        @else

                            <div class="row">
                                <div class ="col-md-1" style="font-size: 14px">HN :</div>    
                                <div class ="col-md-1" style="font-size: 14px">
                                    <label for=""> {{$datashow->hn}}</label>                
                                </div> 
                                <div class ="col-md-1" style="font-size: 14px">ชื่อ-สกุล :</div>    
                                <div class ="col-md-2" style="font-size: 14px">
                                    <label for=""> {{$datashow->ptname}}</label>
                                </div>
                                <div class ="col-md-1" style="font-size: 14px">เพศ :</div>    
                                <div class ="col-md-1" style="font-size: 14px">
                                    <label for=""> {{$datashow->sex}}</label>
                                </div>
                                <div class ="col-md-1" style="font-size: 14px">อายุ :</div>    
                                <div class ="col-md-1" style="font-size: 14px">
                                    <label for=""> {{$datashow->age_y}} &nbsp; ปี</label>
                                </div>
                                <div class ="col-md-1" style="font-size: 14px">เลขบัตร :</div>    
                                <div class ="col-md-2" style="font-size: 14px">
                                    <label for=""> {{$datashow->cid}}</label>
                                </div>  
                            </div>
                            <div class="row">
                                <div class ="col-md-1" style="font-size: 14px">น้ำหนัก :</div>    
                                <div class ="col-md-1" style="font-size: 14px">
                                    <label for=""> {{$datashow->bw}}&nbsp;Kg.</label>
                                </div> 
                                <div class ="col-md-1" style="font-size: 14px">ส่วนสูง :</div>    
                                <div class ="col-md-2" style="font-size: 14px">
                                    <label for=""> {{$datashow->height}} &nbsp;Cm.</label>
                                </div>
                                <div class ="col-md-1" style="font-size: 14px">รอบเอว :</div>    
                                <div class ="col-md-1" style="font-size: 14px">
                                    <label for=""> {{$datashow->waist}}  Cm.</label>
                                </div>
                                <div class ="col-md-1 text-start"> 

                                    @if ($datashow->sex_code == 1) 
                                        @if ($datashow->waist > 90)
                                            <span class="badge bg-danger" style="font-size: 15px">อ้วนลงพุง</span>
                                        @elseif ($datashow->waist == 90)
                                            <span class="badge bg-warning text-dark" style="font-size: 15px">เสี่ยงอ้วนลงพุง</span>
                                        @else
                                            <span class="badge bg-success" style="font-size: 15px">ปกติ</span>
                                        @endif
                                    @elseif ($datashow->sex_code == 2)
                                        @if ($datashow->waist > 80)
                                            <span class="badge bg-danger" style="font-size: 15px">อ้วนลงพุง</span>
                                        @elseif ($datashow->waist == 80)
                                            <span class="badge bg-warning text-dark" style="font-size: 15px">เสี่ยงอ้วนลงพุง</span>
                                        @else
                                            <span class="badge bg-success" style="font-size: 15px">ปกติ</span>
                                        @endif
                                    @endif

                                </div>
                                <div class ="col-md-1" style="font-size: 14px">อุณหภูมิ :</div>    
                                <div class ="col-md-1" style="font-size: 14px">
                                    <label for=""> {{$datashow->temperature}} &nbsp;C</label>
                                </div>
                                <div class ="col-md-1" style="font-size: 14px">อัตราการหายใจ :</div>    
                                <div class ="col-md-1" style="font-size: 14px">
                                    <label for=""> {{$datashow->rr}} &nbsp; / m</label>
                                </div>  
                            </div>

                            <div class="row">
                                <div class ="col-md-1" style="font-size: 14px">ชีพจร :</div>    
                                <div class ="col-md-1" style="font-size: 14px">
                                    <label for=""> {{$datashow->pulse}} &nbsp; / m</label>                                    
                                </div>
                                <div class ="col-md-1">

                                    @if ($datashow->pulse >= 55 && $datashow->pulse <= 100)
                                        <span class="badge bg-success" style="font-size: 15px">ปกติ</span>
                                    @elseif ($datashow->pulse < 55)
                                        <span class="badge bg-warning text-dark" style="font-size: 15px">ชีพจรเต้นช้ากว่าปกติ</span>
                                    @else
                                        <span class="badge bg-danger" style="font-size: 15px">ชีพจรเต้นเร็วกว่าปกติ</span>
                                    @endif

                                </div>
                                <div class ="col-md-1" style="font-size: 14px" >BMI :</div>    
                                <div class ="col-md-1">
                                    <label for=""> {{$datashow->bmi}} </label>
                                </div>
                                <div class ="col-md-1">

                                    @if ($datashow->bmi < 18.5)
                                        <span class="badge bg-primary" style="font-size: 15px">ผอม</span>
                                    @elseif ($datashow->bmi >= 18.5 && $datashow->bmi < 25)
                                        <span class="badge bg-success" style="font-size: 15px">ปกติ</span>
                                    @elseif ($datashow->bmi >= 25 && $datashow->bmi < 30)
                                        <span class="badge bg-warning text-dark" style="font-size: 15px">อ้วนระดับ 1</span>
                                    @elseif ($datashow->bmi >= 30 && $datashow->bmi < 35)
                                        <span class="badge bg-danger" style="font-size: 15px">อ้วนระดับ 2</span>
                                    @else
                                        <span class="badge bg-dark" style="font-size: 15px">อ้วนระดับ 3</span>
                                    @endif

                                </div> 
                                  <div class ="col-md-1" style="font-size: 14px">ความดันโลหิต :</div>    
                                <div class ="col-md-1">
                                    <label for=""> {{$datashow->bps}} &nbsp;/ {{$datashow->bpd}}</label>
                                </div>
                                <div class ="col-md-1" style="font-size: 14px">
                                                                     
                                    @if (($datashow->bps >= 90 && $datashow->bps <= 129) || ($datashow->bpd >= 60 && $datashow->bpd <= 84))
                                        <span class="badge bg-success" style="font-size: 15px">ปกติ</span>
                                    @elseif (($datashow->bps >= 130 && $datashow->bps <= 139) || ($datashow->bpd >= 85 && $datashow->bpd <= 89))
                                        <span class="badge bg-warning text-dark" style="font-size: 15px">เสี่ยงความดันโลหิตสูง</span>
                                    @elseif (($datashow->bps >= 140 && $datashow->bps <= 159) || ($datashow->bpd >= 90 && $datashow->bpd <= 99))
                                        <span class="badge bg-danger" style="font-size: 15px">ความดันโลหิตสูงระดับ 1</span>
                                    @elseif (($datashow->bps >= 160 && $datashow->bps <= 179) || ($datashow->bpd >= 100 && $datashow->bpd <= 109))
                                        <span class="badge bg-danger text-dark" style="font-size: 15px">ความดันโลหิตสูงระดับ 2</span>
                                    @elseif (($datashow->bps >= 180 && $datashow->bps <= 209) || ($datashow->bpd >= 110 && $datashow->bpd <= 119))
                                        <span class="badge bg-dark" style="font-size: 15px">ความดันโลหิตสูงระดับ 3</span>                                    
                                    @endif



                                </div>                                     
                            </div>

                            <div class="row">
                                <div class ="col-md-1" style="font-size: 14px">ตรวจวัดสายตา :</div>    
                                <div class ="col-md-4">
                                    <label for=""> (&nbsp;&nbsp;) &nbsp; ปกติ &nbsp;&nbsp;&nbsp; (&nbsp;&nbsp;) &nbsp; ไม่ปกติ............................................... (&nbsp;&nbsp;) &nbsp; ไม่ได้ตรวจ</label>
                                </div> 
                                <div class ="col-md-2" style="font-size: 14px">ผลตรวจ X-RAY ปอด :</div>    
                                <div class ="col-md-4">
                                    <label for=""> (&nbsp;&nbsp;) &nbsp; ปกติ ............................................... &nbsp;&nbsp;&nbsp; (&nbsp;&nbsp;) &nbsp; ผิดปกติ............................................... </label>
                                </div>              
                            </div>

                         @endif

                    </div>     
    
                </div>

                <div>
                    <table class="table">
                        <thead>
                            <tr>
                                
                            </tr>
                        </thead>

                    </table>

                </div>

            </div>
        </div>

        <div class="row">
            <div class="table table-bordered">
                <table class="table table-sm table-striped table-bordered dt-responsive nowrap myTable" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th class="text-center" width="3%" style="background-color: rgb(219, 247, 232)">ลำดับ</th>
                            <th class="text-center" width="5%" style="background-color: rgb(221, 219, 247)">รายการ</th>
                            <th class="text-center" width="5%" style="background-color: rgb(199, 224, 252)">ผลตรวจ</th>
                            <th class="text-center" width="10%" style="background-color: rgb(255, 255, 255)">ค่ามาตรฐาน</th>
                            <th class="text-center" width="3%" style="background-color: rgb(219, 247, 232)">ผลตรวจ</th>
                            {{-- <th class="text-center" width="3%" style="background-color: rgb(252, 182, 182)">ไม่ปกติ</th> --}}
                        </tr>
                    </thead>

                    <tbody>
                        <?php $i = 0; $total1 = 0; ?>
                        @foreach ($data_show_subnew as $item_sub)
                        <?php $i++; ?>
                            <tr>
                                <td class="text-center" width="3%">
                                    <input type="hidden" name="" id="" value="{{$item_sub->lab_items_code}}">
                                    <input type="hidden" name="" id="" value="{{$item_sub->sex}}">
                                    <input type="hidden" name="" id="" value="{{$item_sub->age_y}}">
                                    {{$i}}
                                </td>
                                <td class="text-start" width="5%">{{$item_sub->lab_items_name}}&nbsp;{{$item_sub->lab_items_display_name}}</td>
                                <td class="text-center" width="5%">{{$item_sub->lab_order_result}}</td>
                                <td class="text-center" width="10%">{{$item_sub->lab_items_normal_value_ref}}</td>
                                <td class="text-center" width="3%">
                                    {{$item_sub->lab_order_result_new}}
                                </td>
                                {{-- <td class="text-center" width="3%"></td> --}}
                            </tr>     
                        @endforeach
                       

                    </tbody>
                    <tr style="background-color: #f3fca1">

                    </tr>
                </table>
            </div>
        </div>

    </div>


@endsection
@section('footer')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"> </script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <script>
        // function Getchackup_hn() {

        // // alert('ใส่ข้อความที่ต้องการ');
        //     var chackup_hn = document.getElementById("chackup_hn").value;
        //         var datepicker = document.getElementById("datepicker").value;
        //         alert(datepicker);

        //         var _token = $('input[name="_token"]').val();
        //         $.ajax({
        //             url: "{{url('checkup_report_detail')}}",
        //             method: "GET",
        //             data: {
        //                 chackup_hn: chackup_hn, datepicker: datepicker,
        //                 _token: _token
        //             },
        //             success: function (data) {
        //                 console.log(data.data_show2.hn);
        //                 $('#hn_').val(data.data_show2.hn)
        //                 // $('#show_detailpatient').html(result);
        //             }
        //         })
        // }

        var Linechart;
        $(document).ready(function() {
            $('#example').DataTable();
            $('#example2').DataTable();
            $('#p4p_work_month').select2({
                placeholder: "--เลือก--",
                allowClear: true
            });
            $('#datepicker').datepicker({
                format: 'yyyy-mm-dd'
            });
            $('#datepicker2').datepicker({
                format: 'yyyy-mm-dd'
            });
 

        });
        $(document).on('click', '.Getchackup_hn', function() {
                // var chackup_hn = $(this).val();
                // var datepicker = $(this).val();
                     var chackup_hn = document.getElementById("chackup_hn").value;
                var datepicker = document.getElementById("datepicker").value;
                // $('#addicodeModal').modal('show');
                // alert(chackup_hn);
                $.ajax({
                    type: "GET",
                    url: "{{ url('checkup_report_detail') }}" + '/' + chackup_hn + '/' + datepicker,
                    success: function(data) {
                        $('#show_detailpatient').html(data);
                        // if (data.status == 200) {
                        //     alert(data.data_shows.hn);
                        //     $('#HN_NEW').val(data.data_shows.hn)
                        //     $('#hn_').val(data.data_shows.hn)
                        // } else {
                            
                        // }
                        // console.log(data.data_show2.hn);
                        // $('#HN_NEW').val(data.data_show2.hn)
                        // $('#hn_').val(data.data_show2.hn)
                        // $('#vn').val(data.data_pang.vn)
                        // $('#an').val(data.data_pang.an)
                        // $('#hn').val(data.data_pang.hn)
                        // $('#cid').val(data.data_pang.cid)
                        // $('#vstdate').val(data.data_pang.vstdate)
                        // $('#dchdate').val(data.data_pang.dchdate)
                        // $('#ptname').val(data.data_pang.ptname)
                        // $('#debit_total').val(data.data_pang.debit_total)
                        // $('#pttype').val(data.data_pang.pttype)
                        // $('#acc_debtor_id').val(data.data_pang.acc_debtor_id)

                        // $('#account_code_new').val(data.data_pang.account_code)
                        // $('#pttype_new').val(data.data_pang.pttype)
                        // $('#debit_total_new').val(data.data_pang.debit_total)
                    },
                });
            });

    </script>


@endsection
