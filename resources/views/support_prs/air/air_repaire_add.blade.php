@extends('layouts.mobile')
@section('title', 'PK-OFFICE || Air-Service')

@section('content')

 

    <style>
        body {
            font-family: 'Kanit', sans-serif;
            font-size: 14px;
        }

        .cardfire {
            border-radius: 1em 1em 1em 1em;
            box-shadow: 0 0 15px pink;
            border: solid 1px #80acfd;
            /* box-shadow: 0 0 10px rgb(232, 187, 243); */
        }
    </style>
    <?php

    use SimpleSoftwareIO\QrCode\Facades\QrCode;

    ?>



    <div class="container-fluid mt-3">
        {{-- <div class="row text-center">
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
        </div> --}}
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
        <div class="row text-center">
            <div class="col"></div>
            <div class="col-md-6 text-center">
                <h2>ทะเบียนซ่อม-เครื่องปรับอากาศ</h2>
            </div>
            <div class="col"></div>

        </div>

        <div class="row mt-2">
            <div class="col-sm-12">
                <div class="card cardfire">
                    <div class="card-body">

                        <form action="{{ route('prs.air_repiare_save') }}" id="Insert_Form" method="POST" enctype="multipart/form-data">
                            @csrf

                                        <div class="row">
                                            <div class="col text-start">
                                                <p style="color:red">ส่วนที่ 1 : รายละเอียด </p>
                                            </div>
                                            <div class="col-6 text-end">
                                                <?php
                                                    $countqti_ = DB::select('SELECT COUNT(air_list_num) as air_list_num FROM air_repaire WHERE air_list_num = "'.$data_detail_->air_list_num.'"');
                                                    foreach ($countqti_ as $key => $value) {
                                                        $countqti = $value->air_list_num;
                                                    }
                                                ?>
                                                <p style="color:red">ซ่อมไปแล้ว {{$countqti}} ครั้ง</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col text-start">
                                                @if ($data_detail_->air_imgname == null)
                                                    <img src="{{ asset('assets/images/defailt_img.jpg') }}" height="190px" width="220px"
                                                        alt="Image" class="img-thumbnail">
                                                @else
                                                    <img src="{{ asset('storage/air/' . $data_detail_->air_imgname) }}" height="170px"
                                                        width="220px" alt="Image" class="img-thumbnail">
                                                @endif
                                            </div>
                                            <div class="col-7">
                                                <p>รหัส : {{ $data_detail_->air_list_num }}</p>
                                                <p>ชื่อ : {{ $data_detail_->air_list_name }}</p>
                                                <p>Btu : {{ $data_detail_->btu }}</p>
                                                <p>serial_no : {{ $data_detail_->serial_no }}</p>

                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col text-start"> <p>ที่ตั้ง : {{ $data_detail_->air_location_name }}</p> </div>
                                        </div>
                                        <div class="row">
                                            <div class="col text-start"> <p>หน่วยงาน : {{ $data_detail_->detail }}</p> </div>
                                        </div>
                                        <hr style="color:red">
                                        <div class="row">
                                            <div class="col text-start">
                                                <p style="color:red">ส่วนที่ 2 : ช่างซ่อม(นอก รพ.) </p>
                                            </div>
                                            <div class="col-5 text-start">
                                                <select class="custom-select custom-select-sm" id="air_type_id" name="air_type_id" style="width: 100%">
                                                    <option value="" >--เลือก--</option>
                                                    @foreach ($air_type as $item_t)
                                                        <option value="{{ $item_t->air_type_id }}" class="text-center">{{ $item_t->air_type_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-4 text-start">
                                                <p style="color:rgb(22, 61, 236)">เลขที่แจ้งซ่อม :  </p>
                                            </div>
                                            <div class="col-8 text-start">
                                                <select class="custom-select custom-select-sm" id="air_repaire_no" name="air_repaire_no" style="width: 100%">
                                                    <option value="maintenance" class="text-center">การบำรุงรักษาประจำปี</option>
                                                    @foreach ($air_no as $item_no)
                                                        <option value="{{ $item_no->REPAIR_ID }}" class="text-center">{{ $item_no->REPAIR_ID }} {{ $item_no->REPAIR_NAME }} / วันที่แจ้ง{{ $item_no->DATE_TIME_REQUEST }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                        <!-- ******************************** รายการซ่อม(ตามปัญหา) *****************************************  -->

                                        <div class="row">
                                            <div class="col text-start">
                                                <p style="color:rgb(9, 119, 209)">* รายการซ่อม(ตามปัญหา) </p>
                                            </div>
                                            <div class="col-5 text-start">
                                                <div class="input-group">
                                                    <input type="checkbox" class="discheckbox" id="air_pro_edit" name="air_pro_edit">
                                                    &nbsp;&nbsp;<p style="color:rgb(248, 61, 14)">แก้ไขงานเดิม</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            @foreach ($air_repaire_ploblem as $item_p)
                                                <div class="col-6 text-start">
                                                    <div class="input-group">
                                                        <input type="checkbox" class="discheckbox" id="air_problems" name="air_problems[]" value="{{$item_p->air_repaire_ploblem_id}}">
                                                        &nbsp;&nbsp;<p>{{$item_p->air_repaire_ploblemname}}</p>
                                                    </div>
                                                </div>
                                            @endforeach

                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="input-group">
                                                    <textarea class="form-control form-control-sm" id="air_problems_orthersub" name="air_problems_orthersub" rows="3"></textarea>
                                                </div>
                                            </div>
                                        </div>


                        <!-- ******************************** การบำรุงรักษา ประจำปี ครั้ง 1*****************************************  -->
                                        <hr style="color:rgb(7, 114, 141)">
                                        <div class="row">
                                            <div class="col text-start">
                                                <p style="color:rgb(9, 119, 209)">* การบำรุงรักษา ประจำปี </p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            @foreach ($air_maintenance_list as $item_ma)
                                                <div class="col-12">
                                                    <div class="input-group">
                                                        <input type="checkbox" class="discheckbox" id="maintenance_list_id" name="maintenance_list_id[]" value="{{$item_ma->maintenance_list_id}}">
                                                        &nbsp;&nbsp;<p>{{$item_ma->maintenance_list_name}} ครั้งที่ {{$item_ma->maintenance_list_num}}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>


                                        <hr style="color:rgb(7, 114, 141)">
                                        <div class="row">
                                            <div class="col text-start">
                                                <p>สถานะซ่อม :</p>
                                            </div>
                                            <div class="col-8">
                                                <select class="custom-select custom-select-sm" id="air_status_techout"
                                                    name="air_status_techout" style="width: 100%">
                                                    {{-- <option value="" class="text-center">- เลือก -</option> --}}
                                                    <option value="Y" class="text-center">- พร้อมใช้งาน -</option>
                                                    <option value="N" class="text-center">- ไม่พร้อมใช้งาน -</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col text-start">
                                                <p>ชื่อ-นามสกุล :</p>
                                            </div>
                                            <div class="col-8">
                                                <input type="hidden" class="form-control form-control-sm" id="air_techout_name" name="air_techout_name" value="{{$users_tech_out_id}}">
                                                <input type="text" class="form-control form-control-sm" id="" name="" value="{{$users_tech_out}}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            {{-- <div class="col"> </div> --}}
                                            <div class="col">
                                                <div id="signature-pad" class="mt-2 text-center">
                                                    <div style="border:solid 1px teal;height:120px;width: auto">
                                                        <div id="note" onmouseover="my_function();" class="text-center">The
                                                            signature should be inside box</div>
                                                        <canvas id="the_canvas" width="320px" height="120px"> </canvas>
                                                    </div>

                                                    <input type="hidden" id="signature" name="signature">

                                                    <button type="button" id="clear_btn" class="btn btn-secondary btn-sm mt-3 ms-3 me-3"
                                                        data-action="clear"><span class="glyphicon glyphicon-remove"></span>
                                                        Clear</button>

                                                    <button type="button" id="save_btn"
                                                        class="btn btn-info btn-sm mt-3 me-2 text-white" data-action="save-png"
                                                        onclick="create()"><span class="glyphicon glyphicon-ok"></span>
                                                        Create
                                                    </button>
                                                </div>
                                            </div>
                                            {{-- <div class="col"> </div> --}}
                                        </div>



                                        <hr style="color:red">
                                        <div class="row">
                                            <div class="col text-start">
                                                <p style="color:red">ส่วนที่ 3 : เจ้าหน้าที่ </p>
                                            </div>
                                            <div class="col-6"> </div>
                                        </div>
                                        <div class="row">
                                            <div class="col text-start">
                                                <p>สถานะซ่อม :</p>
                                            </div>
                                            <div class="col-8">
                                                <select class="custom-select custom-select-sm" id="air_status_staff"
                                                    name="air_status_staff" style="width: 100%">
                                                    {{-- <option value="" class="text-center">- เลือก -</option> --}}
                                                    <option value="Y" class="text-center">- พร้อมใช้งาน -</option>
                                                    <option value="N" class="text-center">- ไม่พร้อมใช้งาน -</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col text-start">
                                                <p>ชื่อ-นามสกุล :</p>
                                            </div>
                                            <div class="col-8">
                                                <select class="custom-select custom-select-sm" id="air_staff_id" name="air_staff_id"
                                                    style="width: 100%">
                                                    @foreach ($users as $item_u)
                                                        <option value="{{ $item_u->id }}" class="text-center">{{ $item_u->fname }}
                                                            {{ $item_u->lname }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            {{-- <div class="col"> </div> --}}
                                            <div class="col">
                                                <div id="signature-pad2" class="mt-2 text-center">
                                                    <div style="border:solid 1px teal;height:120px;width: auto">
                                                        <div id="note2" onmouseover="my_function2();" class="text-center">The
                                                            signature should be inside box</div>
                                                        <canvas id="the_canvas2" width="320px" height="120px"> </canvas>
                                                    </div>

                                                    <input type="hidden" id="signature2" name="signature2">
                                                    <button type="button" id="clear_btn2"
                                                        class="btn btn-secondary btn-sm mt-3 ms-3 me-3" data-action="clear2"><span
                                                            class="glyphicon glyphicon-remove"></span>
                                                        Clear</button>

                                                    <button type="button" id="save_btn2"
                                                        class="btn btn-info btn-sm mt-3 me-2 text-white" data-action="save-png2"
                                                        onclick="create2()"><span class="glyphicon glyphicon-ok"></span>
                                                        Create
                                                    </button>
                                                </div>
                                            </div>
                                            {{-- <div class="col"> </div> --}}
                                        </div>

                                        <hr style="color:red">
                                        <div class="row">
                                            <div class="col text-start">
                                                <p style="color:red">ส่วนที่ 4 : ช่างซ่อม(รพ.) </p>
                                            </div>
                                            <div class="col-6"> </div>
                                        </div>
                                        <div class="row">
                                            <div class="col text-start">
                                                <p>สถานะซ่อม :</p>
                                            </div>
                                            <div class="col-8">
                                                <select class="custom-select custom-select-sm" id="air_status_tech"
                                                    name="air_status_tech" style="width: 100%">
                                                    <option value="Y" class="text-center">- พร้อมใช้งาน -</option>
                                                    <option value="N" class="text-center">- ไม่พร้อมใช้งาน -</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col text-start">
                                                <p>ชื่อ-นามสกุล :</p>
                                            </div>
                                            <div class="col-8">
                                                {{-- <input type="text" class="form-control form-control-sm" id="air_tech_id" name="air_tech_id"> --}}
                                                <select class="custom-select custom-select-sm" id="air_tech_id" name="air_tech_id"
                                                    style="width: 100%">
                                                    @foreach ($air_tech as $item_u)
                                                        <option value="{{ $item_u->air_user_id }}" class="text-center">{{ $item_u->air_user_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            {{-- <div class="col"> </div> --}}
                                            <div class="col">
                                                <div id="signature-pad3" class="mt-2 text-center">
                                                    <div style="border:solid 1px teal;height:120px;width: auto;">
                                                        <div id="note3" onmouseover="my_function3();" class="text-center">The
                                                            signature should be inside box</div>
                                                        <canvas id="the_canvas3" width="320px" height="120px"> </canvas>
                                                    </div>

                                                    <input type="hidden" id="signature3" name="signature3">
                                                    <button type="button" id="clear_btn3"
                                                        class="btn btn-secondary btn-sm mt-3 ms-3 me-3" data-action="clear3"><span
                                                            class="glyphicon glyphicon-remove"></span>
                                                        Clear</button>

                                                    <button type="button" id="save_btn3"
                                                        class="btn btn-info btn-sm mt-3 me-2 text-white" data-action="save-png3"
                                                        onclick="create3()"><span class="glyphicon glyphicon-ok"></span>
                                                        Create
                                                    </button>

                                                </div>
                                            </div>
                                            {{-- <div class="col"> </div> --}}
                                        </div>

                                        <input type="hidden" name="air_list_id" id="air_list_id" value="{{ $data_detail_->air_list_id}}">
                                        <input type="hidden" name="air_list_num" id="air_list_num" value="{{ $data_detail_->air_list_num}}">
                                        <input type="hidden" name="air_list_name" id="air_list_name" value="{{ $data_detail_->air_list_name}}">
                                        <input type="hidden" name="btu" id="btu" value="{{ $data_detail_->btu}}">
                                        <input type="hidden" name="serial_no" id="serial_no" value="{{ $data_detail_->serial_no}}">
                                        <input type="hidden" name="air_location_id" id="air_location_id" value="{{ $data_detail_->air_location_id}}">
                                        <input type="hidden" name="air_location_name" id="air_location_name" value="{{ $data_detail_->air_location_name}}">
                                        <input type="hidden" name="detail" id="detail" value="{{ $data_detail_->detail}}">

                        <hr style="color:red">
                        <div class="row mt-3">
                            <div class="col text-center">
                                {{-- <button type="button" id="saveBtn" class="ladda-button btn-pill btn btn-success"> --}}
                                <button type="submit" class="ladda-button btn-pill btn btn-success">
                                    <i class="fa-solid fa-circle-check text-white me-2"></i>
                                    บันทึกข้อมูล
                                </button>
                            </div>
                        </div>


                        </form>
                    </div>
                </div>

            </div>
        </div>


    </div>

@endsection
@section('footer')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script src="{{ asset('js/gcpdfviewer.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('select').select2();
        });
    </script>
    <script>
        //ช่างซ่อมนอก
        var wrapper = document.getElementById("signature-pad");
        var clearButton = wrapper.querySelector("[data-action=clear]");
        var savePNGButton = wrapper.querySelector("[data-action=save-png]");
        var canvas = wrapper.querySelector("canvas");
        var el_note = document.getElementById("note");
        var signaturePad;
        signaturePad = new SignaturePad(canvas);
        clearButton.addEventListener("click", function(event) {
            document.getElementById("note").innerHTML = "The signature should be inside box";
            signaturePad.clear();
        });
        savePNGButton.addEventListener("click", function(event) {
            if (signaturePad.isEmpty()) {
                // alert("Please provide signature first.");
                Swal.fire(
                    'กรุณาลงลายเซนต์ก่อน !',
                    'You clicked the button !',
                    'warning'
                )
                event.preventDefault();
            } else {
                var canvas = document.getElementById("the_canvas");
                var dataUrl = canvas.toDataURL();
                document.getElementById("signature").value = dataUrl;

                // ข้อความแจ้ง
                Swal.fire({
                    title: 'สร้างสำเร็จ',
                    text: "You create success",
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonColor: '#06D177',
                    confirmButtonText: 'เรียบร้อย'
                }).then((result) => {
                    if (result.isConfirmed) {}
                })
            }
        });

        function my_function() {
            document.getElementById("note").innerHTML = "";
        }

        // เจ้าหน้าที่
        var wrapper = document.getElementById("signature-pad2");
        var clearButton2 = wrapper.querySelector("[data-action=clear2]");
        var savePNGButton2 = wrapper.querySelector("[data-action=save-png2]");
        var canvas2 = wrapper.querySelector("canvas");
        var el_note = document.getElementById("note2");
        var signaturePad2;
        signaturePad2 = new SignaturePad(canvas2);
        clearButton2.addEventListener("click", function(event) {
            document.getElementById("note2").innerHTML = "The signature should be inside box";
            signaturePad2.clear();
        });
        savePNGButton2.addEventListener("click", function(event) {
            if (signaturePad2.isEmpty()) {
                // alert("Please provide signature first.");
                Swal.fire(
                    'กรุณาลงลายเซนต์ก่อน !',
                    'You clicked the button !',
                    'warning'
                )
                event.preventDefault();
            } else {
                var canvas = document.getElementById("the_canvas2");
                var dataUrl = canvas.toDataURL();
                document.getElementById("signature2").value = dataUrl;

                // ข้อความแจ้ง
                Swal.fire({
                    title: 'สร้างสำเร็จ',
                    text: "You create success",
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonColor: '#06D177',
                    confirmButtonText: 'เรียบร้อย'
                }).then((result) => {
                    if (result.isConfirmed) {}
                })
            }
        });

        function my_function2() {
            document.getElementById("note2").innerHTML = "";
        }

        // ช่างซ่อมใน รพ
        var wrapper = document.getElementById("signature-pad3");
        var clearButton3 = wrapper.querySelector("[data-action=clear3]");
        var savePNGButton3 = wrapper.querySelector("[data-action=save-png3]");
        var canvas3 = wrapper.querySelector("canvas");
        var el_note = document.getElementById("note3");
        var signaturePad3;
        signaturePad3 = new SignaturePad(canvas3);
        clearButton3.addEventListener("click", function(event) {
            document.getElementById("note3").innerHTML = "The signature should be inside box";
            signaturePad3.clear();
        });
        savePNGButton3.addEventListener("click", function(event) {
            if (signaturePad3.isEmpty()) {
                // alert("Please provide signature first.");
                Swal.fire(
                    'กรุณาลงลายเซนต์ก่อน !',
                    'You clicked the button !',
                    'warning'
                )
                event.preventDefault();
            } else {
                var canvas = document.getElementById("the_canvas3");
                var dataUrl = canvas.toDataURL();
                document.getElementById("signature3").value = dataUrl;

                // ข้อความแจ้ง
                Swal.fire({
                    title: 'สร้างสำเร็จ',
                    text: "You create success",
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonColor: '#06D177',
                    confirmButtonText: 'เรียบร้อย'
                }).then((result) => {
                    if (result.isConfirmed) {}
                })
            }
        });

        function my_function3() {
            document.getElementById("note3").innerHTML = "";
        }
    </script>
    <script>
        $(document).ready(function() {
            $("#spinner-div").hide(); //Request is complete so hide spinner

            $('#saveBtn').click(function() {
                // alert('okkkkk');
                var air_problems_1     = $('#air_problems_1').val();
                var air_problems_2     = $('#air_problems_2').val();
                var air_problems_3     = $('#air_problems_3').val();
                var air_problems_4     = $('#air_problems_4').val();
                var air_problems_5     = $('#air_problems_5').val();
                var air_problems_6     = $('#air_problems_6').val();
                var air_problems_7     = $('#air_problems_7').val();
                var air_problems_8     = $('#air_problems_8').val();
                var air_problems_9     = $('#air_problems_9').val();
                var air_problems_10    = $('#air_problems_10').val();
                var air_problems_11    = $('#air_problems_11').val();
                var air_problems_12    = $('#air_problems_12').val();
                var air_problems_13    = $('#air_problems_13').val();
                var air_problems_14    = $('#air_problems_14').val();
                var air_problems_15    = $('#air_problems_15').val();
                var air_problems_16    = $('#air_problems_16').val();
                var air_problems_17    = $('#air_problems_17').val();
                var air_problems_18    = $('#air_problems_18').val();
                var air_problems_19    = $('#air_problems_19').val();
                var air_problems_20    = $('#air_problems_20').val();
                var air_status_techout = $('#air_status_techout').val();
                var air_techout_name   = $('#air_techout_name').val();
                var air_status_staff   = $('#air_status_staff').val();
                var air_staff_id       = $('#air_staff_id').val();
                var air_status_tech    = $('#air_status_tech').val();
                var air_tech_id        = $('#air_tech_id').val();
                var signature          = $('#signature').val(); //ช่างนอก
                var signature2         = $('#signature2').val(); //เจ้าหน้าที่
                var signature3         = $('#signature3').val(); //ช่าง รพ
                var air_list_id        = $('#air_list_id').val();
                var air_list_num       = $('#air_list_num').val();
                var air_list_name      = $('#air_list_name').val();
                var btu                = $('#btu').val();
                var serial_no          = $('#serial_no').val();
                var air_location_id    = $('#air_location_id').val();
                var air_location_name  = $('#air_location_name').val();
                var air_repaire_no     = $('#air_repaire_no').val();
                var air_problems_orther     = $('#air_problems_orther').val();
                var air_problems_orthersub  = $('#air_problems_orthersub').val();
                var air_num            = $('#air_num').val();
                Swal.fire({ position: "top-end",
                        title: 'ต้องการบันทึกข้อมูลใช่ไหม ?',
                        text: "You Warn Save Data!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Save it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $("#overlay").fadeIn(300);　
                                $("#spinner").show(); //Load button clicked show spinner

                                $.ajax({
                                    url: "{{ route('prs.air_repiare_save') }}",
                                    type: "POST",
                                    dataType: 'json',
                                    data: {
                                        "_token": "{{ csrf_token() }}",
                                       air_problems_1,air_problems_2,air_problems_3,air_problems_4,air_problems_5,air_problems_6,air_problems_7,air_problems_8
                                        ,air_problems_9,air_problems_10,air_problems_11,air_problems_12,air_problems_13,air_problems_14,air_problems_15,air_problems_16
                                        ,air_problems_17,air_problems_18,air_problems_19,air_problems_20,air_status_techout,air_techout_name,air_status_staff,air_staff_id
                                        ,air_status_tech,air_tech_id,signature,signature2,signature3,air_list_id,air_list_num,air_list_name,btu,serial_no,air_location_id,air_location_name,air_repaire_no,air_problems_orther,air_problems_orthersub,air_num
                                    },
                                    success: function(data) {
                                        if (data.status == 0) {

                                        } else if (data.status == 50) {
                                            Swal.fire(
                                                'กรุณาลงลายชื่อช่างภายนอก !',
                                                'You clicked the button !',
                                                'warning'
                                            )
                                        } else if (data.status == 60) {
                                            Swal.fire(
                                                'กรุณาลงลายชื่อเจ้าหน้าที่ !',
                                                'You clicked the button !',
                                                'warning'
                                            )
                                        } else if (data.status == 70) {
                                            Swal.fire(
                                                'กรุณาลงลายชื่อช่าง รพ !',
                                                'You clicked the button !',
                                                'warning'
                                            )
                                        } else {
                                            Swal.fire({
                                                title: 'บันทึกข้อมูลสำเร็จ',
                                                text: "You Insert data success",
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
                                        }
                                    },
                                });
                        }
                })
            });

            $('#Insert_Form').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                $.ajax({
                    url: $(form).attr('action'),
                    method: $(form).attr('method'),
                    data: new FormData(form),
                    processData: false,
                    dataType: 'json',
                    contentType: false,
                    beforeSend: function() {
                        $(form).find('span.error-text').text('');
                    },
                    success: function(data) {

                        if (data.status == 0) {

                        } else if (data.status == 50) {
                            Swal.fire(
                                'กรุณาลงลายชื่อช่างภายนอก !',
                                'You clicked the button !',
                                'warning'
                            )
                        } else if (data.status == 60) {
                            Swal.fire(
                                'กรุณาลงลายชื่อเจ้าหน้าที่ !',
                                'You clicked the button !',
                                'warning'
                            )
                        } else if (data.status == 70) {
                            Swal.fire(
                                'กรุณาลงลายชื่อช่าง รพ !',
                                'You clicked the button !',
                                'warning'
                            )
                        } else {
                            Swal.fire({
                                position: "top-end",
                                title: 'บันทึกข้อมูลสำเร็จ',
                                text: "You Insert data success",
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonColor: '#06D177',
                                confirmButtonText: 'เรียบร้อย'
                            }).then((result) => {
                                if (result
                                    .isConfirmed) {
                                    console.log(
                                        data);
                                    // window.location.reload();
                                    window.location = "{{ url('home_supplies_mobile') }}";
                                    $('#spinner')
                                .hide(); //Request is complete so hide spinner
                                    setTimeout(function() {
                                        $("#overlay").fadeOut(300);
                                    }, 500);
                                }
                            })
                        }
                    }
                });
            });

        });
    </script>
@endsection
