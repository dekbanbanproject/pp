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
        {{-- <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Detail 1102050101.302</h4>
    
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Detail</a></li>
                                <li class="breadcrumb-item active">1102050101.302</li>
                            </ol>
                        </div>
    
                    </div>
                </div>
            </div>
            <!-- end page title -->
        </div> <!-- container-fluid --> --}}
        <div class="row">
            <div class="col-md-4">
                <h5 class="card-title" style="color:rgb(248, 28, 83)">Detail 1102050101.302</h5>
                <p class="card-title-desc">รายละเอียดข้อมูล ผัง 1102050101.302</p>
            </div>
            <div class="col"></div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="mb-3 card card_audit_4c" style="background-color: rgb(239, 247, 235)">
                    {{-- <div class="card-header">
                    รายละเอียด 1102050101.302
                        <div class="btn-actions-pane-right">

                        </div>
                    </div> --}}
                    <div class="card-body">
                            {{-- <table id="example" class="table table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;"> --}}
                            <table id="datatable-buttons" class="table table-sm table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead style="border: 1px solid rgb(250, 214, 159);">
                                <tr>
                                    <th class="text-center">ลำดับ</th>
                                    {{-- <th class="text-center" width="5%">repno</th> --}}
                                    {{-- <th class="text-center">an</th> --}}
                                    <th class="text-center" >vn</th>
                                    <th class="text-center" >hn</th>
                                    <th class="text-center" >cid</th>
                                    <th class="text-center">ptname</th>

                                    <th class="text-center">vstdate</th>
                                    {{-- <th class="text-center">dchdate</th> --}}
                                    <th class="text-center">pttype</th>
                                    
                                    <th class="text-center">ลูกหนี้</th>
                                    {{-- <th class="text-center">ยอดชดเชย</th> --}}
                                </tr>
                            </thead>
                            <tbody style="border: 1px solid rgb(250, 214, 159);">
                                <?php $number = 0; ?>
                                @foreach ($data as $item)
                                    <?php $number++; ?>
                                   
                                        <tr height="20" style="font-size: 14px;">
                                            <td class="text-font" style="text-align: center;" width="4%">{{ $number }}</td> 
                                            {{-- <td class="text-center" width="10%">{{ $item->repno }}</td>   --}}
                                                    <td class="text-center" width="10%">{{ $item->vn }}</td> 
                                                    <td class="text-center" width="10%">{{ $item->hn }}</td>   
                                                    <td class="text-center" width="10%">{{ $item->cid }}</td>  
                                                    <td class="p-2" >{{ $item->ptname }}</td>  
                                                    <td class="text-center" width="10%">{{ $item->vstdate }}</td>    
                                                    <td class="text-center" width="10%">{{ $item->pttype }}</td> 
                                                    <td class="text-end" style="color:rgb(73, 147, 231)" width="7%">{{ number_format($item->debit_total,2)}}</td>
                                                    {{-- <td class="text-end" width="10%" style="color:rgb(216, 95, 14)"> 
                                                        {{ number_format($item->pricereq_all,2)}}   --}}
                                                </td>
                                        </tr>
                                        
                                    
 
                                @endforeach

                            </tbody>
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
