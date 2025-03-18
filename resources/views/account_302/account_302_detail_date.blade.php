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

        <form action="{{ url('account_302_detail_date') }}" method="GET">
            @csrf
    <div class="row">
        <div class="col-md-4">

            <h4 class="card-title" style="color:rgb(248, 28, 83)">Detail 1102050101.302 </h4>
            <p class="card-title-desc">รายละเอียดข้อมูล ผัง 1102050101.302</p>
        </div>
            <div class="col"></div>
            <div class="col-md-1 text-end mt-2">วันที่</div>
            <div class="col-md-3 text-end">
                <div class="input-daterange input-group" id="datepicker1" data-date-format="dd M, yyyy" data-date-autoclose="true" data-provide="datepicker" data-date-container='#datepicker1'>
                    <input type="text" class="form-control-sm cardacc" name="startdate" id="datepicker" placeholder="Start Date" data-date-container='#datepicker1' data-provide="datepicker" data-date-autoclose="true" autocomplete="off"
                        data-date-language="th-th" value="{{ $startdate }}" required/>
                    <input type="text" class="form-control-sm cardacc" name="enddate" placeholder="End Date" id="datepicker2" data-date-container='#datepicker1' data-provide="datepicker" data-date-autoclose="true" autocomplete="off"
                        data-date-language="th-th" value="{{ $enddate }}"/>
                        <button type="submit" class="ladda-button btn-pill btn btn-sm btn-info cardacc">
                            <span class="ladda-label">
                                <img src="{{ asset('images/Search02.png') }}" class="me-2 ms-2" height="18px" width="18px">
                                ค้นหา</span>
                        </button>

                </div>
            </div>

        </div>
    </form>

    <div class="row">
        <div class="col-xl-12">
            <div class="card card_audit_4c" style="background-color: rgb(239, 247, 235)">

                    <div class="card-body">
                        {{-- <h4 class="card-title" style="color:rgb(10, 151, 85)">รายละเอียด 1102050101.302</h4> --}}
                        <div class="table-responsive">
                            {{-- <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;"> --}}
                                <table id="datatable-buttons" class="table table-sm table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead style="border: 1px solid rgb(250, 214, 159);">
                                    <tr>
                                        <th class="text-center">ลำดับ</th>
                                        <th class="text-center">an</th>
                                        {{-- <th class="text-center" >vn</th> --}}
                                        <th class="text-center" >hn</th>
                                        <th class="text-center" >cid</th>
                                        <th class="text-center">ptname</th>
                                        <th class="text-center">vstdate</th>
                                        <th class="text-center">dchdate</th>
                                        <th class="text-center">pttype</th>
                                        <th class="text-center">ลูกหนี้</th>

                                        <th class="text-center">รับชำระ</th>
                                        <th class="text-center">ส่วนต่าง</th>
                                        <th class="text-center">คงเหลือ</th>
                                        <th class="text-center">เลขที่ใบเสร็จ</th>
                                        <th class="text-center">ลงวันที่</th>
                                    </tr>
                                </thead>
                                <tbody style="border: 1px solid rgb(250, 214, 159);">
                                    <?php $number = 0; $total1 = 0; $total2 = 0;$total3 = 0;$total4 = 0;$total5 = 0;$total6 = 0;$total7 = 0;$total8 = 0;$total9 = 0;?>
                                    @foreach ($data as $item)
                                        <?php $number++; ?>

                                            <tr height="20" style="font-size: 14px;">
                                                <td class="text-font" style="text-align: center;" width="4%">{{ $number }}</td>
                                                <td class="text-center" width="8%">{{ $item->an }}</td>
                                                {{-- <td class="text-center" width="8%">{{ $item->vn }}</td> --}}
                                                <td class="text-center" width="6%">{{ $item->hn }}</td>
                                                <td class="text-center" width="10%">{{ $item->cid }}</td>
                                                <td class="text-start" >{{ $item->ptname }}</td>
                                                <td class="text-center" width="8%">{{ $item->vstdate }}</td>
                                                <td class="text-center" width="8%">{{ $item->dchdate }}</td>
                                                <td class="text-center" width="5%">{{ $item->pttype }}</td>
                                                <td class="text-center" style="color:rgb(6, 92, 190)" width="7%">{{ number_format($item->debit_total,2)}}</td>
                                                <td class="text-center" width="6%"></td>
                                                <td class="text-center" width="6%"></td>
                                                <td class="text-center" width="6%"></td>
                                                <td class="text-center" width="6%"></td>
                                                <td class="text-center" width="6%"></td>
                                            </tr>
                                            <?php
                                                $total1 = $total1 + $item->debit_total;
                                                // $total2 = $total2 + $item->rcpt_money;
                                                // $total3 = $total3 + $item->debit_total;
                                            ?>


                                    @endforeach

                                </tbody>
                                <tr style="background-color: #f3fca1">
                                    <td colspan="8" class="text-end" style="background-color: #ff9d9d"></td>
                                    <td class="text-center" style="background-color: #0759b8;colrgb(11, 104, 226) 0)">{{ number_format($total1,2)}}</td>
                                    <td colspan="5" class="text-end" style="background-color: #ff9d9d"></td>
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
