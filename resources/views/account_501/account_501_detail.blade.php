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
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">

                    <h4 class="card-title" style="color:rgb(248, 28, 83)">Detail 1102050101.501</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Detail</a></li>
                            <li class="breadcrumb-item active">1102050101.501</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card card_audit_4c" style="background-color: rgb(246, 235, 247)">
                    {{-- <div class="card-header">
                     รายละเอียด 1102050101.501
                        <div class="btn-actions-pane-right">

                        </div>
                    </div> --}}
                    <div class="card-body">
                            {{-- <table id="example" class="table table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;"> --}}
                            <table id="datatable-buttons" class="table table-sm table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center">ลำดับ</th>
                                    <th class="text-center" width="5%">vn</th>
                                    <th class="text-center" >hn</th>
                                    <th class="text-center" >cid</th>
                                    <th class="text-center">ptname</th>
                                    <th class="text-center">vstdate</th>
                                    <th class="text-center">pttype</th>
                                    <th class="text-center">hospmain</th>
                                    <th class="text-center">income</th>
                                    <th class="text-center">rcpt_money</th>
                                    <th class="text-center">ลูกหนี้</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $number = 0;
                                 $total1 = 0;
                                $total2 = 0;
                                $total3 = 0;
                                $total4 = 0;
                                ?>
                                @foreach ($data as $item)
                                    <?php $number++; ?>
                                    <tr height="20" style="font-size: 14px;">
                                        <td class="text-font" style="text-align: center;" width="4%">{{ $number }}</td>
                                        <td class="text-center" width="8%">{{ $item->vn }}</td>
                                        <td class="text-center" width="5%">{{ $item->hn }}</td>
                                        <td class="text-center" width="10%">{{ $item->cid }}</td>
                                        <td class="p-2" >{{ $item->ptname }}</td>
                                        <td class="text-center" width="8%">{{ $item->vstdate }}</td>
                                        <td class="text-center" width="5%">{{ $item->pttype }}</td>
                                        <td class="text-center" width="5%">{{ $item->hospmain }}</td>
                                        <td class="text-end" style="color:rgb(10, 104, 211)" width="10%">{{ number_format($item->income, 2) }}</td>
                                        <td class="text-end" style="color:rgb(6, 165, 157)" width="10%">{{ number_format($item->rcpt_money, 2) }}</td>
                                        <td class="text-end" style="color:rgb(5, 66, 136)" width="7%"> {{ number_format($item->debit_total, 2) }}</td>
                                    </tr>
                                    <?php
                                    $total1 = $total1 + $item->income;
                                    $total2 = $total2 + $item->rcpt_money;
                                    $total3 = $total3 + $item->debit_total;
                            ?>
                                @endforeach

                            </tbody>
                            <tr style="background-color: #f3fca1">
                                <td colspan="8" class="text-end" style="background-color: #fca1a1"></td>
                                <td class="text-end" style="background-color: #47A4FA"><label for="" style="color: #47A4FA">{{ number_format($total1, 2) }}</label></td>
                                <td class="text-end" style="background-color: #0395a0" ><label for="" style="color: #0395a0">{{ number_format($total2, 2) }}</label></td>
                                <td class="text-end" style="background-color: #063c79"><label for="" style="color: #063c79">{{ number_format($total3, 2) }}</label> </td>
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
