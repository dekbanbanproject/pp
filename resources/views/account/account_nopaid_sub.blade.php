@extends('layouts.account_new')
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
    {{-- <form action="{{ url('account_nopaid_sub') }}" method="GET">
        @csrf --}}
    <div class="row">
        <div class="col-md-5">
            <h4 class="card-title" style="color:rgba(21, 177, 164, 0.871)">   Detail Overdue OPD</h4>
            <p class="card-title-desc">รายละเอียดข้อมูล Visit ที่มีรายการค่าใช้จ่าย แต่ไม่มีการออกใบเสร็จ หรือลงค้าง OPD</p>
        </div>
        <div class="col"></div>

        {{-- <input type="hidden" id="monthss" name="monthss" value="{{$months}}"> --}}
        {{-- <input type="hidden" id="years" name="years" value="{{$year}}"> --}}

        {{-- <div class="col-md-4">
            <div class="input-daterange input-group" id="datepicker1" data-date-format="dd M, yyyy" data-date-autoclose="true" data-provide="datepicker" data-date-container='#datepicker6'>
                <input type="text" class="form-control cardfinan" name="startdate" id="datepicker" placeholder="Start Date"
                    data-date-container='#datepicker1' data-provide="datepicker" data-date-autoclose="true" autocomplete="off"
                    data-date-language="th-th" value="{{ $startdate }}" required/>
                <input type="text" class="form-control cardfinan" name="enddate" placeholder="End Date" id="datepicker2"
                    data-date-container='#datepicker1' data-provide="datepicker" data-date-autoclose="true" autocomplete="off"
                    data-date-language="th-th" value="{{ $enddate }}" required/>
                    <button type="submit" class="ladda-button btn-pill btn btn-sm btn-info cardfinan" data-style="expand-left">
                            <img src="{{ asset('images/Search02.png') }}" class="ms-2 me-2" height="23px" width="23px">
                            ค้นหา</span>
                    </button>
            </div>
        </div> --}}
    </div>
{{-- </form> --}}
        <div class="row ">
            <div class="col-md-12">
                <div class="card cardfinan" style="background-color: rgb(246, 235, 247)">
                    <div class="card-body">
                        <table id="example" class="table table-sm table-bordered dt-responsive" style="width: 100%;">
                        {{-- <table id="example" class="table table-sm table-striped table-bordered dt-responsive nowrap myTable" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> --}}
                        {{-- <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> --}}
                        {{-- <table id="example" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> --}}
                            <thead>
                                <tr>
                                    {{-- <th class="text-center">ลำดับ</th>  --}}
                                    {{-- <th class="text-center" >vn</th> --}}
                                    <th class="text-center" >hn</th>
                                    <th class="text-center" >cid</th>
                                    <th class="text-center">ptname</th>
                                    <th class="text-center">vstdate</th>
                                    <th class="text-center">pttype</th>
                                    <th class="text-center">แผนก</th>
                                    <th class="text-center">income</th>
                                    <th class="text-center">ลูกหนี้</th>
                                    <th class="text-center">ต้องชำระ</th>
                                    <th class="text-center">ชำระแล้ว</th>
                                    <th class="text-center">ค้างชำระ</th>
                                    <th class="text-center">ต้องลงค้าง</th>
                                    <th class="text-center">Finance No.</th>
                                    <th class="text-center">total_amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $number = 0;
                                 $total1 = 0;
                                    $total2 = 0;
                                    $total3 = 0;
                                    $total4 = 0; $total5 = 0;$total6 = 0;
                                     ?>
                                @foreach ($datashow as $item)
                                    <?php $number++; ?>

                                            <tr height="20" style="font-size: 13px;">
                                                <td class="text-center" width="4%">{{ $item->hn }}</td>
                                                <td class="text-center" width="6%">{{ $item->cid }}</td>
                                                <td class="text-start">{{ $item->ptname }}</td>
                                                <td class="text-center" width="6%">{{ $item->vstdate }}</td>
                                                <td class="text-center" width="4%">{{ $item->pttype }}</td>
                                                <td class="text-start" width="10%">{{ $item->department }}</td>
                                                <td class="text-center" style="color:rgb(83, 55, 243)" width="5%">{{ number_format($item->income,2)}}</td>
                                                <td class="text-center" style="color:rgb(83, 55, 243)" width="5%">{{ number_format($item->uc_money,2)}}</td>
                                                <td class="text-center" style="color:rgb(83, 55, 243)" width="5%">{{ number_format($item->paid_money,2)}}</td>
                                                <td class="text-center" style="color:rgb(83, 55, 243)" width="5%">{{ number_format($item->rcpt_money,2)}}</td>
                                                <td class="text-center" style="color:rgb(83, 55, 243)" width="5%">{{ number_format($item->remain_money,2)}}</td>

                                                @if (($item->income - $item->rcpt_money) == $item->remain_money)
                                                    {{-- <td class="text-end" style="background-color: rgb(192, 143, 255)" width="5%">0.00</td> --}}
                                                    <td class="text-center" style="background-color: rgb(143, 255, 218)" width="5%">0.00</td>
                                                @else
                                                    @if ($item->paid_money-$item->rcpt_money < '0')
                                                        {{-- <td class="text-end" style="background-color: aquamarine" width="5%">0.00</td> --}}
                                                        @if ($item->uc_money+$item->rcpt_money == $item->income)
                                                            {{-- <td class="text-end" style="background-color: rgb(248, 255, 143)" width="5%">0.00</td> --}}
                                                            <td class="text-end" style="background-color: rgb(143, 255, 218)" width="5%">0.00</td>
                                                        @else
                                                            <td class="text-end" style="background-color: rgb(143, 255, 218)" width="5%">0.00</td>
                                                        @endif
                                                    @else
                                                        <td class="text-center" style="color:rgb(255, 255, 255);font-size: 14px;background-color: rgb(245, 25, 91)" width="5%">{{ number_format($item->paid_money-$item->rcpt_money,2)}}</td>
                                                    @endif
                                                @endif

                                                @if ($item->book_number == '')
                                                <td class="text-center" width="7%"></td>
                                                @else
                                                <td class="text-center" width="7%">{{ $item->book_number }}/{{ $item->finance_number }}</td>
                                                @endif
                                                <td class="text-center" width="7%">{{ $item->total_amount }}</td>
                                            </tr>

                                            <?php
                                                $total1 = $total1 + $item->income;
                                                $total2 = $total2 + $item->uc_money;
                                                $total3 = $total3 + $item->paid_money;
                                                $total4 = $total4 + $item->rcpt_money;
                                                $total5 = $total5 + $item->remain_money;
                                                $total6 = $total6 + ($item->paid_money-$item->rcpt_money);
                                                // $total4 = ($total1 - $total3 );
                                            ?>
                                @endforeach

                            </tbody>
                            <tr style="background-color: #f3fca1">
                                <td colspan="6" class="text-end" style="background-color: #fca1a1"></td>
                                <td class="text-center" style="background-color: #9977f5"><label for="" style="color: #ffffff">{{ number_format($total1, 2) }}</label></td>
                                <td class="text-center" style="background-color: #9977f5" ><label for="" style="color: #ffffff">{{ number_format($total2, 2) }}</label></td>
                                <td class="text-center" style="background-color: #9977f5"><label for="" style="color: #ffffff">{{ number_format($total3, 2) }}</label> </td>
                                <td class="text-center" style="background-color: #9977f5"><label for="" style="color: #ffffff">{{ number_format($total4, 2) }}</label></td>
                                <td class="text-center" style="background-color: #9977f5"><label for="" style="color: #ffffff">{{ number_format($total5, 2) }}</label></td>
                                <td class="text-center" style="background-color: #9977f5"><label for="" style="color: #ffffff">{{ number_format($total6, 2) }}</label></td>
                                <td colspan="2" class="text-end" style="background-color: #fca1a1"></td>
                            </tr>
                        </table>
                </div>
                </div>
            </div>

        </div>
    </div>



@endsection
@section('footer')

    <script>
        $(document).ready(function() {
            var table = $('#example').DataTable({
                scrollY: '60vh',
                scrollCollapse: true,
                scrollX: true,
                "autoWidth": false,
                "pageLength": 10,
                "lengthMenu": [10,25,50,100,150,200,300,400,500],
        });

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
