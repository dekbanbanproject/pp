@extends('layouts.envnew')
@section('title', 'PK-OFFICER || ENV')
@section('content')
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
<script>
    function TypeAdmin() {
        window.location.href = '{{ route('index') }}';
    }
</script>
<?php
if (Auth::check()) {
        $type = Auth::user()->type;
        $iduser = Auth::user()->id;
        $iddep =  Auth::user()->dep_subsubtrueid;
    } else {
        echo "<body onload=\"TypeAdmin()\"></body>";
        exit();
    }
    $url = Request::url();
    $pos = strrpos($url, '/') + 1;

    $datenow = date("Y-m-d");
    $y = date('Y') + 543;
    $newweek = date('Y-m-d', strtotime($datenow . ' -1 week')); //ย้อนหลัง 1 สัปดาห์  
    $newDate = date('Y-m-d', strtotime($datenow . ' -1 months')); //ย้อนหลัง 1 เดือน 

    use Illuminate\Support\Facades\DB;



?>
  
<div class="tabs-animation">
    
        <div class="row text-center">  
            <div id="overlay">
                <div class="cv-spinner">
                  <span class="spinner"></span>
                </div>
              </div>
              
        </div> 

       

        <div class="card card_prs_2">
            <div class="card-header">
                <form action="{{ route('env.env_trash') }}" method="GET">
                    @csrf
                    <div class="row"> 
                    <div class="col-md-4">
                        <h4 class="card-title"  style="color:#096825">รายระเอียดข้อมูลขยะ</h4>   
                    </div>
                    <div class="col"></div>
                    <div class="col-md-1 text-end">วันที่</div>
                    <div class="col-md-4 text-center">
                        <div class="input-daterange input-group" id="datepicker1" data-date-format="dd M, yyyy"
                                data-date-autoclose="true" data-provide="datepicker" data-date-container='#datepicker6'>
                                <input type="text" class="form-control" name="startdate" id="datepicker" placeholder="Start Date"
                                    data-date-container='#datepicker1' data-provide="datepicker" data-date-autoclose="true" autocomplete="off"
                                    data-date-language="th-th" value="{{ $startdate }}" required/>
                                <input type="text" class="form-control" name="enddate" placeholder="End Date" id="datepicker2"
                                    data-date-container='#datepicker1' data-provide="datepicker" data-date-autoclose="true" autocomplete="off"
                                    data-date-language="th-th" value="{{ $enddate }}" required/> 
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa-solid fa-magnifying-glass me-2"></i>
                                        ค้นหา
                                    </button> 
                                    
                </form>
                        </div>
                    </div>
                    <div class="col-md-1 text-end">
                        <a href="{{ url('env_trash_add') }}" class="btn btn-outline-success">เพิ่มข้อมูล</a> 
                    </div>
                </div>
                
            </div>
           
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-eg2-0" role="tabpanel">
                        <p>
                             
                            {{-- <form action="{{ route('env.env_trash') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-1 text-end">วันที่</div>
                                    <div class="col-md-4 text-center">
                                        <div class="input-daterange input-group" id="datepicker1" data-date-format="dd M, yyyy"
                                            data-date-autoclose="true" data-provide="datepicker" data-date-container='#datepicker6'>
                                            <input type="text" class="form-control" name="startdate" id="datepicker" placeholder="Start Date"
                                                data-date-container='#datepicker1' data-provide="datepicker" data-date-autoclose="true"
                                                data-date-language="th-th" value="{{ $startdate }}" required/>
                                            <input type="text" class="form-control" name="enddate" placeholder="End Date" id="datepicker2"
                                                data-date-container='#datepicker1' data-provide="datepicker" data-date-autoclose="true"
                                                data-date-language="th-th" value="{{ $enddate }}" required/> 
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa-solid fa-magnifying-glass me-2"></i>ค้นหา
                                        </button>                         
                                    </div>
                            </form>  --}}
                            {{-- <div class="table-responsive mt-3"> --}}
                                <table class="align-middle mb-0 table table-borderless table-striped table-hover" id="example2">
                                    <thead>
                                        <tr>
                                            <th class="text-center"width="5%">ลำดับ</th> 
                                            <th class="text-center"width="10%">TRASH NO</th>
                                            <th class="text-center"width="10%">วันที่บันทึก</th>
                                            <th class="text-center"width="8%">เวลา</th>
                                            <th class="text-center"width="20%">ผู้บันทึก</th>
                                            <th class="text-center"width="6%">คำสั่ง</th> 
                                          
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1; ?>
                                        @foreach ($datashow as $item)
                                        
                                            <tr>                                            
                                                <td class="text-center" width="4%">{{ $i++ }}</td>
                                                <td class="text-center" width="7%">{{ $item->trash_bill_on }}</td>
                                                <td class="text-center" width="7%">{{DateThai( $item->trash_date )}}</td>
                                                <td class="text-center" width="5%">{{ $item->trash_time }}</td>
                                                {{-- <td class="p-2" width="18%">{{ $item->vendor_name }}</td> --}}
                                                <td class="text-center" width="5%">{{ $item->trash_user }}</td>
                                                <td class="text-center" width="7%">    
                                                    
                                                    <div class="btn-group">
                                                        <button type="button"
                                                            class="btn btn-outline-secondary btn-sm dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            ทำรายการ 
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item menu" data-bs-toggle="modal"
                                                                data-bs-target="#trashetailModal{{ $item->trash_id }}"
                                                                data-bs-toggle="tooltip" data-bs-placement="left"
                                                                data-bs-custom-class="custom-tooltip" title="รายละเอียด">
                                                                <i class="fa-solid fa-pen-to-square me-2"></i>
                                                                <label for=""style="color: rgb(33, 187, 248);font-size:13px">รายละเอียด</label>
                                                            </a>
                                                            <a class="dropdown-item text-warning"
                                                                href="{{ url('env_trash_edit/' . $item->trash_id) }}"
                                                                data-bs-toggle="tooltip" data-bs-placement="left"
                                                                data-bs-custom-class="custom-tooltip" title="แก้ไข">
                                                                <i class="fa-solid fa-pen-to-square me-2"></i>
                                                                <label for=""style="color: rgb(7191, 24, 224);font-size:13px">แก้ไข</label>
                                                            </a>
                                                            <a class="dropdown-item text-danger" href="{{url('env_trash_delete/'.$item->trash_id)}}"
                                                                data-bs-toggle="tooltip" data-bs-placement="left"
                                                                data-bs-custom-class="custom-tooltip" title="ลบ">
                                                                <i class="fa-solid fa-trash-can me-2 mb-1"></i>
                                                                <label for="" style="color: rgb(255, 22, 22);font-size:13px">ลบ</label>
                                                            </a>    
                                                            {{-- <div class="dropdown-divider"></div> --}}
                                                            {{-- <a class="dropdown-item text-danger" href="{{url('env_trash_parameter_delete/'.$item->trash_id)}}"
                                                                data-bs-toggle="tooltip" data-bs-placement="left"
                                                                data-bs-custom-class="custom-tooltip" title="ลบ">
                                                                <i class="fa-solid fa-trash-can me-2 mb-1"></i>
                                                                <label for="" style="color: rgb(70, 70, 70);font-size:13px">ลบ</label>
                                                            </a>                                                            --}}
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                      
                                            <!--  Modal content Update -->
                                            <div class="modal fade" 
                                                id="trashetailModal{{ $item->trash_id }}" tabindex="-1"
                                                aria-labelledby="trashetailModal" aria-hidden="true">
                                                <div class="modal-dialog modal-xl">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="trashetailModal">
                                                                รายระเอียดข้อมูลขยะ
                                                        </div>
                                                        <div class="modal-body">

                                                            <div class="row">
                                                                <div class="col-md-2 ">
                                                                    <p for=""><b>Trash_bill_on :</b></p>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <p for="trash_bill_on">{{ $item->trash_bill_on }}</p>
                                                                </div>                                                               
                                                            </div>

                                                            <div class="row mt-3">
                                                                <div class="col-md-2">
                                                                    <label for=""><b>วันที่แจ้ง :</b></label>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <p
                                                                            for="trash_date">{{ DateThai($item->trash_date) }}</p>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-2 ">
                                                                    <label for=""><b>เวลา :</b></label>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label
                                                                            for="trash_time">{{ formatetime($item->trash_time) }}</label>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row mt-3">
                                                                <div class="col-md-2">
                                                                    <label for=""><b>บริษัท :</b></label>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label
                                                                            for="trash_sub">{{ $item->vendor_name }}</label>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <label for=""><b>ผู้บันทึกข้อมูล :</b></label>
                                                                </div>                                                                
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label
                                                                            for="trash_user">{{ $item->trash_user }}</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <hr>
                                                            <div class="row  mt-3">
                                                                <div class="col-md-12">
                                                               
                                                                            <?php   
                                                                                    $j = 1;
                                                                                    $data_ = DB::connection('mysql')->select('
                                                                                            SELECT *
                                                                                            FROM env_trash_sub
                                                                                            WHERE trash_id = "'.$item->trash_id.'"
                                                                                    ');
                                                                            ?>
                                                                          
                                                                    <div class="row ">
                                                                        <div class="col-md-1">ลำดับ</div>
                                                                        <div class="col-md-5">ประเภทขยะ</div>
                                                                        <div class="col-md-3">จำนวน</div>
                                                                        <div class="col-md-3">หน่วยนับ</div>
                                                                    </div>
                                                                    @foreach ($data_ as $item2)
                                                                    <hr>
                                                                    <div class="row">
                                                                        <div class="col-md-1">{{$j++}}</div>
                                                                        <div class="col-md-5">{{$item2->trash_sub_name}}</div>
                                                                        <div class="col-md-3">{{$item2->trash_sub_qty}}</div>
                                                                        <div class="col-md-3">{{$item2->trash_sub_unit}}</div>
                                                                    </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>

                                                 
                                                        </div>
                                                        <div class="modal-footer">

                                                            <button type="button" class="btn btn-danger btn-sm"
                                                                data-bs-dismiss="modal" id="closebtn">
                                                                <i class="fa-solid fa-xmark me-2"></i>
                                                                ปิด
                                                            </button>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div> 

                                        @endforeach
                                    </tbody>
                                    {{-- <tbody>
                                        <?php $ia = 1; ?>
                                        @foreach ($datashow_ as $item)  
                                            
                                            <tr>
                                                <td>{{ $ia++ }}</td>
                                                <td>{{ $item->CHEACKIN_DATE }}</td> 
                                                <td class="p-2">{{ $item->hrname }}</td>   
                                                <td class="p-2">{{ $item->HR_DEPARTMENT_SUB_SUB_NAME }}</td> 
                                                <td>{{ $item->CHEACKINTIME }}</td>  
                                                <td>{{ $item->CHEACKOUTTIME }}</td>  
                                                <td>{{ $item->OPERATE_TYPE_NAME }}</td>   
                                            </tr>    
                                        @endforeach
                                        
                                    </tbody> --}}
                                </table>
                            {{-- </div>  --}}
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
        // $("#overlay").fadeIn(300);　

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

        // $.ajaxSetup({
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         }
        // });
        // $('#HR_DEPARTMENT_ID').select2({
        //         placeholder: "--เลือก--",
        //         allowClear: true
        //     });
        // $('#HR_DEPARTMENT_SUB_ID').select2({
        //         placeholder: "--เลือก--",
        //         allowClear: true
        //     });
        // $('#HR_DEPARTMENT_SUB_SUB_ID').select2({
        //     placeholder: "--เลือก--",
        //     allowClear: true
        // });

        // $("#spinner-div").hide(); //Request is complete so hide spinner

        // $('#Savetime').click(function() {
        //     var startdate = $('#datepicker').val();
        //     var enddate = $('#datepicker2').val();
        //     var HR_DEPARTMENT_SUB_ID = $('#HR_DEPARTMENT_SUB_ID').val();
        //     var HR_DEPARTMENT_SUB_SUB_ID = $('#HR_DEPARTMENT_SUB_SUB_ID').val(); 
        //     $.ajax({
        //         url: "{{ route('t.time_index_excel') }}",
        //         type: "POST",
        //         dataType: 'json',
        //         data: {
        //             startdate,
        //             enddate,
        //             HR_DEPARTMENT_SUB_ID,
        //             HR_DEPARTMENT_SUB_SUB_ID
        //         },
        //         success: function(data) {
        //             if (data.status == 200) { 
        //                 Swal.fire({
        //                     title: 'บันทึกข้อมูลสำเร็จ',
        //                     text: "You Insert data success",
        //                     icon: 'success',
        //                     showCancelButton: false,
        //                     confirmButtonColor: '#06D177',
        //                     confirmButtonText: 'เรียบร้อย'
        //                 }).then((result) => {
        //                     if (result
        //                         .isConfirmed) {
        //                         console.log(
        //                             data);

        //                         window.location
        //                             .reload();
        //                     }
        //                 })
        //             } else {

        //             }

        //         },
        //     });
        // });  
    });
</script>
{{-- <script>
    $('.department').change(function () {
            if ($(this).val() != '') {
                    var select = $(this).val();
                    var _token = $('input[name="_token"]').val();
                    $.ajax({
                            url: "{{route('person.department')}}",
                            method: "GET",
                            data: {
                                    select: select,
                                    _token: _token
                            },
                            success: function (result) {
                                    $('.department_sub').html(result);
                            }
                    })
                    // console.log(select);
            }
    });

    $('.department_sub').change(function () {
            if ($(this).val() != '') {
                    var select = $(this).val();
                    var _token = $('input[name="_token"]').val();
                    $.ajax({
                            url: "{{route('person.departmenthsub')}}",
                            method: "GET",
                            data: {
                                    select: select,
                                    _token: _token
                            },
                            success: function (result) {
                                    $('.department_sub_sub').html(result);
                            }
                    })
                    // console.log(select);
            }
    });
</script> --}}
@endsection
 
 