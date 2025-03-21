@extends('layouts.accountpk')
@section('title', 'PK-OFFICE || Up-Rep')
 
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
            border-top: 10px #fd6812 solid;
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
        .bar{
            height: 50px;
            background-color: rgb(10, 218, 55);
        }
        .percent{
            position: absolute;
            left: 50%;
            color: black;
        }
    </style>
    <?php
    use App\Http\Controllers\StaticController;
    use Illuminate\Support\Facades\DB;
    $count_meettingroom = StaticController::count_meettingroom();
    ?>
    <div class="container-fluid">
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

            <div class="col"></div>
            <div class="col-xl-8 col-md-6">
                <div class="card card_audit_4c mb-3" style="background-color: rgb(239, 247, 235)">
                    <div class="grid-menu-col">
                        <form action="{{ route('acc.account_301_repsave') }}" method="POST" enctype="multipart/form-data"> 
                            @csrf
                            <div class="row">

                                <div class="col"></div>
                                <div class="col-md-6">
                                    <div class="mb-3 mt-3">
                                        <label for="formFileLg" class="form-label">UP REP EXCEL </label>
                                        <input class="form-control form-control-lg" id="formFileLg" name="file"
                                            type="file">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    </div>
                                    {{-- @if ($countc < 1)  --}}
                                    <button type="submit"
                                    class="mb-3 me-2 btn-icon btn-shadow btn-dashed btn btn-outline-info">
                                    <i class="fa-solid fa-cloud-arrow-up me-2" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="UP STM"></i>
                                    UP REP
                                </button>
                    
                                       
                                    {{-- @else --}}
                                    <a class="mb-3 me-2 btn-icon btn-shadow btn-dashed btn btn-outline-primary" id="Senddata_new">
                                        <i class="fa-solid fa-file-import me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="ส่งข้อมูล"></i>
                                            ส่งข้อมูล
                                    </a>
                                       
                                    {{-- @endif --}}
                                    
                                </form>             
                                </div>
                                <div class="col"></div>

                            </div>
                      
                    </div>
                </div>
                <div class="form-group">
                    <div class="progress" style="height: 50px;">
                       <div class="bar"></div>
                       <div class="percent" style="font-size: 30px">0%</div> 
                    </div>
                </div> 
                <br> 
            </div>
            <div class="col"></div>
        </div>


        <div class="row">
 
            <div class="col-xl-12 col-md-6">
                <div class="card p-3 card_audit_4c" style="background-color: rgb(239, 247, 235)">
                    <div class="grid-menu-col"> 
                            <table id="example" class="table table-striped table-bordered "
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead style="border: 1px solid rgb(250, 214, 159);">
                                <tr style="font-size: 12px">
                                    <th class="text-center">ลำดับ</th>
                                    <th class="text-center">repno</th>
                                  
                                    <th class="text-center">tran_id</th>
                                    <th class="text-center">hn</th>
                                    <th class="text-center">cid</th>
                                    <th class="text-center">ชื่อ-สกุล</th>
                                    <th class="text-center">vstdate</th>
                                    <th class="text-center">ยอดเรียกเก็บ</th>
                                    <th class="text-center">Error Code</th>
                                    <th class="text-center">STMDoc</th>

                                </tr>
                            </thead>
                            <tbody style="border: 1px solid rgb(250, 214, 159);">
                                <?php $number = 0;
                                $total1 = 0; ?>
                                @foreach ($datashow as $item)
                                    <?php $number++; ?>

                                    <tr height="20" style="font-size: 12px" >
                                        <td class="text-center" style="text-align: center;" width="4%" >{{ $number }}</td>
                                        <td class="text-center" width="5%" > {{ $item->a }}</td> 
                                        <td class="text-center" width="5%" > {{ $item->c }}</td> 
                                        <td class="text-center" width="5%" > {{ $item->d }}</td> 
                                        <td class="text-center" width="5%" > {{ $item->f }}</td> 
                                        <td class="text-start" width="13%" > {{ $item->g }}</td> 
                                        <td class="text-center" width="7%"> {{ $item->i }}</td> 
                                        <td class="text-center" width="10%" > {{ $item->ao }}</td> 
                                        <td class="text-center" width="10%" > {{ $item->n }}</td> 
                                        <td class="p-2" style="color:rgb(248, 12, 12)"> {{ $item->STMdoc }}</td>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#example').DataTable();
            $('#example2').DataTable();
            $('#datepicker').datepicker({
                format: 'yyyy-mm-dd'
            });
            $('#datepicker2').datepicker({
                format: 'yyyy-mm-dd'
            });

            // var bar = $('.bar');
            // var percent = $('.percent');
            // $('form').ajaxForm({
            //     beforeSend: function() {
            //         var percentVal = '0%';
            //         bar.width(percentVal);
            //         percent.html(percentVal);
            //     },
            //     uploadProgress: function(event, position, total, percentComplete) {
            //         var percentVal = percentComplete+'%';
            //         bar.width(percentVal);
            //         percent.html(percentVal);
            //     },
            //     complete: function(xhr) { 
            //         Swal.fire({
            //             title: 'UP STM สำเร็จ',
            //             text: "You UP STM success",
            //             icon: 'success',
            //             showCancelButton: false,
            //             confirmButtonColor: '#06D177',
            //             // cancelButtonColor: '#d33',
            //             confirmButtonText: 'เรียบร้อย'
            //         }).then((result) => {
            //             if (result.isConfirmed) {
            //                 window.location = "{{ url('ofc_401_rep') }}";
            //             }
            //         })
            //     }
            // })

            $('#Upstmti').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                // alert('OJJJJOL');
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
                        if (data.status == 200) {
                            Swal.fire({
                                title: 'Up Statment สำเร็จ',
                                text: "You Up Statment data success",
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonColor: '#06D177',
                                // cancelButtonColor: '#d33',
                                confirmButtonText: 'เรียบร้อย'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            })

                        } else {
                            Swal.fire({
                                title: 'UP Statment ซ้ำ',
                                text: "You Up Statment data success",
                                icon: 'warning',
                                showCancelButton: false,
                                confirmButtonColor: '#06D177',
                                // cancelButtonColor: '#d33',
                                confirmButtonText: 'เรียบร้อย'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            })
                        }
                    }
                });
            });

            $('#Upstmdata').on('submit',function(e){
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
                                title: 'อัพ STM สำเร็จ',
                                text: "You Up STM success",
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonColor: '#06D177',
                                // cancelButtonColor: '#d33',
                                confirmButtonText: 'เรียบร้อย'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                window.location="{{url('account_401_rep')}}";
                                }
                            })
                        }
                      }
                    });
            });

            $('#Senddata').on('submit',function(e){
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
                                title: 'ส่งข้อมูลสำเร็จ',
                                text: "You Send data success",
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonColor: '#06D177',
                                // cancelButtonColor: '#d33',
                                confirmButtonText: 'เรียบร้อย'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                window.location="{{url('ofc_401_rep')}}";
                                }
                            })
                        }
                      }
                    });
            });

            $('#Senddata_new').click(function() {
                var datepicker = $('#datepicker').val(); 
                var datepicker2 = $('#datepicker2').val(); 
                Swal.fire({ position: "top-end",
                        title: 'ต้องการส่งข้อมูลใช่ไหม ?',
                        text: "You Send data success",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Send it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $("#overlay").fadeIn(300);　
                                $("#spinner").show(); //Load button clicked show spinner 
                                
                                $.ajax({
                                    url: "{{ route('acc.account_401_repsend') }}",
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
                                                title: 'ส่งข้อมูลสำเร็จ',
                                                text: "You Send data success",
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
