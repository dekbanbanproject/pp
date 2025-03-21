@extends('layouts.pkclaim')
@section('title', 'PK-OFFICE || SSOP-REPORT')
 
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
    use App\Http\Controllers\StaticController;
    use Illuminate\Support\Facades\DB;   
    $count_meettingroom = StaticController::count_meettingroom();
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
</style>
    <div class="tabs-animation">
        <div id="preloader">
            <div id="status">
                <div class="spinner">
                    
                </div>
            </div>
        </div>

        <form action="{{ route('claim.ssop_recheck_search') }}" method="POST">
            @csrf

            <div class="row">  
                <div class="col-md-2 text-end">วันที่</div>
                <div class="col-md-9 text-center">
                    <div class="input-daterange input-group" id="datepicker1" data-date-format="dd M, yyyy" data-date-autoclose="true" data-provide="datepicker" data-date-container='#datepicker6'>
                        <input type="text" class="form-control" name="startdate" id="datepicker" placeholder="Start Date" data-date-container='#datepicker1'
                         data-provide="datepicker" data-date-autoclose="true" data-date-language="th-th" value="{{ $start }}"/>
                        <input type="text" class="form-control" name="enddate" placeholder="End Date" id="datepicker2" data-date-container='#datepicker1'
                        data-provide="datepicker" data-date-autoclose="true" data-date-language="th-th" value="{{ $end }}"/>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            ค้นหาข้อมูล 
                        </button>
                        <a href="{{url('ssop_pull_new/'.$start.'/'.$end)}}" class="btn btn-info"><i class="fa-solid fa-arrow-up-right-from-square me-2"></i>ดึงข้อมูลใหม่</a>
                        <a href="{{url('ssop_send')}}" class="btn btn-success"><i class="fa-solid fa-arrow-up-right-from-square me-2"></i>ส่งออก</a>    
                        <a href="{{url('ssop_zip')}}" class="btn btn-danger"><i class="fa-solid fa-file-zipper me-2"></i>ZipFile</a>  
                        {{-- <a href="{{url('aipn_send_pull_new')}}" class="btn btn-success"><i class="fa-solid fa-arrow-up-right-from-square me-2"></i>ส่งออก</a>  
                        <a href="{{url('aipn_zip_pull_new')}}" class="btn btn-info"><i class="fa-solid fa-file-zipper me-2"></i>ZipFile</a> --}}
                        {{-- <a href="{{url('ssop_pull_delete')}}" class="btn btn-danger"><i class="fa-solid fa-trash-can me-2"></i>Delete Data</a> --}}
                    </div>
                </div>    
                <div class="col"></div>
            </div> 
        </form>
        
        <div class="row mt-3">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-4">
                                <h4 class="card-title">Detail SSOP</h4>
                                <p class="card-title-desc">รายละเอียดประกันสังคม</p>
                            </div>
                            <div class="col"></div>
                            <div class="col-md-3 text-end">
                                {{-- <a href="{{url('ssop_prescb_update')}}" class="btn btn-outline-info btn-sm">
                                    <i class="fa-solid fa-file-waveform me-2"></i>
                                    Update Prescb
                                </a>
                                <a href="{{url('ssop_svpid_update')}}" class="btn btn-outline-info btn-sm">
                                    <i class="fa-solid fa-file-waveform me-2"></i>
                                    Update SvPID
                                </a> --}}
                                <button type="button" class="btn btn-outline-danger btn-sm Updateprescb" data-url="{{url('ssop_prescb_update')}}">
                                    <i class="fa-solid fa-file-waveform me-2"></i>
                                    Update Prescb
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-sm Updatesvpid" data-url="{{url('ssop_svpid_update')}}">
                                    <i class="fa-solid fa-file-waveform me-2"></i>
                                    Update SvPID
                                </button>
                            </div>
                        </div>

                  

                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#BillTran" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                    <span class="d-none d-sm-block">BillTran</span>    
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#BillItems" role="tab">
                                    <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                    <span class="d-none d-sm-block">BillItems</span>    
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#Dispensing" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                    <span class="d-none d-sm-block">Dispensing</span>    
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#DispensedItem" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                    <span class="d-none d-sm-block">DispensedItem</span>    
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#OPServices" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                    <span class="d-none d-sm-block">OPServices</span>    
                                </a>
                            </li>
                          
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#OPDX" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                    <span class="d-none d-sm-block">OPDX</span>    
                                </a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content p-3 text-muted">
                            <div class="tab-pane active" id="BillTran" role="tabpanel">
                                <p class="mb-0">
                                    <div class="table-responsive">
                                            <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th width="5%" class="text-center">ลำดับ</th> 
                                                    <th class="text-center" width="5%">Station</th> 
                                                    <th class="text-center">DTtran</th>
                                                    <th class="text-center" >Hcode</th>
                                                    <th class="text-center" >Invno</th>
                                                    <th class="text-center">VerCode</th>
                                                    <th class="text-center">HN</th> 
                                                    <th class="text-center">Tflag</th>
                                                    <th class="text-center" width="7%">HMain</th>
                                                    <th class="text-center">Pid</th>
                                                    <th class="text-center">ชื่อ-สกุล </th>
                                                    <th class="text-center" width="8%">Amount</th>
                                                    <th class="text-center" width="8%">Paid</th> 
                                                    <th class="text-center" width="10%">ClaimAmt</th> 
                                                    <th class="text-center">PayPlan</th>
                                                    <th class="text-center">OtherPay</th>
                                                    <th class="text-center">pttype</th>
                                                    <th class="text-center">Diag</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1; ?>
                                                @foreach ($ssop_billtran as $item) 
                                                    <tr>   
                                                        <td class="text-center">{{ $i++ }}</td>   
                                                        <td class="text-center" width="5%">{{ $item->Station }}</td> 
                                                        <td class="text-center">{{ $item->DTtran }}</td> 
                                                        <td class="text-center">{{ $item->Hcode }}</td>  
                                                        <td class="text-center">{{ $item->Invno }}</td>  
                                                        <td class="text-center">{{ $item->VerCode }}</td> 
                                                        <td class="text-center">{{ $item->HN }}</td>  
                                                        <td class="text-center">{{ $item->Tflag }}</td> 
                                                        <td class="text-center">{{ $item->HMain }}</td> 
                                                        <td class="text-center">{{ $item->Pid }}</td> 
                                                        <td class="p-2">{{ $item->Name }}</td> 
                                                        <td class="text-center">{{ number_format($item->Amount, 2) }}</td> 
                                                        <td class="text-center">{{ number_format($item->Paid, 2) }}</td>  
                                                        <td class="text-center">{{ number_format($item->ClaimAmt, 2) }}</td>  
                                                        <td class="text-center">{{ number_format($item->PayPlan, 2) }}</td> 
                                                        <td class="text-center">{{ number_format($item->OtherPay, 2) }}</td> 
                                                        <td class="text-center">{{ $item->pttype }}</td> 
                                                        <td class="text-center">{{ $item->Diag }}</td> 
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
                                        <table id="selection-datatable" class="table table-striped table-bordered dt-responsive nowrap"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th width="5%" class="text-center">ลำดับ</th>  
                                                    <th class="text-center">Invno</th>
                                                    <th class="text-center">SvDate</th>
                                                    <th class="text-center">BillMuad</th>
                                                    <th class="text-center">LCCode</th>
                                                    <th class="text-center">STDCode</th>   
                                                    <th class="text-center">Desc</th> 
                                                    <th class="text-center">QTY</th> 
                                                    <th class="text-center">UnitPrice</th> 
                                                    <th class="text-center">ChargeAmt</th> 
                                                    <th class="text-center">ClaimUP</th> 
                                                    <th class="text-center">ClaimAmount</th> 
                                                    <th class="text-center">SvRefID</th> 
                                                    <th class="text-center">ClaimCat</th> 
                                                    <th class="text-center">paidst</th>  
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1; ?>
                                                @foreach ($ssop_billitems as $item2) 
                                                    <tr>   
                                                        <td class="text-center">{{ $i++ }}</td>   
                                                        <td class="text-center">{{ $item2->Invno }} </td>
                                                        <td class="text-center">{{ $item2->SvDate }}</td>
                                                        <td class="text-center">{{ $item2->BillMuad }} </td>
                                                        <td class="text-center">{{ $item2->LCCode }} </td>
                                                        <td class="text-center">{{ $item2->STDCode }} </td>
                                                        <td class="p-2">{{ $item2->Desc }}</td>   
                                                        <td class="text-center">{{ $item2->QTY }}</td>
                                                        <td class="text-center">{{ number_format($item2->UnitPrice, 2) }}</td>
                                                        <td class="text-center">{{ number_format($item2->ChargeAmt, 2) }}</td>
                                                        <td class="text-center">{{ number_format($item2->ClaimUP, 2) }}</td>
                                                        <td class="text-center">{{ number_format($item2->ClaimAmount, 2) }}</td>
                                                        <td class="text-center">{{ $item2->SvRefID }}</td>
                                                        <td class="text-center">{{ $item2->ClaimCat }}</td>
                                                        <td class="text-center">{{ $item2->paidst }}</td> 
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </p>
                            </div>
                            <div class="tab-pane" id="Dispensing" role="tabpanel">
                                <p class="mb-0">
                                    <div class="table-responsive">
                                        <table id="key-datatable" class="table table-striped table-bordered dt-responsive nowrap"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th width="5%" class="text-center">ลำดับ</th>  
                                                    <th class="text-center">ProviderID</th>
                                                    <th class="text-center">DispID</th>
                                                    <th class="text-center">Invno</th>
                                                    <th class="text-center">HN</th>
                                                    <th class="text-center">PID</th>   
                                                    <th class="text-center">Prescdt</th> 
                                                    <th class="text-center">Dispdt</th> 
                                                    <th class="text-center"><input type="checkbox" name="stamp" id="stamp" class="me-2">Prescb</th> 
                                                    <th class="text-center">Itemcnt</th> 
                                                    <th class="text-center">ChargeAmt</th> 
                                                    <th class="text-center">ClaimAmt</th> 
                                                    <th class="text-center">Paid</th> 
                                                    <th class="text-center">OtherPay</th> 
                                                    <th class="text-center">Reimburser</th>  
                                                    <th class="text-center">BenefitPlan</th> 
                                                    <th class="text-center">DispeStat</th> 
                                                    <th class="text-center">SvID</th> 
                                                    <th class="text-center">DayCover</th> 
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1; ?>
                                                @foreach ($ssop_dispensing as $item3) 
                                                    <tr id="prescbid{{$item3->ssop_dispensing_id}}">   
                                                        <td class="text-center">{{ $i++ }}</td>   
                                                        <td class="text-center">{{ $item3->ProviderID }} </td>
                                                        <td class="text-center">{{ $item3->DispID }}</td>
                                                        <td class="text-center" >{{ $item3->Invno }} </td>
                                                        <td class="text-center" >{{ $item3->HN }} </td>
                                                        <td class="text-center" >{{ $item3->PID }} </td>
                                                        <td class="text-center" >{{ $item3->Prescdt }}</td>   
                                                        <td class="text-center" >{{ $item3->Dispdt }}</td> 
                                                        <td class="p-2" >
                                                            <input type="checkbox" class="sub_chk me-2" data-id="{{$item3->ssop_dispensing_id}}">
                                                            <button type="button"class="btn btn-outline-danger btn-sm Edit_prescb"
                                                                value="{{ $item3->ssop_dispensing_id }}" data-bs-toggle="tooltip"
                                                                data-bs-placement="left" title="แก้ไข">
                                                                <i class="fa-solid fa-pen-to-square me-2 text-danger"></i>
                                                                <label for="" class="text-danger" style="font-size:13px;"> {{ $item3->Prescb }}</label>
                                                            </button>
                                                        </td> 
                                                        <td class="text-center" >{{ $item3->Itemcnt }}</td> 
                                                        <td class="text-center" >{{ number_format($item3->ChargeAmt, 2) }}</td> 
                                                        <td class="text-center" >{{ number_format($item3->ClaimAmt, 2) }}</td> 
                                                        <td class="text-center" >{{ number_format($item3->Paid, 2) }}</td> 
                                                        <td class="text-center" >{{ number_format($item3->OtherPay, 2) }}</td> 
                                                        <td class="text-center" >{{ $item3->Reimburser }}</td> 
                                                        <td class="text-center" >{{ $item3->BenefitPlan }}</td> 
                                                        <td class="text-center" >{{ $item3->DispeStat }}</td> 
                                                        <td class="text-center" >{{ $item3->SvID }}</td> 
                                                        <td class="text-center" >{{ $item3->DayCover }}</td> 
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </p>
                            </div>

                            <div class="tab-pane" id="DispensedItem" role="tabpanel">
                                <p class="mb-0">
                                    <div class="table-responsive">
                                        <table id="example" class="table table-striped table-bordered dt-responsive nowrap"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th width="5%" class="text-center">ลำดับ</th>  
                                                    <th class="text-center">DispID</th>
                                                    <th class="text-center">PrdCat</th>
                                                    <th class="text-center">HospDrgID</th>
                                                    <th class="text-center">DrgID</th>   
                                                    <th class="text-center">dfsText</th> 
                                                    <th class="text-center">Packsize</th> 
                                                    <th class="text-center">sigCode</th> 
                                                    <th class="text-center">sigText</th> 
                                                    <th class="text-center">Quantity</th> 
                                                    <th class="text-center">UnitPrice</th> 
                                                    <th class="text-center">ChargeAmt</th> 
                                                    <th class="text-center">ReimbPrice</th> 
                                                    <th class="text-center">ReimbAmt</th>  
                                                    <th class="text-center">PrdSeCode</th> 
                                                    <th class="text-center">Claimcont</th> 
                                                    <th class="text-center">ClaimCat</th> 
                                                    <th class="text-center">paidst</th> 
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1; ?>
                                                @foreach ($ssop_dispenseditems as $item4) 
                                                    <tr>   
                                                        <td class="text-center">{{ $i++ }}</td>    
                                                        <td class="text-center">{{ $item4->DispID }}</td>
                                                        <td class="text-center" >{{ $item4->PrdCat }} </td>
                                                        <td class="text-center" >{{ $item4->HospDrgID }} </td>
                                                        <td class="text-center" >{{ $item4->DrgID }} </td>
                                                        <td class="p-2" >{{ $item4->dfsText }}</td>   
                                                        <td class="p-2" >{{ $item4->Packsize }}</td> 
                                                        <td class="text-center" >{{ $item4->sigCode }}</td> 
                                                        <td class="p-2" width="15%">{{ $item4->sigText }}</td> 
                                                        <td class="text-center" >{{ $item4->Quantity }}</td> 
                                                        <td class="text-center" >{{ number_format($item4->UnitPrice, 2) }}</td> 
                                                        <td class="text-center" >{{ number_format($item4->ChargeAmt, 2) }}</td> 
                                                        <td class="text-center" >{{ number_format($item4->ReimbPrice, 2) }}</td> 
                                                        <td class="text-center" >{{ number_format($item4->ReimbAmt, 2) }}</td> 
                                                        <td class="text-center" >{{ $item4->PrdSeCode }}</td> 
                                                        <td class="text-center" >{{ $item4->Claimcont }}</td> 
                                                        <td class="text-center" >{{ $item4->ClaimCat }}</td> 
                                                        <td class="text-center" >{{ $item4->paidst }}</td> 
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </p>
                            </div>
                            <div class="tab-pane" id="OPServices" role="tabpanel">
                                <p class="mb-0">
                                    <div class="table-responsive">
                                        <table id="example2" class="table table-striped table-bordered dt-responsive nowrap"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th width="5%" class="text-center">ลำดับ</th>  
                                                    <th class="text-center">Invno</th>
                                                    <th class="text-center">SvID</th>
                                                    <th class="text-center">Class</th>
                                                    <th class="text-center">Hcode</th>   
                                                    <th class="text-center">HN</th> 
                                                    <th class="text-center">PID</th> 
                                                    <th class="text-center">CareAccount</th> 
                                                    <th class="text-center">TypeServ</th> 
                                                    <th class="text-center">TypeIn</th> 
                                                    <th class="text-center">TypeOut</th> 
                                                    <th class="text-center">DTAppoint</th> 
                                                    <th class="p-2"><input type="checkbox" name="stamp2" id="stamp2" class="me-2">SvPID</th> 
                                                    <th class="text-center">Clinic</th>  
                                                    <th class="text-center">BegDT</th> 
                                                    <th class="text-center">EndDT</th> 
                                                    <th class="text-center">LcCode</th> 
                                                    <th class="text-center">CodeSet</th> 
                                                    <th class="text-center">STDCode</th> 
                                                    <th class="text-center">SvCharge</th> 
                                                    <th class="text-center">Completion</th> 
                                                    <th class="text-center">SvTxCode</th> 
                                                    <th class="text-center">ClaimCat</th> 
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1; ?>
                                                @foreach ($ssop_opservices as $item5) 
                                                    <tr id="prescbid{{$item5->ssop_opservices_id}}">   
                                                        <td class="text-center">{{ $i++ }}</td>    
                                                        <td class="text-center">{{ $item5->Invno }}</td>
                                                        <td class="text-center" >{{ $item5->SvID }} </td>
                                                        <td class="text-center" >{{ $item5->Class }} </td>
                                                        <td class="text-center" >{{ $item5->Hcode }} </td>
                                                        <td class="p-2" >{{ $item5->HN }}</td>   
                                                        <td class="p-2" >{{ $item5->PID }}</td> 
                                                        <td class="text-center" >{{ $item5->CareAccount }}</td> 
                                                        <td class="p-2">{{ $item5->TypeServ }}</td> 
                                                        <td class="text-center" >{{ $item5->TypeIn }}</td>  
                                                        <td class="text-center" >{{ $item5->TypeOut }}</td> 
                                                        <td class="text-center" >{{ $item5->DTAppoint }}</td> 
                                                        <td class="p-2" > 
                                                            <input type="checkbox" class="sub_chk2 me-2" data-id="{{$item5->ssop_opservices_id}}">
                                                            <button type="button"class="btn btn-outline-warning btn-sm Edit_svpid"
                                                                value="{{ $item5->ssop_opservices_id }}" data-bs-toggle="tooltip"
                                                                data-bs-placement="left" title="แก้ไข">
                                                                <i class="fa-solid fa-pen-to-square me-2 text-warning"></i>
                                                                <label for="" class="text-warning" style="font-size:13px;"> {{ $item5->SvPID }}</label>
                                                            </button>
                                                        </td> 
                                                        <td class="text-center" >{{ $item5->Clinic }}</td> 
                                                        <td class="text-center" width="15%">{{ $item5->BegDT }}</td> 
                                                        <td class="text-center" width="15%">{{ $item5->EndDT }}</td> 
                                                        <td class="text-center" >{{ $item5->LcCode }}</td> 
                                                        <td class="text-center" >{{ $item5->CodeSet }}</td> 
                                                        <td class="text-center" >{{ $item5->STDCode }}</td> 
                                                        <td class="text-center" >{{ $item5->SvCharge }}</td> 
                                                        <td class="text-center" >{{ $item5->Completion }}</td> 
                                                        <td class="text-center" >{{ $item5->SvTxCode }}</td> 
                                                        <td class="text-center" >{{ $item5->ClaimCat }}</td> 
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </p>
                            </div>
                            <div class="tab-pane" id="OPDX" role="tabpanel">
                                <p class="mb-0">
                                    <div class="table-responsive">
                                        <table id="example3" class="table table-striped table-bordered dt-responsive nowrap"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th width="5%" class="text-center">ลำดับ</th>  
                                                    <th class="text-center">Class</th>
                                                    <th class="text-center">SvID</th>
                                                    <th class="text-center" width="15%">SL</th>
                                                    <th class="text-center" width="15%">CodeSet</th>
                                                    <th class="text-center">code</th>   
                                                    <th class="text-center" >Desc</th>  
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1; ?>
                                                @foreach ($ssop_opdx_ as $item6) 
                                                    <tr>   
                                                        <td class="text-center">{{ $i++ }}</td>    
                                                        <td class="text-center">{{ $item6->Class }}</td>
                                                        <td class="text-center" >{{ $item6->SvID }} </td>
                                                        <td class="text-center" >{{ $item6->SL }} </td>
                                                        <td class="text-center" >{{ $item6->CodeSet }} </td>                                                       
                                                        <td class="text-center" >{{ $item6->code }}</td>  
                                                        <td class="text-center" >{{ $item6->Desc }}</td>  
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
    });
</script>
@endsection
