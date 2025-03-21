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

        <div class="row">
            <div class="col-md-12">
                <div class="card card_audit_4c" style="background-color: rgb(246, 235, 247)">
                    {{-- <div class="card-header">

                    <h4 class="card-title" style="color:rgb(247, 31, 95)">รายละเอียด 1102050102.106</h4>
                    </div> --}}
                    <div class="card-body">

                            {{-- <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> --}}
                                <table id="datatable-buttons" class="table table-sm table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center">ลำดับ</th>
                                    <th class="text-center" >vn</th>
                                    <th class="text-center" >hn</th>
                                    <th class="text-center" >cid</th>
                                    <th class="text-center">ptname</th>
                                    <th class="text-center">vstdate</th>
                                    <th class="text-center">pttype</th>
                                    <th class="text-center">pdx</th>
                                    <th class="text-center">income</th>
                                    <th class="text-center">discount_money</th>
                                    <th class="text-center">paid_money</th>
                                    <th class="text-center">ชำระแล้ว</th>
                                    <th class="text-center">ลูกหนี้</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $number = 0;
                                $total1 = 0;
                                $total2 = 0;
                                $total3 = 0;
                                $total4 = 0;
                                $total5 = 0;
                                ?>
                                @foreach ($data as $item)
                                    <?php $number++; ?>

                                        <tr height="20" style="font-size: 14px;">
                                            <td class="text-font" style="text-align: center;" width="4%">{{ $number }}</td>
                                            <td class="text-center" width="8%">{{ $item->vn }}</td>
                                            <td class="text-center" width="6%">{{ $item->hn }}</td>
                                            <td class="text-center" width="10%">{{ $item->cid }}</td>
                                            <td class="text-start">{{ $item->ptname }}</td>
                                            <td class="text-center" width="8%">{{ $item->vstdate }}</td>
                                            <td class="text-center" width="8%">{{ $item->pttype }}</td>
                                            <td class="text-center" style="color:rgb(238, 121, 25)" width="6%">{{ $item->pdx }}</td>
                                            <td class="text-center" style="color:rgb(73, 147, 231)" width="7%"> {{ number_format($item->income, 2) }}</td>
                                            <td class="text-center" style="color:rgb(73, 147, 231)" width="7%"> {{ number_format($item->discount_money, 2) }}</td>
                                            <td class="text-center" style="color:rgb(73, 147, 231)" width="7%"> {{ number_format($item->paid_money, 2) }}</td>
                                            <td class="text-center" style="color:rgb(73, 147, 231)" width="7%"> {{ number_format($item->rcpt_money, 2) }}</td>
                                            <td class="text-center" style="color:rgb(241, 61, 91)" width="7%"> {{ number_format($item->debit_total, 2) }}</td>
                                        </tr>

                                        <?php
                                                $total1 = $total1 + $item->income;
                                                $total2 = $total2 + $item->discount_money;
                                                $total3 = $total3 + $item->paid_money;
                                                $total4 = $total4 + $item->rcpt_money;
                                                $total5 = $total5 + $item->debit_total;
                                        ?>

                                @endforeach

                            </tbody>
                            <tr style="background-color: #f3fca1">
                                <td colspan="8" class="text-end" style="background-color: #fca1a1"></td>
                                <td class="text-center" style="background-color: #47A4FA"><label for="" style="color: #ff2667">{{ number_format($total1, 2) }}</label></td>
                                <td class="text-center" style="background-color: #FCA533" ><label for="" style="color: #ff2667">{{ number_format($total2, 2) }}</label></td>
                                <td class="text-center" style="background-color: #e98644"><label for="" style="color: #ff2667">{{ number_format($total3, 2) }}</label> </td>
                                <td class="text-center" style="background-color: #44E952"><label for="" style="color: #ff2667">{{ number_format($total4, 2) }}</label> </td>
                                <td class="text-center" style="background-color: #4491e9"><label for="" style="color: #ff2667">{{ number_format($total5, 2) }}</label> </td>
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
            $('.PulldataAll').click(function() {
                    var months = $('#months').val();
                    var year = $('#year').val();
                    // alert(startdate);
                    Swal.fire({
                            title: 'ต้องการซิ้งค์ข้อมูลใช่ไหม ?',
                            text: "You Sync Data!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, Sync it!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $("#overlay").fadeIn(300);　
                                    $("#spinner").show();

                                    $.ajax({
                                        url: "{{ url('account_309_syncall') }}",
                                        type: "POST",
                                        dataType: 'json',
                                        data: {months,year},
                                        success: function(data) {
                                            if (data.status == 200) {
                                                Swal.fire({
                                                    title: 'ซิ้งค์ข้อมูลสำเร็จ',
                                                    text: "You Sync data success",
                                                    icon: 'success',
                                                    showCancelButton: false,
                                                    confirmButtonColor: '#06D177',
                                                    confirmButtonText: 'เรียบร้อย'
                                                }).then((result) => {
                                                    if (result
                                                        .isConfirmed) {
                                                        console.log(
                                                            data);
                                                        window.location.reload();
                                                        $('#spinner').hide();//Request is complete so hide spinner
                                                            setTimeout(function(){
                                                                $("#overlay").fadeOut(300);
                                                            },500);
                                                    }
                                                })

                                            } else if (data.status == 100) {
                                                Swal.fire({
                                                    title: 'ยังไม่ได้ลงเลขที่หนังสือ',
                                                    text: "Please enter the number of the book.",
                                                    icon: 'warning',
                                                    showCancelButton: false,
                                                    confirmButtonColor: '#06D177',
                                                    confirmButtonText: 'เรียบร้อย'
                                                }).then((result) => {
                                                    if (result
                                                        .isConfirmed) {
                                                        console.log(
                                                            data);
                                                        window.location.reload();

                                                    }
                                                })

                                            } else {

                                            }
                                        },
                                    });

                                }
                    })
            });

        });
    </script>
@endsection
