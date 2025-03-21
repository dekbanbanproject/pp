@extends('layouts.medicine_new')
@section('title', 'PK-OFFICE || แพทย์แผนไทย')

     <?php
     use App\Http\Controllers\StaticController;
     use Illuminate\Support\Facades\DB;   
     $count_meettingroom = StaticController::count_meettingroom();
 ?>


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
               border-top: 10px #20a886 solid;
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
     
                
        <div class="row">
            <div class="col-md-2"> 
                <h4 class="card-title" style="color:rgb(10, 151, 85)">Detail Over the salt pot</h4>
                <p class="card-title-desc"> การลงข้อมูล ทับหม้อเกลือ บัตรทองในเขต  </p>
            </div>
            <div class="col"></div>
            
        </div>
                     
        <div class="row">          
            <div class="col-md-12">
                <div class="card cardmedsalt" style="background-color: rgba(221, 240, 235, 0.74)">
                     
                    <div class="card-body">
                        <table id="example" class="table table-sm table-striped table-bordered dt-responsive nowrap myTable" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        {{-- <table id="example" class="table table-striped table-bordered" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> --}}
                                <thead>                                           
                                    <tr>
                                        <th width="5%" class="text-center">ลำดับ</th>
                                        <th class="text-center">HN</th>
                                        <th class="text-center">AN</th>
                                        <th class="text-center">cid</th>
                                        <th class="text-center">วันคลอด</th>
                                        <th class="text-center">ประเภทการคลอด</th>
                                        <th class="text-center">วันคลอด บัญชี2</th>
                                        <th class="text-center">วันที่รับบริการ</th>
                                        <th class="text-center">ชื่อ - สกุล</th>
                                        <th class="text-center">สิทธิ</th>
                                        <th class="text-center">ที่อยู่</th>
                                        <th class="text-center">รพ.สต.ตามบัตรทอง</th>
                                        <th class="text-center">เบอร์โทร</th>     
                                        <th class="text-center">หัตถการ 9007712,9007713,9007714,9007716,9007730</th>                                                
                                    </tr>                                            
                                </thead>
                                <tbody>
                                    <?php $i = 1; ?>
                                    @foreach ($datashow as $item)
                                        <tr>
                                            <td class="text-center">{{$i++}}</td> 
                                            <td class="text-center">
                                                <a href="{{ url('medicine_salt_subhn/'.$item->hn) }}" target="_blank">{{ $item->hn }}</a>  
                                            </td>
                                            <td class="text-center">{{$item->an}}</td>
                                            <td class="text-center">{{$item->cid}}</td>
                                            <td class="text-center">{{$item->labor_date}}</td>
                                            <td class="text-center">{{$item->deliver_name}}</td>
                                            <td class="text-center">{{$item->dlabor_date}}</td>
                                            <td class="text-center">{{$item->service_date}}</td>
                                            <td class="p-2">{{$item->fullname}}</td>
                                            <td class="text-center">{{$item->pttype}}</td>
                                            <td class="p-2">{{$item->fulladdressname}}</td>
                                            <td class="p-2">{{$item->hname}}</td>
                                            <td class="p-2">{{$item->informtel}}</td>
                                            <td class="p-2">{{$item->icd10tm}}</td>
                                           
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
                $('#example').DataTable();
                $('#example2').DataTable();
                $('#example3').DataTable();
    
                $('select').select2();
                $('#ECLAIM_STATUS').select2({
                    dropdownParent: $('#detailclaim')
                });
    
                $('#users_group_id').select2({
                    placeholder: "--เลือก-- ",
                    allowClear: true
                });
    
                $('#datepicker').datepicker({
                    format: 'yyyy-mm-dd'
                });
                $('#datepicker2').datepicker({
                    format: 'yyyy-mm-dd'
                });
    
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
    
                $(document).on('click', '#printBtn', function() {
                    var month_id = $(this).val();
                    alert(month_id);
                    
                });
     
               
            });
        </script>
    
    @endsection
