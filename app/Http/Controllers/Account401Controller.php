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
use App\Models\Acc_stm_ti_totalhead;
use App\Models\Acc_stm_ti_excel;
use App\Models\Acc_stm_ofc;
use App\Models\acc_stm_ofcexcel;
use App\Models\Acc_stm_lgo;
use App\Models\Acc_stm_lgoexcel;
use App\Models\Check_sit_auto;
use App\Models\Acc_stm_ucs_excel;
use App\Models\D_ins;
use App\Models\D_pat;
use App\Models\D_opd;
use App\Models\D_orf;
use App\Models\D_odx;
use App\Models\D_cht;
use App\Models\D_cha;
use App\Models\D_oop;
use App\Models\D_adp;
use App\Models\D_dru;
use App\Models\D_idx;
use App\Models\D_iop;
use App\Models\D_ipd;
use App\Models\D_aer;
use App\Models\D_irf;

use App\Models\Dapi_ins;
use App\Models\Dapi_pat;
use App\Models\Dapi_opd;
use App\Models\Dapi_orf;
use App\Models\Dapi_odx;
use App\Models\Dapi_cht;
use App\Models\Dapi_cha;
use App\Models\Dapi_oop;
use App\Models\Dapi_adp;
use App\Models\Dapi_dru;
use App\Models\Dapi_idx;
use App\Models\Dapi_iop;
use App\Models\Dapi_ipd;
use App\Models\Dapi_aer;
use App\Models\Dapi_irf;
use App\Models\Dapi_lvd;

use App\Models\Acc_function;

use App\Models\D_apiofc_ins;
use App\Models\D_apiofc_iop;
use App\Models\D_apiofc_adp;
use App\Models\D_apiofc_aer;
use App\Models\D_apiofc_cha;
use App\Models\D_apiofc_cht;
use App\Models\D_apiofc_dru;
use App\Models\D_apiofc_idx;
use App\Models\D_apiofc_pat;
use App\Models\D_apiofc_ipd;
use App\Models\D_apiofc_irf;
use App\Models\D_apiofc_ldv;
use App\Models\D_apiofc_odx;
use App\Models\D_apiofc_oop;
use App\Models\D_apiofc_opd;
use App\Models\D_apiofc_orf;

use App\Models\Fdh_sesion;
use App\Models\Fdh_ins;
use App\Models\Fdh_pat;
use App\Models\Fdh_opd;
use App\Models\Fdh_orf;
use App\Models\Fdh_odx;
use App\Models\Fdh_cht;
use App\Models\Fdh_cha;
use App\Models\Fdh_oop;
use App\Models\Fdh_adp;
use App\Models\Fdh_dru;
use App\Models\Fdh_idx;
use App\Models\Fdh_iop;
use App\Models\Fdh_ipd;
use App\Models\Fdh_aer;
use App\Models\Fdh_irf;
use App\Models\Acc_ofc_dateconfig;
use App\Models\Acc_1102050101_401send;
use App\Models\Acc_debtor_log;
use App\Models\Acc_account_total;

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
use CURLFILE;
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
use App\Models\D_ofc_repexcel;
use App\Models\D_ofc_rep;
use ZipArchive;
use Illuminate\Support\Facades\Redirect;
use PhpParser\Node\Stmt\If_;
use Stevebauman\Location\Facades\Location;
use Illuminate\Filesystem\Filesystem;


date_default_timezone_set("Asia/Bangkok");


class Account401Controller extends Controller
 {

    // public function account_401_dash(Request $request)
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
    //     $yearnew = date('Y')+1;
    //     $yearold = date('Y')-1;
    //     $start = (''.$yearold.'-10-01');
    //     $end = (''.$yearnew.'-09-30');
    //     // dd($start);
    //     if ($startdate == '') {
    //         $datashow = DB::select('
    //             SELECT month(a.vstdate) as months,year(a.vstdate) as year,l.MONTH_NAME
    //                 ,count(distinct a.hn) as hn
    //                 ,count(distinct a.vn) as vn
    //                 ,sum(a.paid_money) as paid_money
    //                 ,sum(a.income) as income
    //                 ,sum(a.income)-sum(a.discount_money)-sum(a.rcpt_money) as total
    //                 FROM acc_debtor a
    //                 left outer join leave_month l on l.MONTH_ID = month(a.vstdate)
    //                 WHERE a.vstdate between "'.$start.'" and "'.$end.'"
    //                 and account_code="1102050101.401"
    //                 and income <> 0
    //                 group by month(a.vstdate) order by a.vstdate desc limit 2;
    //         ');
    //     } else {
    //         $datashow = DB::select('
    //             SELECT month(a.vstdate) as months,year(a.vstdate) as year,l.MONTH_NAME
    //                 ,count(distinct a.hn) as hn
    //                 ,count(distinct a.vn) as vn
    //                 ,sum(a.paid_money) as paid_money
    //                 ,sum(a.income) as income
    //                 ,sum(a.income)-sum(a.discount_money)-sum(a.rcpt_money) as total
    //                 FROM acc_debtor a
    //                 left outer join leave_month l on l.MONTH_ID = month(a.vstdate)
    //                 WHERE a.vstdate between "'.$startdate.'" and "'.$enddate.'"
    //                 and account_code="1102050101.401"
    //                 and income <>0

    //         ');
    //     }

    //     return view('account_401.account_401_dash',[
    //         'startdate'        => $startdate,
    //         'enddate'          => $enddate,
    //         'leave_month_year' => $leave_month_year,
    //         'datashow'         => $datashow,
    //         'newyear'          => $newyear,
    //         'date'             => $date,
    //     ]);
    // }
    public function account_401_dash_old(Request $request)
    {
           $budget_year   = $request->budget_year;
           $datenow       = date("Y-m-d");
           $y             = date('Y') + 543;
           $dabudget_year = DB::table('budget_year')->where('active','=',true)->get();
           $leave_month_year = DB::table('leave_month')->orderBy('MONTH_ID', 'ASC')->get();
           $date = date('Y-m-d');
           $newweek = date('Y-m-d', strtotime($date . ' -1 week')); //ย้อนหลัง 1 สัปดาห์
           $newDate = date('Y-m-d', strtotime($date . ' -5 months')); //ย้อนหลัง 5 เดือน
           $newyear = date('Y-m-d', strtotime($date . ' -1 year')); //ย้อนหลัง 1 ปี

           $months_now = date('m');
           $year_now = date('Y');
           if ($budget_year == '') {
               $yearnew = date('Y');
               $year_old = date('Y')-1;
               $months_old  = ('10');
               $startdate = (''.$year_old.'-10-01');
               $enddate = (''.$yearnew.'-09-30');
               $datashow = DB::select('
                       SELECT MONTH(a.vstdate) as months,YEAR(a.vstdate) as years
                       ,count(DISTINCT a.vn) as total_an,l.MONTH_NAME
                       ,sum(a.debit_total) as tung_looknee
                       FROM acc_1102050101_401 a
                       LEFT OUTER JOIN leave_month l on l.MONTH_ID = month(a.vstdate)
                       WHERE a.vstdate BETWEEN "'.$startdate.'" AND "'.$enddate.'"
                       AND a.account_code ="1102050101.401"
                       GROUP BY months ORDER BY a.vstdate DESC
               ');
           } else {
               $bg           = DB::table('budget_year')->where('leave_year_id','=',$budget_year)->first();
               $startdate    = $bg->date_begin;
               $enddate      = $bg->date_end;
               $datashow = DB::select('
                       SELECT MONTH(a.vstdate) as months,YEAR(a.vstdate) as years
                       ,count(DISTINCT a.vn) as total_an,l.MONTH_NAME
                       ,sum(a.debit_total) as tung_looknee
                       FROM acc_1102050101_401 a
                       LEFT OUTER JOIN leave_month l on l.MONTH_ID = month(a.vstdate)
                       WHERE a.vstdate BETWEEN "'.$startdate.'" AND "'.$enddate.'"
                       AND a.account_code ="1102050101.401"
                       GROUP BY months ORDER BY a.vstdate DESC
               ');
           }

            return view('account_401.account_401_dash',[
                'startdate'        =>  $startdate,
                'enddate'          =>  $enddate,
                'datashow'         =>  $datashow,
                'dabudget_year'    =>  $dabudget_year,
                'budget_year'      =>  $budget_year,
                'y'                =>  $y,
            ]);
    }
    public function account_401_dash(Request $request)
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
                    SELECT month(a.vstdate) as months,year(a.vstdate) as year,l.MONTH_NAME
                    ,count(distinct a.hn) as hn ,count(distinct a.vn) as vn ,count(distinct a.an) as an
                    ,sum(a.income) as income ,sum(a.paid_money) as paid_money
                    ,sum(a.income)-sum(a.discount_money)-sum(a.rcpt_money) as total ,sum(a.debit) as debit
                    ,sum(a.income)-sum(a.discount_money)-sum(a.rcpt_money)-sum(a.fokliad) as debit402,sum(a.fokliad) as sumfokliad

                    FROM acc_debtor a
                    left outer join leave_month l on l.MONTH_ID = month(a.vstdate)
                    WHERE a.vstdate between "'.$startdate.'" and "'.$enddate.'"
                    and account_code="1102050101.401"
                    group by month(a.vstdate)
                    order by a.vstdate desc;
            ');
        } else {

            $bg           = DB::table('budget_year')->where('leave_year_id','=',$budget_year)->first();
            $startdate    = $bg->date_begin;
            $enddate      = $bg->date_end;
            // dd($startdate);
            $datashow = DB::select('
                    SELECT month(a.vstdate) as months,year(a.vstdate) as year,l.MONTH_NAME
                    ,count(distinct a.hn) as hn ,count(distinct a.vn) as vn
                    ,count(distinct a.an) as an ,sum(a.income) as income
                    ,sum(a.paid_money) as paid_money
                    ,sum(a.income)-sum(a.discount_money)-sum(a.rcpt_money) as total ,sum(a.debit) as debit
                    FROM acc_debtor a
                    left outer join leave_month l on l.MONTH_ID = month(a.vstdate)
                    WHERE a.vstdate between "'.$startdate.'" and "'.$enddate.'"
                    and account_code="1102050101.401"
                    group by month(a.vstdate)
                    order by a.vstdate desc;
            ');
        }
        // dd($startdate);
        return view('account_401.account_401_dash',$data,[
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
    public function account_401_claim_detail(Request $request,$months,$year)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        // dd($id);
        $data['users'] = User::get();

        $data = DB::select('
        SELECT *
            from acc_debtor
            WHERE month(vstdate) = "'.$months.'" AND year(vstdate) = "'.$year.'" AND active_claim = "Y"
            GROUP BY vn
        ');
        // WHERE month(U1.vstdate) = "'.$months.'" and year(U1.vstdate) = "'.$year.'"
        return view('account_401.account_401_claim_detail', $data, [
            'data'          =>     $data,
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate
        ]);
    }
    public function account_401_pull(Request $request)
    {
        $datenow       = date('Y-m-d');
        $months        = date('m');
        $year          = date('Y');
        $newday        = date('Y-m-d', strtotime($datenow . ' -2 Day')); //ย้อนหลัง 1 สัปดาห์
        $startdate     = $request->startdate;
        $enddate       = $request->enddate;
        if ($startdate == '') {
            // $data_date_ = Acc_ofc_dateconfig::where('acc_ofc_dateconfig_id','=','1')->first();
            // $startdate = $data_date_->startdate;
            // $enddate = $data_date_->enddate;

            $startdate = '';
            $enddate = '';
                $acc_debtor = DB::select('
                        SELECT *
                        from acc_debtor a
                        WHERE a.account_code="1102050101.401"
                        AND vstdate BETWEEN "' . $newday . '" AND "' . $datenow . '"
                        AND a.debit_total > 0
                        GROUP BY a.vn
                        order by a.vn DESC;
                ');

                $data['data_opd'] = DB::connection('mysql')->select('SELECT * from d_opd WHERE d_anaconda_id ="OFC_401"');
                $data['data_orf'] = DB::connection('mysql')->select('SELECT * from d_orf WHERE d_anaconda_id ="OFC_401"');
                $data['data_oop'] = DB::connection('mysql')->select('SELECT * from d_oop WHERE d_anaconda_id ="OFC_401"');
                $data['data_odx'] = DB::connection('mysql')->select('SELECT * from d_odx WHERE d_anaconda_id ="OFC_401"');
                $data['data_idx'] = DB::connection('mysql')->select('SELECT * from d_idx WHERE d_anaconda_id ="OFC_401"');
                $data['data_ipd'] = DB::connection('mysql')->select('SELECT * from d_ipd WHERE d_anaconda_id ="OFC_401"');
                $data['data_irf'] = DB::connection('mysql')->select('SELECT * from d_irf WHERE d_anaconda_id ="OFC_401"');
                $data['data_aer'] = DB::connection('mysql')->select('SELECT * from d_aer WHERE d_anaconda_id ="OFC_401"');
                $data['data_iop'] = DB::connection('mysql')->select('SELECT * from d_iop WHERE d_anaconda_id ="OFC_401"');
                $data['data_adp'] = DB::connection('mysql')->select('SELECT * from d_adp WHERE d_anaconda_id ="OFC_401"');
                $data['data_pat'] = DB::connection('mysql')->select('SELECT * from d_pat WHERE d_anaconda_id ="OFC_401"');
                $data['data_cht'] = DB::connection('mysql')->select('SELECT * from d_cht WHERE d_anaconda_id ="OFC_401"');
                $data['data_cha'] = DB::connection('mysql')->select('SELECT * from d_cha WHERE d_anaconda_id ="OFC_401"');
                $data['data_ins'] = DB::connection('mysql')->select('SELECT * from d_ins WHERE d_anaconda_id ="OFC_401"');
                $data['data_dru'] = DB::connection('mysql')->select('SELECT * from d_dru WHERE d_anaconda_id ="OFC_401"');
                $data['count_no'] = Acc_debtor::where('approval_code','<>','')->where('account_code','=','1102050101.401')->whereBetween('vstdate', [$newday, $datenow])->count();
                $data['count_null'] = Acc_debtor::where('approval_code','=',Null)->where('account_code','=','1102050101.401')->whereBetween('vstdate', [$newday, $datenow])->count();
                $data['count_claim'] = Acc_debtor::where('active_claim','=','Y')->where('account_code','=','1102050101.401')->whereBetween('vstdate', [$newday, $datenow])->count();
                $data['count_noclaim'] = Acc_debtor::where('active_claim','=','N')->where('account_code','=','1102050101.401')->whereBetween('vstdate', [$newday, $datenow])->count();
        } else {

                $acc_debtor = DB::select('
                        SELECT *
                        from acc_debtor a
                        WHERE a.account_code="1102050101.401"
                        AND vstdate BETWEEN "' . $startdate . '" AND "' . $enddate . '"
                        AND a.debit_total > 0
                        GROUP BY a.vn
                        order by a.vn DESC;
                ');

                $data['data_opd'] = DB::connection('mysql')->select('SELECT * from d_opd WHERE d_anaconda_id ="OFC_401"');
                $data['data_orf'] = DB::connection('mysql')->select('SELECT * from d_orf WHERE d_anaconda_id ="OFC_401"');
                $data['data_oop'] = DB::connection('mysql')->select('SELECT * from d_oop WHERE d_anaconda_id ="OFC_401"');
                $data['data_odx'] = DB::connection('mysql')->select('SELECT * from d_odx WHERE d_anaconda_id ="OFC_401"');
                $data['data_idx'] = DB::connection('mysql')->select('SELECT * from d_idx WHERE d_anaconda_id ="OFC_401"');
                $data['data_ipd'] = DB::connection('mysql')->select('SELECT * from d_ipd WHERE d_anaconda_id ="OFC_401"');
                $data['data_irf'] = DB::connection('mysql')->select('SELECT * from d_irf WHERE d_anaconda_id ="OFC_401"');
                $data['data_aer'] = DB::connection('mysql')->select('SELECT * from d_aer WHERE d_anaconda_id ="OFC_401"');
                $data['data_iop'] = DB::connection('mysql')->select('SELECT * from d_iop WHERE d_anaconda_id ="OFC_401"');
                $data['data_adp'] = DB::connection('mysql')->select('SELECT * from d_adp WHERE d_anaconda_id ="OFC_401"');
                $data['data_pat'] = DB::connection('mysql')->select('SELECT * from d_pat WHERE d_anaconda_id ="OFC_401"');
                $data['data_cht'] = DB::connection('mysql')->select('SELECT * from d_cht WHERE d_anaconda_id ="OFC_401"');
                $data['data_cha'] = DB::connection('mysql')->select('SELECT * from d_cha WHERE d_anaconda_id ="OFC_401"');
                $data['data_ins'] = DB::connection('mysql')->select('SELECT * from d_ins WHERE d_anaconda_id ="OFC_401"');
                $data['data_dru'] = DB::connection('mysql')->select('SELECT * from d_dru WHERE d_anaconda_id ="OFC_401"');
                $data['count_no'] = Acc_debtor::where('approval_code','<>','')->where('account_code','=','1102050101.401')->whereBetween('vstdate', [$startdate, $enddate])->count();
                $data['count_null'] = Acc_debtor::where('approval_code','=',Null)->where('account_code','=','1102050101.401')->whereBetween('vstdate', [$startdate, $enddate])->count();
                $data['count_claim'] = Acc_debtor::where('active_claim','=','Y')->where('account_code','=','1102050101.401')->whereBetween('vstdate', [$startdate, $enddate])->count();
                $data['count_noclaim'] = Acc_debtor::where('active_claim','=','N')->where('account_code','=','1102050101.401')->whereBetween('vstdate', [$startdate, $enddate])->count();
        }

        // $data_activeclaim        = Acc_function::where('pang','1102050101.401')->get();
        $data_activeclaim        = Acc_function::where('pang','1102050101.401')->first();
        $data['activeclaim']     = $data_activeclaim->claim_active;
        $data['acc_function_id'] = $data_activeclaim->acc_function_id;

        return view('account_401.account_401_pull',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'acc_debtor'    =>     $acc_debtor,
        ]);
    }
    function account_401_claimswitch(Request $request)
    {
        // $id = $request->idfunc;
        Acc_function::where('pang','1102050101.401')->update(['claim_active'=> $request->onoff]);
        return response()->json([
            'status'    => '200'
        ]);
    }
    public function account_401_pulldata(Request $request)
    {
        $datenow    = date('Y-m-d');
        $datatime   = date('H:m:s');
        $ip = $request->ip();
        $startdate  = $request->datepicker;
        $enddate    = $request->datepicker2;
        Acc_ofc_dateconfig::truncate();
        $acc_debtor = DB::connection('mysql2')->select(
            'SELECT o.vn,o.an,o.hn,pt.cid,concat(pt.pname,pt.fname," ",pt.lname) ptname
                ,o.vstdate,o.vsttime
                ,v.hospmain,"" regdate,"" dchdate,op.income as income_group
                ,ptt.pttype_eclaim_id,vp.pttype
                ,"17" as acc_code
                ,"1102050101.401" as account_code
                ,"เบิกจ่ายตรงกรมบัญชีกลาง" as account_name
                ,v.income,v.uc_money,v.discount_money,v.paid_money,v.rcpt_money
                ,v.income-v.discount_money-v.rcpt_money as debit
                ,if(op.icode IN ("3010058"),sum_price,0) as fokliad
                ,sum(if(op.income="02",sum_price,0)) as debit_instument
                ,sum(if(op.icode IN("1560016","1540073","1530005","1540048","1620015","1600012","1600015"),sum_price,0)) as debit_drug
                ,sum(if(op.icode IN("3001412","3001417"),sum_price,0)) as debit_toa
                ,sum(if(op.icode IN("3010829","3011068","3010864","3010861","3010862","3010863","3011069","3011012","3011070"),sum_price,0)) as debit_refer
                ,ptt.max_debt_money
                ,GROUP_CONCAT(DISTINCT ov.icd10 order by ov.diagtype) AS icd10,v.pdx
                ,group_concat(DISTINCT hh.appr_code,":",hh.transaction_amount,"/") AS AppKTB
                ,rd.sss_approval_code AS approval_code,rd.amount AS price_ofc,d.cc
                from ovst o
                left join vn_stat v on v.vn=o.vn
                left join patient pt on pt.hn=o.hn
                LEFT JOIN visit_pttype vp on vp.vn = v.vn
                LEFT JOIN pttype ptt on o.pttype=ptt.pttype
                LEFT JOIN pttype_eclaim e on e.code=ptt.pttype_eclaim_id
                LEFT JOIN opitemrece op ON op.vn = o.vn
                LEFT JOIN ovstdiag ov ON ov.vn = v.vn
                LEFT JOIN rcpt_debt rd ON v.vn = rd.vn
                LEFT JOIN hpc11_ktb_approval hh on hh.pid = pt.cid and hh.transaction_date = v.vstdate
                LEFT JOIN opdscreen d on d.vn = v.vn
                WHERE o.vstdate BETWEEN "' . $startdate . '" AND "' . $enddate . '"
                AND vp.pttype IN(SELECT pttype FROM pkbackoffice.acc_setpang_type WHERE pang ="1102050101.401")
                and v.income-v.discount_money-v.rcpt_money <> 0
                and (o.an="" or o.an is null) AND pt.cid IS NOT NULL
                GROUP BY v.vn
        ');
        // AND vp.pttype IN(SELECT pttype from pkbackoffice.acc_setpang_type WHERE pttype IN (SELECT pttype FROM pkbackoffice.acc_setpang_type WHERE pang ="1102050101.401"))
        // AND vp.pttype IN("O1","O2","O3","O4","O5")
        // ,e.ar_opd as account_code
        // ,e.name as account_name
         // $datenow    = date('Y-m-d');
         

        $bgs_year      = DB::table('budget_year')->where('years_now','Y')->first();
        $bg_yearnow    = $bgs_year->leave_year_id;

            foreach ($acc_debtor as $key => $value) {
                        $check = Acc_debtor::where('vn', $value->vn)->where('account_code','1102050101.401')->count();
                        // ->whereBetween('vstdate', [$startdate, $enddate])
                        // $starttime = substr($vst, 0, 5);
                        // $day = substr($value->vstdate,0,2);
                        // $mo = substr($value->vstdate,3,2);
                        // $year = substr($value->vstdate,7,4);
                        // $vsttime = substr($value->vstdate,12,8);
                        $hm = substr($value->vsttime,0,5);
                        // $hh = substr($value->vstdate,12,2);
                        // $mm = substr($value->vstdate,15,2);
                        // $vstdate = $year.'-'.$mo.'-'.$day;
                        if ($check > 0) {
                            Acc_debtor::where('vn', $value->vn)->update([
                                'pdx'                => $value->pdx,
                                'icd10'              => $value->icd10,
                                'approval_code'      => $value->approval_code,
                                'price_ofc'          => $value->price_ofc,
                                'debit_total'        => $value->income,
                                'bg_yearnow'         => $bg_yearnow,
                            ]);
                        }else{
                            Acc_debtor::insert([
                                'bg_yearnow'         => $bg_yearnow,
                                'hn'                 => $value->hn,
                                'an'                 => $value->an,
                                'vn'                 => $value->vn,
                                'cid'                => $value->cid,
                                'ptname'             => $value->ptname,
                                'pttype'             => $value->pttype,
                                'vstdate'            => $value->vstdate,
                                'vsttime'            => $value->vsttime,
                                'hm'                 => $hm,
                                'acc_code'           => $value->acc_code,
                                'account_code'       => $value->account_code,
                                'account_name'       => $value->account_name,
                                'income_group'       => $value->income_group,
                                'income'             => $value->income,
                                'uc_money'           => $value->uc_money,
                                'discount_money'     => $value->discount_money,
                                'paid_money'         => $value->paid_money,
                                'rcpt_money'         => $value->rcpt_money,
                                'debit'              => $value->income,
                                'debit_drug'         => $value->debit_drug,
                                'debit_instument'    => $value->debit_instument,
                                'debit_toa'          => $value->debit_toa,
                                'debit_refer'        => $value->debit_refer,
                                'debit_total'        => $value->income,
                                'max_debt_amount'    => $value->max_debt_money,
                                'pdx'                => $value->pdx,
                                'icd10'              => $value->icd10,
                                'cc'                 => $value->cc,
                                'approval_code'      => $value->approval_code,
                                'price_ofc'          => $value->price_ofc,
                                'acc_debtor_userid'  => Auth::user()->id
                            ]); 
                        }


                        
            }
            
            
            Acc_ofc_dateconfig::insert([
                'startdate'   => $startdate,
                'enddate'     => $enddate,
            ]);

            Acc_debtor_log::insert([
                'account_code'       => '1102050101.216',
                'make_gruop'         => 'ดึงลูกหนี้',
                'date_save'          => $datenow,
                'date_time'          => $datatime,
                'user_id'            => Auth::user()->id,
                'ip'                 => $ip
            ]);

            return response()->json([

                'status'    => '200'
            ]);
    }
    public function account_401_checksit(Request $request)
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
    public function account_401_stam(Request $request)
    {
        $datenow = date('Y-m-d');
        $datatime   = date('H:m:s');
        $ip = $request->ip();
         Acc_debtor_log::insert([
             'account_code'       => '1102050101.401',
             'make_gruop'         => 'ตั้งลูกหนี้และส่งลูกหนี้',
             'date_save'          => $datenow,
             'date_time'          => $datatime,
             'user_id'            => Auth::user()->id,
             'ip'                 => $ip
         ]);
         $maxnumber = DB::table('acc_debtor_log')->where('account_code','1102050101.401')->where('user_id',Auth::user()->id)->max('acc_debtor_log_id');
        $id = $request->ids;
        $iduser = Auth::user()->id;
        $data = Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))->get();
            Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))
                    ->update([
                         'stamp'       => 'Y',
                         'send_active' => 'Y'
                    ]);
        foreach ($data as $key => $value) {
                $date = date('Y-m-d H:m:s');
                $check = Acc_1102050101_401::where('vn', $value->vn)->count();
                // $check = Acc_debtor::where('vn', $value->vn)
                // ->where('debit_total','=','0')
                // ->count();
                if ($check > 0) {
                    Acc_1102050101_401::where('vn', $value->vn)->update([
                        'hm'                => $value->hm,
                    ]);
                } else {
                    Acc_1102050101_401::insert([
                            'vn'                => $value->vn,
                            'hn'                => $value->hn,
                            'an'                => $value->an,
                            'cid'               => $value->cid,
                            'ptname'            => $value->ptname,
                            'vstdate'           => $value->vstdate,
                            'vsttime'           => $value->vsttime,
                            'hm'                => $value->hm,
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

                $check_total  = Acc_account_total::where('vn', $value->vn)->where('account_code','=','1102050101.401')->count();
                if ($check_total > 0) {
                    # code...
                } else {
                    Acc_account_total::insert([
                        'bg_yearnow'         => $value->bg_yearnow,
                        'vn'                 => $value->vn,
                        'hn'                 => $value->hn,
                        'an'                 => $value->an,
                        'cid'                => $value->cid,
                        'ptname'             => $value->ptname,
                        'vstdate'            => $value->vstdate,
                        'vsttime'            => $value->vsttime,
                        'hospmain'           => $value->hospmain,
                        'regdate'            => $value->regdate,
                        'dchdate'            => $value->dchdate,    
                        'pttype'             => $value->pttype,
                        'pttype_nhso'        => $value->subinscl,
                        'hsub'               => $value->hsub,    
                        'acc_code'           => $value->acc_code,
                        'account_code'       => $value->account_code,
                        'rw'                 => $value->rw,
                        'adjrw'              => $value->adjrw,
                        'total_adjrw_income' => $value->total_adjrw_income,
                        'debit_drug'         => $value->debit_drug,
                        'debit_instument'    => $value->debit_instument,
                        'debit_toa'          => $value->debit_toa,
                        'debit_refer'        => $value->debit_refer,
                        'debit_walkin'       => $value->debit_walkin,    
                        'debit_imc'          => $value->debit_imc,
                        'debit_imc_adpcode'  => $value->debit_imc_adpcode,
                        'debit_thai'         => $value->debit_thai,    
                        'income'             => $value->income,
                        'uc_money'           => $value->uc_money,
                        'discount_money'     => $value->discount_money,
                        'rcpt_money'         => $value->rcpt_money,
                        'debit'              => $value->debit,
                        'debit_total'        => $value->debit_total,
                        'acc_debtor_userid'  => $value->acc_debtor_userid,
                        'acc_debtor_log_id'  => $maxnumber
                    ]);
                }



        }
        return response()->json([
            'status'    => '200'
        ]);
    }
    public function account_401_destroy_all(Request $request)
    {
        $id = $request->ids;
        Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))->delete();
        return response()->json([
            'status'    => '200'
        ]);
    }
    public function account_401_detail(Request $request,$months,$year)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        // dd($id);
        $data['users'] = User::get();

        $data = DB::select('
        SELECT *
            from acc_1102050101_401 U1
            WHERE month(U1.vstdate) = "'.$months.'" AND year(U1.vstdate) = "'.$year.'"
            GROUP BY U1.vn
        ');
        // WHERE month(U1.vstdate) = "'.$months.'" and year(U1.vstdate) = "'.$year.'"
        return view('account_401.account_401_detail', $data, [
            'data'          =>     $data,
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate
        ]);
    }
    public function account_401_search(Request $request)
    {
        $datenow       = date('Y-m-d');
        $startdate     = $request->startdate;
        $enddate       = $request->enddate;
        $date          = date('Y-m-d');
        $new_day       = date('Y-m-d', strtotime($date . ' -7 day')); //ย้อนหลัง 1 วัน
        $data['users'] = User::get();
        if ($startdate =='') {
           $datashow = DB::select('
               SELECT a.*,b.approval_code,b.pdx from acc_1102050101_401 a
               LEFT JOIN acc_debtor b ON b.vn = a.vn

               WHERE a.vstdate BETWEEN "'.$new_day.'" AND  "'.$date.'"

               GROUP BY a.vn
           ');
        //    LEFT OUTER JOIN d_fdh d ON d.vn = a.vn
        } else {
           $datashow = DB::select('
               SELECT a.*,b.approval_code,b.pdx 

               from acc_1102050101_401 a
               LEFT JOIN acc_debtor b ON b.vn = a.vn
               WHERE a.vstdate BETWEEN "'.$startdate.'" AND  "'.$enddate.'"

               GROUP BY a.vn
           ');
        }

        $data_activeclaim        = Acc_function::where('pang','1102050101.401')->first();
        $data['activeclaim']     = $data_activeclaim->claim_active;
        $data['acc_function_id'] = $data_activeclaim->acc_function_id;


        return view('account_401.account_401_search', $data, [
            'startdate'     => $startdate,
            'enddate'       => $enddate,
            'datashow'      => $datashow,
            'startdate'     => $startdate,
            'enddate'       => $enddate
        ]);
    }
    public function account_401_stm(Request $request,$months,$year)
    {
        $datenow = date('Y-m-d');

        $data['users'] = User::get();

        // $datashow = DB::select('
        //     SELECT U1.an,U1.vn,U1.hn,U1.cid,U1.ptname,U1.vstdate,U1.dchdate,U1.pttype,U1.debit_total,U2.pricereq_all,U2.STMdoc
        //         from acc_1102050101_401 U1
        //         LEFT JOIN acc_stm_ofc U2 on U2.hn = U1.hn AND U2.vstdate = U1.vstdate
        //         WHERE month(U1.vstdate) = "'.$months.'" AND year(U1.vstdate) = "'.$year.'"
        //         AND U2.pricereq_all is not null
        //         group by U1.vn
        // ');
        $datashow = DB::select('
            SELECT a.vn,a.an,a.hn,a.cid,a.ptname,a.vstdate,a.dchdate,a.debit_total,a.pttype,a.vsttime
            ,a.income_group,a.stm_money,a.stm_total,a.STMdoc
            FROM acc_1102050101_401 a
            WHERE month(a.vstdate) = "'.$months.'" and year(a.vstdate) = "'.$year.'"
            AND a.stm_money IS NOT NULL
            GROUP BY a.vn
        ');

        return view('account_401.account_401_stm', $data, [
            'datashow'      =>     $datashow,
            'months'        =>     $months,
            'year'          =>     $year
        ]);
    }
    public function account_401_stmnull(Request $request,$months,$year)
    {
        $datenow = date('Y-m-d');
        $data['users'] = User::get();

        $datashow = DB::connection('mysql')->select('
            SELECT a.vn,a.an,a.hn,a.cid,a.ptname,a.vstdate,a.dchdate,a.debit_total,a.pttype
            ,a.income_group,a.stm_money,a.stm_total,a.STMdoc
            FROM acc_1102050101_401 a
            WHERE month(a.vstdate) = "'.$months.'" and year(a.vstdate) = "'.$year.'"
            AND a.stm_money IS NULL
            GROUP BY a.vn
        ');

        return view('account_401.account_401_stmnull',[
            'datashow'          =>     $datashow,
            'months'        =>     $months,
            'year'          =>     $year
        ]);
    }
    public function account_401_yok(Request $request,$months,$year)
    {
        $datenow = date('Y-m-d');
        $data['users'] = User::get();
        $data = DB::select('
            SELECT *
                from acc_1102050101_401 U1
                WHERE month(U1.vstdate) = "'.$months.'" AND year(U1.vstdate) = "'.$year.'"
                AND U1.stm_money IS NULL
                GROUP BY U1.vn
        ');
        // U1.an,U1.vn,U1.hn,U1.cid,U1.ptname,U1.vstdate,U1.pttype,U1.debit_total,U1.nhso_docno,U1.dchdate,U1.nhso_ownright_pid,U1.recieve_true,U1.difference,U1.recieve_no,U1.recieve_date
        return view('account_401.account_401_yok', $data, [
            'data'          =>     $data,
            'months'        =>     $months,
            'year'          =>     $year
        ]);
    }
    public function account_401_detail_date(Request $request,$startdate,$enddate)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        // dd($id);
        $data['users'] = User::get();

        $data = DB::select('
        SELECT *
            from acc_1102050101_401 U1
            WHERE U1.vstdate BETWEEN "'.$startdate.'" AND  "'.$enddate.'"
            GROUP BY U1.vn
        ');
        // WHERE month(U1.vstdate) = "'.$months.'" and year(U1.vstdate) = "'.$year.'"
        return view('account_401.account_401_detail_date', $data, [
            'data'          =>     $data,
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate
        ]);
    }
    public function account_401_stm_date(Request $request,$startdate,$enddate)
    {
        $datenow = date('Y-m-d');

        $data['users'] = User::get();

        $datashow = DB::select('
            SELECT U1.an,U1.vn,U1.hn,U1.cid,U1.ptname,U1.vstdate,U1.dchdate,U1.pttype,U1.debit_total,U2.pricereq_all,U2.STMdoc
                from acc_1102050101_401 U1
                LEFT JOIN acc_stm_ofc U2 on U2.hn = U1.hn AND U2.vstdate = U1.vstdate
                WHERE U1.vstdate BETWEEN "'.$startdate.'" AND  "'.$enddate.'"
                AND U2.pricereq_all is not null
                group by U1.vn
        ');

        return view('account_401.account_401_stm_date', $data, [
            'datashow'         =>     $datashow,
            'startdate'        =>     $startdate,
            'enddate'          =>     $enddate
        ]);
    }
    public function account_401_stmnull_date(Request $request,$startdate,$enddate)
    {
        $datenow = date('Y-m-d');

        $data['users'] = User::get();

        $datashow = DB::select('
            SELECT U1.an,U1.vn,U1.hn,U1.cid,U1.ptname,U1.vstdate,U1.dchdate,U1.pttype,U1.income,U1.rcpt_money,U1.debit_total,U2.pricereq_all ,U2.STMdoc
                from acc_1102050101_401 U1
                LEFT JOIN acc_stm_ofc U2 on U2.hn = U1.hn AND U2.vstdate = U1.vstdate
                WHERE U1.vstdate BETWEEN "'.$startdate.'" AND  "'.$enddate.'"
                AND U2.pricereq_all is null
                group by U1.vn
        ');

        return view('account_401.account_401_stmnull_date',[
            'datashow'         =>     $datashow,
            'startdate'        =>     $startdate,
            'enddate'          =>     $enddate
        ]);
    }

    // ********************* Send *******************************
    public function account_401_send(Request $request)
    {
        $id = $request->ids;
        $iduser = Auth::user()->id;
        $data = Acc_1102050101_401::whereIn('acc_1102050101_401_id',explode(",",$id))->get();
        Acc_1102050101_401::whereIn('acc_1102050101_401_id',explode(",",$id))
                    ->update([
                        'sendactive' => 'Y'
                    ]);
        foreach ($data as $key => $value) {
                $date = date('Y-m-d H:m:s');
             $check = Acc_1102050101_401send::where('vn', $value->vn)->count();
                // $check = Acc_debtor::where('vn', $value->vn)
                // ->where('debit_total','=','0')
                // ->count();
                if ($check > 0) {
                    Acc_1102050101_401send::where('vn', $value->vn)->update([
                        'hm'                => $value->hm,
                    ]);
                } else {
                    Acc_1102050101_401send::insert([
                            'vn'                => $value->vn,
                            'hn'                => $value->hn,
                            'an'                => $value->an,
                            'cid'               => $value->cid,
                            'ptname'            => $value->ptname,
                            'vstdate'           => $value->vstdate,
                            'vsttime'           => $value->vsttime,
                            'hm'                => $value->hm,
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
                            'stm_rep'           => $value->stm_rep,
                            'stm_money'         => $value->stm_money,
                            'stm_rcpno'         => $value->stm_rcpno,
                            'STMDoc'            => $value->STMDoc,
                            'acc_debtor_userid' => $iduser
                    ]);
                }

        }
        return response()->json([
            'status'    => '200'
        ]);
    }

    // *************** CLAIM **********************
    public function account_401_claim(Request $request)
    {
        D_opd::where('d_anaconda_id','=','OFC_401')->delete();
        D_orf::where('d_anaconda_id','=','OFC_401')->delete();
        D_oop::where('d_anaconda_id','=','OFC_401')->delete();
        D_odx::where('d_anaconda_id','=','OFC_401')->delete();
        D_idx::where('d_anaconda_id','=','OFC_401')->delete();
        D_ipd::where('d_anaconda_id','=','OFC_401')->delete();
        D_irf::where('d_anaconda_id','=','OFC_401')->delete();
        D_aer::where('d_anaconda_id','=','OFC_401')->delete();
        D_iop::where('d_anaconda_id','=','OFC_401')->delete();
        D_adp::where('d_anaconda_id','=','OFC_401')->delete();
        D_dru::where('d_anaconda_id','=','OFC_401')->delete();
        D_pat::where('d_anaconda_id','=','OFC_401')->delete();
        D_cht::where('d_anaconda_id','=','OFC_401')->delete();
        D_cha::where('d_anaconda_id','=','OFC_401')->delete();
        D_ins::where('d_anaconda_id','=','OFC_401')->delete();
        Fdh_ins::where('d_anaconda_id','=','OFC_401')->delete();
        Fdh_pat::where('d_anaconda_id','=','OFC_401')->delete();
        Fdh_opd::where('d_anaconda_id','=','OFC_401')->delete();


        Fdh_sesion::where('d_anaconda_id', '=', 'OFC_401')->delete();
        $s_date_now = date("Y-m-d");
        $s_time_now = date("H:i:s");
        $id = $request->ids;
        $iduser = Auth::user()->id;

        #ตัดขีด, ตัด : ออก
        $pattern_date = '/-/i';
        $s_date_now_preg = preg_replace($pattern_date, '', $s_date_now);
        $pattern_time = '/:/i';
        $s_time_now_preg = preg_replace($pattern_time, '', $s_time_now);
        #ตัดขีด, ตัด : ออก
        $folder_name = 'OFC_401_' . $s_date_now_preg . '_' . $s_time_now_preg;
        Fdh_sesion::insert([
            'folder_name'      => $folder_name,
            'd_anaconda_id'    => 'OFC_401',
            'date_save'        => $s_date_now,
            'time_save'        => $s_time_now,
            'userid'           => $iduser
        ]);

        $data_vn_1 = Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))->get();
        // $data = Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))->get();
                Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))
                ->update([
                    'active_claim' => 'Y'
                ]);
        // $data_vn_1 = Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))->where('account_code','=',"1102050101.401")->where('stamp','=',"N")->get();
        // $data_vn_1 = Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))->where('account_code','=',"1102050101.401")->where('stamp','=',"N")->where('approval_code','<>',"")->get();
         foreach ($data_vn_1 as $key => $va1) {
                //D_ins OK
                $data_ins_ = DB::connection('mysql2')->select(
                    'SELECT v.hn HN
                    ,if(i.an is null,p.hipdata_code,pp.hipdata_code) INSCL ,if(i.an is null,p.pcode,pp.pcode) SUBTYPE,v.cid CID,v.hcode AS HCODE
                    ,DATE_FORMAT(if(i.an is null,v.pttype_begin,ap.begin_date), "%Y%m%d") DATEIN
                    ,DATE_FORMAT(if(i.an is null,v.pttype_expire,ap.expire_date), "%Y%m%d") DATEEXP
                    ,if(i.an is null,v.hospmain,ap.hospmain) HOSPMAIN,if(i.an is null,v.hospsub,ap.hospsub) HOSPSUB,"" GOVCODE ,"" GOVNAME
                    ,ifnull(if(i.an is null,r.sss_approval_code,ap.claim_code),vp.claim_code) PERMITNO
                    ,"" DOCNO ,"" OWNRPID,"" OWNNAME ,i.an AN ,v.vn SEQ ,"" SUBINSCL,"" RELINSCL
                    ,"" HTYPE
                    FROM vn_stat v
                    LEFT OUTER JOIN pttype p on p.pttype = v.pttype
                    LEFT OUTER JOIN ipt i on i.vn = v.vn
                    LEFT OUTER JOIN pttype pp on pp.pttype = i.pttype
                    LEFT OUTER JOIN ipt_pttype ap on ap.an = i.an
                    LEFT OUTER JOIN visit_pttype vp on vp.vn = v.vn
                    LEFT OUTER JOIN rcpt_debt r on r.vn = v.vn
                    LEFT OUTER JOIN patient px on px.hn = v.hn

                    WHERE v.vn IN("'.$va1->vn.'")
                    GROUP BY v.vn
                ');
                // ,"2" HTYPE
                foreach ($data_ins_ as $va_01) {
                    D_ins::insert([
                        'HN'                => $va_01->HN,
                        'INSCL'             => $va_01->INSCL,
                        'SUBTYPE'           => $va_01->SUBTYPE,
                        'CID'               => $va_01->CID,
                        'HCODE'             => $va_01->HCODE,
                        'DATEEXP'           => $va_01->DATEEXP,
                        'HOSPMAIN'          => $va_01->HOSPMAIN,
                        'HOSPSUB'           => $va_01->HOSPSUB,
                        'GOVCODE'           => $va_01->GOVCODE,
                        'GOVNAME'           => $va_01->GOVNAME,
                        'PERMITNO'          => $va_01->PERMITNO,
                        'DOCNO'             => $va_01->DOCNO,
                        'OWNRPID'           => $va_01->OWNRPID,
                        'OWNNAME'           => $va_01->OWNNAME,
                        'AN'                => $va_01->AN,
                        'SEQ'               => $va_01->SEQ,
                        'SUBINSCL'          => $va_01->SUBINSCL,
                        'RELINSCL'          => $va_01->RELINSCL,
                        'HTYPE'             => $va_01->HTYPE,

                        'user_id'           => $iduser,
                        'd_anaconda_id'     => 'OFC_401'
                    ]);
                }
                //D_pat OK
                $data_pat_ = DB::connection('mysql2')->select(
                    'SELECT v.hcode HCODE,v.hn HN
                    ,pt.chwpart CHANGWAT,pt.amppart AMPHUR,DATE_FORMAT(pt.birthday,"%Y%m%d") DOB
                    ,pt.sex SEX,pt.marrystatus MARRIAGE ,pt.occupation OCCUPA,lpad(pt.nationality,3,0) NATION,pt.cid PERSON_ID
                    ,concat(pt.fname," ",pt.lname,",",pt.pname) NAMEPAT
                    ,pt.pname TITLE,pt.fname FNAME,pt.lname LNAME,"1" IDTYPE
                    FROM vn_stat v
                    LEFT OUTER JOIN pttype p on p.pttype = v.pttype
                    LEFT OUTER JOIN ipt i on i.vn = v.vn
                    LEFT OUTER JOIN patient pt on pt.hn = v.hn
                    WHERE v.vn IN("'.$va1->vn.'")
                    GROUP BY v.hn
                ');
                foreach ($data_pat_ as $va_02) {
                    D_pat::insert([
                        'HCODE'              => $va_02->HCODE,
                        'HN'                 => $va_02->HN,
                        'CHANGWAT'           => $va_02->CHANGWAT,
                        'AMPHUR'             => $va_02->AMPHUR,
                        'DOB'                => $va_02->DOB,
                        'SEX'                => $va_02->SEX,
                        'MARRIAGE'           => $va_02->MARRIAGE,
                        'OCCUPA'             => $va_02->OCCUPA,
                        'NATION'             => $va_02->NATION,
                        'PERSON_ID'          => $va_02->PERSON_ID,
                        'NAMEPAT'            => $va_02->NAMEPAT,
                        'TITLE'              => $va_02->TITLE,
                        'FNAME'              => $va_02->FNAME,
                        'LNAME'              => $va_02->LNAME,
                        'IDTYPE'             => $va_02->IDTYPE,

                        'user_id'            => $iduser,
                        'd_anaconda_id'      => 'OFC_401'
                    ]);
                }
                //D_opd OK
                // $data_opd = DB::connection('mysql2')->select('
                //         SELECT  v.hn HN
                //         ,v.spclty CLINIC
                //         ,DATE_FORMAT(v.vstdate,"%Y%m%d") DATEOPD
                //         ,concat(substr(o.vsttime,1,2),substr(o.vsttime,4,2)) TIMEOPD
                //         ,v.vn SEQ
                //         ,"1" UUC ,"" DETAIL,""BTEMP,""SBP,""DBP,""PR,""RR,""OPTYPE,""TYPEIN,""TYPEOUT
                //         from vn_stat v
                //         LEFT OUTER JOIN ovst o on o.vn = v.vn
                //         LEFT OUTER JOIN pttype p on p.pttype = v.pttype
                //         LEFT OUTER JOIN ipt i on i.vn = v.vn
                //         LEFT OUTER JOIN patient pt on pt.hn = v.hn
                //         WHERE v.vn IN("'.$va1->vn.'")
                // ');
                 //D_opd OK
                 $data_opd = DB::connection('mysql2')->select(
                    'SELECT  v.hn HN,v.spclty CLINIC,DATE_FORMAT(v.vstdate,"%Y%m%d") DATEOPD,concat(substr(o.vsttime,1,2),substr(o.vsttime,4,2)) TIMEOPD,v.vn SEQ
                        ,"1" UUC ,"" DETAIL,oc.temperature as BTEMP,oc.bps as SBP,oc.bpd as DBP,""PR,""RR,""OPTYPE,ot.export_code as TYPEIN,st.export_code as TYPEOUT
                        from vn_stat v
                        LEFT OUTER JOIN ovst o on o.vn = v.vn
                        LEFT OUTER JOIN opdscreen oc  on oc.vn = o.vn
                        LEFT OUTER JOIN pttype p on p.pttype = v.pttype
                        LEFT OUTER JOIN ipt i on i.vn = v.vn
                        LEFT OUTER JOIN patient pt on pt.hn = v.hn
                        LEFT OUTER JOIN ovstist ot on ot.ovstist = o.ovstist
                        LEFT OUTER JOIN ovstost st on st.ovstost = o.ovstost
                        WHERE v.vn IN("'.$va1->vn.'")
                ');
                foreach ($data_opd as $val3) {
                    D_opd::insert([
                        'HN'                => $val3->HN,
                        'CLINIC'            => $val3->CLINIC,
                        'DATEOPD'           => $val3->DATEOPD,
                        'TIMEOPD'           => $val3->TIMEOPD,
                        'SEQ'               => $val3->SEQ,
                        'UUC'               => $val3->UUC,
                        'DETAIL'            => $val3->DETAIL,
                        'BTEMP'             => $val3->BTEMP,
                        'SBP'               => $val3->SBP,
                        'DBP'               => $val3->DBP,
                        'PR'                => $val3->PR,
                        'RR'                => $val3->RR,
                        'OPTYPE'            => $val3->OPTYPE,
                        'TYPEIN'            => $val3->TYPEIN,
                        'TYPEOUT'           => $val3->TYPEOUT,
                        'user_id'           => $iduser,
                        'd_anaconda_id'     => 'OFC_401'
                    ]);
                }
                //D_orf _OK
                $data_orf_ = DB::connection('mysql2')->select(
                    'SELECT v.hn HN
                        ,DATE_FORMAT(v.vstdate,"%Y%m%d") DATEOPD,v.spclty CLINIC,ifnull(r1.refer_hospcode,r2.refer_hospcode) REFER
                        ,"0100" REFERTYPE,v.vn SEQ,"" REFERDATE
                        FROM vn_stat v
                        LEFT OUTER JOIN ovst o on o.vn = v.vn
                        LEFT OUTER JOIN referin r1 on r1.vn = v.vn
                        LEFT OUTER JOIN referout r2 on r2.vn = v.vn
                        WHERE v.vn IN("'.$va1->vn.'")
                        AND (r1.vn is not null or r2.vn is not null);
                ');
                foreach ($data_orf_ as $va_03) {
                    D_orf::insert([
                        'HN'                => $va_03->HN,
                        'CLINIC'            => $va_03->CLINIC,
                        'DATEOPD'           => $va_03->DATEOPD,
                        'REFER'             => $va_03->REFER,
                        'SEQ'               => $va_03->SEQ,
                        'REFERTYPE'         => $va_03->REFERTYPE,
                        'REFERDATE'         => $va_03->REFERDATE,
                        'user_id'           => $iduser,
                        'd_anaconda_id'     => 'OFC_401'
                    ]);
                }
                 // D_odx OK
                 $data_odx_ = DB::connection('mysql2')->select(
                    'SELECT v.hn as HN,DATE_FORMAT(v.vstdate,"%Y%m%d") as DATEDX,v.spclty as CLINIC,o.icd10 as DIAG,o.diagtype as DXTYPE
                        ,if(d.licenseno="","-99999",d.licenseno) as DRDX,v.cid as PERSON_ID ,v.vn as SEQ
                        FROM vn_stat v
                        LEFT OUTER JOIN ovstdiag o on o.vn = v.vn
                        LEFT OUTER JOIN doctor d on d.`code` = o.doctor
                        INNER JOIN icd101 i on i.code = o.icd10
                        WHERE v.vn IN("'.$va1->vn.'")
                ');
                foreach ($data_odx_ as $va_04) {
                    if ($va_04->DIAG == 'U779') {
                        $diag_new = 'U77';
                    } else {
                        $diag_new = $va_04->DIAG;
                    }

                    D_odx::insert([
                        'HN'                => $va_04->HN,
                        'CLINIC'            => $va_04->CLINIC,
                        'DATEDX'            => $va_04->DATEDX,
                        'DIAG'              => $diag_new,
                        'DXTYPE'            => $va_04->DXTYPE,
                        'DRDX'              => $va_04->DRDX,
                        'PERSON_ID'         => $va_04->PERSON_ID,
                        'SEQ'               => $va_04->SEQ,
                        'user_id'           => $iduser,
                        'd_anaconda_id'     => 'OFC_401'
                    ]);
                }
                 //D_oop OK
                 $data_oop_ = DB::connection('mysql2')->select(
                    'SELECT v.hn as HN,DATE_FORMAT(v.vstdate,"%Y%m%d") as DATEOPD,v.spclty as CLINIC,o.icd10 as OPER
                        ,if(d.licenseno="","-99999",d.licenseno) as DROPID,pt.cid as PERSON_ID ,v.vn as SEQ ,""SERVPRICE
                        FROM vn_stat v
                        LEFT OUTER JOIN ovstdiag o on o.vn = v.vn
                        LEFT OUTER JOIN patient pt on v.hn=pt.hn
                        LEFT OUTER JOIN doctor d on d.`code` = o.doctor
                        INNER JOIN icd9cm1 i on i.code = o.icd10
                        WHERE v.vn IN("'.$va1->vn.'")
                        AND substring(o.icd10,1,1) in ("0","1","2","3","4","5","6","7","8","9")
                ');
                foreach ($data_oop_ as $va_05) {
                    D_oop::insert([
                        'HN'                => $va_05->HN,
                        'CLINIC'            => $va_05->CLINIC,
                        'DATEOPD'           => $va_05->DATEOPD,
                        'OPER'              => $va_05->OPER,
                        'DROPID'            => $va_05->DROPID,
                        'PERSON_ID'         => $va_05->PERSON_ID,
                        'SEQ'               => $va_05->SEQ,
                        'SERVPRICE'         => $va_05->SERVPRICE,
                        'user_id'           => $iduser,
                        'd_anaconda_id'     => 'OFC_401'
                    ]);

                }
                //D_ipd OK
                $data_ipd_ = DB::connection('mysql2')->select(
                    'SELECT a.hn HN,a.an AN,DATE_FORMAT(i.regdate,"%Y%m%d") DATEADM,Time_format(i.regtime,"%H%i") TIMEADM
                        ,DATE_FORMAT(i.dchdate,"%Y%m%d") DATEDSC,Time_format(i.dchtime,"%H%i")  TIMEDSC,right(i.dchstts,1) DISCHS
                        ,right(i.dchtype,1) DISCHT,i.ward WARDDSC,i.spclty DEPT,format(i.bw/1000,3) ADM_W,"1" UUC ,"I" SVCTYPE
                        FROM an_stat a
                        LEFT OUTER JOIN ipt i on i.an = a.an
                        LEFT OUTER JOIN pttype p on p.pttype = a.pttype
                        LEFT OUTER JOIN patient pt on pt.hn = a.hn
                        WHERE i.vn IN("'.$va1->vn.'")
                ');
                foreach ($data_ipd_ as $va_06) {
                    D_ipd::insert([
                        'AN'                => $va_06->AN,
                        'HN'                => $va_06->HN,
                        'DATEADM'           => $va_06->DATEADM,
                        'TIMEADM'           => $va_06->TIMEADM,
                        'DATEDSC'           => $va_06->DATEDSC,
                        'TIMEDSC'           => $va_06->TIMEDSC,
                        'DISCHS'            => $va_06->DISCHS,
                        'DISCHT'            => $va_06->DISCHT,
                        'DEPT'              => $va_06->DEPT,
                        'ADM_W'             => $va_06->ADM_W,
                        'UUC'               => $va_06->UUC,
                        'SVCTYPE'           => $va_06->SVCTYPE,
                        'user_id'           => $iduser,
                        'd_anaconda_id'     => 'OFC_401'
                    ]);
                }
                //D_irf OK
                 $data_irf_ = DB::connection('mysql2')->select(
                    'SELECT a.an AN,ifnull(o.refer_hospcode,oo.refer_hospcode) REFER,"0100" REFERTYPE
                        FROM an_stat a
                        LEFT OUTER JOIN ipt ip on ip.an = a.an
                        LEFT OUTER JOIN referout o on o.vn = a.an
                        LEFT OUTER JOIN referin oo on oo.vn = a.an
                        WHERE ip.vn IN("'.$va1->vn.'")
                        AND (a.an in(SELECT vn FROM referin WHERE vn = oo.vn) or a.an in(SELECT vn FROM referout WHERE vn = o.vn));
                ');
                foreach ($data_irf_ as $va_07) {
                    D_irf::insert([
                        'AN'                 => $va_07->AN,
                        'REFER'              => $va_07->REFER,
                        'REFERTYPE'          => $va_07->REFERTYPE,
                        'user_id'            => $iduser,
                        'd_anaconda_id'      => 'OFC_401',
                    ]);
                }
                //D_idx OK
                $data_idx_ = DB::connection('mysql2')->select(
                    'SELECT v.an AN,o.icd10 DIAG,o.diagtype DXTYPE,if(d.licenseno="","-99999",d.licenseno) DRDX
                        FROM an_stat v
                        LEFT OUTER JOIN iptdiag o on o.an = v.an
                        LEFT OUTER JOIN doctor d on d.`code` = o.doctor
                        LEFT OUTER JOIN ipt ip on ip.an = v.an
                        INNER JOIN icd101 i on i.code = o.icd10
                        WHERE ip.vn IN("'.$va1->vn.'")
                ');
                foreach ($data_idx_ as $va_08) {
                    D_idx::insert([
                        'AN'                => $va_08->AN,
                        'DIAG'              => $va_08->DIAG,
                        'DXTYPE'            => $va_08->DXTYPE,
                        'DRDX'              => $va_08->DRDX,
                        'user_id'           => $iduser,
                        'd_anaconda_id'     => 'OFC_401'
                    ]);

                }
                //D_iop OK
                $data_iop_ = DB::connection('mysql2')->select(
                    'SELECT a.an AN,o.icd9 OPER,o.oper_type as OPTYPE,if(d.licenseno="","-99999",d.licenseno) DROPID,DATE_FORMAT(o.opdate,"%Y%m%d") DATEIN,Time_format(o.optime,"%H%i") TIMEIN
                        ,DATE_FORMAT(o.enddate,"%Y%m%d") DATEOUT,Time_format(o.endtime,"%H%i") TIMEOUT
                        FROM an_stat a
                        LEFT OUTER JOIN iptoprt o on o.an = a.an
                        LEFT OUTER JOIN doctor d on d.`code` = o.doctor
                        INNER JOIN icd9cm1 i on i.code = o.icd9
                        LEFT OUTER JOIN ipt ip on ip.an = a.an
                        WHERE ip.vn IN("'.$va1->vn.'")
                ');
                foreach ($data_iop_ as $va_09) {
                    D_iop::insert([
                        'AN'                => $va_09->AN,
                        'OPER'              => $va_09->OPER,
                        'OPTYPE'            => $va_09->OPTYPE,
                        'DROPID'            => $va_09->DROPID,
                        'DATEIN'            => $va_09->DATEIN,
                        'TIMEIN'            => $va_09->TIMEIN,
                        'DATEOUT'           => $va_09->DATEOUT,
                        'TIMEOUT'           => $va_09->TIMEOUT,
                        'user_id'           => $iduser,
                        'd_anaconda_id'     => 'OFC_401'
                    ]);
                }
                //D_cht OK
                $data_cht_ = DB::connection('mysql2')->select(
                        'SELECT o.hn HN,o.an AN,DATE_FORMAT(if(a.an is null,o.vstdate,a.dchdate),"%Y%m%d") DATE,round(if(a.an is null,vv.income,a.income),2) TOTAL,""OPD_MEMO,""INVOICE_NO,""INVOICE_LT
                        ,round(if(a.an is null,vv.paid_money,a.paid_money),2) PAID,if(vv.paid_money >"0" or a.paid_money >"0","10",pt.pcode) PTTYPE,pp.cid PERSON_ID ,o.vn SEQ
                        FROM ovst o
                        LEFT OUTER JOIN vn_stat vv on vv.vn = o.vn
                        LEFT OUTER JOIN an_stat a on a.an = o.an
                        LEFT OUTER JOIN patient pp on pp.hn = o.hn
                        LEFT OUTER JOIN pttype pt on pt.pttype = vv.pttype or pt.pttype=a.pttype
                        LEFT OUTER JOIN pttype p on p.pttype = a.pttype
                        WHERE o.vn IN("'.$va1->vn.'")
                ');
                foreach ($data_cht_ as $va_10) {
                    D_cht::insert([
                        'HN'                => $va_10->HN,
                        'AN'                => $va_10->AN,
                        'DATE'              => $va_10->DATE,
                        'TOTAL'             => $va_10->TOTAL,
                        'PAID'              => $va_10->PAID,
                        'PTTYPE'            => $va_10->PTTYPE,
                        'PERSON_ID'         => $va_10->PERSON_ID,
                        'SEQ'               => $va_10->SEQ,
                        'OPD_MEMO'          => $va_10->OPD_MEMO,
                        'INVOICE_NO'        => $va_10->INVOICE_NO,
                        'INVOICE_LT'        => $va_10->INVOICE_LT,
                        'user_id'           => $iduser,
                        'd_anaconda_id'     => 'OFC_401'
                    ]);
                }
                //D_cha OK
                $data_cha_ = DB::connection('mysql2')->select(
                    'SELECT v.hn HN,if(v1.an is null,"",v1.an) AN ,if(v1.an is null,DATE_FORMAT(v.vstdate,"%Y%m%d"),DATE_FORMAT(v1.dchdate,"%Y%m%d")) DATE
                        ,if(v.paidst in("01","03"),dx.chrgitem_code2,dc.chrgitem_code1) CHRGITEM,round(sum(v.sum_price),2) AMOUNT,p.cid PERSON_ID ,ifnull(v.vn,v.an) SEQ
                        FROM opitemrece v
                        LEFT OUTER JOIN vn_stat vv on vv.vn = v.vn
                        LEFT OUTER JOIN patient p on p.hn = v.hn
                        LEFT OUTER JOIN ipt v1 on v1.an = v.an
                        LEFT OUTER JOIN income i on v.income=i.income
                        LEFT OUTER JOIN drg_chrgitem dc on i.drg_chrgitem_id=dc.drg_chrgitem_id
                        LEFT OUTER JOIN drg_chrgitem dx on i.drg_chrgitem_id= dx.drg_chrgitem_id
                        WHERE v.vn IN("'.$va1->vn.'")
                        GROUP BY v.vn,CHRGITEM
                        UNION ALL
                        SELECT v.hn HN,ip.an AN ,if(ip.an is null,DATE_FORMAT(v.vstdate,"%Y%m%d"),DATE_FORMAT(ip.dchdate,"%Y%m%d")) DATE
                        ,if(v.paidst in("01","03"),dx.chrgitem_code2,dc.chrgitem_code1) CHRGITEM,round(sum(v.sum_price),2) AMOUNT,p.cid PERSON_ID ,ifnull(v.vn,v.an) SEQ
                        FROM opitemrece v
                        LEFT OUTER JOIN vn_stat vv on vv.vn = v.vn
                        LEFT OUTER JOIN patient p on p.hn = v.hn
                        LEFT OUTER JOIN ipt ip on ip.an = v.an
                        LEFT OUTER JOIN income i on v.income=i.income
                        LEFT OUTER JOIN drg_chrgitem dc on i.drg_chrgitem_id=dc.drg_chrgitem_id
                        LEFT OUTER JOIN drg_chrgitem dx on i.drg_chrgitem_id= dx.drg_chrgitem_id
                        WHERE ip.vn IN("'.$va1->vn.'")
                        GROUP BY v.an,CHRGITEM;
                ');
                foreach ($data_cha_ as $va_11) {
                    D_cha::insert([
                        'HN'                => $va_11->HN,
                        'AN'                => $va_11->AN,
                        'DATE'              => $va_11->DATE,
                        'CHRGITEM'          => $va_11->CHRGITEM,
                        'AMOUNT'            => $va_11->AMOUNT,
                        'PERSON_ID'         => $va_11->PERSON_ID,
                        'SEQ'               => $va_11->SEQ,
                        'user_id'           => $iduser,
                        'd_anaconda_id'     => 'OFC_401'
                    ]);
                }
                //D_aer OK
                $data_aer_ = DB::connection('mysql2')->select(
                    'SELECT v.hn HN ,i.an AN ,DATE_FORMAT(v.vstdate,"%Y%m%d") DATEOPD
                        ,vv.claim_code AUTHAE
                        ,"" AEDATE,"" AETIME,"" AETYPE,"" REFER_NO,"" REFMAINI ,"" IREFTYPE,"" REFMAINO,"" OREFTYPE,"" UCAE,"" EMTYPE,v.vn SEQ ,"" AESTATUS,"" DALERT,"" TALERT
                        FROM vn_stat v
                        LEFT OUTER JOIN ipt i on i.vn = v.vn
                        LEFT OUTER JOIN ovst o on o.vn = v.vn
                        LEFT OUTER JOIN visit_pttype vv on vv.vn = v.vn
                        LEFT OUTER JOIN pttype pt on pt.pttype =v.pttype
                        WHERE v.vn IN("'.$va1->vn.'") and i.an is null
                        AND i.an is null
                        GROUP BY v.vn
                         UNION ALL
                        SELECT a.hn HN,a.an AN,DATE_FORMAT(vs.vstdate,"%Y%m%d") DATEOPD,"" AUTHAE
                        ,"" AEDATE,"" AETIME,"" AETYPE,"" REFER_NO,"" REFMAINI ,"" IREFTYPE,"" REFMAINO,"" OREFTYPE,"" UCAE,"" EMTYPE,"" SEQ ,"" AESTATUS,"" DALERT,"" TALERT
                        FROM an_stat a
                        LEFT OUTER JOIN ipt_pttype vv on vv.an = a.an
                        LEFT OUTER JOIN pttype pt on pt.pttype =a.pttype
                        LEFT OUTER JOIN vn_stat vs on vs.vn =a.vn
                        WHERE a.vn IN("'.$va1->vn.'")
                        GROUP BY a.an;
                ');
                foreach ($data_aer_ as $va_12) {
                    D_aer::insert([
                        'HN'                => $va_12->HN,
                        'AN'                => $va_12->AN,
                        'DATEOPD'           => $va_12->DATEOPD,
                        'AUTHAE'            => $va_12->AUTHAE,
                        'AEDATE'            => $va_12->AEDATE,
                        'AETIME'            => $va_12->AETIME,
                        'AETYPE'            => $va_12->AETYPE,
                        'REFER_NO'          => $va_12->REFER_NO,
                        'REFMAINI'          => $va_12->REFMAINI,
                        'IREFTYPE'          => $va_12->IREFTYPE,
                        'REFMAINO'          => $va_12->REFMAINO,
                        'OREFTYPE'          => $va_12->OREFTYPE,
                        'UCAE'              => $va_12->UCAE,
                        'SEQ'               => $va_12->SEQ,
                        'AESTATUS'          => $va_12->AESTATUS,
                        'DALERT'            => $va_12->DALERT,
                        'TALERT'            => $va_12->TALERT,
                        'user_id'           => $iduser,
                        'd_anaconda_id'     => 'OFC_401'
                    ]);
                }
                //D_adp
                $data_adp_ = DB::connection('mysql2')->select(
                    'SELECT HN,AN,DATEOPD,TYPE,CODE,sum(QTY) QTY,RATE,SEQ,"" CAGCODE,"" DOSE,"" CA_TYPE,""SERIALNO,"0" TOTCOPAY,""USE_STATUS,"0" TOTAL,""QTYDAY
                            ,"" TMLTCODE ,"" STATUS1 ,"" BI ,"" CLINIC ,"" ITEMSRC ,"" PROVIDER,"" GRAVIDA ,"" GA_WEEK ,"" DCIP ,"0000-00-00" LMP ,""SP_ITEM,icode ,vstdate,income,rate_new
                            FROM
                            (SELECT v.hn HN,if(v.an is null,"",v.an) AN,DATE_FORMAT(v.rxdate,"%Y%m%d") DATEOPD,n.nhso_adp_type_id TYPE,n.nhso_adp_code CODE ,sum(v.QTY) QTY,round(v.unitprice,2) RATE,if(v.an is null,v.vn,"") SEQ
                            ,"" CAGCODE,"" DOSE,"" CA_TYPE,""SERIALNO,"0" TOTCOPAY,""USE_STATUS,"0" TOTAL,""QTYDAY
                            ,"" TMLTCODE ,"" STATUS1 ,"" BI ,"" CLINIC ,"" ITEMSRC
                            ,"" PROVIDER ,"" GRAVIDA ,"" GA_WEEK ,"" DCIP ,"0000-00-00" LMP ,""SP_ITEM,v.icode,v.vstdate,v.income
                            ,(SELECT SUM(sum_price) FROM opitemrece WHERE vn = i.vn AND income ="14") as rate_new
                        FROM opitemrece v
                        JOIN nondrugitems n on n.icode = v.icode
                        LEFT OUTER JOIN ipt i on i.an = v.an
                        AND i.an is not NULL
                        WHERE i.vn IN("'.$va1->vn.'") AND v.income NOT IN("11","13","14")
                        GROUP BY i.vn,n.nhso_adp_code,rate) a
                        GROUP BY an,CODE,rate
                            UNION
                        SELECT HN,AN,DATEOPD,TYPE,CODE,sum(QTY) QTY,RATE,SEQ,"" CAGCODE,"" DOSE,"" CA_TYPE,""SERIALNO,"0" TOTCOPAY,""USE_STATUS,"0" TOTAL,""QTYDAY
                            ,"" TMLTCODE ,"" STATUS1 ,"" BI ,"" CLINIC ,"" ITEMSRC ,"" PROVIDER,"" GRAVIDA ,"" GA_WEEK ,"" DCIP ,"0000-00-00" LMP ,""SP_ITEM,icode ,vstdate,income,rate_new
                            FROM
                            (SELECT v.hn HN,if(v.an is null,"",v.an) AN,DATE_FORMAT(v.vstdate,"%Y%m%d") DATEOPD,n.nhso_adp_type_id TYPE,n.nhso_adp_code CODE ,sum(v.QTY) QTY,round(v.unitprice,2) RATE,if(v.an is null,v.vn,"") SEQ
                            ,"" CAGCODE,"" DOSE,"" CA_TYPE,""SERIALNO,"0" TOTCOPAY,""USE_STATUS,"0" TOTAL,""QTYDAY,"" TMLTCODE ,"" STATUS1 ,"" BI ,"" CLINIC ,"" ITEMSRC ,"" PROVIDER,"" GRAVIDA ,"" GA_WEEK ,"" DCIP ,"0000-00-00" LMP
                            ,""SP_ITEM,v.icode,v.vstdate,v.income
                            ,(SELECT SUM(sum_price) FROM opitemrece WHERE vn = vv.vn AND income ="14") as rate_new
                        FROM opitemrece v
                        JOIN nondrugitems n on n.icode = v.icode
                        LEFT OUTER JOIN vn_stat vv on vv.vn = v.vn
                        WHERE vv.vn IN("'.$va1->vn.'") AND v.income NOT IN("11","13","14")
                        AND v.an is NULL
                        GROUP BY vv.vn,n.nhso_adp_code,rate) b
                        GROUP BY seq,CODE,rate;
                ');

                foreach ($data_adp_ as $va_13) {
                    if ($va_13->RATE > 0 && $va_13->QTY > 0) {
                        D_adp::insert([
                            'HN'                   => $va_13->HN,
                            'AN'                   => $va_13->AN,
                            'DATEOPD'              => $va_13->DATEOPD,
                            'TYPE'                 => $va_13->TYPE,
                            'CODE'                 => $va_13->CODE,
                            'QTY'                  => $va_13->QTY,
                            'RATE'                 => $va_13->RATE,
                            'SEQ'                  => $va_13->SEQ,
                            'CAGCODE'              => $va_13->CAGCODE,
                            'DOSE'                 => $va_13->DOSE,
                            'CA_TYPE'              => $va_13->CA_TYPE,
                            'SERIALNO'             => $va_13->SERIALNO,
                            'TOTCOPAY'             => $va_13->TOTCOPAY,
                            'USE_STATUS'           => $va_13->USE_STATUS,
                            'TOTAL'                => $va_13->TOTAL,
                            'QTYDAY'               => $va_13->QTYDAY,
                            'TMLTCODE'             => $va_13->TMLTCODE,
                            'STATUS1'              => $va_13->STATUS1,
                            'BI'                   => $va_13->BI,
                            'CLINIC'               => $va_13->CLINIC,
                            'ITEMSRC'              => $va_13->ITEMSRC,
                            'PROVIDER'             => $va_13->PROVIDER,
                            'GRAVIDA'              => $va_13->GRAVIDA,
                            'GA_WEEK'              => $va_13->GA_WEEK,
                            'DCIP'                 => $va_13->DCIP,
                            'LMP'                  => $va_13->LMP,
                            'SP_ITEM'              => $va_13->SP_ITEM,
                            'icode'                => $va_13->icode,
                            'vstdate'              => $va_13->vstdate,
                            'user_id'              => $iduser,
                            'd_anaconda_id'        => 'OFC_401'
                        ]);
                    } else {
                        # code...
                    }
                }
                //D_adp 20-ค่าบริการทางกายภาพบำบัดและเวชกรรมฟื้นฟู
                $data_adp_kay = DB::connection('mysql2')->select(
                    'SELECT HN,AN,DATEOPD,TYPE,CODE,sum(QTY) QTY,RATE,SEQ,"" CAGCODE,"" DOSE,"" CA_TYPE,""SERIALNO,"0" TOTCOPAY,""USE_STATUS,"0" TOTAL,""QTYDAY
                            ,"" TMLTCODE ,"" STATUS1 ,"" BI ,"" CLINIC ,"" ITEMSRC ,"" PROVIDER,"" GRAVIDA ,"" GA_WEEK ,"" DCIP ,"0000-00-00" LMP ,""SP_ITEM,icode ,vstdate,income,rate_new
                            FROM
                            (SELECT v.hn HN,if(v.an is null,"",v.an) AN,DATE_FORMAT(v.rxdate,"%Y%m%d") DATEOPD,n.nhso_adp_type_id TYPE,n.nhso_adp_code CODE ,sum(v.QTY) QTY,round(v.unitprice,2) RATE,if(v.an is null,v.vn,"") SEQ
                            ,"" CAGCODE,"" DOSE,"" CA_TYPE,""SERIALNO,"0" TOTCOPAY,""USE_STATUS,"0" TOTAL,""QTYDAY
                            ,"" TMLTCODE ,"" STATUS1 ,"" BI ,"" CLINIC ,"" ITEMSRC
                            ,"" PROVIDER ,"" GRAVIDA ,"" GA_WEEK ,"" DCIP ,"0000-00-00" LMP ,""SP_ITEM,v.icode,v.vstdate,v.income
                            ,(SELECT SUM(sum_price) FROM opitemrece WHERE vn = i.vn AND income ="14") as rate_new
                        FROM opitemrece v
                        JOIN nondrugitems n on n.icode = v.icode
                        LEFT OUTER JOIN ipt i on i.an = v.an
                        AND i.an is not NULL
                        WHERE i.vn IN("'.$va1->vn.'") AND v.income IN("14")
                        GROUP BY i.vn) a
                        GROUP BY an,CODE,rate
                            UNION
                        SELECT HN,AN,DATEOPD,TYPE,CODE,sum(QTY) QTY,RATE,SEQ,"" CAGCODE,"" DOSE,"" CA_TYPE,""SERIALNO,"0" TOTCOPAY,""USE_STATUS,"0" TOTAL,""QTYDAY
                            ,"" TMLTCODE ,"" STATUS1 ,"" BI ,"" CLINIC ,"" ITEMSRC ,"" PROVIDER,"" GRAVIDA ,"" GA_WEEK ,"" DCIP ,"0000-00-00" LMP ,""SP_ITEM,icode ,vstdate,income,rate_new
                            FROM
                            (SELECT v.hn HN,if(v.an is null,"",v.an) AN,DATE_FORMAT(v.vstdate,"%Y%m%d") DATEOPD,n.nhso_adp_type_id TYPE,n.nhso_adp_code CODE ,sum(v.QTY) QTY,round(v.unitprice,2) RATE,if(v.an is null,v.vn,"") SEQ
                            ,"" CAGCODE,"" DOSE,"" CA_TYPE,""SERIALNO,"0" TOTCOPAY,""USE_STATUS,"0" TOTAL,""QTYDAY,"" TMLTCODE ,"" STATUS1 ,"" BI ,"" CLINIC ,"" ITEMSRC ,"" PROVIDER,"" GRAVIDA ,"" GA_WEEK ,"" DCIP ,"0000-00-00" LMP
                            ,""SP_ITEM,v.icode,v.vstdate,v.income
                            ,(SELECT SUM(sum_price) FROM opitemrece WHERE vn = vv.vn AND income ="14") as rate_new
                        FROM opitemrece v
                        JOIN nondrugitems n on n.icode = v.icode
                        LEFT OUTER JOIN vn_stat vv on vv.vn = v.vn
                        WHERE vv.vn IN("'.$va1->vn.'") AND v.income IN("14")
                        AND v.an is NULL
                        GROUP BY vv.vn) b
                        GROUP BY seq,CODE,rate;
                ');
                foreach ($data_adp_kay as $va_20) {
                        D_adp::insert([
                            'HN'                   => $va_20->HN,
                            'AN'                   => $va_20->AN,
                            'DATEOPD'              => $va_20->DATEOPD,
                            'TYPE'                 => '20',
                            'CODE'                 => 'XXX14',
                            'QTY'                  => '1',
                            'RATE'                 => $va_20->rate_new,
                            'SEQ'                  => $va_20->SEQ,
                            'CAGCODE'              => $va_20->CAGCODE,
                            'DOSE'                 => $va_20->DOSE,
                            'CA_TYPE'              => $va_20->CA_TYPE,
                            'SERIALNO'             => $va_20->SERIALNO,
                            'TOTCOPAY'             => $va_20->TOTCOPAY,
                            'USE_STATUS'           => $va_20->USE_STATUS,
                            'TOTAL'                => $va_20->TOTAL,
                            'QTYDAY'               => $va_20->QTYDAY,
                            'TMLTCODE'             => $va_20->TMLTCODE,
                            'STATUS1'              => $va_20->STATUS1,
                            'BI'                   => $va_20->BI,
                            'CLINIC'               => $va_20->CLINIC,
                            'ITEMSRC'              => $va_20->ITEMSRC,
                            'PROVIDER'             => $va_20->PROVIDER,
                            'GRAVIDA'              => $va_20->GRAVIDA,
                            'GA_WEEK'              => $va_20->GA_WEEK,
                            'DCIP'                 => $va_20->DCIP,
                            'LMP'                  => $va_20->LMP,
                            'SP_ITEM'              => $va_20->SP_ITEM,
                            'icode'                => $va_20->icode,
                            'vstdate'              => $va_20->vstdate,
                            'user_id'              => $iduser,
                            'd_anaconda_id'        => 'OFC_401'
                        ]);
                }
                 //D_adp ทันตกรรม
                 $data_adp_dent = DB::connection('mysql2')->select(
                    'SELECT HN,AN,DATEOPD,TYPE,CODE,sum(QTY) QTY,RATE,SEQ,"" CAGCODE,"" DOSE,"" CA_TYPE,""SERIALNO,"0" TOTCOPAY,""USE_STATUS,"0" TOTAL,""QTYDAY
                            ,"" TMLTCODE ,"" STATUS1 ,"" BI ,"" CLINIC ,"" ITEMSRC ,"" PROVIDER,"" GRAVIDA ,"" GA_WEEK ,"" DCIP ,"0000-00-00" LMP ,""SP_ITEM,icode ,vstdate,income,rate_new
                            FROM
                            (SELECT v.hn HN,if(v.an is null,"",v.an) AN,DATE_FORMAT(v.rxdate,"%Y%m%d") DATEOPD,n.nhso_adp_type_id TYPE,n.nhso_adp_code CODE ,sum(v.QTY) QTY,round(v.unitprice,2) RATE,if(v.an is null,v.vn,"") SEQ
                            ,"" CAGCODE,"" DOSE,"" CA_TYPE,""SERIALNO,"0" TOTCOPAY,""USE_STATUS,"0" TOTAL,""QTYDAY
                            ,"" TMLTCODE ,"" STATUS1 ,"" BI ,"" CLINIC ,"" ITEMSRC
                            ,"" PROVIDER ,"" GRAVIDA ,"" GA_WEEK ,"" DCIP ,"0000-00-00" LMP ,""SP_ITEM,v.icode,v.vstdate,v.income
                            ,(SELECT SUM(sum_price) FROM opitemrece WHERE vn = i.vn AND income ="13") as rate_new
                        FROM opitemrece v
                        JOIN nondrugitems n on n.icode = v.icode
                        LEFT OUTER JOIN ipt i on i.an = v.an
                        AND i.an is not NULL
                        WHERE i.vn IN("'.$va1->vn.'") AND v.income IN("13")
                        GROUP BY i.vn,n.nhso_adp_code,rate) a
                        GROUP BY an,CODE,rate
                            UNION
                        SELECT HN,AN,DATEOPD,TYPE,CODE,sum(QTY) QTY,RATE,SEQ,"" CAGCODE,"" DOSE,"" CA_TYPE,""SERIALNO,"0" TOTCOPAY,""USE_STATUS,"0" TOTAL,""QTYDAY
                            ,"" TMLTCODE ,"" STATUS1 ,"" BI ,"" CLINIC ,"" ITEMSRC ,"" PROVIDER,"" GRAVIDA ,"" GA_WEEK ,"" DCIP ,"0000-00-00" LMP ,""SP_ITEM,icode ,vstdate,income,rate_new
                            FROM
                            (SELECT v.hn HN,if(v.an is null,"",v.an) AN,DATE_FORMAT(v.vstdate,"%Y%m%d") DATEOPD,n.nhso_adp_type_id TYPE,n.nhso_adp_code CODE ,sum(v.QTY) QTY,round(v.unitprice,2) RATE,if(v.an is null,v.vn,"") SEQ
                            ,"" CAGCODE,"" DOSE,"" CA_TYPE,""SERIALNO,"0" TOTCOPAY,""USE_STATUS,"0" TOTAL,""QTYDAY,"" TMLTCODE ,"" STATUS1 ,"" BI ,"" CLINIC ,"" ITEMSRC ,"" PROVIDER,"" GRAVIDA ,"" GA_WEEK ,"" DCIP ,"0000-00-00" LMP
                            ,""SP_ITEM,v.icode,v.vstdate,v.income
                            ,(SELECT SUM(sum_price) FROM opitemrece WHERE vn = vv.vn AND income ="13") as rate_new
                        FROM opitemrece v
                        JOIN nondrugitems n on n.icode = v.icode
                        LEFT OUTER JOIN vn_stat vv on vv.vn = v.vn
                        WHERE vv.vn IN("'.$va1->vn.'") AND v.income IN("13")
                        AND v.an is NULL
                        GROUP BY vv.vn,n.nhso_adp_code,rate) b
                        GROUP BY seq,CODE,rate;
                ');
                foreach ($data_adp_dent as $va_21) {
                    D_adp::insert([
                        'HN'                   => $va_21->HN,
                        'AN'                   => $va_21->AN,
                        'DATEOPD'              => $va_21->DATEOPD,
                        'TYPE'                 => $va_21->TYPE,
                        'CODE'                 => $va_21->CODE,
                        'QTY'                  => $va_21->QTY,
                        'RATE'                 => $va_21->RATE,
                        'SEQ'                  => $va_21->SEQ,
                        'CAGCODE'              => $va_21->CAGCODE,
                        'DOSE'                 => $va_21->DOSE,
                        'CA_TYPE'              => $va_21->CA_TYPE,
                        'SERIALNO'             => $va_21->SERIALNO,
                        'TOTCOPAY'             => $va_21->TOTCOPAY,
                        'USE_STATUS'           => $va_21->USE_STATUS,
                        'TOTAL'                => $va_21->TOTAL,
                        'QTYDAY'               => $va_21->QTYDAY,
                        'TMLTCODE'             => $va_21->TMLTCODE,
                        'STATUS1'              => $va_21->STATUS1,
                        'BI'                   => $va_21->BI,
                        'CLINIC'               => $va_21->CLINIC,
                        'ITEMSRC'              => $va_21->ITEMSRC,
                        'PROVIDER'             => $va_21->PROVIDER,
                        'GRAVIDA'              => $va_21->GRAVIDA,
                        'GA_WEEK'              => $va_21->GA_WEEK,
                        'DCIP'                 => $va_21->DCIP,
                        'LMP'                  => $va_21->LMP,
                        'SP_ITEM'              => $va_21->SP_ITEM,
                        'icode'                => $va_21->icode,
                        'vstdate'              => $va_21->vstdate,
                        'user_id'              => $iduser,
                        'd_anaconda_id'        => 'OFC_401'
                    ]);
                }

                //D_adp สปสชเป็น type 19-ค่าหัตถการและวิสัญญี  ใน hosเป็น income = 11
            $data_adp_visanyee = DB::connection('mysql2')->select(
                'SELECT HN,AN,DATEOPD,TYPE,CODE,sum(QTY) QTY,RATE,SEQ,"" CAGCODE,"" DOSE,"" CA_TYPE,""SERIALNO,"0" TOTCOPAY,""USE_STATUS,"0" TOTAL,""QTYDAY
                        ,"" TMLTCODE ,"" STATUS1 ,"" BI ,"" CLINIC ,"" ITEMSRC ,"" PROVIDER,"" GRAVIDA ,"" GA_WEEK ,"" DCIP ,"0000-00-00" LMP ,""SP_ITEM,icode ,vstdate,income,rate_new,billcode
                        FROM
                        (SELECT v.hn HN,if(v.an is null,"",v.an) AN,DATE_FORMAT(v.rxdate,"%Y%m%d") DATEOPD,n.nhso_adp_type_id TYPE,n.nhso_adp_code CODE ,v.QTY QTY,round(v.unitprice,2) RATE,if(v.an is null,v.vn,"") SEQ
                        ,"" CAGCODE,"" DOSE,"" CA_TYPE,""SERIALNO,"0" TOTCOPAY,""USE_STATUS,"0" TOTAL,""QTYDAY
                        ,"" TMLTCODE ,"" STATUS1 ,"" BI ,"" CLINIC ,"" ITEMSRC
                        ,"" PROVIDER ,"" GRAVIDA ,"" GA_WEEK ,"" DCIP ,"0000-00-00" LMP ,""SP_ITEM,v.icode,v.vstdate,v.income
                        ,(SELECT SUM(sum_price) FROM opitemrece WHERE an = i.an AND income ="11") as rate_new,n.billcode
                    FROM opitemrece v
                    JOIN nondrugitems n on n.icode = v.icode
                    LEFT OUTER JOIN ipt i on i.an = v.an
                    AND i.an is not NULL
                    WHERE i.vn IN("'.$va1->vn.'") AND v.income IN("11")) a
                    GROUP BY an,CODE,rate
                        UNION
                    SELECT HN,AN,DATEOPD,TYPE,CODE,sum(QTY) QTY,RATE,SEQ,"" CAGCODE,"" DOSE,"" CA_TYPE,""SERIALNO,"0" TOTCOPAY,""USE_STATUS,"0" TOTAL,""QTYDAY
                        ,"" TMLTCODE ,"" STATUS1 ,"" BI ,"" CLINIC ,"" ITEMSRC ,"" PROVIDER,"" GRAVIDA ,"" GA_WEEK ,"" DCIP ,"0000-00-00" LMP ,""SP_ITEM,icode ,vstdate,income,rate_new,billcode
                        FROM
                        (SELECT v.hn HN,if(v.an is null,"",v.an) AN,DATE_FORMAT(v.vstdate,"%Y%m%d") DATEOPD,n.nhso_adp_type_id TYPE,n.nhso_adp_code CODE ,v.QTY QTY,round(v.unitprice,2) RATE,if(v.an is null,v.vn,"") SEQ
                        ,"" CAGCODE,"" DOSE,"" CA_TYPE,""SERIALNO,"0" TOTCOPAY,""USE_STATUS,"0" TOTAL,""QTYDAY,"" TMLTCODE ,"" STATUS1 ,"" BI ,"" CLINIC ,"" ITEMSRC ,"" PROVIDER,"" GRAVIDA ,"" GA_WEEK ,"" DCIP ,"0000-00-00" LMP
                        ,""SP_ITEM,v.icode,v.vstdate,v.income
                        ,(SELECT SUM(sum_price) FROM opitemrece WHERE vn = vv.vn AND income ="11") as rate_new,n.billcode
                    FROM opitemrece v
                    JOIN nondrugitems n on n.icode = v.icode
                    LEFT OUTER JOIN vn_stat vv on vv.vn = v.vn
                    WHERE vv.vn IN("'.$va1->vn.'") AND v.income IN("11")
                    AND v.an is NULL) b
                    GROUP BY seq,CODE,rate;
            ');
            foreach ($data_adp_visanyee as $va_22) {
                    D_adp::insert([
                        'HN'                   => $va_22->HN,
                        'AN'                   => $va_22->AN,
                        'DATEOPD'              => $va_22->DATEOPD,
                        'TYPE'                 => $va_22->TYPE,
                        // 'TYPE'                 => '19',
                        'CODE'                 => $va_22->billcode,
                        'QTY'                  => $va_22->QTY,
                        'RATE'                 => $va_22->RATE,
                        'SEQ'                  => $va_22->SEQ,
                        'CAGCODE'              => $va_22->CAGCODE,
                        'DOSE'                 => $va_22->DOSE,
                        'CA_TYPE'              => $va_22->CA_TYPE,
                        'SERIALNO'             => $va_22->SERIALNO,
                        'TOTCOPAY'             => $va_22->TOTCOPAY,
                        'USE_STATUS'           => $va_22->USE_STATUS,
                        'TOTAL'                => $va_22->TOTAL,
                        'QTYDAY'               => $va_22->QTYDAY,
                        'TMLTCODE'             => $va_22->TMLTCODE,
                        'STATUS1'              => $va_22->STATUS1,
                        'BI'                   => $va_22->BI,
                        'CLINIC'               => $va_22->CLINIC,
                        'ITEMSRC'              => $va_22->ITEMSRC,
                        'PROVIDER'             => $va_22->PROVIDER,
                        'GRAVIDA'              => $va_22->GRAVIDA,
                        'GA_WEEK'              => $va_22->GA_WEEK,
                        'DCIP'                 => $va_22->DCIP,
                        'LMP'                  => $va_22->LMP,
                        'SP_ITEM'              => $va_22->SP_ITEM,
                        'icode'                => $va_22->icode,
                        'vstdate'              => $va_22->vstdate,
                        'user_id'              => $iduser,
                        'd_anaconda_id'        => 'OFC_401'
                    ]);
            }

                //D_dru OK
                 $data_dru_ = DB::connection('mysql2')->select('
                    SELECT vv.hcode HCODE ,v.hn HN ,v.an AN ,vv.spclty CLINIC ,vv.cid PERSON_ID ,DATE_FORMAT(v.vstdate,"%Y%m%d") DATE_SERV
                    ,d.icode DID ,concat(d.`name`," ",d.strength," ",d.units) DIDNAME ,sum(v.qty) AMOUNT,round(v.unitprice,2) DRUGPRICE
                    ,"0.00" DRUGCOST ,d.did DIDSTD ,d.units UNIT ,concat(d.packqty,"x",d.units) UNIT_PACK ,v.vn SEQ
                    ,if(v.income="17",oo.presc_reason,"") as DRUGREMARK,"" PA_NO ,"" TOTCOPAY ,if(v.item_type="H","2","1") USE_STATUS
                    ,"" TOTAL ,"" as SIGCODE ,"" as SIGTEXT ,"" PROVIDER,v.vstdate
                    FROM opitemrece v
                    LEFT OUTER JOIN drugitems d on d.icode = v.icode
                    LEFT OUTER JOIN vn_stat vv on vv.vn = v.vn
                    LEFT OUTER JOIN ovst_presc_ned oo on oo.vn = v.vn and oo.icode=v.icode
                    LEFT OUTER JOIN drugitems_ned_reason dn on dn.icode = v.icode
                    WHERE v.vn IN("'.$va1->vn.'")
                    AND d.did is not null
                    GROUP BY v.vn,did

                    UNION all

                    SELECT pt.hcode HCODE ,v.hn HN ,v.an AN ,v1.spclty CLINIC ,pt.cid PERSON_ID ,DATE_FORMAT((v.vstdate),"%Y%m%d") DATE_SERV
                    ,d.icode DID ,concat(d.`name`," ",d.strength," ",d.units) DIDNAME ,sum(v.qty) AMOUNT ,round(v.unitprice,2) DRUGPRICE
                    ,"0.00" DRUGCOST ,d.did DIDSTD ,d.units UNIT ,concat(d.packqty,"x",d.units) UNIT_PACK ,v.vn SEQ
                    ,if(v.income="17",oo.presc_reason,"") as DRUGREMARK,"" PA_NO ,"" TOTCOPAY ,if(v.item_type="H","2","1") USE_STATUS
                    ,"" TOTAL,"" as SIGCODE,"" as SIGTEXT,""  PROVIDER,v.vstdate
                    FROM opitemrece v
                    LEFT OUTER JOIN drugitems d on d.icode = v.icode
                    LEFT OUTER JOIN patient pt  on v.hn = pt.hn
                    INNER JOIN ipt v1 on v1.an = v.an
                    LEFT OUTER JOIN ovst_presc_ned oo on oo.vn = v.vn and oo.icode=v.icode
                    LEFT OUTER JOIN drugitems_ned_reason dn on dn.icode = v.icode
                    WHERE v1.vn IN("'.$va1->vn.'")
                    AND d.did is not null AND v.qty<>"0"
                    GROUP BY v.an,d.icode,USE_STATUS;
                ');

                foreach ($data_dru_ as $va_14) {
                    if ($va_14->AMOUNT < 1) {
                        # code...
                    } else {
                        D_dru::insert([
                            'HN'             => $va_14->HN,
                            'CLINIC'         => $va_14->CLINIC,
                            'HCODE'          => $va_14->HCODE,
                            'AN'             => $va_14->AN,
                            'PERSON_ID'      => $va_14->PERSON_ID,
                            'DATE_SERV'      => $va_14->DATE_SERV,
                            'DID'            => $va_14->DID,
                            'DIDNAME'        => $va_14->DIDNAME,
                            'AMOUNT'         => $va_14->AMOUNT,
                            'DRUGPRICE'       => $va_14->DRUGPRICE,
                            'DRUGCOST'       => $va_14->DRUGCOST,
                            'DIDSTD'         => $va_14->DIDSTD,
                            'UNIT'           => $va_14->UNIT,
                            'UNIT_PACK'      => $va_14->UNIT_PACK,
                            'SEQ'            => $va_14->SEQ,
                            'DRUGREMARK'     => $va_14->DRUGREMARK,
                            'PA_NO'          => $va_14->PA_NO,
                            'TOTCOPAY'       => $va_14->TOTCOPAY,
                            'USE_STATUS'     => $va_14->USE_STATUS,
                            'TOTAL'          => $va_14->TOTAL,
                            'SIGCODE'        => $va_14->SIGCODE,
                            'SIGTEXT'        => $va_14->SIGTEXT,
                            'PROVIDER'       => $va_14->PROVIDER,
                            'vstdate'        => $va_14->vstdate,
                            'user_id'        => $iduser,
                            'd_anaconda_id'  => 'OFC_401'
                        ]);
                    }


                }

         }

        //  D_adp::where('CODE','=','XXXXXX')->delete();

        #delete file in folder ทั้งหมด
        $file = new Filesystem;
        $file->cleanDirectory('Export_OFC'); //ทั้งหมด
        // $file->cleanDirectory('Export_OFC'); //ทั้งหมด

         $dataexport_ = DB::connection('mysql')->select('SELECT folder_name from fdh_sesion where d_anaconda_id = "OFC_401"');
        foreach ($dataexport_ as $key => $v_export) {
            $folder = $v_export->folder_name;
        }
        mkdir ('Export_OFC/'.$folder, 0777, true);  //Web
        header("Content-type: text/txt");
        header("Cache-Control: no-store, no-cache");
        header('Content-Disposition: attachment; filename="content.txt"');

         //********** 1 ins.txt *****************//
         $file_d_ins       = "Export_OFC/".$folder."/INS.txt";
         $objFopen_opd_ins = fopen($file_d_ins, 'w');
         $opd_head_ins     = 'HN|INSCL|SUBTYPE|CID|DATEIN|DATEEXP|HOSPMAIN|HOSPSUB|GOVCODE|GOVNAME|PERMITNO|DOCNO|OWNRPID|OWNNAME|AN|SEQ|SUBINSCL|RELINSCL|HTYPE';
         fwrite($objFopen_opd_ins, $opd_head_ins);
         $ins = DB::connection('mysql')->select('SELECT * from d_ins where d_anaconda_id = "OFC_401"');
         foreach ($ins as $key => $value1) {
             $a1 = $value1->HN;
             $a2 = $value1->INSCL;
             $a3 = $value1->SUBTYPE;
             $a4 = $value1->CID;
             $a5 = $value1->DATEIN;
             $a6 = $value1->DATEEXP;
             $a7 = $value1->HOSPMAIN;
             $a8 = $value1->HOSPSUB;
             $a9 = $value1->GOVCODE;
             $a10 = $value1->GOVNAME;
             $a11 = $value1->PERMITNO;
             $a12 = $value1->DOCNO;
             $a13 = $value1->OWNRPID;
             $a14 = $value1->OWNNAME;
             $a15 = $value1->AN;
             $a16 = $value1->SEQ;
             $a17 = $value1->SUBINSCL;
             $a18 = $value1->RELINSCL;
             $a19 = $value1->HTYPE;
             $strText_ins ="\n".$a1."|".$a2."|".$a3."|".$a4."|".$a5."|".$a6."|".$a7."|".$a8."|".$a9."|".$a10."|".$a11."|".$a12."|".$a13."|".$a14."|".$a15."|".$a16."|".$a17."|".$a18."|".$a19;
             $ansitxt_ins = iconv('UTF-8', 'UTF-8', $strText_ins);
             fwrite($objFopen_opd_ins, $ansitxt_ins);
         }
         fclose($objFopen_opd_ins);
         Dapi_ins::truncate();
        // Dapi_ins::where('claim','=','OFC_401')->delete();
         $fread_file_ins         = fread(fopen($file_d_ins,"r"),filesize($file_d_ins));
         $fread_file_ins_endcode = base64_encode($fread_file_ins);
         $read_file_ins_size     = filesize($file_d_ins);
         Dapi_ins::insert([
            'blobName'   =>  'INS.txt',
            'blobType'   =>  'text/plain',
            'blob'       =>   $fread_file_ins_endcode,
            'size'       =>   $read_file_ins_size,
            'encoding'   =>  'UTF-8',
            'claim'      =>  'OFC_401'
        ]);

        //**********2 pat.txt ******************//
        $file_pat         = "Export_OFC/".$folder."/PAT.txt";
        $objFopen_opd_pat = fopen($file_pat, 'w');
        $opd_head_pat     = 'HCODE|HN|CHANGWAT|AMPHUR|DOB|SEX|MARRIAGE|OCCUPA|NATION|PERSON_ID|NAMEPAT|TITLE|FNAME|LNAME|IDTYPE';
        fwrite($objFopen_opd_pat, $opd_head_pat);
        $pat = DB::connection('mysql')->select('SELECT * from d_pat where d_anaconda_id = "OFC_401"');
        foreach ($pat as $key => $value2) {
            $i1  = $value2->HCODE;
            $i2  = $value2->HN;
            $i3  = $value2->CHANGWAT;
            $i4  = $value2->AMPHUR;
            $i5  = $value2->DOB;
            $i6  = $value2->SEX;
            $i7  = $value2->MARRIAGE;
            $i8  = $value2->OCCUPA;
            $i9  = $value2->NATION;
            $i10 = $value2->PERSON_ID;
            $i11 = $value2->NAMEPAT;
            $i12 = $value2->TITLE;
            $i13 = $value2->FNAME;
            $i14 = $value2->LNAME;
            $i15 = $value2->IDTYPE;
            $strText_pat     ="\n".$i1."|".$i2."|".$i3."|".$i4."|".$i5."|".$i6."|".$i7."|".$i8."|".$i9."|".$i10."|".$i11."|".$i12."|".$i13."|".$i14."|".$i15;
            $ansitxt_pat_pat = iconv('UTF-8', 'UTF-8', $strText_pat);
            fwrite($objFopen_opd_pat, $ansitxt_pat_pat);
        }
        fclose($objFopen_opd_pat);
        Dapi_pat::truncate();
        // Dapi_pat::where('claim','=','OFC_401')->delete();
        $fread_file_pat         = fread(fopen($file_pat,"r"),filesize($file_pat));
        $fread_file_pat_endcode = base64_encode($fread_file_pat);
        $read_file_pat_size     = filesize($file_pat);
        Dapi_pat::insert([
            'blobName'   =>  'PAT.txt',
            'blobType'   =>  'text/plain',
            'blob'       =>   $fread_file_pat_endcode,
            'size'       =>   $read_file_pat_size,
            'encoding'   =>  'UTF-8',
            'claim'      =>  'OFC_401'
        ]);

        //************ 3 opd.txt *****************//
        $file_d_opd       = "Export_OFC/".$folder."/OPD.txt";
        $objFopen_opd_opd = fopen($file_d_opd, 'w');
        $opd_head_opd     = 'HN|CLINIC|DATEOPD|TIMEOPD|SEQ|UUC|DETAIL|BTEMP|SBP|DBP|PR|RR|OPTYPE|TYPEIN|TYPEOUT';
        fwrite($objFopen_opd_opd, $opd_head_opd);
        $opd = DB::connection('mysql')->select('SELECT * from d_opd where d_anaconda_id = "OFC_401"');
        foreach ($opd as $key => $value3) {
            $o1 = $value3->HN;
            $o2 = $value3->CLINIC;
            $o3 = $value3->DATEOPD;
            $o4 = $value3->TIMEOPD;
            $o5 = $value3->SEQ;
            $o6 = $value3->UUC;
            $strText_opd     ="\n".$o1."|".$o2."|".$o3."|".$o4."|".$o5."|".$o6;
            $ansitxt_pat_opd = iconv('UTF-8', 'UTF-8', $strText_opd);
            fwrite($objFopen_opd_opd, $ansitxt_pat_opd);
        }
        fclose($objFopen_opd_opd);
        Dapi_opd::truncate();
        // Dapi_opd::where('claim','=','OFC_401')->delete();
        $fread_file_opd         = fread(fopen($file_d_opd,"r"),filesize($file_d_opd));
        $fread_file_opd_endcode = base64_encode($fread_file_opd);
        $read_file_opd_size     = filesize($file_d_opd);
        Dapi_opd::insert([
            'blobName'   =>  'OPD.txt',
            'blobType'   =>  'text/plain',
            'blob'       =>   $fread_file_opd_endcode,
            'size'       =>   $read_file_opd_size,
            'encoding'   =>  'UTF-8',
            'claim'      =>  'OFC_401'
        ]);

        //****************** 4 orf.txt **************************//
        $file_d_orf       = "Export_OFC/".$folder."/ORF.txt";
        $objFopen_opd_orf = fopen($file_d_orf, 'w');
        $opd_head_orf     = 'HN|DATEOPD|CLINIC|REFER|REFERTYPE|SEQ';
        fwrite($objFopen_opd_orf, $opd_head_orf);
        $orf = DB::connection('mysql')->select('SELECT * from d_orf where d_anaconda_id = "OFC_401"');
        foreach ($orf as $key => $value4) {
            $p1 = $value4->HN;
            $p2 = $value4->DATEOPD;
            $p3 = $value4->CLINIC;
            $p4 = $value4->REFER;
            $p5 = $value4->REFERTYPE;
            $p6 = $value4->SEQ;
            $strText_orf     ="\n".$p1."|".$p2."|".$p3."|".$p4."|".$p5."|".$p6;
            $ansitxt_pat_orf = iconv('UTF-8', 'UTF-8', $strText_orf);
            fwrite($objFopen_opd_orf, $ansitxt_pat_orf);
        }
        fclose($objFopen_opd_orf);
        Dapi_orf::truncate();
        // Dapi_orf::where('claim','=','OFC_401')->delete();
        $fread_file_orf         = fread(fopen($file_d_orf,"r"),filesize($file_d_orf));
        $fread_file_orf_endcode = base64_encode($fread_file_orf);
        $read_file_orf_size     = filesize($file_d_orf);
        Dapi_orf::insert([
            'blobName'   =>  'ORF.txt',
            'blobType'   =>  'text/plain',
            'blob'       =>   $fread_file_orf_endcode,
            'size'       =>   $read_file_orf_size,
            'encoding'   =>  'UTF-8',
            'claim'      =>  'OFC_401'
        ]);

        //****************** 5 odx.txt **************************//
        $file_d_odx       = "Export_OFC/".$folder."/ODX.txt";
        $objFopen_opd_odx = fopen($file_d_odx, 'w');
        $opd_head_odx     = 'HN|DATEDX|CLINIC|DIAG|DXTYPE|DRDX|PERSON_ID|SEQ';
        fwrite($objFopen_opd_odx, $opd_head_odx);
        $odx = DB::connection('mysql')->select('SELECT * from d_odx where d_anaconda_id = "OFC_401"');
        foreach ($odx as $key => $value5) {
            $m1 = $value5->HN;
            $m2 = $value5->DATEDX;
            $m3 = $value5->CLINIC;
            $m4 = $value5->DIAG;
            $m5 = $value5->DXTYPE;
            $m6 = $value5->DRDX;
            $m7 = $value5->PERSON_ID;
            $m8 = $value5->SEQ;
            $strText_odx   ="\n".$m1."|".$m2."|".$m3."|".$m4."|".$m5."|".$m6."|".$m7."|".$m8;
            $ansitxt_odx   = iconv('UTF-8', 'UTF-8', $strText_odx);
            fwrite($objFopen_opd_odx, $ansitxt_odx);
        }
        fclose($objFopen_opd_odx);
        Dapi_odx::truncate();
        // Dapi_odx::where('claim','=','OFC_401')->delete();
        $fread_file_odx         = fread(fopen($file_d_odx,"r"),filesize($file_d_odx));
        $fread_file_odx_endcode = base64_encode($fread_file_odx);
        $read_file_odx_size     = filesize($file_d_odx);
        Dapi_odx::insert([
            'blobName'   =>  'ODX.txt',
            'blobType'   =>  'text/plain',
            'blob'       =>   $fread_file_odx_endcode,
            'size'       =>   $read_file_odx_size,
            'encoding'   =>  'UTF-8',
            'claim'      =>  'OFC_401'
        ]);

        //****************** 6.oop.txt ******************************//
        $file_d_oop       = "Export_OFC/".$folder."/OOP.txt";
        $objFopen_opd_oop = fopen($file_d_oop, 'w');
        $opd_head_oop     = 'HN|DATEOPD|CLINIC|OPER|DROPID|PERSON_ID|SEQ';
        fwrite($objFopen_opd_oop, $opd_head_oop);
        $oop = DB::connection('mysql')->select('SELECT * from d_oop where d_anaconda_id = "OFC_401"');
        foreach ($oop as $key => $value6) {
            $n1 = $value6->HN;
            $n2 = $value6->DATEOPD;
            $n3 = $value6->CLINIC;
            $n4 = $value6->OPER;
            $n5 = $value6->DROPID;
            $n6 = $value6->PERSON_ID;
            $n7 = $value6->SEQ;
            $strText_oop  ="\n".$n1."|".$n2."|".$n3."|".$n4."|".$n5."|".$n6."|".$n7;
            $ansitxt_oop  = iconv('UTF-8', 'UTF-8', $strText_oop);
            fwrite($objFopen_opd_oop, $ansitxt_oop);
        }
        fclose($objFopen_opd_oop);
        Dapi_oop::truncate();
        // Dapi_oop::where('claim','=','OFC_401')->delete();
        $fread_file_oop         = fread(fopen($file_d_oop,"r"),filesize($file_d_oop));
        $fread_file_oop_endcode = base64_encode($fread_file_oop);
        $read_file_oop_size     = filesize($file_d_oop);
        Dapi_oop::insert([
            'blobName'   =>  'OOP.txt',
            'blobType'   =>  'text/plain',
            'blob'       =>   $fread_file_oop_endcode,
            'size'       =>   $read_file_oop_size,
            'encoding'   =>  'UTF-8',
            'claim'      =>  'OFC_401'
        ]);

        //******************** 7.ipd.txt **************************//
        $file_d_ipd       = "Export_OFC/".$folder."/IPD.txt";
        $objFopen_opd_ipd = fopen($file_d_ipd, 'w');
        $opd_head_ipd     = 'HN|AN|DATEADM|TIMEADM|DATEDSC|TIMEDSC|DISCHS|DISCHT|WARDDSC|DEPT|ADM_W|UUC|SVCTYPE';
        fwrite($objFopen_opd_ipd, $opd_head_ipd);
        $ipd = DB::connection('mysql')->select('SELECT * from d_ipd where d_anaconda_id = "OFC_401"');
        foreach ($ipd as $key => $value7) {
            $j1 = $value7->HN;
            $j2 = $value7->AN;
            $j3 = $value7->DATEADM;
            $j4 = $value7->TIMEADM;
            $j5 = $value7->DATEDSC;
            $j6 = $value7->TIMEDSC;
            $j7 = $value7->DISCHS;
            $j8 = $value7->DISCHT;
            $j9 = $value7->WARDDSC;
            $j10 = $value7->DEPT;
            $j11 = $value7->ADM_W;
            $j12 = $value7->UUC;
            $j13 = $value7->SVCTYPE;
            $strText_ipd="\n".$j1."|".$j2."|".$j3."|".$j4."|".$j5."|".$j6."|".$j7."|".$j8."|".$j9."|".$j10."|".$j11."|".$j12."|".$j13;
            $ansitxt_pat_ipd = iconv('UTF-8', 'UTF-8', $strText_ipd);
            fwrite($objFopen_opd_ipd, $ansitxt_pat_ipd);
        }
        fclose($objFopen_opd_ipd);
        Dapi_ipd::truncate();
        // Dapi_ipd::where('claim','=','OFC_401')->delete();
        $fread_file_ipd         = fread(fopen($file_d_ipd,"r"),filesize($file_d_ipd));
        $fread_file_ipd_endcode = base64_encode($fread_file_ipd);
        $read_file_ipd_size     = filesize($file_d_ipd);
        Dapi_ipd::insert([
            'blobName'   =>  'IPD.txt',
            'blobType'   =>  'text/plain',
            'blob'       =>   $fread_file_ipd_endcode,
            'size'       =>   $read_file_ipd_size,
            'encoding'   =>  'UTF-8',
            'claim'      =>  'OFC_401'
        ]);

        //********************* 8.irf.txt ***************************//
        $file_d_irf       = "Export_OFC/".$folder."/IRF.txt";
        $objFopen_opd_irf = fopen($file_d_irf, 'w');
        $opd_head_irf     = 'AN|REFER|REFERTYPE';
        fwrite($objFopen_opd_irf, $opd_head_irf);
        $irf = DB::connection('mysql')->select('SELECT * from d_irf where d_anaconda_id = "OFC_401"');
        foreach ($irf as $key => $value8) {
            $k1 = $value8->AN;
            $k2 = $value8->REFER;
            $k3 = $value8->REFERTYPE;
            $strText_irf      ="\n".$k1."|".$k2."|".$k3;
            $ansitxt_pat_irf  = iconv('UTF-8', 'UTF-8', $strText_irf);
            fwrite($objFopen_opd_irf, $ansitxt_pat_irf);
        }
        fclose($objFopen_opd_irf);
        Dapi_irf::truncate();
        // Dapi_irf::where('claim','=','OFC_401')->delete();
        $fread_file_irf         = fread(fopen($file_d_irf,"r"),filesize($file_d_irf));
        $fread_file_irf_endcode = base64_encode($fread_file_irf);
        $read_file_irf_size     = filesize($file_d_irf);
        Dapi_irf::insert([
            'blobName'   =>  'IRF.txt',
            'blobType'   =>  'text/plain',
            'blob'       =>   $fread_file_irf_endcode,
            'size'       =>   $read_file_irf_size,
            'encoding'   =>  'UTF-8',
            'claim'      =>  'OFC_401'
        ]);

         //********************** 9.idx.txt ***************************//
         $file_d_idx       = "Export_OFC/".$folder."/IDX.txt";
         $objFopen_opd_idx = fopen($file_d_idx, 'w');
         $opd_head_idx     = 'AN|DIAG|DXTYPE|DRDX';
         fwrite($objFopen_opd_idx, $opd_head_idx);
         $idx = DB::connection('mysql')->select('SELECT * from d_idx where d_anaconda_id = "OFC_401"');
         foreach ($idx as $key => $value9) {
             $h1 = $value9->AN;
             $h2 = $value9->DIAG;
             $h3 = $value9->DXTYPE;
             $h4 = $value9->DRDX;
             $strText_idx     ="\n".$h1."|".$h2."|".$h3."|".$h4;
             $ansitxt_pat_idx = iconv('UTF-8', 'UTF-8', $strText_idx);
             fwrite($objFopen_opd_idx, $ansitxt_pat_idx);
         }
         fclose($objFopen_opd_idx);
        Dapi_idx::truncate();
        // Dapi_idx::where('claim','=','OFC_401')->delete();
        $fread_file_idx         = fread(fopen($file_d_idx,"r"),filesize($file_d_idx));
        $fread_file_idx_endcode = base64_encode($fread_file_idx);
        $read_file_idx_size     = filesize($file_d_idx);
        Dapi_idx::insert([
            'blobName'   =>  'IDX.txt',
            'blobType'   =>  'text/plain',
            'blob'       =>   $fread_file_idx_endcode,
            'size'       =>   $read_file_idx_size,
            'encoding'   =>  'UTF-8',
            'claim'      =>  'OFC_401'
        ]);

        //********************** 10 iop.txt ***************************//
        $file_d_iop       = "Export_OFC/".$folder."/IOP.txt";
        $objFopen_opd_iop = fopen($file_d_iop, 'w');
        $opd_head_iop     = 'AN|OPER|OPTYPE|DROPID|DATEIN|TIMEIN|DATEOUT|TIMEOUT';
        fwrite($objFopen_opd_iop, $opd_head_iop);
        $iop = DB::connection('mysql')->select('SELECT * from d_iop where d_anaconda_id = "OFC_401"');
        foreach ($iop as $key => $value10) {
            $b1 = $value10->AN;
            $b2 = $value10->OPER;
            $b3 = $value10->OPTYPE;
            $b4 = $value10->DROPID;
            $b5 = $value10->DATEIN;
            $b6 = $value10->TIMEIN;
            $b7 = $value10->DATEOUT;
            $b8 = $value10->TIMEOUT;
            $strText_iop     ="\n".$b1."|".$b2."|".$b3."|".$b4."|".$b5."|".$b6."|".$b7."|".$b8;
            $ansitxt_pat_iop = iconv('UTF-8', 'UTF-8', $strText_iop);
            fwrite($objFopen_opd_iop, $ansitxt_pat_iop);
        }
        fclose($objFopen_opd_iop);
        Dapi_iop::truncate();
        // Dapi_iop::where('claim','=','OFC_401')->delete();
        $fread_file_iop         = fread(fopen($file_d_iop,"r"),filesize($file_d_iop));
        $fread_file_iop_endcode = base64_encode($fread_file_iop);
        $read_file_iop_size     = filesize($file_d_iop);
        Dapi_iop::insert([
            'blobName'   =>  'IOP.txt',
            'blobType'   =>  'text/plain',
            'blob'       =>   $fread_file_iop_endcode,
            'size'       =>   $read_file_iop_size,
            'encoding'   =>  'UTF-8',
            'claim'      =>  'OFC_401'
        ]);

        //********************** .11 cht.txt *****************************//
        $file_d_cht       = "Export_OFC/".$folder."/CHT.txt";
        $objFopen_opd_cht = fopen($file_d_cht, 'w');
        $opd_head_cht     = 'HN|AN|DATE|TOTAL|PAID|PTTYPE|PERSON_ID|SEQ';
        fwrite($objFopen_opd_cht, $opd_head_cht);
        $cht = DB::connection('mysql')->select('SELECT * from d_cht where d_anaconda_id = "OFC_401"');
        foreach ($cht as $key => $value11) {
            $f1 = $value11->HN;
            $f2 = $value11->AN;
            $f3 = $value11->DATE;
            $f4 = $value11->TOTAL;
            $f5 = $value11->PAID;
            $f6 = $value11->PTTYPE;
            $f7 = $value11->PERSON_ID;
            $f8 = $value11->SEQ;
            $strText_cht     ="\n".$f1."|".$f2."|".$f3."|".$f4."|".$f5."|".$f6."|".$f7."|".$f8;
            $ansitxt_pat_cht = iconv('UTF-8', 'UTF-8', $strText_cht);
            fwrite($objFopen_opd_cht, $ansitxt_pat_cht);
        }
        fclose($objFopen_opd_cht);
        // Dapi_cht::where('claim','=','OFC_401')->delete();
        Dapi_cht::truncate();
        $fread_file_cht         = fread(fopen($file_d_cht,"r"),filesize($file_d_cht));
        $fread_file_cht_endcode = base64_encode($fread_file_cht);
        $read_file_cht_size     = filesize($file_d_cht);
        Dapi_cht::insert([
            'blobName'   =>  'CHT.txt',
            'blobType'   =>  'text/plain',
            'blob'       =>   $fread_file_cht_endcode,
            'size'       =>   $read_file_cht_size,
            'encoding'   =>  'UTF-8',
            'claim'      =>  'OFC_401'
        ]);

        //********************** .12 cha.txt *****************************//
        $file_d_cha       = "Export_OFC/".$folder."/CHA.txt";
        $objFopen_opd_cha = fopen($file_d_cha, 'w');
        $opd_head_cha     = 'HN|AN|DATE|CHRGITEM|AMOUNT|PERSON_ID|SEQ';
        fwrite($objFopen_opd_cha, $opd_head_cha);
        $cha = DB::connection('mysql')->select('SELECT * from d_cha where d_anaconda_id = "OFC_401"');
        foreach ($cha as $key => $value12) {
            $e1 = $value12->HN;
            $e2 = $value12->AN;
            $e3 = $value12->DATE;
            $e4 = $value12->CHRGITEM;
            $e5 = $value12->AMOUNT;
            $e6 = $value12->PERSON_ID;
            $e7 = $value12->SEQ;
            $strText_cha     ="\n".$e1."|".$e2."|".$e3."|".$e4."|".$e5."|".$e6."|".$e7;
            $ansitxt_pat_cha = iconv('UTF-8', 'UTF-8', $strText_cha);
            fwrite($objFopen_opd_cha, $ansitxt_pat_cha);
        }
        fclose($objFopen_opd_cha);
        Dapi_cha::truncate();
        // Dapi_cha::where('claim','=','OFC_401')->delete();
        $fread_file_cha         = fread(fopen($file_d_cha,"r"),filesize($file_d_cha));
        $fread_file_cha_endcode = base64_encode($fread_file_cha);
        $read_file_cha_size     = filesize($file_d_cha);
        Dapi_cha::insert([
            'blobName'   =>  'CHA.txt',
            'blobType'   =>  'text/plain',
            'blob'       =>   $fread_file_cha_endcode,
            'size'       =>   $read_file_cha_size,
            'encoding'   =>  'UTF-8',
            'claim'      =>  'OFC_401'
        ]);

        //************************ .13 aer.txt **********************************//
        $file_d_aer       = "Export_OFC/".$folder."/AER.txt";
        $objFopen_opd_aer = fopen($file_d_aer, 'w');
        $opd_head_aer     = 'HN|AN|DATEOPD|AUTHAE|AEDATE|AETIME|AETYPE|REFER_NO|REFMAINI|IREFTYPE|REFMAINO|OREFTYPE|UCAE|EMTYPE|SEQ|AESTATUS|DALERT|TALERT';
        fwrite($objFopen_opd_aer, $opd_head_aer);
        $aer = DB::connection('mysql')->select('SELECT * from d_aer where d_anaconda_id = "OFC_401"');
        foreach ($aer as $key => $value13) {
            $d1  = $value13->HN;
            $d2  = $value13->AN;
            $d3  = $value13->DATEOPD;
            $d4  = $value13->AUTHAE;
            $d5  = $value13->AEDATE;
            $d6  = $value13->AETIME;
            $d7  = $value13->AETYPE;
            $d8  = $value13->REFER_NO;
            $d9  = $value13->REFMAINI;
            $d10 = $value13->IREFTYPE;
            $d11 = $value13->REFMAINO;
            $d12 = $value13->OREFTYPE;
            $d13 = $value13->UCAE;
            $d14 = $value13->EMTYPE;
            $d15 = $value13->SEQ;
            $d16 = $value13->AESTATUS;
            $d17 = $value13->DALERT;
            $d18 = $value13->TALERT;
            $strText_aer="\n".$d1."|".$d2."|".$d3."|".$d4."|".$d5."|".$d6."|".$d7."|".$d8."|".$d9."|".$d10."|".$d11."|".$d12."|".$d13."|".$d14."|".$d15."|".$d16."|".$d17."|".$d18;
            $ansitxt_pat_aer = iconv('UTF-8', 'UTF-8', $strText_aer);
            fwrite($objFopen_opd_aer, $ansitxt_pat_aer);
        }
        fclose($objFopen_opd_aer);
         Dapi_aer::truncate();
        //  Dapi_aer::where('claim','=','OFC_401')->delete();
         $fread_file_aer         = fread(fopen($file_d_aer,"r"),filesize($file_d_aer));
         $fread_file_aer_endcode = base64_encode($fread_file_aer);
         $read_file_aer_size     = filesize($file_d_aer);
         Dapi_aer::insert([
             'blobName'   =>  'AER.txt',
             'blobType'   =>  'text/plain',
             'blob'       =>   $fread_file_aer_endcode,
             'size'       =>   $read_file_aer_size,
             'encoding'   =>  'UTF-8',
             'claim'      =>  'OFC_401'
         ]);

         //************************ .14 adp.txt **********************************//
        $file_d_adp       = "Export_OFC/".$folder."/ADP.txt";
        $objFopen_opd_adp = fopen($file_d_adp, 'w');
        $opd_head_adp     = 'HN|AN|DATEOPD|TYPE|CODE|QTY|RATE|SEQ|CAGCODE|DOSE|CA_TYPE|SERIALNO|TOTCOPAY|USE_STATUS|TOTAL|QTYDAY|TMLTCODE|STATUS1|BI|CLINIC|ITEMSRC|PROVIDER|GRAVIDA|GA_WEEK|DCIP|LMP|SP_ITEM';
        fwrite($objFopen_opd_adp, $opd_head_adp);
        $adp = DB::connection('mysql')->select('SELECT * from d_adp where d_anaconda_id = "OFC_401"');
        foreach ($adp as $key => $value14) {
            $c1  = $value14->HN;
            $c2  = $value14->AN;
            $c3  = $value14->DATEOPD;
            $c4  = $value14->TYPE;
            $c5  = $value14->CODE;
            $c6  = $value14->QTY;
            $c7  = $value14->RATE;
            $c8  = $value14->SEQ;
            $c9  = $value14->CAGCODE;
            $c10 = $value14->DOSE;
            $c11 = $value14->CA_TYPE;
            $c12 = $value14->SERIALNO;
            $c13 = $value14->TOTCOPAY;
            $c14 = $value14->USE_STATUS;
            $c15 = $value14->TOTAL;
            $c16 = $value14->QTYDAY;
            $c17 = $value14->TMLTCODE;
            $c18 = $value14->STATUS1;
            $c19 = $value14->BI;
            $c20 = $value14->CLINIC;
            $c21 = $value14->ITEMSRC;
            $c22 = $value14->PROVIDER;
            $c23 = $value14->GRAVIDA;
            $c24 = $value14->GA_WEEK;
            $c25 = $value14->DCIP;
            $c26 = $value14->LMP;
            $c27 = $value14->SP_ITEM;
            $strText_adp ="\n".$c1."|".$c2."|".$c3."|".$c4."|".$c5."|".$c6."|".$c7."|".$c8."|".$c9."|".$c10."|".$c11."|".$c12."|".$c13."|".$c14."|".$c15."|".$c16."|".$c17."|".$c18."|".$c19."|".$c20."|".$c21."|".$c22."|".$c23."|".$c24."|".$c25."|".$c26."|".$c27;
            $ansitxt_pat_adp = iconv('UTF-8', 'UTF-8', $strText_adp);
            fwrite($objFopen_opd_adp, $ansitxt_pat_adp);
        }
        fclose($objFopen_opd_adp);
        Dapi_adp::truncate();
        // Dapi_adp::where('claim','=','OFC_401')->delete();
        $fread_file_adp         = fread(fopen($file_d_adp,"r"),filesize($file_d_adp));
        $fread_file_adp_endcode = base64_encode($fread_file_adp);
        $read_file_adp_size     = filesize($file_d_adp);
        Dapi_adp::insert([
            'blobName'   =>  'ADP.txt',
            'blobType'   =>  'text/plain',
            'blob'       =>   $fread_file_adp_endcode,
            'size'       =>   $read_file_adp_size,
            'encoding'   =>  'UTF-8',
            'claim'      =>  'OFC_401'
        ]);

        //************************* 15.lvd.txt *****************************//
        $file_d_lvd       = "Export_OFC/".$folder."/LVD.txt";
        $objFopen_opd_lvd = fopen($file_d_lvd, 'w');
        $opd_head_lvd     = 'SEQLVD|AN|DATEOUT|TIMEOUT|DATEIN|TIMEIN|QTYDAY';
        fwrite($objFopen_opd_lvd, $opd_head_lvd);
        $lvd = DB::connection('mysql')->select('SELECT * from d_lvd where d_anaconda_id = "OFC_401"');
        foreach ($lvd as $key => $value15) {
            $L1 = $value15->SEQLVD;
            $L2 = $value15->AN;
            $L3 = $value15->DATEOUT;
            $L4 = $value15->TIMEOUT;
            $L5 = $value15->DATEIN;
            $L6 = $value15->TIMEIN;
            $L7 = $value15->QTYDAY;
            $strText_lvd     ="\n".$L1."|".$L2."|".$L3."|".$L4."|".$L5."|".$L6."|".$L7;
            $ansitxt_pat_lvd = iconv('UTF-8', 'UTF-8', $strText_lvd);
            fwrite($objFopen_opd_lvd, $ansitxt_pat_lvd);
        }
        fclose($objFopen_opd_lvd);
        Dapi_lvd::truncate();
        // Dapi_lvd::where('claim','=','OFC_401')->delete();
        $fread_file_lvd         = fread(fopen($file_d_lvd,"r"),filesize($file_d_lvd));
        $fread_file_lvd_endcode = base64_encode($fread_file_lvd);
        $read_file_lvd_size     = filesize($file_d_lvd);
        Dapi_lvd::insert([
            'blobName'   =>  'LVD.txt',
            'blobType'   =>  'text/plain',
            'blob'       =>   $fread_file_lvd_endcode,
            'size'       =>   $read_file_lvd_size,
            'encoding'   =>  'UTF-8',
            'claim'      =>  'OFC_401'
        ]);

        //*********************** 16.dru.txt ****************************//
        $file_d_dru = "Export_OFC/".$folder."/DRU.txt";
        $objFopen_opd_dru = fopen($file_d_dru, 'w');
        $opd_head_dru = 'HCODE|HN|AN|CLINIC|PERSON_ID|DATE_SERV|DID|DIDNAME|AMOUNT|DRUGPRICE|DRUGCOST|DIDSTD|UNIT|UNIT_PACK|SEQ|DRUGREMARK|PA_NO|TOTCOPAY|USE_STATUS|TOTAL|SIGCODE|SIGTEXT|PROVIDER|SP_ITEM';
        fwrite($objFopen_opd_dru, $opd_head_dru);
        $dru = DB::connection('mysql')->select('SELECT * from d_dru where d_anaconda_id = "OFC_401"');
        foreach ($dru as $key => $value16) {
            $g1  = $value16->HCODE;
            $g2  = $value16->HN;
            $g3  = $value16->AN;
            $g4  = $value16->CLINIC;
            $g5  = $value16->PERSON_ID;
            $g6  = $value16->DATE_SERV;
            $g7  = $value16->DID;
            $g8  = $value16->DIDNAME;
            $g9  = $value16->AMOUNT;
            $g10 = $value16->DRUGPRICE;
            $g11 = $value16->DRUGCOST;
            $g12 = $value16->DIDSTD;
            $g13 = $value16->UNIT;
            $g14 = $value16->UNIT_PACK;
            $g15 = $value16->SEQ;
            $g16 = $value16->DRUGREMARK;
            $g17 = $value16->PA_NO;
            $g18 = $value16->TOTCOPAY;
            $g19 = $value16->USE_STATUS;
            $g20 = $value16->TOTAL;
            $g21 = $value16->SIGCODE;
            $g22 = $value16->SIGTEXT;
            $g23 = $value16->PROVIDER;
            $g24 = $value16->SP_ITEM;
            $strText_dru ="\n".$g1."|".$g2."|".$g3."|".$g4."|".$g5."|".$g6."|".$g7."|".$g8."|".$g9."|".$g10."|".$g11."|".$g12."|".$g13."|".$g14."|".$g15."|".$g16."|".$g17."|".$g18."|".$g19."|".$g20."|".$g21."|".$g22."|".$g23."|".$g24;;
            $ansitxt_dru = iconv('UTF-8', 'UTF-8', $strText_dru);
            fwrite($objFopen_opd_dru, $ansitxt_dru);
        }
        fclose($objFopen_opd_dru);
        Dapi_dru::truncate();
        // Dapi_dru::where('claim','=','OFC_401')->delete();
        $fread_file_dru         = fread(fopen($file_d_dru,"r"),filesize($file_d_dru));
        $fread_file_dru_endcode = base64_encode($fread_file_dru);
        $read_file_dru_size      = filesize($file_d_dru);
        Dapi_dru::insert([
            'blobName'   =>  'DRU.txt',
            'blobType'   =>  'text/plain',
            'blob'       =>   $fread_file_dru_endcode,
            'size'       =>   $read_file_dru_size,
            'encoding'   =>  'UTF-8',
            'claim'      =>  'OFC_401'
        ]);

         return response()->json([
             'status'    => '200'
         ]);
    }
    public function account_401_send_api(Request $request)
    {
        $iduser = Auth::user()->id;
        // $data_token_ = DB::connection('mysql')->select('SELECT new_eclaim_token FROM api_neweclaim WHERE user_id = "'.$iduser.'" AND active_mini="E"');
        // foreach ($data_token_ as $key => $val_to) {
        //     // $username     = $val_to->api_neweclaim_user;
        //     // $password     = $val_to->api_neweclaim_pass;
        //     $token_        = $val_to->new_eclaim_token;
        // }
        // $username        = '6508634296688';
        // $password        = 'd12345';
        // $response = Http::withHeaders([
        //     'User-Agent:<platform>/<version> <10978>',
        //     'Accept' => 'application/json',
        // ])->post('https://nhsoapi.nhso.go.th/FMU/ecimp/v1/auth', [
        //     'username'    =>  $username ,
        //     'password'    =>  $password
        // ]);
        // $token = $response->json('token');

        $data_token_ = DB::connection('mysql')->select(' SELECT * FROM api_neweclaim WHERE user_id = "'.$iduser.'" AND active_mini="E"');
        foreach ($data_token_ as $key => $val_to) {
            $username     = $val_to->api_neweclaim_user;
            $password     = $val_to->api_neweclaim_pass;
            $token        = $val_to->new_eclaim_token;
        }
        // dd($token);
        $data_table = array("dapi_ins","dapi_pat","dapi_opd","dapi_orf","dapi_odx","dapi_oop","dapi_ipd","dapi_irf","dapi_idx","dapi_iop","dapi_cht","dapi_cha","dapi_aer","dapi_adp","dapi_lvd","dapi_dru");
        // dd($data_table);
        foreach ($data_table as $key => $val_t) {
            $data_all_ = DB::connection('mysql')->select('SELECT * FROM '.$val_t.'');
                foreach ($data_all_ as $val_field) {
                        $blob[] = $val_field->blob;
                        $size[] = $val_field->size;
                 }
            }
            // dd($blob[5]);
            $fame_send = curl_init();
            $postData_send = [
                "fileType" => "txt",
                "maininscl" => "OFC",
                "importDup" => true, //นำเข้าซ้ำ กรณีพบข้อมูลยังไม่ส่งเบิกชดเชย
                "assignToMe" => true,  //กำหนดข้อมูลให้แสดงผลเฉพาะผู้นำเข้าเท่านั้น
                "dataTypes" => ["OP","IP"],
                "opRefer" => false,
                    "file" => [
                        "ins" => [
                            "blobName"  => "INS.txt",
                            "blobType"  => "text/plain",
                            "blob"      => $blob[0],
                            "size"      => $size[0],
                            "encoding"  => "UTF-8"
                        ]
                        ,"pat" => [
                            "blobName"  => "PAT.txt",
                            "blobType"  => "text/plain",
                            "blob"      => $blob[1],
                            "size"      => $size[1],
                            "encoding"  => "UTF-8"
                        ]
                        ,"opd" => [
                            "blobName"  => "OPD.txt",
                            "blobType"  => "text/plain",
                            "blob"      => $blob[2],
                            "size"      => $size[2],
                            "encoding"  => "UTF-8"
                        ]
                        ,"orf" => [
                            "blobName"  => "ORF.txt",
                            "blobType"  => "text/plain",
                            "blob"      => $blob[3],
                            "size"      => $size[3],
                            "encoding"  => "UTF-8"
                        ]
                        ,"odx" => [
                            "blobName"  => "ODX.txt",
                            "blobType"  => "text/plain",
                            "blob"      => $blob[4],
                            "size"      => $size[4],
                            "encoding"  => "UTF-8"
                        ]
                        ,"oop" => [
                            "blobName"  => "OOP.txt",
                            "blobType"  => "text/plain",
                            "blob"      => $blob[5],
                            "size"      => $size[5],
                            "encoding"  => "UTF-8"
                        ]
                        ,"ipd" => [
                            "blobName"  => "IPD.txt",
                            "blobType"  => "text/plain",
                            "blob"      => $blob[6],
                            "size"      => $size[6],
                            "encoding"  => "UTF-8"
                        ]
                        ,"irf" => [
                            "blobName"  => "IRF.txt",
                            "blobType"  => "text/plain",
                            "blob"      => $blob[7],
                            "size"      => $size[7],
                            "encoding"  => "UTF-8"
                        ]
                        ,"idx" => [
                            "blobName"  => "IDX.txt",
                            "blobType"  => "text/plain",
                            "blob"      => $blob[8],
                            "size"      => $size[8],
                            "encoding"  => "UTF-8"
                        ]
                        ,"iop" => [
                            "blobName"  => "IOP.txt",
                            "blobType"  => "text/plain",
                            "blob"      => $blob[9],
                            "size"      => $size[9],
                            "encoding"  => "UTF-8"
                        ]
                        ,"cht" => [
                            "blobName"  => "CHT.txt",
                            "blobType"  => "text",
                            "blob"      => $blob[10],
                            "size"      => $size[10],
                            "encoding"  => "UTF-8"
                        ]
                        ,"cha" => [
                            "blobName"  => "CHA.txt",
                            "blobType"  => "text/plain",
                            "blob"      => $blob[11],
                            "size"      => $size[11],
                            "encoding"  => "UTF-8"
                        ]
                        ,"aer" => [
                            "blobName"  => "AER.txt",
                            "blobType"  => "text/plain",
                            "blob"      => $blob[12],
                            "size"      => $size[12],
                            "encoding"  => "UTF-8"
                        ]
                        ,"adp" => [
                            "blobName"  => "ADP.txt",
                            "blobType"  => "text/plain",
                            "blob"      => $blob[13],
                            "size"      => $size[13],
                            "encoding"  => "UTF-8"
                        ]
                        ,"lvd" => [
                            "blobName"  => "LVD.txt",
                            "blobType"  => "text/plain",
                            "blob"      => $blob[14],
                            "size"      => $size[14],
                            "encoding"  => "UTF-8"
                        ]
                        ,"dru" => [
                            "blobName" => "DRU.txt",
                            "blobType" => "text/plain",
                            "blob"     => $blob[15],
                            "size"     => $size[15],
                            "encoding" => "UTF-8"
                        ]
                        ,"lab" => null
                    ]
            ];
            // dd($postData_send);
            $headers_send  = [
                'Authorization : Bearer '.$token,
                'Content-Type: application/json',
                'User-Agent:<platform>/<version><10978>'
            ];

            curl_setopt($fame_send, CURLOPT_URL,"https://nhsoapi.nhso.go.th/FMU/ecimp/v1/send");
            curl_setopt($fame_send, CURLOPT_POST, 1);
            curl_setopt($fame_send, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($fame_send, CURLOPT_POSTFIELDS, json_encode($postData_send, JSON_UNESCAPED_SLASHES));
            curl_setopt($fame_send, CURLOPT_HTTPHEADER, $headers_send);

            $server_output     = curl_exec ($fame_send);
            $statusCode = curl_getinfo($fame_send, CURLINFO_HTTP_CODE);
            // dd($statusCode);
            $content = $server_output;
            $result = json_decode($content, true);
            dd($result);
            #echo "<BR>";
            @$status = $result['status'];
            #echo "<BR>";
            @$message = $result['message'];
            #echo "<BR>";
            // $client = new Client();
            // $headers  = [
            //     'Authorization' => 'Bearer '.$token,
            //     'Content-Type: application/json',
            //     'User-Agent:<platform>/<version><10978>'
            // ];
            // dd($headers_api);
            // $curl = curl_init();
            // $options = [
            //     "fileType" => "txt",
            //     "maininscl" => "OFC",
            //     "importDup" => false, //นำเข้าซ้ำ กรณีพบข้อมูลยังไม่ส่งเบิกชดเชย
            //     "assignToMe" => false,  //กำหนดข้อมูลให้แสดงผลเฉพาะผู้นำเข้าเท่านั้น
            //     "dataTypes" => ["OP","IP"],
            //     "opRefer" => false,
            //         "file" => [
            //             "ins" => [
            //                 "blobName"  => "INS.txt",
            //                 "blobType"  => "text/plain",
            //                 "blob"      => $blob[0],
            //                 "size"      => $size[0],
            //                 "encoding"  => "UTF-8"
            //             ]
            //             ,"pat" => [
            //                 "blobName"  => "PAT.txt",
            //                 "blobType"  => "text/plain",
            //                 "blob"      => $blob[1],
            //                 "size"      => $size[1],
            //                 "encoding"  => "UTF-8"
            //             ]
            //             ,"opd" => [
            //                 "blobName"  => "OPD.txt",
            //                 "blobType"  => "text/plain",
            //                 "blob"      => $blob[2],
            //                 "size"      => $size[2],
            //                 "encoding"  => "UTF-8"
            //             ]
            //             ,"orf" => [
            //                 "blobName"  => "ORF.txt",
            //                 "blobType"  => "text/plain",
            //                 "blob"      => $blob[3],
            //                 "size"      => $size[3],
            //                 "encoding"  => "UTF-8"
            //             ]
            //             ,"odx" => [
            //                 "blobName"  => "ODX.txt",
            //                 "blobType"  => "text/plain",
            //                 "blob"      => $blob[4],
            //                 "size"      => $size[4],
            //                 "encoding"  => "UTF-8"
            //             ]
            //             ,"oop" => [
            //                 "blobName"  => "OOP.txt",
            //                 "blobType"  => "text/plain",
            //                 "blob"      => $blob[5],
            //                 "size"      => $size[5],
            //                 "encoding"  => "UTF-8"
            //             ]
            //             ,"ipd" => [
            //                 "blobName"  => "IPD.txt",
            //                 "blobType"  => "text/plain",
            //                 "blob"      => $blob[6],
            //                 "size"      => $size[6],
            //                 "encoding"  => "UTF-8"
            //             ]
            //             ,"irf" => [
            //                 "blobName"  => "IRF.txt",
            //                 "blobType"  => "text/plain",
            //                 "blob"      => $blob[7],
            //                 "size"      => $size[7],
            //                 "encoding"  => "UTF-8"
            //             ]
            //             ,"idx" => [
            //                 "blobName"  => "IDX.txt",
            //                 "blobType"  => "text/plain",
            //                 "blob"      => $blob[8],
            //                 "size"      => $size[8],
            //                 "encoding"  => "UTF-8"
            //             ]
            //             ,"iop" => [
            //                 "blobName"  => "IOP.txt",
            //                 "blobType"  => "text/plain",
            //                 "blob"      => $blob[9],
            //                 "size"      => $size[9],
            //                 "encoding"  => "UTF-8"
            //             ]
            //             ,"cht" => [
            //                 "blobName"  => "CHT.txt",
            //                 "blobType"  => "text",
            //                 "blob"      => $blob[10],
            //                 "size"      => $size[10],
            //                 "encoding"  => "UTF-8"
            //             ]
            //             ,"cha" => [
            //                 "blobName"  => "CHA.txt",
            //                 "blobType"  => "text/plain",
            //                 "blob"      => $blob[11],
            //                 "size"      => $size[11],
            //                 "encoding"  => "UTF-8"
            //             ]
            //             ,"aer" => [
            //                 "blobName"  => "AER.txt",
            //                 "blobType"  => "text/plain",
            //                 "blob"      => $blob[12],
            //                 "size"      => $size[12],
            //                 "encoding"  => "UTF-8"
            //             ]
            //             ,"adp" => [
            //                 "blobName"  => "ADP.txt",
            //                 "blobType"  => "text/plain",
            //                 "blob"      => $blob[13],
            //                 "size"      => $size[13],
            //                 "encoding"  => "UTF-8"
            //             ]
            //             ,"lvd" => [
            //                 "blobName"  => "LVD.txt",
            //                 "blobType"  => "text/plain",
            //                 "blob"      => $blob[14],
            //                 "size"      => $size[14],
            //                 "encoding"  => "UTF-8"
            //             ]
            //             ,"dru" => [
            //                 "blobName" => "DRU.txt",
            //                 "blobType" => "text/plain",
            //                 "blob"     => $blob[15],
            //                 "size"     => $size[15],
            //                 "encoding" => "UTF-8"
            //             ]
            //             ,"lab" => null
            //         ]
            // ];
            // dd($options);

            // $response_send = Http::withHeaders([
            //     'User-Agent:<platform>/<version> <10978>',
            //     'Accept' => 'application/json',
            // ])->post('https://nhsoapi.nhso.go.th/FMU/ecimp/v1/auth', [
            //     'file'    =>  $options
            // ]);
            // $token = $response_send->json('token');
            // $api_send = curl_init();
            // curl_setopt($api_send, CURLOPT_URL,"https://nhsoapi.nhso.go.th/FMU/ecimp/v1/send");
            // curl_setopt($api_send, CURLOPT_POST, 1);
            // curl_setopt($api_send, CURLOPT_RETURNTRANSFER, 1);
            // curl_setopt($api_send, CURLOPT_POSTFIELDS, json_encode($options, JSON_UNESCAPED_SLASHES));
            // curl_setopt($api_send, CURLOPT_HTTPHEADER, $headers);
            // $server_output     = curl_exec ($api_send);
            // $statusCode = curl_getinfo($api_send, CURLINFO_HTTP_CODE);
            // $content = $server_output;
            // $result = json_decode($content, true);
            // dd($result);
            // @$status = $result['status'];
            // @$message = $result['message'];


            // dd($message);
        return response()->json([
            'status'    => '200'
        ]);
    }
    public function account_401_claim_export(Request $request)
    {
        $sss_date_now = date("Y-m-d");
        $sss_time_now = date("H:i:s");

        #ตัดขีด, ตัด : ออก
        $pattern_date = '/-/i';
        $sss_date_now_preg = preg_replace($pattern_date, '', $sss_date_now);
        $pattern_time = '/:/i';
        $sss_time_now_preg = preg_replace($pattern_time, '', $sss_time_now);
        #ตัดขีด, ตัด : ออก

         #delete file in folder ทั้งหมด
        $file = new Filesystem;
        $file->cleanDirectory('Export_OFC'); //ทั้งหมด
        // $file->cleanDirectory('UCEP_'.$sss_date_now_preg.'-'.$sss_time_now_preg);
        // $folder='OFC_'.$sss_date_now_preg.'-'.$sss_time_now_preg;

        $dataexport_ = DB::connection('mysql')->select('SELECT folder_name from fdh_sesion where d_anaconda_id = "OFC_401"');
        foreach ($dataexport_ as $key => $v_export) {
            $folder_ = $v_export->folder_name;
        }
        $folder = $folder_;

         mkdir ('Export_OFC/'.$folder, 0777, true);  //Web
        //  mkdir ('C:Export/'.$folder, 0777, true); //localhost

        header("Content-type: text/txt");
        header("Cache-Control: no-store, no-cache");
        header('Content-Disposition: attachment; filename="content.txt"; charset=tis-620″ ;');

         //********** 1 ins.txt *****************//
        $file_d_ins = "Export_OFC/".$folder."/INS.txt";
        $objFopen_opd_ins = fopen($file_d_ins, 'w');
        $opd_head_ins = 'HN|INSCL|SUBTYPE|CID|DATEIN|DATEEXP|HOSPMAIN|HOSPSUB|GOVCODE|GOVNAME|PERMITNO|DOCNO|OWNRPID|OWNNAME|AN|SEQ|SUBINSCL|RELINSCL|HTYPE';
        fwrite($objFopen_opd_ins, $opd_head_ins);
        $ins = DB::connection('mysql')->select('SELECT * from d_ins where d_anaconda_id = "OFC_401"');
        foreach ($ins as $key => $value1) {
            $a1 = $value1->HN;
            $a2 = $value1->INSCL;
            $a3 = $value1->SUBTYPE;
            $a4 = $value1->CID;
            $a5 = $value1->DATEIN;
            $a6 = $value1->DATEEXP;
            $a7 = $value1->HOSPMAIN;
            $a8 = $value1->HOSPSUB;
            $a9 = $value1->GOVCODE;
            $a10 = $value1->GOVNAME;
            $a11 = $value1->PERMITNO;
            $a12 = $value1->DOCNO;
            $a13 = $value1->OWNRPID;
            $a14 = $value1->OWNNAME;
            $a15 = $value1->AN;
            $a16 = $value1->SEQ;
            $a17 = $value1->SUBINSCL;
            $a18 = $value1->RELINSCL;
            $a19 = $value1->HTYPE;
            $strText_ins ="\n".$a1."|".$a2."|".$a3."|".$a4."|".$a5."|".$a6."|".$a7."|".$a8."|".$a9."|".$a10."|".$a11."|".$a12."|".$a13."|".$a14."|".$a15."|".$a16."|".$a17."|".$a18."|".$a19;
            $ansitxt_pat_ins = iconv('UTF-8', 'TIS-620', $strText_ins);
            fwrite($objFopen_opd_ins, $ansitxt_pat_ins);
        }
        fclose($objFopen_opd_ins);

        //**********2 pat.txt ******************//
        $file_pat = "Export_OFC/".$folder."/PAT.txt";
        $objFopen_opd_pat = fopen($file_pat, 'w');
        $opd_head_pat = 'HCODE|HN|CHANGWAT|AMPHUR|DOB|SEX|MARRIAGE|OCCUPA|NATION|PERSON_ID|NAMEPAT|TITLE|FNAME|LNAME|IDTYPE';
        fwrite($objFopen_opd_pat, $opd_head_pat);
        $pat = DB::connection('mysql')->select('SELECT * from d_pat where d_anaconda_id = "OFC_401"');
        foreach ($pat as $key => $value2) {
            $i1 = $value2->HCODE;
            $i2 = $value2->HN;
            $i3 = $value2->CHANGWAT;
            $i4 = $value2->AMPHUR;
            $i5 = $value2->DOB;
            $i6 = $value2->SEX;
            $i7 = $value2->MARRIAGE;
            $i8 = $value2->OCCUPA;
            $i9 = $value2->NATION;
            $i10 = $value2->PERSON_ID;
            $i11 = $value2->NAMEPAT;
            $i12 = $value2->TITLE;
            $i13 = $value2->FNAME;
            $i14 = $value2->LNAME;
            $i15 = $value2->IDTYPE;
            $strText_pat="\n".$i1."|".$i2."|".$i3."|".$i4."|".$i5."|".$i6."|".$i7."|".$i8."|".$i9."|".$i10."|".$i11."|".$i12."|".$i13."|".$i14."|".$i15;
            $ansitxt_pat_pat = iconv('UTF-8', 'TIS-620', $strText_pat);
            fwrite($objFopen_opd_pat, $ansitxt_pat_pat);
        }
        fclose($objFopen_opd_pat);

        //************ 3 opd.txt *****************//
        $file_d_opd = "Export_OFC/".$folder."/OPD.txt";
        $objFopen_opd_opd = fopen($file_d_opd, 'w');
        $opd_head_opd = 'HN|CLINIC|DATEOPD|TIMEOPD|SEQ|UUC|DETAIL|BTEMP|SBP|DBP|PR|RR|OPTYPE|TYPEIN|TYPEOUT';
        fwrite($objFopen_opd_opd, $opd_head_opd);
        $opd = DB::connection('mysql')->select('SELECT * from d_opd where d_anaconda_id = "OFC_401"');
        foreach ($opd as $key => $value3) {
            $o1 = $value3->HN;
            $o2 = $value3->CLINIC;
            $o3 = $value3->DATEOPD;
            $o4 = $value3->TIMEOPD;
            $o5 = $value3->SEQ;
            $o6 = $value3->UUC;
            $strText_opd="\n".$o1."|".$o2."|".$o3."|".$o4."|".$o5."|".$o6;
            $ansitxt_pat_opd = iconv('UTF-8', 'TIS-620', $strText_opd);
            fwrite($objFopen_opd_opd, $ansitxt_pat_opd);
        }
        fclose($objFopen_opd_opd);

        //****************** 4 orf.txt **************************//
        $file_d_orf = "Export_OFC/".$folder."/ORF.txt";
        $objFopen_opd_orf = fopen($file_d_orf, 'w');
        $opd_head_orf = 'HN|DATEOPD|CLINIC|REFER|REFERTYPE|SEQ';
        fwrite($objFopen_opd_orf, $opd_head_orf);
        $orf = DB::connection('mysql')->select('SELECT * from d_orf where d_anaconda_id = "OFC_401"');
        foreach ($orf as $key => $value4) {
            $p1 = $value4->HN;
            $p2 = $value4->DATEOPD;
            $p3 = $value4->CLINIC;
            $p4 = $value4->REFER;
            $p5 = $value4->REFERTYPE;
            $p6 = $value4->SEQ;
            $strText_orf ="\n".$p1."|".$p2."|".$p3."|".$p4."|".$p5."|".$p6;
            $ansitxt_pat_orf = iconv('UTF-8', 'TIS-620', $strText_orf);
            fwrite($objFopen_opd_orf, $ansitxt_pat_orf);
        }
        fclose($objFopen_opd_orf);

        //****************** 5 odx.txt **************************//
        $file_d_odx = "Export_OFC/".$folder."/ODX.txt";
        $objFopen_opd_odx = fopen($file_d_odx, 'w');
        $opd_head_odx = 'HN|DATEDX|CLINIC|DIAG|DXTYPE|DRDX|PERSON_ID|SEQ';
        fwrite($objFopen_opd_odx, $opd_head_odx);
        $odx = DB::connection('mysql')->select('SELECT * from d_odx where d_anaconda_id = "OFC_401"');
        foreach ($odx as $key => $value5) {
            $m1 = $value5->HN;
            $m2 = $value5->DATEDX;
            $m3 = $value5->CLINIC;
            $m4 = $value5->DIAG;
            $m5 = $value5->DXTYPE;
            $m6 = $value5->DRDX;
            $m7 = $value5->PERSON_ID;
            $m8 = $value5->SEQ;
            $strText_odx="\n".$m1."|".$m2."|".$m3."|".$m4."|".$m5."|".$m6."|".$m7."|".$m8;
            $ansitxt_pat_odx = iconv('UTF-8', 'TIS-620', $strText_odx);
            fwrite($objFopen_opd_odx, $ansitxt_pat_odx);
        }
        fclose($objFopen_opd_odx);

        //****************** 6.oop.txt ******************************//
        $file_d_oop = "Export_OFC/".$folder."/OOP.txt";
        $objFopen_opd_oop = fopen($file_d_oop, 'w');
        $opd_head_oop = 'HN|DATEOPD|CLINIC|OPER|DROPID|PERSON_ID|SEQ';
        fwrite($objFopen_opd_oop, $opd_head_oop);
        $oop = DB::connection('mysql')->select('SELECT * from d_oop where d_anaconda_id = "OFC_401"');
        foreach ($oop as $key => $value6) {
            $n1 = $value6->HN;
            $n2 = $value6->DATEOPD;
            $n3 = $value6->CLINIC;
            $n4 = $value6->OPER;
            $n5 = $value6->DROPID;
            $n6 = $value6->PERSON_ID;
            $n7 = $value6->SEQ;
            $strText_oop="\n".$n1."|".$n2."|".$n3."|".$n4."|".$n5."|".$n6."|".$n7;
            $ansitxt_pat_oop = iconv('UTF-8', 'TIS-620', $strText_oop);
            fwrite($objFopen_opd_oop, $ansitxt_pat_oop);
        }
        fclose($objFopen_opd_oop);

        //******************** 7.ipd.txt **************************//
        $file_d_ipd = "Export_OFC/".$folder."/IPD.txt";
        $objFopen_opd_ipd = fopen($file_d_ipd, 'w');
        $opd_head_ipd = 'HN|AN|DATEADM|TIMEADM|DATEDSC|TIMEDSC|DISCHS|DISCHT|WARDDSC|DEPT|ADM_W|UUC|SVCTYPE';
        fwrite($objFopen_opd_ipd, $opd_head_ipd);
        $ipd = DB::connection('mysql')->select('SELECT * from d_ipd where d_anaconda_id = "OFC_401"');
        foreach ($ipd as $key => $value7) {
            $j1 = $value7->HN;
            $j2 = $value7->AN;
            $j3 = $value7->DATEADM;
            $j4 = $value7->TIMEADM;
            $j5 = $value7->DATEDSC;
            $j6 = $value7->TIMEDSC;
            $j7 = $value7->DISCHS;
            $j8 = $value7->DISCHT;
            $j9 = $value7->WARDDSC;
            $j10 = $value7->DEPT;
            $j11 = $value7->ADM_W;
            $j12 = $value7->UUC;
            $j13 = $value7->SVCTYPE;
            $strText_ipd="\n".$j1."|".$j2."|".$j3."|".$j4."|".$j5."|".$j6."|".$j7."|".$j8."|".$j9."|".$j10."|".$j11."|".$j12."|".$j13;
            $ansitxt_pat_ipd = iconv('UTF-8', 'TIS-620', $strText_ipd);
            fwrite($objFopen_opd_ipd, $ansitxt_pat_ipd);
        }
        fclose($objFopen_opd_ipd);

         //********************* 8.irf.txt ***************************//
         $file_d_irf = "Export_OFC/".$folder."/IRF.txt";
         $objFopen_opd_irf = fopen($file_d_irf, 'w');
         $opd_head_irf = 'AN|REFER|REFERTYPE';
         fwrite($objFopen_opd_irf, $opd_head_irf);
         $irf = DB::connection('mysql')->select('SELECT * from d_irf where d_anaconda_id = "OFC_401"');
         foreach ($irf as $key => $value8) {
             $k1 = $value8->AN;
             $k2 = $value8->REFER;
             $k3 = $value8->REFERTYPE;
             $strText_irf="\n".$k1."|".$k2."|".$k3;
             $ansitxt_pat_irf = iconv('UTF-8', 'TIS-620', $strText_irf);
             fwrite($objFopen_opd_irf, $ansitxt_pat_irf);
         }
         fclose($objFopen_opd_irf);

        //********************** 9.idx.txt ***************************//
        $file_d_idx = "Export_OFC/".$folder."/IDX.txt";
        $objFopen_opd_idx = fopen($file_d_idx, 'w');
        $opd_head_idx = 'AN|DIAG|DXTYPE|DRDX';
        fwrite($objFopen_opd_idx, $opd_head_idx);
        $idx = DB::connection('mysql')->select('SELECT * from d_idx where d_anaconda_id = "OFC_401"');
        foreach ($idx as $key => $value9) {
            $h1 = $value9->AN;
            $h2 = $value9->DIAG;
            $h3 = $value9->DXTYPE;
            $h4 = $value9->DRDX;
            $strText_idx="\n".$h1."|".$h2."|".$h3."|".$h4;
            $ansitxt_pat_idx = iconv('UTF-8', 'TIS-620', $strText_idx);
            fwrite($objFopen_opd_idx, $ansitxt_pat_idx);
        }
        fclose($objFopen_opd_idx);

        //********************** 10 iop.txt ***************************//
        $file_d_iop = "Export_OFC/".$folder."/IOP.txt";
        $objFopen_opd_iop = fopen($file_d_iop, 'w');
        $opd_head_iop = 'AN|OPER|OPTYPE|DROPID|DATEIN|TIMEIN|DATEOUT|TIMEOUT';
        fwrite($objFopen_opd_iop, $opd_head_iop);
        $iop = DB::connection('mysql')->select('SELECT * from d_iop where d_anaconda_id = "OFC_401"');
        foreach ($iop as $key => $value10) {
            $b1 = $value10->AN;
            $b2 = $value10->OPER;
            $b3 = $value10->OPTYPE;
            $b4 = $value10->DROPID;
            $b5 = $value10->DATEIN;
            $b6 = $value10->TIMEIN;
            $b7 = $value10->DATEOUT;
            $b8 = $value10->TIMEOUT;
            $strText_iop ="\n".$b1."|".$b2."|".$b3."|".$b4."|".$b5."|".$b6."|".$b7."|".$b8;
            $ansitxt_pat_iop = iconv('UTF-8', 'TIS-620', $strText_iop);
            fwrite($objFopen_opd_iop, $ansitxt_pat_iop);
        }
        fclose($objFopen_opd_iop);

        //********************** .11 cht.txt *****************************//
        $file_d_cht = "Export_OFC/".$folder."/CHT.txt";
        $objFopen_opd_cht = fopen($file_d_cht, 'w');
        $opd_head_cht = 'HN|AN|DATE|TOTAL|PAID|PTTYPE|PERSON_ID|SEQ';
        fwrite($objFopen_opd_cht, $opd_head_cht);
        $cht = DB::connection('mysql')->select('SELECT * from d_cht where d_anaconda_id = "OFC_401"');
        foreach ($cht as $key => $value11) {
            $f1 = $value11->HN;
            $f2 = $value11->AN;
            $f3 = $value11->DATE;
            $f4 = $value11->TOTAL;
            $f5 = $value11->PAID;
            $f6 = $value11->PTTYPE;
            $f7 = $value11->PERSON_ID;
            $f8 = $value11->SEQ;
            $strText_cht="\n".$f1."|".$f2."|".$f3."|".$f4."|".$f5."|".$f6."|".$f7."|".$f8;
            $ansitxt_pat_cht = iconv('UTF-8', 'TIS-620', $strText_cht);
            fwrite($objFopen_opd_cht, $ansitxt_pat_cht);
        }
        fclose($objFopen_opd_cht);

        //********************** .12 cha.txt *****************************//
        $file_d_cha = "Export_OFC/".$folder."/CHA.txt";
        $objFopen_opd_cha = fopen($file_d_cha, 'w');
        $opd_head_cha = 'HN|AN|DATE|CHRGITEM|AMOUNT|PERSON_ID|SEQ';
        fwrite($objFopen_opd_cha, $opd_head_cha);
        $cha = DB::connection('mysql')->select('SELECT * from d_cha where d_anaconda_id = "OFC_401"');
        foreach ($cha as $key => $value12) {
            $e1 = $value12->HN;
            $e2 = $value12->AN;
            $e3 = $value12->DATE;
            $e4 = $value12->CHRGITEM;
            $e5 = $value12->AMOUNT;
            $e6 = $value12->PERSON_ID;
            $e7 = $value12->SEQ;
            $strText_cha="\n".$e1."|".$e2."|".$e3."|".$e4."|".$e5."|".$e6."|".$e7;
            $ansitxt_pat_cha = iconv('UTF-8', 'TIS-620', $strText_cha);
            fwrite($objFopen_opd_cha, $ansitxt_pat_cha);
        }
        fclose($objFopen_opd_cha);

        //************************ .13 aer.txt **********************************//
        $file_d_aer = "Export_OFC/".$folder."/AER.txt";
        $objFopen_opd_aer = fopen($file_d_aer, 'w');
        $opd_head_aer = 'HN|AN|DATEOPD|AUTHAE|AEDATE|AETIME|AETYPE|REFER_NO|REFMAINI|IREFTYPE|REFMAINO|OREFTYPE|UCAE|EMTYPE|SEQ|AESTATUS|DALERT|TALERT';
        fwrite($objFopen_opd_aer, $opd_head_aer);
        $aer = DB::connection('mysql')->select('SELECT * from d_aer where d_anaconda_id = "OFC_401"');
        foreach ($aer as $key => $value13) {
            $d1 = $value13->HN;
            $d2 = $value13->AN;
            $d3 = $value13->DATEOPD;
            $d4 = $value13->AUTHAE;
            $d5 = $value13->AEDATE;
            $d6 = $value13->AETIME;
            $d7 = $value13->AETYPE;
            $d8 = $value13->REFER_NO;
            $d9 = $value13->REFMAINI;
            $d10 = $value13->IREFTYPE;
            $d11 = $value13->REFMAINO;
            $d12 = $value13->OREFTYPE;
            $d13 = $value13->UCAE;
            $d14 = $value13->EMTYPE;
            $d15 = $value13->SEQ;
            $d16 = $value13->AESTATUS;
            $d17 = $value13->DALERT;
            $d18 = $value13->TALERT;
            $strText_aer="\n".$d1."|".$d2."|".$d3."|".$d4."|".$d5."|".$d6."|".$d7."|".$d8."|".$d9."|".$d10."|".$d11."|".$d12."|".$d13."|".$d14."|".$d15."|".$d16."|".$d17."|".$d18;
            $ansitxt_pat_aer = iconv('UTF-8', 'TIS-620', $strText_aer);
            fwrite($objFopen_opd_aer, $ansitxt_pat_aer);
        }
        fclose($objFopen_opd_aer);

        //************************ .14 adp.txt **********************************//
        $file_d_adp = "Export_OFC/".$folder."/ADP.txt";
        $objFopen_opd_adp = fopen($file_d_adp, 'w');
        $opd_head_adp = 'HN|AN|DATEOPD|TYPE|CODE|QTY|RATE|SEQ|CAGCODE|DOSE|CA_TYPE|SERIALNO|TOTCOPAY|USE_STATUS|TOTAL|QTYDAY|TMLTCODE|STATUS1|BI|CLINIC|ITEMSRC|PROVIDER|GRAVIDA|GA_WEEK|DCIP|LMP|SP_ITEM';
        fwrite($objFopen_opd_adp, $opd_head_adp);
        $adp = DB::connection('mysql')->select('SELECT * from d_adp where d_anaconda_id = "OFC_401"');
        foreach ($adp as $key => $value14) {
            $c1 = $value14->HN;
            $c2 = $value14->AN;
            $c3 = $value14->DATEOPD;
            $c4 = $value14->TYPE;
            $c5 = $value14->CODE;
            $c6 = $value14->QTY;
            $c7 = $value14->RATE;
            $c8 = $value14->SEQ;
            $c9 = $value14->CAGCODE;
            $c10 = $value14->DOSE;
            $c11 = $value14->CA_TYPE;
            $c12 = $value14->SERIALNO;
            $c13 = $value14->TOTCOPAY;
            $c14 = $value14->USE_STATUS;
            $c15 = $value14->TOTAL;
            $c16 = $value14->QTYDAY;
            $c17 = $value14->TMLTCODE;
            $c18 = $value14->STATUS1;
            $c19 = $value14->BI;
            $c20 = $value14->CLINIC;
            $c21 = $value14->ITEMSRC;
            $c22 = $value14->PROVIDER;
            $c23 = $value14->GRAVIDA;
            $c24 = $value14->GA_WEEK;
            $c25 = $value14->DCIP;
            $c26 = $value14->LMP;
            $c27 = $value14->SP_ITEM;
            $strText_adp ="\n".$c1."|".$c2."|".$c3."|".$c4."|".$c5."|".$c6."|".$c7."|".$c8."|".$c9."|".$c10."|".$c11."|".$c12."|".$c13."|".$c14."|".$c15."|".$c16."|".$c17."|".$c18."|".$c19."|".$c20."|".$c21."|".$c22."|".$c23."|".$c24."|".$c25."|".$c26."|".$c27;
            $ansitxt_pat_adp = iconv('UTF-8', 'TIS-620', $strText_adp);
            fwrite($objFopen_opd_adp, $ansitxt_pat_adp);
        }
        fclose($objFopen_opd_adp);

        //*********************** 15.dru.txt ****************************//
        $file_d_dru = "Export_OFC/".$folder."/DRU.txt";
        $objFopen_opd_dru = fopen($file_d_dru, 'w');
        $opd_head_dru = 'HCODE|HN|AN|CLINIC|PERSON_ID|DATE_SERV|DID|DIDNAME|AMOUNT|DRUGPRICE|DRUGCOST|DIDSTD|UNIT|UNIT_PACK|SEQ|DRUGREMARK|PA_NO|TOTCOPAY|USE_STATUS|TOTAL|SIGCODE|SIGTEXT|PROVIDER|SP_ITEM';
        fwrite($objFopen_opd_dru, $opd_head_dru);
        $dru = DB::connection('mysql')->select('SELECT * from d_dru where d_anaconda_id = "OFC_401"');
        foreach ($dru as $key => $value15) {
            $g1 = $value15->HCODE;
            $g2 = $value15->HN;
            $g3 = $value15->AN;
            $g4 = $value15->CLINIC;
            $g5 = $value15->PERSON_ID;
            $g6 = $value15->DATE_SERV;
            $g7 = $value15->DID;
            $g8 = $value15->DIDNAME;
            $g9 = $value15->AMOUNT;
            $g10 = $value15->DRUGPRICE;
            $g11 = $value15->DRUGCOST;
            $g12 = $value15->DIDSTD;
            $g13 = $value15->UNIT;
            $g14 = $value15->UNIT_PACK;
            $g15 = $value15->SEQ;
            $g16 = $value15->DRUGREMARK;
            $g17 = $value15->PA_NO;
            $g18 = $value15->TOTCOPAY;
            $g19 = $value15->USE_STATUS;
            $g20 = $value15->TOTAL;
            $g21 = $value15->SIGCODE;
            $g22 = $value15->SIGTEXT;
            $g23 = $value15->PROVIDER;
            $g24 = $value15->SP_ITEM;
            $strText_dru ="\n".$g1."|".$g2."|".$g3."|".$g4."|".$g5."|".$g6."|".$g7."|".$g8."|".$g9."|".$g10."|".$g11."|".$g12."|".$g13."|".$g14."|".$g15."|".$g16."|".$g17."|".$g18."|".$g19."|".$g20."|".$g21."|".$g22."|".$g23."|".$g24;;
            $ansitxt_pat_dru = iconv('UTF-8', 'TIS-620', $strText_dru);
            fwrite($objFopen_opd_dru, $ansitxt_pat_dru);
        }
        fclose($objFopen_opd_dru);

        //16 dru.txt
        // $file_d_dru = "Export_OFC_API/".$folder."/DRU.txt";
        // // $objFopen_dru = fopen($file_d_dru, 'w');
        // $objFopen_dru_utf = fopen($file_d_dru, 'w');
        // $opd_head_dru = 'HCODE|HN|AN|CLINIC|PERSON_ID|DATE_SERV|DID|DIDNAME|AMOUNT|DRUGPRICE|DRUGCOST|DIDSTD|UNIT|UNIT_PACK|SEQ|DRUGREMARK|PA_NO|TOTCOPAY|USE_STATUS|TOTAL|SIGCODE|SIGTEXT|PROVIDER';
        // // fwrite($objFopen_dru, $opd_head_dru);
        // fwrite($objFopen_dru_utf, $opd_head_dru);
        // $dru = DB::connection('mysql')->select('
        //     SELECT * from d_dru where d_anaconda_id = "OFC_401"
        // ');
        // foreach ($dru as $key => $value7) {
        //     $g1 = $value7->HCODE;
        //     $g2 = $value7->HN;
        //     $g3 = $value7->AN;
        //     $g4 = $value7->CLINIC;
        //     $g5 = $value7->PERSON_ID;
        //     $g6 = $value7->DATE_SERV;
        //     $g7 = $value7->DID;
        //     $g8 = $value7->DIDNAME;
        //     $g9 = $value7->AMOUNT;
        //     $g10 = $value7->DRUGPRICE;
        //     $g11 = $value7->DRUGCOST;
        //     $g12 = $value7->DIDSTD;
        //     $g13 = $value7->UNIT;
        //     $g14 = $value7->UNIT_PACK;
        //     $g15 = $value7->SEQ;
        //     // $g16 = $value7->DRUGTYPE;
        //     $g16 = $value7->DRUGREMARK;
        //     $g17 = $value7->PA_NO;
        //     $g18 = $value7->TOTCOPAY;
        //     $g19 = $value7->USE_STATUS;
        //     $g20 = $value7->TOTAL;
        //     $g21 = $value7->SIGCODE;
        //     $g22 = $value7->SIGTEXT;
        //     $g23 = $value7->PROVIDER;
        //     // $g25 = $value7->SP_ITEM;
        //     $str_dru="\n".$g1."|".$g2."|".$g3."|".$g4."|".$g5."|".$g6."|".$g7."|".$g8."|".$g9."|".$g10."|".$g11."|".$g12."|".$g13."|".$g14."|".$g15."|".$g16."|".$g17."|".$g18."|".$g19."|".$g20."|".$g21."|".$g22."|".$g23;
        //     // $ansitxt_dru = iconv('UTF-8', 'TIS-620', $str_dru);
        //     $ansitxt_dru_utf = iconv('UTF-8', 'UTF-8', $str_dru);
        //     // fwrite($objFopen_dru, $ansitxt_dru);
        //     fwrite($objFopen_dru_utf, $ansitxt_dru_utf);
        // }

        //************************* 16.lvd.txt *****************************//
        $file_d_lvd = "Export_OFC/".$folder."/LVD.txt";
        $objFopen_opd_lvd = fopen($file_d_lvd, 'w');
        $opd_head_lvd = 'SEQLVD|AN|DATEOUT|TIMEOUT|DATEIN|TIMEIN|QTYDAY';
        fwrite($objFopen_opd_lvd, $opd_head_lvd);
        $lvd = DB::connection('mysql')->select('SELECT * from d_lvd where d_anaconda_id = "OFC_401"');
        foreach ($lvd as $key => $value16) {
            $L1 = $value16->SEQLVD;
            $L2 = $value16->AN;
            $L3 = $value16->DATEOUT;
            $L4 = $value16->TIMEOUT;
            $L5 = $value16->DATEIN;
            $L6 = $value16->TIMEIN;
            $L7 = $value16->QTYDAY;
            $strText_lvd ="\n".$L1."|".$L2."|".$L3."|".$L4."|".$L5."|".$L6."|".$L7;
            $ansitxt_pat_lvd = iconv('UTF-8', 'TIS-620', $strText_lvd);
            fwrite($objFopen_opd_lvd, $ansitxt_pat_lvd);
        }
        fclose($objFopen_opd_lvd);

        //*********************** 17.lab.txt **********************************//
        $file_d_lab = "Export_OFC/".$folder."/LAB.txt";
        $objFopen_opd_lab = fopen($file_d_lab, 'w');
        $opd_head_lab = 'HCODE|HN|PERSON_ID|DATESERV|SEQ|LABTEST|LABRESULT';
        fwrite($objFopen_opd_lab, $opd_head_lab);
        fclose($objFopen_opd_lab);



        $pathdir =  "Export_OFC/".$folder."/";
        $zipcreated = $folder.".zip";

        $newzip = new ZipArchive;
        if($newzip -> open($zipcreated, ZipArchive::CREATE ) === TRUE) {
        $dir = opendir($pathdir);

        while($file = readdir($dir)) {
            if(is_file($pathdir.$file)) {
                $newzip -> addFile($pathdir.$file, $file);
            }
        }
        $newzip ->close();
                if (file_exists($zipcreated)) {
                    header('Content-Type: application/zip');
                    header('Content-Disposition: attachment; filename="'.basename($zipcreated).'"');
                    header('Content-Length: ' . filesize($zipcreated));
                    flush();
                    readfile($zipcreated);
                    unlink($zipcreated);
                    $files = glob($pathdir . '/*');
                    foreach($files as $file) {
                        if(is_file($file)) {
                            // unlink($file);
                        }
                    }
                    return redirect()->route('acc.account_401_pull');
                }
        }

        return redirect()->route('acc.account_401_pull');

    }
    public function account_401_claim_zip (Request $request)
    {
        $dataexport_ = DB::connection('mysql')->select('SELECT folder_name from fdh_sesion where d_anaconda_id = "OFC_401"');
        foreach ($dataexport_ as $key => $v_export) {
            $folder = $v_export->folder_name;
        }
        $filename = $folder . ".zip";

        $zip = new ZipArchive;
        if ($zip->open(public_path($filename), ZipArchive::CREATE) === TRUE) {
            $files = File::files(public_path("Export_OFC/" . $folder . "/"));
            foreach ($files as $key => $value) {
                $relativenameInZipFile = basename($value);
                $zip->addFile($value, $relativenameInZipFile);
            }
            $zip->close();
        }
        return response()->download(public_path($filename));

    }

    // **************** REP  *****************************
    public function account_401_rep(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $data['users'] = User::get();
        $countc = DB::table('d_ofc_repexcel')->count();
        $datashow = DB::table('d_ofc_repexcel')->get();


        return view('account_401.account_401_rep',[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'countc'        =>     $countc
        ]);
    }
    function account_401_repsave (Request $request)
    {
        // $this->validate($request, [
        //     'file' => 'required|file|mimes:xls,xlsx'
        // ]);
        $the_file = $request->file('file');
        $file_ = $request->file('file')->getClientOriginalName(); //ชื่อไฟล์
        // dd($file_);
            // try{
                // Cheet 2  originalName
                // $spreadsheet = IOFactory::createReader($the_file);
                // $spreadsheet = IOFactory::load($the_file->getRealPath());
                $spreadsheet = IOFactory::load($the_file);
                $sheet        = $spreadsheet->setActiveSheetIndex(0);
                $row_limit    = $sheet->getHighestDataRow();
                // $column_limit = $sheet->getHighestDataColumn();
                $row_range    = range( 8, $row_limit );
                // $column_range = range( 'AO', $column_limit );
                $startcount = 8;
                $data = array();
                foreach ($row_range as $row ) {
                    $vst = $sheet->getCell( 'I' . $row )->getValue();
                    $day = substr($vst,0,2);
                    $mo = substr($vst,3,2);
                    $year = substr($vst,6,4);
                    $vstdate = $year.'-'.$mo.'-'.$day;

                    $reg = $sheet->getCell( 'J' . $row )->getValue();
                    $regday = substr($reg, 0, 2);
                    $regmo = substr($reg, 3, 2);
                    $regyear = substr($reg, 6, 4);
                    $dchdate = $regyear.'-'.$regmo.'-'.$regday;

                    $k = $sheet->getCell( 'K' . $row )->getValue();
                    $del_k = str_replace(",","",$k);
                    $l= $sheet->getCell( 'L' . $row )->getValue();
                    $del_l = str_replace(",","",$l);

                    $ad = $sheet->getCell( 'AD' . $row )->getValue();
                    $del_ad = str_replace(",","",$ad);
                    $ae = $sheet->getCell( 'AE' . $row )->getValue();
                    $del_ae = str_replace(",","",$ae);
                    $af = $sheet->getCell( 'AF' . $row )->getValue();
                    $del_af = str_replace(",","",$af);
                    $ag = $sheet->getCell( 'AG' . $row )->getValue();
                    $del_ag = str_replace(",","",$ag);
                    $ah = $sheet->getCell( 'AH' . $row )->getValue();
                    $del_ah = str_replace(",","",$ah);
                    $ai = $sheet->getCell( 'AI' . $row )->getValue();
                    $del_ai = str_replace(",","",$ai);
                    $an= $sheet->getCell( 'AN' . $row )->getValue();
                    $del_an = str_replace(",","",$an);
                    $ao = $sheet->getCell( 'AO' . $row )->getValue();
                    $del_ao = str_replace(",","",$ao);
                    $ap = $sheet->getCell( 'AP' . $row )->getValue();
                    $del_ap = str_replace(",","",$ap);
                    $aq = $sheet->getCell( 'AQ' . $row )->getValue();
                    $del_aq = str_replace(",","",$aq);
                    $ar = $sheet->getCell( 'AR' . $row )->getValue();
                    $del_ar = str_replace(",","",$ar);
                    $as = $sheet->getCell( 'AS' . $row )->getValue();
                    $del_as = str_replace(",","",$as);
                    $at = $sheet->getCell( 'AT' . $row )->getValue();
                    $del_at = str_replace(",","",$at);
                    $au = $sheet->getCell( 'AU' . $row )->getValue();
                    $del_au = str_replace(",","",$au);
                    $av = $sheet->getCell( 'AV' . $row )->getValue();
                    $del_av = str_replace(",","",$av);

                    $data[] = [
                        'a'                   =>$sheet->getCell( 'A' . $row )->getValue(),
                        'b'                   =>$sheet->getCell( 'B' . $row )->getValue(),
                        'c'                   =>$sheet->getCell( 'C' . $row )->getValue(),
                        'd'                   =>$sheet->getCell( 'D' . $row )->getValue(),
                        'e'                   =>$sheet->getCell( 'E' . $row )->getValue(),
                        'f'                   =>$sheet->getCell( 'F' . $row )->getValue(),
                        'g'                   =>$sheet->getCell( 'G' . $row )->getValue(),
                        'h'                   =>$sheet->getCell( 'H' . $row )->getValue(),
                        'i'                   =>$vstdate,
                        'j'                   =>$dchdate,
                        'k'                   =>$del_k,
                        'l'                   =>$del_l,
                        'm'                   =>$sheet->getCell( 'M' . $row )->getValue(),
                        'n'                   =>$sheet->getCell( 'N' . $row )->getValue(),
                        'o'                   =>$sheet->getCell( 'O' . $row )->getValue(),
                        'p'                   =>$sheet->getCell( 'P' . $row )->getValue(),
                        'q'                   =>$sheet->getCell( 'Q' . $row )->getValue(),
                        'r'                   =>$sheet->getCell( 'R' . $row )->getValue(),
                        's'                   =>$sheet->getCell( 'S' . $row )->getValue(),
                        't'                   =>$sheet->getCell( 'T' . $row )->getValue(),
                        'u'                   =>$sheet->getCell( 'U' . $row )->getValue(),
                        'v'                   =>$sheet->getCell( 'V' . $row )->getValue(),
                        'w'                   =>$sheet->getCell( 'W' . $row )->getValue(),
                        'x'                   =>$sheet->getCell( 'X' . $row )->getValue(),
                        'y'                   =>$sheet->getCell( 'Y' . $row )->getValue(),
                        'z'                   =>$sheet->getCell( 'Z' . $row )->getValue(),
                        'aa'                  =>$sheet->getCell( 'AA' . $row )->getValue(),
                        'ab'                  =>$sheet->getCell( 'AB' . $row )->getValue(),
                        'ac'                  =>$sheet->getCell( 'AC' . $row )->getValue(),

                        'ad'                  =>$del_ad,
                        'ae'                  =>$del_ae,
                        'af'                  =>$del_af,
                        'ag'                  =>$del_ag,
                        'ah'                  =>$del_ah,
                        'ai'                  =>$del_ai,

                        'ak'                  =>$sheet->getCell( 'AK' . $row )->getValue(),
                        'al'                  =>$sheet->getCell( 'AL' . $row )->getValue(),
                        'am'                  =>$sheet->getCell( 'AM' . $row )->getValue(),
                        'an'                  =>$del_an,
                        'ao'                  =>$del_ao,
                        'ap'                  =>$del_ap,
                        'aq'                  =>$del_aq,
                        'ar'                  =>$del_ar,
                        'as'                  =>$del_as,
                        'at'                  =>$del_at,
                        'au'                  =>$del_au,
                        'av'                  =>$del_av,
                        'aw'                  =>$sheet->getCell( 'AW' . $row )->getValue(),
                        'ax'                  =>$sheet->getCell( 'AX' . $row )->getValue(),
                        'ay'                  =>$sheet->getCell( 'AY' . $row )->getValue(),
                        'az'                  =>$sheet->getCell( 'AZ' . $row )->getValue(),
                        'ba'                  =>$sheet->getCell( 'BA' . $row )->getValue(),
                        'bb'                  =>$sheet->getCell( 'BB' . $row )->getValue(),
                        'bc'                  =>$sheet->getCell( 'BC' . $row )->getValue(),
                        'STMdoc'              =>$file_
                    ];
                    $startcount++;

                }

                foreach (array_chunk($data,500) as $t)
                {
                    DB::table('d_ofc_repexcel')->insert($t);
                }

                // $the_file->delete('public/File_eclaim/'.$file_);
                // $the_file->storeAs('Import/',$file_);   // ย้าย ไฟล์
                // Storage::delete('File_eclaim/'.$file_);   // ลบไฟล์
                // ลบไฟล์
                // if(file_exists(public_path('File_eclaim/'.$file_))){
                //     unlink(public_path('File_eclaim/'.$file_));
                //     // Storage::delete('File_eclaim/'.$file_);   // ลบไฟล์
                // }else{
                //     dd('File does not exists.');
                // }


            // } catch (Exception $e) {
            //     $error_code = $e->errorInfo[1];
            //     return back()->withErrors('There was a problem uploading the data!');
            // }
            // return redirect()->back();
            return redirect()->route('acc.account_401_rep');
            // return response()->json([
            //     'status'    => '200',
            // ]);
    }
    public function account_401_repsend(Request $request)
    {

        try{
            $data_ = DB::connection('mysql')->select('SELECT * FROM d_ofc_repexcel');
                foreach ($data_ as $key => $value) {
                    if ($value->b != '') {
                        $check = D_ofc_rep::where('rep_a','=',$value->a)->where('no_b','=',$value->b)->count();
                        if ($check > 0) {
                        } else {
                            D_ofc_rep::insert([
                                'rep_a'                   =>$value->a,
                                'no_b'                    =>$value->b,
                                'tranid_c'                =>$value->c,
                                'hn_d'                    =>$value->d,
                                'an_e'                    =>$value->e,
                                'pid_f'                   =>$value->f,
                                'ptname_g'                =>$value->g,
                                'type_h'                  =>$value->h,
                                'vstdate_i'               =>$value->i,
                                'dchdate_j'               =>$value->j,
                                'price1_k'                =>$value->k,
                                'pp_spsch_l'              =>$value->l,
                                'errorcode_m'             =>$value->m,
                                'kongtoon_n'              =>$value->n,
                                'typeservice_o'           =>$value->o,
                                'refer_p'                 =>$value->p,
                                'pttype_have_q'           =>$value->q,
                                'pttype_true_r'           =>$value->r,
                                'mian_pttype_s'           =>$value->s,
                                'secon_pttype_t'          =>$value->t,
                                'href_u'                  =>$value->u,
                                'HCODE_v'                 =>$value->v,
                                'prov1_w'                 =>$value->w,
                                'code_dep_x'              =>$value->x,
                                'name_dep_y'              =>$value->y,
                                'proj_z'                  =>$value->z,
                                'pa_aa'                   =>$value->aa,
                                'drg_ab'                  =>$value->ab,
                                'rw_ac'                   =>$value->ac,
                                'income_ad'               =>$value->ad,
                                'pp_gep_ae'               =>$value->ae,
                                'claim_true_af'           =>$value->af,
                                'claim_false_ag'          =>$value->ag,
                                'cash_money_ah'           =>$value->ah,
                                'pay_ai'                  =>$value->ai,
                                'ps_aj'                   =>$value->aj,
                                'ps_percent_ak'           =>$value->ak,
                                'ccuf_al'                 =>$value->al,
                                'AdjRW_am'                =>$value->am,
                                'plb_an'                  =>$value->an,
                                'IPCS_ao'                 =>$value->ao,
                                'IPCS_ORS_ap'             =>$value->ap,
                                'OPCS_aq'                 =>$value->aq,
                                'PACS_ar'                 =>$value->ar,
                                'INSTCS_as'               =>$value->as,
                                'OTCS_at'                 =>$value->at,
                                'PP_au'                   =>$value->au,
                                'DRUG_av'                 =>$value->av,
                                'IPCS_aw'                 =>$value->aw,
                                'OPCS_AX'                 =>$value->ax,
                                'PACS_ay'                 =>$value->ay,
                                'INSTCS_az'               =>$value->az,
                                'OTCS_ba'                 =>$value->ba,
                                'ORS_bb'                  =>$value->bb,
                                'VA_bc'                   =>$value->bc,
                                'STMdoc'                  =>$value->STMdoc
                            ]);
                        }

                        $checks = Acc_debtor::where('hn', $value->d)->where('vstdate', $value->i)->where('account_code','1102050101.401')->count();
                        if ($checks > 0) {
                            Acc_debtor::where('hn', $value->d)->where('vstdate', $value->i)->where('account_code','1102050101.401')->update(
                                [
                                    'rep_error'    => $value->m,
                                    'rep_pay'      => $value->af,
                                    'rep_nopay'    => $value->ag,
                                    'rep_doc'       => $value->STMdoc
                                ]
                            );

                            Acc_1102050101_401::where('hn', $value->d)->where('vstdate', $value->i)->update(
                                [
                                    'rep_error'    => $value->m,
                                    'rep_pay'      => $value->af,
                                    'rep_nopay'    => $value->ag,
                                    'rep_doc'      => $value->STMdoc,
                                ]
                            );
                        }
                    }


                // Acc_debtor
                // $data_new = Acc_debtor::where('vstdate', $value->i)->where('account_code','1102050101.401')->get();
                // foreach ($data_new as $key => $value_new) {
                    // acc_1102050101_401::where('vn', $value_new->vn)->update(
                    //     [
                    //         'rep_error'    => $value_new->rep_error,
                    //         'rep_pay'      => $value_new->rep_pay,
                    //         'rep_nopay'    => $value_new->rep_nopay,
                    //         'rep_doc'      => $value_new->rep_doc,
                    //     ]
                    // );
                // }



                }
            } catch (Exception $e) {
                $error_code = $e->errorInfo[1];
                return back()->withErrors('There was a problem uploading the data!');
            }






            D_ofc_repexcel::truncate();

            return response()->json([
                'status'    => '200',
            ]);
        // return redirect()->back();
    }



 }
