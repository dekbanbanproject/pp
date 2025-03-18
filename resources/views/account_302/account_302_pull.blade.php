@extends('layouts.accountpk')
@section('title', 'PK-OFFICE || ACCOUNT')

@section('content')
    <script>
        function TypeAdmin() {
            window.location.href = '{{ route('index') }}';
        }
        // function account_302_destroy(d_abillitems_id) {
        //     Swal.fire({
        //         title: 'ต้องการลบข้อมูลใช่ไหม?',
        //         text: "ข้อมูลนี้จะถูกลบ",
        //         icon: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: '#3085d6',
        //         cancelButtonColor: '#d33',
        //         confirmButtonText: 'ใช่ ',
        //         cancelButtonText: 'ไม่'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $.ajax({
        //                 url: "{{ url('account_302_destroy') }}" + '/' + d_abillitems_id,
        //                 type: "GET",
        //                 data: {
        //                     _token: $("input[name=_token]").val()
        //                 },
        //                 success: function(response) {
        //                     if (response == 200) {
        //                         Swal.fire({
        //                             title: 'ลบข้อมูลสำเร็จ !!',
        //                             text: "Delete Data Success",
        //                             icon: 'success',
        //                             showCancelButton: false,
        //                             confirmButtonColor: '#06D177',
        //                             confirmButtonText: 'เรียบร้อย'
        //                         }).then((result) => {
        //                             if (result.isConfirmed) {
        //                                 $("#sidb" + d_abillitems_id).remove();
        //                                 window.location.reload();
        //                                 // window.location="{{ url('aipn') }}";
        //                             }
        //                         })
        //                     } else {}
        //                 }
        //             })
        //         }
        //     })
        // }
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
               .inputaccs{
                 border: none;
                 box-shadow: 0 0 10px pink;
                 border:solid 1px #80acfd;
                 border-radius: 40px;
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
        <form action="{{ URL('account_302_pull') }}" method="GET">
            @csrf
        <div class="row">
            <div class="col-md-4">
                <h5 class="card-title" style="color:rgb(248, 28, 83)">Detail 1102050101.302</h5>
                <p class="card-title-desc">รายละเอียดข้อมูล ผัง 1102050101.302</p>
            </div>
            <div class="col"></div>
            <div class="col-md-1 text-end mt-2">วันที่</div>
            <div class="col-md-4 text-end">
                <div class="input-daterange input-group" id="datepicker1" data-date-format="dd M, yyyy" data-date-autoclose="true" data-provide="datepicker" data-date-container='#datepicker1'>
                    <input type="text" class="form-control-sm cardacc" name="startdate" id="datepicker" placeholder="Start Date" data-date-container='#datepicker1' data-provide="datepicker" data-date-autoclose="true" autocomplete="off"
                        data-date-language="th-th" value="{{ $startdate }}" required/>
                    <input type="text" class="form-control-sm cardacc" name="enddate" placeholder="End Date" id="datepicker2" data-date-container='#datepicker1' data-provide="datepicker" data-date-autoclose="true" autocomplete="off"
                        data-date-language="th-th" value="{{ $enddate }}"/>
                        <button type="submit" class="ladda-button btn-pill btn btn-sm btn-info cardacc" data-style="expand-left">
                            <span class="ladda-label">
                                <img src="{{ asset('images/Search02.png') }}" class="me-2 ms-2" height="18px" width="18px">
                                ค้นหา</span>
                        </button>
                    </form>
                        <button type="button" class="ladda-button me-2 btn-pill btn btn-sm btn-primary cardacc" data-style="expand-left" id="Pulldata">
                            <span class="ladda-label">
                                <img src="{{ asset('images/pull_datawhite.png') }}" class="me-2 ms-2" height="18px" width="18px">
                                ดึงข้อมูล</span>
                        </button>
            </div>
        </div>
    </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card card_audit_4c" style="background-color: rgb(239, 247, 235)">
                    <div class="card-body">

                         <div class="row mb-2">
                            <div class="col-md-6 text-start">
                                @if ($activeclaim == 'Y')
                                  <button class="ladda-button me-2 btn-pill btn btn-info btn-sm cardacc" onclick="check()">Check</button>
                                  <input type="checkbox" id="myCheck" class="dcheckbox_ me-2" checked>
                                  <button class="ladda-button me-2 btn-pill btn btn-danger btn-sm input_new" onclick="uncheck()">Uncheck</button>
                                @else
                                  <button class="ladda-button me-2 btn-pill btn btn-info btn-sm input_new" onclick="check()">Check</button>
                                  <input type="checkbox" id="myCheck" class="dcheckbox_ me-2">
                                  <button class="ladda-button me-2 btn-pill btn btn-danger btn-sm input_new" onclick="uncheck()">Uncheck</button>
                                @endif
                                  <button type="button" class="ladda-button me-2 btn-pill btn btn-sm btn-warning input_new Claim" data-url="{{url('account_302_claim')}}">
                                      <img src="{{ asset('images/loading_white.png') }}" class="me-2 ms-2" height="18px" width="18px">
                                     ประมวลผล
                                 </button>
                                 <button type="button" class="ladda-button me-2 btn-pill btn btn-sm input_new text-white UpdateBillitems" style="background-color: #f82185" data-url="{{url('account_302_destroy')}}">
                                    <img src="{{ asset('images/removewhite.png') }}" class="me-2 ms-2" height="18px" width="18px">
                                    Billitems
                                </button>
                                 <a href="{{url('account_302_export')}}" class="ladda-button me-2 btn-pill btn btn-sm input_new text-white" style="background-color: #07a1df">
                                    <img src="{{ asset('images/export_white.png') }}" class="me-2 ms-2" height="18px" width="18px">
                                    Export
                                </a>
                                 <a href="{{url('account_302_zip')}}" class="ladda-button me-2 btn-pill btn btn-sm btn-success input_new">
                                  <img src="{{ asset('images/zipwhite.png') }}" class="me-2 ms-2" height="18px" width="18px">
                                      Zip
                                  </a>
                                  {{-- <button type="button" class="ladda-button me-2 btn-pill btn btn-sm input_new" id="Apinhso" style="background-color: rgb(241, 7, 136);color:#ffffff">
                                      <img src="{{ asset('images/Apiwhite.png') }}" class="me-2 ms-2" height="18px" width="18px">
                                      API NHSO
                                  </button> --}}
                              </div>
                            <div class="col"></div>
                            <div class="col-md-3 text-end">

                                <button type="button" class="ladda-button me-2 btn-pill btn btn-info btn-sm input_new Check_sit" data-url="{{url('account_302_checksit')}}">
                                    <img src="{{ asset('images/Check_sitwhite.png') }}" class="me-2 ms-2" height="18px" width="18px">
                                    ตรวจสอบสิทธิ์
                                </button>

                                <button type="button" class="ladda-button me-2 btn-pill btn btn-primary btn-sm input_new Savestamp" data-url="{{url('account_302_stam')}}">
                                    <img src="{{ asset('images/Stam_white.png') }}" class="me-2 ms-2" height="18px" width="18px">
                                    ตั้งลูกหนี้ + ส่งลูกหนี้
                                </button>
                                <button type="button" class="ladda-button me-2 btn-pill btn btn-danger btn-sm input_new Destroystamp" data-url="{{url('account_302_destroy_all')}}">
                                    <img src="{{ asset('images/removewhite.png') }}" class="me-2 ms-2" height="18px" width="18px">
                                    ลบ
                                </button>
                            </div>
                        </div>

                         <!-- Nav tabs -->
                         <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#AIPN" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                    <span class="d-none d-sm-block">AIPN</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#IPADT" role="tab">
                                    <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                    <span class="d-none d-sm-block">IPADT</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#BillItems" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                    <span class="d-none d-sm-block">BillItems</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#IPDx" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                    <span class="d-none d-sm-block">IPDx</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#IPOp" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                    <span class="d-none d-sm-block">IPOp</span>
                                </a>
                            </li>

                        </ul>
                         <!-- Tab panes -->
                         <div class="tab-content p-3 text-muted">

                            <div class="tab-pane active" id="AIPN" role="tabpanel">
                                <p class="mb-0">
                                    <div class="table-responsive">
                                        <table id="example" class="table table-sm table-hover table-striped table-bordered dt-responsive nowrap" style=" border-spacing: 0; width: 100%;">
                                        {{-- <table id="scroll-vertical-datatable" class="table table-sm table-striped dt-responsive nowrap w-100"> --}}
                                            <thead style="border: 1px solid rgb(250, 214, 159);">
                                                <tr>
                                                    <th width="5%" class="text-center">ลำดับ</th>
                                                    <th width="5%" class="text-center"><input type="checkbox" class="dcheckbox_" name="stamp" id="stamp"> </th>
                                                    <th class="text-center">ตั้งลูกหนี้</th>
                                                    <th class="text-center" style="font-size: 11px;">ส่งลูกหนี้</th>
                                                    <th class="text-center">
                                                        <span class="bg-success badge">{{ $count_claim }}</span> เคลม
                                                        <span class="bg-danger badge">{{ $count_noclaim }}</span>
                                                    </th>
                                                    <th class="text-center" style="background-color: #fad6b8">pdx</th>
                                                    <th class="text-center">an</th>
                                                    <th class="text-center" >hn</th>
                                                    <th class="text-center" >cid</th>
                                                    <th class="text-center">ptname</th>
                                                    <th class="text-center">dchdate</th>
                                                    <th class="text-center">pttype</th>
                                                    <th class="text-center">spsch</th>
                                                    <th class="text-center">ลูกหนี้</th>
                                                </tr>
                                            </thead>
                                            <tbody style="border: 1px solid rgb(250, 214, 159);">
                                                <?php $i = 1; ?>
                                                @foreach ($acc_debtor as $item)
                                                    <tr id="tr_{{$item->acc_debtor_id}}">
                                                        <td class="text-center" width="2%">{{ $i++ }}</td>
                                                        @if ($activeclaim == 'Y')
                                                        @if ($item->debit_total == '' || $item->pdx =='')
                                                            <td class="text-center" width="4%">
                                                                <input class="form-check-input" type="checkbox" id="flexCheckDisabled" disabled>
                                                            </td>
                                                        @else
                                                            <td class="text-center" width="4%"><input type="checkbox" class="dcheckbox_ sub_chk" data-id="{{$item->acc_debtor_id}}"> </td>
                                                        @endif
                                                    @else
                                                        <td class="text-center" width="4%"><input type="checkbox" class="dcheckbox_ sub_chk" data-id="{{$item->acc_debtor_id}}"> </td>
                                                    @endif

                                                    {{-- <td class="text-center" width="4%">
                                                        @if ($item->stamp =='N')
                                                            <span class="bg-danger badge me-2">{{ $item->stamp }}</span>
                                                        @else
                                                            <span class="bg-success badge me-2">{{ $item->stamp }}</span>
                                                        @endif
                                                    </td> --}}
                                                    <td class="text-center" width="4%">
                                                        @if ($item->stamp =='N')
                                                        <img src="{{ asset('images/Cancel_new2.png') }}" height="23px" width="23px"> 
                                                        @else
                                                        <img src="{{ asset('images/check_trueinfo3.png') }}" height="23px" width="23px"> 
                                                        @endif
                                                    </td>
                                                    <td class="text-center" width="3%">
                                                        @if ($item->send_active =='N')
                                                            <img src="{{ asset('images/Cancel_new2.png') }}" height="20px" width="20px">
                                                        @else
                                                        <img src="{{ asset('images/check_trueinfo3.png') }}" height="20px" width="20px">
                                                        @endif
                                                    </td>
                                                    <td class="text-center" width="5%">
                                                        @if ($item->active_claim =='N')
                                                        <img src="{{ asset('images/Cancel_new2.png') }}" height="23px" width="23px"> 
                                                        @else
                                                        <img src="{{ asset('images/check_trueinfo3.png') }}" height="23px" width="23px"> 
                                                        @endif
                                                    </td>
                                                    {{-- <td class="text-center" width="5%">
                                                        @if ($item->active_claim =='N')
                                                            <span class="bg-danger badge me-2">{{ $item->active_claim }}</span>
                                                        @else
                                                            <span class="bg-success badge me-2">{{ $item->active_claim }}</span>
                                                        @endif
                                                    </td> --}}
                                                    <td class="text-start" width="4%">
                                                        @if ($item->pdx != NULL)
                                                            <span class="bg-info badge">{{ $item->pdx }}</span>
                                                        @else
                                                            <span class="bg-warning badge">-</span>
                                                        @endif
                                                    </td>
                                                        <td class="text-center" width="5%">{{ $item->an }}</td>
                                                        <td class="text-center" width="5%">{{ $item->hn }}</td>
                                                        <td class="text-center" width="8%">{{ $item->cid }}</td>
                                                        <td class="text-start" >{{ $item->ptname }}</td>
                                                        <td class="text-center" width="10%">{{ $item->dchdate }}</td>
                                                        <td class="text-center" style="color:rgb(73, 147, 231)" width="5%">{{ $item->pttype }}</td>
                                                        <td class="text-center" style="color:rgb(216, 95, 14)" width="5%">{{ $item->subinscl }}</td>
                                                        <td class="text-center" width="8%">{{ number_format($item->debit_total, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                    </div>
                                </p>
                            </div>

                            <div class="tab-pane" id="IPADT" role="tabpanel">
                                <p class="mb-0">
                                    <div class="table-responsive">
                                        <table id="example" class="table table-sm table-striped table-bordered" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th width="5%" class="text-center">ลำดับ</th>
                                                    <th class="text-center" width="5%">AN</th>
                                                    <th class="text-center">HN</th>
                                                    <th class="text-center" >IDTYPE</th>
                                                    <th class="text-center" >PIDPAT</th>
                                                    <th class="text-center">TITLE</th>
                                                    <th class="text-center">NAMEPAT</th>
                                                    <th class="text-center">DOB</th>
                                                    <th class="text-center" width="7%">SEX</th>
                                                    <th class="text-center">MARRIAGE</th>
                                                    <th class="text-center">CHANGWAT</th>
                                                    <th class="text-center" width="8%">AMPHUR</th>
                                                    <th class="text-center" width="8%">NATION</th>
                                                    <th class="text-center" width="10%">AdmType</th>
                                                    <th class="text-center">AdmSource</th>
                                                    <th class="text-center">DTAdm_d</th>
                                                    <th class="text-center">DTAdm_t</th>
                                                    <th class="text-center">DTDisch_d</th>
                                                    <th class="text-center">DTDisch_t</th>
                                                    <th class="text-center">LeaveDay</th>
                                                    <th class="text-center">DischStat</th>
                                                    <th class="text-center">DishType</th>
                                                    <th class="text-center">AdmWt</th>
                                                    <th class="text-center">DishWard</th>
                                                    <th class="text-center">Dept</th>
                                                    <th class="text-center">HMAIN</th>
                                                    <th class="text-center">ServiceType</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $ii = 1; ?>
                                                @foreach ($d_aipadt as $item2)
                                                    <tr>
                                                        <td class="text-center">{{ $ii++ }}</td>
                                                        <td class="text-center" width="5%">{{ $item2->AN }}</td>
                                                        <td class="text-center">{{ $item2->HN }}</td>
                                                        <td class="text-center">{{ $item2->IDTYPE }}</td>
                                                        <td class="text-center">{{ $item2->PIDPAT }}</td>
                                                        <td class="text-center">{{ $item2->TITLE }}</td>
                                                        <td class="text-center">{{ $item2->NAMEPAT }}</td>
                                                        <td class="text-center">{{ $item2->DOB }}</td>
                                                        <td class="text-center">{{ $item2->SEX }}</td>
                                                        <td class="text-center">{{ $item2->MARRIAGE }}</td>
                                                        <td class="text-center">{{ $item2->CHANGWAT }}</td>
                                                        <td class="text-center">{{ $item2->AMPHUR }}</td>
                                                        <td class="text-center">{{ $item2->NATION}}</td>
                                                        <td class="text-center">{{ $item2->AdmType}}</td>
                                                        <td class="text-center">{{ $item2->AdmSource}}</td>
                                                        <td class="text-center">{{ $item2->DTAdm_d}}</td>
                                                        <td class="text-center">{{ $item2->DTAdm_t }}</td>
                                                        <td class="text-center">{{ $item2->DTDisch_d }}</td>
                                                        <td class="text-center">{{ $item2->DTDisch_t }}</td>
                                                        <td class="text-center">{{ $item2->LeaveDay }}</td>
                                                        <td class="text-center">{{ $item2->DischStat }}</td>
                                                        <td class="text-center">{{ $item2->DishType }}</td>
                                                        <td class="text-center">{{ $item2->AdmWt }}</td>
                                                        <td class="text-center">{{ $item2->DishWard }}</td>
                                                        <td class="text-center">{{ $item2->Dept }}</td>
                                                        <td class="text-center">{{ $item2->HMAIN }}</td>
                                                        <td class="text-center">{{ $item2->ServiceType }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </p>
                            </div>

                            <div class="tab-pane" id="BillItems" role="tabpanel">
                                <p class="mb-0">
                                    <div class="table-responsive">
                                        <table id="example3" class="table table-sm table-striped table-bordered" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th width="5%" class="text-center">ลำดับ</th>
                                                    <th width="5%" class="text-center"><input type="checkbox" class="dcheckbox_" name="stamp2" id="stamp2"> </th>
                                                    <th class="text-center">AN</th>
                                                    <th class="text-center">BillGr</th>
                                                    <th class="text-center">BillGrCS</th>
                                                    <th class="text-center">CodeSys</th>
                                                    <th class="text-center">CSCode</th>
                                                    <th class="text-center">STDCode</th>
                                                    <th class="text-center">ClaimCat</th>
                                                    <th class="text-center">LCCode</th>
                                                    <th class="text-center">Descript</th>
                                                    <th class="text-center">QTY</th>
                                                    <th class="text-center">UnitPrice</th>
                                                    <th class="text-center">ChargeAmt</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $iii = 1; ?>
                                                @foreach ($d_abillitems as $item3)
                                                    <tr id="sidb{{ $item3->d_abillitems_id }}" style="font-size: 12px;">
                                                        <td class="text-center" width="5%">{{ $iii++ }}</td>
                                                        <td class="text-center" width="5%"> <input type="checkbox" class="sub_chk_billitem dcheckbox_ me-2" data-id="{{$item3->d_abillitems_id}}"></td>
                                                        <td class="text-center" width="10%">{{ $item3->AN }} </td>
                                                        <td class="text-center" width="5%">{{ $item3->BillGr }} </td>
                                                        <td class="text-center" width="5%">{{ $item3->BillGrCS }}</td>
                                                        @if ($item3->CodeSys == 'TMT' && $item3->STDCode == '')
                                                            <td class="text-center" style="background-color: rgb(241, 6, 45)" width="5%">{{ $item3->CodeSys }} </td>
                                                        @elseif ($item3->CodeSys == 'TMLT' && $item3->STDCode == '')
                                                            <td class="text-center" style="background-color: rgb(252, 2, 44)" width="5%">{{ $item3->CodeSys }} </td>
                                                        @else
                                                            <td class="text-center" width="5%">{{ $item3->CodeSys }} </td>
                                                        @endif
                                                        <td class="text-center" width="5%">{{ $item3->CSCode }}</td>
                                                        <td class="text-center" width="5%">{{ $item3->STDCode }} </td>
                                                        <td class="text-center" width="5%">{{ $item3->ClaimCat }} </td>
                                                        <td class="text-center" width="10%"> {{ $item3->LCCode }} </td>
                                                        <td class="text-start">{{ $item3->Descript }}</td>
                                                        <td class="text-center" width="5%">{{ $item3->QTY }}</td>
                                                        <td class="text-center" width="5%"> {{ number_format($item3->UnitPrice, 2) }}</td>
                                                        <td class="text-center" width="5%"> {{ number_format($item3->ChargeAmt, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </p>
                            </div>

                            <div class="tab-pane" id="IPDx" role="tabpanel">
                                <p class="mb-0">
                                <div class="table-responsive">
                                    <table id="key-datatable"
                                        class="table table-striped table-bordered dt-responsive nowrap"
                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th width="5%" class="text-center">ลำดับ</th>
                                                <th class="text-center">an</th>
                                                <th class="text-center">sequence</th>
                                                <th class="text-center">DxType</th>
                                                <th class="text-center">CodeSys</th>
                                                <th class="text-center">Dcode</th>
                                                <th class="text-center">DiagTerm</th>
                                                <th class="text-center">DR</th>
                                                <th class="text-center">datediag</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 1; ?>
                                            @foreach ($d_aipdx as $item3)
                                                <tr style="font-size: 12px;">
                                                    <td class="text-center">{{ $i++ }}</td>
                                                    <td class="text-center">{{ $item3->an }} </td>
                                                    <td class="text-center">{{ $item3->sequence }}</td>
                                                    <td class="text-center">{{ $item3->DxType }} </td>
                                                    <td class="text-center">{{ $item3->CodeSys }} </td>
                                                    <td class="text-center">{{ $item3->Dcode }} </td>
                                                    <td class="text-start">{{ $item3->DiagTerm }}</td>
                                                    <td class="text-center">{{ $item3->DR }}</td>
                                                    <td class="text-center">{{ $item3->datediag }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                </p>
                            </div>

                            <div class="tab-pane" id="IPOp" role="tabpanel">
                                <p class="mb-0">
                                <div class="table-responsive">
                                    <table id="example5" class="table table-striped table-bordered dt-responsive nowrap"
                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th width="5%" class="text-center">ลำดับ</th>
                                                <th class="text-center">an</th>
                                                <th class="text-center">sequence</th>
                                                <th class="text-center">CodeSys</th>
                                                <th class="text-center">Code</th>
                                                <th class="text-center">Procterm</th>
                                                <th class="text-center">DR</th>
                                                <th class="text-center">DateIn</th>
                                                <th class="text-center">DateOut</th>
                                                {{-- <th class="text-center">Location</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 1; ?>
                                            @foreach ($d_aipop as $item4)
                                                <tr style="font-size: 12px;">
                                                    <td class="text-center">{{ $i++ }}</td>
                                                    <td class="text-center">{{ $item4->an }}</td>
                                                    <td class="text-center">{{ $item4->sequence }} </td>
                                                    <td class="text-center">{{ $item4->CodeSys }} </td>
                                                    <td class="text-center">{{ $item4->Code }} </td>
                                                    <td class="text-start">{{ $item4->Procterm }}</td>
                                                    <td class="text-start">{{ $item4->DR }}</td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn-icon btn-shadow btn-dashed btn btn-sm btn-outline-warning addicodeModal" value="{{ $item4->d_aipop_id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="แก้ไข DateIn  DateOut">
                                                            <i class="fa-regular fa-pen-to-square me-2"></i>
                                                            {{ $item4->DateIn }}
                                                        </button>
                                                    </td>
                                                    <td class="text-start" width="15%">

                                                        {{ $item4->DateOut }}
                                                    </td>
                                                    {{-- <td class="text-center">{{ $item4->Location }}</td> --}}

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
            </div>
        </div>


    </div>
    </div>


     <!-- addicodeModal Modal -->
     <div class="modal fade" id="addicodeModal"  tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">รายการขอปรับเปลี่ยนวันที่ DateIn DateOut</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="an" class="form-label">an</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" id="an_edit" name="an" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="DateOut" class="form-label">DateIn</label>
                            </div>
                            <div class="col-md-6">
                                <label for="DateOut" class="form-label">DateOut</label>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <div class="input-daterange input-group" id="datepicker9" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container='#datepicker6'>
                                    <input type="text" class="form-control" name="datein_edit" id="datein_edit" placeholder="DateIn" data-date-container='#datepicker9' autocomplete="off" data-provide="datepicker"
                                        data-date-autoclose="true" data-date-language="th-th"/>
                                    <input type="text" class="form-control" name="dateout_edit" placeholder="DateOut" id="dateout_edit" data-date-container='#datepicker9' autocomplete="off" data-provide="datepicker"
                                        data-date-autoclose="true" data-date-language="th-th" />
                                </div>
                            </div>
                        </div>


                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="TimeIn" class="form-label">TimeIn</label>
                                <div class="input-group input-group-sm">
                                    <input class="form-control" data-provide="timepicker" data-minute-step="1" type="text" id="timein_edit" name="TimeIn" placeholder="09:25:42">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="TimeOut" class="form-label">TimeOut</label>
                                <div class="input-group input-group-sm">
                                    <input class="form-control" data-provide="timepicker" data-minute-step="1" type="text" id="timeout_edit" name="TimeOut" placeholder="21:35:42">
                                </div>
                            </div>
                        </div>
                    <input type="hidden" name="d_aipop_id" id="d_aipop_id_edit">
                </div>
                <div class="modal-footer">
                    <button type="button" class="me-2 btn-icon btn-shadow btn-dashed btn btn-outline-info text-center" id="Updatedata_time">
                        <i class="pe-7s-diskette btn-icon-wrapper me-2"></i>Update changes
                    </button>
                </div>
            </div>
        </div>
    </div>


    @endsection
    @section('footer')

    <script>
         function check() {
        var onoff;
        document.getElementById("myCheck").checked = true;
        onoff = "Y";
          var _token=$('input[name="_token"]').val();
            $.ajax({
                    url:"{{route('acc.account_302_claimswitch')}}",
                    method:"GET",
                    data:{onoff:onoff,_token:_token},
                    success:function(data){
                        if (data.status == 200) {
                            Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: "Your open function success",
                            showConfirmButton: false,
                            timer: 1500
                            });

                            window.location.reload();

                        } else {

                        }
                }
            });

        }

        function uncheck() {
            document.getElementById("myCheck").checked = false;
            onoff = "N";
            var _token=$('input[name="_token"]').val();
            $.ajax({
                    url:"{{route('acc.account_302_claimswitch')}}",
                    method:"GET",
                    data:{onoff:onoff,_token:_token},
                    success:function(data){
                        if (data.status == 200) {
                            Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: "Your Close function success",
                            showConfirmButton: false,
                            timer: 1500
                            });

                            window.location.reload();

                        } else {

                        }
                }
            });

        }
        $(document).ready(function() {
            $('#example').DataTable();
            $('#example2').DataTable();
            $('#example3').DataTable();
            $('#example4').DataTable();
            $('#example5').DataTable();

            $('#datepicker').datepicker({
                format: 'yyyy-mm-dd'
            });
            $('#datepicker2').datepicker({
                format: 'yyyy-mm-dd'
            });

            $('#datein_edit').datepicker({
                format: 'yyyy-mm-dd'
            });
            $('#dateout_edit').datepicker({
                format: 'yyyy-mm-dd'
            });

            // $('#timein_edit, #addicodeModal').timepicker({
            //     format: 'hh:mm:ss',
            //     minuteStep: 1,
            //     showSeconds: true,
            //     showMeridian: false,
            //     snapToStep: true
            // });

            // $('#timeout_edit, #addicodeModal').timepicker({
            //     format: 'hh:mm:ss',
            //     minuteStep: 1,
            //     showSeconds: true,
            //     showMeridian: false,
            //     snapToStep: true
            // });

            $('#stamp').on('click', function(e) {
            if($(this).is(':checked',true))
            {
                $(".sub_chk").prop('checked', true);
            } else {
                $(".sub_chk").prop('checked',false);
            }
            });

            $('#stamp2').on('click', function(e) {
            if($(this).is(':checked',true))
            {
                $(".sub_chk_billitem").prop('checked', true);
            } else {
                $(".sub_chk_billitem").prop('checked',false);
            }
            });

            $('.Savestamp').on('click', function(e) {
                // alert('oo');
                var allValls = [];
                $(".sub_chk:checked").each(function () {
                    allValls.push($(this).attr('data-id'));
                });
                if (allValls.length <= 0) {
                    // alert("SSSS");
                    Swal.fire({
                        title: 'คุณยังไม่ได้เลือกรายการ ?',
                        text: "กรุณาเลือกรายการก่อน",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        }).then((result) => {

                        })
                } else {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "คุณต้องการตั้งลูกหนี้รายการนี้ใช่ไหม!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Debtor it.!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                var check = true;
                                if (check == true) {
                                    var join_selected_values = allValls.join(",");
                                    // alert(join_selected_values);
                                    $("#overlay").fadeIn(300);　
                                    $("#spinner").show(); //Load button clicked show spinner

                                    $.ajax({
                                        url:$(this).data('url'),
                                        type: 'POST',
                                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                        data: 'ids='+join_selected_values,
                                        success:function(data){
                                                if (data.status == 200) {
                                                    $(".sub_chk:checked").each(function () {
                                                        $(this).parents("tr").remove();
                                                    });
                                                    Swal.fire({
                                                        title: 'ตั้งลูกหนี้สำเร็จ',
                                                        text: "You Debtor data success",
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


                                            // } else {
                                            //     alert("Whoops Something went worng all");
                                            // }
                                        }
                                    });
                                    $.each(allValls,function (index,value) {
                                        $('table tr').filter("[data-row-id='"+value+"']").remove();
                                    });
                                }
                            }
                        })
                    // var check = confirm("Are you want ?");
                }
            });

            $("#spinner-div").hide(); //Request is complete so hide spinner

            $('#Pulldata').click(function() {
                var datepicker = $('#datepicker').val();
                var datepicker2 = $('#datepicker2').val();
                Swal.fire({
                        title: 'ต้องการดึงข้อมูลใช่ไหม ?',
                        text: "You Warn Pull Data!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, pull it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $("#overlay").fadeIn(300);　
                                $("#spinner").show(); //Load button clicked show spinner

                                $.ajax({
                                    url: "{{ route('acc.account_302_pulldata') }}",
                                    type: "POST",
                                    dataType: 'json',
                                    data: {
                                        datepicker,
                                        datepicker2
                                    },
                                    success: function(data) {
                                        if (data.status == 200) {
                                            Swal.fire({
                                                title: 'ดึงข้อมูลสำเร็จ',
                                                text: "You Pull data success",
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

            $('.UpdateBillitems').on('click', function(e) {
                // alert('oo');
                var allValls = [];
                $(".sub_chk_billitem:checked").each(function () {
                    allValls.push($(this).attr('data-id'));
                });
                if (allValls.length <= 0) {
                    // alert("SSSS");
                    Swal.fire({
                        title: 'คุณยังไม่ได้เลือกรายการ ?',
                        text: "กรุณาเลือกรายการก่อน",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        }).then((result) => {

                        })
                } else {
                    Swal.fire({
                            title: 'Are you sure?',
                            text: "คุณต้องการลบรายการที่เลือกใช่ไหม!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, Delete it.!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    var check = true;
                                    if (check == true) {
                                        var join_selected_values = allValls.join(",");
                                        $.ajax({
                                            url:$(this).data('url'),
                                            type: 'POST',
                                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                            data: 'ids2='+join_selected_values,
                                            success:function(data){
                                                if (data.status == 200) {
                                                    $(".sub_chk_billitem:checked").each(function () {
                                                        $(this).parents("tr").remove();
                                                    });
                                                    Swal.fire({
                                                        title: 'ลบรายการ สำเร็จ',
                                                        text: "You Delete data success",
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
                                                            // window.location="{{url('warehouse/warehouse_index')}}";
                                                        }
                                                    })
                                                } else {

                                                }
                                            }
                                        });
                                        $.each(allValls,function (index,value) {
                                            $('table tr').filter("[data-row-id='"+value+"']").remove();
                                        });
                                    }
                                }
                            })

                }
            });

            $('#destroy').on('click', function(e) {
                if($(this).is(':checked',true))
                {
                    $(".sub_destroy").prop('checked', true);
                } else {
                    $(".sub_destroy").prop('checked',false);
                }
            });

            $('.Destroystamp').on('click', function(e) {
                // alert('oo');
                var allValls = [];
                $(".sub_chk:checked").each(function () {
                    allValls.push($(this).attr('data-id'));
                });
                if (allValls.length <= 0) {
                    // alert("SSSS");
                    Swal.fire({
                        title: 'คุณยังไม่ได้เลือกรายการ ?',
                        text: "กรุณาเลือกรายการก่อน",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        }).then((result) => {

                        })
                } else {
                    Swal.fire({
                        title: 'Are you Want Delete sure?',
                        text: "คุณต้องการลบรายการนี้ใช่ไหม!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Delete it.!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                var check = true;
                                if (check == true) {
                                    var join_selected_values = allValls.join(",");
                                    // alert(join_selected_values);
                                    $("#overlay").fadeIn(300);　
                                    $("#spinner").show(); //Load button clicked show spinner

                                    $.ajax({
                                        url:$(this).data('url'),
                                        type: 'POST',
                                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                        data: 'ids='+join_selected_values,
                                        success:function(data){
                                                if (data.status == 200) {
                                                    $(".sub_chk:checked").each(function () {
                                                        $(this).parents("tr").remove();
                                                    });
                                                    Swal.fire({
                                                        title: 'ลบข้อมูลสำเร็จ',
                                                        text: "You Delete data success",
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


                                            // } else {
                                            //     alert("Whoops Something went worng all");
                                            // }
                                        }
                                    });
                                    $.each(allValls,function (index,value) {
                                        $('table tr').filter("[data-row-id='"+value+"']").remove();
                                    });
                                }
                            }
                        })
                    // var check = confirm("Are you want ?");
                }
            });

            $('.Check_sit').click(function() {
                var allValls = [];
                $(".sub_chk:checked").each(function () {
                    allValls.push($(this).attr('data-id'));
                });
                if (allValls.length <= 0) {
                    // alert("SSSS");
                    Swal.fire({
                        title: 'คุณยังไม่ได้เลือกรายการ ?',
                        text: "กรุณาเลือกรายการก่อน",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        }).then((result) => {

                        })
                } else {

                    Swal.fire({
                        position: "top-end",
                        title: 'Are you sure?',
                        text: "ต้องการตรวจสอบสอทธิ์ใช่ไหม!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'You Check Sit Data!.!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                var check = true;
                                if (check == true) {
                                    var join_selected_values = allValls.join(",");
                                    // alert(join_selected_values);
                                    $("#overlay").fadeIn(300);　
                                    $("#spinner").show(); //Load button clicked show spinner

                                    $.ajax({
                                        url:$(this).data('url'),
                                        type: 'POST',
                                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                        data: 'ids='+join_selected_values,
                                        success:function(data){
                                                if (data.status == 200) {
                                                    $(".sub_chk:checked").each(function () {
                                                        $(this).parents("tr").remove();
                                                    });
                                                    Swal.fire({
                                                        position: "top-end",
                                                        title: 'เช็คสิทธิ์สำเร็จ',
                                                        text: "You Check sit success",
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

                                        }
                                    });
                                    $.each(allValls,function (index,value) {
                                        $('table tr').filter("[data-row-id='"+value+"']").remove();
                                    });
                                }
                            }
                        })


                    }
            });

            $('.Claim').on('click', function(e) {
                // alert('oo');
                var allValls = [];
                // $(".sub_destroy:checked").each(function () {
                $(".sub_chk:checked").each(function () {
                    allValls.push($(this).attr('data-id'));
                });
                if (allValls.length <= 0) {
                    // alert("SSSS");
                    Swal.fire({ position: "top-end",
                        title: 'คุณยังไม่ได้เลือกรายการ ?',
                        text: "กรุณาเลือกรายการก่อน",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        }).then((result) => {

                        })
                } else {
                    Swal.fire({ position: "top-end",
                        title: 'Are you Want Process sure?',
                        text: "คุณต้องการ ประมวลผล รายการนี้ใช่ไหม!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Process it.!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                var check = true;
                                if (check == true) {
                                    var join_selected_values = allValls.join(",");
                                    // alert(join_selected_values);
                                    $("#overlay").fadeIn(300);　
                                    $("#spinner").show(); //Load button clicked show spinner

                                    $.ajax({
                                        url:$(this).data('url'),
                                        type: 'POST',
                                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                        data: 'ids='+join_selected_values,
                                        success:function(data){
                                                if (data.status == 200) {
                                                    // $(".sub_destroy:checked").each(function () {
                                                    $(".sub_chk:checked").each(function () {
                                                        $(this).parents("tr").remove();
                                                    });
                                                    Swal.fire({ position: "top-end",
                                                        title: 'ประมวลผลสำเร็จ',
                                                        text: "You Process data success",
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

                                        }
                                    });
                                    $.each(allValls,function (index,value) {
                                        $('table tr').filter("[data-row-id='"+value+"']").remove();
                                    });
                                }
                            }
                        })
                    // var check = confirm("Are you want ?");
                }
            });

            $('#Updatedata_time').click(function() {
                    var DateIn = $('#datein_edit').val();
                    var TimeIn = $('#timein_edit').val();
                    var DateOut = $('#dateout_edit').val();
                    var TimeOut = $('#timeout_edit').val();
                    var d_aipop_id_edit = $('#d_aipop_id_edit').val();
                    Swal.fire({
                            title: 'ต้องการแก้ไขข้อมูลใช่ไหม ?',
                            text: "You Edit Data!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, pull it!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $("#overlay").fadeIn(300);　
                                    $("#spinner").show(); //Load button clicked show spinner

                                    $.ajax({
                                        url: "{{ route('acc.account_302_update') }}",
                                        type: "POST",
                                        dataType: 'json',
                                        data: {
                                            DateIn, TimeIn ,DateOut,TimeOut ,d_aipop_id_edit
                                        },
                                        success: function(data) {
                                            if (data.status == 200) {
                                                Swal.fire({
                                                    title: 'แก้ไขข้อมูลสำเร็จ',
                                                    text: "You Edit data success",
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

        $(document).on('click', '.addicodeModal', function() {
                var d_aipop_id = $(this).val();
                $('#addicodeModal').modal('show');
                // alert(d_aipop_id);
                $.ajax({
                    type: "GET",
                    url: "{{ url('aipn_ipop_edit') }}" + '/' + d_aipop_id,
                    success: function(data) {
                        console.log(data.data_ipop.d_aipop_id);
                        $('#an_edit').val(data.data_ipop.an)
                        $('#datein_edit').val(data.data_ipop.Date_In)
                        $('#timein_edit').val(data.data_ipop.Time_In)
                        $('#dateout_edit').val(data.data_ipop.Date_Out)
                        $('#timeout_edit').val(data.data_ipop.Time_Out)
                        $('#d_aipop_id_edit').val(data.data_ipop.d_aipop_id)

                    },
                });
        });


    </script>
    @endsection
