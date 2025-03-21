@extends('layouts.person_new')
@section('title', 'PK-OFFICE || บัญชีรายงานวันลา')
@section('content')
    <script>
        function TypeAdmin() {
            window.location.href = '{{ route('index') }}';
        }
    </script>
    <style>
        .table th {
            font-family: sans-serif;
            font-size: 12px;
        }

        /* .table td {
                font-family: sans-serif;
                font-size: 12px;
            } */
        table,
        tr,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
        }
    </style>
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
            <div class="col-xl-12">
                <form action="{{ url('report_type') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col"></div>
                        <div class="col-md-1 text-end">วันที่</div>
                        <div class="col-md-2 text-center">
                            <div class="input-group" id="datepicker1">
                                <input type="text" class="form-control" placeholder="yyyy-mm-dd" name="startdate"
                                    id="startdate" data-date-format="yyyy-mm-dd" data-date-container='#datepicker1'
                                    data-provide="datepicker" data-date-autoclose="true" value="{{ $startdate }}">

                                <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                            </div>
                        </div>
                        <div class="col-md-1 text-center">ถึงวันที่</div>
                        <div class="col-md-2 text-center">
                            <div class="input-group" id="datepicker1">
                                <input type="text" class="form-control" placeholder="yyyy-mm-dd" name="enddate"
                                    id="enddate" data-date-format="yyyy-mm-dd" data-date-container='#datepicker1'
                                    data-provide="datepicker" data-date-autoclose="true" value="{{ $enddate }}">
                                <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                            </div>
                        </div>

                        <div class="col-md-1 text-center">ประเภทการจ้าง</div>
                        <div class="col-md-2 text-center">
                            <div class="input-group">
                                <select type="text" class="form-control" name="HR_PERSON_TYPE_ID" id="HR_PERSON_TYPE_ID">
                                    <option value="">--เลือก--</option>
                                    @foreach ($persontype as $item2)
                                    @if ($deptype == $item2->HR_PERSON_TYPE_ID)
                                    <option value="{{$item2->HR_PERSON_TYPE_ID}}" selected>{{$item2->HR_PERSON_TYPE_NAME}}</option>
                                    @else
                                    <option value="{{$item2->HR_PERSON_TYPE_ID}}">{{$item2->HR_PERSON_TYPE_NAME}}</option>
                                    @endif

                                    @endforeach
                                </select>

                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-magnifying-gla
                            ss me-2"></i>
                                ค้นหา
                            </button>
                        </div>
                        <div class="col"></div>
                </form>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <h5 class="mb-sm-0">บัญชีรายงานวันลา ประจำเดือน โรงพยาบาลภูเขียวเฉลิมพระเกียรติ</h5>
        <div class="col-xl-12 mt-2">
            <div class="card">
                <div class="card-body py-0 px-2 mt-2">
                    <div class="table-responsive">
                        {{-- <table class="table table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;"> --}}
                             <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                            <tr>
                                <th rowspan="2" style="text-align: center">ลำดับ</th>
                                <th rowspan="2" style="text-align: center">ชื่อ-สกุล</th>
                                <th colspan="3" style="text-align: center">ลาป่วย</th>
                                <th colspan="3" style="text-align: center">ลากิจ</th>
                                <th colspan="6" style="text-align: center">ลาพักผ่อน</th>
                                <th rowspan="2" style="text-align: center">หมายเหตุ</th>

                            </tr>
                            <tr>
                                <td style="text-align: center">จำนวนวันที่ลา</td>
                                <td style="text-align: center">ตั้งแต่วันที่-วันที่</td>
                                <td style="text-align: center"> รวม</td>
                                <td style="text-align: center">จำนวนวันที่ลา(กิจ)</td>
                                <td style="text-align: center">ตั้งแต่วันที่-วันที่(กิจ)</td>
                                <td style="text-align: center">รวม(กิจ)</td>
                                <td style="text-align: center">วันลาสะสม</td>
                                <td style="text-align: center">รวมมีสิทธิลาทั้งสิ้นในปีนี้</td>
                                <td style="text-align: center">จำนวนวันที่ลา(พักผ่อน)</td>
                                <td style="text-align: center">ตั้งแต่วันที่-วันที่(พักผ่อน)</td>
                                <td style="text-align: center">รวม(พักผ่อน)</td>
                                <td style="text-align: center">คงเหลือ</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            @foreach ($datashow as $item)
                                <tr>
                                    <td style="text-align: center">{{ $i++ }}</td>
                                    {{-- <td>{{ $item->HR_DEPARTMENT_NAME }}</td> --}}
                                    <td style="text-align: center">{{ $item->LEAVE_PERSON_FULLNAME }}</td>
                                    <td style="text-align: center">2021-10-01</td>
                                    <td style="text-align: center">2</td>
                                    <td style="text-align: center">{{$item->LEAVE_DATE_SUM}}</td>
                                    <td style="text-align: center">4</td>
                                    <td style="text-align: center">5</td>
                                    <td style="text-align: center">6</td>
                                    <td style="text-align: center">7</td>
                                    <td style="text-align: center">8</td>
                                    <td style="text-align: center">9</td>
                                    <td style="text-align: center"> 10</td>
                                    <td style="text-align: center">11</td>
                                    <td style="text-align: center">12</td>
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





@endsection
@section('footer')
    <script>
        $(document).ready(function() {
            $('#example').DataTable();
            $('#example2').DataTable();

            $('select').select2();
            $('#ECLAIM_STATUS').select2({
                dropdownParent: $('#detailclaim')
            });
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

        });
    </script>

@endsection
