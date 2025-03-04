@extends('layouts.mobig')
@section('title', 'PK-OFFICE || Password')

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

    use Illuminate\Support\Facades\Crypt;
    use Illuminate\Contracts\Encryption\DecryptException;
 
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
        <div class="col-md-12">
            
            <div class="card card_audit_4c" style="background-color: rgb(248, 241, 237)">
                        <div class="card-body">                           
                            <div class="row"> 
                                <div class="col-xl-12"> 
                                        <table id="example" class="table table-sm table-striped table-bordered dt-responsive nowrap myTable" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead> 
                                            <tr style="font-size: 10px;">
                                                <th class="text-center" style="background-color: rgb(255, 251, 228);font-size: 11px;" width="5%">ลำดับ</th>
                                                <th class="text-start" style="background-color: rgb(255, 251, 228);font-size: 12px;">ชื่อ-นาสกุล</th>
                                                <th class="text-center" style="background-color: rgb(255, 251, 228);font-size: 11px;">username</th>
                                                <th class="text-center" style="background-color: rgb(255, 251, 228);font-size: 11px;">Password</th> 
                                                <th class="text-start" style="background-color: rgb(255, 251, 228);font-size: 11px;">Password Hash</th>  
                                            </tr> 
                                        </thead>
                                        <tbody>
                                            <?php $i = 0;$total1 = 0; $total2 = 0;$total3 = 0;$total4 = 0;$total5 = 0;$total6 = 0;$total7 = 0;$total8 = 0;$total9 = 0; ?>
                                            @foreach ($user as $item)
                                            <?php $i++                                             
                                                // $decrypt= Crypt::decrypt($item->password);  
                                                // $passhash = $item->password;
                                                // $passhash = password_verify($item->password);  
                                            ?>
                                            @php
                                                // $passhash = Auth::user()->getAuthPassword();
                                                // $decrypted = Crypt::decryptString($encryptedValue);
                                                $encrypted = Crypt::encryptString($item->password); 
                                                $passhash = Crypt::decryptString($encrypted);

                                                // Decrypt hash::make(password)
                                                // $decrypted = decrypt($item->password);
                                            @endphp
                                                <tr>
                                                    <td class="text-center" width="5%">{{$i}}</td>
                                                    <td class="text-start" width="10%">{{$item->fname}} || {{$item->lname}}</td>
                                                    <td class="text-center">{{$item->username}}</td>
                                                    <td class="text-start">{{$passhash}}</td> 
                                                    <td class="text-start">{{$item->password}}</td>                                          
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
