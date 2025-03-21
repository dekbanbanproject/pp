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
use App\Models\Acc_1102050102_603;
use App\Models\Acc_stm_prb;
use App\Models\Orginfo;
use App\Models\Acc_stm_ti_excel;
use App\Models\Acc_stm_ofc;
use App\Models\acc_stm_ofcexcel;
use App\Models\Acc_stm_lgo;
use App\Models\Acc_stm_lgoexcel;
use App\Models\Check_sit_auto;
use App\Models\Acc_stm_ucs_excel;

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
use App\Models\Acc_temp603;

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


class Account603Controller extends Controller
 {

    public function account_603_dash_old(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $dabudget_year = DB::table('budget_year')->where('active','=',true)->first();
        $leave_month_year = DB::table('leave_month')->orderBy('MONTH_ID', 'ASC')->get();
        $date = date('Y-m-d');
        $y = date('Y') + 543;
        $newweek = date('Y-m-d', strtotime($date . ' -1 week')); //ย้อนหลัง 1 สัปดาห์
        $newDate = date('Y-m-d', strtotime($date . ' -5 months')); //ย้อนหลัง 5 เดือน
        $newyear = date('Y-m-d', strtotime($date . ' -1 year')); //ย้อนหลัง 1 ปี
        $yearnew = date('Y')+1;
        $yearold = date('Y')-1;
        $start = (''.$yearold.'-10-01');
        $end = (''.$yearnew.'-09-30');
        // dd($start );
        if ($startdate == '') {
            $datashow = DB::select('
                SELECT month(a.dchdate) as months,year(a.dchdate) as year,l.MONTH_NAME
                    ,count(distinct a.hn) as hn
                    ,count(distinct a.vn) as vn
                    ,sum(a.paid_money) as paid_money
                    ,sum(a.income) as income
                    ,sum(a.income)-sum(a.discount_money)-sum(a.rcpt_money) as total
                    FROM acc_debtor a
                    left outer join leave_month l on l.MONTH_ID = month(a.dchdate)
                    WHERE a.dchdate between "'.$start.'" and "'.$end.'"
                    and account_code="1102050102.603"
                    and income <> 0
                    group by month(a.dchdate)
                    order by a.dchdate desc limit 6;

            ');

        } else {
            $datashow = DB::select('
                SELECT month(a.dchdate) as months,year(a.dchdate) as year,l.MONTH_NAME
                    ,count(distinct a.hn) as hn
                    ,count(distinct a.vn) as vn
                    ,sum(a.paid_money) as paid_money
                    ,sum(a.income) as income
                    ,sum(a.income)-sum(a.discount_money)-sum(a.rcpt_money) as total
                    FROM acc_debtor a
                    left outer join leave_month l on l.MONTH_ID = month(a.dchdate)
                    WHERE a.dchdate between "'.$startdate.'" and "'.$enddate.'"
                    and account_code="1102050102.603"
                    and income <>0
                    order by a.dchdate desc;
            ');
        }

        return view('account_603.account_603_dash',[
            'startdate'        => $startdate,
            'enddate'          => $enddate,
            'leave_month_year' => $leave_month_year,
            'datashow'         => $datashow,
            'newyear'          => $newyear,
            'date'             => $date,
        ]);
    }
    public function account_603_dash(Request $request)
    {
        $budget_year        = $request->budget_year;
        $acc_trimart_id = $request->acc_trimart_id;
        $dabudget_year      = DB::table('budget_year')->where('active','=',true)->get();
        $leave_month_year   = DB::table('leave_month')->orderBy('MONTH_ID', 'ASC')->get();
        $date = date('Y-m-d');
        $y = date('Y') + 543;
        $newweek = date('Y-m-d', strtotime($date . ' -1 week')); //ย้อนหลัง 1 สัปดาห์
        $newDate = date('Y-m-d', strtotime($date . ' -5 months')); //ย้อนหลัง 5 เดือน
        $newyear = date('Y-m-d', strtotime($date . ' -1 year')); //ย้อนหลัง 1 ปี
        $bgs_year      = DB::table('budget_year')->where('years_now','Y')->first();
        $data['bg_yearnow']    = $bgs_year->leave_year_id;

        if ($budget_year == '') {
            $yearnew     = date('Y');
            $year_old    = date('Y')-1;
            // $startdate   = (''.$year_old.'-10-01');
            // $enddate     = (''.$yearnew.'-09-30');
            $bg           = DB::table('budget_year')->where('years_now','Y')->first();
            $startdate    = $bg->date_begin;
            $enddate      = $bg->date_end;
            // dd($startdate);
            $datashow = DB::select('
                    SELECT month(a.dchdate) as months,year(a.dchdate) as year,l.MONTH_NAME
                    ,count(distinct a.hn) as hn ,count(distinct a.vn) as vn ,count(distinct a.an) as an
                    ,sum(a.income) as income ,sum(a.paid_money) as paid_money
                    ,sum(a.income)-sum(a.discount_money)-sum(a.rcpt_money) as total ,sum(a.debit) as debit
                    ,sum(a.income)-sum(a.discount_money)-sum(a.rcpt_money)-sum(a.fokliad) as debit402,sum(a.fokliad) as sumfokliad

                    FROM acc_debtor a
                    left outer join leave_month l on l.MONTH_ID = month(a.dchdate)
                    WHERE a.dchdate between "'.$startdate.'" and "'.$enddate.'"
                    and account_code="1102050102.603"
                    group by month(a.dchdate)
                    order by a.dchdate desc;
            ');
        } else {

            $bg           = DB::table('budget_year')->where('leave_year_id','=',$budget_year)->first();
            $startdate    = $bg->date_begin;
            $enddate      = $bg->date_end;
            // dd($startdate);
            $datashow = DB::select('
                    SELECT month(a.dchdate) as months,year(a.dchdate) as year,l.MONTH_NAME
                    ,count(distinct a.hn) as hn ,count(distinct a.vn) as vn
                    ,count(distinct a.an) as an ,sum(a.income) as income
                    ,sum(a.paid_money) as paid_money
                    ,sum(a.income)-sum(a.discount_money)-sum(a.rcpt_money) as total ,sum(a.debit) as debit
                    FROM acc_debtor a
                    left outer join leave_month l on l.MONTH_ID = month(a.dchdate)
                    WHERE a.dchdate between "'.$startdate.'" and "'.$enddate.'"
                    and account_code="1102050102.603"
                    group by month(a.dchdate)
                    order by a.dchdate desc;
            ');
        }
        // dd($startdate);
        return view('account_603.account_603_dash',$data,[
            'startdate'         =>  $startdate,
            'enddate'           =>  $enddate,
            'leave_month_year'  =>  $leave_month_year,
            'datashow'          =>  $datashow,
            'dabudget_year'     =>  $dabudget_year,
            'budget_year'       =>  $budget_year,
            'y'                 =>  $y,
        ]);

        // return view('account_304.account_304_dash',[
        //     'startdate'        => $startdate,
        //     'enddate'          => $enddate,
        //     'leave_month_year' => $leave_month_year,
        //     'data_trimart'     => $data_trimart,
        //     'newyear'          => $newyear,
        //     'date'             => $date,
        //     'trimart'          => $trimart,
        // ]);
    }

    public function account_603_pull(Request $request)
    {
        $datenow       = date('Y-m-d');
        $months        = date('m');
        $year          = date('Y');
        $newday        = date('Y-m-d', strtotime($datenow . ' -10 Day')); //ย้อนหลัง 1 สัปดาห์
        $startdate     = $request->startdate;
        $enddate       = $request->enddate;
        if ($startdate == '') {
            // $acc_debtor = Acc_debtor::where('stamp','=','N')->whereBetween('dchdate', [$datenow, $datenow])->get();
            $acc_debtor = DB::select('
                SELECT * from acc_debtor a
                WHERE a.account_code="1102050102.603" AND a.dchdate BETWEEN "' . $newday . '" AND "' . $datenow . '"
                group by a.an
                order by a.dchdate asc
            ');
            // and month(a.dchdate) = "'.$months.'" and year(a.dchdate) = "'.$year.'"
        } else {
            $acc_debtor = DB::select('
            SELECT * from acc_debtor a
            WHERE a.account_code="1102050102.603" AND a.dchdate BETWEEN "' . $startdate . '" AND "' . $enddate . '"
            group by a.an
            order by a.dchdate asc
        ');
        }

        return view('account_603.account_603_pull',[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'acc_debtor'    =>     $acc_debtor,
        ]);
    }

    public function account_603_search(Request $request)
    {
        $datenow       = date('Y-m-d');
        $months        = date('m');
        $year          = date('Y');
        $newday        = date('Y-m-d', strtotime($datenow . ' -10 Day')); //ย้อนหลัง 1 สัปดาห์
        $startdate     = $request->startdate;
        $enddate       = $request->enddate;
        if ($startdate == '') {
            // $acc_debtor = Acc_debtor::where('stamp','=','N')->whereBetween('dchdate', [$datenow, $datenow])->get();
            $datashow = DB::select('
                SELECT * from acc_debtor a
                WHERE a.account_code="1102050102.603" AND a.dchdate BETWEEN "' . $newday . '" AND "' . $datenow . '"
                group by a.an
                order by a.vstdate asc
            ');
            // and month(a.dchdate) = "'.$months.'" and year(a.dchdate) = "'.$year.'"
        } else {
            $datashow = DB::select('
            SELECT * from acc_debtor a
            WHERE a.account_code="1102050102.603" AND a.dchdate BETWEEN "' . $startdate . '" AND "' . $enddate . '"
            group by a.an
            order by a.vstdate asc
        ');
        }

        return view('account_603.account_603_search',[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'    =>     $datashow,
        ]);
    }

    public function account_603_checksit(Request $request)
    {
        $datestart = $request->datestart;
        $dateend   = $request->dateend;
        $date      = date('Y-m-d');
        $id        = $request->ids;
        // $data_sitss = DB::connection('mysql')->select('SELECT vn,an,cid,vstdate,dchdate FROM acc_debtor WHERE account_code="1102050101.401" AND stamp = "N" GROUP BY vn');
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

                            Acc_debtor::where('an', $an)
                            ->update([
                                'status'         => 'จำหน่าย/เสียชีวิต',
                                'maininscl'      => @$maininscl,
                                'pttype_spsch'   => @$subinscl,
                                'hmain'          => @$hmain,
                                'subinscl'       => @$subinscl,
                            ]);
                        }elseif(@$maininscl !="" || @$subinscl !=""){
                           Acc_debtor::where('an', $an)
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
    public function account_603_pulldata(Request $request)
    {
        $db_ = Orginfo::where('orginfo_id','=','1')->first();
        $db  = $db_->dbname;
        $datenow = date('Y-m-d');
        // dd($db);
        $startdate = $request->datepicker;
        $enddate = $request->datepicker2;
        // Acc_opitemrece::truncate();
        $acc_debtor = DB::connection('mysql2')->select(
            'SELECT ip.vn,a.an,a.hn,pt.cid,concat(pt.pname,pt.fname," ",pt.lname) ptname
                    ,a.regdate,a.dchdate as dchdate,v.vstdate
                    ,ipt.pttype,ptt.max_debt_money
                     
                    ,CASE  
                        WHEN ipt.pttype IN ("31","36","37","38","39") THEN "28"		 
                        ELSE ec.code
                    END as code
                    ,CASE  
                        WHEN ipt.pttype IN ("31","36","37","38","39") THEN "1102050102.603"		 
                        ELSE ec.ar_ipd
                    END as account_code
                    ,CASE  
                        WHEN ipt.pttype IN ("31","36","37","38","39") THEN "พรบ.รถ"		 
                        ELSE ec.name
                    END as account_name

                    ,CASE
                   
                    WHEN ipt.pttype_number ="1" AND ipt.pttype IN ("31","36","37","38","39") AND a.income-a.rcpt_money-a.discount_money > 30000 THEN ipt.max_debt_amount
                    WHEN ipt.pttype_number = "2" AND ipt.pttype IN ("31","36","37","38","39") THEN ipt.nhso_ownright_pid
                    ELSE a.income-a.rcpt_money-a.discount_money
                    END as debit

                    ,a.income,a.uc_money,a.rcpt_money,a.discount_money,a.paid_money
                    ,ipt.pttype_number ,ip.rw,ip.adjrw,ip.adjrw*8350 as total_adjrw_income

                    ,sum(if(d.name like "CT%",sum_price,0)) as CT
                    ,sum(if(op.icode ="3010058",sum_price,0)) as fokliad
                    ,sum(if(op.income="02",sum_price,0)) as debit_instument
                    ,sum(if(op.icode IN("1560016","1540073","1530005","1540048","1620015","1600012","1600015"),sum_price,0)) as debit_drug
                    ,sum(if(op.icode IN("3001412","3001417"),sum_price,0)) as debit_toa
                    ,sum(if(op.icode IN("3010829","3011068","3010864","3010861","3010862","3010863","3011069","3011012","3011070"),sum_price,0)) as debit_refer

                    from ipt ip
                    LEFT OUTER JOIN an_stat a ON ip.an = a.an
                    LEFT OUTER JOIN patient pt on pt.hn=a.hn
                    LEFT OUTER JOIN pttype ptt on a.pttype=ptt.pttype
                    LEFT OUTER JOIN pttype_eclaim ec on ec.code=ptt.pttype_eclaim_id
                    LEFT OUTER JOIN ipt_pttype ipt ON ipt.an = a.an
                    LEFT OUTER JOIN opitemrece op ON ip.an = op.an
                    LEFT OUTER JOIN s_drugitems d on d.icode = op.icode
                    LEFT OUTER JOIN vn_stat v on v.vn = a.vn
                    WHERE a.dchdate BETWEEN "' . $startdate . '" AND "' . $enddate . '"
                    AND ipt.pttype IN(SELECT pttype FROM pkbackoffice.acc_setpang_type WHERE pang ="1102050102.603" AND opdipd ="IPD")
                    AND a.income-a.rcpt_money-a.discount_money <> 0
                    GROUP BY a.an
        ');
        // WHEN a.income-a.rcpt_money-a.discount_money < 30000 THEN a.income-a.rcpt_money-a.discount_money
        // ,ec.code,ec.ar_ipd as account_code
        // ,ec.name as account_name
        //   AND d.name NOT like "CT%"
        $bgs_year      = DB::table('budget_year')->where('years_now','Y')->first();
        $bg_yearnow    = $bgs_year->leave_year_id;
        foreach ($acc_debtor as $key => $value) {
            // $check = Acc_debtor::where('an', $value->an)->where('account_code','1102050102.603')->whereBetween('dchdate', [$startdate, $enddate])->count();
            $check = Acc_debtor::where('an', $value->an)->where('account_code','1102050102.603')->count();
                //    dd($check);
                    if ($check > 0) {
                
                            Acc_debtor::where('an', $value->an)->where('account_code','1102050102.603')->update([ 
                                'pttype'          => $value->pttype,
                                'debit'           => $value->debit,
                                'debit_total'     => $value->debit,
                                'bg_yearnow'      => $bg_yearnow,
                            ]);
                     
                    } else {
                        if ($value->debit > 0) {
                            Acc_debtor::insert([
                                'bg_yearnow'         => $bg_yearnow,
                                'hn'                 => $value->hn,
                                'an'                 => $value->an,
                                'vn'                 => $value->vn,
                                'cid'                => $value->cid,
                                'ptname'             => $value->ptname,
                                'pttype'             => $value->pttype,
                                'vstdate'            => $value->vstdate,
                                'rxdate'             => $value->regdate,
                                'dchdate'            => $value->dchdate,
                                'acc_code'           => $value->code,
                                'account_code'       => $value->account_code,
                                'account_name'       => $value->account_name,
                                // 'income_group'       => $value->income_group,
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
                                'debit_total'        => $value->debit,
                                'max_debt_amount'    => $value->max_debt_money,
                                'acc_debtor_userid'  => Auth::user()->id
                            ]);
                        } else {
                            # code...
                        }
                        
                           
                       
                    }

        }

        return response()->json([
            'status'    => '200'
        ]);
    }
    public function account_603_stam(Request $request)
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
                $check = Acc_1102050102_603::where('an', $value->an)->count();
                if ($check > 0) {
                # code...
                } else {
                    Acc_1102050102_603::insert([
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
                        'income_group'      => $value->income_group,
                        'uc_money'          => $value->uc_money,
                        'discount_money'    => $value->discount_money,
                        'rcpt_money'        => $value->rcpt_money,
                        'debit'             => $value->debit,
                        'debit_drug'        => $value->debit_drug,
                        'debit_instument'   => $value->debit_instument,
                        'debit_refer'       => $value->debit_refer,
                        'debit_toa'         => $value->debit_toa,
                        'debit_total'       => $value->debit_total,
                        'max_debt_amount'   => $value->max_debt_amount,
                        'acc_debtor_userid' => $iduser
                    ]);
                }

        }
        return response()->json([
            'status'    => '200'
        ]);
    }
    public function account_603_detail(Request $request,$months,$year)
    {
        $datenow = date('Y-m-d');
        $data['users'] = User::get();
        $data = DB::select('
            SELECT U1.acc_1102050102_603_id,U1.an,U1.vn,U1.hn,U1.cid,U1.ptname,U1.vstdate,U1.pttype,U1.debit_total,U1.nhso_docno,U1.dchdate,U1.nhso_ownright_pid,U1.nhso_ownright_name,U1.nhso_govname
            ,U1.recieve_true,U1.difference,U1.recieve_no,U1.recieve_date
                from acc_1102050102_603 U1
                WHERE month(U1.dchdate) = "'.$months.'" AND year(U1.dchdate) = "'.$year.'"
                GROUP BY U1.an
        ');


        return view('account_603.account_603_detail', $data, [
            'data'          =>     $data,
            'months'        =>     $months,
            'year'          =>     $year
        ]);
    }

    public function account_603_syncall(Request $request)
    {
        $months   = $request->months;
        $year     = $request->year;
        // Acc_temp603::truncate();
        // $syncdata = DB::connection('mysql')->select(
        //     'SELECT vn,an,vstdate,dchdate
        //     FROM acc_1102050102_603
        //         WHERE month(dchdate) = "'.$months.'"
        //         AND year(dchdate) = "'.$year.'"
        //         GROUP BY an
        //     ');
        //     foreach ($syncdata as $key => $val) {
        //         Acc_temp603::insert([
        //             'vn'           => $val->vn ,
        //             'an'           => $val->an,
        //             'vstdate'      => $val->vstdate,
        //             'dchdate'      => $val->dchdate
        //         ]);
        //     }

        // $sync = DB::connection('mysql')->select(
        //     'SELECT ac.acc_1102050102_603_id,a.an,a.pttype,ip.nhso_ownright_pid,ip.nhso_docno,ip.nhso_ownright_name,ac.dchdate,ip.nhso_govname
        //         FROM hos.an_stat a
        //         LEFT JOIN hos.ipt_pttype ip ON ip.an = a.an
        //         LEFT JOIN pkbackoffice.acc_1102050102_603 ac ON ac.an = a.an
        //         WHERE month(a.dchdate) = "'.$months.'"
        //         AND year(a.dchdate) = "'.$year.'"
        //         AND ip.nhso_ownright_pid IS NOT NULL AND ip.nhso_docno IS NOT NULL AND ac.acc_1102050102_603_id IS NOT NULL
        //         GROUP BY a.an
        // ');

        // foreach ($sync as $key => $value) {
        //     if ($value->nhso_ownright_pid =='' ) {
        //         Acc_1102050102_603::where('an',$value->an)
        //             ->update([
        //                 'nhso_docno'           => $value->nhso_docno ,
        //                 'nhso_ownright_pid'    => '0.00',
        //                 'nhso_ownright_name'   => $value->nhso_ownright_name,
        //                 'nhso_govname'         => $value->nhso_govname
        //         ]);
        //     } else {
        //         Acc_1102050102_603::where('an',$value->an)
        //             ->update([
        //                 'nhso_docno'           => $value->nhso_docno ,
        //                 'nhso_ownright_pid'    => $value->nhso_ownright_pid,
        //                 'nhso_ownright_name'   => $value->nhso_ownright_name,
        //                 'nhso_govname'         => $value->nhso_govname
        //         ]);
        //     }


        // }

        $id = $request->ids;
        $iduser = Auth::user()->id;
        $sync = Acc_1102050102_603::whereIn('acc_1102050102_603_id',explode(",",$id))->get();

        foreach ($sync as $key => $value) {
                //  $sync = DB::connection('mysql')->select(
                //     'SELECT ac.acc_1102050102_603_id,a.an,a.pttype,ip.nhso_ownright_pid,ip.nhso_docno,ip.nhso_ownright_name,ac.dchdate,ip.nhso_govname
                //         FROM hos.an_stat a
                //         LEFT JOIN hos.ipt_pttype ip ON ip.an = a.an
                //         LEFT JOIN pkbackoffice.acc_1102050102_603 ac ON ac.an = a.an
                //         WHERE month(a.dchdate) = "'.$months.'"
                //         AND year(a.dchdate) = "'.$year.'"
                //         AND ip.nhso_ownright_pid IS NOT NULL AND ip.nhso_docno IS NOT NULL AND ac.acc_1102050102_603_id IS NOT NULL
                //         GROUP BY a.an
                // ');
                    $sync_data = DB::connection('mysql2')->select('
                            SELECT a.an,a.pttype,ip.nhso_ownright_pid,ip.nhso_docno,ip.nhso_ownright_name,a.dchdate,ip.nhso_govname
                            from an_stat a
                            LEFT JOIN ipt_pttype ip ON ip.an = a.an
                            WHERE a.an = "'.$value->an.'"

                            GROUP BY a.an
                    ');
                    // AND ip.nhso_docno  IS NOT NULL
                    foreach ($sync_data as $key => $value2) {
                        if ($value2->nhso_ownright_pid =='' ) {
                            Acc_1102050102_603::where('an',$value2->an)
                                ->update([
                                    'nhso_docno'           => $value2->nhso_docno ,
                                    'nhso_ownright_pid'    => '0.00',
                                    'nhso_ownright_name'   => $value2->nhso_ownright_name,
                                    'nhso_govname'         => $value2->nhso_govname
                            ]);
                        } else {
                            Acc_1102050102_603::where('an',$value2->an)
                                ->update([
                                    'nhso_docno'           => $value2->nhso_docno ,
                                    'nhso_ownright_pid'    => $value2->nhso_ownright_pid,
                                    'nhso_ownright_name'   => $value2->nhso_ownright_name,
                                    'nhso_govname'         => $value2->nhso_govname
                            ]);
                        }
                    }


        }

        return response()->json([
            'status'    => '200'
        ]);


    }
    public function account_603_stm(Request $request,$months,$year)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        // dd($id);
        $data['users'] = User::get();

        $datashow = DB::select('
                SELECT U1.an,U1.vn,U1.hn,U1.cid,U1.ptname,U1.vstdate,U1.pttype,U1.debit_total,U1.nhso_docno,U1.dchdate,U1.nhso_ownright_pid
                ,U1.recieve_true,U1.difference,U1.recieve_no,U1.recieve_date
                    from acc_1102050102_603 U1

                    WHERE month(U1.dchdate) = "'.$months.'"
                    and year(U1.dchdate) = "'.$year.'"
                    AND U1.recieve_true IS NOT NULL
                    GROUP BY U1.an
        ');
        // SELECT U1.an,U1.vn,U1.hn,U1.cid,U1.ptname,U1.vstdate,U1.pttype,U1.debit_total,U1.nhso_docno,U1.dchdate,U1.nhso_ownright_pid
        //         from acc_1102050102_603 U1

        //         WHERE month(U1.dchdate) = "'.$months.'" AND year(U1.dchdate) = "'.$year.'"
        //         GROUP BY U1.an
        return view('account_603.account_603_stm', $data, [
            'startdate'         =>     $startdate,
            'enddate'           =>     $enddate,
            'datashow'          =>     $datashow,
            'months'            =>     $months,
            'year'              =>     $year,

        ]);
    }

    public function account_603_stmnull(Request $request,$months,$year)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        // dd($id);
        $data['users'] = User::get();

        $datashow = DB::select('
                SELECT U1.an,U1.vn,U1.hn,U1.cid,U1.ptname,U1.vstdate,U1.pttype,U1.debit_total,U1.nhso_docno,U1.dchdate,U1.nhso_ownright_pid
                ,U1.recieve_true,U1.difference,U1.recieve_no,U1.recieve_date
                    from acc_1102050102_603 U1

                    WHERE month(U1.dchdate) = "'.$months.'"
                    and year(U1.dchdate) = "'.$year.'"
                    AND U1.recieve_true IS NULL
                    GROUP BY U1.an
        ');

        return view('account_603.account_603_stmnull', $data, [
            'startdate'         =>     $startdate,
            'enddate'           =>     $enddate,
            'datashow'          =>     $datashow,
            'months'            =>     $months,
            'year'              =>     $year,

        ]);
    }

    public function account_603_edit(Request $request, $id)
    {
        $acc603 = Acc_1102050102_603::find($id);

        return response()->json([
            'status'      => '200',
            'acc603'      =>  $acc603,
        ]);
    }
    public function account_603_update(Request $request)
    {
        $iduser = Auth::user()->id;
        $id = $request->acc_1102050102_603_id;
        $sauntang_ = Acc_1102050102_603::where('acc_1102050102_603_id','=',$id)->first();
        $sauntang  = $sauntang_->debit_total;
        Acc_1102050102_603::whereIn('acc_1102050102_603_id',explode(",",$id))
        ->update([
            'status'           => 'Y',
            'recieve_true'     => $request->payprice,
            'recieve_no'       => $request->money_billno,
            'recieve_date'     => $request->paydate,
            'savedate'         => $request->savedate,
            'difference'       => $sauntang - $request->payprice,
            'comment'          => $request->comment,
            'recieve_user'     => $iduser

        ]);

        $check = Acc_stm_prb::where('acc_1102050102_603_sid',$id)->count();
        if ($check > 0) {
            # code...
        } else {
            $add = new Acc_stm_prb();
            $add->acc_1102050102_603_sid = $id;
            $add->req_no           = $request->req_no;
            $add->pid              = $request->cid;
            $add->fullname           = $request->ptname;
            $add->claim_no         = $request->claim_no;
            $add->vendor           = $request->vendor;
            $add->money_billno     = $request->money_billno;
            $add->paytype          = $request->paytype;
            $add->no               = $request->no;
            $add->payprice         = $request->payprice;
            $add->paydate          = $request->paydate;
            $add->savedate         = $request->savedate;
            $add->save();
        }


        return response()->json([
            'status'      => '200'
        ]);
    }
    public function account_603_detail_date(Request $request,$startdate,$enddate)
    {
        $datenow = date('Y-m-d');

        $data['users'] = User::get();

        $data = DB::select('
            SELECT U1.acc_1102050102_603_id,U1.an,U1.vn,U1.hn,U1.cid,U1.ptname,U1.vstdate,U1.pttype,U1.debit_total,U1.nhso_docno,U1.dchdate,U1.nhso_ownright_pid,U1.nhso_ownright_name
            ,U1.recieve_true,U1.difference,U1.recieve_no,U1.recieve_date ,U1.nhso_govname
                from acc_1102050102_603 U1

                WHERE U1.dchdate BETWEEN "'.$startdate.'" AND "'.$enddate.'"
                GROUP BY U1.an
        ');

        return view('account_603.account_603_detail_date', $data, [
            'data'             =>     $data,
            'startdate'        =>     $startdate,
            'enddate'          =>     $enddate
        ]);
    }
    public function account_603_syncall_date(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $sync = DB::connection('mysql')->select('
                SELECT ac.acc_1102050102_603_id,a.an,a.pttype,ip.nhso_ownright_pid,ip.nhso_docno,ac.dchdate ,ip.nhso_ownright_name
                FROM hos.an_stat a
                LEFT JOIN hos.ipt_pttype ip ON ip.an = a.an
                LEFT JOIN pkbackoffice.acc_1102050102_603 ac ON ac.an = a.an
                WHERE a.dchdate BETWEEN "'.$startdate.'"
                AND "'.$enddate.'"
                AND ip.nhso_ownright_pid  <> "" AND ip.nhso_docno  <> "" AND ac.acc_1102050102_603_id <> ""
                GROUP BY a.an

            ');
            foreach ($sync as $key => $value) {

                    Acc_1102050102_603::where('an',$value->an)
                        ->update([
                            'nhso_docno'           => $value->nhso_docno ,
                            'nhso_ownright_pid'    => $value->nhso_ownright_pid,
                            'nhso_ownright_name'   => $value->nhso_ownright_name
                    ]);
            }
            return response()->json([
                'status'    => '200'
            ]);


    }
    public function account_603_stm_date(Request $request,$startdate,$enddate)
    {
        $datenow = date('Y-m-d');
        $data['users'] = User::get();

        $datashow = DB::select('
                SELECT U1.an,U1.vn,U1.hn,U1.cid,U1.ptname,U1.vstdate,U1.pttype,U1.debit_total,U1.nhso_docno,U1.dchdate,U1.nhso_ownright_pid
                ,U1.recieve_true,U1.difference,U1.recieve_no,U1.recieve_date
                    from acc_1102050102_603 U1

                    WHERE U1.dchdate BETWEEN "'.$startdate.'"
                    and  "'.$enddate.'"
                    AND U1.recieve_true IS NOT NULL
                    GROUP BY U1.an
        ');

        return view('account_603.account_603_stm_date', $data, [
            'startdate'         =>     $startdate,
            'enddate'           =>     $enddate,
            'datashow'          =>     $datashow,

        ]);
    }
    public function account_603_stmnull_date(Request $request,$startdate,$enddate)
    {
        $datenow = date('Y-m-d');

        $data['users'] = User::get();

        $datashow = DB::select('
                SELECT U1.an,U1.vn,U1.hn,U1.cid,U1.ptname,U1.vstdate,U1.pttype,U1.debit_total,U1.nhso_docno,U1.dchdate,U1.nhso_ownright_pid
                ,U1.recieve_true,U1.difference,U1.recieve_no,U1.recieve_date
                    from acc_1102050102_603 U1

                    WHERE U1.dchdate BETWEEN "'.$startdate.'"
                    and "'.$enddate.'"
                    AND U1.recieve_true IS NULL
                    GROUP BY U1.an
        ');

        return view('account_603.account_603_stmnull_date', $data, [
            'startdate'         =>     $startdate,
            'enddate'           =>     $enddate,
            'datashow'          =>     $datashow,
        ]);
    }

    public function account_603_destroy(Request $request)
    {
        $id = $request->ids;
        // $data = Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))->get();
        Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))->delete();

        return response()->json([
            'status'    => '200'
        ]);
    }
    // public function account_602_edit(Request $request, $id)
    // {
    //     $acc602 = Acc_1102050102_602::LEFTJOIN('acc_stm_prb','acc_stm_prb.acc_1102050102_602_sid','=','acc_1102050102_602.acc_1102050102_602_id')
    //     ->find($id);

    //     return response()->json([
    //         'status'      => '200',
    //         'acc602'      =>  $acc602,
    //     ]);
    // }
    // public function account_602_update(Request $request)
    // {
    //     $id = $request->acc_1102050102_602_id;

    //     Acc_1102050102_602::whereIn('acc_1102050102_602_id',explode(",",$id))
    //     ->update([
    //         'status' => 'Y'
    //     ]);

    //     Acc_stm_prb::whereIn('acc_1102050102_602_sid',explode(",",$id))->delete();

    //     $add = new Acc_stm_prb();
    //     $add->acc_1102050102_602_sid = $id;
    //     $add->req_no           = $request->req_no;
    //     $add->pid              = $request->cid;
    //     $add->fullname           = $request->ptname;
    //     $add->claim_no         = $request->claim_no;
    //     $add->vendor           = $request->vendor;
    //     $add->money_billno     = $request->money_billno;
    //     $add->paytype          = $request->paytype;
    //     $add->no               = $request->no;
    //     $add->payprice         = $request->payprice;
    //     $add->paydate          = $request->paydate;
    //     $add->savedate         = $request->savedate;
    //     $add->save();
    //     return response()->json([
    //         'status'      => '200'
    //     ]);
    // }
    // public function account_602_stmnull(Request $request,$months,$year)
    // {
    //     $datenow = date('Y-m-d');
    //     $startdate = $request->startdate;
    //     $enddate = $request->enddate;
    //     // dd($id);
    //     $data['users'] = User::get();

    //     $datashow = DB::connection('mysql')->select('
    //             SELECT U2.req_no,U1.vn,U1.hn,U1.cid,U1.ptname,U1.vstdate,U1.pttype,U1.debit_total,U2.money_billno,U2.payprice
    //             from acc_1102050102_602 U1
    //             LEFT JOIN acc_stm_prb U2 ON U2.acc_1102050102_602_sid = U1.acc_1102050102_602_id
    //             WHERE month(U1.vstdate) = "'.$months.'" and year(U1.vstdate) = "'.$year.'"
    //             AND U1.status ="N"
    //         ');


    //     return view('account_602.account_602_stmnull', $data, [
    //         'startdate'         =>     $startdate,
    //         'enddate'           =>     $enddate,
    //         'datashow'          =>     $datashow,
    //         'months'            =>     $months,
    //         'year'              =>     $year,
    //     ]);
    // }
    // public function account_602_stmnull_all(Request $request,$months,$year)
    // {
    //     $datenow = date('Y-m-d');
    //     $startdate = $request->startdate;
    //     $enddate = $request->enddate;
    //     // dd($id);
    //     $data['users'] = User::get();

    //     $datashow = DB::connection('mysql')->select('
    //             SELECT U2.req_no,U1.vn,U1.hn,U1.cid,U1.ptname,U1.vstdate,U1.pttype,U1.debit_total,U2.money_billno,U2.payprice
    //             from acc_1102050102_602 U1
    //             LEFT JOIN acc_stm_prb U2 ON U2.acc_1102050102_602_sid = U1.acc_1102050102_602_id
    //             WHERE U1.status ="N"
    //             AND U2.acc_1102050102_602_sid IS NULL

    //         ');

    //         // AND month(U1.vstdate) = "'.$months.'" and year(U1.vstdate) = "'.$year.'"
    //     return view('account_602.account_602_stmnull_all', $data, [
    //         'startdate'         =>     $startdate,
    //         'enddate'           =>     $enddate,
    //         'datashow'          =>     $datashow,
    //         'months'            =>     $months,
    //         'year'              =>     $year,
    //     ]);
    // }


    // public function account_603_dash(Request $request)
    // {
    //     $startdate = $request->startdate;
    //     $enddate = $request->enddate;
    //     $dabudget_year = DB::table('budget_year')->where('active','=',true)->first();
    //     $leave_month_year = DB::table('leave_month')->orderBy('MONTH_ID', 'ASC')->get();
    //     $date = date('Y-m-d');
    //     $y = date('Y') + 543;
    //     $newweek = date('Y-m-d', strtotime($date . ' -1 week')); //ย้อนหลัง 1 สัปดาห์
    //     $newDate = date('Y-m-d', strtotime($date . ' -5 months')); //ย้อนหลัง 5 เดือน
    //     $newyear = date('Y-m-d', strtotime($date . ' -1 year')); //ย้อนหลัง 1 ปี

    //     if ($startdate == '') {
    //         $datashow = DB::select('
    //             SELECT month(a.dchdate) as months,year(a.dchdate) as year,l.MONTH_NAME
    //                 ,count(distinct a.hn) as hn
    //                 ,count(distinct a.vn) as vn
    //                 ,sum(a.paid_money) as paid_money
    //                 ,sum(a.income) as income
    //                 ,sum(a.income)-sum(a.discount_money)-sum(a.rcpt_money) as total
    //                 FROM acc_debtor a
    //                 left outer join leave_month l on l.MONTH_ID = month(a.dchdate)
    //                 WHERE a.dchdate between "'.$newyear.'" and "'.$date.'"
    //                 and account_code="1102050102.603"
    //                 and income <> 0
    //                 group by month(a.dchdate) order by month(a.dchdate) desc limit 3;
    //         ');

    //     } else {
    //         $datashow = DB::select('
    //             SELECT month(a.dchdate) as months,year(a.dchdate) as year,l.MONTH_NAME
    //                 ,count(distinct a.hn) as hn
    //                 ,count(distinct a.vn) as vn
    //                 ,sum(a.paid_money) as paid_money
    //                 ,sum(a.income) as income
    //                 ,sum(a.income)-sum(a.discount_money)-sum(a.rcpt_money) as total
    //                 FROM acc_debtor a
    //                 left outer join leave_month l on l.MONTH_ID = month(a.dchdate)
    //                 WHERE a.dchdate between "'.$startdate.'" and "'.$enddate.'"
    //                 and account_code="1102050102.603"
    //                 and income <>0
    //                 group by month(a.dchdate) order by month(a.dchdate) desc;
    //         ');
    //     }

    //     return view('account_pk.account_603_dash',[
    //         'startdate'        => $startdate,
    //         'enddate'          => $enddate,
    //         'leave_month_year' => $leave_month_year,
    //         'datashow'         => $datashow,
    //         'newyear'          => $newyear,
    //         'date'             => $date,
    //     ]);
    // }
    // public function account_603_pull(Request $request)
    // {
    //     $datenow = date('Y-m-d');
    //     $months = date('m');
    //     $year = date('Y');
    //     // dd($year);
    //     $startdate = $request->startdate;
    //     $enddate = $request->enddate;
    //     if ($startdate == '') {
    //         // $acc_debtor = Acc_debtor::where('stamp','=','N')->whereBetween('dchdate', [$datenow, $datenow])->get();
    //         $acc_debtor = DB::select('
    //             SELECT * from acc_debtor
    //             WHERE account_code="1102050102.603"
    //             AND stamp = "N"
    //             group by an
    //             order by dchdate asc;

    //         ');
    //         // and month(a.dchdate) = "'.$months.'" and year(a.dchdate) = "'.$year.'"
    //     } else {
    //         // $acc_debtor = Acc_debtor::where('stamp','=','N')->whereBetween('dchdate', [$startdate, $enddate])->get();
    //     }

    //     return view('account_pk.account_603_pull',[
    //         'startdate'     =>     $startdate,
    //         'enddate'       =>     $enddate,
    //         'acc_debtor'    =>     $acc_debtor,
    //     ]);
    // }
    // public function account_603_pulldata(Request $request)
    //  {
    //      $datenow = date('Y-m-d');
    //      $startdate = $request->datepicker;
    //      $enddate = $request->datepicker2;
    //      // Acc_opitemrece::truncate();
    //      $acc_debtor = DB::connection('mysql3')->select('
    //             select
    //             o.vn,a.hn,i.an,showcid(p.cid) as cid,i.pttype,o.vstdate,a.dchdate,i.pttype_number,i.max_debt_amount,pt.name as pttype_name
    //             ,concat(p.pname,p.fname," ",p.lname) as ptname,a.income ,format(a.paid_money,2) as paid_money,e.code as acc_code
    //             ,e.ar_ipd as account_code,e.name as account_name
    //             ,a.income,a.uc_money,a.discount_money,a.paid_money,a.rcpt_money
    //             ,a.rcpno_list as rcpno

    //             ,ifnull(case
    //             when i.max_debt_amount ="" then a.income
    //             else i.max_debt_amount end,a.income) debit

    //             from ipt_pttype i
    //             left outer join pttype pt on pt.pttype = i.pttype
    //             left outer join pttype_eclaim e on e.code=pt.pttype_eclaim_id
    //             left outer join an_stat a on a.an = i.an
    //             LEFT OUTER JOIN patient p ON p.hn=a.hn
    //             LEFT JOIN ovst o on o.an = i.an

    //          WHERE a.dchdate BETWEEN "' . $startdate . '" AND "' . $enddate . '"
    //          and e.ar_ipd = "1102050102.603"
    //         GROUP BY i.an
    //         order by i.pttype_number
    //      ');

    //      foreach ($acc_debtor as $key => $value) {
    //                  $check = Acc_debtor::where('an', $value->an)->where('account_code','1102050102.603')->whereBetween('dchdate', [$startdate, $enddate])->count();
    //                  if ($check == 0) {
    //                      Acc_debtor::insert([
    //                          'hn'                 => $value->hn,
    //                          'an'                 => $value->an,
    //                          'vn'                 => $value->vn,
    //                          'cid'                => $value->cid,
    //                          'ptname'             => $value->ptname,
    //                          'pttype'             => $value->pttype,
    //                          'vstdate'            => $value->vstdate,
    //                          'dchdate'            => $value->dchdate,
    //                          'acc_code'           => $value->acc_code,
    //                          'account_code'       => $value->account_code,
    //                          'account_name'       => $value->account_name,
    //                         //  'income_group'       => $value->income_group,
    //                          'income'             => $value->income,
    //                          'uc_money'           => $value->uc_money,
    //                          'discount_money'     => $value->discount_money,
    //                          'paid_money'         => $value->paid_money,
    //                          'rcpt_money'         => $value->rcpt_money,
    //                          'debit'              => $value->debit,
    //                         //  'debit_drug'         => $value->debit_drug,
    //                         //  'debit_instument'    => $value->debit_instument,
    //                         //  'debit_toa'          => $value->debit_toa,
    //                         //  'debit_refer'        => $value->debit_refer,
    //                          'debit_total'        => $value->debit,
    //                          'max_debt_amount'    => $value->max_debt_amount,
    //                          'acc_debtor_userid'  => Auth::user()->id
    //                      ]);
    //                  }

    //      }

    //          return response()->json([

    //              'status'    => '200'
    //          ]);
    //  }
    //  public function account_603_stam(Request $request)
    //  {
    //      $id = $request->ids;
    //      $iduser = Auth::user()->id;
    //      $data = Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))->get();
    //          Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))
    //                  ->update([
    //                      'stamp' => 'Y'
    //                  ]);
    //      foreach ($data as $key => $value) {
    //              $date = date('Y-m-d H:m:s');
    //          //  $check = Acc_debtor::where('vn', $value->vn)->where('account_code','1102050101.4011')->where('account_code','1102050101.4011')->count();
    //              $check = Acc_debtor::where('an', $value->an)->where('debit_total','=','0')->count();
    //              if ($check > 0) {
    //              # code...
    //              } else {
    //                 Acc_1102050102_603::insert([
    //                          'vn'                => $value->vn,
    //                          'hn'                => $value->hn,
    //                          'an'                => $value->an,
    //                          'cid'               => $value->cid,
    //                          'ptname'            => $value->ptname,
    //                          'vstdate'           => $value->vstdate,
    //                          'regdate'           => $value->regdate,
    //                          'dchdate'           => $value->dchdate,
    //                          'pttype'            => $value->pttype,
    //                          'pttype_nhso'       => $value->pttype_spsch,
    //                          'acc_code'          => $value->acc_code,
    //                          'account_code'      => $value->account_code,
    //                          'income'            => $value->income,
    //                          'income_group'      => $value->income_group,
    //                          'uc_money'          => $value->uc_money,
    //                          'discount_money'    => $value->discount_money,
    //                          'rcpt_money'        => $value->rcpt_money,
    //                          'debit'             => $value->debit,
    //                          'debit_drug'        => $value->debit_drug,
    //                          'debit_instument'   => $value->debit_instument,
    //                          'debit_refer'       => $value->debit_refer,
    //                          'debit_toa'         => $value->debit_toa,
    //                          'debit_total'       => $value->debit,
    //                          'max_debt_amount'   => $value->max_debt_amount,
    //                          'acc_debtor_userid' => $iduser
    //                  ]);
    //              }

    //      }
    //      return response()->json([
    //          'status'    => '200'
    //      ]);
    //  }




 }
