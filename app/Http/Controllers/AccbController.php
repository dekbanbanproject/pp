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
use App\Models\Acc_1102050101_402send;
use App\Models\Acc_dashboard;

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


class AccbController extends Controller
 {
    public function account_totalrep(Request $request)
    {
        $budget_year        = $request->budget_year;
        $acc_trimart_id     = $request->acc_trimart_id;
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
            $bg           = DB::table('budget_year')->where('years_now','Y')->first();
            $startdate    = $bg->date_begin;
            $enddate      = $bg->date_end;
            // $datashow = DB::select('
            //         SELECT month(a.vstdate) as months,year(a.vstdate) as year,l.MONTH_NAME
            //         ,count(distinct a.hn) as hn ,count(distinct a.vn) as vn ,count(distinct a.an) as an
            //         ,sum(a.income) as income ,sum(a.paid_money) as paid_money
            //         ,sum(a.income)-sum(a.discount_money)-sum(a.rcpt_money) as total ,sum(a.debit) as debit
            //         ,sum(a.income)-sum(a.discount_money)-sum(a.rcpt_money)-sum(a.fokliad) as debit402,sum(a.fokliad) as sumfokliad

            //         FROM acc_debtor a
            //         left outer join leave_month l on l.MONTH_ID = month(a.vstdate)
            //         WHERE a.vstdate between "'.$startdate.'" and "'.$enddate.'"
                    
            //         group by month(a.vstdate)
            //         order by a.vstdate desc;
            // ');
            $data_pangall = DB::select('SELECT * FROM acc_setpang WHERE active="TRUE"'); 
        } else {

            $bg           = DB::table('budget_year')->where('leave_year_id','=',$budget_year)->first();
            $startdate    = $bg->date_begin;
            $enddate      = $bg->date_end;

            // $datashow = DB::select('
            //         SELECT month(a.vstdate) as months,year(a.vstdate) as year,l.MONTH_NAME
            //         ,count(distinct a.hn) as hn ,count(distinct a.vn) as vn
            //         ,count(distinct a.an) as an ,sum(a.income) as income
            //         ,sum(a.paid_money) as paid_money
            //         ,sum(a.income)-sum(a.discount_money)-sum(a.rcpt_money) as total ,sum(a.debit) as debit
            //         FROM acc_debtor a
            //         left outer join leave_month l on l.MONTH_ID = month(a.vstdate)
            //         WHERE a.vstdate between "'.$startdate.'" and "'.$enddate.'"
                   
            //         group by month(a.vstdate)
            //         order by a.vstdate desc;
            // ');
            $data_pangall = DB::select('SELECT * FROM acc_setpang WHERE active="TRUE"'); 
        }
   
        return view('accb.account_totalrep', $data, [
            'startdate'         =>  $startdate,
            'enddate'           =>  $enddate,
            'leave_month_year'  =>  $leave_month_year,
     
            'dabudget_year'     =>  $dabudget_year,
            'budget_year'       =>  $budget_year,
            'data_pangall'      =>  $data_pangall,
            'y'                 =>  $y,
        ]);
    }
    public function account_totalrep_detail(Request $request,$id,$months,$year)
    {
        $budget_year        = $request->budget_year;
        $acc_trimart_id     = $request->acc_trimart_id;
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
            $bg           = DB::table('budget_year')->where('years_now','Y')->first();
            $startdate    = $bg->date_begin;
            $enddate      = $bg->date_end;
    
            $data_pangall = DB::select('SELECT * FROM acc_setpang WHERE active="TRUE"'); 
        } else {

            $bg           = DB::table('budget_year')->where('leave_year_id','=',$budget_year)->first();
            $startdate    = $bg->date_begin;
            $enddate      = $bg->date_end;

          
            $data_pangall = DB::select('SELECT * FROM acc_setpang WHERE active="TRUE"'); 
        }
   
        return view('accb.account_totalrep', $data, [
            'startdate'         =>  $startdate,
            'enddate'           =>  $enddate,
            'leave_month_year'  =>  $leave_month_year,
     
            'dabudget_year'     =>  $dabudget_year,
            'budget_year'       =>  $budget_year,
            'data_pangall'      =>  $data_pangall,
            'y'                 =>  $y,
        ]);
    }
    public function account_pk_dash(Request $request)
    {
            $budget_year   = $request->budget_year;
            $yearnew = date('Y');
            $year_old = date('Y')-1;
            $months_old  = ('10');
            $startdate = (''.$year_old.'-10-01');
            $enddate = (''.$yearnew.'-09-30');

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
                //  dd($dabudget_year);
              if ($budget_year == '') {

                  $datashow = DB::select('
                          SELECT MONTH(a.dchdate) as months,YEAR(a.dchdate) as years
                          ,count(DISTINCT a.an) as total_an,l.MONTH_NAME
                          ,sum(a.debit_total) as tung_looknee
                          FROM acc_1102050101_202 a
                          LEFT OUTER JOIN leave_month l on l.MONTH_ID = month(a.dchdate)
                          WHERE a.dchdate BETWEEN "'.$startdate.'" AND "'.$enddate.'"
                          AND a.account_code ="1102050101.202"
                          GROUP BY months ORDER BY a.dchdate DESC
                  ');
              } else {
                  $bg           = DB::table('budget_year')->where('leave_year_id','=',$budget_year)->first();
                  $startdate    = $bg->date_begin;
                  $enddate      = $bg->date_end;
                  // dd($enddate);
                  $datashow = DB::select('
                          SELECT MONTH(a.dchdate) as months,YEAR(a.dchdate) as years
                          ,count(DISTINCT a.an) as total_an,l.MONTH_NAME
                          ,sum(a.debit_total) as tung_looknee
                          FROM acc_1102050101_202 a
                          LEFT OUTER JOIN leave_month l on l.MONTH_ID = month(a.dchdate)
                          WHERE a.dchdate BETWEEN "'.$startdate.'" AND "'.$enddate.'"
                          AND a.account_code ="1102050101.202"
                          GROUP BY months ORDER BY a.dchdate DESC
                  ');
              }
          $data['pang']          =  DB::connection('mysql')->select('SELECT * FROM acc_setpang WHERE active ="TRUE" order by pang ASC');
          $data['sum_201']       = DB::table('acc_1102050101_201')->whereBetween('vstdate', [$startdate, $enddate])->sum('debit_total');
          $data['sum_202']       = DB::table('acc_1102050101_202')->whereBetween('dchdate', [$startdate, $enddate])->sum('debit_total');
          $data['sum_203']       = DB::table('acc_1102050101_203')->whereBetween('vstdate', [$startdate, $enddate])->sum('debit_total');
          $data['sum_209']       = DB::table('acc_1102050101_209')->whereBetween('vstdate', [$startdate, $enddate])->sum('debit_total');
          $data['sum_216']       = DB::table('acc_1102050101_216')->whereBetween('vstdate', [$startdate, $enddate])->sum('debit_total');
          $data['sum_2166']       = DB::table('acc_1102050101_2166')->whereBetween('vstdate', [$startdate, $enddate])->sum('debit_total');
          $data['sum_217']       = DB::table('acc_1102050101_217')->whereBetween('dchdate', [$startdate, $enddate])->sum('debit_total');

          $datashow = Acc_dashboard::where('months',' month("'. $startdate.'")')->get();
          $data['sumlooknee'] = $data['sum_201']+$data['sum_202']+$data['sum_203']+$data['sum_209']+$data['sum_216']+$data['sum_2166']+$data['sum_217']+$data['sum_201'];

      return view('dashboard.account_pk_dash',$data, [
        'datashow'          =>  $datashow,
        'startdate'         =>  $startdate,
        'enddate'           =>  $enddate,
      ]);
    }
    public function account_rep(Request $request)
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
            $bg           = DB::table('budget_year')->where('years_now','Y')->first();
            $startdate    = $bg->date_begin;
            $enddate      = $bg->date_end;
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

        return view('accb.account_rep',$data,[
            'startdate'         =>  $startdate,
            'enddate'           =>  $enddate,
            'leave_month_year'  =>  $leave_month_year,
            'datashow'          =>  $datashow,
            'dabudget_year'     =>  $dabudget_year,
            'budget_year'       =>  $budget_year,
            'y'                 =>  $y,
        ]);


    }

    public function account_rep_401(Request $request)
    {
        $datenow       = date('Y-m-d');
        $startdate     = $request->startdate;
        $enddate       = $request->enddate;
        $date          = date('Y-m-d');
        $new_day       = date('Y-m-d', strtotime($date . ' -10 day')); //ย้อนหลัง 1 วัน
        $data['users'] = User::get();
        if ($startdate =='') {
           $datashow = DB::select(
            'SELECT a.*,b.approval_code,b.pdx
                FROM acc_1102050101_401send a
                LEFT JOIN acc_debtor b ON b.vn = a.vn
                WHERE a.vstdate BETWEEN "'.$new_day.'" AND  "'.$date.'"
                GROUP BY a.vn
           ');
        //    LEFT OUTER JOIN d_fdh d ON d.vn = a.vn
        } else {
           $datashow = DB::select(
            'SELECT a.*,b.approval_code,b.pdx
            FROM acc_1102050101_401send a
            LEFT JOIN acc_debtor b ON b.vn = a.vn
            WHERE a.vstdate BETWEEN "'.$startdate.'" AND  "'.$enddate.'"
            GROUP BY a.vn
           ');
        }

        // $data_activeclaim        = Acc_function::where('pang','1102050101.401')->first();
        // $data['activeclaim']     = $data_activeclaim->claim_active;
        // $data['acc_function_id'] = $data_activeclaim->acc_function_id;


        return view('accb.account_rep_401', $data, [
            'startdate'     => $startdate,
            'enddate'       => $enddate,
            'datashow'      => $datashow,
            'startdate'     => $startdate,
            'enddate'       => $enddate
        ]);
    }
    public function account_rep_402(Request $request)
    {
        $datenow       = date('Y-m-d');
        $startdate     = $request->startdate;
        $enddate       = $request->enddate;
        $date          = date('Y-m-d');
        $new_day       = date('Y-m-d', strtotime($date . ' -30 day')); //ย้อนหลัง 1 วัน
        $data['users'] = User::get();
        if ($startdate =='') {
           $datashow = DB::select(
            'SELECT a.*,b.approval_code,b.pdx
                FROM acc_1102050101_402send a
                LEFT JOIN acc_debtor b ON b.an = a.an
                WHERE a.dchdate BETWEEN "'.$new_day.'" AND  "'.$date.'"
                GROUP BY a.an
           ');
        //    LEFT OUTER JOIN d_fdh d ON d.vn = a.vn
        } else {
           $datashow = DB::select(
            'SELECT a.*,b.approval_code,b.pdx
            FROM acc_1102050101_402send a
            LEFT JOIN acc_debtor b ON b.an = a.an
            WHERE a.dchdate BETWEEN "'.$startdate.'" AND  "'.$enddate.'"
            GROUP BY a.an
           ');
        }

        // $data_activeclaim        = Acc_function::where('pang','1102050101.401')->first();
        // $data['activeclaim']     = $data_activeclaim->claim_active;
        // $data['acc_function_id'] = $data_activeclaim->acc_function_id;


        return view('accb.account_rep_402', $data, [
            'startdate'     => $startdate,
            'enddate'       => $enddate,
            'datashow'      => $datashow,
            'startdate'     => $startdate,
            'enddate'       => $enddate
        ]);
    }

    public function account_rep_801(Request $request)
    {
        $datenow       = date('Y-m-d');
        $startdate     = $request->startdate;
        $enddate       = $request->enddate;
        $date          = date('Y-m-d');
        $new_day       = date('Y-m-d', strtotime($date . ' -10 day')); //ย้อนหลัง 1 วัน
        $data['users'] = User::get();
        if ($startdate =='') {
           $datashow = DB::select(
            'SELECT a.*,b.approval_code,b.pdx
                FROM acc_1102050102_801send a
                LEFT JOIN acc_debtor b ON b.vn = a.vn
                WHERE a.vstdate BETWEEN "'.$new_day.'" AND  "'.$date.'"
                GROUP BY a.vn
           ');
        } else {
           $datashow = DB::select(
            'SELECT a.*,b.approval_code,b.pdx
            FROM acc_1102050102_801send a
            LEFT JOIN acc_debtor b ON b.vn = a.vn
            WHERE a.vstdate BETWEEN "'.$startdate.'" AND  "'.$enddate.'"
            GROUP BY a.vn
           ');
        }

        return view('accb.account_rep_801', $data, [
            'startdate'     => $startdate,
            'enddate'       => $enddate,
            'datashow'      => $datashow,
            'startdate'     => $startdate,
            'enddate'       => $enddate
        ]);
    }
    public function account_rep_802(Request $request)
    {
        $datenow       = date('Y-m-d');
        $startdate     = $request->startdate;
        $enddate       = $request->enddate;
        $date          = date('Y-m-d');
        $new_day       = date('Y-m-d', strtotime($date . ' -10 day')); //ย้อนหลัง 1 วัน
        $data['users'] = User::get();
        if ($startdate =='') {
           $datashow = DB::select(
            'SELECT a.*,b.approval_code,b.pdx
                FROM acc_1102050102_802send a
                LEFT JOIN acc_debtor b ON b.an = a.an
                WHERE a.dchdate BETWEEN "'.$new_day.'" AND  "'.$date.'"
                GROUP BY a.an
           ');
        } else {
           $datashow = DB::select(
            'SELECT a.*,b.approval_code,b.pdx
            FROM acc_1102050102_802send a
            LEFT JOIN acc_debtor b ON b.an = a.an
            WHERE a.dchdate BETWEEN "'.$startdate.'" AND  "'.$enddate.'"
            GROUP BY a.an
           ');
        }

        return view('accb.account_rep_802', $data, [
            'startdate'     => $startdate,
            'enddate'       => $enddate,
            'datashow'      => $datashow,
            'startdate'     => $startdate,
            'enddate'       => $enddate
        ]);
    }
    public function account_rep_803(Request $request)
    {
        $datenow       = date('Y-m-d');
        $startdate     = $request->startdate;
        $enddate       = $request->enddate;
        $date          = date('Y-m-d');
        $new_day       = date('Y-m-d', strtotime($date . ' -10 day')); //ย้อนหลัง 1 วัน
        $data['users'] = User::get();
        if ($startdate =='') {
           $datashow = DB::select(
            'SELECT a.*,b.approval_code,b.pdx
                FROM acc_1102050102_803send a
                LEFT JOIN acc_debtor b ON b.vn = a.vn
                WHERE a.vstdate BETWEEN "'.$new_day.'" AND  "'.$date.'"
                GROUP BY a.vn
           ');
        } else {
           $datashow = DB::select(
            'SELECT a.*,b.approval_code,b.pdx
            FROM acc_1102050102_803send a
            LEFT JOIN acc_debtor b ON b.vn = a.vn
            WHERE a.vstdate BETWEEN "'.$startdate.'" AND  "'.$enddate.'"
            GROUP BY a.vn
           ');
        }

        return view('accb.account_rep_803', $data, [
            'startdate'     => $startdate,
            'enddate'       => $enddate,
            'datashow'      => $datashow,
            'startdate'     => $startdate,
            'enddate'       => $enddate
        ]);
    }
    public function account_rep_804(Request $request)
    {
        $datenow       = date('Y-m-d');
        $startdate     = $request->startdate;
        $enddate       = $request->enddate;
        $date          = date('Y-m-d');
        $new_day       = date('Y-m-d', strtotime($date . ' -10 day')); //ย้อนหลัง 1 วัน
        $data['users'] = User::get();
        if ($startdate =='') {
           $datashow = DB::select(
            'SELECT a.*,b.approval_code,b.pdx
                FROM acc_1102050102_804send a
                LEFT JOIN acc_debtor b ON b.an = a.an
                WHERE a.dchdate BETWEEN "'.$new_day.'" AND  "'.$date.'"
                GROUP BY a.an
           ');
        } else {
           $datashow = DB::select(
            'SELECT a.*,b.approval_code,b.pdx
            FROM acc_1102050102_804send a
            LEFT JOIN acc_debtor b ON b.an = a.an
            WHERE a.dchdate BETWEEN "'.$startdate.'" AND  "'.$enddate.'"
            GROUP BY a.an
           ');
        }

        return view('accb.account_rep_804', $data, [
            'startdate'     => $startdate,
            'enddate'       => $enddate,
            'datashow'      => $datashow,
            'startdate'     => $startdate,
            'enddate'       => $enddate
        ]);
    }
    public function account_rep_4011(Request $request)
    {
        $datenow       = date('Y-m-d');
        $startdate     = $request->startdate;
        $enddate       = $request->enddate;
        $date          = date('Y-m-d');
        $new_day       = date('Y-m-d', strtotime($date . ' -10 day')); //ย้อนหลัง 1 วัน
        $data['users'] = User::get();
        if ($startdate =='') {
           $datashow = DB::select(
            'SELECT a.*,b.approval_code,b.pdx,c.STMdoc,SUM(c.Total_amount) as total
                FROM acc_1102050101_4011send a
                LEFT JOIN acc_debtor b ON b.vn = a.vn           
                LEFT JOIN acc_stm_ti_total c ON c.HDBill_hn = a.hn AND (c.vstdate BETWEEN a.vstdate AND a.vstdate)
                WHERE a.vstdate BETWEEN "'.$new_day.'" AND  "'.$date.'"  
                GROUP BY a.vn
           ');
        //    LEFT JOIN acc_1102050101_4011 c ON c.vn = a.vn
        //    LEFT OUTER JOIN d_fdh d ON d.vn = a.vn
        } else {
           $datashow = DB::select(
            'SELECT a.*,b.approval_code,b.pdx,c.STMdoc,SUM(c.Total_amount) as total
                FROM acc_1102050101_4011send a
                LEFT JOIN acc_debtor b ON b.vn = a.vn           
                LEFT JOIN acc_stm_ti_total c ON c.HDBill_hn = a.hn AND (c.vstdate BETWEEN a.vstdate AND a.vstdate)
                WHERE a.vstdate BETWEEN "'.$startdate.'" AND  "'.$enddate.'" 
                GROUP BY a.vn 
           ');
        }
        // AND c.HDBill_TBill_HDflag IN("COC")
        return view('accb.account_rep_4011', $data, [
            'startdate'     => $startdate,
            'enddate'       => $enddate,
            'datashow'      => $datashow,
            'startdate'     => $startdate,
            'enddate'       => $enddate
        ]);
    }
    public function account_rep_4022(Request $request)
    {
        $datenow       = date('Y-m-d');
        $startdate     = $request->startdate;
        $enddate       = $request->enddate;
        $date          = date('Y-m-d');
        $new_day       = date('Y-m-d', strtotime($date . ' -20 day')); //ย้อนหลัง 1 วัน
        $data['users'] = User::get();
        if ($startdate =='') {
           $datashow = DB::select(
            'SELECT a.*,b.approval_code,b.pdx,c.STMDoc,c.Total_amount as total
                FROM acc_1102050101_4022send a
                LEFT JOIN acc_debtor b ON b.an = a.an
                LEFT JOIN acc_stm_ti_total c ON c.HDBill_hn = a.hn AND (c.vstdate BETWEEN a.vstdate AND a.dchdate)
                WHERE a.dchdate BETWEEN "'.$new_day.'" AND  "'.$date.'"  
           
                GROUP BY a.hn,a.rxdate 
           ');
        //    SELECT b.STMDoc,SUM(b.Total_amount) as total
        //    FROM acc_1102050101_4022 a
        //    LEFT JOIN acc_stm_ti_total b ON b.HDBill_hn = a.hn AND (b.vstdate BETWEEN a.vstdate AND a.dchdate)
        //    WHERE b.HDBill_TBill_HDflag IN("CIC")
        //    GROUP BY b.STMDoc
        //    ORDER BY STMDoc DESC
        //    LEFT OUTER JOIN d_fdh d ON d.vn = a.vn
        } else {
           $datashow = DB::select(
            'SELECT a.*,b.approval_code,b.pdx,c.STMDoc,c.Total_amount as total
                FROM acc_1102050101_4022send a
                LEFT JOIN acc_debtor b ON b.an = a.an
                LEFT JOIN acc_stm_ti_total c ON c.HDBill_hn = a.hn AND (c.vstdate BETWEEN a.vstdate AND a.dchdate)
                WHERE a.dchdate BETWEEN "'.$startdate.'" AND  "'.$enddate.'" 
                 GROUP BY a.hn,a.rxdate
           ');
        }
        //  .$enddate.'" AND c.HDBill_TBill_HDflag IN("CIC")
        return view('accb.account_rep_4022', $data, [
            'startdate'     => $startdate,
            'enddate'       => $enddate,
            'datashow'      => $datashow,
            'startdate'     => $startdate,
            'enddate'       => $enddate
        ]);
    }



 }
