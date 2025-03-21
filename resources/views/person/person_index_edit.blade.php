@extends('layouts.person_new')
@section('title', 'PK-OFFICE || บุคลากร')


@section('content')
    <script>
        function TypeAdmin() {
            window.location.href = '{{ route('index') }}';
        }

        function addpre() {
            var prenew = document.getElementById("PRE_INSERT").value;
            // alert(prenew);
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url: "{{ url('person/addpre') }}",
                method: "GET",
                data: {
                    prenew: prenew,
                    _token: _token
                },
                success: function(result) {
                    $('.show_pre').html(result);
                }
            })
        }

        function editpic(input) {
            var fileInput = document.getElementById('img');
            var url = input.value;
            var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
            if (input.files && input.files[0] && (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg")) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#edit_upload_preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                alert('กรุณาอัพโหลดไฟล์ประเภทรูปภาพ .jpeg/.jpg/.png/.gif .');
                fileInput.value = '';
                return false;
            }
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
        border-top: 10px #d22cf3 solid;
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
        <div class="row ">
            <div class="col-md-12">
                <div class="card card_audit_2b">
                    <div class="card-header ">

                        <h4 style="color:#f80d6f">แก้ไขข้อมูลบุคลากร</h4>
                    </div>
                    <div class="card-body">

                        <div id="progrss-wizard" class="twitter-bs-wizard">
                            <ul class="twitter-bs-wizard-nav nav-justified">
                                <li class="nav-item">
                                    <a href="#progress-seller-details" class="nav-link" data-toggle="tab">
                                        <span class="step-number">01</span>
                                        <span class="step-title">ข้อมูลส่วนตัว</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#progress-company-document" class="nav-link" data-toggle="tab">
                                        <span class="step-number">02</span>
                                        <span class="step-title">ข้อมูลอาชีพ</span>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="#progress-bank-detail" class="nav-link" data-toggle="tab">
                                        <span class="step-number">03</span>
                                        <span class="step-title">รูปภาพ</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#progress-confirm-detail" class="nav-link" data-toggle="tab">
                                        <span class="step-number">04</span>
                                        <span class="step-title">Confirm Detail</span>
                                    </a>
                                </li>

                            </ul>

                            <div id="bar" class="progress mt-4">
                                <div class="progress-bar bg-success progress-bar-striped progress-bar-animated"></div>
                            </div>
                            <div class="tab-content twitter-bs-wizard-tab-content">
                                <div class="tab-pane" id="progress-seller-details">

                                    <form action="{{ route('person.person_update') }}" method="POST" id="update_personForm"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input id="id" type="hidden" class="form-control" name="id"
                                            value="{{ $dataedits->id }}">

                                        <div class="row">
                                            <div class="col-lg-2">
                                                <div class="mb-3">
                                                    <label class="form-label" for="pname">คำนำหน้า :</label>
                                                    <select id="pname" name="pname"
                                                        class="form-control select2 show_pre" style="width: 100%">
                                                        <option value=""></option>
                                                        @foreach ($users_prefix as $pre)
                                                            @if ($dataedits->pname == $pre->prefix_id)
                                                                <option value="{{ $pre->prefix_id }}" selected>
                                                                    {{ $pre->prefix_name }} </option>
                                                            @else
                                                                <option value="{{ $pre->prefix_id }}">
                                                                    {{ $pre->prefix_name }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label class="form-label" for="fname" style="color: red">ชื่อ
                                                        :</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="fname" name="fname" value="{{ $dataedits->fname }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label class="form-label" for="lname" style="color: red">นามสกุล
                                                        :</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="lname" name="lname" value="{{ $dataedits->lname }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="mb-3">
                                                    <label class="form-label" for="cid">บัตรประชาชน</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="cid" name="cid" value="{{ $dataedits->cid }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-5">
                                                <div class="mb-3">
                                                    <label class="form-label" for="store_id"
                                                        style="color: red">โรงพยาบาล</label>
                                                        <select id="store_id" name="store_id" class="form-control " style="width: 100%">
                                                            <option value=""></option>
                                                            @foreach ($users_hos as $hos)
                                                            @if ($dataedits->store_id == $hos->users_hos_id)
                                                            <option value="{{ $hos->users_hos_id }}" selected> {{ $hos->users_hos_name }}</option>
                                                            @else
                                                            <option value="{{ $hos->users_hos_id }}"> {{ $hos->users_hos_name }}</option>
                                                            @endif

                                                            @endforeach
                                                        </select>

                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="mb-3">
                                                    <label class="form-label" for="username"
                                                        style="color: red">ชื่อผู้ใช้งาน</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="username" name="username" value="{{ $dataedits->username }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="mb-3">
                                                    <label class="form-label" for="password"
                                                        style="color: red">Password</label>
                                                    <input type="password" class="form-control form-control-sm"
                                                        id="password" name="password">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="mb-3">
                                                    <label class="form-label" for="line_token">Line Token</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="line_token" name="line_token" value="{{ $dataedits->line_token }}">
                                                    {{-- <textarea id="line_token" name="line_token" class="form-control" rows="2">{{ $dataedits->line_token }}</textarea> --}}
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="mb-3">
                                                    <label class="form-label" for="line_token">กลุ่ม P4P</label>
                                                    <select id="group_p4p" name="group_p4p"
                                                    class="form-select form-select-sm" style="width: 100%" >
                                                    <option value=""> </option>
                                                    @foreach ($p4p_work_position as $its)
                                                    @if ($dataedits->group_p4p == $its->p4p_work_position_id)
                                                    <option value="{{ $its->p4p_work_position_id }}" selected> {{ $its->p4p_work_position_code }}::{{ $its->p4p_work_position_name }} </option>
                                                    @else
                                                    <option value="{{ $its->p4p_work_position_id }}"> {{ $its->p4p_work_position_code }}::{{ $its->p4p_work_position_name }} </option>
                                                    @endif

                                                    @endforeach
                                                </select>

                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="mb-3">
                                                    <label class="form-label" for="staff">Staff hos</label>
                                                    <select id="staff" name="staff"
                                                    class="form-select form-select-sm" style="width: 100%" >
                                                    <option value=""> </option>
                                                    @foreach ($opduser as $its_u)
                                                    @if ($dataedits->staff == $its_u->loginname)
                                                    <option value="{{ $its_u->loginname }}" selected> {{ $its_u->name }}  </option>
                                                    @else
                                                    <option value="{{ $its_u->loginname }}"> {{ $its_u->name }} </option>
                                                    @endif

                                                    @endforeach
                                                </select>

                                                </div>
                                            </div>
                                        </div>


                                </div>
                                <div class="tab-pane" id="progress-company-document">
                                    <div>

                                        <div>

                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="basicpill-pancard-input"
                                                            style="color: red">กลุ่มงาน
                                                            :</label><select id="dep" name="dep_id"
                                                            class="form-control department" style="width: 100%">
                                                            <option value=""></option>
                                                            @foreach ($department as $depart)
                                                                @if ($dataedits->dep_id == $depart->DEPARTMENT_ID)
                                                                    <option value="{{ $depart->DEPARTMENT_ID }}" selected>
                                                                        {{ $depart->DEPARTMENT_NAME }}</option>
                                                                @else
                                                                    <option value="{{ $depart->DEPARTMENT_ID }}">
                                                                        {{ $depart->DEPARTMENT_NAME }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-4">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="basicpill-vatno-input"
                                                            style="color: red">ฝ่าย/แผนก
                                                            :</label><select id="depsub" name="dep_subid"
                                                            class="form-control department_sub" style="width: 100%">
                                                            <option value=""></option>
                                                            @foreach ($department_sub as $departsub)
                                                                @if ($dataedits->dep_subid == $departsub->DEPARTMENT_SUB_ID)
                                                                    <option value="{{ $departsub->DEPARTMENT_SUB_ID }}"
                                                                        selected>
                                                                        {{ $departsub->DEPARTMENT_SUB_NAME }}</option>
                                                                @else
                                                                    <option value="{{ $departsub->DEPARTMENT_SUB_ID }}">
                                                                        {{ $departsub->DEPARTMENT_SUB_NAME }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="basicpill-cstno-input"
                                                            style="color: red">หน่วยงาน
                                                            :</label> <select id="depsubsub" name="dep_subsubid"
                                                            class="form-control department_sub_sub" style="width: 100%">
                                                            <option value=""></option>
                                                            @foreach ($department_sub_sub as $departsubsub)
                                                                @if ($dataedits->dep_subsubid == $departsubsub->DEPARTMENT_SUB_SUB_ID)
                                                                    <option
                                                                        value="{{ $departsubsub->DEPARTMENT_SUB_SUB_ID }}"
                                                                        selected>
                                                                        {{ $departsubsub->DEPARTMENT_SUB_SUB_NAME }}
                                                                    </option>
                                                                @else
                                                                    <option
                                                                        value="{{ $departsubsub->DEPARTMENT_SUB_SUB_ID }}">
                                                                        {{ $departsubsub->DEPARTMENT_SUB_SUB_NAME }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="basicpill-servicetax-input"
                                                            style="color: red">หน่วยงานจริง :</label>
                                                        <select id="depsubsubtrue" name="dep_subsubtrueid"
                                                            class="form-control" style="width: 100%">
                                                            <option value=""></option>
                                                            @foreach ($department_sub_sub as $departsubsubtrue)
                                                                @if ($dataedits->dep_subsubtrueid == $departsubsubtrue->DEPARTMENT_SUB_SUB_ID)
                                                                    <option
                                                                        value="{{ $departsubsubtrue->DEPARTMENT_SUB_SUB_ID }}"
                                                                        selected>
                                                                        {{ $departsubsubtrue->DEPARTMENT_SUB_SUB_NAME }}
                                                                    </option>
                                                                @else
                                                                    <option
                                                                        value="{{ $departsubsubtrue->DEPARTMENT_SUB_SUB_ID }}">
                                                                        {{ $departsubsubtrue->DEPARTMENT_SUB_SUB_NAME }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-2">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="basicpill-companyuin-input"
                                                            style="color: red">ตำแหน่ง
                                                            :</label>
                                                        <select id="position" name="position_id" style="width: 100%">
                                                            <option value=""></option>
                                                            @foreach ($position as $item)
                                                                @if ($dataedits->position_id == $item->POSITION_ID)
                                                                    <option value="{{ $item->POSITION_ID }}" selected> {{ $item->POSITION_NAME }}</option>
                                                                @else
                                                                    <option value="{{ $item->POSITION_ID }}"> {{ $item->POSITION_NAME }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="basicpill-companyuin-input"
                                                            style="color: red">ระดับ
                                                            :</label>
                                                        <select id="user_level_id" name="user_level_id" style="width: 100%">
                                                            <option value=""></option>
                                                            @foreach ($user_level as $item_le)
                                                                @if ($dataedits->user_level_id == $item_le->user_level_id)
                                                                    <option value="{{ $item_le->user_level_id }}" selected> {{ $item_le->user_level_name }}</option>
                                                                @else
                                                                    <option value="{{ $item_le->user_level_id }}"> {{ $item_le->user_level_name }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-2">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="start_date"
                                                            style="color: red">วันที่บรรจุ
                                                            :</label>
                                                        <input id="start_date" type="date"
                                                            class="form-control form-control-sm datepicker"
                                                            name="start_date" value="{{ $dataedits->start_date }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="mb-3">
                                                        <label class="form-label"
                                                            for="basicpill-servicetax-input">วันที่ลาออก :</label>
                                                        <input id="end_date" type="date"
                                                            class="form-control form-control-sm datepicker"
                                                            name="end_date" value="{{ $dataedits->end_date }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-2">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="basicpill-companyuin-input"
                                                            style="color: red">สถานะทำงาน :</label>
                                                        <select id="statusA" name="status" class="form-control"
                                                            style="width: 100%">
                                                            <option value=""></option>
                                                            @foreach ($status as $st)
                                                                @if ($dataedits->status == $st->STATUS_ID)
                                                                    <option value="{{ $st->STATUS_ID }}" selected>
                                                                        {{ $st->STATUS_NAME }}</option>
                                                                @else
                                                                    <option value="{{ $st->STATUS_ID }}">
                                                                        {{ $st->STATUS_NAME }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-2">
                                                    <div class="mb-3">
                                                        <label class="form-label"
                                                            for="basicpill-companyuin-input">เงินเดือน :</label>
                                                        <input id="money" type="text"
                                                            class="form-control form-control-sm" name="money"
                                                            value="{{ $dataedits->money }}">
                                                    </div>
                                                </div>

                                                <div class="col-lg-4">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="start_date"
                                                            style="color: red">กลุ่มบุคลากร
                                                            :</label>
                                                        <select id="users_group_id" name="users_group_id"
                                                            class="form-control" style="width: 100%">
                                                            <option value=""></option>
                                                            @foreach ($users_group as $stu)
                                                                @if ($dataedits->users_group_id == $stu->users_group_id)
                                                                    <option value="{{ $stu->users_group_id }}" selected>
                                                                        {{ $stu->users_group_name }}</option>
                                                                @else
                                                                    <option value="{{ $stu->users_group_id }}">
                                                                        {{ $stu->users_group_name }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-4">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="users_type_id"
                                                            style="color: red">ประเภทข้าราชการ
                                                            :</label>
                                                        <select id="users_type_id" name="users_type_id"
                                                            class="form-control" style="width: 100%">
                                                            <option value=""></option>
                                                            @foreach ($users_kind_type as $st)
                                                                @if ($dataedits->users_type_id == $st->users_kind_type_id)
                                                                    <option value="{{ $st->users_kind_type_id }}"
                                                                        selected>
                                                                        {{ $st->users_kind_type_name }}</option>
                                                                @else
                                                                    <option value="{{ $st->users_kind_type_id }}">
                                                                        {{ $st->users_kind_type_name }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>

                                    </div>
                                </div>
                                <div class="tab-pane" id="progress-bank-detail">
                                    <div>

                                        @if ($dataedits->img == null)
                                            <img src="{{ asset('assets/images/default-image.jpg') }}"
                                                id="edit_upload_preview" height="300px" width="300px" alt="Image"
                                                class="img-thumbnail">
                                        @else
                                            <img src="{{ asset('storage/person/' . $dataedits->img) }}"
                                                id="edit_upload_preview" height="300px" width="300px" alt="Image"
                                                class="img-thumbnail">
                                        @endif
                                        <br>
                                        <div class="input-group mb-3 mt-3">

                                            <input type="file" class="form-control" id="img" name="img"
                                                onchange="editpic(this)">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        </div>


                                    </div>
                                </div>
                                <div class="tab-pane" id="progress-confirm-detail">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-6">
                                            <div class="text-center">
                                                <div class="mb-4">
                                                    <i class="mdi mdi-check-circle-outline text-success display-4"></i>
                                                </div>
                                                <div>
                                                    <h5>Confirm Detail</h5>
                                                    {{-- <button type="button" id="saveBtn" class="btn btn-primary btn-sm"> --}}
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        <i class="fa-solid fa-floppy-disk me-2"></i>
                                                        บันทึกข้อมูล
                                                    </button>
                                                    <a href="{{ url('person/person_index') }}"
                                                        class="btn btn-danger btn-sm">
                                                        <i class="fa-solid fa-xmark me-2"></i>
                                                        ยกเลิก
                                                    </a>
                                                    <p class="text-muted">ยืนยันการบันทึกข้อมูลบุคลากร</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <ul class="pager wizard twitter-bs-wizard-pager-link">
                                <li class="previous"><a href="javascript: void(0);">Previous</a></li>
                                <li class="next"><a href="javascript: void(0);">Next</a></li>
                            </ul>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->
    </div>



@endsection
@section('footer')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#group_p4p').select2({
                placeholder:"--เลือก--",
                allowClear:true
            });
            $('#dep').select2({
                placeholder:"--เลือก--",
                allowClear:true
            });
            $('#depsubsubtrue').select2({
                placeholder:"--เลือก--",
                allowClear:true
            });
            $('#depsub').select2({
                placeholder:"--เลือก--",
                allowClear:true
            });
            $('#depsubsub').select2({
                placeholder:"--เลือก--",
                allowClear:true
            });
            $('#staff').select2({
                placeholder:"--เลือก--",
                allowClear:true
            });

            $('#statusA').select2({
                placeholder:"--เลือก--",
                allowClear:true
            });
            $('#position').select2({
                placeholder:"--เลือก--",
                allowClear:true
            });
            $('#users_group_id').select2({
                placeholder:"--เลือก--",
                allowClear:true
            });

            $('#users_type_id').select2({
                placeholder:"--เลือก--",
                allowClear:true
            });

            $('#user_level_id').select2({
                placeholder:"--เลือก--",
                allowClear:true
            });

        });

        $(document).ready(function() {
            $('#update_personForm').on('submit', function(e) {
                e.preventDefault();
                //   alert('Person');
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
                            Swal.fire({
                                icon: 'error',
                                title: 'Username...!!',
                                text: 'Username นี้ได้ถูกใช้ไปแล้ว!',
                            }).then((result) => {
                                if (result.isConfirmed) {

                                }
                            })
                        } else {
                            Swal.fire({
                                title: 'แก้ไขข้อมูลสำเร็จ',
                                text: "You Edit data success",
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonColor: '#06D177',
                                confirmButtonText: 'เรียบร้อย'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location =
                                        "{{ route('person.person_index') }}"; //
                                }
                            })
                        }
                    }
                });
            });
        });
    </script>

@endsection
