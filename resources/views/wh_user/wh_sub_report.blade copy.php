@extends('layouts.user_layout')
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


        <div class="app-main__outer">
            <div class="app-main__inner">

                <form action="{{ URL('wh_sub_report') }}" method="GET">
                    @csrf
                    <div class="row mt-2">
                        <div class="col-md-4">
                            <h4 style="color:rgb(2, 94, 148)">รายงานวัสดุคงเหลือ</h4>
                            {{-- <p style="font-size: 15px">รายงานวัสดุคงเหลือ</p> --}}
                        </div>

                        <div class="col"></div>
                        <div class="col-md-1 text-end mt-2">วันที่</div>
                        <div class="col-md-3 text-end">
                            <div class="input-daterange input-group" id="datepicker1" data-date-format="dd M, yyyy" data-date-autoclose="true" data-provide="datepicker" data-date-container='#datepicker1'>
                                <input type="text" class="form-control-sm card_prs_4" name="startdate" id="datepicker" placeholder="Start Date" data-date-container='#datepicker1' style="font-size: 12px"
                                    data-provide="datepicker" data-date-autoclose="true" autocomplete="off" data-date-language="th-th" value="{{ $startdate }}" required />
                                <input type="text" class="form-control-sm card_prs_4" name="enddate" placeholder="End Date" id="datepicker2" data-date-container='#datepicker1' style="font-size: 12px"
                                    data-provide="datepicker" data-date-autoclose="true" autocomplete="off" data-date-language="th-th" value="{{ $enddate }}" />
                                <button type="submit" class="ladda-button btn-pill btn btn-sm btn-info card_prs_4" data-style="expand-left">
                                        <img src="{{ asset('images/Search02.png') }}" class="ms-2 me-2" height="23px" width="23px">

                                        ค้นหา</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="row mt-2">
                    {{-- <div class="col"></div>  --}}

                        <div class="col-xl-12">
                            <div class="card card_prs_4" style="background-color: rgb(238, 252, 255)">
                                <div class="table-responsive p-2">
                                    <table id="example" class="table table-sm table-striped table-bordered" style="width: 100%;">
                                        {{-- dt-responsive nowrap myTable --}}
                                        <thead>
                                            <tr style="font-size: 12px">
                                                <th class="text-center" style="background-color: rgb(219, 247, 232);width: 5%">ลำดับ</th>
                                                <th class="text-center" style="background-color: rgb(140, 243, 188)">รายการ</th>
                                                <th class="text-center" style="background-color: rgb(245, 122, 85)">ประเภทวัสดุ</th>
                                                <th class="text-center" style="background-color: rgb(245, 122, 85);width: 7%">หน่วยนับ</th>
                                                {{-- <th colspan="3" class="text-center" style="background-color: rgb(252, 185, 246);width: 15%">ยอดยกมา</th>  --}}
                                                <th colspan="4" class="text-center" style="background-color: rgb(152, 245, 115);width: 20%">ขอเบิก</th>
                                                <th colspan="3" class="text-center" style="background-color: rgb(252, 144, 185);width: 15%">ตัดจ่าย</th>
                                                <th colspan="3" class="text-center" style="background-color: rgb(144, 203, 252);width: 15%">คงเหลือ</th>
                                            </tr>
                                            <tr style="font-size: 11px">
                                                <th class="text-center" style="background-color: rgb(219, 247, 232)"></th>
                                                <th class="text-center" style="background-color: rgb(140, 243, 188)"></th>
                                                <th colspan="2" class="text-center" style="background-color: rgb(245, 122, 85)"></th>
                                                {{-- <th class="text-center" style="background-color: rgb(250, 249, 230)">จำนวน </th>
                                                <th class="text-center" style="background-color: rgb(219, 247, 232)">ราคา/หน่วย </th>
                                                <th class="text-center" style="background-color: rgb(253, 236, 242)">จำนวนเงิน </th>  --}}
                                                <th class="text-center" style="background-color: rgb(204, 252, 185)">จำนวนเบิก </th>
                                                <th class="text-center" style="background-color: rgb(204, 252, 185)">จำนวนที่ได้ </th>
                                                <th class="text-center" style="background-color: rgb(204, 252, 185)">ราคา/หน่วย </th>
                                                <th class="text-center" style="background-color: rgb(204, 252, 185)">จำนวนเงิน </th>
                                                <th class="text-center" style="background-color: rgb(253, 205, 223)">จำนวน </th>
                                                <th class="text-center" style="background-color: rgb(253, 205, 223)">ราคา/หน่วย </th>
                                                <th class="text-center" style="background-color: rgb(253, 205, 223)">จำนวนเงิน </th>
                                                <th class="text-center" style="background-color: rgb(195, 225, 250)">จำนวน </th>
                                                <th class="text-center" style="background-color: rgb(195, 225, 250)">ราคา/หน่วย </th>
                                                <th class="text-center" style="background-color: rgb(195, 225, 250)">จำนวนเงิน </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $number = 0; $total1 = 0; $total2 = 0; $total3 = 0; $total4 = 0; $total5 = 0;$total6 = 0;$total7 = 0;
                                            ?>
                                            @foreach ($wh_stock_sub as $item)
                                                <?php $number++; ?>
                                                    <tr>
                                                        <td class="text-font" style="text-align: center;" width="4%">{{ $number }} </td>
                                                        <td class="text-start" style="font-size: 13px;color:#022f57">{{$item->pro_name}}</td>
                                                        <td class="text-start" style="font-size: 13px;color:rgb(245, 122, 85)" width="7%"> {{ $item->wh_type_name}}</td>
                                                        <td class="text-center" style="font-size: 13px;color:rgb(245, 122, 85)" width="5%">{{ $item->wh_unit_name}} </td>

                                                        <td class="text-center" style="font-size: 12px;color:rgb(54, 184, 3)"> {{ $item->request_qty}}</td>
                                                        <td class="text-center" style="font-size: 12px;color:rgb(54, 184, 3)"> {{ $item->request_qty_pay}}</td>
                                                        <td class="text-end" style="font-size: 12px;color:rgb(54, 184, 3)"> {{ $item->one_price}}</td>
                                                        <td class="text-end" style="font-size: 12px;color:rgb(54, 184, 3)">{{ $item->request_total_price}}</td>

                                                        <td class="text-center" style="font-size: 12px;color:rgb(235, 9, 95)">
                                                            @if ($item->stock_pay =='')
                                                                0
                                                            @else
                                                                {{ $item->stock_pay}}
                                                            @endif
                                                        </td>
                                                        <td class="text-end" style="font-size: 12px;color:rgb(235, 9, 95)">
                                                            @if ($item->stock_pay =='')
                                                                0.00
                                                            @else
                                                                {{ $item->one_price}}
                                                            @endif
                                                        </td>
                                                        <td class="text-end" style="font-size: 12px;color:rgb(235, 9, 95)">
                                                            @if ($item->stock_pay =='')
                                                                0.00
                                                            @else
                                                                {{ $item->stock_pay_total_price}}
                                                            @endif
                                                         </td>
                                                        <td class="text-center" style="font-size: 12px;color:rgb(29, 102, 185)"> {{ $item->request_qty_pay-$item->stock_pay}}</td>
                                                        <td class="text-end" style="font-size: 12px;color:rgb(29, 102, 185)"> {{ $item->one_price}}</td>
                                                        <td class="text-end" style="font-size: 12px;color:rgb(29, 102, 185)">{{ number_format($item->request_total_price-$item->stock_pay_total_price, 2)}} </td>

                                                    </tr>
                                              
                                            @endforeach

                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>

                    {{-- <div class="col"></div> --}}
                </div>

            </div>
        </div>
    </div>


    </div>

@endsection
@section('footer')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"> </script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
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

            var xmlhttp = new XMLHttpRequest();
            var url = "{{ route('acc.account_dashline') }}";
            xmlhttp.open("GET", url, true);
            xmlhttp.send();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var datas = JSON.parse(this.responseText);
                    console.log(datas);
                    label = datas.Dataset1.map(function(e) {
                        return e.label;
                    });

                    count_vn = datas.Dataset1.map(function(e) {
                        return e.count_vn;
                    });
                    income = datas.Dataset1.map(function(e) {
                        return e.income;
                    });
                    rcpt_money = datas.Dataset1.map(function(e) {
                        return e.rcpt_money;
                    });
                    debit = datas.Dataset1.map(function(e) {
                        return e.debit;
                    });
                     // setup
                    const data = {
                        // labels: ["ม.ค", "ก.พ", "มี.ค", "เม.ย", "พ.ย", "มิ.ย", "ก.ค","ส.ค","ก.ย","ต.ค","พ.ย","ธ.ค"] ,
                        labels: label ,
                        datasets: [
                            {
                                label: ['income'],
                                data: income,
                                fill: false,
                                borderColor: 'rgba(75, 192, 192)',
                                lineTension: 0.4
                            },
                            {
                                label: ['rcpt_money'],
                                data: rcpt_money,
                                fill: false,
                                borderColor: 'rgba(255, 99, 132)',
                                lineTension: 0.4
                            },

                        ]
                    };

                    const config = {
                        type: 'line',
                        data:data,
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        },
                        plugins:[ChartDataLabels],
                    };
                    // render init block
                    const myChart = new Chart(
                        document.getElementById('myChartNew'),
                        config
                    );

                }
             }

        });
    </script>


@endsection
