<!doctype html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
<title>ทะเบียนควบคุมเวชภัณฑ์</title>

{{-- <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' /> --}}
{{-- <link href='https://fonts.googleapis.com/css?family=Kanit&subset=thai,latin' rel='stylesheet' type='text/css'> --}}
{{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> --}}

{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"type='text/css'> --}}
{{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous"> --}}
{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous"> --}}


<style type="text/css">
      @font-face {
          font-family: 'THSarabunNew';
          src: url({{ storage_path('fonts/THSarabunNew.ttf') }}) format('truetype');
          font-weight: 100; // use the matching font-weight here ( 100, 200, 300, 400, etc).
          font-style: normal; // use the matching font-style here
      }
      body{
        font-family: "THSarabunNew",  //set your font name u can set custom font name also which u set in @font-face css rule

      }
        header {
            position: fixed;
            top: -10px;
            left: 0px;
            right: 0px;
            height: 10px;
            font-size: 14px !important;

            /** Extra personal styles **/
            background-color: #008B8B;
            color: white;
            text-align: center;
            line-height: 25px;
        }

        footer {
            position: fixed;
            bottom: -20px;
            left: 0px;
            right: 0px;
            height: 20px;
            font-size: 20px !important;

            /** Extra personal styles **/
            background-color: #008B8B;
            color: white;
            text-align: center;
            line-height: 25px;
        }

        .bottom-date {
            position: relative;
        }

        .bottom-date-text {
            position: absolute;
            top: -3;
            left: 70;
            width: 140px;
            text-align: center;
        }

        table,
        td {
            border-collapse: collapse; //กรอบด้านในหายไป
        }

        td.o {
            border: 0.1px solid rgb(5, 5, 5);
        }

        table.one {
            border: 0.1px solid rgb(5, 5, 5);
        }
 
        /* @media print {
          footer {page-break-after: always;}
        } */
        /* .page-break {

          page-break-after: always;

          } */


          /* @media print {
            .page-break {
              page-break-after: always;
            }
          } */
          /* @media print { */
            /* thead { 
              display: table-header-group;
            } */
          /* } */
          /* @media print {
              .headers {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                background: white;
                border-bottom: 1px solid black;
              }
            } */

            /* @media print {
              thead { 
                display: table-header-group;
              }
            } */
            .page-break {
                page-break-after: always;
            }


</style>
</head>
<body>
 
  {{-- <div class="container-fluid text-center"> --}}
 
   
    @php
      $row_in_table = 2;
      $row2_in_table = 20;
      $num = 0;
      $countloop = 0;
    @endphp

    
            @foreach($datashow as $key=>$item)  

                {{-- <div style="page-break-after: always;"> --}}
                  {{-- <p>{{ $loop->iteration . '/' . $loop->count }}</p> --}}
                  {{-- <p> {{$loop->index}}</p>  --}}
                  {{-- {{$loop->parent}} --}}

                       {{-- <table style="width: 100%;">
                          <thead>  
                            <tr>
                              <th width="20%"></th>
                              <th align="center" width="50%"><label for="" style="font-size:14px;"><b>บัญชีวัสดุ</b></label></th>
                              <th width="20%"></th>
                            </tr>  
                            <tr>                  
                              <th align="left" width="10%"><label for="" style="font-size:13px;"><b>แผ่นที่ ........</b></label></th>  
                              <th width="60%"></th>                   
                              <th align="right" width="30%"><label for="" style="font-size:13px;"><b>โรงพยาบาลภูเขียวเฉลิมพระเกียรติ</b></label></th>                  
                            </tr>  
                            <tr>                  
                              <th align="left" width="20%"><label for="" style="font-size:13px;"><b>ประเภท </b>{{$protype}}</label></th>  
                              <th align="center" width="60%"><label for="" style="font-size:13px;"><b>ชื่อหรือชนิดวัสดุ </b>{{$pro_name}}</label></th>           
                              <th align="right" width="20%"><label for="" style="font-size:13px;"><b>รหัส </b>{{$pro_code}}</label></th>         
                            </tr> 
                            <tr>               
                              <th align="left" width="30%"><label for="" style="font-size:13px;"><b>ขนาดหรือลักษณะ </b>{{$pro_detail}}</label></th>  
                              <th width="57%"></th>  
                              <th align="right" width="13%"><label for="" style="font-size:13px;"><b>จำนวนอย่างสูง </b>{{$pro_highly}}</label></th>         
                            </tr> 
                            <tr>               
                                <th align="left" width="30%"><label for="" style="font-size:13px;"><b>หน่วยที่นับ </b>{{$wh_unit_name}}</label></th>  
                                <th width="57%"></th>  
                                <th align="right" width="13%"><label for="" style="font-size:13px;"><b>จำนวนอย่างต่ำ </b>{{$pro_atleast}}</label></th>         
                            </tr> 
                          </thead>
                        </table>     --}}
                            

                        <table style="width: 100%;">
                            <thead>      
                              
                              <tr>
                                <th width="10%"></th>
                                <th></th>
                                <th></th>
                                <th align="center" width="60%"><label for="" style="font-size:14px;"><b>บัญชีวัสดุ</b></label></th>
                                <th></th>
                                <th></th>
                                <th width="30%"></th>
                              </tr>  
                              <tr>                  
                                <th align="left" width="10%"><label for="" style="font-size:13px;"><b>แผ่นที่ ........</b></label></th>  
                                <th></th>
                                <th></th>
                                <th width="60%"></th>     
                                {{-- <th></th>
                                <th></th>               --}}
                                <th align="right" width="30%"><label for="" style="font-size:13px;"><b>โรงพยาบาลภูเขียวเฉลิมพระเกียรติ</b></label></th>                  
                              </tr>  


                                  <tr style="font-size: 11px;height: 11px;color:rgb(65, 63, 63)" class="text-center">    
                                  
                                      <th rowspan="2" style="border: 1px solid rgb(250, 214, 159);width: 5%;background-color: rgb(252, 237, 219);color:#252424">
                                        ว/ด/ป 
                                      </th>
                                      <th rowspan="2" style="border: 1px solid rgb(250, 214, 159);width: 12%;background-color: rgb(252, 237, 219);color:#252424">
                                        รับจาก || จ่ายให้
                                          <!-- <span class="badge" style="background-color: rgb(7, 192, 152);font-size: 12px;">รับจาก</span>|| จ่ายให้ -->                                          
                                      </th>
                                      <th rowspan="2" style="border: 1px solid rgb(250, 214, 159);width: 5%;background-color: rgb(252, 237, 219);color:#252424">เลขที่เอกสาร</th> 
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
      
                            <tbody style="font-size: 11.2px">  
                                        
                                  <tr>    
                                        <td align="center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="5%">{{DateThai($item->recieve_date)}}</td>
                                        <td align="left" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;">&nbsp;รับจาก&nbsp;{{$item->supplies_namesub}}</td>
                                        <td align="center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="5%">{{$item->recieve_po_sup}}</td>
                                        <td align="center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="4%">{{$item->one_price}}</td>
                                        <td align="center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="4%">{{$item->qty}}</td> 
                                        <td align="center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="4%">-</td> 
                                        <td align="center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="4%">{{$item->total_allqty}}</td> 
                                        <td align="center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="4%">{{number_format($item->total_allprice, 2)}}</td> 
                                        <td align="center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="12%">LOT: {{$item->lot_no}}</td> 
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

                                        $counts = 1;
                                    @endphp              
                                    @forelse ($datashow2 as $item2)  
                                                                                  
                                              <tr>  
                                                  <td align="center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="5%">{{DateThai($item2->export_date)}}</td>
                                                  <td align="left" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;">&nbsp;จ่ายให้&nbsp;{{$item2->DEPARTMENT_SUB_SUB_NAME}}</td>
                                                  <td align="center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="5%">{{$item2->request_no}}</td>
                                                  <td align="center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="4%">{{$item2->one_price}}</td>
                                                  <td align="center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="4%">-</td> 
                                                  <td align="center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="4%">{{$item2->qty_pay}}</td>                                                 
                                                  <td align="center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="4%">
                                                      @if ($item2->total_stock == '0')                                                                
                                                          <span class="badge" style="font-size: 12px;">{{$item2->total_stock}}</span>
                                                      @else
                                                          {{$item2->total_stock}}
                                                      @endif 
                                                  </td> 
                                                  <td align="center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="4%">
                                                      @if ($item2->total_stock_price == '0')                                                                
                                                          <span class="badge" style="font-size: 12px;">{{number_format($item2->total_stock_price, 2)}}</span>
                                                      @else
                                                          {{number_format($item2->total_stock_price, 2)}}
                                                      @endif                                                                         
                                                  </td> 
                                                  <td align="center" style="border: 1px solid rgb(250, 232, 221);font-size: 11px;" width="12%">LOT: {{$item2->lot_no}}</td> 
                                              </tr>  
                                             
                                              @php $counts++; @endphp  
                                              {{-- <p>{{ $loop->iteration . '/' . $loop->count }}</p> --}}
                                              {{-- @if($loop->last)
                                                  <p>Our last element of the array</p>
                                              @endif --}}
                                              {{-- @if ($loop->count === '12')
                                              <p style="page-break-after: always;"></p>
                                              @endif --}}
                                              {{-- @if ($counts % 12 == 0)
                                                <div style="page-break-after: always;"></div>
                                              @endif --}}
                                              
                                      @empty
                                    @endforelse 
                           
                            </tbody>
                        </table> 
           <div class="page-break"></div> <!-- เพิ่มการแบ่งหน้าหลังจากหมวดหมู่ -->
                {{-- </div>      --}}
   
        @endforeach
 

{{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> --}}
{{-- <script type="text/php">
  if ( isset($pdf) ) {
      $pdf->page_text(65, 66, " {PAGE_NUM}",null, 10, array(255,0,0));
  }
</script> --}}
{{-- <script type="text/php">
  if ( isset($pdf) ) {
      $pdf->page_text(710, 20, "แผ่นที่: {PAGE_NUM} หน้าที่ {PAGE_NUM}",null, 10, array(255,0,0));
  }
</script> --}}

</body>

</html>
{{-- {PAGE_COUNT} --}}