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
    $ynow = date('Y')+543;
    $yb =  date('Y')+542;
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
 
            <form action="{{ URL('account_rep') }}" method="GET">
                @csrf
                <div class="row"> 
                    <div class="col"></div>
                    <div class="col-md-7">
                        <h4 class="card-title" style="color:rgb(202, 120, 250)">Detail Report Account</h4>
                        <p class="card-title-desc">รายงานลูกหนี้ค่ารักษาพยาบาล</p>
                    </div>
                  
                     
                    @if ($budget_year =='')
                    <div class="col-md-2"> 
                            <select name="budget_year" id="budget_year" class="form-control inputmedsalt text-center card_audit_4c" style="width: 100%;font-size:13px">
                                @foreach ($dabudget_year as $item_y)
                                    @if ($bg_yearnow == $item_y->leave_year_id )
                                        <option value="{{$item_y->leave_year_id}}" selected>{{$item_y->leave_year_name}}</option>
                                    @else
                                        <option value="{{$item_y->leave_year_id}}">{{$item_y->leave_year_name}}</option>
                                    @endif                                   
                                @endforeach
                            </select>
                    </div>
                    @else
                    <div class="col-md-2"> 
                            <select name="budget_year" id="budget_year" class="form-control inputmedsalt text-center card_audit_4c" style="width: 100%;font-size:13px">
                                @foreach ($dabudget_year as $item_y)
                                    @if ($budget_year == $item_y->leave_year_id )
                                        <option value="{{$item_y->leave_year_id}}" selected>{{$item_y->leave_year_name}}</option>
                                    @else
                                        <option value="{{$item_y->leave_year_id}}">{{$item_y->leave_year_name}}</option>
                                    @endif                                   
                                @endforeach
                            </select>
                    </div>
                    @endif
                    <div class="col-md-2 text-start">  
                        <button type="submit" class="ladda-button btn-pill btn btn-info cardacc" data-style="expand-left">
                            <span class="ladda-label">
                                <img src="{{ asset('images/Search02.png') }}" class="me-2 ms-2" height="18px" width="18px">
                                ค้นหา {{$budget_year}}</span>
                        </button>
                           
                    </div> 
               
               
            </div>
            </form>  
       
            <div class="row">  
             
                @if ($budget_year =='')
                    <div class="col-xl-12">
                        <div class="card card_audit_4c" style="background-color: rgb(253, 249, 239)">   
                            <div class="table-responsive p-2"> 
                                <table id="example" class="table table-sm table-hover table-striped table-bordered dt-responsive nowrap" style=" border-spacing: 0; width: 100%;"> 
                                    @php
                                        $ynowh = date('Y')+543;
                                        $yoldh = date('Y')+542;
                                    @endphp
                                    <thead style="color:#0770c5">
                                        <tr>
                                            <th width="7%" class="text-center" style="font-size: 11px;border: 1px solid rgb(250, 214, 159);">รหัสผัง</th>
                                            <th class="text-center" style="font-size: 11px;border: 1px solid rgb(250, 214, 159);">ชื่อผัง</th>

                                            <th width="6%" class="text-center" style="font-size: 11px;border: 1px solid rgb(250, 214, 159);">มกราคม<br>( {{$ynowh}} )</th>
                                            <th width="6%" class="text-center" style="font-size: 11px;border: 1px solid rgb(250, 214, 159);">กุมภาพันธ์<br>( {{$ynowh}} )</th>
                                            <th width="6%" class="text-center" style="font-size: 11px;border: 1px solid rgb(250, 214, 159);">มีนาคม<br>( {{$ynowh}} )</th>
                                            <th width="6%" class="text-center" style="font-size: 11px;border: 1px solid rgb(250, 214, 159);">เมษายน<br>( {{$ynowh}} )</th>
                                            <th width="6%" class="text-center" style="font-size: 11px;border: 1px solid rgb(250, 214, 159);">พฤษภาคม<br>( {{$ynowh}} )</th>
                                            <th width="6%" class="text-center" style="font-size: 11px;border: 1px solid rgb(250, 214, 159);">มิถุนายน<br>( {{$ynowh}} )</th>
                                            <th width="6%" class="text-center" style="font-size: 11px;border: 1px solid rgb(250, 214, 159);">กรกฎาคม<br>( {{$ynowh}} )</th>
                                            <th width="6%" class="text-center" style="font-size: 11px;border: 1px solid rgb(250, 214, 159);">สิงหาคม<br>( {{$ynowh}} )</th>
                                            <th width="6%" class="text-center" style="font-size: 11px;border: 1px solid rgb(250, 214, 159);">กันยายน<br>( {{$ynowh}} )</th>
                                            <th width="6%" class="text-center" style="font-size: 11px;border: 1px solid rgb(250, 214, 159);">ตุลาคม<br>( {{$yoldh}} )</th>
                                            <th width="6%" class="text-center" style="font-size: 11px;border: 1px solid rgb(250, 214, 159);">พฤษจิกายน<br>( {{$yoldh}} )</th>
                                            <th width="6%" class="text-center" style="font-size: 11px;border: 1px solid rgb(250, 214, 159);">ธันวาคม<br>( {{$yoldh}} )</th>

                                        </tr>                                        
                                    </thead>
                                    <tbody>
                                        @foreach ($data_pangall as $item)
                                        @if ($item->pang != '')
                                            
                                                @php 
                                                    $ynow = date('Y');
                                                    $yold = date('Y')-1;
                                                    $total1       = DB::table('acc_account_total')->where('account_code','=',$item->pang)->whereMonth('vstdate','=',"1")->whereYear('vstdate','=',$ynow)->sum('debit_total');
                                                    $total2       = DB::table('acc_account_total')->where('account_code','=',$item->pang)->whereMonth('vstdate','=',"2")->whereYear('vstdate','=',$ynow)->sum('debit_total');
                                                    $total3       = DB::table('acc_account_total')->where('account_code','=',$item->pang)->whereMonth('vstdate','=',"3")->whereYear('vstdate','=',$ynow)->sum('debit_total');
                                                    $total4       = DB::table('acc_account_total')->where('account_code','=',$item->pang)->whereMonth('vstdate','=',"4")->whereYear('vstdate','=',$ynow)->sum('debit_total');
                                                    $total5       = DB::table('acc_account_total')->where('account_code','=',$item->pang)->whereMonth('vstdate','=',"5")->whereYear('vstdate','=',$ynow)->sum('debit_total');
                                                    $total6       = DB::table('acc_account_total')->where('account_code','=',$item->pang)->whereMonth('vstdate','=',"6")->whereYear('vstdate','=',$ynow)->sum('debit_total');
                                                    $total7       = DB::table('acc_account_total')->where('account_code','=',$item->pang)->whereMonth('vstdate','=',"7")->whereYear('vstdate','=',$ynow)->sum('debit_total');
                                                    $total8       = DB::table('acc_account_total')->where('account_code','=',$item->pang)->whereMonth('vstdate','=',"8")->whereYear('vstdate','=',$ynow)->sum('debit_total');
                                                    $total9       = DB::table('acc_account_total')->where('account_code','=',$item->pang)->whereMonth('vstdate','=',"9")->whereYear('vstdate','=',$ynow)->sum('debit_total');

                                                    $total10       = DB::table('acc_account_total')->where('account_code','=',$item->pang)->whereMonth('vstdate','=',"9")->whereYear('vstdate','=',$yold)->sum('debit_total');
                                                    $total11       = DB::table('acc_account_total')->where('account_code','=',$item->pang)->whereMonth('vstdate','=',"10")->whereYear('vstdate','=',$yold)->sum('debit_total');
                                                    $total12       = DB::table('acc_account_total')->where('account_code','=',$item->pang)->whereMonth('vstdate','=',"12")->whereYear('vstdate','=',$yold)->sum('debit_total');
                                                                                
                                                @endphp
                                                <tr>
                                                    <td class="text-center" width="7%" style="color:#fc800d;border: 1px solid rgb(250, 214, 159);">{{$item->pang}}</td>
                                                    <td style="color:#6b6869;border: 1px solid rgb(250, 214, 159);">{{$item->pangname}}</td> 
                                                    <td class="text-center" width="5%" style="color:#1179ce;border: 1px solid rgb(250, 214, 159);">
                                                        <a href="{{URL('account_totalrep_detail/'.$item->pang.'/1/'.$ynow)}}">{{number_format($total1, 2)}}</a> 
                                                    </td>
                                                    <td class="text-center" width="5%" style="color:#1179ce;border: 1px solid rgb(250, 214, 159);"> 
                                                        <a href="{{URL('account_totalrep_detail/'.$item->pang.'/2/'.$ynow)}}">{{number_format($total2, 2)}}</a> 
                                                    </td>
                                                    <td class="text-center" width="5%" style="color:#1179ce;border: 1px solid rgb(250, 214, 159);"> 
                                                        <a href="{{URL('account_totalrep_detail/'.$item->pang.'/3/'.$ynow)}}">{{number_format($total3, 2)}}</a> 
                                                    </td>
                                                    <td class="text-center" width="5%" style="color:#1179ce;border: 1px solid rgb(250, 214, 159);"> 
                                                        <a href="{{URL('account_totalrep_detail/'.$item->pang.'/4/'.$ynow)}}">{{number_format($total4, 2)}}</a> 
                                                    </td>
                                                    <td class="text-center" width="5%" style="color:#1179ce;border: 1px solid rgb(250, 214, 159);"> 
                                                        <a href="{{URL('account_totalrep_detail/'.$item->pang.'/5/'.$ynow)}}">{{number_format($total5, 2)}}</a> 
                                                    </td>
                                                    <td class="text-center" width="5%" style="color:#1179ce;border: 1px solid rgb(250, 214, 159);"> 
                                                        <a href="{{URL('account_totalrep_detail/'.$item->pang.'/6/'.$ynow)}}">{{number_format($total6, 2)}}</a> 
                                                    </td>
                                                    <td class="text-center" width="5%" style="color:#1179ce;border: 1px solid rgb(250, 214, 159);"> 
                                                        <a href="{{URL('account_totalrep_detail/'.$item->pang.'/7/'.$ynow)}}">{{number_format($total7, 2)}}</a> 
                                                    </td>
                                                    <td class="text-center" width="5%" style="color:#1179ce;border: 1px solid rgb(250, 214, 159);"> 
                                                        <a href="{{URL('account_totalrep_detail/'.$item->pang.'/8/'.$ynow)}}">{{number_format($total8, 2)}}</a> 
                                                    </td>
                                                    <td class="text-center" width="5%" style="color:#1179ce;border: 1px solid rgb(250, 214, 159);"> 
                                                        <a href="{{URL('account_totalrep_detail/'.$item->pang.'/9/'.$ynow)}}">{{number_format($total9, 2)}}</a> 
                                                    </td>
                                                    <td class="text-center" width="5%" style="color:#1179ce;border: 1px solid rgb(250, 214, 159);"> 
                                                        <a href="{{URL('account_totalrep_detail/'.$item->pang.'/10/'.$yold)}}">{{number_format($total10, 2)}}</a> 
                                                    </td>
                                                    <td class="text-center" width="5%" style="color:#1179ce;border: 1px solid rgb(250, 214, 159);"> 
                                                        <a href="{{URL('account_totalrep_detail/'.$item->pang.'/11/'.$yold)}}">{{number_format($total11, 2)}}</a> 
                                                    </td>
                                                    <td class="text-center" width="5%" style="color:#1179ce;border: 1px solid rgb(250, 214, 159);"> 
                                                        <a href="{{URL('account_totalrep_detail/'.$item->pang.'/12/'.$yold)}}">{{number_format($total12, 2)}}</a> 
                                                    </td>
                                                </tr> 
                                            
                                        @else
                                            
                                        @endif
                                        @endforeach
                                    </tbody>
                                </table>
                                
                            </div>
                        </div>
                    </div>
                @else
                    
                @endif
                
                    
              
              
            </div>
    
        </div>

@endsection
@section('footer')
    <script>
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
    </script>

@endsection
