@extends('layouts.support_prs_fireback')
@section('title', 'PK-OFFICE || Fire-Service')

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

<div class="tabs-animation">

    <div id="preloader">
        <div id="status">
            <div id="container_spin">
                <svg viewBox="0 0 100 100">
                    <defs>
                        <filter id="shadow">
                        <feDropShadow dx="0" dy="0" stdDeviation="2.5"
                            flood-color="#fc6767"/>
                        </filter>
                    </defs>
                    <circle id="spinner" style="fill:transparent;stroke:#dd2476;stroke-width: 7px;stroke-linecap: round;filter:url(#shadow);" cx="50" cy="50" r="45"/>
                </svg>
            </div>
        </div>
    </div>
    <form action="{{ url('air_setting') }}" method="GET">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <h4 style="color:rgb(255, 255, 255)">ยกยอดจำนวนทั้งหมด(รายเดือน)</h4>
            </div>

            <div class="col"></div>

            <div class="col-md-4 text-end">
                <button type="button" class="ladda-button btn-pill btn btn-info bt_prs" data-bs-toggle="modal" target="_blank" data-bs-target="#exampleModal">
                    <i class="fa-solid fa-circle-plus text-white me-2"></i>
                    <span>ทำรายการ</span>
                </button>

            </div>
        </div>
    </form>

        <div class="row mt-2">
            <div class="col-xl-12">
                <div class="card card_prs_4">
                    <div class="card-body">
                        <p class="mb-0">
                            <div class="table-responsive">
                                <table id="example" class="table table-hover table-sm" style=" border-spacing: 0; width: 100%;">
                                    <thead>
                                            <tr style="font-size:13px">
                                                <th class="text-center" width="5%">ลำดับ</th>
                                                <th class="text-center" width="7%">ปี พ.ศ.</th>
                                                <th class="text-center" width="10%">เดือน</th>
                                                <th class="text-center" width="10%">ทั้งหมด</th>
                                                <th class="text-center" width="10%">สำรองทั้งหมด</th>

                                                <th class="text-center" width="7%">Red-10</th>
                                                <th class="text-center" width="7%">Red-15</th>
                                                <th class="text-center" width="7%">Red-20</th>
                                                <th class="text-center" width="7%">Green-10</th>
                                                <th class="text-center" width="7%">Green-15</th>
                                                <th class="text-center" width="7%">Green-20</th>

                                            </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 0; ?>
                                        @foreach ($fire_stock_month as $item)
                                        <?php $i++  ?>
                                            <tr id="sid{{ $item->fire_stock_month_id }}">
                                                <td class="text-center" style="font-size:13px;width: 5%;color: rgb(13, 134, 185)">{{$i}}</td>
                                                <td class="text-center" style="font-size:14px;color: rgb(2, 95, 182)" width="7%">{{$item->years_th}}</td>
                                                <td class="text-start" style="font-size:14px;color: rgb(2, 95, 182)">{{$item->month_name}}</td>
                                                <td class="text-center" style="font-size:13px;color: rgb(5, 160, 139)" width="7%">{{$item->total_all_qty}} </td>
                                                <td class="text-center" style="font-size:13px;color: rgb(253, 65, 81)" width="7%">
                                                    {{$item->total_backup_r10+$item->total_backup_r15+$item->total_backup_r20+$item->total_backup_g10+$item->total_backup_g15+$item->total_backup_g20}}
                                                </td>
                                                <td class="text-center" style="font-size:14px;color: rgb(5, 127, 131)" width="5%">{{$item->total_red10}}</td>
                                                <td class="text-center" style="font-size:14px;color: rgb(5, 127, 131)" width="5%">{{$item->total_red15}}</td>
                                                <td class="text-center" style="font-size:14px;color: rgb(5, 127, 131)" width="5%">{{$item->total_red20}}</td>
                                                <td class="text-center" style="font-size:14px;color: rgb(5, 127, 131)" width="7%">{{$item->total_green10}}</td>
                                                <td class="text-center" style="font-size:14px;color: rgb(5, 127, 131)" width="7%">{{$item->total_green15}}</td>
                                                <td class="text-center" style="font-size:14px;color: rgb(5, 127, 131)" width="7%">{{$item->total_green20}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </p>

                    </div>
                </div>
            </div>


        </div>

         <!-- exampleModal Modal -->
         <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">

                            <br>
                                <div class="container">
                                    <div class="row">
                                        <h3 class="text-center">ยกยอดจำนวนทั้งหมด(รายเดือน)</h3><br>
                                        <h4 class="text-center">ปีงบประมาณ {{$bg_yearnow}}</h4>
                                        <div class="col-md-6">
                                            <p style="font-size: 14px">เดือน</p>
                                            <div class="form-group">
                                                <div class="input-group mt-3">
                                                    <select name="month_no1" id="month_no1" class="form-control" style="width: 100%">
                                                        @foreach ($months_data as $itemm)
                                                        <option value="{{$itemm->month_no}}">{{$itemm->month_name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <p style="font-size: 14px">ปีงบประมาณ</p>
                                            <div class="form-group">
                                                <div class="input-group mt-3">
                                                    <select name="leave_year_id1" id="leave_year_id1" class="form-control" style="width: 100%">
                                                        @foreach ($yearsshow as $item1)
                                                        @if ($bg_yearnow == $item1->leave_year_id)
                                                            <option value="{{$item1->leave_year_id}}" selected>{{$item1->leave_year_id}}</option>
                                                        @else
                                                            <option value="{{$item1->leave_year_id}}">{{$item1->leave_year_id}}</option>
                                                        @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col"></div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <button type="button" class="ladda-button btn-pill btn btn-success bt_prs" id="Copydatato">
                                                    <i class="fa-regular fa-clone text-white me-2"></i>
                                                    ยกยอดไป
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col"></div>
                                    </div>

                                </div>
                            <br>

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

            $('#air_plan_month_id').select2({
                dropdownParent: $('#exampleModal')
            });

            $('#insert_Form').on('submit',function(e){
                  e.preventDefault();

                  var form = this;
                    //   alert('OJJJJOL');
                  $.ajax({
                    url:$(form).attr('action'),
                    method:$(form).attr('method'),
                    data:new FormData(form),
                    processData:false,
                    dataType:'json',
                    contentType:false,
                    beforeSend:function(){
                      $(form).find('span.error-text').text('');
                    },
                    success:function(data){
                      if (data.status == 0 ) {

                      } else {
                        Swal.fire({
                          position: "top-end",
                          title: 'นำเข้าข้อมูลสำเร็จ',
                          text: "You Import data success",
                          icon: 'success',
                          showCancelButton: false,
                          confirmButtonColor: '#06D177',
                          // cancelButtonColor: '#d33',
                          confirmButtonText: 'เรียบร้อย'
                        }).then((result) => {
                          if (result.isConfirmed) {
                            window.location.reload();
                            // window.location="{{url('air_main')}}";
                          }
                        })
                      }
                    }
                  });
            });

            $("#spinner-div").hide(); //Request is complete so hide spinner


            $('#Sendata').click(function() {
                // var datepicker = $('#datepicker').val();
                // var datepicker2 = $('#datepicker2').val();
                var datepicker = '1';
                var datepicker2 = '2';
                Swal.fire({
                    position: "top-end",
                        title: 'คุณต้องการนำเข้าข้อมูลใช่ไหม ?',
                        text: "You Import Data!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Import it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $("#overlay").fadeIn(300);　
                                $("#spinner").show(); //Load button clicked show spinner

                                $.ajax({
                                    url: "{{ route('prs.importplan_send') }}",
                                    type: "POST",
                                    dataType: 'json',
                                    data: {
                                        datepicker,
                                        datepicker2
                                    },
                                    success: function(data) {
                                        if (data.status == 200) {
                                            Swal.fire({
                                                position: "top-end",
                                                title: 'นำเข้าข้อมูลสำเร็จ',
                                                text: "You Import data success",
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
                                        } else {

                                        }
                                    },
                                });

                            }
                })
            });

            $('#Copydatato').click(function() {
                var month_no1      = $('#month_no1').val();
                var leave_year_id1 = $('#leave_year_id1').val();
                Swal.fire({
                    position: "top-end",
                        title: 'คุณต้องการยกยอดใช่ไหม ?',
                        text: "You Carry Data!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Carry it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $("#overlay").fadeIn(300);　
                                $("#spinner").show(); //Load button clicked show spinner

                                $.ajax({
                                    url: "{{ route('prs.fire_stock_month_save') }}",
                                    type: "POST",
                                    dataType: 'json',
                                    data: {month_no1,leave_year_id1},
                                    success: function(data) {
                                        if (data.status == 200) {
                                            Swal.fire({
                                                position: "top-end",
                                                title: 'ยกยอดสำเร็จ',
                                                text: "You Carry data success",
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


