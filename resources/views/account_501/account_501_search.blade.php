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
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="card-title" style="color:rgb(248, 28, 83)">Search Detail 1102050101.501</h4>
                        <form action="{{ route('acc.account_501_search') }}" method="GET">
                            @csrf
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <div class="input-daterange input-group" id="datepicker1" data-date-format="dd M, yyyy" data-date-autoclose="true" data-provide="datepicker" data-date-container='#datepicker6'>
                                            <input type="text" class="form-control-sm cardacc" name="startdate" id="datepicker" placeholder="Start Date"
                                                data-date-container='#datepicker1' data-provide="datepicker" data-date-autoclose="true" autocomplete="off"
                                                data-date-language="th-th" value="{{ $startdate }}" required/>
                                            <input type="text" class="form-control-sm cardacc" name="enddate" placeholder="End Date" id="datepicker2"
                                                data-date-container='#datepicker1' data-provide="datepicker" data-date-autoclose="true" autocomplete="off"
                                                data-date-language="th-th" value="{{ $enddate }}" required/>

                                                <button type="submit" class="ladda-button btn-pill btn btn-sm btn-info cardacc">
                                                    <span class="ladda-label">
                                                        <img src="{{ asset('images/Search02.png') }}" class="me-2 ms-2" height="18px" width="18px">
                                                        ค้นหา</span>
                                                </button>
                                        </div>
                                    </ol>
                                </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->
        </div> <!-- container-fluid -->

        <div class="row ">
            <div class="col-md-12">
                <div class="card card_audit_4c" style="background-color: rgb(246, 235, 247)">
                    {{-- <div class="card-header">
                       รายละเอียดตั้งลูกหนี้ผัง 1102050101.202
                        <div class="btn-actions-pane-right">
                        </div>
                    </div> --}}
                    <div class="card-body">
                        <div class="table-responsive">

                        <table id="datatable-buttons" class="table table-sm table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            {{-- <table id="example" class="table table-striped table-bordered "
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;"> --}}
                            <thead>
                                <tr>
                                    <th class="text-center">ลำดับ</th>
                                    {{-- <th class="text-center">stm_rcpno</th>   --}}
                                    <th class="text-center" >vn</th>
                                    <th class="text-center" >hn</th>
                                    <th class="text-center">ptname</th>
                                    <th class="text-center">vstdate</th>

                                    <th class="text-center">drug</th>
                                    <th class="text-center">inst</th>
                                    <th class="text-center">toa</th>
                                    <th class="text-center">refer</th>
                                    <th class="text-center">income</th>
                                    <th class="text-center">rcpt</th>
                                    <th class="text-center">ลูกหนี้</th>
                                    {{-- <th class="text-center">ส่วนต่าง</th>  --}}
                                    {{-- <th class="text-center">Stm 401</th>   --}}
                                    {{-- <th class="text-center">STMdoc</th>  --}}
                                </tr>
                            </thead>
                            <tbody>
                                <?php $number = 0;
                                $total1 = 0; $total2 = 0;$total3 = 0;$total4 = 0;$total5 = 0;$total6 = 0;$total7 = 0;$total8 = 0;$total9 = 0;
                                ?>
                                @foreach ($datashow as $item)
                                    <?php $number++; ?>
                                    <tr height="20" style="font-size: 14px;">
                                        <td class="text-font" style="text-align: center;" width="4%">{{ $number }}</td>
                                        {{-- <td class="text-center" width="5%">{{ $item->stm_rcpno }}</td>   --}}
                                        <td class="text-center" width="7%">{{ $item->vn }}</td>
                                        <td class="text-center" width="4%">{{ $item->hn }}</td>
                                        <td class="p-2" width="8%">{{ $item->ptname }}</td>
                                        <td class="text-center" width="6%">{{ $item->vstdate }}</td>

                                        <td class="text-end" style="color:rgb(155, 50, 18)" width="6%">{{ number_format($item->debit_drug,2)}}</td>
                                        <td class="text-end" style="color:rgb(155, 50, 18)" width="6%">{{ number_format($item->debit_instument,2)}}</td>
                                        <td class="text-end" style="color:rgb(155, 50, 18)" width="6%">{{ number_format($item->debit_toa,2)}}</td>
                                        <td class="text-end" style="color:rgb(155, 50, 18)" width="6%">{{ number_format($item->debit_refer,2)}}</td>
                                        <td class="text-end" style="color:rgb(18, 89, 155)" width="6%">{{ number_format($item->income,2)}}</td>
                                        <td class="text-end" style="color:rgb(100, 18, 155)" width="6%">{{ number_format($item->rcpt_money,2)}}</td>
                                        <td class="text-end" style="color:rgb(201, 15, 86)" width="6%">{{ number_format($item->debit_total,2)}}</td>
                                        {{-- <td class="text-end" style="color:rgb(184, 12, 169)" width="6%">{{ number_format(($item->debit_total-$item->stm_money),2)}}</td>  --}}
                                        {{-- <td class="text-end" style="color:rgb(6, 159, 170)" width="6%">{{ number_format($item->stm_money,2)}}</td>  --}}
                                        {{-- <td class="text-end" style="color:rgb(9, 196, 180)" width="6%">{{ number_format($item->stm_total,2)}}</td>   --}}
                                        {{-- <td class="p-2" width="9%">{{ $item->STMDoc }}</td>   --}}
                                    </tr>
                                        <?php
                                            $total1 = $total1 + $item->debit_drug;
                                            $total2 = $total2 + $item->debit_instument;
                                            $total3 = $total3 + $item->debit_toa;
                                            $total4 = $total4 + $item->debit_refer;
                                            $total5 = $total5 + $item->income;
                                            $total6 = $total6 + $item->rcpt_money;
                                            $total7 = $total7 + $item->debit_total;
                                            // $total7 = $total7 + ($item->debit_total-$item->stm_money);
                                            // $total8 = $total8 + $item->stm_money;
                                            // $total9 = $total9 + $item->stm_total;
                                        ?>
                                @endforeach

                            </tbody>
                                        <tr style="background-color: #f3fca1">
                                            <td colspan="5" class="text-end" style="background-color: #ff9d9d"></td>
                                            <td class="text-end" style="background-color: #f58d73;color: #f58d73">{{ number_format($total1,2)}}</td>
                                            <td class="text-end" style="background-color: #f58d73;color: #f58d73">{{ number_format($total2,2)}}</td>
                                            <td class="text-end" style="background-color: #f58d73;color: #f58d73">{{ number_format($total3,2)}}</td>
                                            <td class="text-end" style="background-color: #f58d73;color: #f58d73">{{ number_format($total4,2)}}</td>
                                            <td class="text-end" style="background-color: #084C7A;color: #084C7A">{{ number_format($total5,2)}}</td>
                                            <td class="text-end" style="background-color: #760880;color: #760880">{{ number_format($total6,2)}}</td>
                                            <td class="text-end" style="background-color: #C50D63;color: #C50D63">{{ number_format($total7,2)}}</td>
                                            {{-- <td class="text-end" style="background-color: #059b75;color: #FFFFFF">{{ number_format($total8,2)}}</td>   --}}
                                            {{-- <td class="text-end" style="background-color: #bbf0e3">{{ number_format($total9,2)}}</td>   --}}
                                            {{-- <td class="text-end" style="background-color: #ff9d9d"></td>  --}}
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

        });
    </script>
@endsection
