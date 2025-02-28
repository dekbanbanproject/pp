@extends('layouts.wh')
@section('title', 'PK-OFFICE || Where House')

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
            border-top: 10px rgb(252, 101, 1) solid;
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

        <div class="row">
            <div class="col-md-8">
                <h4 style="color:rgb(247, 103, 68)>คลัง{{$stock_name}} </h4>
                <p style="color:rgb(236, 11, 97);font-size:19px">รายการ {{$pro_name}} || รหัส {{$pro_code}}</p>
            </div>
            {{-- <div class="col"></div>    --}}
            <div class="col-md-4 text-end">
                <button type="button" class="ladda-button btn-pill btn btn-white card_audit_4c" style="color:rgb(4, 161, 148);font-size:19px">
                   <i class="fa-regular fa-rectangle-list me-2 ms-2" style="color:rgb(4, 161, 148);font-size:19px"></i>คงเหลือ {{number_format($total_qty, 0)}}
                </button>
                <button type="button" class="ladda-button btn-pill btn btn-white card_audit_4c" style="color:rgb(238, 15, 89);font-size:19px">
                    ราคา {{number_format($total_price, 2)}} <i class="fa-solid fa-baht-sign me-2 ms-2" style="color:rgb(238, 15, 89);font-size:19px"></i>
                 </button>
                 <a href="{{URL('wh_stock_card_pdf/'.$idstore.'/'.$idpro)}}" class="ladda-button btn-pill btn btn-white card_audit_4c" style="color:rgb(238, 15, 89);font-size:19px">
                    <i class="fa-solid fa-print me-2 ms-2" style="color:rgb(17, 167, 226);font-size:19px"></i>  
                 </a>
            </div>
        </div>
      
        <input type="hidden" name="idpro" id="idpro" value="{{$idpro}}">
        <input type="hidden" name="idstore" id="idstore" value="{{$idstore}}">

        <div class="row mt-2">
            <div class="col-md-12">

                <div class="card card_audit_4c" style="background-color: rgb(248, 241, 237)">

                            <div class="card-body">

                                <div class="row">
                                    <div class="col-xl-12">
                                        
                                        <table class="table table-sm table-striped table-bordered" style="width: 100%;">
                                                <thead> 
                                                    <tr style="font-size: 11px;height: 11px;color:rgb(65, 63, 63)" class="text-center">                            
                                                    <th rowspan="2" style="border: 1px solid rgb(250, 214, 159);width: 6%;background-color: rgb(252, 237, 219);color:#252424">ว/ด/ป</th>
                                                    <th rowspan="2" style="border: 1px solid rgb(250, 214, 159);width: 12%;background-color: rgb(252, 237, 219);color:#252424">
                                                        <span class="badge" style="background-color: rgb(7, 192, 152);font-size: 12px;">รับจาก</span>
                                                         /จ่ายให้
                                                        </th>
                                                    <th rowspan="2" style="border: 1px solid rgb(250, 214, 159);width: 6%;background-color: rgb(252, 237, 219);color:#252424">เลขที่เอกสาร</th> 
                                                    <th rowspan="2" style="border: 1px solid rgb(250, 214, 159);width: 4%;background-color: rgb(252, 237, 219);color:#252424">ราคาต่อหน่วย</th>
                                                    <th colspan="3" style="border: 1px solid rgb(250, 214, 159);width: 4%;background-color: rgb(252, 237, 219);color:#252424">จำนวน</th>
                                                    <th rowspan="2" style="border: 1px solid rgb(250, 214, 159);width: 4%;background-color: rgb(252, 237, 219);color:#252424">ราคารวม</th>
                                                    <th rowspan="2" style="border: 1px solid rgb(250, 214, 159);width: 10%;background-color: rgb(252, 237, 219);color:#252424">หมายเหตุ</th> 
                                                </tr>  
                                                <tr style="font-size: 11px;height: 11px;color:rgb(65, 63, 63)" class="text-center">
                                                    <th style="border: 1px solid rgb(250, 214, 159);width: 4%;background-color: rgb(252, 237, 219);color:#252424">รับ</th>
                                                    <th style="border: 1px solid rgb(250, 214, 159);width: 4%;background-color: rgb(252, 237, 219);color:#252424">จ่าย</th>
                                                    <th style="border: 1px solid rgb(250, 214, 159);width: 4%;background-color: rgb(252, 237, 219);color:#252424">คงเหลือ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $i = 1; @endphp
                                                @foreach ($stock_card_recieve as $item)
                                                @php $i++; @endphp   
                                                        <tr>
                                                            <td class="text-center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="6%"><span class="badge" style="background-color: rgb(7, 192, 152);font-size: 12px;">{{DateThai($item->recieve_date)}}</span></td>
                                                            <td class="text-start" style="border: 1px solid rgb(250, 232, 221);font-size: 12px;"><span class="badge" style="background-color: rgb(7, 192, 152);font-size: 12px;">{{$item->supplies_namesub}}</span></td>
                                                            <td class="text-center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="6%"><span class="badge" style="background-color: rgb(7, 192, 152);font-size: 12px;">{{$item->recieve_po_sup}}</span></td>
                                                            <td class="text-center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="4%"><span class="badge" style="background-color: rgb(7, 192, 152);font-size: 12px;">{{$item->one_price}}</span></td>
                                                            <td class="text-center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="4%"><span class="badge" style="background-color: rgb(7, 192, 152);font-size: 12px;">{{$item->qty}}</span></td> 
                                                            <td class="text-center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="4%">-</td> 
                                                            <td class="text-center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="4%">
                                                                {{-- @if ($item->total_allqty = '0')                                                                
                                                                    <span class="badge" style="background-color: rgb(252, 27, 83);font-size: 12px;">{{$item->total_allqty}}</span>
                                                                @else --}}
                                                                    <span class="badge" style="background-color: rgb(7, 192, 152);font-size: 12px;">{{$item->total_allqty}}</span>
                                                                {{-- @endif  --}}
                                                            </td> 
                                                            <td class="text-center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="4%"><span class="badge" style="background-color: rgb(7, 192, 152);font-size: 12px;">{{number_format($item->total_allprice, 2)}}</span></td> 
                                                            <td class="text-center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="10%"><span class="badge" style="background-color: rgb(7, 192, 152);font-size: 12px;">LOT: {{$item->lot_no}}</span></td> 
                                                        </tr>                                                 
                                                        @php
                                                            $datashow2 = DB::select(
                                                                    'SELECT b.pro_id,b.pro_code,b.pro_name,d.wh_unit_name,b.qty_pay,b.lot_no,c.export_date,b.one_price
                                                                    ,e.DEPARTMENT_SUB_SUB_NAME,b.stock_list_subid,f.request_no,b.total_stock,b.total_stock_price
                                                                
                                                                    FROM wh_stock_export_sub b  
                                                                    LEFT JOIN wh_stock_export c ON c.wh_stock_export_id = b.wh_stock_export_id
                                                                    LEFT JOIN wh_request f ON f.wh_request_id = b.wh_request_id
                                                                    LEFT JOIN wh_unit d ON d.wh_unit_id = b.unit_id
                                                                    LEFT JOIN department_sub_sub e ON e.DEPARTMENT_SUB_SUB_ID = c.stock_list_subid 
                                                                    WHERE b.lot_no = "'.$item->lot_no.'" 
                                                                    GROUP BY b.lot_no,b.wh_stock_export_sub_id
                                                                    ORDER BY b.lot_no ASC  
                                                            ');
                                                        @endphp

                                                        @foreach ($datashow2 as $item2) 
                                                                <tr>
                                                                    <td class="text-center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="6%">{{DateThai($item2->export_date)}}</td>
                                                                    <td class="text-start" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;">{{$item2->DEPARTMENT_SUB_SUB_NAME}}</td>
                                                                    <td class="text-center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="6%">{{$item2->request_no}}</td>
                                                                    <td class="text-center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="4%">{{$item2->one_price}}</td>
                                                                    <td class="text-center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="4%">-</td> 
                                                                    <td class="text-center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="4%">{{$item2->qty_pay}}</td> 
                                                                    
                                                                    <td class="text-center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="4%">
                                                                        @if ($item2->total_stock == '0')                                                                
                                                                            <span class="badge" style="background-color: rgb(252, 27, 83);font-size: 12px;">{{$item2->total_stock}}</span>
                                                                        @else
                                                                            {{$item2->total_stock}}
                                                                        @endif 
                                                                    </td> 
                                                                    <td class="text-center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="4%">
                                                                        @if ($item2->total_stock_price == '0')                                                                
                                                                            <span class="badge" style="background-color: rgb(252, 27, 83);font-size: 12px;">{{number_format($item2->total_stock_price, 2)}}</span>
                                                                        @else
                                                                            {{number_format($item2->total_stock_price, 2)}}
                                                                        @endif 
                                                                        
                                                                    </td> 
                                                                    <td class="text-center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="10%">LOT: {{$item2->lot_no}}</td> 
                                                                </tr> 
                                                        @endforeach


                                                @endforeach 
                                            

                                                {{-- @foreach ($stock_card_recieve as $item)
                                                    <tr style="font-size: 11px;height: 11px;">        
                                                        <td colspan="7" style="border: 1px solid rgb(250, 232, 221);" class="text-center">
                                                                <table class="table table-sm table-striped">
                                                                    <tr>
                                                                        <td>{{$item->recieve_date}}</td>
                                                                        <td>{{$item->supplies_namesub}}</td>
                                                                        <td>{{$item->recieve_po_sup}}</td>
                                                                        <td>{{$item->qty}}</td>
                                                                        <td>{{$item->qty}}</td>
                                                                        <td>{{$item->one_price}}</td>
                                                                        <td></td> 
                                                                    </tr>                                                                    
                                                                </table>
                                                        </td>
                                                        <td colspan="5" style="border: 1px solid rgb(250, 232, 221);" class="text-center">
                                                            @php
                                                                $datashow2 = DB::select(
                                                                    'SELECT b.pro_id,b.pro_code,b.pro_name,d.wh_unit_name,b.qty_pay,b.lot_no,c.export_date,b.one_price,e.DEPARTMENT_SUB_SUB_NAME,b.stock_list_subid,f.request_no,b.total_stock
                                                                 
                                                                    FROM wh_stock_export_sub b  
                                                                    LEFT JOIN wh_stock_export c ON c.wh_stock_export_id = b.wh_stock_export_id
                                                                    LEFT JOIN wh_request f ON f.wh_request_id = b.wh_request_id
                                                                    LEFT JOIN wh_unit d ON d.wh_unit_id = b.unit_id
                                                                    LEFT JOIN department_sub_sub e ON e.DEPARTMENT_SUB_SUB_ID = c.stock_list_subid 
                                                                    WHERE b.lot_no = "'.$item->lot_no.'" 
                                                                    GROUP BY b.lot_no,b.wh_stock_export_sub_id
                                                                    ORDER BY b.wh_stock_export_sub_id ASC 
                                                                                
                                                                ');
                                                            @endphp
                                                            <table class="table table-sm table-striped" width="100%">
                                                                @foreach ($datashow2 as $item2)
                                                                    <tr>
                                                                        <td style="border: 1px solid rgb(250, 232, 221);" width="20%">&nbsp;{{DateThai($item2->export_date)}}</td>
                                                                        <td style="border: 1px solid rgb(250, 232, 221);" width="20%">&nbsp;{{$item2->qty_pay}}</td>
                                                                        <td style="border: 1px solid rgb(250, 232, 221);" width="20%">3</td>
                                                                        <td style="border: 1px solid rgb(250, 232, 221);" width="20%">4</td>
                                                                        <td style="border: 1px solid rgb(250, 232, 221);" width="20%">5</td> 
                                                                    </tr> 
                                                                @endforeach
                                                               
                                                            </table>

                                                        </td>
                                            
                                                   </tr>   
                                                @endforeach --}}
                                            </tbody>
                                        </table> 
                                    </div>
                                    {{-- <div class="col-xl-8">
                                        <p style="color: rgb(7, 125, 172);font-size:17px">รายละเอียดการจ่ายออก</p>
                                        <table id="example2" class="table table-sm table-striped table-bordered nowrap w-100" style="width: 100%;">
                                            <thead>
                                                <tr style="font-size: 10px;">
                                                    <th class="text-center" style="background-color: rgb(255, 251, 228);font-size: 11px;" width="3%">ลำดับ</th>
                                                    <th class="text-center" style="background-color: rgb(255, 251, 228);font-size: 11px;">LOT</th>
                                                    <th class="text-center" style="background-color: rgb(255, 251, 228);font-size: 12px;" width="10%">วันที่จ่าย</th>
                                                    <th class="text-center" style="background-color: rgb(255, 251, 228);font-size: 11px;">หน่วย</th>
                                                    <th class="text-center" style="background-color: rgb(222, 201, 248);font-size: 11px;">จ่ายออก</th>
                                                    <th class="text-center" style="background-color: rgb(174, 236, 245);font-size: 11px;">ราคาต่อหน่วย</th>
                                                    <th class="text-center" style="background-color: rgb(255, 228, 234);font-size: 11px;">ราคารวม</th>
                                                    <th class="text-center" style="background-color: rgb(255, 228, 234);font-size: 11px;">หน่วยงาน</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $ii = 0;$total1 = 0; $total2 = 0;$total3 = 0;$total4 = 0;$total5 = 0;$total6 = 0;$total7 = 0;$total8 = 0;$total9 = 0; ?>
                                                @foreach ($stock_card_pay as $item_p)
                                                <?php $ii++ ?>

                                                <tr style="font-size: 11px;">
                                                    <td class="text-center" width="3%">{{$ii}}</td>
                                                    <td class="text-center" style="color:rgb(216, 6, 76)" width="8%">{{$item_p->lot_no}}</td>
                                                    <td class="text-center" width="8%">{{$item_p->export_date}}</td>
                                                    <td class="text-center" width="7%">{{$item_p->wh_unit_name}}</td>
                                                    <td class="text-center" style="color:rgb(224, 75, 6)" width="8%">
                                                        @if ($item_p->qty_pay =='') 0
                                                        @else {{$item_p->qty_pay}}
                                                        @endif
                                                    </td>
                                                    <td class="text-center" style="color:rgb(4, 115, 180)" width="10%">{{number_format($item_p->one_price, 2)}}</td>
                                                    <td class="text-center" style="color:rgb(4, 115, 180)" width="10%">{{number_format($item_p->qty_pay*$item_p->one_price, 2)}}</td>
                                                    <td class="text-start" style="color:rgb(9, 127, 182)">{{$item_p->DEPARTMENT_SUB_SUB_NAME}}</td>
                                                </tr>


                                                @endforeach
                                            </tbody>

                                        </table>
                                    </div> --}}
                                </div>
                            </div>

                </div>

            </div>
        </div>

</div>




@endsection
@section('footer')

    <script>
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
    </script>


@endsection
