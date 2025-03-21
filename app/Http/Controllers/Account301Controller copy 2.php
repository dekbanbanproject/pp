<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\support\Facades\Hash;
use Illuminate\support\Facades\Validator;
use App\Models\User;
use App\Models\Acc_debtor;
use App\Models\Pttype_eclaim;
use App\Models\Account_listpercen;
use App\Models\Leave_month;
use App\Models\Acc_debtor_stamp;
use App\Models\Acc_debtor_sendmoney;
use App\Models\Pttype;
use App\Models\Pttype_acc;
use App\Models\Acc_stm_ti;
use App\Models\Acc_stm_ti_total;
use App\Models\Acc_opitemrece;
use App\Models\Acc_1102050101_202;
use App\Models\Acc_1102050101_217;
use App\Models\Acc_1102050101_2166;
use App\Models\Acc_stm_ucs;
use App\Models\Acc_1102050101_301;
use App\Models\Acc_1102050101_304;
use App\Models\Acc_1102050101_308;
use App\Models\Acc_1102050101_4011;
use App\Models\Acc_1102050101_3099;
use App\Models\Acc_1102050101_401;
use App\Models\Acc_1102050101_402;
use App\Models\Acc_1102050102_801;
use App\Models\Acc_1102050102_802;
use App\Models\Acc_1102050102_803;
use App\Models\Acc_1102050102_804;
use App\Models\Acc_1102050101_4022;
use App\Models\Acc_1102050102_602;
use App\Models\Acc_1102050101_3011;
use App\Models\Acc_1102050101_3012;
use App\Models\Acc_1102050101_3013;
use App\Models\Acc_stm_ti_totalhead;
use App\Models\Acc_stm_ti_excel;
use App\Models\Acc_stm_ofc;
use App\Models\acc_stm_ofcexcel;
use App\Models\Acc_stm_lgo;
use App\Models\Acc_stm_lgoexcel;
use App\Models\Check_sit_auto;
use App\Models\Acc_stm_ucs_excel;
use App\Models\D_claim;
use App\Models\Stm;
use App\Models\Ssop_session;
use App\Models\Ssop_opdx;
use App\Models\Pang_stamp_temp;
use App\Models\Ssop_token;
use App\Models\Ssop_opservices;
use App\Models\Ssop_dispenseditems;
use App\Models\Ssop_dispensing;
use App\Models\Ssop_billtran;
use App\Models\Ssop_billitems;
use App\Models\D_ssop_main;
use App\Models\Fdh_sesion;

use PDF;
use setasign\Fpdi\Fpdi;
use App\Models\Budget_year;
use Illuminate\Support\Facades\File;
use DataTables;
use Intervention\Image\ImageManagerStatic as Image;
use App\Mail\DissendeMail;
use Mail;
use Illuminate\Support\Facades\Storage;
use Auth;
use Http;
use SoapClient;

// use File;
// use SplFileObject;
use Arr;
// use Storage;
use GuzzleHttp\Client;

use App\Imports\ImportAcc_stm_ti;
use App\Imports\ImportAcc_stm_tiexcel_import;
use App\Imports\ImportAcc_stm_ofcexcel_import;
use App\Imports\ImportAcc_stm_lgoexcel_import;
use App\Models\Acc_1102050101_217_stam;
use App\Models\Acc_opitemrece_stm;

use SplFileObject;
use PHPExcel;
use PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

date_default_timezone_set("Asia/Bangkok");


class Account301Controller extends Controller
 {
    // ***************** 301********************************
     
    public function account_301_dash(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $acc_trimart_id = $request->acc_trimart_id;

        $dabudget_year = DB::table('budget_year')->where('active','=',true)->first();
        $leave_month_year = DB::table('leave_month')->orderBy('MONTH_ID', 'ASC')->get();
        $date = date('Y-m-d');
        $y = date('Y') + 543;
        $newweek = date('Y-m-d', strtotime($date . ' -1 week')); //ย้อนหลัง 1 สัปดาห์
        $newDate = date('Y-m-d', strtotime($date . ' -5 months')); //ย้อนหลัง 5 เดือน
        $newyear = date('Y-m-d', strtotime($date . ' -1 year')); //ย้อนหลัง 1 ปี 
       
        $data_ = DB::table('acc_trimart')->where('acc_trimart_id','=',$acc_trimart_id)->first();
        // $startdate_tri = $data_->acc_trimart_start_date;
        // $enddate_tri = $data_->acc_trimart_end_date;
        // dd($data_trimart);
        
       if ($acc_trimart_id == '') {
            $datashow = DB::table('acc_trimart')->where('active','Y')->limit(12)->orderBy('acc_trimart_id','desc')->get();
            $trimart = DB::table('acc_trimart')->where('active','Y')->orderBy('acc_trimart_id','desc')->get();
       } else {
            // $data_trimart = DB::table('acc_trimart')->whereBetween('dchdate', [$startdate, $enddate])->orderBy('acc_trimart_id','desc')->get();
            $datashow = DB::table('acc_trimart')->where('active','Y')->where('acc_trimart_id','=',$acc_trimart_id)->orderBy('acc_trimart_id','desc')->get();
            $trimart = DB::table('acc_trimart')->where('active','Y')->orderBy('acc_trimart_id','desc')->get();
       }
       

        return view('account_301.account_301_dash',[
            'startdate'        => $startdate,
            'enddate'          => $enddate,
            'leave_month_year' => $leave_month_year,
            'datashow'         => $datashow,
            'newyear'          => $newyear,
            'date'             => $date,
            'trimart'          => $trimart,
        ]);
    }
    public function account_301_dashsub(Request $request,$startdate,$enddate)
    {
        $datenow = date('Y-m-d');
        
        $dabudget_year = DB::table('budget_year')->where('active','=',true)->first();
        $leave_month_year = DB::table('leave_month')->orderBy('MONTH_ID', 'ASC')->get();
        $date = date('Y-m-d'); 
        // dd($end );
       
            $datashow = DB::select('
                    SELECT month(a.vstdate) as months,year(a.vstdate) as year,l.MONTH_NAME,l.MONTH_ID
                    ,count(distinct a.hn) as hn
                    ,count(distinct a.vn) as vn
                    ,count(distinct a.an) as an
                    ,sum(a.income) as income
                    ,sum(a.paid_money) as paid_money
                    ,sum(a.income)-sum(a.discount_money)-sum(a.rcpt_money) as total
                    ,sum(a.debit_total) as debit
                    FROM acc_debtor a
                    left outer join leave_month l on l.MONTH_ID = month(a.vstdate)
                    WHERE a.vstdate between "'.$startdate.'" and "'.$enddate.'"
                    and account_code="1102050101.301"
                    group by month(a.vstdate) order by month(a.vstdate) desc;
            ');
            

        return view('account_301.account_301_dashsub',[
            'startdate'          =>  $startdate,
            'enddate'            =>  $enddate,
            'datashow'           =>  $datashow,
            'leave_month_year'   =>  $leave_month_year,
        ]);
    }
    public function account_301_dashsubdetail(Request $request,$months,$year)
    {
        $datenow = date('Y-m-d'); 
        // dd($id);
        $data['users'] = User::get();

        $data = DB::select('
        SELECT 
            vn,hn,cid,ptname,vstdate,pttype,debit_total
            from acc_1102050101_301 
            WHERE month(vstdate) = "'.$months.'"  
            AND year(vstdate) = "'.$year.'"
        ');
        // WHERE month(U1.vstdate) = "'.$months.'" and year(U1.vstdate) = "'.$year.'"
        return view('account_301.account_301_dashsubdetail', $data, [ 
            'data'          =>     $data,
            'year'          =>     $year,
            'months'        =>     $months
        ]);
    }
    public function account_301_pull(Request $request)
    {
        $datenow       = date('Y-m-d');
        $months        = date('m');
        $year          = date('Y');
        $newday        = date('Y-m-d', strtotime($datenow . ' -5 Day')); //ย้อนหลัง 1 สัปดาห์
        $startdate     = $request->startdate;
        $enddate       = $request->enddate;

        if ($startdate == '') {
            // $acc_debtor = Acc_debtor::where('stamp','=','N')->whereBetween('dchdate', [$datenow, $datenow])->get();
            $acc_debtor = DB::select('
                SELECT a.* from acc_debtor a                  
                WHERE a.account_code="1102050101.301"                
                AND vstdate BETWEEN "' . $newday . '" AND "' . $datenow . '"
                group by a.vn
                order by a.vstdate desc;
            ');
            // AND a.stamp = "N"
            // and month(a.dchdate) = "'.$months.'" and year(a.dchdate) = "'.$year.'"
            $data['count_claim'] = Acc_debtor::where('active_claim','=','Y')->where('account_code','=','1102050101.301')->whereBetween('vstdate', [$newday, $datenow])->count();
            $data['count_noclaim'] = Acc_debtor::where('active_claim','=','N')->where('account_code','=','1102050101.301')->whereBetween('vstdate', [$newday, $datenow])->count();
            
        } else {
            $acc_debtor = DB::select('
                SELECT a.* from acc_debtor a
               
                WHERE a.account_code="1102050101.301"
                AND vstdate BETWEEN "' . $startdate . '" AND "' . $enddate . '"
                group by a.vn
                order by a.vstdate desc
            ');
            $data['count_claim'] = Acc_debtor::where('active_claim','=','Y')->where('account_code','=','1102050101.301')->whereBetween('vstdate', [$newday, $datenow])->count();
            $data['count_noclaim'] = Acc_debtor::where('active_claim','=','N')->where('account_code','=','1102050101.301')->whereBetween('vstdate', [$newday, $datenow])->count();
            // $acc_debtor = Acc_debtor::where('stamp','=','N')->whereBetween('dchdate', [$startdate, $enddate])->get();
        }
        // left join checksit_hos c on c.vn = a.vn
        $data['d_ssop_main'] = DB::connection('mysql')->select('SELECT * from d_ssop_main');  
        $data['ssop_billtran'] = DB::connection('mysql')->select('SELECT * from ssop_billtran');  
        $data['ssop_billitems'] = DB::connection('mysql')->select('SELECT * from ssop_billitems'); 
        $data['ssop_dispensing'] = DB::connection('mysql')->select('SELECT * from ssop_dispensing'); 
        $data['ssop_dispenseditems'] = DB::connection('mysql')->select('SELECT * from ssop_dispenseditems');
        $data['ssop_opservices'] = DB::connection('mysql')->select('SELECT * from ssop_opservices');
        $data['ssop_opdx'] = DB::connection('mysql')->select('SELECT * from ssop_opdx');

        return view('account_301.account_301_pull',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'acc_debtor'    =>     $acc_debtor,
        ]);
    }
    public function account_301_pulldata(Request $request)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->datepicker;
        $enddate = $request->datepicker2;

        $type = DB::connection('mysql')->select('
            SELECT pttype from acc_setpang_type WHERE pttype IN (SELECT pttype FROM acc_setpang_type WHERE pang ="1102050101.301")
        ');
        // Acc_opitemrece::truncate();
        $acc_debtor = DB::connection('mysql2')->select(
            'SELECT v.vn,ifnull(o.an,"") as an,o.hn,v.cid,concat(pt.pname,pt.fname," ",pt.lname) as ptname,GROUP_CONCAT(DISTINCT ov.icd10 order by ov.diagtype) AS icd10,v.pdx
                    ,v.vstdate ,o.vsttime ,v.hospmain,op.income as income_group ,ptt.pttype_eclaim_id ,vp.pttype,ptt.max_debt_money
                    ,e.code as acc_code ,e.ar_opd as account_code,e.name as account_name ,v.income,v.uc_money,v.discount_money,v.paid_money,v.rcpt_money ,v.rcpno_list as rcpno
                    ,v.income-v.discount_money-v.rcpt_money as debit
                    ,if(op.icode IN ("3010058"),sum_price,0) as fokliad
                    ,sum(if(op.income="02",sum_price,0)) as debit_instument
                    ,sum(if(op.icode IN("1560016","1540073","1530005","1540048","1620015","1600012","1600015"),sum_price,0)) as debit_drug
                    ,sum(if(op.icode IN("3001412","3001417"),sum_price,0)) as debit_toa
                    ,sum(if(op.icode IN("3010829","3011068","3010864","3010861","3010862","3010863","3011069","3011012","3011070"),sum_price,0)) as debit_refer
                    ,(SELECT SUM(o.sum_price) FROM opitemrece o LEFT JOIN nondrugitems n on n.icode = o.icode WHERE o.vn=v.vn AND o.pttype="A7" AND (n.billcode like "8%" OR n.billcode ="2509") and n.billcode not in ("8608","8307") and o.an is null) as debit_ins_sss
                    ,(SELECT SUM(ot.sum_price) FROM opitemrece ot WHERE EXISTS (SELECT icode FROM xray_items WHERE icode = ot.icode AND xray_items_group ="3") AND vn =v.vn) as debit_ct_sss
            from ovst o
            left join vn_stat v on v.vn=o.vn
            LEFT JOIN visit_pttype vp on vp.vn = v.vn
            left join patient pt on pt.hn=o.hn
            LEFT JOIN pttype ptt on o.pttype=ptt.pttype
            LEFT JOIN pttype_eclaim e on e.code=ptt.pttype_eclaim_id
            LEFT JOIN ovstdiag ov ON ov.vn = v.vn
            LEFT JOIN opitemrece op ON op.vn = o.vn
            WHERE v.vstdate BETWEEN "' . $startdate . '" AND "' . $enddate . '"
            AND vp.pttype IN (SELECT pttype FROM pkbackoffice.acc_setpang_type WHERE pang ="1102050101.301" AND pttype IS NOT NULL)             
            AND v.income-v.discount_money-v.rcpt_money <> 0 
            AND (v.cid IS NOT NULL or v.cid <>"")
            and (o.an="" or o.an is null)
            GROUP BY v.vn
        ');
        // ,e.code as acc_code
        // ,e.ar_opd as account_code 
        // ,e.name as account_name
        // AND v.hospmain = "10702"

        foreach ($acc_debtor as $key => $value) {
                    $check = Acc_debtor::where('vn', $value->vn)->where('account_code','1102050101.301')->count();
                    // $check = Acc_debtor::where('vn', $value->vn)->whereBetween('vstdate', [$startdate, $enddate])->count();
                    if ($check > 0) {
                        Acc_debtor::where('vn', $value->vn)->where('account_code','1102050101.301')->update([
                            'debit_total'        => $value->debit - ($value->debit_ins_sss+$value->debit_ct_sss),
                            'debit_ins_sss'      => $value->debit_ins_sss,
                            'debit_ct_sss'       => $value->debit_ct_sss,
                            'pdx'                => $value->pdx,
                            'icd10'              => $value->icd10, 
                        ]);                     
                                                                    
                    } else {
                        if ($value->cid !='') {
                            Acc_debtor::insert([
                                'hn'                 => $value->hn,
                                'an'                 => $value->an,
                                'vn'                 => $value->vn,
                                'cid'                => $value->cid,
                                'ptname'             => $value->ptname,
                                'pttype'             => $value->pttype,
                                'vstdate'            => $value->vstdate,
                                'acc_code'           => $value->acc_code,
                                'account_code'       => $value->account_code,
                                'account_name'       => $value->account_name, 
                                'income'             => $value->income,
                                'uc_money'           => $value->uc_money,
                                'discount_money'     => $value->discount_money,
                                'paid_money'         => $value->paid_money,
                                'rcpt_money'         => $value->rcpt_money,
                                'debit'              => $value->debit,
                                'debit_drug'         => $value->debit_drug,
                                'debit_instument'    => $value->debit_instument,
                                'debit_toa'          => $value->debit_toa,
                                'debit_refer'        => $value->debit_refer, 
                                'fokliad'            => $value->fokliad,
                                // 'debit_total'        => $value->debit,
                                'debit_total'        => $value->debit - ($value->debit_ins_sss+$value->debit_ct_sss),
                                'debit_ins_sss'      => $value->debit_ins_sss,
                                'debit_ct_sss'       => $value->debit_ct_sss, 
                                'max_debt_amount'    => $value->max_debt_money,
                                'pdx'                => $value->pdx,
                                'icd10'              => $value->icd10, 
                                'acc_debtor_userid'  => Auth::user()->id
                            ]);  
                        }   
                    }  
                    if ($value->debit_ins_sss > 0) {
                        $check_sss = Acc_debtor::where('vn', $value->vn)->where('account_code','1102050101.3011')->count();
                        if ($check_sss > 0) { 
                        } else {
                            Acc_debtor::insert([
                                'hn'                 => $value->hn,
                                'an'                 => $value->an,
                                'vn'                 => $value->vn,
                                'cid'                => $value->cid,
                                'ptname'             => $value->ptname,
                                'pttype'             => $value->pttype,
                                'vstdate'            => $value->vstdate, 
                                'account_code'       => "1102050101.3011", 
                                'account_name'       => $value->account_name, 
                                'income'             => $value->income,
                                'uc_money'           => $value->uc_money,
                                'discount_money'     => $value->discount_money,
                                'paid_money'         => $value->paid_money,
                                'rcpt_money'         => $value->rcpt_money,
                                'debit'              => $value->debit,
                                'debit_drug'         => $value->debit_drug,
                                'debit_instument'    => $value->debit_instument,
                                'debit_toa'          => $value->debit_toa,
                                'debit_refer'        => $value->debit_refer, 
                                'fokliad'            => $value->fokliad, 
                                'debit_total'        => $value->debit_ins_sss, 
                                'debit_ins_sss'      => $value->debit_ins_sss,
                                'debit_ct_sss'       => $value->debit_ct_sss, 
                                'max_debt_amount'    => $value->max_debt_money,
                                'pdx'                => $value->pdx,
                                'icd10'              => $value->icd10, 
                                'acc_debtor_userid'  => Auth::user()->id
                            ]); 
                        } 
                    }
                    
                    // if ($value->debit_ct_sss > 0) {
                    //     $check_ct = Acc_debtor::where('vn', $value->vn)->where('account_code','1102050101.3013')->count();
                    //     if ($check_ct > 0) { 
                    //     } else {
                    //         Acc_debtor::insert([
                    //             'hn'                 => $value->hn,
                    //             'an'                 => $value->an,
                    //             'vn'                 => $value->vn,
                    //             'cid'                => $value->cid,
                    //             'ptname'             => $value->ptname,
                    //             'pttype'             => $value->pttype,
                    //             'vstdate'            => $value->vstdate, 
                    //             'account_code'       => "1102050101.3013", 
                    //             'account_name'       => $value->account_name, 
                    //             'income'             => $value->income,
                    //             'uc_money'           => $value->uc_money,
                    //             'discount_money'     => $value->discount_money,
                    //             'paid_money'         => $value->paid_money,
                    //             'rcpt_money'         => $value->rcpt_money,
                    //             'debit'              => $value->debit,
                    //             'debit_drug'         => $value->debit_drug,
                    //             'debit_instument'    => $value->debit_instument,
                    //             'debit_toa'          => $value->debit_toa,
                    //             'debit_refer'        => $value->debit_refer, 
                    //             'fokliad'            => $value->fokliad, 
                    //             'debit_total'        => $value->debit_ct_sss, 
                    //             'debit_ins_sss'      => $value->debit_ins_sss,
                    //             'debit_ct_sss'       => $value->debit_ct_sss, 
                    //             'max_debt_amount'    => $value->max_debt_money,
                    //             'acc_debtor_userid'  => Auth::user()->id
                    //         ]); 
                    //     }
                       
                    // }   
        }

            return response()->json([

                'status'    => '200'
            ]);
    }
    public function account_301_checksit(Request $request)
    {
        $datestart = $request->datestart;
        $dateend   = $request->dateend;
        $date      = date('Y-m-d');
        $id        = $request->ids;
        
        // $data_sitss = DB::connection('mysql')->select('SELECT vn,an,cid,vstdate,dchdate FROM acc_debtor WHERE account_code="1102050101.301" AND stamp = "N" GROUP BY vn');
       //  AND subinscl IS NULL
           //  LIMIT 30
        // WHERE vstdate = CURDATE()
        // BETWEEN "2024-02-03" AND "2024-02-15"
        // $token_data = DB::connection('mysql')->select('SELECT cid,token FROM ssop_token');
        $data_sitss = Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))->get();
        $token_data = DB::connection('mysql10')->select('SELECT * FROM nhso_token ORDER BY update_datetime desc limit 1');
        foreach ($token_data as $key => $value) { 
            $cid_    = $value->cid;
            $token_  = $value->token;
        }
        
        foreach ($data_sitss as $key => $item) {
            $pids = $item->cid;
            $vn   = $item->vn; 
            $an   = $item->an; 
                // $token_data = DB::connection('mysql10')->select('SELECT cid,token FROM hos.nhso_token where token <> ""');
                // foreach ($token_data as $key => $value) { 
                    $client = new SoapClient("http://ucws.nhso.go.th/ucwstokenp1/UCWSTokenP1?wsdl",
                        array("uri" => 'http://ucws.nhso.go.th/ucwstokenp1/UCWSTokenP1?xsd=1',"trace" => 1,"exceptions" => 0,"cache_wsdl" => 0)
                        );
                        $params = array(
                            'sequence' => array(
                                "user_person_id"   => "$cid_",
                                "smctoken"         => "$token_",
                                // "user_person_id" => "$value->cid",
                                // "smctoken"       => "$value->token",
                                "person_id"        => "$pids"
                        )
                    );
                    $contents = $client->__soapCall('searchCurrentByPID',$params);
                    foreach ($contents as $v) {
                        @$status = $v->status ;
                        @$maininscl = $v->maininscl;
                        @$startdate = $v->startdate;
                        @$hmain = $v->hmain ;
                        @$subinscl = $v->subinscl ;
                        @$person_id_nhso = $v->person_id;

                        @$hmain_op = $v->hmain_op;  //"10978"
                        @$hmain_op_name = $v->hmain_op_name;  //"รพ.ภูเขียวเฉลิมพระเกียรติ"
                        @$hsub = $v->hsub;    //"04047"
                        @$hsub_name = $v->hsub_name;   //"รพ.สต.แดงสว่าง"
                        @$subinscl_name = $v->subinscl_name ; //"ช่วงอายุ 12-59 ปี"

                        IF(@$maininscl == "" || @$maininscl == null || @$status == "003" ){ #ถ้าเป็นค่าว่างไม่ต้อง insert
                            $date = date("Y-m-d");
                          
                            Acc_debtor::where('vn', $vn)
                            ->update([
                                'status'         => 'จำหน่าย/เสียชีวิต',
                                'maininscl'      => @$maininscl,
                                'pttype_spsch'   => @$subinscl,
                                'hmain'          => @$hmain,
                                'subinscl'       => @$subinscl, 
                            ]);
                            
                        }elseif(@$maininscl !="" || @$subinscl !=""){
                           Acc_debtor::where('vn', $vn)
                           ->update([
                               'status'         => @$status,
                               'maininscl'      => @$maininscl,
                               'pttype_spsch'   => @$subinscl,
                               'hmain'          => @$hmain,
                               'subinscl'       => @$subinscl,
                           
                           ]); 
                                    
                        }

                    }
           
        }

        return response()->json([

           'status'    => '200'
       ]);

    }
    public function account_301_stam(Request $request)
    {
        $id = $request->ids;
        $iduser = Auth::user()->id;
        $data = Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))->get();
            Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))
                    ->update([
                        'stamp' => 'Y'
                    ]);
        foreach ($data as $key => $value) {
                $date = date('Y-m-d H:m:s');
            //  $check = Acc_debtor::where('vn', $value->vn)->where('account_code','1102050101.4011')->where('account_code','1102050101.4011')->count();
                $check = Acc_1102050101_301::where('vn', $value->vn)->count();
                if ($check > 0) {
                # code...
                } else {
                    Acc_1102050101_301::insert([
                            'vn'                => $value->vn,
                            'hn'                => $value->hn,
                            'an'                => $value->an,
                            'cid'               => $value->cid,
                            'ptname'            => $value->ptname,
                            'vstdate'           => $value->vstdate,
                            'regdate'           => $value->regdate,
                            'dchdate'           => $value->dchdate,
                            'pttype'            => $value->pttype,
                            'pttype_nhso'       => $value->pttype_spsch,
                            'acc_code'          => $value->acc_code,
                            'account_code'      => $value->account_code,
                            'income'            => $value->income, 
                            'uc_money'          => $value->uc_money,
                            'discount_money'    => $value->discount_money,
                            'rcpt_money'        => $value->rcpt_money,
                            'debit'             => $value->debit,
                            'debit_drug'        => $value->debit_drug,
                            'debit_instument'   => $value->debit_instument,
                            'debit_refer'       => $value->debit_refer,
                            'debit_toa'         => $value->debit_toa,
                            'debit_total'       => $value->debit_total, 
                            'debit_ins_sss'     => $value->debit_ins_sss,
                            'debit_ct_sss'      => $value->debit_ct_sss,  
                            'max_debt_amount'   => $value->max_debt_amount,
                            'acc_debtor_userid' => $iduser
                    ]);
                }

        }
        return response()->json([
            'status'    => '200'
        ]);
    }
    public function account_301_destroy_all(Request $request)
    {
        $id = $request->ids;
        $iduser = Auth::user()->id;
        $data = Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))->get();
            Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))->delete();
                   
        return response()->json([
            'status'    => '200'
        ]);
    }
    public function account_301_detail(Request $request,$startdate,$enddate)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        // dd($id);
        $data['users'] = User::get();

        $data = DB::select('
        SELECT U1.vn,U1.hn,U1.cid,U1.ptname,U1.vstdate,U1.pttype,U1.debit_total
            from acc_1102050101_301 U1
        
            WHERE U1.vstdate BETWEEN "'.$startdate.'" and "'.$enddate.'"
            GROUP BY U1.vn
        ');
        // WHERE month(U1.vstdate) = "'.$months.'" and year(U1.vstdate) = "'.$year.'"
        return view('account_301.account_301_detail', $data, [ 
            'data'          =>     $data,
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate
        ]);
    }
    public function account_301_detail_date(Request $request)
    { 
        $data['users'] = User::get();
        $startdate = $request->startdate;
        $enddate = $request->enddate;

        $data = DB::select('
        SELECT *
    
            from acc_1102050101_301 U1
        
            WHERE U1.vstdate BETWEEN "'.$startdate.'" and "'.$enddate.'"
            GROUP BY U1.vn
        ');
        // U1.vn,U1.hn,U1.cid,U1.ptname,U1.vstdate,U1.pttype,U1.debit_total
        return view('account_301.account_301_detail_date', $data, [ 
            'data'           =>     $data,
            'startdate'      =>     $startdate,
            'enddate'        =>     $enddate
        ]);
    }
    public function account_301_income(Request $request,$startdate,$enddate)
    {
        $datenow = date('Y-m-d');
        $months = date('m');
        $year = date('Y'); 
        $startdate     = $request->startdate;
        $enddate       = $request->enddate;
        if ($startdate == '') {
            // $acc_debtor = Acc_debtor::where('stamp','=','N')->whereBetween('dchdate', [$datenow, $datenow])->get();
                $datashow = DB::select('SELECT * from acc_1102050101_301  GROUP BY vn ORDER BY vstdate DESC');
            // and month(a.dchdate) = "'.$months.'" and year(a.dchdate) = "'.$year.'"
        } else {
            $datashow = DB::select('SELECT * from acc_1102050101_301 WHERE vstdate BETWEEN "'.$startdate.'" AND "'.$enddate.'"  GROUP BY vn ORDER BY vstdate DESC');
             
            // $acc_debtor = Acc_debtor::where('stamp','=','N')->whereBetween('dchdate', [$startdate, $enddate])->get();
        }

        return view('account_301.account_301_income',[
            'startdate'     =>  $startdate,
            'enddate'       =>  $enddate,
            'datashow'      =>  $datashow,
        ]);
    }
    public function account_301_stm_date(Request $request,$startdate,$enddate)
    {
        $datenow       = date('Y-m-d'); 
        $startdate     = $request->startdate;
        $enddate       = $request->enddate;
        if ($startdate == '') { 
                $datashow = DB::select('SELECT * from acc_1102050101_301  GROUP BY vn ORDER BY vstdate DESC'); 
        } else {
            $datashow = DB::select('SELECT * from acc_1102050101_301 WHERE vstdate BETWEEN "'.$startdate.'" AND "'.$enddate.'"  GROUP BY vn ORDER BY vstdate DESC'); 
        }

        return view('account_301.account_301_stm_date',[
            'startdate'     =>  $startdate,
            'enddate'       =>  $enddate,
            'datashow'      =>  $datashow,
        ]);
    }


    // ************************** CLAIM ***********************************************

    public function account_301_claim(Request $request)
    { 
        // $data_vn_1 = DB::connection('mysql')->select('SELECT vn,an from d_ssop_main');
    
        Ssop_billtran::truncate(); 
        Ssop_billitems::truncate();
        Ssop_dispensing::truncate();
        Ssop_dispenseditems::truncate();
        Ssop_opservices::truncate();
        Ssop_opdx::truncate();
       
        Fdh_sesion::where('d_anaconda_id', '=', 'SSS_OP')->delete(); 
        $s_date_now  = date("Y-m-d");
        $s_time_now  = date("H:i:s");
        $id          = $request->ids;
        $iduser      = Auth::user()->id;

        $data_vn_1    = Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))->get();

        #ตัดขีด, ตัด : ออก
        $pattern_date = '/-/i';
        $s_date_now_preg = preg_replace($pattern_date, '', $s_date_now);
        $pattern_time = '/:/i';
        $s_time_now_preg = preg_replace($pattern_time, '', $s_time_now);
        #ตัดขีด, ตัด : ออก
        $folder_name = 'SSS_OP_' . $s_date_now_preg . '_' . $s_time_now_preg;
        Fdh_sesion::insert([
            'folder_name'      => $folder_name,
            'd_anaconda_id'    => 'SSS_OP',
            'date_save'        => $s_date_now,
            'time_save'        => $s_time_now,
            'userid'           => $iduser
        ]);

        foreach ($data_vn_1 as $key => $va1) {
            $ssop_billtran_ = DB::connection('mysql2')->select('  
                SELECT o.vn as Invno,p.hn as HN,o.an,o.vstdate,o.vsttime,o.hcode as Hcode,pt.pttype,v.pdx as Diag,v.dx0,v.dx1,v.dx2,v.dx3,v.dx4,v.dx5,v.sex,v.uc_money
                    ,op.icode,op.qty,op.unitprice,op.income,op.paidst,op.sum_price,CONCAT(p.pname,p.fname," ",p.lname) AS "Name",pt.nhso_code
                    ,d.name as doctorname,d.licenseno
                    ,"01" AS "Station", "" AS "Authencode", CONCAT(o.vstdate,"T",o.vsttime) AS "DTtran","" AS "Billno"
                    , "" AS "MemberNo",v.uc_money AS "Amount","0.00" AS "Paid"
                    ,"" AS "VerCode", "A" AS "Tflag",v.cid AS "Pid"
                    ,o.hospmain AS "HMain", "80" AS "PayPlan"
                    ,v.uc_money AS "ClaimAmt"										
                    ,"" AS "OtherPayplan"
                    ,"0.00" AS "OtherPay" ,o.spclty 
                    FROM ovst o  
                    LEFT OUTER JOIN vn_stat v ON o.vn=v.vn
                    LEFT OUTER JOIN opitemrece op ON o.vn=op.vn          
                    LEFT OUTER JOIN patient p ON o.hn=p.hn   
                    LEFT OUTER JOIN pttype pt on pt.pttype = o.pttype 
                    LEFT OUTER JOIN doctor d on d.code = o.doctor 
                    WHERE o.vn IN("'.$va1->vn.'")
	                GROUP BY o.vn;
            '); 
            foreach ($ssop_billtran_ as $key => $value) {
                Ssop_billtran::insert([
                    'Station'           => $value->Station,
                    'Authencode'        => $value->Authencode,
                    'vstdate'           => $value->vstdate,
                    'DTtran'            => $value->DTtran,
                    'Hcode'             => "10978",
                    'Invno'             => $value->Invno, 
                    'VerCode'           => $value->VerCode, 
                    'Tflag'             => $value->Tflag, 
                    'HMain'             => $value->HMain, 
                    'HN'                => $value->HN, 
                    'Pid'               => $value->Pid, 
                    'Name'              => $value->Name, 
                    'Amount'            => $value->Amount, 
                    'Paid'              => $value->Paid, 
                    'ClaimAmt'          => $value->ClaimAmt, 
                    'PayPlan'           => $value->PayPlan, 
                    'OtherPay'          => $value->OtherPay, 
                    'OtherPayplan'      => $value->OtherPayplan, 
                    'pttype'            => $value->pttype, 
                    'Diag'              => $value->Diag, 
                ]);
                    
            }

            $ssop_billitems_ = DB::connection('mysql2')->select('  
                SELECT op.vn AS "Invno"
                        ,op.vstdate AS "SvDate"
                        , i.income_group AS "BillMuad"
                        , op.icode AS "LCCode"
                        ,ifnull(if(i.income in (03,04),sk.tmt_tmlt,sd.nhso_adp_code),sd.nhso_adp_code) AS STDCode
                        ,sd.name AS "Desc"        
                        ,op.qty AS "QTY"
                        , ROUND(op.unitprice,2) AS "UnitPrice"
                        , ROUND(op.sum_price,2) AS "ChargeAmt"
                        , ROUND(op.unitprice,2) AS "ClaimUP"
                        , ROUND(op.sum_price,2)  AS "ClaimAmount"
                        , op.vn AS "SvRefID"
                        , "OP1" AS "ClaimCat"
                        ,"02" As "paidst"
                        FROM opitemrece op 
                        LEFT OUTER JOIN s_drugitems sd ON sd.icode=op.icode  
                        LEFT OUTER JOIN income i ON i.income=op.income
                        LEFT OUTER JOIN pkbackoffice.ssop_drugcat_labcat sk ON sk.icode=op.icode
                        WHERE op.vn IN("'.$va1->vn.'")
                        AND op.qty <> 0 AND op.unitprice <> 0
                        AND op.paidst="02" 
            ');  
            foreach ($ssop_billitems_ as $key => $value2) {           
                $add2= new Ssop_billitems();
                $add2->Invno       = $value2->Invno ; 
                $add2->SvDate      = $value2->SvDate; 
                $add2->BillMuad    = $value2->BillMuad;
                $add2->LCCode      = $value2->LCCode;
                if ($value2->STDCode == 'XXXXXX') {
                    $add2->STDCode = '';
                } else {
                    $add2->STDCode = $value2->STDCode;
                }                        
                $add2->Desc        = $value2->Desc;
                $add2->QTY         = $value2->QTY;
                $add2->UnitPrice   = $value2->UnitPrice;
                $add2->ChargeAmt   = $value2->ChargeAmt;
                $add2->ClaimUP     = $value2->ClaimUP;
                $add2->ClaimAmount = $value2->ClaimAmount;
                $add2->SvRefID     = $value2->SvRefID;
                $add2->ClaimCat    = $value2->ClaimCat;
                $add2->paidst      = $value2->paidst; 
                $add2->save();
            } 

            $ssop_dispensing_ = DB::connection('mysql2')->select('   
                    SELECT "10978" AS "ProviderID" , o.vn AS "DispID" , o.vn AS "Invno", o.hn AS "HN", v.cid AS "PID"
                    ,CONCAT(o.vstdate,"T",o.vsttime) AS "Prescdt" , CONCAT(o.vstdate,"T",o.vsttime) AS "Dispdt"
                    ,IFNULL( (SELECT licenseno FROM doctor WHERE code=o.doctor) ,"ว64919") AS "Prescb"
                    ,SUM(IF(op.income IN ("03","17","05"),"1","")) AS "Itemcnt"
                    ,ROUND( SUM(IF(op.income IN ("03","17","05"),op.sum_price,0)) ,2) AS "ChargeAmt"
                    ,ROUND( SUM(IF(op.income IN ("03","17","05"),op.sum_price,0)) ,2) AS "ClaimAmt"
                    ,"0.00" AS "Paid" ,"0.00" AS "OtherPay" , "HP" AS "Reimburser" , "SS" AS "BenefitPlan" , "1" AS "DispeStat" , " " AS "SvID"," " AS "DayCover"				
                    FROM ovst o 
                    LEFT OUTER JOIN vn_stat v ON o.vn=v.vn
                    LEFT OUTER JOIN opitemrece op ON o.vn=op.vn
                    LEFT OUTER JOIN pttype pt on pt.pttype = o.pttype 
                    WHERE op.vn IN("'.$va1->vn.'")
                    AND op.income IN ("03","17","05") 
                    AND op.qty<>0 
                    AND op.paidst="02"
                    AND pt.pttype ="A7"
                    GROUP BY o.vn
            ');  
            foreach ($ssop_dispensing_ as $key => $value3) {           
                    $add3= new Ssop_dispensing();
                    $add3->ProviderID     = $value3->ProviderID ; 
                    $add3->DispID         = $value3->DispID; 
                    $add3->Invno          = $value3->Invno;
                    $add3->HN             = $value3->HN;
                    $add3->PID            = $value3->PID;
                    $add3->Prescdt        = $value3->Prescdt;
                    $add3->Dispdt         = $value3->Dispdt;
                    $add3->Prescb         = $value3->Prescb;
                    $add3->Itemcnt        = $value3->Itemcnt;
                    $add3->ChargeAmt      = $value3->ChargeAmt;
                    $add3->ClaimAmt       = $value3->ClaimAmt;
                    $add3->Paid           = $value3->Paid;
                    $add3->OtherPay       = $value3->OtherPay;
                    $add3->Reimburser     = $value3->Reimburser; 
                    $add3->BenefitPlan    = $value3->BenefitPlan; 
                    $add3->DispeStat      = $value3->DispeStat; 
                    $add3->SvID           = $value3->SvID; 
                    $add3->DayCover       = $value3->DayCover; 
                    $add3->save();
            } 

            $ssop_dispenseditems_ = DB::connection('mysql2')->select(' 
                    SELECT  o.vn AS "DispID", IF(di.icode<>"",di.sks_product_category_id, di.sks_product_category_id) AS "PrdCat"
                    ,op.icode AS "HospDrgID", IF(di.sks_drug_code!="",di.sks_drug_code,"") AS "DrgID" 
                    ,di.name AS "dfsText", di.units AS "Packsize"
                    ,IF(op.sp_use!="",op.sp_use,op.drugusage) AS "sigCode"
                    ,IF(op.sp_use!=""
                    ,(SELECT CONCAT( ifnull(name1,""), ifnull(name2,""), ifnull(name3,"") ) FROM sp_use where sp_use=op.sp_use )
                    ,(SELECT CONCAT( ifnull(name1,""), ifnull(name2,""), ifnull(name3,"") ) FROM drugusage WHERE drugusage=op.drugusage )
                    ) AS "sigText"
                    , op.qty AS "Quantity", ROUND(op.unitprice,2) AS "UnitPrice", ROUND(op.sum_price,2) AS "ChargeAmt", ROUND(op.unitprice,2) AS "ReimbPrice", ROUND(op.sum_price,2) AS 					"ReimbAmt","" AS "PrdSeCode"
                    ,"OD" AS "Claimcont", "OP1" AS "ClaimCat"              
                    ,"02" AS "paidst"
                    FROM ovst o  
                    LEFT JOIN opitemrece op ON o.vn=op.vn
                    LEFT JOIN pttype pt on pt.pttype = o.pttype 
                    LEFT JOIN s_drugitems di ON di.icode=op.icode
                    WHERE o.vn IN("'.$va1->vn.'")
                    AND op.income IN ("03","17","05") 
                    AND op.qty<>0 
                    AND op.paidst="02"
                    AND pt.pttype ="A7"   
            ');
            foreach ($ssop_dispenseditems_ as $key => $value4) {           
                $add4= new Ssop_dispenseditems();
                $add4->DispID         = $value4->DispID ; 
                $add4->PrdCat         = $value4->PrdCat; 
                $add4->HospDrgID      = $value4->HospDrgID;
                $add4->DrgID          = $value4->DrgID;
                $add4->dfsText        = $value4->dfsText;
                
    
                if ($value4->Packsize == '') {
                    $add4->Packsize = "Unit";
                } else {
                    $add4->Packsize = $value4->Packsize;
                }
                if ($value4->sigCode == '') {
                    $add4->sigCode = "0004";
                } else {
                    $add4->sigCode = $value4->sigCode;
                }
                if ($value4->sigText == '') {
                    $add4->sigText = "ใช้ตามแพทย์สั่ง";
                } else {
                    $add4->sigText = $value4->sigText;
                }
                
                $add4->Quantity      = $value4->Quantity;
                $add4->UnitPrice     = $value4->UnitPrice;
                $add4->ChargeAmt     = $value4->ChargeAmt;
                $add4->ReimbPrice    = $value4->ReimbPrice;
                $add4->ReimbAmt      = $value4->ReimbAmt;
                $add4->PrdSeCode     = $value4->PrdSeCode; 
                $add4->Claimcont     = $value4->Claimcont; 
                $add4->ClaimCat      = $value4->ClaimCat; 
                $add4->paidst        = $value4->paidst;  
                $add4->save();
            } 

            $ssop_opservices_ = DB::connection('mysql2')->select('   
                SELECT o.vn AS "Invno", o.vn AS "SvID", "EC" AS "Class", "10978" AS "Hcode", o.hn AS "HN", v.cid AS "PID"
                ,"1" AS "CareAccount", "01" AS "TypeServ", "1" AS "TypeIn", "1" AS "TypeOut", "" AS "DTAppoint"
                ,CASE  
                WHEN d.licenseno LIKE "-%" THEN "ว71021"
                WHEN d.licenseno LIKE "พท%" THEN d.licenseno
                WHEN d.licenseno LIKE "พ%" THEN "ว34064"  
                ELSE "ว64919" 
                END as SvPID

                ,IF(o.spclty NOT IN ("01","02","03","04","05","06","07","08","09","10","11","12"),"99",o.spclty) AS "Clinic"
                , CONCAT(o.vstdate,"T",o.vsttime) AS "BegDT", CONCAT(o.vstdate,"T",o.vsttime) AS "EndDT"
                ,op.icode as LcCode
                ,ifnull(case  
                when inc.income in (02) then sd.nhso_adp_code
                when inc.income in (03,04) then dd.billcode 
                when inc.income in (06,07) then sd.nhso_adp_code
                else sd.nhso_adp_code end,"") CSCode
                , "" AS "CodeSet"
                ,ifnull(case  
                when inc.income in (03,04) then dd.tmt_tmlt
                when inc.income in (06,07) then dd.tmt_tmlt
                else "" end,"") STDCode

                , "0.00" AS "SvCharge", "Y" AS "Completion", "" AS "SvTxCode", "OP1" AS "ClaimCat"
                FROM ovst o  
                LEFT OUTER JOIN vn_stat v ON o.vn=v.vn
                LEFT OUTER JOIN ovstdiag ov on ov.vn = v.vn
                LEFT OUTER JOIN doctor d on d.`code` = ov.doctor
                left outer join opitemrece op on op.vn=o.vn
                LEFT OUTER JOIN income inc on inc.income=op.income
                LEFT OUTER JOIN s_drugitems sd on op.icode=sd.icode
                LEFT OUTER JOIN pkbackoffice.ssop_drugcat_labcat dd on dd.icode=op.icode
                WHERE o.vn IN("'.$va1->vn.'")
                AND v.pttype ="A7"
                GROUP BY o.vn
                
            ');  
        
            foreach ($ssop_opservices_ as $key => $value5) {           
                $add5= new Ssop_opservices();
                $add5->Invno       = $value5->Invno ; 
                $add5->SvID        = $value5->SvID; 
                $add5->Class       = $value5->Class;
                $add5->Hcode       = $value5->Hcode;
                $add5->HN          = $value5->HN; 
                $add5->PID         = $value5->PID;
                $add5->CareAccount = $value5->CareAccount;
                $add5->TypeServ    = $value5->TypeServ;
                $add5->TypeIn      = $value5->TypeIn;
                $add5->TypeOut     = $value5->TypeOut;
                $add5->DTAppoint   = $value5->DTAppoint; 
                $add5->SvPID       = $value5->SvPID; 
                $add5->Clinic      = $value5->Clinic; 
                $add5->BegDT       = $value5->BegDT;
                $add5->EndDT       = $value5->EndDT;
                $add5->LcCode      = $value5->LcCode;
                $add5->CodeSet     = $value5->CodeSet;
                $add5->STDCode     = $value5->STDCode;
                $add5->SvCharge    = $value5->SvCharge;
                $add5->Completion  = $value5->Completion;
                $add5->SvTxCode    = $value5->SvTxCode;
                $add5->ClaimCat    = $value5->ClaimCat;  
                $add5->save();
            } 

            $ssop_opdx_ = DB::connection('mysql2')->select('   
                SELECT "EC" AS "Class", o.vn AS "SvID", od.diagtype AS "SL", "IT" AS "CodeSet" 
                ,IF(od.icd10 like "M%", SUBSTR(od.icd10,1,4) ,IF(od.icd10 like "Z%", SUBSTR(od.icd10,1,4) ,od.icd10)) as code
                ," " as "Desc"

                FROM ovst o 
               
                LEFT JOIN ovstdiag od ON o.vn=od.vn
                WHERE o.vn IN("'.$va1->vn.'")
                AND od.icd10 NOT BETWEEN "0000" AND "9999"            
            '); 
            // Ssop_opdx::truncate();
            foreach ($ssop_opdx_ as $key => $valueop) {           
                $addop= new Ssop_opdx();
                $addop->Class     = $valueop->Class ; 
                $addop->SvID      = $valueop->SvID; 
                $addop->SL        = $valueop->SL;
                $addop->CodeSet   = $valueop->CodeSet;
                $addop->code      = $valueop->code; 
                $addop->Desc      = $valueop->Desc; 
                $addop->save();
            } 

        }

        Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))
        ->update([
            'active_claim' => 'Y'
        ]);

        return response()->json([
            'status'    => '200'
        ]);
    }
    public function account_301_prescb_update(Request $request)
    {
        // $ssop_dispensing = DB::connection('mysql7')->select('   
        //     SELECT * FROM ssop_dispensing   
        // ');  
        $id = $request->ids; 
        Ssop_dispensing::whereIn('ssop_dispensing_id',explode(",",$id)) 
                    ->update([   
                        'Prescb'  => 'ว64919'
                    ]); 
        return response()->json([
            'status'     => '200' 
        ]); 
    }
    public function account_301_svpid_update(Request $request)
    {
        $id = $request->ids2; 
        Ssop_opservices::whereIn('ssop_opservices_id',explode(",",$id)) 
                    ->update([   
                        'SvPID'  => 'ว64919'
                    ]);
        return response()->json([
            'status'     => '200' 
        ]);
        // return redirect()->route('claim.ssop'); 
    }
   
 

 }