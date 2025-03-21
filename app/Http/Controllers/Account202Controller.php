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
use App\Models\Acc_ucep24;
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
use App\Models\Acc_ucep_24;
use App\Models\Acc_db_202;
use App\Models\Acc_account_total;
use App\Models\Acc_debtor_log;

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


class Account202Controller extends Controller
 {
     // *************************** 202 ********************************************
     public function account_pkucs202_pull(Request $request)
     {
        $datenow   = date('Y-m-d');
        $months    = date('m');
        $year      = date('Y');
        $newday    = date('Y-m-d', strtotime($datenow . ' -2 Day')); //ย้อนหลัง 1 สัปดาห์
        $startdate = $request->startdate;
        $enddate   = $request->enddate;
         if ($startdate == '') {
             // $acc_debtor = Acc_debtor::where('stamp','=','N')->whereBetween('dchdate', [$datenow, $datenow])->get();
             $acc_debtor = DB::select('
                 SELECT a.*
                 from acc_debtor a
                 WHERE account_code="1102050101.202"
                 AND a.dchdate BETWEEN "' . $newday . '" AND "' . $datenow . '"
                 AND a.debit_total > 0
                 group by a.an
                 order by dchdate desc;

             ');
             $data['count_claim'] = Acc_debtor::where('active_claim','=','Y')->where('account_code','=','1102050101.202')->whereBetween('dchdate', [$newday, $datenow])->count();
             $data['count_noclaim'] = Acc_debtor::where('active_claim','=','N')->where('account_code','=','1102050101.202')->whereBetween('dchdate', [$newday, $datenow])->count();
         } else {
             $acc_debtor = DB::select(
                'SELECT *
                 from acc_debtor a
                 WHERE a.account_code="1102050101.202" AND a.dchdate BETWEEN "' . $startdate . '" AND "' . $enddate . '"
                 AND a.debit_total > 0
                 group by a.an
                 order by a.dchdate desc;
             ');
             $data['count_claim'] = Acc_debtor::where('active_claim','=','Y')->where('account_code','=','1102050101.202')->whereBetween('dchdate', [$startdate, $enddate])->count();
             $data['count_noclaim'] = Acc_debtor::where('active_claim','=','N')->where('account_code','=','1102050101.202')->whereBetween('dchdate', [$startdate, $enddate])->count();
            //  a.acc_debtor_id,a.an,a.vn,a.hn,a.cid,a.ptname,a.pttype,a.dchdate,a.income,a.debit_total,a.debit_instument,a.debit_drug,a.debit_toa,a.debit_refer,a.debit_ucep
         }
         $data_activeclaim        = Acc_function::where('pang','1102050101.202')->first();
         $data['activeclaim']     = $data_activeclaim->claim_active;
         $data['acc_function_id'] = $data_activeclaim->acc_function_id;

         return view('account_202.account_pkucs202_pull',$data,[
             'startdate'     =>     $startdate,
             'enddate'       =>     $enddate,
             'acc_debtor'      =>     $acc_debtor,
         ]);
     }
     function account_202_switch(Request $request)
     {
         // $id = $request->idfunc;
         Acc_function::where('pang','1102050101.202')->update(['claim_active'=> $request->onoff]);
         return response()->json([
             'status'    => '200'
         ]);
     }
     public function account_202_checksit(Request $request)
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
     public function account_pkucs202_search(Request $request)
     {
         $datenow      = date('Y-m-d');
         $startdate    = $request->startdate;
         $enddate      = $request->enddate;
         $date         = date('Y-m-d');
         $new_day      = date('Y-m-d', strtotime($date . ' -5 day')); //ย้อนหลัง 1 วัน
         $data['users'] = User::get();
         if ($startdate =='') {
            $datashow = DB::select('
                SELECT * from acc_1102050101_202
                WHERE dchdate BETWEEN "'.$new_day.'" AND  "'.$date.'"

            ');
         } else {
            $datashow = DB::select('
                SELECT * from acc_1102050101_202
                WHERE dchdate BETWEEN "'.$startdate.'" AND  "'.$enddate.'"

            ');
         }




         return view('account_202.account_pkucs202_search', $data, [
             'startdate'     => $startdate,
             'enddate'       => $enddate,
             'datashow'      => $datashow,
             'startdate'     => $startdate,
             'enddate'       => $enddate
         ]);
     }
     public function account_pkucs202_pulldata_09_07_67(Request $request)
     {
        $date              = date('Y-m-d H:i:s');
        $startdate         = $request->datepicker;
        $enddate           = $request->datepicker2;
        $data_main = DB::connection('mysql2')->select('
                SELECT o.an,a.vn,o.rxdate,o.rxtime,a.vstdate,a.vsttime,i.dchdate
                FROM opitemrece o
                LEFT JOIN ipt i on i.an = o.an
                LEFT JOIN ovst a on a.an = o.an
                left JOIN er_regist e on e.vn = i.vn
                LEFT JOIN pttype p on p.pttype = i.pttype
                WHERE i.dchdate BETWEEN "'.$startdate.'" and "'.$enddate.'"
                AND o.an is not null
                AND p.hipdata_code ="ucs"
                AND DATEDIFF(o.rxdate,a.vstdate)<="1"
                AND TIMEDIFF(o.rxtime,a.vsttime)<="24"
                AND e.er_emergency_level_id IN("1","2")
                AND o.income NOT IN("02")
                GROUP BY o.an
        ');
        Acc_ucep_24::truncate();
        foreach ($data_main as $key => $val) {
                $data_ = DB::connection('mysql2')->select('
                        SELECT o.an,a.vn,o.rxdate,o.rxtime,a.vstdate,a.vsttime,i.dchdate
                        ,(
                                SELECT SUM(o.sum_price) sum_price
                                FROM opitemrece o
                                LEFT JOIN ipt i on i.an = o.an
                                LEFT JOIN ovst a on a.an = o.an
                                left JOIN er_regist e on e.vn = i.vn
                                LEFT JOIN pttype p on p.pttype = i.pttype
                                WHERE o.an = "'.$val->an.'"
                                AND o.an is not null
                                AND p.hipdata_code ="ucs"
                                AND o.income NOT IN("02")
                        ) as sum_price_ipd
                        ,(
                            SELECT SUM(o.sum_price) sum_price
                            FROM opitemrece o
                            LEFT JOIN ipt i on i.an = o.an
                            LEFT JOIN ovst a on a.an = o.an
                            left JOIN er_regist e on e.vn = i.vn
                            LEFT JOIN pttype p on p.pttype = i.pttype
                            WHERE o.an = "'.$val->an.'"
                            AND o.an is not null
                            AND p.hipdata_code ="ucs"
                            AND DATEDIFF(o.rxdate,a.vstdate)<="1"
                            AND TIMEDIFF(o.rxtime,a.vsttime)<="24"
                            AND e.er_emergency_level_id IN("1","2")
                            AND o.income NOT IN("02")
                        ) as sum_price_ucep_all
                        ,(
                                SELECT SUM(o.sum_price) sum_price
                                FROM opitemrece o
                                LEFT JOIN ipt i on i.an = o.an
                                LEFT JOIN ovst a on a.an = o.an
                                left JOIN er_regist e on e.vn = i.vn
                                LEFT JOIN pttype p on p.pttype = i.pttype
                                WHERE o.an = "'.$val->an.'"
                                AND (o.income IN("02") OR icode IN("1560016","1540073","1530005","3001412","3001417","3010829","3011068","3010864","3010861","3010862","3010863","3011069","3011012","3011070"))
                                AND o.an is not null
                                AND p.hipdata_code ="ucs"
                                AND DATEDIFF(o.rxdate,a.vstdate)<="1"
                                AND TIMEDIFF(o.rxtime,a.vsttime)<="24"
                                AND e.er_emergency_level_id IN("1","2")
                                AND o.income NOT IN("02")
                        ) as sum_price_ucep_cr
                        ,(
                                SELECT SUM(o.sum_price) sum_price
                                FROM opitemrece o
                                LEFT JOIN ipt i on i.an = o.an
                                LEFT JOIN ovst a on a.an = o.an
                                left JOIN er_regist e on e.vn = i.vn
                                LEFT JOIN pttype p on p.pttype = i.pttype
                                WHERE o.an = "'.$val->an.'"
                                AND o.income NOT IN("02") AND o.icode NOT IN("1560016","1540073","1530005","3001412","3001417","3010829","3011068","3010864","3010861","3010862","3010863","3011069","3011012","3011070")
                                AND o.an is not null
                                AND p.hipdata_code ="ucs"
                                AND DATEDIFF(o.rxdate,a.vstdate)<="1"
                                AND TIMEDIFF(o.rxtime,a.vsttime)<="24"
                                AND e.er_emergency_level_id IN("1","2")
                                AND o.income NOT IN("02")
                        ) as sum_price_ucep_normal

                        FROM opitemrece o
                        LEFT JOIN ipt i on i.an = o.an
                        LEFT JOIN ovst a on a.an = o.an
                        left JOIN er_regist e on e.vn = i.vn
                        LEFT JOIN pttype p on p.pttype = i.pttype
                        WHERE o.an = "'.$val->an.'"
                        AND o.an is not null
                        AND p.hipdata_code ="ucs"
                        AND DATEDIFF(o.rxdate,a.vstdate)<="1"
                        AND TIMEDIFF(o.rxtime,a.vsttime)<="24"
                        AND e.er_emergency_level_id IN("1","2")
                        AND p.pttype NOT IN ("31","33","36","39")
                        AND o.income NOT IN("02")
                        GROUP BY o.an
                ');
                foreach ($data_ as $key => $val2) {
                    Acc_ucep_24::insert([
                            'vn'                        => $val2->vn,
                            'an'                        => $val2->an,
                            'vstdate'                   => $val2->vstdate,
                            'vsttime'                   => $val2->vsttime,
                            'dchdate'                   => $val2->dchdate,
                            'rxdate'                    => $val2->rxdate,
                            'rxtime'                    => $val2->rxtime,
                            'sum_price_ipd'             => $val2->sum_price_ipd,
                            'sum_price_ucep_all'        => $val2->sum_price_ucep_all,
                            'sum_price_ucep_cr'         => $val2->sum_price_ucep_cr,
                            'sum_price_ucep_normal'     => $val2->sum_price_ucep_normal,
                            'sum_price_ipd_202'         => $val2->sum_price_ipd - $val2->sum_price_ucep_all,
                    ]);
                }

        }

        $acc_debtor = DB::connection('mysql2')->select('
                    SELECT ip.vn,a.an,a.hn,pt.cid,concat(pt.pname,pt.fname," ",pt.lname) ptname,a.regdate as admdate,a.dchdate,v.vstdate,op.income as income_group
                    ,ipt.pttype,ipt.pttype_number,ipt.max_debt_amount ,ip.rw,ip.adjrw,ip.adjrw*8350 as total_adjrw_income ,ipt.nhso_ownright_pid ,a.income,a.uc_money,a.rcpt_money,a.discount_money

                    ,"01" as acc_code
                    ,"1102050101.202" as account_code
                    ,"UC ใน CUP" as account_name

                    ,CASE
                    WHEN  ipt.pttype_number ="1" AND ipt.pttype IN ("31","33","36","39") THEN ipt.max_debt_amount
                    ELSE a.income - ipt.max_debt_amount
                    END as debit_prb

                    ,CASE
					WHEN  ipt.pttype_number ="2" THEN a.income - a.rcpt_money - a.discount_money - ipt.max_debt_amount
                    ELSE a.income - a.rcpt_money - a.discount_money
                    END as debit

                    ,sum(if(op.icode IN("3002895","3002896","3002897","3002898","3002909","3002910","3002911","3002912","3002913","3002914","3002915","3002916","3002918"),sum_price,0)) as portex
                    ,sum(if(op.income="02",sum_price,0)) as debit_instument
                    ,sum(if(op.icode IN("1560016","1540073","1530005"),sum_price,0)) as debit_drug
                    ,sum(if(op.icode IN ("3001412","3001417"),sum_price,0)) as debit_toa
                    ,sum(if(op.icode IN("3010829","3011068","3010864","3010861","3010862","3010863","3011069","3011012","3011070"),sum_price,0)) as debit_refer
                    ,(SELECT SUM(opp.sum_price) FROM opitemrece opp LEFT JOIN nondrugitems nn ON nn.icode = opp.icode WHERE opp.an = a.an AND nn.nhso_adp_code IN("5601","9104","5402","5403","5406","5609")) as nonpay

                    from ipt ip
                    LEFT JOIN an_stat a ON ip.an = a.an
                    LEFT JOIN patient pt on pt.hn=a.hn
                    LEFT JOIN pttype ptt on a.pttype=ptt.pttype
                    LEFT JOIN pttype_eclaim ec on ec.code=ptt.pttype_eclaim_id
                    LEFT JOIN ipt_pttype ipt ON ipt.an = a.an
                    LEFT JOIN opitemrece op ON ip.an = op.an
                    LEFT JOIN vn_stat v on v.vn = ip.vn
                WHERE a.dchdate BETWEEN "' . $startdate . '" AND "' . $enddate . '"
                AND ipt.pttype IN(SELECT pttype FROM pkbackoffice.acc_setpang_type WHERE pang ="1102050101.202" AND opdipd ="IPD" AND pttype <>"")
                GROUP BY a.an
        ');

         foreach ($acc_debtor as $key => $value) {
            // $count_pttype = DB::connection('mysql2')->select('SELECT COUNT(an) as C_an FROM  ipt_pttype WHERE an = "'.$value->an.'" ');
            $count_pttype = DB::connection('mysql2')->table('ipt_pttype')->where('an', $value->an)->count();
            // $total_ = $value->debit-$value->debit_drug-$value->debit_instument-$value->debit_toa-$value->debit_refer-$value->debit_ucep;
            // dd($count_pttype);
            if ($count_pttype > 1) {
                $check = Acc_debtor::where('an', $value->an)->where('account_code', '1102050101.202')->count();
                    if ($check == 0) {
                        if ($value->pttype_number == '2') {
                            if ($value->debit_toa > 0 ) {
                            } else {
                                if ($value->debit > 0) {

                                    if ($value->debit_prb > $value->debit) {
                                        # code...
                                    } else {
                                        Acc_debtor::insert([
                                            'hn'                 => $value->hn,
                                            'an'                 => $value->an,
                                            'vn'                 => $value->vn,
                                            'cid'                => $value->cid,
                                            'ptname'             => $value->ptname,
                                            'pttype'             => $value->pttype,
                                            'vstdate'            => $value->vstdate,
                                            'rxdate'             => $value->admdate,
                                            'dchdate'            => $value->dchdate,
                                            'acc_code'           => $value->acc_code,
                                            'account_code'       => $value->account_code,
                                            'account_name'       => $value->account_name,
                                            'income'             => $value->income,
                                            'uc_money'           => $value->uc_money,
                                            'discount_money'     => $value->discount_money,
                                            'rcpt_money'         => $value->rcpt_money,
                                            'debit'              => $value->debit,
                                            'debit_drug'         => $value->debit_drug,
                                            'debit_instument'    => $value->debit_instument,
                                            'debit_toa'          => $value->debit_toa,
                                            'debit_refer'        => $value->debit_refer,
                                            // 'debit_total'        => $value->debit-$value->debit_drug-$value->debit_instument-$value->debit_toa-$value->debit_refer,
                                            'debit_total'        => $value->debit+$value->nonpay,
                                            // 'debit_ucep'         => $value->debit_ucep,
                                            'max_debt_amount'    => $value->max_debt_amount,
                                            'rw'                 => $value->rw,
                                            'adjrw'              => $value->adjrw,
                                            'total_adjrw_income' => $value->total_adjrw_income,
                                            'acc_debtor_userid'  => Auth::user()->id
                                        ]);
                                    }
                                }
                            }
                        } else {
                            if ($value->debit_toa > 0 ) {
                            } else {

                                if ($value->debit > 0) {
                                    Acc_debtor::insert([
                                        'hn'                 => $value->hn,
                                        'an'                 => $value->an,
                                        'vn'                 => $value->vn,
                                        'cid'                => $value->cid,
                                        'ptname'             => $value->ptname,
                                        'pttype'             => $value->pttype,
                                        'vstdate'            => $value->vstdate,
                                        'rxdate'             => $value->admdate,
                                        'dchdate'            => $value->dchdate,
                                        'acc_code'           => $value->acc_code,
                                        'account_code'       => $value->account_code,
                                        'account_name'       => $value->account_name,
                                        'income'             => $value->income,
                                        'uc_money'           => $value->uc_money,
                                        'discount_money'     => $value->discount_money,
                                        'rcpt_money'         => $value->rcpt_money,
                                        'debit'              => $value->debit,
                                        'debit_drug'         => $value->debit_drug,
                                        'debit_instument'    => $value->debit_instument,
                                        'debit_toa'          => $value->debit_toa,
                                        'debit_refer'        => $value->debit_refer,
                                        // 'debit_total'        => $value->debit,
                                        'debit_total'        => $value->debit-($value->debit_drug-$value->debit_instument-$value->debit_toa-$value->debit_refer)+$value->nonpay,
                                        // 'debit_ucep'         => $value->debit_ucep,
                                        'max_debt_amount'    => $value->max_debt_amount,
                                        'rw'                 => $value->rw,
                                        'adjrw'              => $value->adjrw,
                                        'total_adjrw_income' => $value->total_adjrw_income,
                                        'acc_debtor_userid'  => Auth::user()->id
                                    ]);
                                }
                            }
                        }
                    }
            } else {
                if ($value->debit > 0) {
                        $check22 = Acc_debtor::where('an', $value->an)->where('account_code', '1102050101.202')->count();
                        if ($check22 == '0') {
                            if ($value->debit_toa > '0' ) {
                            } else {
                                if ($value->debit < '1') {
                                } else {
                                    Acc_debtor::insert([
                                        'hn'                 => $value->hn,
                                        'an'                 => $value->an,
                                        'vn'                 => $value->vn,
                                        'cid'                => $value->cid,
                                        'ptname'             => $value->ptname,
                                        'pttype'             => $value->pttype,
                                        'vstdate'            => $value->vstdate,
                                        'rxdate'             => $value->admdate,
                                        'dchdate'            => $value->dchdate,
                                        'acc_code'           => $value->acc_code,
                                        'account_code'       => $value->account_code,
                                        'account_name'       => $value->account_name,
                                        'income'             => $value->income,
                                        'uc_money'           => $value->uc_money,
                                        'discount_money'     => $value->discount_money,
                                        'rcpt_money'         => $value->rcpt_money,
                                        'debit'              => $value->debit,
                                        'debit_drug'         => $value->debit_drug,
                                        'debit_instument'    => $value->debit_instument,
                                        'debit_toa'          => $value->debit_toa,
                                        'debit_refer'        => $value->debit_refer,
                                        'debit_total'        => $value->debit+$value->nonpay,
                                        'nonpay'             => $value->nonpay,
                                        'max_debt_amount'    => $value->max_debt_amount,
                                        'rw'                 => $value->rw,
                                        'adjrw'              => $value->adjrw,
                                        'total_adjrw_income' => $value->total_adjrw_income,
                                        'acc_debtor_userid'  => Auth::user()->id
                                    ]);
                                }
                            }
                        }
                }
            }
         }
         $acc_ucep = DB::connection('mysql')->select('SELECT an,sum_price_ipd,sum_price_ucep_all FROM acc_ucep_24');
         foreach ($acc_ucep as $key => $val_up) {
            $count_u = Acc_debtor::where('account_code', '1102050101.202')->where('an', $val_up->an)->count();
            if ($count_u > 0) {
                Acc_debtor::where('an',$val_up->an)->where('account_code', '1102050101.202')->update([
                    'debit_total'    => $val_up->sum_price_ipd - $val_up->sum_price_ucep_all,
                    'debit_ucep'     => $val_up->sum_price_ucep_all,
                ]);
            }

         }
        $acc_norget = DB::connection('mysql')->select('
            SELECT an,debit,debit_instument,debit_drug,debit_toa,debit_refer,debit_ucep,nonpay
            FROM acc_debtor
            WHERE dchdate BETWEEN "' . $startdate . '" AND "' . $enddate . '"
            AND account_code = "1102050101.202"
            AND (debit_ucep IS NULL OR debit_ucep = "")
        ');
        foreach ($acc_norget as $key => $value_get) {
                    Acc_debtor::where('an',$value_get->an)->where('account_code', '1102050101.202')->update([
                        'debit_total'    => (($value_get->debit - $value_get->debit_instument) - ($value_get->debit_drug - $value_get->debit_toa)) - ($value_get->debit_refer)+($value_get->nonpay),
                        'debit_cr'       => ($value_get->debit_instument + $value_get->debit_drug) + ($value_get->debit_toa + $value_get->debit_refer),
                    ]);
                    Acc_debtor::where('an',$value_get->an)->where('debit_total', '<', 1)->delete();
        }
        // $deleted = DB::table('users')->where('votes', '>', 100)->delete();
        Acc_debtor::where('account_code', '1102050101.202')->where('debit_total', '<', 1)->delete();
        return response()->json([

            'status'    => '200'
        ]);
     }
     public function account_pkucs202_pulldata(Request $request)
     {
        $date              = date('Y-m-d H:i:s');
        $datenow           = date('Y-m-d');
        $datatime          = date('H:m:s');
        $ip                = $request->ip();
        $startdate         = $request->datepicker;
        $enddate           = $request->datepicker2;
        $data_main = DB::connection('mysql2')->select('
                SELECT o.an,a.vn,o.rxdate,o.rxtime,a.vstdate,a.vsttime,i.dchdate
                FROM opitemrece o
                LEFT JOIN ipt i on i.an = o.an
                LEFT JOIN ovst a on a.an = o.an
                left JOIN er_regist e on e.vn = i.vn
                LEFT JOIN pttype p on p.pttype = i.pttype
                WHERE i.dchdate BETWEEN "'.$startdate.'" and "'.$enddate.'"
                AND o.an is not null
                AND p.hipdata_code ="ucs"
                AND DATEDIFF(o.rxdate,a.vstdate)<="1"
                AND TIMEDIFF(o.rxtime,a.vsttime)<="24"
                AND e.er_emergency_level_id IN("1","2")
                AND o.income NOT IN("02")
                GROUP BY o.an
        ');
        Acc_ucep_24::truncate();
        foreach ($data_main as $key => $val) {
                $data_ = DB::connection('mysql2')->select('
                        SELECT o.an,a.vn,o.rxdate,o.rxtime,a.vstdate,a.vsttime,i.dchdate
                        ,(
                                SELECT SUM(o.sum_price) sum_price
                                FROM opitemrece o
                                LEFT JOIN ipt i on i.an = o.an
                                LEFT JOIN ovst a on a.an = o.an
                                left JOIN er_regist e on e.vn = i.vn
                                LEFT JOIN pttype p on p.pttype = i.pttype
                                WHERE o.an = "'.$val->an.'"
                                AND o.an is not null
                                AND p.hipdata_code ="ucs"
                                AND o.income NOT IN("02")
                        ) as sum_price_ipd
                        ,(
                            SELECT SUM(o.sum_price) sum_price
                            FROM opitemrece o
                            LEFT JOIN ipt i on i.an = o.an
                            LEFT JOIN ovst a on a.an = o.an
                            left JOIN er_regist e on e.vn = i.vn
                            LEFT JOIN pttype p on p.pttype = i.pttype
                            WHERE o.an = "'.$val->an.'"
                            AND o.an is not null
                            AND p.hipdata_code ="ucs"
                            AND DATEDIFF(o.rxdate,a.vstdate)<="1"
                            AND TIMEDIFF(o.rxtime,a.vsttime)<="24"
                            AND e.er_emergency_level_id IN("1","2")
                            AND o.income NOT IN("02")
                        ) as sum_price_ucep_all
                        ,(
                                SELECT SUM(o.sum_price) sum_price
                                FROM opitemrece o
                                LEFT JOIN ipt i on i.an = o.an
                                LEFT JOIN ovst a on a.an = o.an
                                left JOIN er_regist e on e.vn = i.vn
                                LEFT JOIN pttype p on p.pttype = i.pttype
                                WHERE o.an = "'.$val->an.'"
                                AND (o.income IN("02") OR icode IN("1560016","1540073","1530005","3001412","3001417","3010829","3011068","3010864","3010861","3010862","3010863","3011069","3011012","3011070"))
                                AND o.an is not null
                                AND p.hipdata_code ="ucs"
                                AND DATEDIFF(o.rxdate,a.vstdate)<="1"
                                AND TIMEDIFF(o.rxtime,a.vsttime)<="24"
                                AND e.er_emergency_level_id IN("1","2")
                                AND o.income NOT IN("02")
                        ) as sum_price_ucep_cr
                        ,(
                                SELECT SUM(o.sum_price) sum_price
                                FROM opitemrece o
                                LEFT JOIN ipt i on i.an = o.an
                                LEFT JOIN ovst a on a.an = o.an
                                left JOIN er_regist e on e.vn = i.vn
                                LEFT JOIN pttype p on p.pttype = i.pttype
                                WHERE o.an = "'.$val->an.'"
                                AND o.income NOT IN("02") AND o.icode NOT IN("1560016","1540073","1530005","3001412","3001417","3010829","3011068","3010864","3010861","3010862","3010863","3011069","3011012","3011070")
                                AND o.an is not null
                                AND p.hipdata_code ="ucs"
                                AND DATEDIFF(o.rxdate,a.vstdate)<="1"
                                AND TIMEDIFF(o.rxtime,a.vsttime)<="24"
                                AND e.er_emergency_level_id IN("1","2")
                                AND o.income NOT IN("02")
                        ) as sum_price_ucep_normal

                        FROM opitemrece o
                        LEFT JOIN ipt i on i.an = o.an
                        LEFT JOIN ovst a on a.an = o.an
                        left JOIN er_regist e on e.vn = i.vn
                        LEFT JOIN pttype p on p.pttype = i.pttype
                        WHERE o.an = "'.$val->an.'"
                        AND o.an is not null
                        AND p.hipdata_code ="ucs"
                        AND DATEDIFF(o.rxdate,a.vstdate)<="1"
                        AND TIMEDIFF(o.rxtime,a.vsttime)<="24"
                        AND e.er_emergency_level_id IN("1","2")
                        AND p.pttype NOT IN ("31","33","36","39")
                        AND o.income NOT IN("02")
                        GROUP BY o.an
                ');
                foreach ($data_ as $key => $val2) {
                    Acc_ucep_24::insert([
                            'vn'                        => $val2->vn,
                            'an'                        => $val2->an,
                            'vstdate'                   => $val2->vstdate,
                            'vsttime'                   => $val2->vsttime,
                            'dchdate'                   => $val2->dchdate,
                            'rxdate'                    => $val2->rxdate,
                            'rxtime'                    => $val2->rxtime,
                            'sum_price_ipd'             => $val2->sum_price_ipd,
                            'sum_price_ucep_all'        => $val2->sum_price_ucep_all,
                            'sum_price_ucep_cr'         => $val2->sum_price_ucep_cr,
                            'sum_price_ucep_normal'     => $val2->sum_price_ucep_normal,
                            'sum_price_ipd_202'         => $val2->sum_price_ipd - $val2->sum_price_ucep_all,
                    ]);
                }

        }

        $acc_debtor = DB::connection('mysql2')->select('
                    SELECT ip.vn,a.an,a.hn,pt.cid,concat(pt.pname,pt.fname," ",pt.lname) ptname,a.regdate as admdate,a.dchdate,v.vstdate,op.income as income_group
                    ,ipt.pttype,ipt.pttype_number,ipt.max_debt_amount ,ip.rw,ip.adjrw,ip.adjrw*8350 as total_adjrw_income ,ipt.nhso_ownright_pid ,a.income,a.uc_money,a.rcpt_money,a.discount_money

                    ,"01" as acc_code
                    ,"1102050101.202" as account_code
                    ,"UC ใน CUP" as account_name

                    ,CASE
                    WHEN  ipt.pttype_number ="1" AND ipt.pttype IN ("31","33","36","39") THEN ipt.max_debt_amount
                    ELSE a.income - ipt.max_debt_amount
                    END as debit_prb

                    ,(a.income-a.rcpt_money-a.discount_money-IFNULL(ipt.max_debt_amount,"0"))-
                    (sum(if(op.income="02",sum_price,0))) -
                    (sum(if(op.icode IN("1560016","1540073","1530005"),sum_price,0))) -
                    (sum(if(op.icode IN("3001412","3001417"),sum_price,0))) -
                    (sum(if(op.icode IN("3010829","3011068","3010864","3010861","3010862","3010863","3011069","3011012","3011070"),sum_price,0))) +
                    (sum(if(op.icode IN("3002895","3002896","3002897","3002898","3002909","3002910","3002911","3002912","3002913","3002914","3002915","3002916","3002918"),sum_price,0)))
                    as debit

                    ,sum(if(op.icode IN("3002895","3002896","3002897","3002898","3002909","3002910","3002911","3002912","3002913","3002914","3002915","3002916","3002918"),sum_price,0)) as portex
                    ,sum(if(op.income="02",sum_price,0)) as debit_instument
                    ,sum(if(op.icode IN("1560016","1540073","1530005"),sum_price,0)) as debit_drug
                    ,sum(if(op.icode IN ("3001412","3001417"),sum_price,0)) as debit_toa
                    ,sum(if(op.icode IN("3010829","3011068","3010864","3010861","3010862","3010863","3011069","3011012","3011070"),sum_price,0)) as debit_refer
                    ,(SELECT SUM(opp.sum_price) FROM opitemrece opp LEFT JOIN nondrugitems nn ON nn.icode = opp.icode WHERE opp.an = a.an AND nn.nhso_adp_code IN("5601","9104","5402","5403","5406","5609","5307","5705","72940","4805")) as nonpay

                    ,(SELECT
                        SUM(opp.sum_price) sum_price
                        FROM opitemrece opp
                        LEFT JOIN ipt i2 on i2.an = opp.an
                        LEFT JOIN ovst a2 on a2.an = opp.an
                        left JOIN er_regist e2 on e2.vn = i2.vn
                        LEFT JOIN pttype p2 on p2.pttype = i2.pttype
                        WHERE i2.an = ip.an
                        AND opp.an is not null
                        AND p2.hipdata_code ="ucs"
                        AND DATEDIFF(opp.rxdate,a2.vstdate)<="1"
                        AND TIMEDIFF(opp.rxtime,a2.vsttime)<="24"
                        AND e2.er_emergency_level_id IN("1","2")
                        AND opp.income NOT IN("02")
                    ) as debit_ucep



                    from ipt ip
                    LEFT JOIN an_stat a ON ip.an = a.an
                    LEFT JOIN patient pt on pt.hn=a.hn
                    LEFT JOIN pttype ptt on a.pttype=ptt.pttype
                    LEFT JOIN pttype_eclaim ec on ec.code=ptt.pttype_eclaim_id
                    LEFT JOIN ipt_pttype ipt ON ipt.an = a.an
                    LEFT JOIN opitemrece op ON ip.an = op.an
                    LEFT JOIN vn_stat v on v.vn = ip.vn
                WHERE a.dchdate BETWEEN "' . $startdate . '" AND "' . $enddate . '"
                AND ipt.pttype IN(SELECT pttype FROM pkbackoffice.acc_setpang_type WHERE pang ="1102050101.202" AND opdipd ="IPD" AND pttype <>"")
                GROUP BY a.an
        ');
          
        $bgs_year      = DB::table('budget_year')->where('years_now','Y')->first();
        $bg_yearnow    = $bgs_year->leave_year_id;

         foreach ($acc_debtor as $key => $value) {
           
            $count_pttype = DB::connection('mysql2')->table('ipt_pttype')->where('an', $value->an)->count();
           
            if ($count_pttype > 1) {
                $check = Acc_debtor::where('an', $value->an)->where('account_code', '1102050101.202')->count();
                    if ($check == 0) {
                        if ($value->pttype_number == '2') {
                            if ($value->debit_toa > 0 ) {
                            } else {
                                if ($value->debit > 0) {

                                    if ($value->debit_prb > $value->debit) {
                                        # code...
                                    } else {
                                        Acc_debtor::insert([
                                            'bg_yearnow'         => $bg_yearnow,
                                            'hn'                 => $value->hn,
                                            'an'                 => $value->an,
                                            'vn'                 => $value->vn,
                                            'cid'                => $value->cid,
                                            'ptname'             => $value->ptname,
                                            'pttype'             => $value->pttype,
                                            'vstdate'            => $value->vstdate,
                                            'rxdate'             => $value->admdate,
                                            'dchdate'            => $value->dchdate,
                                            'acc_code'           => $value->acc_code,
                                            'account_code'       => $value->account_code,
                                            'account_name'       => $value->account_name,
                                            'income'             => $value->income,
                                            'uc_money'           => $value->uc_money,
                                            'discount_money'     => $value->discount_money,
                                            'rcpt_money'         => $value->rcpt_money,
                                            'debit'              => $value->debit,
                                            'debit_drug'         => $value->debit_drug,
                                            'debit_instument'    => $value->debit_instument,
                                            'debit_toa'          => $value->debit_toa,
                                            'debit_refer'        => $value->debit_refer,
                                            // 'debit_total'        => $value->debit-$value->debit_drug-$value->debit_instument-$value->debit_toa-$value->debit_refer,
                                            'debit_total'        => ($value->debit-$value->debit_ucep)+$value->nonpay,
                                            'debit_ucep'         => $value->debit_ucep,
                                            'max_debt_amount'    => $value->max_debt_amount,
                                            'rw'                 => $value->rw,
                                            'adjrw'              => $value->adjrw,
                                            'total_adjrw_income' => $value->total_adjrw_income,
                                            'acc_debtor_userid'  => Auth::user()->id
                                        ]);
                                    }
                                }
                            }
                        } else {
                            if ($value->debit_toa > 0 ) {
                                
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
                                        'rxdate'             => $value->admdate,
                                        'dchdate'            => $value->dchdate,
                                        'acc_code'           => $value->acc_code,
                                        'account_code'       => $value->account_code,
                                        'account_name'       => $value->account_name,
                                        'income'             => $value->income,
                                        'uc_money'           => $value->uc_money,
                                        'discount_money'     => $value->discount_money,
                                        'rcpt_money'         => $value->rcpt_money,
                                        'debit'              => $value->debit,
                                        'debit_drug'         => $value->debit_drug,
                                        'debit_instument'    => $value->debit_instument,
                                        'debit_toa'          => $value->debit_toa,
                                        'debit_refer'        => $value->debit_refer,
                                        'debit_total'        => ($value->debit-$value->debit_ucep)+$value->nonpay,
                                        // 'debit_total'        => $value->debit,
                                        // 'debit_total'        => $value->debit-($value->debit_drug-$value->debit_instument-$value->debit_toa-$value->debit_refer-$value->debit_ucep)+$value->nonpay,
                                        'debit_ucep'         => $value->debit_ucep,
                                        'max_debt_amount'    => $value->max_debt_amount,
                                        'rw'                 => $value->rw,
                                        'adjrw'              => $value->adjrw,
                                        'total_adjrw_income' => $value->total_adjrw_income,
                                        'acc_debtor_userid'  => Auth::user()->id
                                    ]);
                                }
                            }
                        }
                    }
            } else {
                if ($value->debit > 0) {
                        $check22 = Acc_debtor::where('an', $value->an)->where('account_code', '1102050101.202')->count();
                        if ($check22 == '0') {
                            if ($value->debit_toa > '0' ) {
                            } else {
                                if ($value->debit < '1') {
                                } else {
                                    Acc_debtor::insert([
                                        'bg_yearnow'         => $bg_yearnow,
                                        'hn'                 => $value->hn,
                                        'an'                 => $value->an,
                                        'vn'                 => $value->vn,
                                        'cid'                => $value->cid,
                                        'ptname'             => $value->ptname,
                                        'pttype'             => $value->pttype,
                                        'vstdate'            => $value->vstdate,
                                        'rxdate'             => $value->admdate,
                                        'dchdate'            => $value->dchdate,
                                        'acc_code'           => $value->acc_code,
                                        'account_code'       => $value->account_code,
                                        'account_name'       => $value->account_name,
                                        'income'             => $value->income,
                                        'uc_money'           => $value->uc_money,
                                        'discount_money'     => $value->discount_money,
                                        'rcpt_money'         => $value->rcpt_money,
                                        'debit'              => $value->debit,
                                        'debit_drug'         => $value->debit_drug,
                                        'debit_instument'    => $value->debit_instument,
                                        'debit_toa'          => $value->debit_toa,
                                        'debit_refer'        => $value->debit_refer,
                                        'debit_total'        => ($value->debit-$value->debit_ucep)+$value->nonpay,
                                        // 'debit_total'        => $value->debit+$value->nonpay,
                                        'debit_ucep'         => $value->debit_ucep,
                                        'nonpay'             => $value->nonpay,
                                        'max_debt_amount'    => $value->max_debt_amount,
                                        'rw'                 => $value->rw,
                                        'adjrw'              => $value->adjrw,
                                        'total_adjrw_income' => $value->total_adjrw_income,
                                        'acc_debtor_userid'  => Auth::user()->id
                                    ]);
                                }
                            }
                        }
                }
            }
         }
         
        Acc_debtor::where('account_code', '1102050101.202')->where('debit_total', '<', 1)->delete();

        Acc_debtor_log::insert([
            'account_code'       => '1102050101.402',
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
     public function account_pkucs202_pulldata_(Request $request)
     {
        $date              = date('Y-m-d H:i:s');
        $startdate         = $request->datepicker;
        $enddate           = $request->datepicker2;

        $acc_debtor = DB::connection('mysql2')->select('
                    SELECT ip.vn,a.an,a.hn,pt.cid,concat(pt.pname,pt.fname," ",pt.lname) ptname,a.regdate as admdate,a.dchdate,v.vstdate,op.income as income_group
                    ,ipt.pttype,ipt.pttype_number,ipt.max_debt_amount ,ip.rw,ip.adjrw,ip.adjrw*8350 as total_adjrw_income ,ipt.nhso_ownright_pid ,a.income,a.uc_money,a.rcpt_money,a.discount_money

                    ,"01" as acc_code
                    ,"1102050101.202" as account_code
                    ,"UC ใน CUP" as account_name

                    ,CASE
                    WHEN  ipt.pttype_number ="1" AND ipt.pttype IN ("31","33","36","39") THEN ipt.max_debt_amount
                    ELSE a.income - ipt.max_debt_amount
                    END as debit_prb

                    ,CASE
					WHEN  ipt.pttype_number ="2" THEN a.income - a.rcpt_money - a.discount_money - ipt.max_debt_amount
                    ELSE a.income - a.rcpt_money - a.discount_money
                    END as debit

                    ,sum(if(op.icode IN("3002895","3002896","3002897","3002898","3002909","3002910","3002911","3002912","3002913","3002914","3002915","3002916","3002918"),sum_price,0)) as portex
                    ,sum(if(op.income="02",sum_price,0)) as debit_instument
                    ,sum(if(op.icode IN("1560016","1540073","1530005"),sum_price,0)) as debit_drug
                    ,sum(if(op.icode IN ("3001412","3001417"),sum_price,0)) as debit_toa
                    ,sum(if(op.icode IN("3010829","3011068","3010864","3010861","3010862","3010863","3011069","3011012","3011070"),sum_price,0)) as debit_refer
                    ,(SELECT SUM(opp.sum_price) FROM opitemrece opp LEFT JOIN nondrugitems nn ON nn.icode = opp.icode WHERE opp.an = a.an AND nn.nhso_adp_code IN("5307","5402","5403","5406","5705","72940","5601","9104","9104","4805")) as nonpay

                    from ipt ip
                    LEFT JOIN an_stat a ON ip.an = a.an
                    LEFT JOIN patient pt on pt.hn=a.hn
                    LEFT JOIN pttype ptt on a.pttype=ptt.pttype
                    LEFT JOIN pttype_eclaim ec on ec.code=ptt.pttype_eclaim_id
                    LEFT JOIN ipt_pttype ipt ON ipt.an = a.an
                    LEFT JOIN opitemrece op ON ip.an = op.an
                    LEFT JOIN vn_stat v on v.vn = ip.vn
                WHERE a.dchdate BETWEEN "' . $startdate . '" AND "' . $enddate . '"
                AND ipt.pttype IN(SELECT pttype FROM pkbackoffice.acc_setpang_type WHERE pang ="1102050101.202" AND opdipd ="IPD" AND pttype <>"")
                GROUP BY a.an
        ');

         foreach ($acc_debtor as $key => $value) {

            $count_pttype = DB::connection('mysql2')->table('ipt_pttype')->where('an', $value->an)->count();

            if ($count_pttype > 1) {
                $check = Acc_debtor::where('an', $value->an)->where('account_code', '1102050101.202')->count();
                    if ($check == 0) {

                            if ($value->debit_toa > 0 ) {
                                Acc_debtor::insert([
                                    'hn'                 => $value->hn,
                                    'an'                 => $value->an,
                                    'vn'                 => $value->vn,
                                    'cid'                => $value->cid,
                                    'ptname'             => $value->ptname,
                                    'pttype'             => $value->pttype,
                                    'vstdate'            => $value->vstdate,
                                    'rxdate'             => $value->admdate,
                                    'dchdate'            => $value->dchdate,
                                    'acc_code'           => $value->acc_code,
                                    'account_code'       => $value->account_code,
                                    'account_name'       => $value->account_name,
                                    'income'             => $value->income,
                                    'uc_money'           => $value->uc_money,
                                    'discount_money'     => $value->discount_money,
                                    'rcpt_money'         => $value->rcpt_money,
                                    'debit'              => $value->debit,
                                    'debit_drug'         => $value->debit_drug,
                                    'debit_instument'    => $value->debit_instument,
                                    'debit_toa'          => $value->debit_toa,
                                    'debit_refer'        => $value->debit_refer,
                                    // 'debit_total'        => $value->debit,
                                    // 'debit_total'        => $value->debit-($value->debit_drug-$value->debit_instument-$value->debit_toa-$value->debit_refer)+$value->nonpay, //ก่อนวันที่ 17.12.67
                                    'debit_total'        => $value->debit+$value->nonpay, //วันที่ 17.12.67
                                    // 'debit_ucep'         => $value->debit_ucep,
                                    'max_debt_amount'    => $value->max_debt_amount,
                                    'rw'                 => $value->rw,
                                    'adjrw'              => $value->adjrw,
                                    'total_adjrw_income' => $value->total_adjrw_income,
                                    'acc_debtor_userid'  => Auth::user()->id
                                ]);
                            } else {

                                if ($value->debit > 0) {
                                    Acc_debtor::insert([
                                        'hn'                 => $value->hn,
                                        'an'                 => $value->an,
                                        'vn'                 => $value->vn,
                                        'cid'                => $value->cid,
                                        'ptname'             => $value->ptname,
                                        'pttype'             => $value->pttype,
                                        'vstdate'            => $value->vstdate,
                                        'rxdate'             => $value->admdate,
                                        'dchdate'            => $value->dchdate,
                                        'acc_code'           => $value->acc_code,
                                        'account_code'       => $value->account_code,
                                        'account_name'       => $value->account_name,
                                        'income'             => $value->income,
                                        'uc_money'           => $value->uc_money,
                                        'discount_money'     => $value->discount_money,
                                        'rcpt_money'         => $value->rcpt_money,
                                        'debit'              => $value->debit,
                                        'debit_drug'         => $value->debit_drug,
                                        'debit_instument'    => $value->debit_instument,
                                        'debit_toa'          => $value->debit_toa,
                                        'debit_refer'        => $value->debit_refer,
                                        // 'debit_total'        => $value->debit,
                                        'debit_total'        => $value->debit-($value->debit_drug-$value->debit_instument-$value->debit_toa-$value->debit_refer)+$value->nonpay,
                                        // 'debit_ucep'         => $value->debit_ucep,
                                        'max_debt_amount'    => $value->max_debt_amount,
                                        'rw'                 => $value->rw,
                                        'adjrw'              => $value->adjrw,
                                        'total_adjrw_income' => $value->total_adjrw_income,
                                        'acc_debtor_userid'  => Auth::user()->id
                                    ]);
                                }
                            }

                    }
            } else {

            }
         }


        return response()->json([

            'status'    => '200'
        ]);
     }
     public function account_pkucs202_checksit(Request $request)
     {
         $datestart = $request->datestart;
         $dateend = $request->dateend;
         $date = date('Y-m-d');

         $data_sitss = DB::connection('mysql')->select('SELECT vn,an,cid,vstdate,dchdate FROM acc_debtor WHERE account_code="1102050101.202" AND stamp = "N" GROUP BY an');
       
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
     public function account_pkucs202_processdata(Request $request)
     {
        $datetime          = date('Y-m-d H:i:s');
        //  $startdate         = $request->datepicker;
        //  $enddate           = $request->datepicker2;
        $bdy               = $request->budget_year;
        $iduser            = Auth::user()->id;
        $data_year         = DB::table('budget_year')->where('leave_year_id',$bdy)->first();
        $startdate         = $data_year->date_begin;
        $enddate           = $data_year->date_end;
 
        $acc_tung = DB::connection('mysql')->select('
                SELECT day(a.dchdate) as days,month(a.dchdate) as months,year(a.dchdate) as years,l.MONTH_NAME
                ,count(distinct a.an) as an_tung
                ,sum(a.debit_total) as debit_total
                FROM acc_1102050101_202 a
                LEFT OUTER JOIN leave_month l on l.MONTH_ID = month(a.dchdate)
                WHERE a.dchdate BETWEEN "' . $startdate . '" AND "' . $enddate . '"
                AND account_code = "1102050101.202"
                GROUP BY days,months,years
                order by a.dchdate desc
        ');
        foreach ($acc_tung as $key => $value) {
            $count = Acc_db_202::where('days',$value->days)->where('months',$value->months)->where('years',$value->years)->count();
            if ($count > 0) {
                Acc_db_202::where('days',$value->days)->where('months',$value->months)->where('years',$value->years)->update([
                    'count_an'           => $value->an_tung,
                    'debit_total'        => $value->debit_total,
                    'user_id'            => $iduser,
                    'last_update'        => $datetime,
                ]);
            } else {
                Acc_db_202::insert([
                    'days'               => $value->days,
                    'months'             => $value->months,
                    'years'              => $value->years,
                    'MONTH_NAME'         => $value->MONTH_NAME,
                    'count_an'           => $value->an_tung,
                    'debit_total'        => $value->debit_total,
                    'user_id'            => $iduser,
                    'last_update'        => $datetime,
                ]);
            }
        }
        $acc_stm = DB::connection('mysql')->select('
                SELECT day(a.dchdate) as days,month(a.dchdate) as months,year(a.dchdate) as years,l.MONTH_NAME
                ,count(distinct a.an) as an_stm
                ,sum(a.ip_paytrue) as ip_paytrue
                FROM acc_stm_ucs a
                LEFT OUTER JOIN leave_month l on l.MONTH_ID = month(a.dchdate)
                INNER JOIN acc_1102050101_202 b ON b.an = a.an
                WHERE a.dchdate BETWEEN "' . $startdate . '" AND "' . $enddate . '"
                AND a.ip_paytrue > "0.00"
                GROUP BY days,months,years
                order by a.dchdate desc
        ');
        foreach ($acc_stm as $key => $value_stm) {
            $count_s = Acc_db_202::where('days',$value_stm->days)->where('months',$value_stm->months)->where('years',$value_stm->years)->count();
                Acc_db_202::where('days',$value_stm->days)->where('months',$value_stm->months)->where('years',$value_stm->years)->update([
                    'count_an_stm'   => $value_stm->an_stm,
                    'ip_paytrue'     => $value_stm->ip_paytrue,
                ]);
        }


         return response()->json([

            'status'    => '200'
        ]);
     }
     public function account_pkucs202_pulldata_old(Request $request)
     {
         $date              = date('Y-m-d H:i:s');
         $startdate         = $request->datepicker;
         $enddate           = $request->datepicker2;
          
        $data_ = DB::connection('mysql2')->select('
                        SELECT i.vn,i.an,o.vstdate,i.dchdate,op.rxdate,op.rxtime
                        FROM ipt i
                        LEFT JOIN opitemrece op on i.an = op.an
                        LEFT JOIN ovst o on o.an = op.an
                        left JOIN er_regist e on e.vn = i.vn
                        LEFT JOIN ipt_pttype ii on ii.an = i.an
                        LEFT JOIN pttype p on p.pttype = ii.pttype
                        WHERE i.dchdate BETWEEN "'.$startdate.'" and "'.$enddate.'"
                        and op.an is not null
                        and op.paidst ="02"
                        and p.hipdata_code ="ucs"
                        and e.er_emergency_level_id in("1","2")
                        group BY i.an
                        ORDER BY op.rxdate,op.rxtime ASC;
        ');
        Acc_ucep_24::truncate();
        foreach ($data_ as $key => $val) {
            Acc_ucep_24::insert([
                'vn'                => $val->vn,
                'an'                => $val->an,
                'vstdate'           => $val->vstdate,
                'dchdate'           => $val->dchdate,
                'rxdate'            => $val->rxdate,
                'rxtime'            => $val->rxtime,
            ]);
        }

        $data_2 = DB::connection('mysql')->select('SELECT an,dchdate,rxdate,rxtime FROM acc_ucep_24');
        foreach ($data_2 as $key => $val2) {
            // $newweek = date('Y-m-d', strtotime($date . ' -1 week'));
            $d1 = $val2->rxdate;
            $t1 = $val2->rxtime;
            $old_time2          = strtotime($t1);
            $now_timestamp     = strtotime(date($d1.''.$t1));
            $last_timestamp    = date('Y-m-d',strtotime($val2->dchdate));
            // $diff_timestamp    = $now_timestamp - strtotime($val2->dchdate);
            // $old_time          = strtotime(date($d1.''.$t1));
            $last_time         = strtotime($val2->dchdate);
            $old_time          = strtotime($val2->rxdate);
            $diff_timestamp    = $last_time - $old_time;
            $dt = date('Y-m-d',strtotime($diff_timestamp));                //"2023-10-01"
            $dtt = date('Y-m-d',$diff_timestamp);
            // $dt24 = date($last_time,strtotime($diff_timestamp));
            $hours = floor($diff_timestamp/(60*60));
            // $old_timestamp2     = strtotime(date("2023-09-25"));
            // $now_timestamp2     = strtotime(date("'.$val2->dchdate.'"));
            // dd($hours);
            // dd($diff_timestamp);
            // if ($diff_timestamp < (86400 * 24)) {
                $data_3 = DB::connection('mysql2')->select('
                           SELECT i.an,i.dchdate,op.vstdate,(select SUM(sum_price) from opitemrece where an = "'.$val2->an.'" and rxdate ="'.$val2->rxdate.'") as sum_price
                           FROM opitemrece op
                            LEFT JOIN ovst o on o.an = op.an
                            LEFT JOIN ipt i on i.an = o.an
                            WHERE op.an = "'.$val2->an.'"
                            AND DATEDIFF(i.dchdate,op.vstdate)<="1"
                            AND hour(TIMEDIFF(concat(op.vstdate," ",o.vsttime),concat(op.rxdate," ",op.rxtime))) <="24"
                            GROUP BY op.an
               ');
               foreach($data_3 as $key => $val3) {
                    Acc_ucep_24::where('an',$val3->an)->update(['sum_price' => $val3->sum_price]);
               }
            // } else {
            //    # code...
            // }
        }

        $acc_debtor = DB::connection('mysql2')->select('
                    SELECT ip.vn,a.an,a.hn,pt.cid,concat(pt.pname,pt.fname," ",pt.lname) ptname,a.regdate as admdate,a.dchdate as dchdate,v.vstdate,op.income as income_group
                    ,ipt.pttype,ipt.pttype_number,ipt.max_debt_amount ,ip.rw,ip.adjrw,ip.adjrw*8350 as total_adjrw_income ,ipt.nhso_ownright_pid ,a.income as income ,a.uc_money,a.rcpt_money,a.discount_money
                    ,CASE
                    WHEN  ipt.pttype_number ="2" THEN "01"
                    ELSE ec.code
                    END as code
                    ,CASE
                    WHEN  ipt.pttype_number ="2" THEN "1102050101.202"
                    ELSE ec.ar_ipd
                    END as account_code
                    ,CASE
                    WHEN  ipt.pttype_number ="2" THEN "UC ใน CUP"
                    ELSE ec.name
                    END as account_name

                    ,CASE
                    WHEN  ipt.pttype_number ="1" AND ipt.pttype IN ("31","33","36","39") THEN ipt.max_debt_amount
                    ELSE a.income -IFNULL(ipt.max_debt_amount,"0")
                    END as debit_prb

                    ,(a.income-a.rcpt_money-a.discount_money-IFNULL(ipt.max_debt_amount,"0"))-
                    (sum(if(op.income="02",sum_price,0))) -
                    (sum(if(op.icode IN("1560016","1540073","1530005"),sum_price,0))) -
                    (sum(if(op.icode IN("3001412","3001417"),sum_price,0))) -
                    (sum(if(op.icode IN("3010829","3011068","3010864","3010861","3010862","3010863","3011069","3011012","3011070"),sum_price,0))) +
                    (sum(if(op.icode IN("3002895","3002896","3002897","3002898","3002909","3002910","3002911","3002912","3002913","3002914","3002915","3002916","3002918"),sum_price,0)))
                    as debit

                    ,sum(if(op.icode IN("3002895","3002896","3002897","3002898","3002909","3002910","3002911","3002912","3002913","3002914","3002915","3002916","3002918"),sum_price,0)) as portex
                    ,sum(if(op.income="02",sum_price,0)) as debit_instument
                    ,sum(if(op.icode IN("1560016","1540073","1530005"),sum_price,0)) as debit_drug
                    ,sum(if(op.icode IN ("3001412","3001417"),sum_price,0)) as debit_toa
                    ,sum(if(op.icode IN("3010829","3011068","3010864","3010861","3010862","3010863","3011069","3011012","3011070"),sum_price,0)) as debit_refer

                    ,(
                        SELECT SUM(o.sum_price) as ucepprice
                                FROM ipt i
                                LEFT JOIN opitemrece o on i.an = o.an
                                LEFT JOIN ovst a on a.an = o.an
                                left JOIN er_regist e on e.vn = i.vn
                                LEFT JOIN ipt_pttype ii on ii.an = i.an
                                LEFT JOIN pttype p on p.pttype = ii.pttype
                                LEFT JOIN s_drugitems n on n.icode = o.icode
                                LEFT JOIN patient pt on pt.hn = a.hn
                                WHERE i.dchdate BETWEEN "' . $startdate . '" AND "' . $enddate . '"
                                AND i.an = ip.an
                                AND o.income NOT IN ("02")
                                AND op.icode NOT IN ("3002895","3002896","3002897","3002898","3002909","3002910","3002911","3002912","3002913","3002914","3002915","3002916","3002918","1560016","1540073","1530005","3001412","3001417","3010829","3011068","3010864","3010861","3010862","3010863","3011069","3011012","3011070")
                                AND o.an is not null
                                AND o.paidst ="02"
                                AND p.hipdata_code ="ucs"
                                AND DATEDIFF(o.rxdate,a.vstdate)<="1"
                                AND hour(TIMEDIFF(concat(a.vstdate," ",a.vsttime),concat(o.rxdate," ",o.rxtime))) <="24"
                                AND e.er_emergency_type in("1","2","5")
                    ) as debit_ucep

                    from ipt ip
                    LEFT JOIN an_stat a ON ip.an = a.an
                    LEFT JOIN patient pt on pt.hn=a.hn
                    LEFT JOIN pttype ptt on a.pttype=ptt.pttype
                    LEFT JOIN pttype_eclaim ec on ec.code=ptt.pttype_eclaim_id
                    LEFT JOIN ipt_pttype ipt ON ipt.an = a.an
                    LEFT JOIN opitemrece op ON ip.an = op.an
                    LEFT JOIN vn_stat v on v.vn = ip.vn
                WHERE a.dchdate BETWEEN "' . $startdate . '" AND "' . $enddate . '"
                AND ipt.pttype IN (SELECT pttype FROM pkbackoffice.acc_setpang_type WHERE pang ="1102050101.202" AND opdipd ="IPD")
                GROUP BY a.an;
        ');


         foreach ($acc_debtor as $key => $value) {
            // $count_pttype = DB::connection('mysql2')->select('SELECT COUNT(an) as C_an FROM  ipt_pttype WHERE an = "'.$value->an.'" ');
            $count_pttype = DB::connection('mysql2')->table('ipt_pttype')->where('an', $value->an)->count();
            $total_ = $value->debit-$value->debit_drug-$value->debit_instument-$value->debit_toa-$value->debit_refer-$value->debit_ucep;
            // dd($count_pttype);
            if ($count_pttype > 1) {
                $check = Acc_debtor::where('an', $value->an)->where('account_code', '1102050101.202')->count();
                    if ($check == 0) {
                        if ($value->pttype_number == 2) {
                            if ($value->debit_toa > 0 ) {
                            } else {
                                if ($value->debit > 0) {
                                    Acc_debtor::insert([
                                        'hn'                 => $value->hn,
                                        'an'                 => $value->an,
                                        'vn'                 => $value->vn,
                                        'cid'                => $value->cid,
                                        'ptname'             => $value->ptname,
                                        'pttype'             => $value->pttype,
                                        'vstdate'            => $value->vstdate,
                                        'rxdate'             => $value->admdate,
                                        'dchdate'            => $value->dchdate,
                                        'acc_code'           => $value->code,
                                        'account_code'       => $value->account_code,
                                        'account_name'       => $value->account_name,
                                        'income'             => $value->income,
                                        'uc_money'           => $value->uc_money,
                                        'discount_money'     => $value->discount_money,
                                        'rcpt_money'         => $value->rcpt_money,
                                        'debit'              => $value->debit,
                                        'debit_drug'         => $value->debit_drug,
                                        'debit_instument'    => $value->debit_instument,
                                        'debit_toa'          => $value->debit_toa,
                                        'debit_refer'        => $value->debit_refer,
                                        'debit_total'        => $value->debit,
                                        'debit_ucep'         => $value->debit_ucep,
                                        'max_debt_amount'    => $value->max_debt_amount,
                                        'rw'                 => $value->rw,
                                        'adjrw'              => $value->adjrw,
                                        'total_adjrw_income' => $value->total_adjrw_income,
                                        'acc_debtor_userid'  => Auth::user()->id
                                    ]);
                                } else {

                                }
                            }
                        } else {
                            if ($value->debit_toa > 0 ) {
                            } else {
                                if ($total_ < 1) {

                                } else {
                                    if ($value->debit > 0) {
                                        Acc_debtor::insert([
                                            'hn'                 => $value->hn,
                                            'an'                 => $value->an,
                                            'vn'                 => $value->vn,
                                            'cid'                => $value->cid,
                                            'ptname'             => $value->ptname,
                                            'pttype'             => $value->pttype,
                                            'vstdate'            => $value->vstdate,
                                            'rxdate'             => $value->admdate,
                                            'dchdate'            => $value->dchdate,
                                            'acc_code'           => $value->code,
                                            'account_code'       => $value->account_code,
                                            'account_name'       => $value->account_name,
                                            'income'             => $value->income,
                                            'uc_money'           => $value->uc_money,
                                            'discount_money'     => $value->discount_money,
                                            'rcpt_money'         => $value->rcpt_money,
                                            'debit'              => $value->debit,
                                            'debit_drug'         => $value->debit_drug,
                                            'debit_instument'    => $value->debit_instument,
                                            'debit_toa'          => $value->debit_toa,
                                            'debit_refer'        => $value->debit_refer,
                                            'debit_total'        => $value->debit-$value->debit_drug-$value->debit_instument-$value->debit_toa-$value->debit_refer-$value->debit_ucep,
                                            'debit_ucep'         => $value->debit_ucep,
                                            'max_debt_amount'    => $value->max_debt_amount,
                                            'rw'                 => $value->rw,
                                            'adjrw'              => $value->adjrw,
                                            'total_adjrw_income' => $value->total_adjrw_income,
                                            'acc_debtor_userid'  => Auth::user()->id
                                        ]);
                                    } else {

                                    }
                                }
                            }
                        }
                    }

            } else {
                if ($value->debit > 0) {
                        $check = Acc_debtor::where('an', $value->an)->where('account_code', '1102050101.202')->count();
                        if ($check == '0') {
                            if ($value->debit_toa > '0' ) {
                            } else {
                                if ($total_ < '1') {
                                } else {
                                    Acc_debtor::insert([
                                        'hn'                 => $value->hn,
                                        'an'                 => $value->an,
                                        'vn'                 => $value->vn,
                                        'cid'                => $value->cid,
                                        'ptname'             => $value->ptname,
                                        'pttype'             => $value->pttype,
                                        'vstdate'            => $value->vstdate,
                                        'rxdate'             => $value->admdate,
                                        'dchdate'            => $value->dchdate,
                                        'acc_code'           => $value->code,
                                        'account_code'       => $value->account_code,
                                        'account_name'       => $value->account_name,
                                        'income'             => $value->income,
                                        'uc_money'           => $value->uc_money,
                                        'discount_money'     => $value->discount_money,
                                        'rcpt_money'         => $value->rcpt_money,
                                        'debit'              => $value->debit,
                                        'debit_drug'         => $value->debit_drug,
                                        'debit_instument'    => $value->debit_instument,
                                        'debit_toa'          => $value->debit_toa,
                                        'debit_refer'        => $value->debit_refer,
                                        'debit_total'        => $value->debit-$value->debit_drug-$value->debit_instument-$value->debit_toa-$value->debit_refer-$value->debit_ucep,
                                        'debit_ucep'         => $value->debit_ucep,
                                        'max_debt_amount'    => $value->max_debt_amount,
                                        'rw'                 => $value->rw,
                                        'adjrw'              => $value->adjrw,
                                        'total_adjrw_income' => $value->total_adjrw_income,
                                        'acc_debtor_userid'  => Auth::user()->id
                                    ]);
                                }
                            }
                        }
                } else {

                }
            }

         }


        return response()->json([

            'status'    => '200'
        ]);
     }
     public function account_pkucs202_dash(Request $request)
     {
        //  $datenow = date('Y-m-d');
        //  $startdate     = $request->startdate;
        //  $enddate       = $request->enddate;
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
            //    dd($budget_year);
            $bgs_year      = DB::table('budget_year')->where('years_now','Y')->first();
            $data['bg_yearnow']    = $bgs_year->leave_year_id;

            if ($budget_year == '') {
                $yearnew = date('Y');
                $year_old = date('Y')-1;
                $months_old  = ('10');
                $bg           = DB::table('budget_year')->where('years_now','Y')->first();
                $startdate    = $bg->date_begin;
                $enddate      = $bg->date_end;

                $datashow = DB::select('
                        SELECT MONTH(a.dchdate) as months,YEAR(a.dchdate) as years
                        ,count(DISTINCT a.an) as total_an,l.MONTH_NAME,sum(a.income) as income
                        ,sum(a.debit_total) as tung_looknee
                        FROM acc_1102050101_202 a
                        LEFT OUTER JOIN leave_month l on l.MONTH_ID = month(a.dchdate)
                        WHERE a.dchdate BETWEEN "'.$startdate.'" AND "'.$enddate.'"
                        AND a.account_code ="1102050101.202"
                        GROUP BY months ORDER BY a.dchdate DESC
                ');
            } else {
                $bgg           = DB::table('budget_year')->where('leave_year_id',$budget_year)->first();
                $startdate    = $bgg->date_begin;
                $enddate      = $bgg->date_end;
                // dd($enddate);
                $datashow = DB::select('
                        SELECT MONTH(a.dchdate) as months,YEAR(a.dchdate) as years
                        ,count(DISTINCT a.an) as total_an,l.MONTH_NAME,sum(a.income) as income
                        ,sum(a.debit_total) as tung_looknee
                        FROM acc_1102050101_202 a
                        LEFT OUTER JOIN leave_month l on l.MONTH_ID = month(a.dchdate)
                        WHERE a.dchdate BETWEEN "'.$startdate.'" AND "'.$enddate.'"
                        AND a.account_code ="1102050101.202"
                        GROUP BY months ORDER BY a.dchdate DESC
                ');
            }

             return view('account_202.account_pkucs202_dash',$data,[
                 'startdate'        =>  $startdate,
                 'enddate'          =>  $enddate,
                 'datashow'         =>  $datashow,
                 'dabudget_year'    =>  $dabudget_year,
                 'budget_year'      =>  $budget_year,
                 'y'                =>  $y,
             ]);
     }
     public function account_pkucs202(Request $request,$months,$year)
     {
         $datenow = date('Y-m-d');
         $startdate = $request->startdate;
         $enddate = $request->enddate;
         // dd($id);
         $data['users'] = User::get();

         $acc_debtor = DB::select('
             SELECT a.*,c.subinscl from acc_debtor a
             left outer join check_sit_auto c on c.cid = a.cid and c.vstdate = a.vstdate

             WHERE a.account_code="1102050101.202"
             AND a.stamp = "N"
             and month(a.dchdate) = "'.$months.'" and year(a.dchdate) = "'.$year.'"
             order by a.dchdate asc;

         ');

         return view('account_202.account_pkucs202', $data, [
             'startdate'     =>     $startdate,
             'enddate'       =>     $enddate,
             'acc_debtor'    =>     $acc_debtor,
             'months'        =>     $months,
             'year'          =>     $year
         ]);
     }
     public function account_pkucs202_detail(Request $request,$months,$year)
     {
         $datenow = date('Y-m-d');
         $startdate = $request->startdate;
         $enddate = $request->enddate;
         // dd($id);
         $data['users'] = User::get();

         $data = DB::select('
             SELECT *  from acc_1102050101_202
             WHERE month(dchdate) = "'.$months.'" and year(dchdate) = "'.$year.'"
             GROUP BY an
         ');
            //  AND stamp = "Y"
            // SELECT *,au.subinscl  from acc_1102050101_202 a
            //     LEFT JOIN acc_debtor au ON au.an = a.an
            //     WHERE month(a.dchdate) = "'.$months.'" and year(a.dchdate) = "'.$year.'";

         return view('account_202.account_pkucs202_detail', $data, [
             'startdate'     =>     $startdate,
             'enddate'       =>     $enddate,
             'data'          =>     $data,
             'months'        =>     $months,
             'year'          =>     $year
         ]);
     }
     public function account_pkucs202_detail_date(Request $request,$startdate,$enddate)
     {
         $datenow = date('Y-m-d');
         $startdate = $request->startdate;
         $enddate = $request->enddate;
         // dd($id);
         $data['users'] = User::get();

         $data = DB::select('
             SELECT *  from acc_1102050101_202
             WHERE dchdate BETWEEN "'.$startdate.'" AND  "'.$enddate.'"

         ');
        
         return view('account_202.account_pkucs202_detail_date', $data, [
             'startdate'     =>     $startdate,
             'enddate'       =>     $enddate,
             'data'          =>     $data,
             'startdate'     =>     $startdate,
             'enddate'       =>     $enddate
         ]);
     }
     public function account_pkucs202_stam(Request $request)
     {
        $datenow    = date('Y-m-d');
        $datatime   = date('H:m:s');
        $ip = $request->ip();
        Acc_debtor_log::insert([
            'account_code'       => '1102050101.402',
            'make_gruop'         => 'ตั้งลูกหนี้และส่งลูกหนี้',
            'date_save'          => $datenow,
            'date_time'          => $datatime,
            'user_id'            => Auth::user()->id,
            'ip'                 => $ip
        ]);
        $maxnumber = DB::table('acc_debtor_log')->where('account_code','1102050101.202')->where('user_id',Auth::user()->id)->max('acc_debtor_log_id');
         $id       = $request->ids;
         $iduser   = Auth::user()->id;
         $data     = Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))->get();

             Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))
                ->update([
                    'stamp'       => 'Y',
                    'send_active' => 'Y'
                ]);

         foreach ($data as $key => $value) {
                 $date = date('Y-m-d H:m:s');
                 $check = Acc_1102050101_202::where('an', $value->an)->count();
                 if ($check>0) {
                    # code...
                 } else {
                    Acc_1102050101_202::insert([
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
                        'income_group'      => $value->income_group,
                        'income'            => $value->income,
                        'uc_money'          => $value->uc_money,
                        'discount_money'    => $value->discount_money,
                        'rcpt_money'        => $value->rcpt_money,
                        'debit'             => $value->debit,
                        'debit_drug'        => $value->debit_drug,
                        'debit_instument'   => $value->debit_instument,
                        'debit_refer'       => $value->debit_refer,
                        'debit_toa'         => $value->debit_toa,
                       //  'debit_total'       => $value->debit - $value->debit_drug - $value->debit_instument - $value->debit_refer - $value->debit_toa,
                        'debit_total'       => $value->debit_total,
                        'debit_ucep'        => $value->debit_ucep,
                        'max_debt_amount'   => $value->max_debt_amount,
                        'rw'                => $value->rw,
                        'adjrw'             => $value->adjrw,
                        'total_adjrw_income'=> $value->total_adjrw_income,
                        'acc_debtor_userid' => $value->acc_debtor_userid


                    ]);
                 }


                $check_total  = Acc_account_total::where('vn', $value->vn)->where('account_code','=','1102050101.202')->count();
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
     public function account_pkucs202_stm(Request $request,$months,$year)
     {
         $datenow = date('Y-m-d');
         $startdate = $request->startdate;
         $enddate = $request->enddate;
         // dd($id);
         $data['users'] = User::get();

         $datashow = DB::select('
                SELECT a.stm_trainid,a.vn,a.an,a.hn,a.cid,a.ptname,a.vstdate,a.dchdate,a.debit_total
                ,a.income_group,a.adjrw,a.total_adjrw_income,a.stm_money,a.stm_total,a.STMdoc
                FROM acc_1102050101_202 a
                WHERE month(a.dchdate) = "'.$months.'" and year(a.dchdate) = "'.$year.'"
                AND a.stm_money IS NOT NULL
                GROUP BY a.an
         ');
         
         return view('account_202.account_pkucs202_stm', $data, [
             'startdate'         =>     $startdate,
             'enddate'           =>     $enddate,
             'datashow'          =>     $datashow,
             'months'            =>     $months,
             'year'              =>     $year, 
         ]);
     }
     public function account_pkucs202_stm_date(Request $request,$startdate,$enddate)
     {
         $data['users'] = User::get();

         $datashow = DB::select('
                SELECT s.tranid,a.vn,a.an,a.hn,a.cid,a.ptname,a.vstdate,a.dchdate,a.debit_total,s.dmis_money2
                ,s.total_approve,a.income_group,s.inst,s.hc,s.hc_drug,s.ae,s.ae_drug,s.ip_paytrue,s.STMdoc,a.adjrw,a.total_adjrw_income
                from acc_1102050101_202 a
             LEFT JOIN acc_stm_ucs s ON s.an = a.an
             WHERE a.dchdate BETWEEN "'.$startdate.'" AND  "'.$enddate.'"

             AND s.ip_paytrue > "0.00"

         ');
        //  AND s.rep IS NOT NULL
        // GROUP BY a.an
         return view('account_202.account_pkucs202_stm_date', $data, [
             'startdate'         =>     $startdate,
             'enddate'           =>     $enddate,
             'datashow'          =>     $datashow,
         ]);
     }
     public function account_pkucs202_stmnull(Request $request,$months,$year)
     {
         $datenow = date('Y-m-d');
         $startdate = $request->startdate;
         $enddate = $request->enddate;
         // dd($id);
         $data['users'] = User::get();

            $data = DB::connection('mysql')->select('
                SELECT a.stm_trainid,a.vn,a.an,a.hn,a.cid,a.ptname,a.vstdate,a.dchdate,a.debit_total
                ,a.income_group,a.adjrw,a.total_adjrw_income,a.stm_money,a.stm_total,a.STMdoc
                FROM acc_1102050101_202 a
                WHERE month(a.dchdate) = "'.$months.'" and year(a.dchdate) = "'.$year.'"
                AND a.stm_money IS NULL
                GROUP BY a.an
             ');
 
         return view('account_202.account_pkucs202_stmnull', $data, [
             'startdate'         =>     $startdate,
             'enddate'           =>     $enddate,
             'data'              =>     $data,
             'months'            =>     $months,
             'year'              =>     $year, 
         ]);
     }
     public function account_pkucs202_stmnull_date(Request $request,$startdate,$enddate)
     {
         $data['users'] = User::get();

            $data = DB::connection('mysql')->select('
            SELECT au.tranid,a.vn,a.an,a.hn,a.cid,a.ptname,a.vstdate,a.dchdate,a.debit_total,au.dmis_money2,au.total_approve,a.income_group
            ,au.hc,au.hc_drug,au.ae,au.ae_drug,au.inst,au.ip_paytrue,au.STMdoc,a.adjrw,a.total_adjrw_income
            from acc_1102050101_202 a
            LEFT JOIN acc_stm_ucs au ON au.an = a.an
            WHERE a.dchdate BETWEEN "'.$startdate.'" AND  "'.$enddate.'"
            AND au.ip_paytrue <= "0.00"

             ');
            //  GROUP BY a.an
            //  WHERE status ="N" AND a.dchdate BETWEEN "'.$startdate.'" AND  "'.$enddate.'"

         return view('account_202.account_pkucs202_stmnull_date', $data, [
             'startdate'         =>     $startdate,
             'enddate'           =>     $enddate,
             'data'              =>     $data,
         ]);
     }
     public function account_pkucs202_stmnull_all(Request $request,$months,$year)
     {
         $datenow = date('Y-m-d');
         $startdate = $request->startdate;
         $enddate = $request->enddate;
         // dd($id);
         $data['users'] = User::get();
         $mototal = $months + 1;
         $datashow = DB::connection('mysql')->select('

                 SELECT au.tranid,a.vn,a.an,a.hn,a.cid,a.ptname,a.vstdate,a.dchdate,a.debit_total,au.dmis_money2,au.total_approve,a.income_group
                 ,au.hc,au.hc_drug,au.ae,au.ae_drug,au.inst,au.ip_paytrue ,au.STMdoc,a.adjrw,a.total_adjrw_income
                from acc_1102050101_202 a
                LEFT JOIN acc_stm_ucs au ON au.an = a.an
                WHERE month(a.dchdate) < "'.$mototal.'"
                and year(a.dchdate) = "'.$year.'"
                AND (au.ip_paytrue IS NULL OR au.ip_paytrue <= "0.00")
                GROUP BY a.an
             ');
         return view('account_202.account_pkucs202_stmnull_all', $data, [
             'startdate'         =>     $startdate,
             'enddate'           =>     $enddate,
             'datashow'          =>     $datashow,
             'months'            =>     $months,
             'year'              =>     $year,
         ]);
     }
     public function account_202_destroy(Request $request)
    {
        $id = $request->ids;
        $data = Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))->get();
            Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))->delete();

        return response()->json([
            'status'    => '200'
        ]);
    }




 }
