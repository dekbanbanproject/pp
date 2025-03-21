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
use App\Models\Acc_stm_bkkexcel;
use App\Models\Acc_stm_bkk;
use App\Models\Acc_debtor_stamp;
use App\Models\Acc_debtor_sendmoney;
use App\Models\Pttype;
use App\Models\Pttype_acc;
use App\Models\Acc_stm_ti;
use App\Models\Acc_stm_ti_total;
use App\Models\Acc_opitemrece;
use App\Models\Acc_1102050101_202;
use App\Models\Acc_1102050101_216;
use App\Models\Acc_1102050101_217;
use App\Models\Acc_1102050101_2166;
use App\Models\Acc_stm_ucs;
use App\Models\Acc_1102050101_304;
use App\Models\Acc_1102050101_307;
use App\Models\Acc_1102050101_308;
use App\Models\Acc_1102050101_309;
use App\Models\Acc_1102050101_4011;
use App\Models\Acc_1102050101_3099;
use App\Models\Acc_1102050101_401;
use App\Models\Acc_1102050101_402;
use App\Models\Acc_1102050102_801;
use App\Models\Acc_1102050102_802;
use App\Models\Acc_1102050102_803;
use App\Models\Acc_1102050102_804;
use App\Models\Acc_1102050101_4022;
use App\Models\Acc_stm_lgonew;
use App\Models\Acc_stm_lgoexcelnew;
use App\Models\Acc_1102050102_8011;
use App\Models\Acc_stm_ti_totalsub;
use App\Models\Acc_stm_ti_totalhead;
use App\Models\Acc_stm_ti_excel;
use App\Models\Acc_stm_ofc;
use App\Models\Acc_stm_ofcexcel;
use App\Models\Acc_stm_lgo;
use App\Models\Acc_stm_lgoexcel;
use App\Models\Check_sit_auto;
use App\Models\Acc_stm_ucs_excel;
use App\Models\Acc_stm_repmoney;
use App\Models\Acc_stm_lgoti_excel;
use App\Models\Acc_stm_lgoti;
use App\Models\Acc_stm_sssnew;
use App\Models\Acc_trimart;
use App\Models\Acc_stm_sss;
use App\Models\Acc_stm_sssexcel;
use App\Models\Acc_lgo_repexcel;
use App\Models\Acc_lgo_rep;
use App\Models\Acc_1102050102_8022;

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

use function Ramsey\Uuid\v1;

// use Orchestra\Parser\Xml\Facade as XmlParser;
// use Illuminate\Container\Container;
// use Orchestra\Parser\Xml\Document;
// use Orchestra\Parser\Xml\Reader;
// use Saloon\XmlWrangler\XmlReader;
// use Saloon\XmlWrangler\Data\Element;
// use Saloon\XmlWrangler\XmlWriter;

date_default_timezone_set("Asia/Bangkok");


class AccountPKController extends Controller
{
     // ดึงข้อมูลมาไว้เช็คสิทธิ์
     public function sit_accpull_auto(Request $request)
     {
             $data_sits = DB::connection('mysql3')->select('
                 SELECT o.an,o.vn,p.hn,p.cid,o.vstdate,o.vsttime,o.pttype,concat(p.pname,p.fname," ",p.lname) as fullname,o.staff,pt.nhso_code,o.hospmain,o.hospsub
                 FROM ovst o
                 join patient p on p.hn=o.hn
                 JOIN pttype pt on pt.pttype=o.pttype
                 JOIN opduser op on op.loginname = o.staff
                 WHERE o.vstdate BETWEEN "2023-07-01" AND "2023-07-05"
                 group by p.cid
                 limit 1500
             ');
            //  BETWEEN "2023-07-01" AND "2023-07-05"
             // CURDATE()
             foreach ($data_sits as $key => $value) {
                 $check = Check_sit_auto::where('vn', $value->vn)->count();
                 if ($check == 0) {
                     Check_sit_auto::insert([
                         'vn' => $value->vn,
                         'an' => $value->an,
                         'hn' => $value->hn,
                         'cid' => $value->cid,
                         'vstdate' => $value->vstdate,
                         'vsttime' => $value->vsttime,
                         'fullname' => $value->fullname,
                         'pttype' => $value->pttype,
                         'hospmain' => $value->hospmain,
                         'hospsub' => $value->hospsub,
                         'staff' => $value->staff
                     ]);
                 }
             }
             $data_sits_ipd = DB::connection('mysql3')->select('
                     SELECT a.an,a.vn,p.hn,p.cid,a.dchdate,a.pttype
                     from hos.opitemrece op
                     LEFT JOIN hos.ipt ip ON ip.an = op.an
                     LEFT JOIN hos.an_stat a ON ip.an = a.an
                     LEFT JOIN hos.vn_stat v on v.vn = a.vn
                     LEFT JOIN patient p on p.hn=a.hn
                     WHERE a.dchdate BETWEEN "2023-07-01" AND "2023-07-05"
                     group by p.cid
                     limit 1500

             ');
            //  BETWEEN "2023-06-11" AND "2023-06-30"
             // CURDATE()
             foreach ($data_sits_ipd as $key => $value2) {
                 $check = Check_sit_auto::where('an', $value2->an)->count();
                 if ($check == 0) {
                     Check_sit_auto::insert([
                         'vn' => $value2->vn,
                         'an' => $value2->an,
                         'hn' => $value2->hn,
                         'cid' => $value2->cid,
                         'pttype' => $value2->pttype,
                         'dchdate' => $value2->dchdate
                     ]);
                 }
             }
             return view('authen.sit_pull_auto');
     }
    public function sit_acc_debtorauto(Request $request)
    {
        $datestart = $request->datestart;
        $dateend = $request->dateend;
        $date = date('Y-m-d');
        $token_data = DB::connection('mysql')->select('
            SELECT cid,token FROM ssop_token
        ');

        foreach ($token_data as $key => $valuetoken) {
            $cid_ = $valuetoken->cid;
            $token_ = $valuetoken->token;
        }
        $data_sitss = DB::connection('mysql')->select('
        SELECT cid,vn,an
		FROM acc_debtor
		WHERE vstdate BETWEEN "2023-07-01" AND "2023-07-05"
		AND subinscl IS NULL AND subinscl IS NULL AND status IS NULL
		LIMIT 100
        ');
        // BETWEEN "2023-01-05" AND "2023-05-16"       CURDATE()
        foreach ($data_sitss as $key => $item) {
            $pids = $item->cid;
            $vn = $item->vn;
            $an = $item->an;
            $client = new SoapClient("http://ucws.nhso.go.th/ucwstokenp1/UCWSTokenP1?wsdl",
                array(
                    "uri" => 'http://ucws.nhso.go.th/ucwstokenp1/UCWSTokenP1?xsd=1',
                                    "trace"      => 1,
                                    "exceptions" => 0,
                                    "cache_wsdl" => 0
                    )
                );
                $params = array(
                    'sequence' => array(
                        "user_person_id" => "$cid_",
                        "smctoken" => "$token_",
                        // "person_id" => "$pids"
                        "person_id" => "3450101451327"
                )
            );
            $contents = $client->__soapCall('searchCurrentByPID',$params);

            // $contents = $client->__soapCall('nhsoDataSetC1',$params);
            // dd( $contents);
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

                // dd( @$subinscl);
                IF(@$maininscl == "" || @$maininscl == null || @$status == "003" ){ #ถ้าเป็นค่าว่างไม่ต้อง insert
                    $date = date("Y-m-d");
                    Check_sit_auto::where('vn', $vn)
                        ->update([
                            'status' => 'จำหน่าย/เสียชีวิต',
                            'maininscl' => @$maininscl,
                            'startdate' => @$startdate,
                            'hmain' => @$hmain,
                            'subinscl' => @$subinscl,
                            'person_id_nhso' => @$person_id_nhso,
                            'hmain_op' => @$hmain_op,
                            'hmain_op_name' => @$hmain_op_name,
                            'hsub' => @$hsub,
                            'hsub_name' => @$hsub_name,
                            'subinscl_name' => @$subinscl_name,
                            'upsit_date'    => $date
                    ]);

                    Acc_debtor::where('vn', $vn)
                        ->update([
                            'status' => 'จำหน่าย/เสียชีวิต',
                            'maininscl' => @$maininscl,
                            'hmain' => @$hmain,
                            'subinscl' => @$subinscl,
                            'pttype_spsch' => @$subinscl,
                            'hsub' => @$hsub,

                    ]);
                }elseif(@$maininscl !="" || @$subinscl !=""){

                    // dd( $vn);
                        $date2 = date("Y-m-d");
                            Check_sit_auto::where('vn', $vn)
                            ->update([
                                'status' => @$status,
                                'maininscl' => @$maininscl,
                                'startdate' => @$startdate,
                                'hmain' => @$hmain,
                                'subinscl' => @$subinscl,
                                'person_id_nhso' => @$person_id_nhso,
                                'hmain_op' => @$hmain_op,
                                'hmain_op_name' => @$hmain_op_name,
                                'hsub' => @$hsub,
                                'hsub_name' => @$hsub_name,
                                'subinscl_name' => @$subinscl_name,
                                'upsit_date'    => $date2
                            ]);

                            Acc_debtor::where('vn', $vn)
                                ->update([
                                    'status' => 'จำหน่าย/เสียชีวิต',
                                    'maininscl' => @$maininscl,
                                    'hmain' => @$hmain,
                                    'subinscl' => @$subinscl,
                                    'pttype_spsch' => @$subinscl,
                                    'hsub' => @$hsub,
                                ]);

                // }else{

                //     Acc_debtor::where('vn', $vn)
                //     ->update([
                //         'status' => @$status,
                //         'maininscl' => @$maininscl,
                //         'hmain' => @$hmain,
                //         'subinscl' => @$subinscl,
                //         'pttype_spsch' => @$subinscl,
                //         'hsub' => @$hsub,
                //     ]);
                }

            }
        }

        return view('account_pk.sit_acc_debtorauto');

    }
    public function account_pk_dash(Request $request)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $dabudget_year = DB::table('budget_year')->where('active','=',true)->first();

        $leave_month_year = DB::table('leave_month')->orderBy('MONTH_ID', 'ASC')->get();
        $date = date('Y-m-d');
        $y = date('Y') + 543;
        $newweek = date('Y-m-d', strtotime($date . ' -1 week')); //ย้อนหลัง 1 สัปดาห์
        $newDate = date('Y-m-d', strtotime($date . ' -5 months')); //ย้อนหลัง 5 เดือน
        $newyear = date('Y-m-d', strtotime($date . ' -1 year')); //ย้อนหลัง 1 ปี

        if ($startdate == '') {
            $datashow = DB::select('
                    SELECT month(a.dchdate) as months,year(a.dchdate) as year,l.MONTH_NAME
                    ,count(distinct a.hn) as hn
                    ,count(distinct a.vn) as vn
                    ,count(distinct a.an) as an
                    ,sum(a.income) as income
                    ,sum(a.paid_money) as paid_money
                    ,sum(a.income)-sum(a.discount_money)-sum(a.rcpt_money) as total
                    ,sum(a.debit) as debit
                    FROM acc_debtor a
                    left outer join leave_month l on l.MONTH_ID = month(a.dchdate)
                    WHERE a.dchdate between "'.$newyear.'" and "'.$date.'"
                    and account_code="1102050101.217"
                    group by month(a.dchdate) desc;
            ');
            // and stamp = "N"
        } else {
            $datashow = DB::select('
                    SELECT month(a.dchdate) as months,year(a.dchdate) as year,l.MONTH_NAME
                    ,count(distinct a.hn) as hn
                    ,count(distinct a.vn) as vn
                    ,count(distinct a.an) as an
                    ,sum(a.income) as income
                    ,sum(a.paid_money) as paid_money
                    ,sum(a.income)-sum(a.discount_money)-sum(a.rcpt_money) as total
                    ,sum(a.debit) as debit
                    FROM acc_debtor a
                    left outer join leave_month l on l.MONTH_ID = month(a.dchdate)
                    WHERE a.dchdate between "'.$startdate.'" and "'.$enddate.'"
                    and account_code="1102050101.217"
                    group by month(a.dchdate) desc;
            ');
        }

        $realhos = DB::select('
            SELECT
            SUM(debit_total) as total
            ,COUNT(vn) as vn
            FROM acc_1102050101_401
        ');
        $realtime = DB::select('
            SELECT
            SUM(debit_total) as total
            ,COUNT(vn) as vn
            FROM acc_1102050101_401
        ');


        return view('account_pk.account_pk_dash',[
            'startdate'         => $startdate,
            'enddate'           => $enddate,
            'datashow'          => $datashow,
            'leave_month_year'  => $leave_month_year,
            'realtime'          => $realtime,
        ]);
    }
    public function account_pk(Request $request)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $pang = $request->pang_id;
        if ($pang == '') {
            // $pang_id = '';
        } else {
            $pangtype = DB::connection('mysql5')->table('pang')->where('pang_id', '=', $pang)->first();
            $pang_type = $pangtype->pang_type;
            $pang_id = $pang;
        }
        // dd($enddate);
        $data['com_tec'] = DB::table('com_tec')->get();
        $data['users'] = User::get();

        $check = Acc_debtor::count();

        if ($startdate == '') {
            $acc_debtor = Acc_debtor::where('stamp','=','N')->whereBetween('vstdate', [$datenow, $datenow])->get();
        } else {
            $acc_debtor = Acc_debtor::where('stamp','=','N')->whereBetween('vstdate', [$startdate, $enddate])->get();
        }

        return view('account_pk.account_pk', $data, [
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'acc_debtor'      =>     $acc_debtor,
            // 'datashow'       =>     $datashow
        ]);
    }
    public function account_pksave(Request $request)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->datepicker;
        $enddate = $request->datepicker2;
        $datashow = DB::connection('mysql3')->select('
            SELECT o.vn,ifnull(o.an,"") as an,o.hn,showcid(pt.cid) as cid
                    ,concat(pt.pname,pt.fname," ",pt.lname) as ptname
                    ,setdate(o.vstdate) as vstdate,totime(o.vsttime) as vsttime
                    ,v.hospmain
                    ,o.vstdate as vstdatesave
                    ,seekname(o.pt_subtype,"pt_subtype") as ptsubtype
                    ,ptt.pttype_eclaim_id
                    ,o.pttype
                    ,e.gf_opd as gfmis,e.code as acc_code
                    ,e.ar_opd as account_code
                    ,e.name as account_name
                    ,v.income,v.uc_money,v.discount_money,v.paid_money,v.rcpt_money
                    ,v.rcpno_list as rcpno
                    ,v.income-v.discount_money-v.rcpt_money as debit
                    ,sum(if(op.income="02",sum_price,0)) as debit_instument
                    ,sum(if(op.icode IN("1560016","1540073","1530005","1540048","1620015","1600012","1600015"),sum_price,0)) as debit_drug
                    ,sum(if(op.icode IN ("3001412","3001417"),sum_price,0)) as debit_toa
                    ,sum(if(op.icode IN ("3010829","3010726 "),sum_price,0)) as debit_refer
                    ,ptt.max_debt_money
                from ovst o
                left join vn_stat v on v.vn=o.vn
                left join patient pt on pt.hn=o.hn
                LEFT JOIN pttype ptt on o.pttype=ptt.pttype
                LEFT JOIN pttype_eclaim e on e.code=ptt.pttype_eclaim_id
                LEFT JOIN opitemrece op ON op.vn = o.vn

            where o.vstdate between "' . $startdate . '" and "' . $enddate . '"

            group by o.vn
        ');
        // and o.an IS NULL
            foreach ($datashow as $key => $value) {
                $check = Acc_debtor::where('vn', $value->vn)->count();
                // $check = Acc_debtor::where('vn', $value->vn)->whereBetween('vstdate', [$startdate, $enddate])->count();
                if ($check > 0) {
                    // Acc_debtor::where('vn', $value->vn)
                    // ->update([
                    //     'hn'                => $value->hn,
                    //     'an'                => $value->an,
                    //     'cid'               => $value->cid,
                    //     'ptname'            => $value->ptname,
                    //     'ptsubtype'         => $value->ptsubtype,
                    //     'pttype_eclaim_id'  => $value->pttype_acc_eclaimid,
                    //     'hospmain'          => $value->hospmain,
                    //     'pttype'            => $value->pttype,
                    //     'pttypename'        => $value->pttype_acc_name,
                    //     'vstdate'           => $value->vstdatesave,
                    //     'vsttime'           => $value->vsttime,
                    //     'gfmis'             => $value->gfmis,
                    //     'acc_code'          => $value->acc_code,
                    //     'account_code'      => $value->account_code,
                    //     'account_name'      => $value->account_name,
                    //     'income'            => $value->income,
                    //     'uc_money'          => $value->uc_money,
                    //     'discount_money'    => $value->discount_money,
                    //     'paid_money'        => $value->paid_money,
                    //     'rcpt_money'        => $value->rcpt_money,
                    //     'rcpno'             => $value->rcpno,
                    //     'debit'             => $value->debit
                    // ]);
                } else {
                    // if ($check == 0) {
                    Acc_debtor::insert([
                        'hn'                 => $value->hn,
                        'an'                 => $value->an,
                        'vn'                 => $value->vn,
                        'cid'                => $value->cid,
                        'ptname'             => $value->ptname,
                        'ptsubtype'          => $value->ptsubtype,
                        'pttype_eclaim_id'   => $value->pttype_eclaim_id,
                        'hospmain'           => $value->hospmain,
                        'pttype'             => $value->pttype,
                        'vstdate'            => $value->vstdatesave,
                        'vsttime'            => $value->vsttime,
                        'acc_code'           => $value->acc_code,
                        'account_code'       => $value->account_code,
                        'account_name'       => $value->account_name,
                        'income'             => $value->income,
                        'uc_money'           => $value->uc_money,
                        'discount_money'     => $value->discount_money,
                        'paid_money'         => $value->paid_money,
                        'rcpt_money'         => $value->rcpt_money,
                        'rcpno'              => $value->rcpno,
                        'debit'              => $value->debit,
                        'debit_drug'         => $value->debit_drug,
                        'debit_instument'    => $value->debit_instument,
                        'debit_toa'          => $value->debit_toa,
                        'debit_refer'        => $value->debit_refer,
                        'max_debt_amount'    => $value->max_debt_money
                    ]);

                $acc_opitemrece_ = DB::connection('mysql3')->select('
                    SELECT o.vn,o.an,o.hn,o.vstdate,o.rxdate,o.income as income_group,o.pttype,o.paidst
                    ,o.icode,s.name as iname,o.qty,o.cost,o.finance_number,o.unitprice,o.discount,o.sum_price
                    FROM opitemrece o
                    LEFT JOIN vn_stat v ON v.vn = o.vn
                    left outer join s_drugitems s on s.icode = o.icode
                    WHERE o.vn ="'.$value->vn.'"

                ');
                foreach ($acc_opitemrece_ as $key => $va2) {
                    Acc_opitemrece::insert([
                        'hn'                 => $va2->hn,
                        'an'                 => $va2->an,
                        'vn'                 => $va2->vn,
                        'pttype'             => $va2->pttype,
                        'paidst'             => $va2->paidst,
                        'rxdate'             => $va2->rxdate,
                        'vstdate'            => $va2->vstdate,
                        'income'             => $va2->income_group,
                        'icode'              => $va2->icode,
                        'name'               => $va2->iname,
                        'qty'                => $va2->qty,
                        'cost'               => $va2->cost,
                        'finance_number'     => $va2->finance_number,
                        'unitprice'          => $va2->unitprice,
                        'discount'           => $va2->discount,
                        'sum_price'          => $va2->sum_price,
                    ]);
                }
            }
            }

        return response()->json([
            'status'        => '200'
        ]);
    }
    public function account_pkCheck_sit(Request $request)
    {
        $startdate = $request->datepicker;
        $enddate = $request->datepicker2;
        $date = date('Y-m-d');

        $token_data = DB::connection('mysql7')->select('
            SELECT cid,token FROM ssop_token
        ');
        foreach ($token_data as $key => $valuetoken) {
            $cid_ = $valuetoken->cid;
            $token_ = $valuetoken->token;
        }
        // $data_sitss = DB::connection('mysql')->select('
        //     SELECT *
        //     FROM acc_debtor
        //     WHERE vstdate BETWEEN "'.$datestart.'" AND "'.$dateend.'"
        //     AND pttype_spsch IS NULL
        // ');
        $data_sitss = Acc_debtor::whereBetween('vstdate', [$startdate, $enddate])
        ->whereNull('pttype_spsch')
        ->get();
        //   dd($data_sitss);
        foreach ($data_sitss as $key => $item) {
            $pids = $item->cid;
            $vn = $item->vn;
            $hn = $item->hn;
            $vsttime = $item->vsttime;
            $vstdate = $item->vstdate;
            $ptname = $item->ptname;

            $client = new SoapClient("http://ucws.nhso.go.th/ucwstokenp1/UCWSTokenP1?wsdl",
                array(
                    "uri" => 'http://ucws.nhso.go.th/ucwstokenp1/UCWSTokenP1?xsd=1',
                                    "trace"      => 1,
                                    "exceptions" => 0,
                                    "cache_wsdl" => 0
                    )
                );
                $params = array(
                    'sequence' => array(
                        "user_person_id" => "$cid_",
                        "smctoken" => "$token_",
                        "person_id" => "$pids"
                )
            );
            $contents = $client->__soapCall('searchCurrentByPID',$params);
        //    dd($contents);
            foreach ($contents as $key => $v) {
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
                IF(@$maininscl =="" || @$maininscl==null || @$status =="003" ){ #ถ้าเป็นค่าว่างไม่ต้อง insert
                        $date_now = date('Y-m-d');
                        Acc_debtor::where('vn', $vn)
                            ->update([
                                'status'         => 'จำหน่าย/เสียชีวิต',
                                'maininscl'      => @$maininscl,
                                'pttype_spsch'      => @$subinscl,
                                'hmain'          => @$hmain,
                                'subinscl'       => @$subinscl,
                                'hmain_op'       => @$hmain_op,
                                'hmain_op_name'  => @$hmain_op_name,
                                'hsub'           => @$hsub,
                                'hsub_name'      => @$hsub_name,
                                'subinscl_name'  => @$subinscl_name
                            ]);
                  }elseif(@$maininscl !="" || @$subinscl !=""){
                        $date_now2 = date('Y-m-d');
                        Acc_debtor::where('vn', $vn)
                            ->update([
                                'status'        => @$status,
                                'maininscl'     => @$maininscl,
                                'pttype_spsch'     => @$subinscl,
                                'hmain'         => @$hmain,
                                'subinscl'      => @$subinscl,
                                'hmain_op'      => @$hmain_op,
                                'hmain_op_name' => @$hmain_op_name,
                                'hsub'          => @$hsub,
                                'hsub_name'     => @$hsub_name,
                                'subinscl_name' => @$subinscl_name
                            ]);

                  }

            }
        }
        $acc_debtor = Acc_debtor::whereBetween('vstdate', [$startdate, $enddate])->get();

        return response()->json([
            'status'     => '200',
            'acc_debtor'    => $acc_debtor,
            'start'     => $startdate,
            'end'        => $enddate,
        ]);
    }
    public function account_pkCheck_sitipd(Request $request)
    {
        $startdate = $request->datepicker;
        $enddate = $request->datepicker2;
        $date = date('Y-m-d');

        $token_data = DB::connection('mysql7')->select('
            SELECT cid,token FROM ssop_token
        ');
        foreach ($token_data as $key => $valuetoken) {
            $cid_ = $valuetoken->cid;
            $token_ = $valuetoken->token;
        }
        // $data_sitss = DB::connection('mysql')->select('
        //     SELECT *
        //     FROM acc_debtor
        //     WHERE vstdate BETWEEN "'.$datestart.'" AND "'.$dateend.'"
        //     AND pttype_spsch IS NULL
        // ');
        $data_sitss = Acc_debtor::whereBetween('dchdate', [$startdate, $enddate])
        ->whereNull('pttype_spsch')
        ->get();
        //   dd($data_sitss);
        foreach ($data_sitss as $key => $item) {
            $pids = $item->cid;
            $an = $item->an;
            $vn = $item->vn;
            $hn = $item->hn;
            $vsttime = $item->vsttime;
            $vstdate = $item->vstdate;
            $ptname = $item->ptname;

            $client = new SoapClient("http://ucws.nhso.go.th/ucwstokenp1/UCWSTokenP1?wsdl",
                array(
                    "uri" => 'http://ucws.nhso.go.th/ucwstokenp1/UCWSTokenP1?xsd=1',
                                    "trace"      => 1,
                                    "exceptions" => 0,
                                    "cache_wsdl" => 0
                    )
                );
                $params = array(
                    'sequence' => array(
                        "user_person_id" => "$cid_",
                        "smctoken" => "$token_",
                        "person_id" => "$pids"
                )
            );
            $contents = $client->__soapCall('searchCurrentByPID',$params);

            foreach ($contents as $key => $v) {
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
                IF(@$maininscl =="" || @$maininscl==null || @$status =="003" ){ #ถ้าเป็นค่าว่างไม่ต้อง insert
                        $date_now = date('Y-m-d');
                        Acc_debtor::where('an', $an)
                            ->update([
                                'status'         => 'จำหน่าย/เสียชีวิต',
                                'maininscl'      => @$maininscl,
                                'pttype_spsch'      => @$subinscl,
                                'hmain'          => @$hmain,
                                'subinscl'       => @$subinscl,
                                'hmain_op'       => @$hmain_op,
                                'hmain_op_name'  => @$hmain_op_name,
                                'hsub'           => @$hsub,
                                'hsub_name'      => @$hsub_name,
                                'subinscl_name'  => @$subinscl_name
                            ]);
                  }elseif(@$maininscl !="" || @$subinscl !=""){
                        $date_now2 = date('Y-m-d');
                        Acc_debtor::where('an', $an)
                            ->update([
                                'status'        => @$status,
                                'maininscl'     => @$maininscl,
                                'pttype_spsch'     => @$subinscl,
                                'hmain'         => @$hmain,
                                'subinscl'      => @$subinscl,
                                'hmain_op'      => @$hmain_op,
                                'hmain_op_name' => @$hmain_op_name,
                                'hsub'          => @$hsub,
                                'hsub_name'     => @$hsub_name,
                                'subinscl_name' => @$subinscl_name
                            ]);
                // }elseif(@$maininscl !="" || @$subinscl !=""){
                //             $date_now2 = date('Y-m-d');
                //             Acc_debtor::where('an', $an)
                //                 ->update([
                //                     'status'        => @$status,
                //                     'maininscl'     => @$maininscl,
                //                     'pttype_spsch'     => @$subinscl,
                //                     'hmain'         => @$hmain,
                //                     'subinscl'      => @$subinscl,
                //                     'hmain_op'      => @$hmain_op,
                //                     'hmain_op_name' => @$hmain_op_name,
                //                     'hsub'          => @$hsub,
                //                     'hsub_name'     => @$hsub_name,
                //                     'subinscl_name' => @$subinscl_name
                //                 ]);
                    }else{
                        $date_now2 = date('Y-m-d');
                        Acc_debtor::where('an', $an)
                            ->update([
                                'status'        => @$status,
                                'maininscl'     => @$maininscl,
                                'pttype_spsch'     => @$subinscl,
                                'hmain'         => @$hmain,
                                'subinscl'      => @$subinscl,
                                'hmain_op'      => @$hmain_op,
                                'hmain_op_name' => @$hmain_op_name,
                                'hsub'          => @$hsub,
                                'hsub_name'     => @$hsub_name,
                                'subinscl_name' => @$subinscl_name
                            ]);

                  }

            }
        }
        $acc_debtor = Acc_debtor::whereBetween('vstdate', [$startdate, $enddate])->get();

        return response()->json([
            'status'     => '200',
            'acc_debtor'    => $acc_debtor,
            'start'     => $startdate,
            'end'        => $enddate,
        ]);
    }

    // ***************** และ stam OPD********************************
    public function account_pk_debtor(Request $request)
    {
        $id = $request->ids;
        $iduser = Auth::user()->id;
        $data = Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))->get();

            Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))
                    ->update([
                        'stamp' => 'Y'
                    ]);

        foreach ($data as $key => $value) {
            $check = Acc_debtor_stamp::where('stamp_vn', $value->vn)->count();
            if ($check > 0) {
                Acc_debtor_stamp::where('stamp_vn', $value->vn)
                ->update([
                    // 'stamp_vn' => $value->vn,
                    'stamp_hn' => $value->hn,
                    'stamp_an' => $value->an,
                    'stamp_cid' => $value->cid,
                    'stamp_ptname' => $value->ptname,
                    'stamp_vstdate' => $value->vstdate,
                    'stamp_vsttime' => $value->vsttime,
                    'stamp_pttype' => $value->pttype,
                    'stamp_pttype_nhso' => $value->pttype_spsch,
                    'stamp_acc_code' => $value->acc_code,
                    'stamp_account_code' => $value->account_code,
                    'stamp_income' => $value->income,
                    'stamp_uc_money' => $value->uc_money,
                    'stamp_discount_money' => $value->discount_money,
                    'stamp_paid_money' => $value->paid_money,
                    'stamp_rcpt_money' => $value->rcpt_money,
                    'stamp_rcpno' => $value->rcpno,
                    'stamp_debit' => $value->debit,
                    'max_debt_amount' => $value->max_debt_amount,
                    'acc_debtor_userid' => $iduser
                ]);
            } else {
                $date = date('Y-m-d H:m:s');
                Acc_debtor_stamp::insert([
                    'stamp_vn' => $value->vn,
                    'stamp_hn' => $value->hn,
                    'stamp_an' => $value->an,
                    'stamp_cid' => $value->cid,
                    'stamp_ptname' => $value->ptname,
                    'stamp_vstdate' => $value->vstdate,
                    'stamp_vsttime' => $value->vsttime,
                    'stamp_pttype' => $value->pttype,
                    'stamp_pttype_nhso' => $value->pttype_spsch,
                    'stamp_acc_code' => $value->acc_code,
                    'stamp_account_code' => $value->account_code,
                    'stamp_income' => $value->income,
                    'stamp_uc_money' => $value->uc_money,
                    'stamp_discount_money' => $value->discount_money,
                    'stamp_paid_money' => $value->paid_money,
                    'stamp_rcpt_money' => $value->rcpt_money,
                    'stamp_rcpno' => $value->rcpno,
                    'stamp_debit' => $value->debit,
                    'max_debt_amount' => $value->max_debt_amount,
                    'created_at'=> $date,
                    'acc_debtor_userid' => $iduser

                ]);
            }
        }

        return response()->json([
            'status'    => '200'
        ]);
    }

    // ***************** Send การเงิน ********************************
    public function acc_debtor_send(Request $request)
    {
        $id = $request->ids;
        $iduser = Auth::user()->id;
        $data = Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))->get();

            // Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))
            //         ->update([
            //             'stamp' => 'Y'
            //         ]);

        foreach ($data as $key => $value) {
            $check = Acc_debtor_sendmoney::where('send_vn', $value->vn)->count();
            if ($check > 0) {
                Acc_debtor_stamp::where('send_vn', $value->vn)
                ->update([
                    'send_vn' => $value->vn,
                    'send_hn' => $value->hn,
                    'send_an' => $value->an,
                    'send_cid' => $value->cid,
                    'send_ptname' => $value->ptname,
                    'send_vstdate' => $value->vstdate,
                    'send_vsttime' => $value->vsttime,
                    'send_dchdate' => $value->dchdate,
                    'send_pttype' => $value->pttype,
                    'send_pttype_nhso' => $value->pttype_spsch,
                    'send_acc_code' => $value->acc_code,
                    'send_account_code' => $value->account_code,
                    'send_income' => $value->income,
                    'send_uc_money' => $value->uc_money,
                    'send_discount_money' => $value->discount_money,
                    'send_paid_money' => $value->paid_money,
                    'send_rcpt_money' => $value->rcpt_money,
                    'send_rcpno' => $value->rcpno,
                    'send_debit' => $value->debit,
                    'max_debt_amount' => $value->max_debt_amount,
                    'acc_debtor_userid' => $iduser
                ]);
            } else {
                $date = date('Y-m-d H:m:s');
                Acc_debtor_sendmoney::insert([
                    'send_vn' => $value->vn,
                    'send_hn' => $value->hn,
                    'send_an' => $value->an,
                    'send_cid' => $value->cid,
                    'send_ptname' => $value->ptname,
                    'send_vstdate' => $value->vstdate,
                    'send_vsttime' => $value->vsttime,
                    'send_dchdate' => $value->dchdate,
                    'send_pttype' => $value->pttype,
                    'send_pttype_nhso' => $value->pttype_spsch,
                    'send_acc_code' => $value->acc_code,
                    'send_account_code' => $value->account_code,
                    'send_income' => $value->income,
                    'send_uc_money' => $value->uc_money,
                    'send_discount_money' => $value->discount_money,
                    'send_paid_money' => $value->paid_money,
                    'send_rcpt_money' => $value->rcpt_money,
                    'send_rcpno' => $value->rcpno,
                    'send_debit' => $value->debit,
                    'max_debt_amount' => $value->max_debt_amount,
                    'created_at'=> $date,
                    'acc_debtor_userid' => $iduser
                ]);
            }
        }

        return response()->json([
            'status'    => '200'
        ]);
    }

    // *************************** IPD *******************************************

    public function account_pk_ipd(Request $request)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        if ($startdate == '') {
            $acc_debtor = Acc_debtor::where('stamp','=','N')->whereBetween('dchdate', [$datenow, $datenow])->get();
        } else {
            $acc_debtor = Acc_debtor::where('stamp','=','N')->whereBetween('dchdate', [$startdate, $enddate])->get();
        }

        return view('account_pk.account_pk_ipd',[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'acc_debtor'      =>     $acc_debtor,
        ]);
    }
    public function account_pk_ipdsave(Request $request)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->datepicker;
        $enddate = $request->datepicker2;
        Acc_opitemrece::truncate();
        $acc_debtor = DB::connection('mysql3')->select('
                SELECT a.vn,a.an,a.hn,pt.cid,concat(pt.pname,pt.fname," ",pt.lname) fullname
                ,a.regdate as admdate,a.dchdate as dchdate,v.vstdate,op.income as income_group
                ,a.pttype,ptt.max_debt_money,ec.code,ec.ar_ipd as account_code
                ,ec.name as account_name,ifnull(ec.ar_ipd,"") pang_debit
                ,a.income as income ,a.uc_money,a.rcpt_money as cash_money,a.discount_money
                ,a.income-a.rcpt_money-a.discount_money as looknee_money
                ,sum(if(op.income="02",sum_price,0)) as debit_instument
                ,sum(if(op.icode IN("1560016","1540073","1530005","1540048","1620015","1600012","1600015"),sum_price,0)) as debit_drug
                ,sum(if(op.icode IN ("3001412","3001417"),sum_price,0)) as debit_toa
                ,sum(if(op.icode IN ("3010829","3010726 "),sum_price,0)) as debit_refer
                from ipt ip
                LEFT JOIN hos.an_stat a ON ip.an = a.an
                LEFT JOIN patient pt on pt.hn=a.hn
                LEFT JOIN pttype ptt on a.pttype=ptt.pttype
                LEFT JOIN pttype_eclaim ec on ec.code=ptt.pttype_eclaim_id
                LEFT JOIN hos.ipt_pttype ipt ON ipt.an = a.an
                LEFT JOIN hos.opitemrece op ON ip.an = op.an
                LEFT JOIN hos.vn_stat v on v.vn = a.vn
            WHERE a.dchdate BETWEEN "' . $startdate . '" AND "' . $enddate . '"
            GROUP BY a.an;
        ');
        foreach ($acc_debtor as $key => $value) {
                    $check = Acc_debtor::where('an', $value->an)->whereBetween('dchdate', [$startdate, $enddate])->count();
                    if ($check == 0) {
                        Acc_debtor::insert([
                            'hn'                 => $value->hn,
                            'an'                 => $value->an,
                            'vn'                 => $value->vn,
                            'cid'                => $value->cid,
                            'ptname'             => $value->fullname,
                            'pttype'             => $value->pttype,
                            'vstdate'            => $value->vstdate,
                            'regdate'            => $value->admdate,
                            'dchdate'            => $value->dchdate,
                            'acc_code'           => $value->code,
                            'account_code'       => $value->pang_debit,
                            'account_name'       => $value->account_name,
                            'income_group'       => $value->income_group,
                            'income'             => $value->income,
                            'uc_money'           => $value->uc_money,
                            'discount_money'     => $value->discount_money,
                            'paid_money'         => $value->cash_money,
                            'rcpt_money'         => $value->cash_money,
                            'debit'              => $value->looknee_money,
                            'debit_drug'         => $value->debit_drug,
                            'debit_instument'    => $value->debit_instument,
                            'debit_toa'          => $value->debit_toa,
                            'debit_refer'        => $value->debit_refer,
                            'debit_total'        => $value->looknee_money,
                            'max_debt_amount'    => $value->max_debt_money
                        ]);
                    }

                    if ($value->debit_toa > 0) {
                            Acc_debtor::where('an', $value->an)->where('account_code', '1102050101.202')->whereBetween('dchdate', [$startdate, $enddate])
                            ->update([
                                'acc_code'         => "03",
                                'account_code'     => "1102050101.217",
                                'account_name'     => "บริการเฉพาะ(CR)"
                            ]);
                    }
                    if ($value->debit_instument > 0 && $value->pang_debit =='1102050101.202') {
                            $checkins = Acc_debtor::where('an', $value->an)->where('account_code', '1102050101.217')->count();

                            if ($checkins == 0) {
                                Acc_debtor::insert([
                                    'hn'                 => $value->hn,
                                    'an'                 => $value->an,
                                    'vn'                 => $value->vn,
                                    'cid'                => $value->cid,
                                    'ptname'             => $value->fullname,
                                    'pttype'             => $value->pttype,
                                    'vstdate'            => $value->vstdate,
                                    'regdate'            => $value->admdate,
                                    'dchdate'            => $value->dchdate,
                                    'acc_code'           => "03",
                                    'account_code'       => '1102050101.217',
                                    'account_name'       => 'บริการเฉพาะ(CR)',
                                    'income_group'       => '02',
                                    'debit'              => $value->debit_instument,
                                    'debit_ipd_total'    => $value->debit_instument
                                ]);
                            }
                    }
                    if ($value->debit_drug > 0 && $value->pang_debit =='1102050101.202') {
                            $checkindrug = Acc_debtor::where('an', $value->an)->where('account_code', '1102050101.217')->where('debit','=',$value->debit_drug)->count();
                            if ($checkindrug == 0) {
                                Acc_debtor::insert([
                                    'hn'                 => $value->hn,
                                    'an'                 => $value->an,
                                    'vn'                 => $value->vn,
                                    'cid'                => $value->cid,
                                    'ptname'             => $value->fullname,
                                    'pttype'             => $value->pttype,
                                    'vstdate'            => $value->vstdate,
                                    'regdate'            => $value->admdate,
                                    'dchdate'            => $value->dchdate,
                                    'acc_code'           => "03",
                                    'account_code'       => '1102050101.217',
                                    'account_name'       => 'บริการเฉพาะ(CR)',
                                    'income_group'       => '03',
                                    'debit'              => $value->debit_drug,
                                    'debit_ipd_total'    => $value->debit_drug
                                ]);
                            }
                    }
                    if ($value->debit_refer > 0 && $value->pang_debit =='1102050101.202') {
                        $checkinrefer = Acc_debtor::where('an', $value->an)->where('account_code', '1102050101.217')->where('debit','=',$value->debit_refer)->count();
                        if ($checkinrefer == 0) {
                            Acc_debtor::insert([
                                'hn'                 => $value->hn,
                                'an'                 => $value->an,
                                'vn'                 => $value->vn,
                                'cid'                => $value->cid,
                                'ptname'             => $value->fullname,
                                'pttype'             => $value->pttype,
                                'vstdate'            => $value->vstdate,
                                'regdate'            => $value->admdate,
                                'dchdate'            => $value->dchdate,
                                'acc_code'           => "03",
                                'account_code'       => '1102050101.217',
                                'account_name'       => 'บริการเฉพาะ(CR)',
                                'income_group'       => '20',
                                'debit'              => $value->debit_refer,
                                'debit_ipd_total'    => $value->debit_refer
                            ]);
                        }
                    }

                    $acc_opitemrece_ = DB::connection('mysql3')->select('
                            SELECT a.vn,o.an,o.hn,o.vstdate,o.rxdate,a.dchdate,o.income as income_group,o.pttype,o.paidst
                            ,o.icode,s.name as iname,o.qty,o.cost,o.finance_number,o.unitprice,o.discount,o.sum_price
                            FROM opitemrece o
                            LEFT JOIN an_stat a ON o.an = a.an
                            left outer join s_drugitems s on s.icode = o.icode
                            WHERE o.an ="'.$value->an.'"

                    ');
                    foreach ($acc_opitemrece_ as $key => $va2) {
                        Acc_opitemrece::insert([
                            'hn'                 => $va2->hn,
                            'an'                 => $va2->an,
                            'vn'                 => $va2->vn,
                            'pttype'             => $va2->pttype,
                            'paidst'             => $va2->paidst,
                            'rxdate'             => $va2->rxdate,
                            'vstdate'            => $va2->vstdate,
                            'dchdate'            => $va2->dchdate,
                            'income'             => $va2->income_group,
                            'icode'              => $va2->icode,
                            'name'               => $va2->iname,
                            'qty'                => $va2->qty,
                            'cost'               => $va2->cost,
                            'finance_number'     => $va2->finance_number,
                            'unitprice'          => $va2->unitprice,
                            'discount'           => $va2->discount,
                            'sum_price'          => $va2->sum_price,
                        ]);
                    }
        }
        return response()->json([
            'status'    => '200'
        ]);
    }

    // ***************** และ stam IPD********************************
    public function account_pk_debtor_ipd(Request $request)
    {
        $id = $request->ids;
        $iduser = Auth::user()->id;
        $data = Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))->get();

            Acc_debtor::whereIn('acc_debtor_id',explode(",",$id))
                    ->update([
                        'stamp' => 'Y'
                    ]);

        foreach ($data as $key => $value) {
            $check = Acc_debtor_stamp::where('stamp_an', $value->an)->count();
            if ($check > 0) {
                Acc_debtor_stamp::where('stamp_an', $value->an)
                ->update([
                    'stamp_vn' => $value->vn,
                    'stamp_hn' => $value->hn,
                    // 'stamp_an' => $value->an,
                    'stamp_cid' => $value->cid,
                    'stamp_ptname' => $value->ptname,
                    'stamp_vstdate' => $value->vstdate,
                    'stamp_vsttime' => $value->vsttime,
                    'stamp_pttype' => $value->pttype,
                    'stamp_pttype_nhso' => $value->pttype_spsch,
                    'stamp_acc_code' => $value->acc_code,
                    'stamp_account_code' => $value->account_code,
                    'stamp_income' => $value->income,
                    'stamp_uc_money' => $value->uc_money,
                    'stamp_discount_money' => $value->discount_money,
                    'stamp_paid_money' => $value->paid_money,
                    'stamp_rcpt_money' => $value->rcpt_money,
                    'stamp_rcpno' => $value->rcpno,
                    'stamp_debit' => $value->debit,
                    'max_debt_amount' => $value->max_debt_amount,
                    'acc_debtor_userid' => $iduser
                ]);
            } else {
                $date = date('Y-m-d H:m:s');
                Acc_debtor_stamp::insert([
                    'stamp_vn' => $value->vn,
                    'stamp_hn' => $value->hn,
                    'stamp_an' => $value->an,
                    'stamp_cid' => $value->cid,
                    'stamp_ptname' => $value->ptname,
                    'stamp_vstdate' => $value->vstdate,
                    'stamp_vsttime' => $value->vsttime,
                    'stamp_pttype' => $value->pttype,
                    'stamp_pttype_nhso' => $value->pttype_spsch,
                    'stamp_acc_code' => $value->acc_code,
                    'stamp_account_code' => $value->account_code,
                    'stamp_income' => $value->income,
                    'stamp_uc_money' => $value->uc_money,
                    'stamp_discount_money' => $value->discount_money,
                    'stamp_paid_money' => $value->paid_money,
                    'stamp_rcpt_money' => $value->rcpt_money,
                    'stamp_rcpno' => $value->rcpno,
                    'stamp_debit' => $value->debit,
                    'max_debt_amount' => $value->max_debt_amount,
                    'created_at'=> $date,
                    'acc_debtor_userid' => $iduser

                ]);
            }
        }

        return response()->json([
            'status'    => '200'
        ]);
    }

    public function acc_stm(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $filename = $request->filename;
        $dabudget_year = DB::table('budget_year')->where('active','=',true)->first();
        $data_startdate = $dabudget_year->date_begin;
        $data_enddate = $dabudget_year->date_end;
        $leave_month_year = DB::table('leave_month_year')->get();
        $data_month = DB::table('leave_month')->get();


        $acc_stm = DB::connection('mysql9')->select('
            SELECT IF(ps.pang_stamp_vn IS NULL,"","Y")AS Stamp
                ,ps.pang_stamp_vn AS "vn"
                ,ps.pang_stamp_hn AS "hn"
                ,ps.pang_stamp_an AS "an"
                ,ps.pang_stamp_vstdate
                ,ps.pang_stamp_nhso
                ,ps.pang_stamp_uc_money ,ps.pang_stamp_uc_money_kor_tok
                ,ps.pang_stamp_stm_money AS stm
                ,ps.pang_stamp_uc_money_minut_stm_money
                ,ps.pang_stamp_send
                ,ps.pang_stamp_id
                ,ps.pang_stamp
                ,ps.pang_stamp_stm_file_name ,ps.pang_stamp_stm_rep
                ,ps.pang_stamp_edit_send_id
                ,CONCAT(rn.receipt_book_id,"/",rn.receipt_number_id) AS receipt_n
                ,rn.receipt_date
                ,ps.pang_stamp_rcpt
                ,CONCAT(ps.pang_stamp_pname,ps.pang_stamp_fname," ",ps.pang_stamp_lname) AS pt_name

                FROM pang_stamp ps
                LEFT JOIN receipt_number rn ON ps.pang_stamp_stm_file_name = rn.receipt_number_stm_file_name


                WHERE ps.pang_stamp_stm_file_name ="'.$filename.'";
        ');
        // WHERE ps.pang_stamp = "1102050101.202"
        // AND ps.pang_stamp_vstdate BETWEEN "'.$startdate.'" AND "'.$enddate.'"

        $filen_ = DB::connection('mysql9')->select('SELECT pang_stamp_stm_file_name FROM pang_stamp group by pang_stamp_stm_file_name');

        $sum_uc_money_ = DB::connection('mysql9')->select('
            SELECT SUM(pang_stamp_uc_money) as sumuc_money
            FROM pang_stamp
            WHERE pang_stamp_stm_file_name ="'.$filename.'"
        ');
        foreach ($sum_uc_money_ as $key => $value) {
            $sum_uc_money = $value->sumuc_money;
        }

        $sum_stmuc_money_ = DB::connection('mysql9')->select('
            SELECT SUM(pang_stamp_stm_money) as sumstmuc_money
            FROM pang_stamp
            WHERE pang_stamp_stm_file_name ="'.$filename.'"
        ');
        foreach ($sum_stmuc_money_ as $key => $value2) {
            $sum_stmuc_money = $value2->sumstmuc_money;
        }

        $sum_hiegt_money_ = DB::connection('mysql9')->select('
            SELECT SUM(pang_stamp_uc_money_minut_stm_money) as sumsthieg_money
            FROM pang_stamp
            WHERE pang_stamp_stm_file_name ="'.$filename.'"
        ');
        foreach ($sum_hiegt_money_ as $key => $value3) {
            $sum_hiegt_money = $value3->sumsthieg_money;
        }


        // $data_file_ = DB::connection('mysql9')->table('pang_stamp')
        // ->leftjoin('stm','stm.stm_file_name','=','pang_stamp.pang_stamp_stm_file_name')
        // ->where('pang_stamp_stm_file_name','=',$filename)->first();
        // $file_n = $data_file_->stm_file_name;


        // $file_n = $data_file_->pang_stamp_stm_file_name;

        // $data_file_ = DB::connection('mysql9')->select('
        //     SELECT * FROM stm s
        //     LEFT JOIN pang_stamp p ON p.pang_stamp_stm_file_name = s.stm_file_name
        //     WHERE pang_stamp_stm_file_name ="'.$filename.'"
        // ');

        return view('account_pk.acc_stm',[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'acc_stm'       =>     $acc_stm,
            'filen_'        =>     $filen_,
            'sum_uc_money'  =>     $sum_uc_money,
            'sum_stmuc_money'  =>  $sum_stmuc_money,
            'sum_hiegt_money'  =>  $sum_hiegt_money,
            // 'file_n'  =>  $file_n
        ]);
    }

    public function acc_repstm(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $filename = $request->filename;
        $pang_stamp = $request->pang_stamp;
        $dabudget_year = DB::table('budget_year')->where('active','=',true)->first();
        $data_startdate = $dabudget_year->date_begin;
        $data_enddate = $dabudget_year->date_end;
        $leave_month_year = DB::table('leave_month_year')->get();
        $data_month = DB::table('leave_month')->get();
        $pang = DB::connection('mysql9')->table('pang')->get();

        $acc_stm = DB::connection('mysql9')->select('
            SELECT IF(ps.pang_stamp_vn IS NULL,"","Y")AS Stamp
                ,ps.pang_stamp_vn AS "vn"
                ,ps.pang_stamp_hn AS "hn"
                ,ps.pang_stamp_an AS "an"
                ,ps.pang_stamp_vstdate
                ,ps.pang_stamp_nhso
                ,ps.pang_stamp_uc_money ,ps.pang_stamp_uc_money_kor_tok
                ,ps.pang_stamp_stm_money AS stm
                ,ps.pang_stamp_uc_money_minut_stm_money
                ,ps.pang_stamp_send
                ,ps.pang_stamp_id
                ,ps.pang_stamp
                ,ps.pang_stamp_stm_file_name ,ps.pang_stamp_stm_rep
                ,ps.pang_stamp_edit_send_id
                ,CONCAT(rn.receipt_book_id,"/",rn.receipt_number_id) AS receipt_n
                ,rn.receipt_date
                ,ps.pang_stamp_rcpt
                ,SUM(ati.price_approve) as price_approve
                ,CONCAT(ps.pang_stamp_pname,ps.pang_stamp_fname," ",ps.pang_stamp_lname) AS pt_name

                FROM pang_stamp ps
                LEFT JOIN receipt_number rn ON ps.pang_stamp_stm_file_name = rn.receipt_number_stm_file_name
                LEFT JOIN acc_stm_ti ati ON ati.hn = ps.pang_stamp_hn and ati.vstdate = ps.pang_stamp_vstdate
                WHERE ati.vstdate BETWEEN "'.$startdate.'" AND "'.$enddate.'"

                AND pang_stamp = "'.$pang_stamp.'"
                GROUP BY ati.cid,ati.vstdate
                ORDER BY ps.pang_stamp_hn ;
        ');
        $filen_ = DB::connection('mysql9')->select('SELECT pang_stamp_stm_file_name FROM pang_stamp group by pang_stamp_stm_file_name');
        $sum_uc_money_ = DB::connection('mysql9')->select('
            SELECT SUM(pang_stamp_uc_money) as sumuc_money
            FROM pang_stamp
            WHERE pang_stamp_vstdate BETWEEN "'.$startdate.'" AND "'.$enddate.'"
            AND pang_stamp_send IS NOT NULL
            AND pang_stamp_uc_money <> 0
            AND pang_stamp = "'.$pang_stamp.'"
        ');
        foreach ($sum_uc_money_ as $key => $value) {
            $sum_uc_money = $value->sumuc_money;

        }

        $sum_stmuc_money_ = DB::connection('mysql9')->select('
            SELECT SUM(ps.pang_stamp_stm_money) as sumstmuc_money ,SUM(ati.price_approve) as price_approve
            FROM pang_stamp ps
            LEFT JOIN acc_stm_ti ati ON ati.hn = ps.pang_stamp_hn and ati.vstdate = ps.pang_stamp_vstdate
            WHERE ati.vstdate BETWEEN "'.$startdate.'" AND "'.$enddate.'"
            AND ps.pang_stamp_send IS NOT NULL
            AND ps.pang_stamp_uc_money <> 0
            AND ps.pang_stamp = "'.$pang_stamp.'"

        ');
        foreach ($sum_stmuc_money_ as $key => $value2) {
            $sum_stmuc_money = $value2->sumstmuc_money;
            $price_approve = $value2->price_approve;
        }

        $sum_hiegt_money_ = DB::connection('mysql9')->select('
            SELECT SUM(pang_stamp_uc_money_minut_stm_money) as sumsthieg_money
            FROM pang_stamp
            WHERE pang_stamp_vstdate BETWEEN "'.$startdate.'" AND "'.$enddate.'"
            AND pang_stamp_send IS NOT NULL
            AND pang_stamp_uc_money <> 0
            AND pang_stamp = "'.$pang_stamp.'"
        ');
        foreach ($sum_hiegt_money_ as $key => $value3) {
            $sum_hiegt_money = $value3->sumsthieg_money;
        }
        // $data_file_ = DB::connection('mysql9')->table('pang_stamp')->where('pang_stamp_stm_file_name','=',$filename)->first();
        // $file_n = $data_file_->pang_stamp_stm_file_name;

        return view('account_pk.acc_repstm',[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'acc_stm'       =>     $acc_stm,
            'filen_'        =>     $filen_,
            'sum_uc_money'  =>     $sum_uc_money,
            'sum_stmuc_money'  =>  $sum_stmuc_money,
            'price_approve'    =>  $price_approve,
            'sum_hiegt_money'  =>  $sum_hiegt_money,
            'pang'             =>  $pang,
            'pang_stamp'       =>  $pang_stamp
        ]);
    }

    public function upstm(Request $request)
    {
         return view('account_pk.upstm');
    }
    public function upstm_save(Request $request)
    {
        if ($request->hasfile('file')) {

            $image = $request->file('file');
            $imageName = time().'.'.$image->extension();
            $image->move(public_path('Stm'),$imageName);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'Hello World !');

            $writer = new Xlsx($spreadsheet);
            $writer->save('hello world.xlsx');

        }

        // I have a table and therefore model to list all excels
        // $excelfile = ExcelFile::fromForm($request->file('file'));

        return response()->json(['success'=>$imageName]);
    }
    public function upstm_import(Request $request)
    {

        if ($request->hasfile('file')) {
            $inputFileName = time().'.'.$image->extension();
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($inputFileName);

            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            $highestRow = $objWorksheet->getHighestRow();
            $highestColumn = $objWorksheet->getHighestColumn();

            $headingsArray = $objWorksheet->rangeToArray('A1:'.$highestColumn.'1',null, true, true, true);
            $headingsArray = $headingsArray[1];

            $r = -1;
            $namedDataArray = array();
            for ($row = 2; $row <= $highestRow; ++$row) {
                $dataRow = $objWorksheet->rangeToArray('A'.$row.':'.$highestColumn.$row,null, true, true, true);
                if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '') && (is_numeric($dataRow[$row]['A'])) && (empty($dataRow[$row]['X'])) ) { //ตรวจคอลัมน์ Excel
                    ++$r;
                    foreach($headingsArray as $columnKey => $columnHeading) {
                        //$namedDataArray[$r][$columnHeading] = $dataRow[$row][$columnKey];
                        foreach (range('A', 'W') as $column){
                            $namedDataArray[$r][$column] = $dataRow[$row][$column];
                        }
                    }
                }elseif( isset($dataRow[$row]['X']) ){
                    $show_error = "<font style='background-color: red'>ไม่ใช่ STM ข้าราชการ</font>";
                }
            }
        }
        // dd( $namedDataArray);

     return view('account_pk.upstm');
    }

    public function ti2166_send(Request $request,$id)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        // dd($id);
        $data['users'] = User::get();

        $acc_debtor = DB::select('
                SELECT * from acc_debtor a
                WHERE stamp="Y"
                and account_code="1102050101.2166"
                and month(vstdate) = "'.$id.'";
            ');
            // left join acc_debtor_stamp ad on ad.stamp_vn=a.vn
        return view('account_pk.ti2166_send', $data, [
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'acc_debtor'      =>     $acc_debtor,
            'id'       =>     $id
        ]);
    }

    public function ti2166_detail(Request $request,$id)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $dabudget_year = DB::table('budget_year')->where('active','=',true)->first();

        $data_startdate = $dabudget_year->date_begin;
        $data_enddate = $dabudget_year->date_end;
        $leave_month_year = DB::table('leave_month_year')->get();
        $data_month = DB::table('leave_month')->get();
        // dd($id);
        $data['users'] = User::get();

        $acc_debtor = DB::select('
                SELECT * from acc_debtor a
                WHERE account_code="1102050101.2166"
                and income <> 0
                and month(vstdate) = "'.$id.'";
            ');

        return view('account_pk.ti2166_detail', $data, [
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'acc_debtor'      =>     $acc_debtor,
            'leave_month_year' =>  $leave_month_year,
            'id'       =>     $id
        ]);
    }

    public function upstm_ofcexcel(Request $request)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
                SELECT repno,vstdate,SUM(pricereq_all) as Sumprice,STMdoc,month(vstdate) as months
                FROM acc_stm_ofcexcel
                GROUP BY repno
            ');
        $countc = DB::table('acc_stm_ofcexcel')->count();
        // dd($countc );
        return view('account_pk.upstm_ofcexcel',[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'countc'        =>     $countc
        ]);
    }
    // upstm_ofcexcel_senddata
    public function upstm_ofcexcel_save(Request $request)
    {
            // Excel::import(new ImportAcc_stm_ofcexcel_import, $request->file('file')->store('files'));
            //  return response()->json([
            //     'status'    => '200',
            // ]);

            $this->validate($request, [
                'file' => 'required|file|mimes:xls,xlsx'
            ]);
            $the_file = $request->file('file');
            $file_ = $request->file('file')->getClientOriginalName(); //ชื่อไฟล์

                    try{
                        $spreadsheet = IOFactory::load($the_file->getRealPath());
                        // $sheet        = $spreadsheet->getActiveSheet();
                        $sheet        = $spreadsheet->setActiveSheetIndex(0);
                        $row_limit    = $sheet->getHighestDataRow();
                        $column_limit = $sheet->getHighestDataColumn();
                        $row_range    = range( '12', $row_limit );
                        // $row_range    = range( "!", $row_limit );
                        $column_range = range( 'T', $column_limit );
                        $startcount = '12';
                        // $row_range_namefile  = range( 9, $sheet->getCell( 'A' . $row )->getValue() );

                        $data = array();
                        foreach ($row_range as $row ) {

                            $vst = $sheet->getCell( 'G' . $row )->getValue();
                            // $starttime = substr($vst, 0, 5);
                            $day = substr($vst,0,2);
                            $mo = substr($vst,3,2);
                            $year = substr($vst,7,4);

                            $vsttime = substr($vst,12,8);
                            $hm = substr($vst,12,5);
                            $hh = substr($vst,12,2);
                            $mm = substr($vst,15,2);
                            $vstdate = $year.'-'.$mo.'-'.$day;

                            $reg = $sheet->getCell( 'H' . $row )->getValue();
                            // $starttime = substr($reg, 0, 5);
                            $regday = substr($reg, 0, 2);
                            $regmo = substr($reg, 3, 2);
                            $regyear = substr($reg, 7, 4);
                            $dchdate = $regyear.'-'.$regmo.'-'.$regday;

                            $k = $sheet->getCell( 'K' . $row )->getValue();
                            $del_k = str_replace(",","",$k);

                            $l = $sheet->getCell( 'L' . $row )->getValue();
                            $del_l = str_replace(",","",$l);
                            $m = $sheet->getCell( 'M' . $row )->getValue();
                            $del_m = str_replace(",","",$m);
                            $n = $sheet->getCell( 'N' . $row )->getValue();
                            $del_n = str_replace(",","",$n);
                            $o = $sheet->getCell( 'O' . $row )->getValue();
                            $del_o = str_replace(",","",$o);
                            $p = $sheet->getCell( 'P' . $row )->getValue();
                            $del_p = str_replace(",","",$p);
                            $q = $sheet->getCell( 'Q' . $row )->getValue();
                            $del_q = str_replace(",","",$q);
                            $r = $sheet->getCell( 'R' . $row )->getValue();
                            $del_r = str_replace(",","",$r);
                            $s = $sheet->getCell( 'S' . $row )->getValue();
                            $del_s = str_replace(",","",$s);
                            $t = $sheet->getCell( 'T' . $row )->getValue();
                            $del_t = str_replace(",","",$t);

                            $data[] = [
                                    'repno'                   =>$sheet->getCell( 'A' . $row )->getValue(),
                                    'no'                      =>$sheet->getCell( 'B' . $row )->getValue(),
                                    'hn'                      =>$sheet->getCell( 'C' . $row )->getValue(),
                                    'an'                      =>$sheet->getCell( 'D' . $row )->getValue(),
                                    'cid'                     =>$sheet->getCell( 'E' . $row )->getValue(),
                                    'fullname'                =>$sheet->getCell( 'F' . $row )->getValue(),
                                    'vstdate'                 =>$vstdate,

                                    'vsttime'                 =>$vsttime,
                                    'hm'                      =>$hm,
                                    'hh'                      =>$hh,
                                    'mm'                      =>$mm,

                                    'dchdate'                 =>$dchdate,
                                    'PROJCODE'                =>$sheet->getCell( 'I' . $row )->getValue(),
                                    'AdjRW'                   =>$sheet->getCell( 'J' . $row )->getValue(),
                                    'price_req'               =>$del_k,
                                    'prb'                     =>$del_l,
                                    'room'                    =>$del_m,
                                    'inst'                    =>$del_n,
                                    'drug'                    =>$del_o,
                                    'income'                  =>$del_p,
                                    'refer'                   =>$del_q,
                                    'waitdch'                 =>$del_r,
                                    'service'                 =>$del_s,
                                    'pricereq_all'            =>$del_t,
                                    'STMdoc'                  =>$file_
                            ];

                            $startcount++;

                        }
                        $for_insert = array_chunk($data, length:1000);
                        foreach ($for_insert as $key => $data_) {
                                Acc_stm_ofcexcel::insert($data_);
                        }





                        // DB::table('acc_stm_ofcexcel')->insert($data);
                    } catch (Exception $e) {
                        $error_code = $e->errorInfo[1];
                        return back()->withErrors('There was a problem uploading the data!');
                    }

            return response()->json([
                'status'    => '200',
            ]);
    }

    // Up STM BKK
    public function upstm_bkkexcel(Request $request)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select(
            'SELECT repno,vstdate,SUM(pricereq_all) as Sumprice,STMdoc,month(vstdate) as months
            FROM acc_stm_bkkexcel WHERE repno IS NOT NULL
            GROUP BY repno
        ');
        $datacount_op = DB::connection('mysql')->select(
            'SELECT count(STMdoc) as cou_op
            FROM acc_stm_bkkexcel WHERE (repno IS NOT NULL OR repno <> "") AND (an IS NULL OR an ="-")
        ');
        // AND STMdoc LIKE "%STM_10978_OP%"
        foreach ($datacount_op as $key => $value) {
            $data['count_op'] = $value->cou_op;
        }
        $datacount_ip = DB::connection('mysql')->select(
            'SELECT count(STMdoc) as cou_ip
            FROM acc_stm_bkkexcel WHERE (repno IS NOT NULL OR repno <> "") AND an <> "-"

        ');
        // AND STMdoc LIKE "%STM_10978_IP%"
         foreach ($datacount_ip as $key => $valuei) {
            $data['count_ip'] = $valuei->cou_ip;
        }
        $countc = DB::table('acc_stm_bkkexcel')->count();
        // dd($countc );
        return view('account_pk.upstm_bkkexcel',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'countc'        =>     $countc
        ]);
    }
    public function upstm_bkkexcel_save(Request $request)
    {
            $this->validate($request, [
                'file' => 'required|file|mimes:xls,xlsx'
            ]);
            $the_file = $request->file('file');
            $file_ = $request->file('file')->getClientOriginalName(); //ชื่อไฟล์

            try{
                $spreadsheet = IOFactory::load($the_file->getRealPath());
                // $sheet        = $spreadsheet->getActiveSheet();
                $sheet        = $spreadsheet->setActiveSheetIndex(0);
                $row_limit    = $sheet->getHighestDataRow();
                $column_limit = $sheet->getHighestDataColumn();
                $row_range    = range( '12', $row_limit );
                // $row_range    = range( "!", $row_limit );
                $column_range = range( 'T', $column_limit );
                $startcount = '12';
                // $row_range_namefile  = range( 9, $sheet->getCell( 'A' . $row )->getValue() );
                $data = array();
                foreach ($row_range as $row ) {

                    $vst = $sheet->getCell( 'G' . $row )->getValue();
                    // $starttime = substr($vst, 0, 5);
                    $day = substr($vst,0,2);
                    $mo = substr($vst,3,2);
                    $year = substr($vst,7,4);

                    $vsttime = substr($vst,12,8);
                    $hm = substr($vst,12,5);
                    $hh = substr($vst,12,2);
                    $mm = substr($vst,15,2);
                    $vstdate = $year.'-'.$mo.'-'.$day;

                    $reg = $sheet->getCell( 'H' . $row )->getValue();
                    // $starttime = substr($reg, 0, 5);
                    $regday = substr($reg, 0, 2);
                    $regmo = substr($reg, 3, 2);
                    $regyear = substr($reg, 7, 4);
                    $dchdate = $regyear.'-'.$regmo.'-'.$regday;

                    $k = $sheet->getCell( 'K' . $row )->getValue();
                    $del_k = str_replace(",","",$k);

                    $l = $sheet->getCell( 'L' . $row )->getValue();
                    $del_l = str_replace(",","",$l);
                    $m = $sheet->getCell( 'M' . $row )->getValue();
                    $del_m = str_replace(",","",$m);
                    $n = $sheet->getCell( 'N' . $row )->getValue();
                    $del_n = str_replace(",","",$n);
                    $o = $sheet->getCell( 'O' . $row )->getValue();
                    $del_o = str_replace(",","",$o);
                    $p = $sheet->getCell( 'P' . $row )->getValue();
                    $del_p = str_replace(",","",$p);
                    $q = $sheet->getCell( 'Q' . $row )->getValue();
                    $del_q = str_replace(",","",$q);
                    $r = $sheet->getCell( 'R' . $row )->getValue();
                    $del_r = str_replace(",","",$r);
                    $s = $sheet->getCell( 'S' . $row )->getValue();
                    $del_s = str_replace(",","",$s);
                    $t = $sheet->getCell( 'T' . $row )->getValue();
                    $del_t = str_replace(",","",$t);

                    $data[] = [
                            'repno'                   =>$sheet->getCell( 'A' . $row )->getValue(),
                            'no'                      =>$sheet->getCell( 'B' . $row )->getValue(),
                            'hn'                      =>$sheet->getCell( 'C' . $row )->getValue(),
                            'an'                      =>$sheet->getCell( 'D' . $row )->getValue(),
                            'cid'                     =>$sheet->getCell( 'E' . $row )->getValue(),
                            'fullname'                =>$sheet->getCell( 'F' . $row )->getValue(),
                            'vstdate'                 =>$vstdate,
                            'vsttime'                 =>$vsttime,
                            'hm'                      =>$hm,
                            'hh'                      =>$hh,
                            'mm'                      =>$mm,
                            'dchdate'                 =>$dchdate,
                            'PROJCODE'                =>$sheet->getCell( 'I' . $row )->getValue(),
                            'AdjRW'                   =>$sheet->getCell( 'J' . $row )->getValue(),
                            'price_req'               =>$del_k,
                            'prb'                     =>$del_l,
                            'room'                    =>$del_m,
                            'inst'                    =>$del_n,
                            'drug'                    =>$del_o,
                            'income'                  =>$del_p,
                            'refer'                   =>$del_q,
                            'waitdch'                 =>$del_r,
                            'service'                 =>$del_s,
                            'pricereq_all'            =>$del_t,
                            'STMdoc'                  =>$file_
                    ];
                    $startcount++;
                }
                $for_insert = array_chunk($data, length:1000);
                foreach ($for_insert as $key => $data_) {
                    Acc_stm_bkkexcel::insert($data_);
                }
                // DB::table('acc_stm_ofcexcel')->insert($data);
            } catch (Exception $e) {
                $error_code = $e->errorInfo[1];
                return back()->withErrors('There was a problem uploading the data!');
            }
            return response()->json([
                'status'    => '200',
            ]);
    }
    public function upstm_bkk803_senddata(Request $request)
    {
        // dd($type);
        try{
                $data_ = DB::connection('mysql')->select('
                    SELECT *
                    FROM acc_stm_bkkexcel
                    WHERE income <> "" AND repno <> ""

                ');
                $type = 'BKK_IPD';

                foreach ($data_ as $key => $value) {
                    // $value->no != '' && $value->repno != 'REP' &&
                    if ($value->repno != 'REP%' || $value->repno != '') {
                            $check = Acc_stm_bkk::where('repno','=',$value->repno)->where('no','=',$value->no)->count();
                            if ($check > 0) {
                                $add = Acc_stm_bkk::where('repno','=',$value->repno)->where('no','=',$value->no)->update([
                                    'type'     => $type
                                ]);
                            } else {
                                $add = new Acc_stm_bkk();
                                $add->repno          = $value->repno;
                                $add->no             = $value->no;
                                $add->hn             = $value->hn;
                                $add->an             = $value->an;
                                $add->cid            = $value->cid;
                                $add->fullname       = $value->fullname;
                                $add->vstdate        = $value->vstdate;
                                $add->dchdate        = $value->dchdate;
                                $add->PROJCODE       = $value->PROJCODE;
                                $add->AdjRW          = $value->AdjRW;
                                $add->price_req      = $value->price_req;
                                $add->prb            = $value->prb;
                                $add->room           = $value->room;
                                $add->inst           = $value->inst;
                                $add->drug           = $value->drug;
                                $add->income         = $value->income;
                                $add->refer          = $value->refer;
                                $add->waitdch        = $value->waitdch;
                                $add->service        = $value->service;
                                $add->pricereq_all   = $value->pricereq_all;
                                $add->STMdoc         = $value->STMdoc;
                                $add->type           = $type;
                                $add->save();
                            }

                            $check803 = Acc_1102050102_803::where('hn',$value->hn)->where('vstdate',$value->vstdate)->where('STMdoc',NULL)->count();
                            if ($check803 > 0) {
                                Acc_1102050102_803::where('hn',$value->hn)->where('vstdate',$value->vstdate)
                                ->update([
                                    'stm_rep'         => $value->price_req,
                                    'stm_money'       => $value->pricereq_all,
                                    'stm_rcpno'       => $value->repno.'-'.$value->no,
                                    'STMdoc'          => $value->STMdoc,
                                    'stm_total'       => $value->pricereq_all,
                                ]);
                            } else {
                            }

                    } else {
                        # code...
                    }

                }
        } catch (Exception $e) {
            $error_code = $e->errorInfo[1];
            return back()->withErrors('There was a problem uploading the data!');
        }
        Acc_stm_bkkexcel::truncate();
        return response()->json([
                'status'    => '200',
            ]);
    }

    //บันทึกข้อมูล
    public function upstm_ofcexcel_senddata(Request $request)
    {
        // dd($type);
        try{
                $data_ = DB::connection('mysql')->select('
                    SELECT *
                    FROM acc_stm_ofcexcel
                    WHERE income <> "" AND repno <> ""

                ');
                $type = $request->type;

                foreach ($data_ as $key => $value) {
                    // $value->no != '' && $value->repno != 'REP' &&
                    if ($value->repno != 'REP%' || $value->repno != '') {
                            $check = Acc_stm_ofc::where('repno','=',$value->repno)->where('no','=',$value->no)->count();
                            if ($check > 0) {
                                Acc_stm_ofc::where('repno','=',$value->repno)->where('no','=',$value->no)->update([
                                    'vsttime'        => $value->vsttime,
                                    'hm'             => $value->hm,
                                    'hh'             => $value->hh,
                                    'mm'             => $value->mm
                                ]);
                            } else {
                                $add = new Acc_stm_ofc();
                                $add->repno          = $value->repno;
                                $add->no             = $value->no;
                                $add->hn             = $value->hn;
                                $add->an             = $value->an;
                                $add->cid            = $value->cid;
                                $add->fullname       = $value->fullname;
                                $add->vstdate        = $value->vstdate;

                                $add->vsttime        = $value->vsttime;
                                $add->hm             = $value->hm;
                                $add->hh             = $value->hh;
                                $add->mm             = $value->mm;

                                $add->dchdate        = $value->dchdate;
                                $add->PROJCODE       = $value->PROJCODE;
                                $add->AdjRW          = $value->AdjRW;
                                $add->price_req      = $value->price_req;
                                $add->prb            = $value->prb;
                                $add->room           = $value->room;
                                $add->inst           = $value->inst;
                                $add->drug           = $value->drug;
                                $add->income         = $value->income;
                                $add->refer          = $value->refer;
                                $add->waitdch        = $value->waitdch;
                                $add->service        = $value->service;
                                $add->pricereq_all   = $value->pricereq_all;
                                $add->STMdoc         = $value->STMdoc;
                                $add->type           = $type;
                                $add->save();
                            }
                            // $check401 = Acc_1102050101_401::where('cid',$value->cid)->where('vstdate',$value->vstdate)->where('STMdoc',NULL)->count();
                            // if ($check401 > 0) {
                            //     Acc_1102050101_401::where('cid',$value->cid)->where('vstdate',$value->vstdate)
                            //     ->update([
                            //         'stm_rep'         => $value->price_req,
                            //         'stm_money'       => $value->pricereq_all,
                            //         'stm_rcpno'       => $value->repno.'-'.$value->no,
                            //         'stm_total'       => $value->pricereq_all,
                            //         'STMdoc'          => $value->STMdoc,
                            //     ]);
                            // }
                    } else {
                        # code...
                    }

                }
        } catch (Exception $e) {
            $error_code = $e->errorInfo[1];
            return back()->withErrors('There was a problem uploading the data!');
        }

        return response()->json([
                'status'    => '200',
            ]);
    }
    // กระทบลูกหนี้ 401
    public function upstm_ofcexcel_sendstmdata(Request $request)
    {
        try{
                $data_ = DB::connection('mysql')->select('
                    SELECT *
                    FROM acc_stm_ofcexcel
                    WHERE income <> "" AND repno <> ""
                ');
                foreach ($data_ as $key => $value) {
                    $check401 = Acc_1102050101_401::where('cid',$value->cid)->where('vstdate',$value->vstdate)->count();
                    // $check401 = Acc_1102050101_401::where('cid',$value->cid)->where('vstdate',$value->vstdate)->where('STMdoc',NULL)->count();
                    if ($check401 > 0) {
                        // $checkmore = Acc_1102050101_401::where('cid',$value->cid)->where('vstdate',$value->vstdate)->where('hm',$value->hm)->count();
                        // if ($checkmore > 0) {
                            Acc_1102050101_401::where('cid',$value->cid)->where('vstdate',$value->vstdate)->where('hm',$value->hm)
                            ->update([
                                // 'hm'              => $value->hm,
                                'stm_rep'         => $value->price_req,
                                'stm_money'       => $value->pricereq_all,
                                'stm_rcpno'       => $value->repno.'-'.$value->no,
                                'STMdoc'          => $value->STMdoc,
                            ]);
                        // } else {
                        //     Acc_1102050101_401::where('cid',$value->cid)->where('vstdate',$value->vstdate)
                        //     ->update([
                        //         'hm'              => $value->hm,
                        //         'stm_rep'         => $value->price_req,
                        //         'stm_money'       => $value->pricereq_all,
                        //         'stm_rcpno'       => $value->repno.'-'.$value->no,
                        //         'STMdoc'          => $value->STMdoc,
                        //     ]);
                        // }



                    } else {
                    }

                }
        } catch (Exception $e) {
            $error_code = $e->errorInfo[1];
            return back()->withErrors('There was a problem uploading the data!');
        }


        Acc_stm_ofcexcel::truncate();

        return response()->json([
                'status'    => '200',
            ]);
    }
    // 402
    public function upstm_ofcexcel_sendstmipddata(Request $request)
    {
        try{
                $data_ = DB::connection('mysql')->select('
                    SELECT *
                    FROM acc_stm_ofcexcel
                    WHERE income <> "" AND dchdate <> "0000-00-00"
                ');
                foreach ($data_ as $key => $value) {
                    $check402 = Acc_1102050101_402::where('an',$value->an)->where('STMdoc',NULL)->count();
                    if ($check402 > 0) {
                        Acc_1102050101_402::where('an',$value->an)
                        ->update([
                            'adjrw'           => $value->AdjRW,
                            'stm_rep'         => $value->price_req,
                            'stm_money'       => $value->pricereq_all,
                            'stm_rcpno'       => $value->repno.'-'.$value->no,
                            'STMdoc'          => $value->STMdoc,
                        ]);
                    } else {
                    }
                }
        } catch (Exception $e) {
            $error_code = $e->errorInfo[1];
            return back()->withErrors('There was a problem uploading the data!');
        }
        Acc_stm_ofcexcel::truncate();

        return response()->json([
                'status'    => '200',
            ]);
    }
    public function upstm_bkk804_senddata(Request $request)
    {
        // try{
                $data_ = DB::connection('mysql')->select('
                    SELECT *
                    FROM acc_stm_bkkexcel
                    WHERE income <> "" AND repno <> ""

                ');
                $type = $request->type;

                foreach ($data_ as $key => $value) {
                    // $value->no != '' && $value->repno != 'REP' &&
                    if ($value->repno != 'REP%' || $value->repno != '') {
                            $check = Acc_stm_ofc::where('repno','=',$value->repno)->where('no','=',$value->no)->count();
                            if ($check > 0) {
                                $add = Acc_stm_ofc::where('repno','=',$value->repno)->where('no','=',$value->no)->update([
                                    'type'     => $type
                                ]);
                            } else {
                                $add = new Acc_stm_ofc();
                                $add->repno          = $value->repno;
                                $add->no             = $value->no;
                                $add->hn             = $value->hn;
                                $add->an             = $value->an;
                                $add->cid            = $value->cid;
                                $add->fullname       = $value->fullname;
                                $add->vstdate        = $value->vstdate;
                                $add->dchdate        = $value->dchdate;
                                $add->PROJCODE       = $value->PROJCODE;
                                $add->AdjRW          = $value->AdjRW;
                                $add->price_req      = $value->price_req;
                                $add->prb            = $value->prb;
                                $add->room           = $value->room;
                                $add->inst           = $value->inst;
                                $add->drug           = $value->drug;
                                $add->income         = $value->income;
                                $add->refer          = $value->refer;
                                $add->waitdch        = $value->waitdch;
                                $add->service        = $value->service;
                                $add->pricereq_all   = $value->pricereq_all;
                                $add->STMdoc         = $value->STMdoc;
                                $add->type           = $type;
                                $add->save();
                            }

                            // $check804 = Acc_1102050102_804::where('an',$value->an)->where('STMdoc',NULL)->count();
                            // if ($check804 > 0) {
                                Acc_1102050102_804::where('an',$value->an)
                                ->update([
                                    'stm_rep'         => $value->price_req,
                                    'stm_money'       => $value->pricereq_all,
                                    'stm_rcpno'       => $value->repno.'-'.$value->no,
                                    'stm_total'       => $value->pricereq_all,
                                    'STMdoc'          => $value->STMdoc,
                                ]);
                            // }

                    } else {
                        # code...
                    }

                }
        // } catch (Exception $e) {
        //     $error_code = $e->errorInfo[1];
        //     return back()->withErrors('There was a problem uploading the data!');
        // }
        Acc_stm_bkkexcel::truncate();
        return response()->json([
                'status'    => '200',
            ]);
    }
    public function upstm_lgoexcel(Request $request)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
            SELECT rep_no,vstdate,SUM(pay) as Sumprice,STMdoc,month(vstdate) as months
            FROM acc_stm_lgoexcelnew
            WHERE rep_no <> ""
            GROUP BY rep_no
            ');
        $countc = DB::table('acc_stm_lgoexcelnew')->count();
        // dd($countc );
        return view('account_pk.upstm_lgoexcel',[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'countc'        =>     $countc
        ]);
    }

    //  ************************* Rep **************************
    public function upstm_lgoexcel_save_old(Request $request)
    {
            $this->validate($request, [
                'file' => 'required|file|mimes:xls,xlsx'
            ]);
            $the_file = $request->file('file');
            $file_ = $request->file('file')->getClientOriginalName(); //ชื่อไฟล์

            try{
                $spreadsheet = IOFactory::load($the_file->getRealPath());
                $sheet        = $spreadsheet->setActiveSheetIndex(0);
                $row_limit    = $sheet->getHighestDataRow();
                $column_limit = $sheet->getHighestDataColumn();
                $row_range    = range( '8', $row_limit );
                // $column_range = range( 'AO', $column_limit );
                $startcount = '8';
                // $row_range_namefile  = range( 9, $sheet->getCell( 'A' . $row )->getValue() );
                $data = array();
                foreach ($row_range as $row ) {

                    $vst = $sheet->getCell( 'I' . $row )->getValue();
                    // $starttime = substr($vst, 0, 5);
                    $day = substr($vst,0,2);
                    $mo = substr($vst,3,2);
                    $year = substr($vst,6,4);
                    $vstdate = $year.'-'.$mo.'-'.$day;

                    $dch = $sheet->getCell( 'J' . $row )->getValue();
                    // $starttime = substr($vst, 0, 5);
                    $day2 = substr($dch,0,2);
                    $mo2 = substr($dch,3,2);
                    $year2 = substr($dch,6,4);
                    $dchdate = $year2.'-'.$mo2.'-'.$day2;

                    $k = $sheet->getCell( 'K' . $row )->getValue();
                    $del_k = str_replace(",","",$k);
                    $l = $sheet->getCell( 'L' . $row )->getValue();
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
                    $an = $sheet->getCell( 'AN' . $row )->getValue();
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
                        $data[] = [
                            'rep_a'                   =>$sheet->getCell( 'A' . $row )->getValue(),
                            'no_b'                    =>$sheet->getCell( 'B' . $row )->getValue(),
                            'tranid_c'                =>$sheet->getCell( 'C' . $row )->getValue(),
                            'hn_d'                    =>$sheet->getCell( 'D' . $row )->getValue(),
                            'an_e'                    =>$sheet->getCell( 'E' . $row )->getValue(),
                            'cid_f'                   =>$sheet->getCell( 'F' . $row )->getValue(),
                            'fullname_g'              =>$sheet->getCell( 'G' . $row )->getValue(),
                            'type_h'                  =>$sheet->getCell( 'H' . $row )->getValue(),
                            'vstdate_i'               =>$vstdate,
                            'dchdate_j'               =>$dchdate,
                            'price1_k'                =>$del_k,
                            'pp_spsch_l'              =>$del_l,
                            'errorcode_m'             =>$sheet->getCell( 'M' . $row )->getValue(),
                            'kongtoon_n'              =>$sheet->getCell( 'N' . $row )->getValue(),
                            'typeservice_o'           =>$sheet->getCell( 'O' . $row )->getValue(),
                            'refer_p'                 =>$sheet->getCell( 'P' . $row )->getValue(),
                            'pttype_have_q'           =>$sheet->getCell( 'Q' . $row )->getValue(),
                            'pttype_true_r'           =>$sheet->getCell( 'R' . $row )->getValue(),
                            'mian_pttype_s'           =>$sheet->getCell( 'S' . $row )->getValue(),
                            'secon_pttype_t'          =>$sheet->getCell( 'T' . $row )->getValue(),
                            'href_u'                  =>$sheet->getCell( 'U' . $row )->getValue(),
                            'HCODE_v'                 =>$sheet->getCell( 'V' . $row )->getValue(),
                            'prov1_w'                 =>$sheet->getCell( 'W' . $row )->getValue(),
                            'code_dep_x'              =>$sheet->getCell( 'X' . $row )->getValue(),
                            'name_dep_y'              =>$sheet->getCell( 'Y' . $row )->getValue(),
                            'proj_z'                  =>$sheet->getCell( 'Z' . $row )->getValue(),
                            'pa_aa'                   =>$sheet->getCell( 'AA' . $row )->getValue(),
                            'drg_ab'                  =>$sheet->getCell( 'AB' . $row )->getValue(),
                            'rw_ac'                   =>$sheet->getCell( 'AC' . $row )->getValue(),
                            'income_ad'               =>$del_ad,
                            'pp_gep_ae'               =>$del_ae,
                            'claim_true_af'           =>$del_af,
                            'claim_false_ag'          =>$del_ag,
                            'cash_money_ah'           =>$del_ah,
                            'pay_ai'                  =>$del_ai,
                            'ps_aj'                   =>$sheet->getCell( 'AJ' . $row )->getValue(),
                            'ps_percent_ak'           =>$sheet->getCell( 'AK' . $row )->getValue(),
                            'ccuf_al'                 =>$sheet->getCell( 'AL' . $row )->getValue(),
                            'AdjRW_am'                =>$sheet->getCell( 'AM' . $row )->getValue(),
                            'plb_an'                  =>$del_an,
                            'IPLG_ao'                 =>$del_ao,
                            'OPLG_ap'                 =>$del_ap,
                            'PALG_aq'                 =>$del_aq,
                            'INSTLG_ar'               =>$del_ar,
                            'OTLG_as'                 =>$del_as,
                            'PP_at'                   =>$del_at,
                            'DRUG_au'                 =>$del_au,
                            'IPLG2'                   =>$sheet->getCell( 'AV' . $row )->getValue(),
                            'OPLG2'                   =>$sheet->getCell( 'AW' . $row )->getValue(),
                            'PALG2'                   =>$sheet->getCell( 'AX' . $row )->getValue(),
                            'INSTLG2'                 =>$sheet->getCell( 'AY' . $row )->getValue(),
                            'OTLG2'                   =>$sheet->getCell( 'AZ' . $row )->getValue(),
                            'ORS'                     =>$sheet->getCell( 'BA' . $row )->getValue(),
                            'VA'                      =>$sheet->getCell( 'BB' . $row )->getValue(),
                            'STMdoc'                  =>$file_
                        ];
                    $startcount++;

                }
                $for_insert = array_chunk($data, length:1000);
                foreach ($for_insert as $key => $data_) {
                        Acc_stm_lgoexcel::insert($data_);
                }

            } catch (Exception $e) {
                $error_code = $e->errorInfo[1];
                return back()->withErrors('There was a problem uploading the data!');
            }
               return response()->json([
                'status'    => '200',
            ]);
    }
    public function upstm_lgoexcel_save_210168(Request $request)
    {
            $this->validate($request, [
                'file' => 'required|file|mimes:xls,xlsx'
            ]);
            $the_file = $request->file('file');
            $file_ = $request->file('file')->getClientOriginalName(); //ชื่อไฟล์

            try{
                $spreadsheet = IOFactory::load($the_file->getRealPath());
                $sheet        = $spreadsheet->setActiveSheetIndex(0);
                $row_limit    = $sheet->getHighestDataRow();
                // $column_limit = $sheet->getHighestDataColumn();
                $row_range    = range( '8', $row_limit );
                $startcount = '8';

                $data = array();
                foreach ($row_range as $row ) {

                    $vst = $sheet->getCell( 'I' . $row )->getValue();
                    // $starttime = substr($vst, 0, 5);
                    $day           = substr($vst,0,2);
                    $mo            = substr($vst,3,2);
                    $year          = substr($vst,6,4);
                    $vstdate       = $year.'-'.$mo.'-'.$day;

                    $tran          = $sheet->getCell( 'B' . $row )->getValue();
                    $day2          = substr($tran,0,2);
                    $mo2           = substr($tran,3,2);
                    $year2         = substr($tran,6,4);
                    $transfer_date = $year2.'-'.$mo2.'-'.$day2;

                    $j = $sheet->getCell( 'J' . $row )->getValue();
                    $del_j = str_replace(",","",$j);

                        $data[] = [
                            'transfer_date'         =>$sheet->getCell( 'B' . $row )->getValue(),
                            'hn'                    =>$sheet->getCell( 'C' . $row )->getValue(),
                            'an'                    =>$sheet->getCell( 'D' . $row )->getValue(),
                            'fun'                   =>$sheet->getCell( 'E' . $row )->getValue(),
                            'type'                  =>$sheet->getCell( 'F' . $row )->getValue(),
                            'cid'                   =>$sheet->getCell( 'G' . $row )->getValue(),
                            'ptname'                =>$sheet->getCell( 'H' . $row )->getValue(),
                            'vstdate'               =>$vstdate,
                            'pay'                   =>$del_j,
                            'rep_no'                =>$sheet->getCell( 'K' . $row )->getValue(),
                            'STMDoc'                =>$file_
                        ];
                    $startcount++;

                }
                $for_insert = array_chunk($data, length:1000);
                foreach ($for_insert as $key => $data_) {
                        Acc_stm_lgoexcelnew::insert($data_);
                }

            } catch (Exception $e) {
                $error_code = $e->errorInfo[1];
                return back()->withErrors('There was a problem uploading the data!');
            }
               return response()->json([
                'status'    => '200',
            ]);
    }
    public function upstm_lgoexcel_save(Request $request)
    {
            $this->validate($request, [
                'file' => 'required|file|mimes:xls,xlsx'
            ]);
            $the_file = $request->file('file');
            $file_ = $request->file('file')->getClientOriginalName(); //ชื่อไฟล์

            try{
                $spreadsheet = IOFactory::load($the_file->getRealPath());
                $sheet        = $spreadsheet->setActiveSheetIndex(0);
                $row_limit    = $sheet->getHighestDataRow();
                // $column_limit = $sheet->getHighestDataColumn();
                $row_range    = range( '8', $row_limit );
                $startcount = '8';

                $data = array();
                foreach ($row_range as $row ) {

                    $vst = $sheet->getCell( 'I' . $row )->getValue();
                    // $starttime = substr($vst, 0, 5);
                    $day           = substr($vst,0,2);
                    $mo            = substr($vst,3,2);
                    $year          = substr($vst,6,4);
                    $vstdate       = $year.'-'.$mo.'-'.$day;

                    $tran          = $sheet->getCell( 'B' . $row )->getValue();
                    $day2          = substr($tran,0,2);
                    $mo2           = substr($tran,3,2);
                    $year2         = substr($tran,6,4);
                    $transfer_date = $year2.'-'.$mo2.'-'.$day2;

                    $j = $sheet->getCell( 'J' . $row )->getValue();
                    $del_j = str_replace(",","",$j);

                        $data[] = [
                            'transfer_date'         =>$sheet->getCell( 'B' . $row )->getValue(),
                            'hn'                    =>$sheet->getCell( 'C' . $row )->getValue(),
                            'an'                    =>$sheet->getCell( 'D' . $row )->getValue(),
                            'fun'                   =>$sheet->getCell( 'E' . $row )->getValue(),
                            'type'                  =>$sheet->getCell( 'F' . $row )->getValue(),
                            'cid'                   =>$sheet->getCell( 'G' . $row )->getValue(),
                            'ptname'                =>$sheet->getCell( 'H' . $row )->getValue(),
                            'vstdate'               =>$vstdate,
                            'pay'                   =>$del_j,
                            'rep_no'                =>$sheet->getCell( 'K' . $row )->getValue(),
                            'STMDoc'                =>$file_
                        ];
                    $startcount++;

                }
                $for_insert = array_chunk($data, length:1000);
                foreach ($for_insert as $key => $data_) {
                        Acc_stm_lgoexcelnew::insert($data_);
                }

            } catch (Exception $e) {
                $error_code = $e->errorInfo[1];
                return back()->withErrors('There was a problem uploading the data!');
            }
               return response()->json([
                'status'    => '200',
            ]);
    }
    public function upstm_lgoexcel_senddata(Request $request)
    {
        $data_ = DB::connection('mysql')->select('SELECT *,SUM(pay) as total FROM acc_stm_lgoexcelnew WHERE rep_no <> "" GROUP BY cid,vstdate');

        foreach ($data_ as $key => $value) {
            // ผู้ป่วยใน
            if ($value->an != '') {
                $check_ipd = acc_stm_lgonew::where('an',$value->an)->count();
                if ($check_ipd > 0) {
                    acc_stm_lgonew::where('an',$value->an)->update([
                        'transfer_date'  => $value->transfer_date,
                        'hn'             => $value->hn,
                        'fun'            => $value->fun,
                        'type'           => $value->type,
                        'cid'            => $value->cid,
                        'ptname'         => $value->ptname,
                        'vstdate'        => $value->vstdate,
                        'pay'            => $value->total,
                        'rep_no'         => $value->rep_no,
                        'STMDoc'         => $value->STMDoc
                    ]);
                } else {
                    acc_stm_lgonew::insert([
                        'transfer_date'  => $value->transfer_date,
                        'hn'             => $value->hn,
                        'an'             => $value->an,
                        'fun'            => $value->fun,
                        'type'           => $value->type,
                        'cid'            => $value->cid,
                        'ptname'         => $value->ptname,
                        'vstdate'        => $value->vstdate,
                        'pay'            => $value->total,
                        'rep_no'         => $value->rep_no,
                        'STMDoc'         => $value->STMDoc
                    ]);
                }
                $check802 = Acc_1102050102_802::where('an',$value->an)->where('STMdoc',NULL)->count();
                if ($check802 > 0) {
                    Acc_1102050102_802::where('an',$value->an)
                        ->update([
                            'status'          => 'Y',
                            'stm_money'       => $value->total,
                            'stm_rcpno'       => $value->rep_no,
                            'STMdoc'          => $value->STMDoc,
                    ]);
                } else {
                    Acc_1102050102_802::where('an',$value->an)
                    ->update([
                        'status'          => 'Y',
                        'stm_money'       => $value->pay,
                        'stm_rcpno'       => $value->rep_no,
                        'STMdoc'          => $value->STMDoc,
                    ]);
                }

            // ผู้ป่วยนอก
            } else {
                $check_opd = acc_stm_lgonew::where('hn',$value->hn)->where('vstdate',$value->vstdate)->count();
                if ($check_opd > 0) {
                    acc_stm_lgonew::where('hn',$value->hn)->where('vstdate',$value->vstdate)->update([
                        'transfer_date'  => $value->transfer_date,
                        'an'             => $value->an,
                        'fun'            => $value->fun,
                        'type'           => $value->type,
                        'cid'            => $value->cid,
                        'ptname'         => $value->ptname,
                        'pay'            => $value->total,
                        'rep_no'         => $value->rep_no,
                        'STMDoc'         => $value->STMDoc
                    ]);
                    $check801 = Acc_1102050102_801::where('hn',$value->hn)->where('vstdate',$value->vstdate)->where('STMdoc',NULL)->count();
                    if ($check801 > 0) {
                        Acc_1102050102_801::where('hn',$value->hn)->where('vstdate',$value->vstdate)
                            ->update([
                                'status'          => 'Y',
                                'stm_money'       => $value->total,
                                'stm_rcpno'       => $value->rep_no,
                                'STMdoc'          => $value->STMDoc,
                            ]);
                    } else {
                        Acc_1102050102_801::where('hn',$value->hn)->where('vstdate',$value->vstdate)
                        ->update([
                            'status'          => 'Y',
                            'stm_money'       => $value->total,
                            'stm_rcpno'       => $value->rep_no,
                            'STMdoc'          => $value->STMDoc,
                        ]);

                    }
                } else {
                    acc_stm_lgonew::insert([
                        'transfer_date'  => $value->transfer_date,
                        'hn'             => $value->hn,
                        'an'             => $value->an,
                        'fun'            => $value->fun,
                        'type'           => $value->type,
                        'cid'            => $value->cid,
                        'ptname'         => $value->ptname,
                        'vstdate'        => $value->vstdate,
                        'pay'            => $value->total,
                        'rep_no'         => $value->rep_no,
                        'STMDoc'         => $value->STMDoc
                    ]);
                    $check801 = Acc_1102050102_801::where('hn',$value->hn)->where('vstdate',$value->vstdate)->where('STMdoc',NULL)->count();
                    if ($check801 > 0) {
                        Acc_1102050102_801::where('hn',$value->hn)->where('vstdate',$value->vstdate)
                            ->update([
                                'status'          => 'Y',
                                'stm_money'       => $value->total,
                                'stm_rcpno'       => $value->rep_no,
                                'STMdoc'          => $value->STMDoc,
                            ]);
                    } else {
                        Acc_1102050102_801::where('hn',$value->hn)->where('vstdate',$value->vstdate)
                        ->update([
                            'status'          => 'Y',
                            'stm_money'       => $value->total,
                            'stm_rcpno'       => $value->rep_no,
                            'STMdoc'          => $value->STMDoc,
                        ]);

                    }
                }
            }

                // $check = acc_stm_lgonew::where('tranid_c',$value->tranid_c)->count();
                // if ($check  == 0) {
                //     Acc_stm_lgo::insert([
                //             'rep_a'         => $value->rep_a,
                //             'no_b'          => $value->no_b,
                //             'tranid_c'      => $value->tranid_c,
                //             'hn_d'          => $value->hn_d,
                //             'an_e'          => $value->an_e,
                //             'cid_f'         => $value->cid_f,
                //             'fullname_g'    => $value->fullname_g,
                //             'type_h'        => $value->type_h,
                //             'vstdate_i'     => $value->vstdate_i,
                //             'dchdate_j'     => $value->dchdate_j,
                //             'price1_k'      => $value->price1_k,
                //             'pp_spsch_l'    => $value->pp_spsch_l,
                //             'errorcode_m'   => $value->errorcode_m,
                //             'kongtoon_n'    => $value->kongtoon_n,
                //             'typeservice_o' => $value->typeservice_o,
                //             'refer_p'       => $value->refer_p,
                //             'pttype_have_q' => $value->pttype_have_q,
                //             'pttype_true_r' => $value->pttype_true_r,
                //             'mian_pttype_s' => $value->mian_pttype_s,
                //             'secon_pttype_t' =>$value->secon_pttype_t,
                //             'href_u'        => $value->href_u,
                //             'HCODE_v'       => $value->HCODE_v,
                //             'prov1_w'       => $value->prov1_w,
                //             'code_dep_x'    => $value->code_dep_x,
                //             'name_dep_y'    => $value->name_dep_y,
                //             'proj_z'        => $value->proj_z,
                //             'pa_aa'          => $value->pa_aa,
                //             'drg_ab'         => $value->drg_ab,
                //             'rw_ac'          => $value->rw_ac,
                //             'income_ad'      => $value->income_ad,
                //             'pp_gep_ae'      => $value->pp_gep_ae,
                //             'claim_true_af'  => $value->claim_true_af,
                //             'claim_false_ag' => $value->claim_false_ag,
                //             'cash_money_ah'  => $value->cash_money_ah,
                //             'pay_ai'         => $value->pay_ai,
                //             'ps_aj'          => $value->ps_aj,
                //             'ps_percent_ak'  => $value->ps_percent_ak,
                //             'ccuf_al'        => $value->ccuf_al,
                //             'AdjRW_am'       => $value->AdjRW_am,
                //             'plb_an'         => $value->plb_an,
                //             'IPLG_ao'        => $value->IPLG_ao,
                //             'OPLG_ap'        => $value->OPLG_ap,
                //             'PALG_aq'        => $value->PALG_aq,
                //             'INSTLG_ar'      => $value->INSTLG_ar,
                //             'OTLG_as'        => $value->OTLG_as,
                //             'PP_at'          => $value->PP_at,
                //             'DRUG_au'        => $value->DRUG_au,
                //             'IPLG2'          => $value->IPLG2,
                //             'OPLG2'          => $value->OPLG2,
                //             'PALG2'          => $value->PALG2,
                //             'INSTLG2'        => $value->INSTLG2,
                //             'OTLG2'          => $value->OTLG2,
                //             'ORS'            => $value->ORS,
                //             'VA'             => $value->VA,
                //             'STMdoc'         => $value->STMdoc
                //     ]);
                // }

                // $check801 = Acc_1102050102_801::where('cid',$value->cid_f)->where('vstdate',$value->vstdate_i)->where('STMdoc',NULL)->count();
                // if ($check801 > 0) {
                //     Acc_1102050102_801::where('cid',$value->cid_f)->where('vstdate',$value->vstdate_i)
                //         ->update([
                //             'status'          => 'Y',
                //             'stm_rep'         => $value->income_ad,
                //             'stm_money'       => $value->claim_true_af,
                //             'stm_rcpno'       => $value->rep_a.'-'.$value->no_b,
                //             'STMdoc'          => $value->STMdoc,
                //         ]);
                // } else {
                //     Acc_1102050102_801::where('cid',$value->cid_f)->where('vstdate',$value->vstdate_i)
                //     ->update([
                //         'status'          => 'Y',
                //         'stm_rep'         => $value->income_ad,
                //         'stm_money'       => $value->claim_true_af,
                //         'stm_rcpno'       => $value->rep_a.'-'.$value->no_b,
                //         'STMdoc'          => $value->STMdoc,
                //     ]);

                // }

                // $check802 = Acc_1102050102_802::where('an',$value->an_e)->where('STMdoc',NULL)->count();
                // if ($check802 > 0) {
                //     Acc_1102050102_802::where('an',$value->an_e)
                //         ->update([
                //             'status'          => 'Y',
                //             'stm_rep'         => $value->income_ad,
                //             'stm_money'       => $value->claim_true_af,
                //             'stm_rcpno'       => $value->rep_a.'-'.$value->no_b,
                //             'STMdoc'          => $value->STMdoc,
                //     ]);
                // } else {
                //     Acc_1102050102_802::where('an',$value->an_e)
                //     ->update([
                //         'status'          => 'Y',
                //         'stm_rep'         => $value->income_ad,
                //         'stm_money'       => $value->claim_true_af,
                //         'stm_rcpno'       => $value->rep_a.'-'.$value->no_b,
                //         'STMdoc'          => $value->STMdoc,
                //     ]);
                // }

        }
        Acc_stm_lgoexcelnew::truncate();
        // return response()->json([
        //     'status'    => '200',
        // ]);
        return redirect()->back();
    }
    public function upstm_lgo_rep(Request $request)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
            SELECT rep,vstdate,SUM(pay) as Sumprice,STMdoc,month(vstdate) as months
            FROM acc_lgo_repexcel
            WHERE tran_id <> ""
            GROUP BY tran_id
            ');
        $countc = DB::table('acc_lgo_repexcel')->count();
        // dd($countc );
        return view('account_pk.upstm_lgo_rep',[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'countc'        =>     $countc
        ]);
    }
    public function upstm_lgo_rep_save(Request $request)
    {
            $this->validate($request, [
                'file' => 'required|file|mimes:xls,xlsx'
            ]);
            $the_file = $request->file('file');
            $file_ = $request->file('file')->getClientOriginalName(); //ชื่อไฟล์

            try{
                $spreadsheet = IOFactory::load($the_file->getRealPath());
                $sheet        = $spreadsheet->setActiveSheetIndex(0);
                $row_limit    = $sheet->getHighestDataRow();
                // $column_limit = $sheet->getHighestDataColumn();
                $row_range    = range( '8', $row_limit );
                $startcount = '8';

                $data = array();
                foreach ($row_range as $row ) {

                    $vst = $sheet->getCell( 'I' . $row )->getValue();
                    // $starttime = substr($vst, 0, 5);
                    $day           = substr($vst,0,2);
                    $mo            = substr($vst,3,2);
                    $year          = substr($vst,6,4);
                    $vstdate       = $year.'-'.$mo.'-'.$day;

                    $tran          = $sheet->getCell( 'J' . $row )->getValue();
                    $day2          = substr($tran,0,2);
                    $mo2           = substr($tran,3,2);
                    $year2         = substr($tran,6,4);
                    $dchdate       = $year2.'-'.$mo2.'-'.$day2;

                    $k = $sheet->getCell( 'K' . $row )->getValue();
                    $del_k = str_replace(",","",$k);
                    $l = $sheet->getCell( 'L' . $row )->getValue();
                    $del_l = str_replace(",","",$l);
                    $ad = $sheet->getCell( 'AD' . $row )->getValue();
                    $del_ad = str_replace(",","",$ad);
                    $g = $sheet->getCell( 'AG' . $row )->getValue();
                    $del_g = str_replace(",","",$g);
                    $ae = $sheet->getCell( 'AE' . $row )->getValue();
                    $del_ae = str_replace(",","",$ae);
                    $af = $sheet->getCell( 'AF' . $row )->getValue();
                    $del_af = str_replace(",","",$af);
                    $ah = $sheet->getCell( 'AH' . $row )->getValue();
                    $del_ah = str_replace(",","",$ah);
                    $ai = $sheet->getCell( 'AI' . $row )->getValue();
                    $del_ai = str_replace(",","",$ai);

                    $an = $sheet->getCell( 'AN' . $row )->getValue();
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


                        $data[] = [
                            'rep'                    =>$sheet->getCell( 'A' . $row )->getValue(),
                            'no'                     =>$sheet->getCell( 'B' . $row )->getValue(),
                            'tran_id'                =>$sheet->getCell( 'C' . $row )->getValue(),
                            'hn'                     =>$sheet->getCell( 'D' . $row )->getValue(),
                            'an'                     =>$sheet->getCell( 'E' . $row )->getValue(),
                            'pid'                    =>$sheet->getCell( 'F' . $row )->getValue(),
                            'ptname'                 =>$sheet->getCell( 'G' . $row )->getValue(),
                            'type'                   =>$sheet->getCell( 'H' . $row )->getValue(),
                            'vstdate'                =>$vstdate ,
                            'dchdate'                =>$dchdate ,
                            'income_cherd'           =>$sheet->getCell( 'J' . $row )->getValue(),
                            'pp_cherd'               =>$sheet->getCell( 'L' . $row )->getValue(),
                            'error_code'             =>$sheet->getCell( 'M' . $row )->getValue(),

                            'toon'                   =>$sheet->getCell( 'N' . $row )->getValue(),
                            'type_service'           =>$sheet->getCell( 'O' . $row )->getValue(),
                            'refer'                  =>$sheet->getCell( 'P' . $row )->getValue(),
                            'ucc'                    =>$sheet->getCell( 'Q' . $row )->getValue(),
                            'hospmain'               =>$sheet->getCell( 'S' . $row )->getValue(),
                            'hospsub'                =>$sheet->getCell( 'T' . $row )->getValue(),
                            'hospmain'               =>$sheet->getCell( 'S' . $row )->getValue(),
                            'hospsub'                =>$sheet->getCell( 'T' . $row )->getValue(),
                            'href'                   =>$sheet->getCell( 'U' . $row )->getValue(),

                            'hcode'                  =>$sheet->getCell( 'V' . $row )->getValue(),
                            'prov1'                  =>$sheet->getCell( 'W' . $row )->getValue(),
                            'hoscode'                =>$sheet->getCell( 'U' . $row )->getValue(),
                            'hosname'                =>$sheet->getCell( 'U' . $row )->getValue(),
                            'proj'                   =>$sheet->getCell( 'U' . $row )->getValue(),
                            'pa'                     =>$sheet->getCell( 'AA' . $row )->getValue(),
                            'drg'                    =>$sheet->getCell( 'AB' . $row )->getValue(),
                            'rw'                     =>$sheet->getCell( 'AC' . $row )->getValue(),
                            'income'                 =>$del_ad ,
                            'pp_claim'               =>$del_ae ,
                            'income_claim'           =>$del_af ,
                            'income_noclaim'         =>$del_g ,
                            'rcpt'                   =>$del_g ,
                            'pay'                    =>$del_ai ,
                            'ps'                     =>$sheet->getCell( 'AJ' . $row )->getValue(),
                            'psper'                  =>$sheet->getCell( 'AK' . $row )->getValue(),
                            'ccuf'                   =>$sheet->getCell( 'AL' . $row )->getValue(),
                            'adjrw'                  =>$sheet->getCell( 'AM' . $row )->getValue(),
                            'prb'                    =>$del_an ,
                            'iplg'                   =>$del_ao ,
                            'oplg'                   =>$del_ap ,
                            'palg'                   =>$del_aq ,
                            'instlg'                 =>$del_ar ,
                            'otlg'                   =>$del_as ,
                            'pp_grnee'               =>$del_at ,
                            'drug'                   =>$del_au ,
                            'iplg_deny'              =>$sheet->getCell( 'AV' . $row )->getValue(),
                            'oplg_deny'              =>$sheet->getCell( 'AW' . $row )->getValue(),
                            'palg_deny'              =>$sheet->getCell( 'AX' . $row )->getValue(),
                            'instlg_deny'            =>$sheet->getCell( 'AY' . $row )->getValue(),
                            'otlg_deny'              =>$sheet->getCell( 'AZ' . $row )->getValue(),
                            'ors'                    =>$sheet->getCell( 'BA' . $row )->getValue(),
                            'va'                     =>$sheet->getCell( 'BB' . $row )->getValue(),
                            'audit'                  =>$sheet->getCell( 'BC' . $row )->getValue(),
                            'STMdoc'                 =>$file_ ,
                        ];
                    $startcount++;

                }
                $for_insert = array_chunk($data, length:1000);
                foreach ($for_insert as $key => $data_) {
                    Acc_lgo_repexcel::insert($data_);
                }

            } catch (Exception $e) {
                $error_code = $e->errorInfo[1];
                return back()->withErrors('There was a problem uploading the data!');
            }
               return response()->json([
                'status'    => '200',
            ]);
    }
    public function upstm_lgo_rep_send(Request $request)
    {
        $data_ = DB::connection('mysql')->select('SELECT * FROM acc_lgo_repexcel WHERE tran_id <> ""');

        foreach ($data_ as $key => $value) {
            // ผู้ป่วยใน
            if ($value->an != '') {
                $check_ipd = acc_lgo_rep::where('an',$value->an)->count();
                if ($check_ipd > 0) {
                    Acc_lgo_rep::where('tran_id',$value->tran_id)->update([
                        'rep'             => $value->rep,
                        'no'              => $value->no,
                        'tran_id'         => $value->tran_id,
                        'hn'              => $value->hn,
                        'an'              => $value->an,
                        'pid'             => $value->pid,
                        'ptname'          => $value->ptname,
                        'type'            => $value->type,
                        'vstdate'         => $value->vstdate,
                        'dchdate'         => $value->dchdate,
                        'income_cherd'    => $value->income_cherd,
                        'pp_cherd'        => $value->pp_cherd,
                        'error_code'      => $value->error_code,
                        'toon'            => $value->toon,
                        'type_service'    => $value->type_service,
                        'refer'           => $value->refer,
                        'ucc'             => $value->ucc,
                        'hospmain'        => $value->hospmain,
                        'hospsub'         => $value->hospsub,
                        'href'            => $value->href,
                        'hcode'           => $value->hcode,
                        'prov1'           => $value->prov1,
                        'hoscode'         => $value->hoscode,
                        'hosname'         => $value->hosname,
                        'proj'            => $value->proj,
                        'pa'              => $value->pa,
                        'drg'             => $value->drg,
                        'rw'              => $value->rw,
                        'income'          => $value->income,
                        'pp_claim'        => $value->pp_claim,
                        'income_claim'    => $value->income_claim,
                        'income_noclaim'  => $value->income_noclaim,
                        'rcpt'            => $value->rcpt,
                        'pay'             => $value->pay,
                        'ps'              => $value->ps,
                        'psper'           => $value->psper,
                        'ccuf'            => $value->ccuf,
                        'adjrw'           => $value->adjrw,
                        'prb'             => $value->prb,
                        'iplg'            => $value->iplg,
                        'oplg'            => $value->oplg,
                        'palg'            => $value->palg,
                        'instlg'          => $value->instlg,
                        'otlg'            => $value->otlg,
                        'pp_grnee'        => $value->pp_grnee,
                        'drug'            => $value->drug,
                        'iplg_deny'       => $value->iplg_deny,
                        'oplg_deny'       => $value->oplg_deny,
                        'palg_deny'       => $value->palg_deny,
                        'instlg_deny'     => $value->instlg_deny,
                        'otlg_deny'       => $value->otlg_deny,
                        'ors'             => $value->ors,
                        'va'              => $value->va,
                        'audit'           => $value->audit,
                        'STMDoc'          => $value->STMdoc,
                    ]);
                } else {
                    Acc_lgo_rep::insert([
                        'rep'             => $value->rep,
                        'no'              => $value->no,
                        'tran_id'         => $value->tran_id,
                        'hn'              => $value->hn,
                        'an'              => $value->an,
                        'pid'             => $value->pid,
                        'ptname'          => $value->ptname,
                        'type'            => $value->type,
                        'vstdate'         => $value->vstdate,
                        'dchdate'         => $value->dchdate,
                        'income_cherd'    => $value->income_cherd,
                        'pp_cherd'        => $value->pp_cherd,
                        'error_code'      => $value->error_code,
                        'toon'            => $value->toon,
                        'type_service'    => $value->type_service,
                        'refer'           => $value->refer,
                        'ucc'             => $value->ucc,
                        'hospmain'        => $value->hospmain,
                        'hospsub'         => $value->hospsub,
                        'href'            => $value->href,
                        'hcode'           => $value->hcode,
                        'prov1'           => $value->prov1,
                        'hoscode'         => $value->hoscode,
                        'hosname'         => $value->hosname,
                        'proj'            => $value->proj,
                        'pa'              => $value->pa,
                        'drg'             => $value->drg,
                        'rw'              => $value->rw,
                        'income'          => $value->income,
                        'pp_claim'        => $value->pp_claim,
                        'income_claim'    => $value->income_claim,
                        'income_noclaim'  => $value->income_noclaim,
                        'rcpt'            => $value->rcpt,
                        'pay'             => $value->pay,
                        'ps'              => $value->ps,
                        'psper'           => $value->psper,
                        'ccuf'            => $value->ccuf,
                        'adjrw'           => $value->adjrw,
                        'prb'             => $value->prb,
                        'iplg'            => $value->iplg,
                        'oplg'            => $value->oplg,
                        'palg'            => $value->palg,
                        'instlg'          => $value->instlg,
                        'otlg'            => $value->otlg,
                        'pp_grnee'        => $value->pp_grnee,
                        'drug'            => $value->drug,
                        'iplg_deny'       => $value->iplg_deny,
                        'oplg_deny'       => $value->oplg_deny,
                        'palg_deny'       => $value->palg_deny,
                        'instlg_deny'     => $value->instlg_deny,
                        'otlg_deny'       => $value->otlg_deny,
                        'ors'             => $value->ors,
                        'va'              => $value->va,
                        'audit'           => $value->audit,
                        'STMDoc'          => $value->STMdoc,
                    ]);
                }
                $check802 = Acc_1102050102_802::where('an',$value->an)->where('STMdoc',NULL)->count();
                if ($check802 > 0) {
                    Acc_1102050102_802::where('an',$value->an)
                        ->update([
                            'status'          => 'Y',
                            'stm_money'       => $value->income_claim,
                            'stm_rcpno'       => $value->rep,
                            'STMDoc'          => $value->STMdoc,
                    ]);
                } else {
                    Acc_1102050102_802::where('an',$value->an)
                    ->update([
                        'status'          => 'Y',
                        'stm_money'       => $value->income_claim,
                        'stm_rcpno'       => $value->rep,
                        'STMDoc'          => $value->STMdoc,
                    ]);
                }
                Acc_debtor::where('an',$value->an)
                ->update([
                    'rep_error'       => $value->error_code,
                    'rep_pay'         => $value->income_claim,
                    'rep_nopay'       => $value->income_noclaim,
                    'rep_doc'         => $value->STMdoc,
                ]);

            // ผู้ป่วยนอก
            } else {
                $check_opd = Acc_lgo_rep::where('tran_id',$value->tran_id)->count();
                if ($check_opd > 0) {
                    Acc_lgo_rep::where('tran_id',$value->tran_id)->update([
                        'rep'             => $value->rep,
                        'no'              => $value->no,
                        'tran_id'         => $value->tran_id,
                        'hn'              => $value->hn,
                        'an'              => $value->an,
                        'pid'             => $value->pid,
                        'ptname'          => $value->ptname,
                        'type'            => $value->type,
                        'vstdate'         => $value->vstdate,
                        'dchdate'         => $value->dchdate,
                        'income_cherd'    => $value->income_cherd,
                        'pp_cherd'        => $value->pp_cherd,
                        'error_code'      => $value->error_code,
                        'toon'            => $value->toon,
                        'type_service'    => $value->type_service,
                        'refer'           => $value->refer,
                        'ucc'             => $value->ucc,
                        'hospmain'        => $value->hospmain,
                        'hospsub'         => $value->hospsub,
                        'href'            => $value->href,
                        'hcode'           => $value->hcode,
                        'prov1'           => $value->prov1,
                        'hoscode'         => $value->hoscode,
                        'hosname'         => $value->hosname,
                        'proj'            => $value->proj,
                        'pa'              => $value->pa,
                        'drg'             => $value->drg,
                        'rw'              => $value->rw,
                        'income'          => $value->income,
                        'pp_claim'        => $value->pp_claim,
                        'income_claim'    => $value->income_claim,
                        'income_noclaim'  => $value->income_noclaim,
                        'rcpt'            => $value->rcpt,
                        'pay'             => $value->pay,
                        'ps'              => $value->ps,
                        'psper'           => $value->psper,
                        'ccuf'            => $value->ccuf,
                        'adjrw'           => $value->adjrw,
                        'prb'             => $value->prb,
                        'iplg'            => $value->iplg,
                        'oplg'            => $value->oplg,
                        'palg'            => $value->palg,
                        'instlg'          => $value->instlg,
                        'otlg'            => $value->otlg,
                        'pp_grnee'        => $value->pp_grnee,
                        'drug'            => $value->drug,
                        'iplg_deny'       => $value->iplg_deny,
                        'oplg_deny'       => $value->oplg_deny,
                        'palg_deny'       => $value->palg_deny,
                        'instlg_deny'     => $value->instlg_deny,
                        'otlg_deny'       => $value->otlg_deny,
                        'ors'             => $value->ors,
                        'va'              => $value->va,
                        'audit'           => $value->audit,
                        'STMdoc'          => $value->STMdoc,
                    ]);
                    $check801 = Acc_1102050102_801::where('hn',$value->hn)->where('vstdate',$value->vstdate)->where('STMdoc',NULL)->count();
                    $check801 = Acc_1102050102_801::where('hn',$value->hn)->where('vstdate',$value->vstdate)->where('STMdoc',NULL)->count();
                    if ($check801 > 0) {
                        Acc_1102050102_801::where('hn',$value->hn)->where('vstdate',$value->vstdate)
                            ->update([
                                'status'          => 'Y',
                                'stm_money'       => $value->income_claim,
                                'stm_rcpno'       => $value->rep,
                                'STMDoc'          => $value->STMdoc,
                            ]);
                    } else {
                        Acc_1102050102_801::where('hn',$value->hn)->where('vstdate',$value->vstdate)
                        ->update([
                            'status'          => 'Y',
                            'stm_money'       => $value->income_claim,
                            'stm_rcpno'       => $value->rep,
                            'STMDoc'          => $value->STMdoc,
                        ]);

                    }
                } else {
                    Acc_lgo_rep::insert([
                        'rep'             => $value->rep,
                        'no'              => $value->no,
                        'tran_id'         => $value->tran_id,
                        'hn'              => $value->hn,
                        'an'              => $value->an,
                        'pid'             => $value->pid,
                        'ptname'          => $value->ptname,
                        'type'            => $value->type,
                        'vstdate'         => $value->vstdate,
                        'dchdate'         => $value->dchdate,
                        'income_cherd'    => $value->income_cherd,
                        'pp_cherd'        => $value->pp_cherd,
                        'error_code'      => $value->error_code,
                        'toon'            => $value->toon,
                        'type_service'    => $value->type_service,
                        'refer'           => $value->refer,
                        'ucc'             => $value->ucc,
                        'hospmain'        => $value->hospmain,
                        'hospsub'         => $value->hospsub,
                        'href'            => $value->href,
                        'hcode'           => $value->hcode,
                        'prov1'           => $value->prov1,
                        'hoscode'         => $value->hoscode,
                        'hosname'         => $value->hosname,
                        'proj'            => $value->proj,
                        'pa'              => $value->pa,
                        'drg'             => $value->drg,
                        'rw'              => $value->rw,
                        'income'          => $value->income,
                        'pp_claim'        => $value->pp_claim,
                        'income_claim'    => $value->income_claim,
                        'income_noclaim'  => $value->income_noclaim,
                        'rcpt'            => $value->rcpt,
                        'pay'             => $value->pay,
                        'ps'              => $value->ps,
                        'psper'           => $value->psper,
                        'ccuf'            => $value->ccuf,
                        'adjrw'           => $value->adjrw,
                        'prb'             => $value->prb,
                        'iplg'            => $value->iplg,
                        'oplg'            => $value->oplg,
                        'palg'            => $value->palg,
                        'instlg'          => $value->instlg,
                        'otlg'            => $value->otlg,
                        'pp_grnee'        => $value->pp_grnee,
                        'drug'            => $value->drug,
                        'iplg_deny'       => $value->iplg_deny,
                        'oplg_deny'       => $value->oplg_deny,
                        'palg_deny'       => $value->palg_deny,
                        'instlg_deny'     => $value->instlg_deny,
                        'otlg_deny'       => $value->otlg_deny,
                        'ors'             => $value->ors,
                        'va'              => $value->va,
                        'audit'           => $value->audit,
                        'STMdoc'          => $value->STMdoc,
                    ]);
                    $check801 = Acc_1102050102_801::where('hn',$value->hn)->where('vstdate',$value->vstdate)->where('STMdoc',NULL)->count();
                    if ($check801 > 0) {
                        Acc_1102050102_801::where('hn',$value->hn)->where('vstdate',$value->vstdate)
                            ->update([
                                'status'          => 'Y',
                                'stm_money'       => $value->income_claim,
                                'stm_rcpno'       => $value->rep,
                                'STMDoc'          => $value->STMdoc,
                            ]);
                    } else {
                        Acc_1102050102_801::where('hn',$value->hn)->where('vstdate',$value->vstdate)
                        ->update([
                            'status'          => 'Y',
                            'stm_money'       => $value->income_claim,
                            'stm_rcpno'       => $value->rep,
                            'STMDoc'          => $value->STMdoc,
                        ]);

                    }
                }
                Acc_debtor::where('hn',$value->hn)->where('vstdate',$value->vstdate)
                ->update([
                    'rep_error'       => $value->error_code,
                    'rep_pay'         => $value->income_claim,
                    'rep_nopay'       => $value->income_noclaim,
                    'rep_doc'         => $value->STMdoc,
                ]);
            }


        }
        Acc_lgo_repexcel::truncate();
        // return response()->json([
        //     'status'    => '200',
        // ]);
        return redirect()->back();
    }    

    // *********************************************************

    public function upstm_lgotiexcel(Request $request)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
            SELECT repno,vstdate,SUM(pay_amount) as Sumprice,STMdoc,month(vstdate) as months,cid,ptname
            FROM acc_stm_lgoti_excel
            WHERE cid <> ""
            GROUP BY cid,vstdate
            ');
        $countc = DB::table('acc_stm_lgoti_excel')->count();
        // dd($countc );
        return view('account_pk.upstm_lgotiexcel',[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'countc'        =>     $countc
        ]);
    }
    public function upstm_lgotiexcel_save(Request $request)
    {
            $this->validate($request, [
                'file' => 'required|file|mimes:xls,xlsx'
            ]);
            $the_file = $request->file('file');
            $file_ = $request->file('file')->getClientOriginalName(); //ชื่อไฟล์

            try{
                $spreadsheet = IOFactory::load($the_file->getRealPath());
                $sheet        = $spreadsheet->setActiveSheetIndex(0);
                $row_limit    = $sheet->getHighestDataRow();
                $column_limit = $sheet->getHighestDataColumn();
                $row_range    = range( '11', $row_limit );
                // $column_range = range( 'AO', $column_limit );
                $startcount = '11';
                // $row_range_namefile  = range( 9, $sheet->getCell( 'A' . $row )->getValue() );
                $data = array();
                foreach ($row_range as $row ) {

                    $vst = $sheet->getCell( 'G' . $row )->getValue();
                    // $starttime = substr($vst, 0, 5);
                    $day = substr($vst,0,2);
                    $mo = substr($vst,3,2);
                    $year = substr($vst,6,4);
                    $vstdate = $year.'-'.$mo.'-'.$day;

                    $h = $sheet->getCell( 'H' . $row )->getValue();
                    $del_h = str_replace(",","",$h);

                        $data[] = [
                            'repno'                     =>$sheet->getCell( 'B' . $row )->getValue(),
                            'hn'                        =>$sheet->getCell( 'C' . $row )->getValue(),
                            'cid'                       =>$sheet->getCell( 'D' . $row )->getValue(),
                            'ptname'                    =>$sheet->getCell( 'E' . $row )->getValue(),
                            'type'                      =>$sheet->getCell( 'F' . $row )->getValue(),
                            'vstdate'                   =>$vstdate,
                            'pay_amount'                =>$del_h,
                            'STMDoc'                    =>$file_
                        ];
                    $startcount++;

                }
                $for_insert = array_chunk($data, length:1000);
                foreach ($for_insert as $key => $data_) {
                    Acc_stm_lgoti_excel::insert($data_);
                }

            } catch (Exception $e) {
                $error_code = $e->errorInfo[1];
                return back()->withErrors('There was a problem uploading the data!');
            }
               return response()->json([
                'status'    => '200',
            ]);
    }
    public function upstm_lgotiexcel_senddata(Request $request)
    {
        $data_ = DB::connection('mysql')->select('SELECT * FROM acc_stm_lgoti_excel WHERE cid <> "" GROUP BY cid,vstdate');
        // group by tranid_c
        // GROUP BY cid,vstdate
        foreach ($data_ as $key => $value) {
            if ($value->type == 'ผู้ป่วยนอก') { 

                $check = Acc_stm_lgoti::where('cid',$value->cid)->where('vstdate',$value->vstdate)->count();
                if ($check  == 0) {
                    Acc_stm_lgoti::insert([
                            'repno'           => $value->repno,
                            'hn'              => $value->hn,
                            'cid'             => $value->cid,
                            'ptname'          => $value->ptname,
                            'type'            => $value->type,
                            'vstdate'         => $value->vstdate,
                            'pay_amount'      => $value->pay_amount,
                            'STMDoc'          => $value->STMDoc
                    ]);
                }
                Acc_1102050102_8011::where('cid',$value->cid)->where('vstdate',$value->vstdate)
                ->update([
                    'status'        => 'Y',
                    'STMDoc'        => $value->STMDoc,
                    'stm_total'     => $value->pay_amount

                ]);
            } else {
                Acc_1102050102_8022::where('cid',$value->cid)->where('rxdate',$value->vstdate)
                ->update([
                    'status'        => 'Y',
                    'STMDoc'        => $value->STMDoc,
                    'stm_total'     => $value->pay_amount
                ]);
            }

        }
        Acc_stm_lgoti_excel::truncate();
        // return response()->json([
        //     'status'    => '200',
        // ]);
        return redirect()->back();
    }


    // **********************************************
    public function upstm_all(Request $request)
    {
        $datenow             = date('Y-m-d');
        $startdate           = $request->startdate;
        $enddate             = $request->enddate;
        $datashow            = DB::connection('mysql')->select('SELECT STMDoc ,SUM(total_approve) as total FROM acc_stm_ucs WHERE STMDoc LIKE "STM_10978_OPU%" GROUP BY STMDoc ORDER BY STMDoc DESC');
        $data['ucs_216']     = DB::connection('mysql')->select('
            SELECT b.STMDoc ,SUM(b.hc_drug)+SUM(b.hc)+SUM(b.ae)+SUM(b.ae_drug)+SUM(b.inst)+SUM(b.dmis_money2)+SUM(b.dmis_drug) as total
            FROM acc_1102050101_216 a
            LEFT JOIN acc_stm_ucs b ON b.cid = a.cid AND b.vstdate = a.vstdate
            WHERE b.STMDoc LIKE "STM_10978_OPU%"
            GROUP BY b.STMDoc ORDER BY STMDoc DESC
        ');
        $data['ucs_ipd']      = DB::connection('mysql')->select('
            SELECT b.STMDoc,SUM(b.ip_paytrue) as total
            FROM acc_1102050101_202 a
            LEFT JOIN acc_stm_ucs b ON b.an = a.an
            WHERE b.STMDoc LIKE "STM_10978_IPU%"
            GROUP BY b.STMDoc
            ORDER BY b.STMDoc DESC
        ');
        $data['ucs_217']      = DB::connection('mysql')->select('
                SELECT b.STMDoc,SUM(b.hc_drug) + SUM(b.hc) + SUM(b.ae_drug) + SUM(b.inst) + SUM(b.dmis_money2) + SUM(b.dmis_drug) as total
                FROM acc_1102050101_217 a
                LEFT JOIN acc_stm_ucs b ON b.an = a.an
                WHERE b.STMDoc LIKE "STM_10978_IPU%"
                GROUP BY b.STMDoc ORDER BY b.STMDoc DESC
        ');
        // ,(SELECT STMDoc WHERE STMDoc LIKE "STM_10978_IPU%") as STMDoc_ipd
        // ,(SELECT STMDoc WHERE STMDoc LIKE "STM_10978_OPU%") as STMDoc_opd
        $data['ofc_opd']      = DB::connection('mysql')->select('
                SELECT STMDoc,SUM(pricereq_all) as total
                FROM acc_stm_ofc
                WHERE STMDoc LIKE "STM_10978_OP%"
                GROUP BY STMDoc ORDER BY STMDoc DESC
        ');
        $data['ofc_ipd']      = DB::connection('mysql')->select('SELECT STMDoc,SUM(pricereq_all) as total FROM acc_stm_ofc WHERE STMDoc LIKE "STM_10978_IP%" GROUP BY STMDoc ORDER BY STMDoc DESC');
        $data['lgo_opd']      = DB::connection('mysql')->select('SELECT STMDoc,SUM(claim_true_af) as total FROM acc_stm_lgo WHERE STMDoc LIKE "eclaim_10978_OP%" GROUP BY STMDoc ORDER BY STMDoc DESC');
        $data['lgo_ipd']      = DB::connection('mysql')->select('SELECT STMDoc,SUM(claim_true_af) as total FROM acc_stm_lgo WHERE STMDoc LIKE "eclaim_10978_IP%" GROUP BY STMDoc ORDER BY STMDoc DESC');
        $data['ucs_ti']       = DB::connection('mysql')->select('SELECT STMDoc,SUM(Total_amount) as total FROM acc_stm_ti_total WHERE HDflag IN("WEL","UCS") GROUP BY STMDoc ORDER BY STMDoc DESC');

        $data['ofc_ti_opd']   = DB::connection('mysql')->select('SELECT STMDoc,SUM(Total_amount) as total FROM acc_stm_ti_total WHERE HDflag IN("COC") GROUP BY STMDoc ORDER BY STMDoc DESC');
        $data['ofc_ti_ipd']   = DB::connection('mysql')->select('SELECT STMDoc,SUM(Total_amount) as total FROM acc_stm_ti_total WHERE HDflag IN("CIC") GROUP BY STMDoc ORDER BY STMDoc DESC');
        $data['sss_ti']       = DB::connection('mysql')->select('SELECT STMDoc,SUM(Total_amount) as total FROM acc_stm_ti_total WHERE HDflag IN("COS") GROUP BY STMDoc ORDER BY STMDoc DESC');
        $data['lgo_opdti']    = DB::connection('mysql')->select('SELECT STMDoc,SUM(pay_amount) as total FROM acc_stm_lgoti WHERE type LIKE "ผู้ป่วยนอก%" GROUP BY STMDoc ORDER BY STMDoc DESC');
        $data['lgo_ipdti']    = DB::connection('mysql')->select('SELECT STMDoc,SUM(pay_amount) as total FROM acc_stm_lgoti WHERE type LIKE "ผู้ป่วยใน%" GROUP BY STMDoc ORDER BY STMDoc DESC');

        $countc = DB::table('acc_stm_ucs_excel')->count();
        // dd($countc );
        return view('account_pk.upstm_all',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'countc'        =>     $countc
        ]);
    }
    public function upstm_ucs_opd(Request $request)
    {
        $datenow             = date('Y-m-d');
        $startdate           = $request->startdate;
        $enddate             = $request->enddate;
        $data['datashow']           = DB::connection('mysql')->select('
            SELECT STMDoc
            FROM acc_stm_ucs
            WHERE STMDoc LIKE "STM_10978_OPU%"
            GROUP BY STMDoc ORDER BY STMDoc DESC
        ');
        return view('account_pk.upstm_ucs_opd',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
        ]);
    }
    public function upstm_ucs_detail_opd(Request $request,$id)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
                SELECT a.vn,a.hn,a.vstdate,a.cid,a.ptname,a.pttype,a.income,a.debit,a.debit_total,b.STMdoc,b.ip_paytrue,b.total_approve
                from acc_1102050101_201 a
                LEFT JOIN acc_stm_ucs b ON b.hn = a.hn AND b.vstdate = a.vstdate
                where b.STMdoc = "'.$id.'"
                AND b.total_approve > 0.00
        ');
        $data['ucs_opd'] = DB::connection('mysql')->select(' SELECT STMDoc FROM acc_stm_ucs WHERE STMDoc LIKE "STM_10978_OPU%" GROUP BY STMDoc ORDER BY STMDoc DESC');
        return view('account_pk.upstm_ucs_detail_opd',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'STMDoc'        =>     $id,
        ]);
    }
    public function upstm_ucs_ipd(Request $request)
    {
        $datenow             = date('Y-m-d');
        $startdate           = $request->startdate;
        $enddate             = $request->enddate;
        $data['datashow']           = DB::connection('mysql')->select('
            SELECT b.STMDoc,sum(b.ip_paytrue) as total
            FROM acc_1102050101_202 a
            LEFT JOIN acc_stm_ucs b ON b.an = a.an
            WHERE b.STMDoc LIKE "STM_10978_IPU%"
            GROUP BY b.STMDoc ORDER BY STMDoc DESC
        ');
        return view('account_pk.upstm_ucs_ipd',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
        ]);
    }
    public function upstm_ucs_detail_ipd(Request $request,$id)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
                SELECT a.an,a.vn,a.hn,a.vstdate,a.dchdate,a.cid,a.ptname,a.pttype,a.income,a.debit,a.debit_total,b.STMdoc,b.ip_paytrue,b.total_approve,a.stm_money
                from acc_1102050101_202 a
                LEFT JOIN acc_stm_ucs b ON b.an = a.an
                where b.STMdoc = "'.$id.'"
                GROUP BY a.an
        ');

        $data['ucs_ipd'] = DB::connection('mysql')->select('
            SELECT b.STMDoc,sum(b.ip_paytrue) as total
                FROM acc_1102050101_202 a
                LEFT JOIN acc_stm_ucs b ON b.an = a.an
                WHERE b.STMDoc LIKE "STM_10978_IPU%"
                GROUP BY b.STMDoc ORDER BY STMDoc DESC
        ');
        return view('account_pk.upstm_ucs_detail_ipd',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'STMDoc'        =>     $id,
        ]);
    }
    public function upstm_ucs_opd216(Request $request)
    {
        $datenow             = date('Y-m-d');
        $startdate           = $request->startdate;
        $enddate             = $request->enddate;

        // $data['datashow']     = DB::connection('mysql')->select('
        //     SELECT b.STMDoc ,SUM(b.hc_drug)+SUM(b.hc)+SUM(b.ae_drug)+SUM(b.inst)+SUM(b.dmis_money2)+SUM(b.dmis_drug)+ SUM(b.ae) as total
        //     FROM acc_1102050101_216 a
        //     LEFT JOIN acc_stm_ucs b ON b.cid = a.cid AND b.vstdate = a.vstdate
        //     WHERE b.STMDoc LIKE "STM_10978_OPU%"
        //     GROUP BY b.STMDoc ORDER BY STMDoc DESC
        // ');
        $data['datashow']     = DB::connection('mysql')->select('SELECT STMDoc FROM acc_stm_ucs WHERE STMDoc LIKE "STM_10978_OPUCS%" GROUP BY STMDoc ORDER BY STMDoc DESC');
        return view('account_pk.upstm_ucs_opd216',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
        ]);
    }
    public function upstm_ucs_detail_opd_216(Request $request,$id)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
                SELECT a.an,a.vn,a.hn,a.vstdate,a.dchdate,a.cid,a.ptname,a.pttype,a.income,a.debit,a.debit_total,b.STMdoc,b.ip_paytrue,b.total_approve,b.projectcode
                ,b.hc_drug+ b.hc+ b.ae+ b.ae_drug + b.inst+ b.dmis_money2 + b.dmis_drug  as total_216,a.chod_chery_imc
                from acc_1102050101_216 a
                LEFT JOIN acc_stm_ucs b ON b.cid = a.cid AND b.vstdate = a.vstdate
                where b.STMdoc = "'.$id.'"
                AND b.total_approve IS NOT NULL
        ');
        $data['ucs_216']     = DB::connection('mysql')->select('SELECT STMDoc FROM acc_stm_ucs WHERE STMDoc LIKE "STM_10978_OPUCS%" GROUP BY STMDoc ORDER BY STMDoc DESC');
        // $data['ucs_216'] = DB::connection('mysql')->select('
        //         SELECT a.vn,b.STMDoc,SUM(b.hc_drug) + SUM(b.hc) + SUM(b.ae_drug) + SUM(b.inst) + SUM(b.ae) as total
        //         FROM acc_1102050101_216 a
        //         LEFT JOIN acc_stm_ucs b ON b.cid = a.cid AND b.vstdate = a.vstdate
        //         WHERE b.STMDoc LIKE "STM_10978_OPU%"
        //         GROUP BY b.STMDoc ORDER BY b.STMDoc DESC
        // ');
        return view('account_pk.upstm_ucs_detail_opd_216',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'STMDoc'        =>     $id,
        ]);
    }
    public function upstm_ucs_ipd217(Request $request)
    {
        $datenow             = date('Y-m-d');
        $startdate           = $request->startdate;
        $enddate             = $request->enddate;

        $data['datashow']     = DB::connection('mysql')->select('
            SELECT b.STMDoc ,SUM(b.ae) + SUM(b.ae_drug) + SUM(b.hc_drug)+SUM(b.hc)+SUM(b.inst)+SUM(b.dmis_money2)+SUM(b.dmis_drug) as total
            FROM acc_1102050101_217 a
            LEFT JOIN acc_stm_ucs b ON b.an = a.an
            WHERE b.STMDoc LIKE "STM_10978_IPU%"
            GROUP BY b.STMDoc ORDER BY STMDoc DESC
        ');
        return view('account_pk.upstm_ucs_ipd217',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
        ]);
    }
    public function upstm_ucs_detail_ipd_217(Request $request,$id)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
                SELECT a.an,a.vn,a.hn,a.vstdate,a.dchdate,a.cid,a.ptname,a.pttype,a.income,a.debit,a.debit_total,b.STMdoc,b.ip_paytrue,b.total_approve,a.auton
                ,b.ae+ b.ae_drug +b.hc_drug+ b.hc + b.inst + b.dmis_money2 + b.dmis_drug as total_217
                from acc_1102050101_217 a
                LEFT JOIN acc_stm_ucs b ON b.an = a.an
                where b.STMdoc = "'.$id.'"
                AND b.ae+ b.ae_drug +b.hc_drug+ b.hc + b.inst + b.dmis_money2 + b.dmis_drug > 0
                GROUP BY a.an
        ');
        $data['ucs_217'] = DB::connection('mysql')->select('
                SELECT b.STMDoc,SUM(b.ae) + SUM(b.ae_drug) + SUM(b.hc_drug) + SUM(b.hc) + SUM(b.inst) + SUM(b.dmis_money2) + SUM(b.dmis_drug) as total,a.auton
                FROM acc_1102050101_217 a
                LEFT JOIN acc_stm_ucs b ON b.an = a.an
                WHERE b.STMDoc LIKE "STM_10978_IPU%"
                GROUP BY b.STMDoc ORDER BY b.STMDoc DESC
        ');
        return view('account_pk.upstm_ucs_detail_ipd_217',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'STMDoc'        =>     $id,
        ]);
    }
    public function upstm_ucs_ti(Request $request)
    {
        $datenow             = date('Y-m-d');
        $startdate           = $request->startdate;
        $enddate             = $request->enddate;
        // $data['ucs_ti']       = DB::connection('mysql')->select('SELECT STMDoc,SUM(Total_amount) as total FROM acc_stm_ti_total WHERE HDflag IN("WEL","UCS") GROUP BY STMDoc ORDER BY STMDoc DESC');
        // $data['ucs_ti']     = DB::connection('mysql')->select('
        //     SELECT b.STMDoc,SUM(b.Total_amount) as total,SUM(b.sum_price_approve) as total2
        //     FROM acc_1102050101_2166 a
        //     LEFT JOIN acc_stm_ti_total b ON b.HDBill_pid = a.cid AND b.vstdate = a.vstdate
        //     WHERE b.STMDoc LIKE "10978_DCKD%" AND HDBill_TBill_HDflag IN("UCS","WEL")
        //     GROUP BY b.STMDoc ORDER BY STMDoc DESC
        // ');

        $data['ucs_ti']     = DB::connection('mysql')->select('
            SELECT b.STMDoc,SUM(b.Total_amount) as total,SUM(b.sum_price_approve) as total2
            FROM acc_stm_ti_total b

            WHERE b.STMDoc LIKE "10978_DCKD%" AND b.HDBill_TBill_HDflag IN("UCS","WEL")
            GROUP BY b.STMDoc ORDER BY STMDoc DESC
        ');

        return view('account_pk.upstm_ucs_ti',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
        ]);
    }
    public function upstm_ucs_ti_detail(Request $request,$id)
    {
        $datenow             = date('Y-m-d');
        $startdate           = $request->startdate;
        $enddate             = $request->enddate;
        $data['ucs_ti']     = DB::connection('mysql')->select('
            SELECT b.STMDoc,SUM(b.Total_amount) as total,SUM(b.sum_price_approve) as total2
            FROM acc_stm_ti_total b

            WHERE b.STMDoc LIKE "10978_DCKD%" AND b.HDBill_TBill_HDflag IN("UCS","WEL")
            GROUP BY b.STMDoc ORDER BY STMDoc DESC
        ');
        //     $data['ucs_ti']     = DB::connection('mysql')->select('
        //     SELECT b.STMDoc,SUM(b.Total_amount) as total,SUM(b.sum_price_approve) as total2
        //     FROM acc_1102050101_2166 a
        //     LEFT JOIN acc_stm_ti_total b ON b.HDBill_pid = a.cid AND b.vstdate = a.vstdate
        //     WHERE b.STMDoc LIKE "10978_DCKD%" AND HDBill_TBill_HDflag IN("UCS","WEL")
        //     GROUP BY b.STMDoc ORDER BY STMDoc DESC
        // ');
        $data['datashow']     = DB::connection('mysql')->select('
            SELECT a.vn,a.hn,a.vstdate,a.cid,a.ptname,a.pttype,a.income,a.debit,a.debit_total,b.STMdoc,b.Total_amount
            FROM acc_1102050101_2166 a
            LEFT OUTER JOIN acc_stm_ti_total b ON b.HDBill_pid = a.cid AND b.vstdate = a.vstdate
                WHERE b.STMdoc = "'.$id.'"


        ');
        // GROUP BY
        // AND b.Total_amount IS NOT NULL
        return view('account_pk.upstm_ucs_ti_detail',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'STMDoc'        =>     $id,
        ]);
    }

    public function upstm_ofc_opd(Request $request)
    {
        $startdate       = $request->startdate;
        $enddate         = $request->enddate;
        $data['ofc_opd'] = DB::connection('mysql')->select('
                SELECT STMDoc,SUM(stm_money) as total
                FROM acc_1102050101_401
                GROUP BY STMDoc
                ORDER BY STMDoc DESC
        ');

        // SELECT STMDoc,SUM(pricereq_all) as total
        // FROM acc_stm_ofc
        // WHERE STMDoc LIKE "STM_10978_OP%" AND an = "-"
        // GROUP BY STMDoc ORDER BY STMDoc DESC
        // $data['ofc_opd'] = DB::connection('mysql')->select('SELECT STMDoc FROM acc_stm_ofc WHERE STMDoc LIKE "STM_10978_OP%" GROUP BY STMDoc ORDER BY STMDoc DESC');
        return view('account_pk.upstm_ofc_opd',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            // 'datashow'      =>     $datashow,
        ]);
    }
    public function upstm_ofc_opd_detail(Request $request,$id)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
                SELECT a.vn,a.hn,a.vstdate,a.cid,a.ptname,a.pttype,a.income,a.debit,a.debit_total,a.STMdoc,a.stm_money
                FROM acc_1102050101_401 a
                WHERE STMdoc = "'.$id.'"
                AND a.stm_money IS NOT NULL
        ');
        // SELECT a.vn,a.hn,a.vstdate,a.cid,a.ptname,a.pttype,a.income,a.debit,a.debit_total,b.STMdoc,b.pricereq_all
        // FROM acc_1102050101_401 a
        // LEFT OUTER JOIN acc_stm_ofc b ON b.cid = a.cid AND b.vstdate = a.vstdate
        // WHERE b.STMdoc = "'.$id.'" AND b.an = "-"
        // AND b.pricereq_all IS NOT NULL
        $data['ofc_opd'] = DB::connection('mysql')->select('
                SELECT STMDoc,SUM(stm_money) as total
                FROM acc_1102050101_401
                GROUP BY STMDoc
                ORDER BY STMDoc DESC
        ');
        return view('account_pk.upstm_ofc_opd_detail',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'STMDoc'        =>     $id,
        ]);
    }
    public function upstm_ofc_ipd(Request $request)
    {
        $startdate       = $request->startdate;
        $enddate         = $request->enddate;
        $data['ofc_ipd'] = DB::connection('mysql')->select('
                SELECT STMDoc,SUM(stm_money) as total
                FROM acc_1102050101_402
                GROUP BY STMDoc
                ORDER BY STMDoc DESC
        ');
        return view('account_pk.upstm_ofc_ipd',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
        ]);
    }
    public function upstm_ofc_ipd_detail(Request $request,$id)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
                SELECT a.vn,a.an,a.hn,a.vstdate,a.dchdate,a.cid,a.ptname,a.pttype,a.income,a.debit,a.debit_total,a.STMdoc,a.stm_money
                FROM acc_1102050101_402 a
                WHERE STMdoc = "'.$id.'"
                AND a.stm_money IS NOT NULL
        ');
        $data['ofc_ipd'] = DB::connection('mysql')->select('
                SELECT STMDoc,SUM(stm_money) as total
                FROM acc_1102050101_402
                GROUP BY STMDoc
                ORDER BY STMDoc DESC
        ');
        return view('account_pk.upstm_ofc_ipd_detail',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'STMDoc'        =>     $id,
        ]);
    }
    public function upstm_lgo_opd(Request $request)
    {
        $startdate       = $request->startdate;
        $enddate         = $request->enddate;
        $data['lgo_opd'] = DB::connection('mysql')->select('
                SELECT STMDoc,SUM(stm_money) as total
                FROM acc_1102050102_801
                GROUP BY STMDoc
                ORDER BY STMDoc DESC
        ');
        return view('account_pk.upstm_lgo_opd',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
        ]);
    }
    public function upstm_lgo_opd_detail(Request $request,$id)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
                SELECT *
                FROM acc_1102050102_801
                WHERE STMdoc = "'.$id.'"
                AND stm_money IS NOT NULL
        ');

        $data['lgo_opd'] = DB::connection('mysql')->select('
                SELECT STMDoc,SUM(stm_money) as total
                FROM acc_1102050102_801
                GROUP BY STMDoc
                ORDER BY STMDoc DESC
        ');
        return view('account_pk.upstm_lgo_opd_detail',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'STMDoc'        =>     $id,
        ]);
    }
    public function upstm_lgo_ipd(Request $request)
    {
        $startdate       = $request->startdate;
        $enddate         = $request->enddate;
        $data['lgo_opd'] = DB::connection('mysql')->select('
                SELECT STMDoc,SUM(stm_money) as total
                FROM acc_1102050102_802
                GROUP BY STMDoc
                ORDER BY STMDoc DESC
        ');
        return view('account_pk.upstm_lgo_ipd',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
        ]);
    }
    public function upstm_lgo_ipd_detail(Request $request,$id)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
                SELECT *
                FROM acc_1102050102_802
                WHERE STMdoc = "'.$id.'"
                AND stm_money IS NOT NULL
        ');

        $data['lgo_ipd'] = DB::connection('mysql')->select('
                SELECT STMDoc,SUM(stm_money) as total
                FROM acc_1102050102_802
                GROUP BY STMDoc
                ORDER BY STMDoc DESC
        ');
        return view('account_pk.upstm_lgo_ipd_detail',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'STMDoc'        =>     $id,
        ]);
    }

    public function upstm_ofc_ti(Request $request)
    {
        $datenow             = date('Y-m-d');
        $startdate           = $request->startdate;
        $enddate             = $request->enddate;
        $data['ofc_ti']     = DB::connection('mysql')->select(
            'SELECT a.STMDoc,SUM(a.Total_amount) as total
                FROM acc_stm_ti_total a

                WHERE a.HDBill_TBill_HDflag IN("COC")
                GROUP BY a.STMDoc
                ORDER BY STMDoc DESC
        ');
        //  LEFT JOIN acc_stm_ti_total b ON b.hn = a.hn AND b.vstdate = a.vstdate
        return view('account_pk.upstm_ofc_ti',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
        ]);
    }
    public function upstm_ofc_ti_detail(Request $request,$id)
    {
        $datenow             = date('Y-m-d');
        $startdate           = $request->startdate;
        $enddate             = $request->enddate;
        $data['ofc_ti']     = DB::connection('mysql')->select('
                SELECT a.STMDoc,SUM(a.Total_amount) as total
                FROM acc_stm_ti_total a
                WHERE a.HDBill_TBill_HDflag IN("COC")
                GROUP BY a.STMDoc
                ORDER BY STMDoc DESC
        ');
        $data['datashow']     = DB::connection('mysql')->select('
            SELECT a.vn,a.hn,a.vstdate,a.cid,a.ptname,a.pttype,a.income,a.debit_total,b.STMdoc,b.Total_amount
            FROM acc_1102050101_4011 a
            LEFT JOIN acc_stm_ti_total b ON b.HDBill_hn = a.hn AND b.vstdate = a.vstdate
            WHERE b.STMdoc = "'.$id.'" AND b.HDBill_TBill_HDflag IN("COC") AND b.Total_amount <> ""
            GROUP BY a.hn,a.vstdate
        ');
        // AND b.Total_amount IS NOT NULL
        return view('account_pk.upstm_ofc_ti_detail',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'STMDoc'        =>     $id,
        ]);
    }

    public function upstm_lgo_ti(Request $request)
    {
        $datenow             = date('Y-m-d');
        $startdate           = $request->startdate;
        $enddate             = $request->enddate;
        $data['lgo_ti']     = DB::connection('mysql')->select('
                SELECT b.STMDoc,SUM(b.pay_amount) as total
                FROM acc_1102050102_8011 a
                LEFT JOIN acc_stm_lgoti b ON b.hn = a.hn AND b.vstdate = a.vstdate

                GROUP BY b.STMDoc
                ORDER BY STMDoc DESC
        ');
        // WHERE b.HDflag IN("COC")
        return view('account_pk.upstm_lgo_ti',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
        ]);
    }
    public function upstm_lgo_ti_detail(Request $request,$id)
    {
        $datenow             = date('Y-m-d');
        $startdate           = $request->startdate;
        $enddate             = $request->enddate;
        $data['lgo_ti']     = DB::connection('mysql')->select('
                SELECT b.STMDoc,SUM(b.pay_amount) as total
                FROM acc_1102050102_8011 a
                LEFT JOIN acc_stm_lgoti b ON b.hn = a.hn AND b.vstdate = a.vstdate
                GROUP BY b.STMDoc
                ORDER BY STMDoc DESC
        ');
        $data['datashow']     = DB::connection('mysql')->select('
            SELECT a.vn,a.hn,a.vstdate,a.cid,a.ptname,a.pttype,a.income,a.debit_total,b.STMdoc,b.pay_amount
            FROM acc_1102050102_8011 a
            LEFT JOIN acc_stm_lgoti b ON b.hn = a.hn AND b.vstdate = a.vstdate
            WHERE b.STMdoc = "'.$id.'"

        ');
        // AND b.Total_amount IS NOT NULL
        return view('account_pk.upstm_lgo_ti_detail',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'STMDoc'        =>     $id,
        ]);
    }

    public function upstm_lgo_tiipd(Request $request)
    {
        $datenow             = date('Y-m-d');
        $startdate           = $request->startdate;
        $enddate             = $request->enddate;
        $data['lgo_ti']     = DB::connection('mysql')->select(
            'SELECT b.STMDoc,SUM(b.pay_amount) as total
                FROM acc_1102050102_8022 a
                LEFT JOIN acc_stm_lgoti b ON b.hn = a.hn AND b.vstdate = a.rxdate
                WHERE b.STMDoc IS NOT NULL
                GROUP BY b.STMDoc
                ORDER BY a.STMDoc DESC
        ');
        // WHERE b.HDflag IN("COC")
        return view('account_pk.upstm_lgo_tiipd',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
        ]);
    }

    public function upstm_lgo_tiipd_detail(Request $request,$id)
    {
        $datenow             = date('Y-m-d');
        $startdate           = $request->startdate;
        $enddate             = $request->enddate;
        $data['lgo_ti']     = DB::connection('mysql')->select(
            'SELECT b.STMDoc,SUM(b.pay_amount) as total
                FROM acc_1102050102_8022 a
                LEFT JOIN acc_stm_lgoti b ON b.hn = a.hn AND b.vstdate = a.rxdate
                WHERE b.STMDoc IS NOT NULL
                GROUP BY b.STMDoc
                ORDER BY a.STMDoc DESC
        ');
        $data['datashow']     = DB::connection('mysql')->select('
            SELECT a.an,a.vn,a.hn,a.vstdate,a.cid,a.ptname,a.pttype,a.income,a.debit_total,b.STMdoc,b.pay_amount,a.rxdate
            FROM acc_1102050102_8022 a
            LEFT JOIN acc_stm_lgoti b ON b.hn = a.hn AND b.vstdate = a.rxdate
            WHERE b.STMdoc = "'.$id.'"

        ');
        // WHERE b.HDflag IN("COC")
        return view('account_pk.upstm_lgo_tiipd_detail',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'STMDoc'        =>     $id,
        ]);
    }

    public function upstm_sss_ti(Request $request)
    {
        $datenow             = date('Y-m-d');
        $startdate           = $request->startdate;
        $enddate             = $request->enddate;
        $data['sss_ti']     = DB::connection('mysql')->select('
                SELECT STMDoc,SUM(stm_total) as total
                FROM acc_1102050101_3099
                WHERE stm_money IS NOT NULL
                GROUP BY STMDoc
                ORDER BY STMDoc DESC
        ');
        return view('account_pk.upstm_sss_ti',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
        ]);
    }
    public function upstm_sss_ti_detail(Request $request,$id)
    {
        $datenow             = date('Y-m-d');
        $startdate           = $request->startdate;
        $enddate             = $request->enddate;
        // $data['sss_ti']     = DB::connection('mysql')->select('
        //         SELECT b.STMDoc,SUM(b.Total_amount) as total
        //         FROM acc_1102050101_3099 a
        //         LEFT JOIN acc_stm_ti_total b ON b.hn = a.hn AND b.vstdate = a.vstdate
        //         WHERE b.HDflag IN("COC")
        //         GROUP BY STMDoc
        //         ORDER BY STMDoc DESC
        // ');
        $data['sss_ti']     = DB::connection('mysql')->select('
                SELECT a.STMDoc,SUM(a.stm_total) as total
                FROM acc_1102050101_3099 a

                GROUP BY a.STMDoc
                ORDER BY a.STMDoc DESC
        ');
        // WHERE a.stm_money IS NOT NULL
        $data['datashow']     = DB::connection('mysql')->select('
            SELECT *
            FROM acc_1102050101_3099
            WHERE STMdoc = "'.$id.'"

        ');
        // AND b.Total_amount IS NOT NULL
        return view('account_pk.upstm_sss_ti_detail',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'STMDoc'        =>     $id,
        ]);
    }

    public function upstm_ofc_ti_ipd(Request $request)
    {
        $datenow             = date('Y-m-d');
        $startdate           = $request->startdate;
        $enddate             = $request->enddate;
        $data['ofc_ti_ipd']     = DB::connection('mysql')->select('
            SELECT b.STMDoc,SUM(b.Total_amount) as total
            FROM acc_1102050101_4022 a
            LEFT JOIN acc_stm_ti_total b ON b.HDBill_hn = a.hn AND (b.vstdate BETWEEN a.vstdate AND a.dchdate)
            WHERE b.HDBill_TBill_HDflag IN("CIC")
            GROUP BY b.STMDoc
            ORDER BY STMDoc DESC
        ');
        return view('account_pk.upstm_ofc_ti_ipd',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
        ]);
    }
    public function upstm_ofc_ti_ipd_detail(Request $request,$id)
    {
        $datenow             = date('Y-m-d');
        $startdate           = $request->startdate;
        $enddate             = $request->enddate;
        $data['ofc_ti_ipd']     = DB::connection('mysql')->select('
            SELECT b.STMDoc,SUM(b.Total_amount) as total
            FROM acc_1102050101_4022 a
            LEFT JOIN acc_stm_ti_total b ON b.HDBill_hn = a.hn AND (b.vstdate BETWEEN a.vstdate AND a.dchdate)
            WHERE b.HDBill_TBill_HDflag IN("CIC")
            GROUP BY b.STMDoc
            ORDER BY STMDoc DESC
        ');
        $data['datashow']     = DB::connection('mysql')->select('
            SELECT a.vn,a.an,a.hn,a.vstdate,a.dchdate,a.cid,a.ptname,a.pttype,a.income,a.debit_total,b.STMdoc,b.Total_amount,a.rxdate
            FROM acc_1102050101_4022 a
            LEFT JOIN acc_stm_ti_total b ON b.HDBill_hn = a.hn AND (b.vstdate BETWEEN a.vstdate AND a.dchdate)
            WHERE b.STMdoc = "'.$id.'" AND b.HDBill_TBill_HDflag IN("CIC")
            GROUP BY a.hn,a.rxdate
        ');
        // HAVING COUNT(a.rxdate) >1;
        // GROUP BY a.hn,a.rxdate
        // AND b.Total_amount IS NOT NULL
        return view('account_pk.upstm_ofc_ti_ipd_detail',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'STMDoc'        =>     $id,
        ]);
    }
    public function upstm_lgo_detail_opd(Request $request,$id)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
            SELECT a.vn,a.vstdate,a.hn,a.cid,a.ptname,a.pttype,a.income,a.debit,a.debit_total,au.STMdoc,au.claim_true_af,au.price1_k
            from acc_1102050102_801 a
            LEFT OUTER JOIN acc_stm_lgo au ON au.cid_f = a.cid AND au.vstdate_i = a.vstdate
            where au.STMdoc = "'.$id.'"
            AND au.claim_true_af IS NOT NULL
        ');
        $data['lgo_opd'] = DB::connection('mysql')->select('SELECT STMDoc FROM acc_stm_lgo WHERE STMDoc LIKE "eclaim_10978_OP%" GROUP BY STMDoc ORDER BY STMDoc DESC');
        return view('account_pk.upstm_lgo_detail_opd',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'STMDoc'        =>     $id,
        ]);
    }
    public function upstm_lgo_detail_ipd(Request $request,$id)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
            SELECT a.vn,a.an,a.hn,a.vstdate,a.dchdate,a.cid,a.ptname,a.pttype,a.income,a.debit,a.debit_total,au.STMdoc,au.claim_true_af,au.price1_k
            from acc_1102050102_802 a
            LEFT OUTER JOIN acc_stm_lgo au ON au.an_e = a.an
            where au.STMdoc = "'.$id.'"
            AND au.claim_true_af IS NOT NULL
        ');
        $data['lgo_ipd'] = DB::connection('mysql')->select('SELECT STMDoc FROM acc_stm_lgo WHERE STMDoc LIKE "eclaim_10978_IP%" GROUP BY STMDoc ORDER BY STMDoc DESC');
        return view('account_pk.upstm_lgo_detail_ipd',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'STMDoc'        =>     $id,
        ]);
    }
    public function upstm_ofc_detail_ipd(Request $request,$id)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
                SELECT a.an,a.vn,a.hn,a.vstdate,a.dchdate,a.cid,a.ptname,a.pttype,a.income,a.debit,a.debit_total,b.STMdoc,b.pricereq_all
                FROM acc_1102050101_402 a
                LEFT OUTER JOIN acc_stm_ofc b ON b.an = a.an
                WHERE b.STMdoc = "'.$id.'"
                AND b.pricereq_all IS NOT NULL
        ');
        $data['ofc_ipd'] = DB::connection('mysql')->select('SELECT STMDoc FROM acc_stm_ofc WHERE STMDoc LIKE "STM_10978_IP%" GROUP BY STMDoc ORDER BY STMDoc DESC');
        return view('account_pk.upstm_ofc_detail_ipd',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'STMDoc'        =>     $id,
        ]);
    }
    public function upstm_ofc_detail_ti(Request $request,$id)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
                SELECT a.vn,a.hn,a.vstdate,a.cid,a.ptname,a.pttype,a.income,a.debit,a.debit_total,b.STMdoc,b.Total_amount
                FROM acc_1102050101_4011 a
                LEFT OUTER JOIN acc_stm_ti_total b ON b.hn = a.hn AND b.vstdate = a.vstdate
                WHERE b.STMdoc = "'.$id.'"
                AND b.Total_amount IS NOT NULL
        ');
        $data['ofc_ti_opd'] = DB::connection('mysql')->select('SELECT STMDoc,SUM(Total_amount) as total FROM acc_stm_ti_total WHERE HDflag IN("COC") GROUP BY STMDoc ORDER BY STMDoc DESC');
        return view('account_pk.upstm_ofc_detail_ti',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'STMDoc'        =>     $id,
        ]);
    }
    public function upstm_ofc_detail_ti_ipd(Request $request,$id)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
                SELECT a.an,a.vn,a.hn,a.vstdate,a.dchdate,a.cid,a.ptname,a.pttype,a.income,a.debit,a.debit_total,b.STMdoc,b.Total_amount
                FROM acc_1102050101_4022 a
                LEFT OUTER JOIN acc_stm_ti_total b ON b.hn = a.hn AND b.vstdate = a.dchdate
                WHERE b.STMdoc = "'.$id.'"
                AND b.Total_amount IS NOT NULL
        ');
        $data['ofc_ti_ipd'] = DB::connection('mysql')->select('SELECT STMDoc,SUM(Total_amount) as total FROM acc_stm_ti_total WHERE HDflag IN("CIC") GROUP BY STMDoc ORDER BY STMDoc DESC');
        return view('account_pk.upstm_ofc_detail_ti_ipd',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'STMDoc'        =>     $id,
        ]);
    }
    public function upstm_lgo_detail_ti(Request $request,$id)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
                SELECT a.vn,a.hn,a.vstdate,a.cid,a.ptname,a.pttype,a.income,a.debit,a.debit_total,b.STMdoc,b.pay_amount
                FROM acc_1102050102_8011 a
                LEFT OUTER JOIN acc_stm_lgoti b ON b.hn = a.hn AND b.vstdate = a.vstdate
                WHERE b.STMdoc = "'.$id.'"
                AND b.pay_amount IS NOT NULL
        ');
        $data['lgo_ti_opd'] = DB::connection('mysql')->select('SELECT STMDoc,SUM(pay_amount) as total FROM acc_stm_lgoti WHERE type LIKE "ผู้ป่วยนอก%" GROUP BY STMDoc ORDER BY STMDoc DESC');
        return view('account_pk.upstm_lgo_detail_ti',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'STMDoc'        =>     $id,
        ]);
    }
    public function upstm_lgo_detail_ti_ipd(Request $request,$id)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
            SELECT * FROM acc_stm_lgoti
                WHERE STMdoc = "'.$id.'"
                AND type = "ผู้ป่วยใน"

        ');
        $data['lgo_ti_ipd'] = DB::connection('mysql')->select('SELECT STMDoc,SUM(pay_amount) as total FROM acc_stm_lgoti WHERE type LIKE "ผู้ป่วยใน%" GROUP BY STMDoc ORDER BY STMDoc DESC');
        return view('account_pk.upstm_lgo_detail_ti_ipd',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'STMDoc'        =>     $id,
        ]);
    }
    public function upstm_sss_detail_ti(Request $request,$id)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
                SELECT a.vn,a.hn,a.vstdate,a.cid,a.ptname,a.pttype,a.income,a.debit,a.debit_total,b.STMdoc,b.Total_amount
                FROM acc_1102050101_3099 a
                LEFT OUTER JOIN acc_stm_ti_total b ON b.hn = a.hn AND b.vstdate = a.vstdate
                WHERE b.STMdoc = "'.$id.'"
                AND b.Total_amount IS NOT NULL
        ');
        $data['sss_ti'] = DB::connection('mysql')->select('SELECT STMDoc,SUM(Total_amount) as total FROM acc_stm_ti_total WHERE HDflag IN("COS") GROUP BY STMDoc ORDER BY STMDoc DESC');
        return view('account_pk.upstm_sss_detail_ti',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'STMDoc'        =>     $id,
        ]);
    }
    public function upstm_bkk_opd(Request $request)
    {
        $startdate       = $request->startdate;
        $enddate         = $request->enddate;
        $data['ofc_opd'] = DB::connection('mysql')->select('
                SELECT STMDoc,SUM(stm_money) as total
                FROM acc_1102050102_803
                GROUP BY STMDoc
                ORDER BY STMDoc DESC
        ');

        return view('account_pk.upstm_bkk_opd',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            // 'datashow'      =>     $datashow,
        ]);
    }
    public function upstm_bkk_opd_detail(Request $request,$id)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
                SELECT *
                FROM acc_1102050102_803
                WHERE STMdoc = "'.$id.'"
                AND stm_money IS NOT NULL
        ');
        // a.vn,a.hn,a.vstdate,a.cid,a.ptname,a.pttype,a.income,a.debit,a.debit_total,a.STMdoc,a.stm_money
        $data['bkk_opd'] = DB::connection('mysql')->select('
                SELECT STMDoc,SUM(stm_money) as total
                FROM acc_1102050102_803
                GROUP BY STMDoc
                ORDER BY STMDoc DESC
        ');
        return view('account_pk.upstm_bkk_opd_detail',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'STMDoc'        =>     $id,
        ]);
    }
    public function upstm_bkk_ipd(Request $request)
    {
        $startdate       = $request->startdate;
        $enddate         = $request->enddate;
        $data['ofc_ipd'] = DB::connection('mysql')->select('
                SELECT STMDoc,SUM(stm_money) as total
                FROM acc_1102050102_804
                GROUP BY STMDoc
                ORDER BY STMDoc DESC
        ');

        return view('account_pk.upstm_bkk_ipd',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            // 'datashow'      =>     $datashow,
        ]);
    }
    public function upstm_bkk_ipd_detail(Request $request,$id)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
                SELECT *
                FROM acc_1102050102_804
                WHERE STMdoc = "'.$id.'"
                AND stm_money IS NOT NULL
        ');
        // a.vn,a.hn,a.vstdate,a.cid,a.ptname,a.pttype,a.income,a.debit,a.debit_total,a.STMdoc,a.stm_money
        $data['bkk_ipd'] = DB::connection('mysql')->select('
                SELECT STMDoc,SUM(stm_money) as total
                FROM acc_1102050102_804
                GROUP BY STMDoc
                ORDER BY STMDoc DESC
        ');
        return view('account_pk.upstm_bkk_ipd_detail',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'STMDoc'        =>     $id,
        ]);
    }

    public function upstm_ucs(Request $request)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
            SELECT rep,vstdate,SUM(ip_paytrue) as Sumprice,STMdoc,month(vstdate) as months
            FROM acc_stm_ucs_excel
            GROUP BY rep
            ');
        $countc = DB::table('acc_stm_ucs_excel')->count();
        // dd($countc );
        // SELECT STMDoc,SUM(total_approve) as total FROM acc_stm_ucs GROUP BY STMDoc
        return view('account_pk.upstm_ucs',[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'countc'        =>     $countc
        ]);
    }
    function upstm_ucs_excel(Request $request)
    {
        // $this->validate($request, [
        //     'file' => 'required|file|mimes:xls,xlsx'
        // ]);
        $the_file = $request->file('file_stm');
        $file_ = $request->file('file_stm')->getClientOriginalName(); //ชื่อไฟล์
        // $the_file = $request->file('file_stm');
        // $file_ = $request->file('file_stm')->getClientOriginalName(); //ชื่อไฟล์
        // dd($the_file);
            // try{

                // Cheet 2
                $spreadsheet = IOFactory::load($the_file->getRealPath());
                $sheet        = $spreadsheet->setActiveSheetIndex(2);
                $row_limit    = $sheet->getHighestDataRow();
                // $column_limit = $sheet->getHighestDataColumn();
                $row_range    = range( '15', $row_limit );
                // $column_range = range( 'AO', $column_limit );
                // $spreadsheet->getActiveSheet()->setCellValue('A10', 1513789642);
                // $cellValue = $spreadsheet->getActiveSheet()->getCell('A10')->getCalculatedValue();
                // $data_header = '10';
                // $data_header = $sheet->getCell( 'A' . '10' )->getValue();


                $startcount = '15';
                $data = array();
                // dd($row_range);
                foreach ($row_range as $row ) {
                    $vst = $sheet->getCell( 'H' . $row )->getValue();
                    $day = substr($vst,0,2);
                    $mo = substr($vst,3,2);
                    $year = substr($vst,6,4);
                    $vstdate = $year.'-'.$mo.'-'.$day;

                    $reg = $sheet->getCell( 'I' . $row )->getValue();
                    $regday = substr($reg, 0, 2);
                    $regmo = substr($reg, 3, 2);
                    $regyear = substr($reg, 6, 4);
                    $dchdate = $regyear.'-'.$regmo.'-'.$regday;

                    $s = $sheet->getCell( 'S' . $row )->getValue();
                    $del_s = str_replace(",","",$s);
                    $t = $sheet->getCell( 'T' . $row )->getValue();
                    $del_t = str_replace(",","",$t);
                    $u = $sheet->getCell( 'U' . $row )->getValue();
                    $del_u = str_replace(",","",$u);
                    $v= $sheet->getCell( 'V' . $row )->getValue();
                    $del_v = str_replace(",","",$v);
                    $w = $sheet->getCell( 'W' . $row )->getValue();
                    $del_w = str_replace(",","",$w);
                    $x = $sheet->getCell( 'X' . $row )->getValue();
                    $del_x = str_replace(",","",$x);
                    $y = $sheet->getCell( 'Y' . $row )->getValue();
                    $del_y = str_replace(",","",$y);
                    $z = $sheet->getCell( 'Z' . $row )->getValue();
                    $del_z = str_replace(",","",$z);
                    $aa = $sheet->getCell( 'AA' . $row )->getValue();
                    $del_aa = str_replace(",","",$aa);
                    $ab = $sheet->getCell( 'AB' . $row )->getValue();
                    $del_ab = str_replace(",","",$ab);
                    $ac = $sheet->getCell( 'AC' . $row )->getValue();
                    $del_ac = str_replace(",","",$ac);
                    $ad = $sheet->getCell( 'AD' . $row )->getValue();
                    $del_ad = str_replace(",","",$ad);
                    $ae = $sheet->getCell( 'AE' . $row )->getValue();
                    $del_ae = str_replace(",","",$ae);
                    $af= $sheet->getCell( 'AF' . $row )->getValue();
                    $del_af = str_replace(",","",$af);
                    $ag = $sheet->getCell( 'AG' . $row )->getValue();
                    $del_ag = str_replace(",","",$ag);
                    $ah = $sheet->getCell( 'AH' . $row )->getValue();
                    $del_ah = str_replace(",","",$ah);
                    $ai = $sheet->getCell( 'AI' . $row )->getValue();
                    $del_ai = str_replace(",","",$ai);
                    $aj = $sheet->getCell( 'AJ' . $row )->getValue();
                    $del_aj = str_replace(",","",$aj);
                    $ak = $sheet->getCell( 'AK' . $row )->getValue();
                    $del_ak = str_replace(",","",$ak);
                    $al = $sheet->getCell( 'AL' . $row )->getValue();
                    $del_al = str_replace(",","",$al);

                    // $cellValue = $spreadsheet->getActiveSheet()->getCell('A10')->getCalculatedValue();
                    // if ($cellValue = 'ข้อมูลอุทธรณ์') {
                    //     $file_type = 'AUTON';
                    // } else {
                    //     $file_type = 'NARMAL';
                    // }


                    $data[] = [
                        'rep'                   =>$sheet->getCell( 'A' . $row )->getValue(),
                        'repno'                 =>$sheet->getCell( 'B' . $row )->getValue(),
                        'tranid'                =>$sheet->getCell( 'C' . $row )->getValue(),
                        'hn'                    =>$sheet->getCell( 'D' . $row )->getValue(),
                        'an'                    =>$sheet->getCell( 'E' . $row )->getValue(),
                        'cid'                   =>$sheet->getCell( 'F' . $row )->getValue(),
                        'fullname'              =>$sheet->getCell( 'G' . $row )->getValue(),
                        'vstdate'               =>$vstdate,
                        'dchdate'               =>$dchdate,
                        'maininscl'             =>$sheet->getCell( 'J' . $row )->getValue(),
                        'projectcode'           =>$sheet->getCell( 'K' . $row )->getValue(),
                        'debit'                 =>$sheet->getCell( 'L' . $row )->getValue(),
                        'debit_prb'             =>$sheet->getCell( 'M' . $row )->getValue(),
                        'adjrw'                 =>$sheet->getCell( 'N' . $row )->getValue(),
                        'ps1'                   =>$sheet->getCell( 'O' . $row )->getValue(),
                        'ps2'                   =>$sheet->getCell( 'P' . $row )->getValue(),
                        'ccuf'                  =>$sheet->getCell( 'Q' . $row )->getValue(),
                        'adjrw2'                =>$sheet->getCell( 'R' . $row )->getValue(),
                        'pay_money'             => $del_s,
                        'pay_slip'              => $del_t,
                        'pay_after'             => $del_u,
                        'op'                    => $del_v,
                        'ip_pay1'               => $del_w,
                        'ip_paytrue'            => $del_x,
                        'hc'                    => $del_y,
                        'hc_drug'               => $del_z,
                        'ae'                    => $del_aa,
                        'ae_drug'               => $del_ab,
                        'inst'                  => $del_ac,
                        'dmis_money1'           => $del_ad,
                        'dmis_money2'           => $del_ae,
                        'dmis_drug'             => $del_af,
                        'palliative_care'       => $del_ag,
                        'dmishd'                => $del_ah,
                        'pp'                    => $del_ai,
                        'fs'                    => $del_aj,
                        'opbkk'                 => $del_ak,
                        'total_approve'         => $del_al,
                        'va'                    =>$sheet->getCell( 'AM' . $row )->getValue(),
                        'covid'                 =>$sheet->getCell( 'AN' . $row )->getValue(),
                        'STMdoc'                =>$file_ ,
                        'STMdoc_type'           =>'NARMAL'
                    ];
                    $startcount++;

                }
                // DB::table('acc_stm_ucs_excel')->insert($data);

                $for_insert = array_chunk($data, length:1000);
                foreach ($for_insert as $key => $data_) {
                    Acc_stm_ucs_excel::insert($data_);
                }
                // Acc_stm_ucs_excel::insert($data);


                // Cheet 3
                $spreadsheet2 = IOFactory::load($the_file->getRealPath());
                $sheet2        = $spreadsheet2->setActiveSheetIndex(3);
                $row_limit2    = $sheet2->getHighestDataRow();
                $row_range2    = range( '15', $row_limit2 );
                $startcount2 = '15';
                $data2 = array();
                foreach ($row_range2 as $row2 ) {
                    $vst2 = $sheet2->getCell( 'H' . $row2 )->getValue();
                    $day2 = substr($vst2,0,2);
                    $mo2 = substr($vst2,3,2);
                    $year2 = substr($vst2,6,4);
                    $vstdate2 = $year2.'-'.$mo2.'-'.$day2;

                    $reg2 = $sheet2->getCell( 'I' . $row2 )->getValue();
                    $regday2 = substr($reg2, 0, 2);
                    $regmo2 = substr($reg2, 3, 2);
                    $regyear2 = substr($reg2, 6, 4);
                    $dchdate2 = $regyear2.'-'.$regmo2.'-'.$regday2;

                    $ss2 = $sheet2->getCell( 'S' . $row2 )->getValue();
                    $del_ss2 = str_replace(",","",$ss2);
                    $tt2 = $sheet2->getCell( 'T' . $row2 )->getValue();
                    $del_tt2 = str_replace(",","",$tt2);
                    $uu2 = $sheet2->getCell( 'U' . $row2 )->getValue();
                    $del_uu2 = str_replace(",","",$uu2);
                    $vv2= $sheet2->getCell( 'V' . $row2 )->getValue();
                    $del_vv2 = str_replace(",","",$vv2);
                    $ww2 = $sheet2->getCell( 'W' . $row2 )->getValue();
                    $del_ww2 = str_replace(",","",$ww2);
                    $xx2 = $sheet2->getCell( 'X' . $row2 )->getValue();
                    $del_xx2 = str_replace(",","",$xx2);
                    $yy2 = $sheet2->getCell( 'Y' . $row2 )->getValue();
                    $del_yy2 = str_replace(",","",$yy2);
                    $zz2 = $sheet2->getCell( 'Z' . $row2 )->getValue();
                    $del_zz2 = str_replace(",","",$zz2);
                    $aa2 = $sheet2->getCell( 'AA' . $row2 )->getValue();
                    $del_aa2 = str_replace(",","",$aa2);
                    $ab2 = $sheet2->getCell( 'AB' . $row2 )->getValue();
                    $del_ab2 = str_replace(",","",$ab2);
                    $ac2 = $sheet2->getCell( 'AC' . $row2 )->getValue();
                    $del_ac2 = str_replace(",","",$ac2);
                    $ad2 = $sheet2->getCell( 'AD' . $row2 )->getValue();
                    $del_ad2 = str_replace(",","",$ad2);
                    $ae2 = $sheet2->getCell( 'AE' . $row2 )->getValue();
                    $del_ae2 = str_replace(",","",$ae2);
                    $af2= $sheet2->getCell( 'AF' . $row2 )->getValue();
                    $del_af2 = str_replace(",","",$af2);
                    $ag2 = $sheet2->getCell( 'AG' . $row2 )->getValue();
                    $del_ag2 = str_replace(",","",$ag2);
                    $ah2 = $sheet2->getCell( 'AH' . $row2 )->getValue();
                    $del_ah2 = str_replace(",","",$ah2);
                    $ai2 = $sheet2->getCell( 'AI' . $row2 )->getValue();
                    $del_ai2 = str_replace(",","",$ai2);
                    $aj2 = $sheet2->getCell( 'AJ' . $row2 )->getValue();
                    $del_aj2 = str_replace(",","",$aj2);
                    $ak2 = $sheet2->getCell( 'AK' . $row2 )->getValue();
                    $del_ak2 = str_replace(",","",$ak2);
                    $al2 = $sheet2->getCell( 'AL' . $row2 )->getValue();
                    $del_al2 = str_replace(",","",$al2);


                    $data2[] = [
                        'rep'                   =>$sheet2->getCell( 'A' . $row2 )->getValue(),
                        'repno'                 =>$sheet2->getCell( 'B' . $row2 )->getValue(),
                        'tranid'                =>$sheet2->getCell( 'C' . $row2 )->getValue(),
                        'hn'                    =>$sheet2->getCell( 'D' . $row2 )->getValue(),
                        'an'                    =>$sheet2->getCell( 'E' . $row2 )->getValue(),
                        'cid'                   =>$sheet2->getCell( 'F' . $row2 )->getValue(),
                        'fullname'              =>$sheet2->getCell( 'G' . $row2 )->getValue(),
                        'vstdate'               =>$vstdate2,
                        'dchdate'               =>$dchdate2,
                        'maininscl'             =>$sheet2->getCell( 'J' . $row2 )->getValue(),
                        'projectcode'           =>$sheet2->getCell( 'K' . $row2 )->getValue(),
                        'debit'                 =>$sheet2->getCell( 'L' . $row2 )->getValue(),
                        'debit_prb'             =>$sheet2->getCell( 'M' . $row2 )->getValue(),
                        'adjrw'                 =>$sheet2->getCell( 'N' . $row2 )->getValue(),
                        'ps1'                   =>$sheet2->getCell( 'O' . $row2 )->getValue(),
                        'ps2'                   =>$sheet2->getCell( 'P' . $row2 )->getValue(),
                        'ccuf'                  =>$sheet2->getCell( 'Q' . $row2 )->getValue(),
                        'adjrw2'                =>$sheet2->getCell( 'R' . $row2 )->getValue(),
                        'pay_money'             => $del_ss2,
                        'pay_slip'              => $del_tt2,
                        'pay_after'             => $del_uu2,
                        'op'                    => $del_vv2,
                        'ip_pay1'               => $del_ww2,
                        'ip_paytrue'            => $del_xx2,
                        'ip_paytrue_auton'      => $del_xx2,
                        'hc'                    => $del_yy2,
                        'hc_drug'               => $del_zz2,
                        'ae'                    => $del_aa2,
                        'ae_drug'               => $del_ab2,
                        'inst'                  => $del_ac2,
                        'dmis_money1'           => $del_ad2,
                        'dmis_money2'           => $del_ae2,
                        'dmis_drug'             => $del_af2,
                        'palliative_care'       => $del_ag2,
                        'dmishd'                => $del_ah2,
                        'pp'                    => $del_ai2,
                        'fs'                    => $del_aj2,
                        'opbkk'                 => $del_ak2,
                        'total_approve_auton'   => $del_al2,
                        'va'                    =>$sheet2->getCell( 'AM' . $row2 )->getValue(),
                        'covid'                 =>$sheet2->getCell( 'AN' . $row2 )->getValue(),
                        // 'ao'                    =>$sheet->getCell( 'AO' . $row )->getValue(),
                        'STMdoc'                =>$file_,

                        'rep_auton'             =>$sheet2->getCell( 'A' . $row2 )->getValue(),
                        'repno_auton'           =>$sheet2->getCell( 'B' . $row2 )->getValue(),
                        'tranid_authon'         =>$sheet2->getCell( 'C' . $row2 )->getValue(),
                        'auton'                 =>$del_al2,
                        'STMdoc_authon'         =>$file_,
                        'STMdoc_type'           =>'AUTON'
                    ];
                    $startcount2++;

                }

                $for_insert2 = array_chunk($data2, length:1000);
                foreach ($for_insert2 as $key => $data2_) {
                    Acc_stm_ucs_excel::insert($data2_);
                }



            // } catch (Exception $e) {
            //     $error_code = $e->errorInfo[1];
            //     return back()->withErrors('There was a problem uploading the data!');
            // }
            // return back()->withSuccess('Great! Data has been successfully uploaded.');
            // return response()->json([
            //     'status'    => '200',
            // ]);
            return redirect()->back();
    }

    public function upstm_ucs_sendexcel(Request $request)
    {
        try{
            $data_ = DB::connection('mysql')->select('SELECT * FROM acc_stm_ucs_excel WHERE cid IS NOT NULL');
            foreach ($data_ as $key => $value) {
                if ($value->cid != '') {
                    $check = Acc_stm_ucs::where('tranid','=',$value->tranid)->count();
                    if ($check > 0) {
                        Acc_stm_ucs::where('tranid','=',$value->tranid)->update([
                            'rep_auton'      => $value->rep_auton,
                            'repno_auton'    => $value->repno_auton,
                            'tranid_authon'  => $value->tranid_authon
                        ]);
                    } else {
                        $add = new Acc_stm_ucs();
                        $add->rep            = $value->rep;
                        $add->repno          = $value->repno;
                        $add->tranid         = $value->tranid;
                        $add->hn             = $value->hn;
                        $add->an             = $value->an;
                        $add->cid            = $value->cid;
                        $add->fullname       = $value->fullname;
                        $add->vstdate        = $value->vstdate;
                        $add->dchdate        = $value->dchdate;
                        $add->maininscl      = $value->maininscl;
                        $add->projectcode    = $value->projectcode;
                        $add->debit          = $value->debit;
                        $add->debit_prb      = $value->debit_prb;
                        $add->adjrw          = $value->adjrw;
                        $add->ps1            = $value->ps1;
                        $add->ps2            = $value->ps2;

                        $add->ccuf           = $value->ccuf;
                        $add->adjrw2         = $value->adjrw2;
                        $add->pay_money      = $value->pay_money;
                        $add->pay_slip       = $value->pay_slip;
                        $add->pay_after      = $value->pay_after;
                        $add->op             = $value->op;
                        $add->ip_pay1        = $value->ip_pay1;
                        $add->ip_paytrue     = $value->ip_paytrue;
                        $add->ip_paytrue_auton     = $value->ip_paytrue_auton;
                        $add->hc             = $value->hc;
                        $add->hc_drug        = $value->hc_drug;
                        $add->ae             = $value->ae;
                        $add->ae_drug        = $value->ae_drug;
                        $add->inst           = $value->inst;
                        $add->dmis_money1    = $value->dmis_money1;
                        $add->dmis_money2    = $value->dmis_money2;
                        $add->dmis_drug      = $value->dmis_drug;
                        $add->palliative_care = $value->palliative_care;
                        $add->dmishd         = $value->dmishd;
                        $add->pp             = $value->pp;
                        $add->fs             = $value->fs;
                        $add->opbkk          = $value->opbkk;
                        $add->total_approve  = $value->total_approve;
                        $add->total_approve_auton  = $value->total_approve_auton;
                        $add->va             = $value->va;
                        $add->covid          = $value->covid;
                        $add->date_save      = $value->date_save;
                        $add->STMdoc         = $value->STMdoc;
                        $add->rep_auton      = $value->rep_auton;
                        $add->repno_auton    = $value->repno_auton;
                        $add->tranid_authon  = $value->tranid_authon;
                        $add->auton          = $value->auton;
                        $add->STMdoc_authon  = $value->STMdoc_authon;
                        $add->STMdoc_type    = $value->STMdoc_type;
                        $add->save();
                    }

                    if ($value->STMdoc_type == 'AUTON') {

                        if ($value->ip_paytrue > "0.00") {
                            Acc_1102050101_202::where('an',$value->an)
                                ->update([
                                    'repno_auton'       => $value->rep_auton.'-'.$value->repno_auton,
                                    'tranid_authon'     => $value->tranid_authon,
                                    'auton'             => $value->auton,
                                    'STMdoc_authon'     => $value->STMdoc_authon,
                            ]);
                        }
                        if ($value->hc_drug+$value->hc+$value->ae+$value->ae_drug+$value->inst+$value->dmis_money2+$value->dmis_drug == "0") {
                        // if ($value->hc_drug+$value->hc+$value->inst+$value->dmis_money2+$value->dmis_drug == "0") {

                            Acc_1102050101_217::where('an',$value->an)
                                ->update([
                                    'repno_auton'       => $value->rep_auton.'-'.$value->repno_auton,
                                    'tranid_authon'     => $value->tranid_authon,
                                    'stm_auton'         => $value->hc_drug+$value->hc+$value->inst+$value->dmis_money2+$value->dmis_drug,
                                    // 'stm_auton'         => $value->hc_drug+$value->hc+$value->inst+$value->dmis_money2+$value->dmis_drug,
                                    'auton'             => $value->auton,
                                    'STMdoc_authon'     => $value->STMdoc_authon,
                            ]);
                        }else if ($value->ae+$value->ae_drug+$value->hc_drug+$value->hc+$value->inst+$value->dmis_money2+$value->dmis_drug > "0") {
                            Acc_1102050101_217::where('an',$value->an)
                                ->update([
                                    'repno_auton'       => $value->rep_auton.'-'.$value->repno_auton,
                                    'tranid_authon'     => $value->tranid_authon,
                                    'stm_auton'         => $value->hc_drug+$value->hc+$value->ae+$value->ae_drug+$value->inst+$value->dmis_money2+$value->dmis_drug,
                                    'auton'             => $value->auton,
                                    'STMdoc_authon'     => $value->STMdoc_authon,
                            ]);

                        } else {
                        }
                    } else {
                        if ($value->ip_paytrue > "0.00") {
                            Acc_1102050101_202::where('an',$value->an)
                                ->update([
                                    'status'          => 'Y',
                                    'stm_rep'         => $value->debit,
                                    // 'stm_money'       => $value->ip_paytrue+$value->ae+$value->ae_drug,
                                    'stm_money'       => $value->ip_paytrue,
                                    'stm_rcpno'       => $value->rep.'-'.$value->repno,
                                    'stm_trainid'     => $value->tranid,
                                    // 'stm_total'       => $value->ip_paytrue+$value->ae+$value->ae_drug,
                                    'stm_total'       => $value->ip_paytrue,
                                    'STMdoc'          => $value->STMdoc,
                                    'va'              => $value->va,
                                    // 'auton'           => $value->ip_paytrue,
                                    // 'STMdoc_authon'   => $value->STMdoc_authon,
                            ]);
                        }
                        if ($value->ae+$value->ae_drug+$value->hc_drug+$value->hc+$value->inst+$value->dmis_money2+$value->dmis_drug == "0") {
                            Acc_1102050101_217::where('an',$value->an)
                                ->update([
                                    'status'          => 'Y',
                                    'stm_rep'         => $value->debit,
                                    'stm_rcpno'       => $value->rep.'-'.$value->repno,
                                    'stm_trainid'     => $value->tranid,
                                    'stm_total'       => $value->total_approve,
                                    // 'stm_auton'       => $value->hc_drug+$value->hc+$value->ae+$value->ae_drug+$value->inst+$value->dmis_money2+$value->dmis_drug,
                                    'STMdoc'          => $value->STMdoc,
                                    'va'              => $value->va,
                                    // 'auton'           => $value->auton,
                                    // 'STMdoc_authon'   => $value->STMdoc_authon,
                            ]);
                        }else if ($value->ae+$value->ae_drug+$value->hc_drug+$value->hc+$value->inst+$value->dmis_money2+$value->dmis_drug > "0") {
                            Acc_1102050101_217::where('an',$value->an)
                                ->update([
                                    'status'          => 'Y',
                                    'stm_rep'         => $value->debit,
                                    'stm_money'       => $value->ae+$value->ae_drug+$value->hc_drug+$value->hc+$value->inst+$value->dmis_money2+$value->dmis_drug,
                                    'stm_rcpno'       => $value->rep.'-'.$value->repno,
                                    'stm_trainid'     => $value->tranid,
                                    'stm_total'        => $value->ae+$value->ae_drug+$value->hc_drug+$value->hc+$value->inst+$value->dmis_money2+$value->dmis_drug,
                                    // 'stm_auton'       => $value->hc_drug+$value->hc+$value->ae+$value->ae_drug+$value->inst+$value->dmis_money2+$value->dmis_drug,
                                    'STMdoc'          => $value->STMdoc,
                                    'va'              => $value->va,
                                    // 'auton'           => $value->auton,
                                    // 'STMdoc_authon'   => $value->STMdoc_authon,
                            ]);
                        } else {
                        }
                    }


                } else {
                }
            }
            } catch (Exception $e) {
                $error_code = $e->errorInfo[1];
                return back()->withErrors('There was a problem uploading the data!');
            }
            Acc_stm_ucs_excel::truncate();
        return redirect()->back();
    }

    public function upstm_ucs_op(Request $request)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
            SELECT rep,vstdate,SUM(ip_paytrue) as Sumprice,STMdoc,month(vstdate) as months
            FROM acc_stm_ucs_excel
            GROUP BY rep
            ');
        $countc = DB::table('acc_stm_ucs_excel')->count();
        return view('account_pk.upstm_ucs_op',[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'countc'        =>     $countc
        ]);
    }
    function upstm_ucs_opsaveexcel(Request $request)
    {
         // $this->validate($request, [
        //     'file' => 'required|file|mimes:xls,xlsx'
        // ]);
        $the_file_ = $request->file('file');
        // $file_ = $the_file->getClientOriginalName();
        // $file_ = $request->file('upload_file')->getClientOriginalName(); //ชื่อไฟล์
        if($request->hasFile('file')){
            $the_file = $request->file('file');
            $file_ = $the_file->getClientOriginalName();
        }
        // dd($file);
            try{
                // $a = array('2','3');
                // foreach($a as $value){
                //     $table_insert = $sss[0];
                //     $sheet_read = $sss[1];
                //     // code($sheet_read)
                //     // insert_table $table_insert
                // }

                // Cheet 2
                $spreadsheet = IOFactory::load($the_file_->getRealPath());
                $sheet        = $spreadsheet->setActiveSheetIndex(2);
                $row_limit    = $sheet->getHighestDataRow();
                $column_limit = $sheet->getHighestDataColumn();
                $row_range    = range( '15', $row_limit );
                // $column_range = range( 'AO', $column_limit );
                $startcount = '15';
                $data = array();
                foreach ($row_range as $row ) {
                    $vst = $sheet->getCell( 'H' . $row )->getValue();
                    $day = substr($vst,0,2);
                    $mo = substr($vst,3,2);
                    $year = substr($vst,6,4);
                    $vstdate = $year.'-'.$mo.'-'.$day;

                    $reg = $sheet->getCell( 'I' . $row )->getValue();
                    $regday = substr($reg, 0, 2);
                    $regmo = substr($reg, 3, 2);
                    $regyear = substr($reg, 6, 4);
                    $dchdate = $regyear.'-'.$regmo.'-'.$regday;

                    $s = $sheet->getCell( 'S' . $row )->getValue();
                    $del_s = str_replace(",","",$s);
                    $t = $sheet->getCell( 'T' . $row )->getValue();
                    $del_t = str_replace(",","",$t);
                    $u = $sheet->getCell( 'U' . $row )->getValue();
                    $del_u = str_replace(",","",$u);
                    $v= $sheet->getCell( 'V' . $row )->getValue();
                    $del_v = str_replace(",","",$v);
                    $w = $sheet->getCell( 'W' . $row )->getValue();
                    $del_w = str_replace(",","",$w);
                    $x = $sheet->getCell( 'X' . $row )->getValue();
                    $del_x = str_replace(",","",$x);
                    $y = $sheet->getCell( 'Y' . $row )->getValue();
                    $del_y = str_replace(",","",$y);
                    $z = $sheet->getCell( 'Z' . $row )->getValue();
                    $del_z = str_replace(",","",$z);
                    $aa = $sheet->getCell( 'AA' . $row )->getValue();
                    $del_aa = str_replace(",","",$aa);
                    $ab = $sheet->getCell( 'AB' . $row )->getValue();
                    $del_ab = str_replace(",","",$ab);
                    $ac = $sheet->getCell( 'AC' . $row )->getValue();
                    $del_ac = str_replace(",","",$ac);
                    $ad = $sheet->getCell( 'AD' . $row )->getValue();
                    $del_ad = str_replace(",","",$ad);
                    $ae = $sheet->getCell( 'AE' . $row )->getValue();
                    $del_ae = str_replace(",","",$ae);
                    $af= $sheet->getCell( 'AF' . $row )->getValue();
                    $del_af = str_replace(",","",$af);
                    $ag = $sheet->getCell( 'AG' . $row )->getValue();
                    $del_ag = str_replace(",","",$ag);
                    $ah = $sheet->getCell( 'AH' . $row )->getValue();
                    $del_ah = str_replace(",","",$ah);
                    $ai = $sheet->getCell( 'AI' . $row )->getValue();
                    $del_ai = str_replace(",","",$ai);
                    $aj = $sheet->getCell( 'AJ' . $row )->getValue();
                    $del_aj = str_replace(",","",$aj);
                    $ak = $sheet->getCell( 'AK' . $row )->getValue();
                    $del_ak = str_replace(",","",$ak);
                    $al = $sheet->getCell( 'AL' . $row )->getValue();
                    $del_al = str_replace(",","",$al);

                    // $rep_ = $sheet->getCell( 'A' . $row )->getValue();

                    $data[] = [
                        'rep'                   =>$sheet->getCell( 'A' . $row )->getValue(),
                        'repno'                 =>$sheet->getCell( 'B' . $row )->getValue(),
                        'tranid'                =>$sheet->getCell( 'C' . $row )->getValue(),
                        'hn'                    =>$sheet->getCell( 'D' . $row )->getValue(),
                        'an'                    =>$sheet->getCell( 'E' . $row )->getValue(),
                        'cid'                   =>$sheet->getCell( 'F' . $row )->getValue(),
                        'fullname'              =>$sheet->getCell( 'G' . $row )->getValue(),
                        'vstdate'               =>$vstdate,
                        'dchdate'               =>$dchdate,
                        'maininscl'             =>$sheet->getCell( 'J' . $row )->getValue(),
                        'projectcode'           =>$sheet->getCell( 'K' . $row )->getValue(),
                        'debit'                 =>$sheet->getCell( 'L' . $row )->getValue(),
                        'debit_prb'             =>$sheet->getCell( 'M' . $row )->getValue(),
                        'adjrw'                 =>$sheet->getCell( 'N' . $row )->getValue(),
                        'ps1'                   =>$sheet->getCell( 'O' . $row )->getValue(),
                        'ps2'                   =>$sheet->getCell( 'P' . $row )->getValue(),
                        'ccuf'                  =>$sheet->getCell( 'Q' . $row )->getValue(),
                        'adjrw2'                =>$sheet->getCell( 'R' . $row )->getValue(),
                        'pay_money'             => $del_s,
                        'pay_slip'              => $del_t,
                        'pay_after'             => $del_u,
                        'op'                    => $del_v,
                        'ip_pay1'               => $del_w,
                        'ip_paytrue'            => $del_x,
                        'hc'                    => $del_y,
                        'hc_drug'               => $del_z,
                        'ae'                    => $del_aa,
                        'ae_drug'               => $del_ab,
                        'inst'                  => $del_ac,
                        'dmis_money1'           => $del_ad,
                        'dmis_money2'           => $del_ae,
                        'dmis_drug'             => $del_af,
                        'palliative_care'       => $del_ag,
                        'dmishd'                => $del_ah,
                        'pp'                    => $del_ai,
                        'fs'                    => $del_aj,
                        'opbkk'                 => $del_ak,
                        'total_approve'         => $del_al,
                        'va'                    =>$sheet->getCell( 'AM' . $row )->getValue(),
                        'covid'                 =>$sheet->getCell( 'AN' . $row )->getValue(),
                        // 'ao'                    =>$sheet->getCell( 'AO' . $row )->getValue(),
                        'STMdoc'                =>$file_
                    ];
                    $startcount++;

                }
                // DB::table('acc_stm_ucs_excel')->insert($data);

                $for_insert = array_chunk($data, length:5000);
                foreach ($for_insert as $key => $data_) {
                    Acc_stm_ucs_excel::insert($data_);
                }
                // Acc_stm_ucs_excel::insert($data);


                // Cheet 3
                // $spreadsheet2 = IOFactory::load($the_file_->getRealPath());
                // $sheet2        = $spreadsheet2->setActiveSheetIndex(3);
                // $row_limit2    = $sheet2->getHighestDataRow();
                // $column_limit2 = $sheet2->getHighestDataColumn();
                // $row_range2    = range( 15, $row_limit2 );
                // // $column_range2 = range( 'AO', $column_limit2 );
                // $startcount2 = 15;
                // $data2 = array();
                // foreach ($row_range2 as $row2 ) {
                //     $vst2 = $sheet2->getCell( 'H' . $row2 )->getValue();
                //     $day2 = substr($vst2,0,2);
                //     $mo2 = substr($vst2,3,2);
                //     $year2 = substr($vst2,6,4);
                //     $vstdate2 = $year2.'-'.$mo2.'-'.$day2;

                //     $reg2 = $sheet2->getCell( 'I' . $row2 )->getValue();
                //     $regday2 = substr($reg2, 0, 2);
                //     $regmo2 = substr($reg2, 3, 2);
                //     $regyear2 = substr($reg2, 6, 4);
                //     $dchdate2 = $regyear2.'-'.$regmo2.'-'.$regday2;

                //     $ss = $sheet2->getCell( 'S' . $row2 )->getValue();
                //     $del_ss = str_replace(",","",$ss);
                //     $tt = $sheet2->getCell( 'T' . $row2 )->getValue();
                //     $del_tt = str_replace(",","",$tt);
                //     $uu = $sheet2->getCell( 'U' . $row2 )->getValue();
                //     $del_uu = str_replace(",","",$uu);
                //     $vv= $sheet2->getCell( 'V' . $row2 )->getValue();
                //     $del_vv = str_replace(",","",$vv);
                //     $ww = $sheet2->getCell( 'W' . $row2 )->getValue();
                //     $del_ww = str_replace(",","",$ww);
                //     $xx = $sheet2->getCell( 'X' . $row2 )->getValue();
                //     $del_xx = str_replace(",","",$xx);
                //     $yy = $sheet2->getCell( 'Y' . $row2 )->getValue();
                //     $del_yy = str_replace(",","",$yy);
                //     $zz = $sheet2->getCell( 'Z' . $row2 )->getValue();
                //     $del_zz = str_replace(",","",$zz);
                //     $aa2 = $sheet2->getCell( 'AA' . $row2 )->getValue();
                //     $del_aa2 = str_replace(",","",$aa2);
                //     $ab2 = $sheet2->getCell( 'AB' . $row2 )->getValue();
                //     $del_ab2 = str_replace(",","",$ab2);
                //     $ac2 = $sheet2->getCell( 'AC' . $row2 )->getValue();
                //     $del_ac2 = str_replace(",","",$ac2);
                //     $ad2 = $sheet2->getCell( 'AD' . $row2 )->getValue();
                //     $del_ad2 = str_replace(",","",$ad2);
                //     $ae2 = $sheet2->getCell( 'AE' . $row2 )->getValue();
                //     $del_ae2 = str_replace(",","",$ae2);
                //     $af2= $sheet2->getCell( 'AF' . $row2 )->getValue();
                //     $del_af2 = str_replace(",","",$af2);
                //     $ag2 = $sheet2->getCell( 'AG' . $row2 )->getValue();
                //     $del_ag2 = str_replace(",","",$ag2);
                //     $ah2 = $sheet2->getCell( 'AH' . $row2 )->getValue();
                //     $del_ah2 = str_replace(",","",$ah2);
                //     $ai2 = $sheet2->getCell( 'AI' . $row2 )->getValue();
                //     $del_ai2 = str_replace(",","",$ai2);
                //     $aj2 = $sheet2->getCell( 'AJ' . $row2 )->getValue();
                //     $del_aj2 = str_replace(",","",$aj2);
                //     $ak2 = $sheet2->getCell( 'AK' . $row2 )->getValue();
                //     $del_ak2 = str_replace(",","",$ak2);
                //     $al2 = $sheet2->getCell( 'AL' . $row2 )->getValue();
                //     $del_al2 = str_replace(",","",$al2);


                //     $data2[] = [
                //         'rep'                   =>$sheet2->getCell( 'A' . $row2 )->getValue(),
                //         'repno'                 =>$sheet2->getCell( 'B' . $row2 )->getValue(),
                //         'tranid'                =>$sheet2->getCell( 'C' . $row2 )->getValue(),
                //         'hn'                    =>$sheet2->getCell( 'D' . $row2 )->getValue(),
                //         'an'                    =>$sheet2->getCell( 'E' . $row2 )->getValue(),
                //         'cid'                   =>$sheet2->getCell( 'F' . $row2 )->getValue(),
                //         'fullname'              =>$sheet2->getCell( 'G' . $row2 )->getValue(),
                //         'vstdate'               =>$vstdate2,
                //         'dchdate'               =>$dchdate2,
                //         'maininscl'             =>$sheet2->getCell( 'J' . $row2 )->getValue(),
                //         'projectcode'           =>$sheet2->getCell( 'K' . $row2 )->getValue(),
                //         'debit'                 =>$sheet2->getCell( 'L' . $row2 )->getValue(),
                //         'debit_prb'             =>$sheet2->getCell( 'M' . $row2 )->getValue(),
                //         'adjrw'                 =>$sheet2->getCell( 'N' . $row2 )->getValue(),
                //         'ps1'                   =>$sheet2->getCell( 'O' . $row2 )->getValue(),
                //         'ps2'                   =>$sheet2->getCell( 'P' . $row2 )->getValue(),
                //         'ccuf'                  =>$sheet2->getCell( 'Q' . $row2 )->getValue(),
                //         'adjrw2'                =>$sheet2->getCell( 'R' . $row2 )->getValue(),
                //         'pay_money'             => $del_ss,
                //         'pay_slip'              => $del_tt,
                //         'pay_after'             => $del_uu,
                //         'op'                    => $del_vv,
                //         'ip_pay1'               => $del_ww,
                //         'ip_paytrue'            => $del_xx,
                //         'hc'                    => $del_yy,
                //         'hc_drug'               => $del_zz,
                //         'ae'                    => $del_aa2,
                //         'ae_drug'               => $del_ab2,
                //         'inst'                  => $del_ac2,
                //         'dmis_money1'           => $del_ad2,
                //         'dmis_money2'           => $del_ae2,
                //         'dmis_drug'             => $del_af2,
                //         'palliative_care'       => $del_ag2,
                //         'dmishd'                => $del_ah2,
                //         'pp'                    => $del_ai2,
                //         'fs'                    => $del_aj2,
                //         'opbkk'                 => $del_ak2,
                //         'total_approve'         => $del_al2,
                //         'va'                    =>$sheet2->getCell( 'AM' . $row2 )->getValue(),
                //         'covid'                 =>$sheet2->getCell( 'AN' . $row2 )->getValue(),
                //         // 'ao'                    =>$sheet->getCell( 'AO' . $row )->getValue(),
                //         'STMdoc'                =>$file_
                //     ];
                //     $startcount2++;

                // }
                // // DB::table('acc_stm_ucs_excel')->Transaction::insert($data2);
                // $for_insert2 = array_chunk($data2, length:5000);
                // foreach ($for_insert2 as $key => $data2_) {
                //     Acc_stm_ucs_excel::insert($data2_);
                // }



            } catch (Exception $e) {
                $error_code = $e->errorInfo[1];
                return back()->withErrors('There was a problem uploading the data!');
            }
            // return back()->withSuccess('Great! Data has been successfully uploaded.');
            return response()->json([
            'status'    => '200',
        ]);
    }
    public function upstm_ucs_op_sendexcel(Request $request)
    {
        try{
            $data_ = DB::connection('mysql')->select('
                SELECT *
                FROM acc_stm_ucs_excel
            ');
            foreach ($data_ as $key => $value) {
                if ($value->cid != '') {
                    $check = Acc_stm_ucs::where('tranid','=',$value->tranid)->count();
                    if ($check > 0) {
                    } else {
                        $add = new Acc_stm_ucs();
                        $add->rep            = $value->rep;
                        $add->repno          = $value->repno;
                        $add->tranid         = $value->tranid;
                        $add->hn             = $value->hn;
                        $add->an             = $value->an;
                        $add->cid            = $value->cid;
                        $add->fullname       = $value->fullname;
                        $add->vstdate        = $value->vstdate;
                        $add->dchdate        = $value->dchdate;
                        $add->maininscl      = $value->maininscl;
                        $add->projectcode    = $value->projectcode;
                        $add->debit          = $value->debit;
                        $add->debit_prb      = $value->debit_prb;
                        $add->adjrw          = $value->adjrw;
                        $add->ps1            = $value->ps1;
                        $add->ps2            = $value->ps2;

                        $add->ccuf           = $value->ccuf;
                        $add->adjrw2         = $value->adjrw2;
                        $add->pay_money      = $value->pay_money;
                        $add->pay_slip       = $value->pay_slip;
                        $add->pay_after      = $value->pay_after;
                        $add->op             = $value->op;
                        $add->ip_pay1        = $value->ip_pay1;
                        $add->ip_paytrue     = $value->ip_paytrue;
                        $add->hc             = $value->hc;
                        $add->hc_drug        = $value->hc_drug;
                        $add->ae             = $value->ae;
                        $add->ae_drug        = $value->ae_drug;
                        $add->inst           = $value->inst;
                        $add->dmis_money1    = $value->dmis_money1;
                        $add->dmis_money2    = $value->dmis_money2;
                        $add->dmis_drug      = $value->dmis_drug;
                        $add->palliative_care = $value->palliative_care;
                        $add->dmishd         = $value->dmishd;
                        $add->pp             = $value->pp;
                        $add->fs             = $value->fs;
                        $add->opbkk          = $value->opbkk;
                        $add->total_approve  = $value->total_approve;
                        $add->va             = $value->va;
                        $add->covid          = $value->covid;
                        $add->date_save      = $value->date_save;
                        $add->STMdoc         = $value->STMdoc;
                        $add->save();

                    }

                    // if ($value->ip_paytrue == "0.00") {
                    //     Acc_1102050101_202::where('an',$value->an)
                    //         ->update([
                    //             'status'          => 'Y',
                    //             'stm_rep'         => $value->debit,
                    //             // 'stm_money'       => $value->ip_paytrue,
                    //             'stm_rcpno'       => $value->rep.'-'.$value->repno,
                    //             'stm_trainid'     => $value->tranid,
                    //             'stm_total'       => $value->total_approve,
                    //             'STMdoc'          => $value->STMdoc,
                    //             'va'              => $value->va,
                    //     ]);
                    // }else if ($value->ip_paytrue > "0.00") {
                    //         Acc_1102050101_202::where('an',$value->an)
                    //             ->update([
                    //                 'status'          => 'Y',
                    //                 'stm_rep'         => $value->debit,
                    //                 'stm_money'       => $value->ip_paytrue,
                    //                 'stm_rcpno'       => $value->rep.'-'.$value->repno,
                    //                 'stm_trainid'     => $value->tranid,
                    //                 'stm_total'       => $value->total_approve,
                    //                 'STMdoc'          => $value->STMdoc,
                    //                 'va'              => $value->va,
                    //         ]);
                    // } else {
                    // }

                    if ($value->hc_drug+$value->hc+$value->ae+$value->ae_drug+$value->inst+$value->dmis_money2+$value->dmis_drug == "0") {
                        Acc_1102050101_216::where('hn',$value->hn)->where('vstdate',$value->vstdate)
                            ->update([
                                'status'          => 'Y',
                                'stm_rep'         => $value->debit,
                                // 'stm_money'       => $value->hc_drug+$value->hc+$value->ae+$value->ae_drug+$value->inst+$value->dmis_money2+$value->dmis_drug,
                                'stm_rcpno'       => $value->rep.'-'.$value->repno,
                                'stm_trainid'     => $value->tranid,
                                'stm_total'       => $value->total_approve,
                                'STMDoc'          => $value->STMdoc,
                                'va'              => $value->va,
                        ]);
                    }else if ($value->hc_drug+$value->hc+$value->ae+$value->ae_drug+$value->inst+$value->dmis_money2+$value->dmis_drug > "0") {
                        Acc_1102050101_216::where('hn',$value->hn)->where('vstdate',$value->vstdate)
                            ->update([
                                'status'          => 'Y',
                                'stm_rep'         => $value->debit,
                                'stm_money'       => $value->hc_drug+$value->hc+$value->ae+$value->ae_drug+$value->inst+$value->dmis_money2+$value->dmis_drug,
                                'stm_rcpno'       => $value->rep.'-'.$value->repno,
                                'stm_trainid'     => $value->tranid,
                                'stm_total'       => $value->total_approve,
                                'STMDoc'          => $value->STMdoc,
                                'va'              => $value->va,
                        ]);
                    } else {
                    }



                } else {
                }
            }
            } catch (Exception $e) {
                $error_code = $e->errorInfo[1];
                return back()->withErrors('There was a problem uploading the data!');
            }
            Acc_stm_ucs_excel::truncate();
        return redirect()->back();
    }

    // Acc_stm_ti
    public function upstm_ti(Request $request)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('
                SELECT repno,vstdate,SUM(pay_amount) as Sumprice,filename,month(vstdate) as months
                FROM acc_stm_ti_excel
                GROUP BY repno
            ');
        $countc = DB::table('acc_stm_ti_excel')->count();
        // dd($countc );
        return view('account_pk.upstm_ti',[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'countc'        =>     $countc
        ]);
    }
    public function upstm_hn(Request $request)
    {
        $startdate = $request->datepicker;
        $enddate = $request->datepicker2;

        $acc_debtor = DB::select('
            SELECT tranid,hn,cid,vstdate from acc_stm_ti
            WHERE vstdate BETWEEN "'.$startdate.'" AND "'.$enddate.'"
            AND hn is null;
        ');
        // AND hn is null;
        foreach ($acc_debtor as $key => $value) {

            $data_ = DB::table('acc_stm_ti')->where('hn','<>','')->where('cid','=',$value->cid)->first();
            $datahn = $data_->hn;

            Acc_stm_ti::where('cid', $value->cid)
            // ->where('vstdate', $value->vstdate)
            // ->where('hn','=',$datahn)
                    ->update([
                            'hn'   => $datahn
                ]);
        }
        return view('account_pk.upstm_ti',[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
        ]);
        // return response()->json([
        //     'status'    => '200'
        // ]);
    }

    public function upstm_ti_import(Request $request)
    {
        // dd($request->file('file'));
        // Excel::import(new ImportAcc_stm_ti, $request->file('file')->store('files'));
        // Acc_stm_ti_excel::truncate();

            Excel::import(new ImportAcc_stm_tiexcel_import, $request->file('file')->store('files'));
             return response()->json([
                'status'    => '200',
            ]);

        // return response()->json([
        //     'status'    => '200',
        // ]);
    }
    public function upstm_ti_importtotal(Request $request)
    {
        $data_ = DB::connection('mysql')->select('
                SELECT repno,hn,cid,fullname,vstdate,hipdata_code,qty,unitprice,pay_amount,filename,SUM(pay_amount) as total_pay
                FROM acc_stm_ti_excel

                WHERE pay_amount <> 0 AND list LIKE "%HD%"
                GROUP BY cid,vstdate

        ');
        // --  WHERE cid ='5361100020614'
        // ,sum(pay_amount) as Sumprice
        // GROUP BY cid,vstdate
        foreach ($data_ as $key => $value) {
            // $check = Acc_stm_ti_total::where('cid',$value->cid)->where('vstdate',$value->vstdate)->count();

                Acc_1102050101_2166::where('cid',$value->cid)->where('vstdate',$value->vstdate)
                    ->update([
                        'status'   => 'Y'
                    ]);

                $add = new Acc_stm_ti_total();
                // $add->repno                  = $value->repno;
                $add->HDBill_hn               = $value->hn;
                $add->HDBill_pid              = $value->cid;
                $add->HDBill_name             = $value->fullname;
                $add->vstdate                 = $value->vstdate;
                $add->HDBill_TBill_amount     = $value->pay_amount;
                $add->sum_price_approve       = $value->total_pay;
                $add->Total_amount            = $value->total_pay;
                $add->STMdoc                  = $value->filename;
                $add->HDBill_TBill_HDflag     = $value->hipdata_code;
                $add->save();


        }


        Acc_stm_ti_excel::truncate();
        return redirect()->back();
    }

    function upstm_ti_importexcel(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:xls,xlsx'
        ]);
        $the_file = $request->file('file');
        $file_ = $request->file('file')->getClientOriginalName(); //ชื่อไฟล์
            try{
                $spreadsheet = IOFactory::load($the_file->getRealPath());
                // $sheet        = $spreadsheet->getActiveSheet(2);
                $sheet        = $spreadsheet->setActiveSheetIndex(2);
                $row_limit    = $sheet->getHighestDataRow();
                $column_limit = $sheet->getHighestDataColumn();
                $row_range    = range( '9', $row_limit );
                // $column_range = range( 'F', $column_limit );
                $startcount = '9';

                $data = array();
                foreach ($row_range as $row ) {

                    $vst = $sheet->getCell( 'K' . $row )->getValue();
                    // $starttime = substr($vst, 0, 5);
                    $day = substr($vst,0,2);
                    $mo = substr($vst,3,2);
                    $year = substr($vst,6,4);
                    $vstdate = $year.'-'.$mo.'-'.$day;

                    $reg = $sheet->getCell( 'J' . $row )->getValue();
                    // $starttime = substr($reg, 0, 5);
                    $regday = substr($reg, 0, 2);
                    $regmo = substr($reg, 3, 2);
                    $regyear = substr($reg, 6, 4);
                    $regdate = $regyear.'-'.$regmo.'-'.$regday;

                    $o = $sheet->getCell( 'O' . $row )->getValue();
                    $del_o = str_replace(",","",$o);
                    $p = $sheet->getCell( 'P' . $row )->getValue();
                    $del_p = str_replace(",","",$p);
                    $q = $sheet->getCell( 'Q' . $row )->getValue();
                    $del_q = str_replace(",","",$q);
                    $t= $sheet->getCell( 'T' . $row )->getValue();
                    $del_t = str_replace(",","",$t);
                    $u = $sheet->getCell( 'U' . $row )->getValue();
                    $del_u = str_replace(",","",$u);
                    $v = $sheet->getCell( 'V' . $row )->getValue();
                    $del_v = str_replace(",","",$v);
                    $w = $sheet->getCell( 'W' . $row )->getValue();
                    $del_w = str_replace(",","",$w);

                   $data[] = [
                        'repno'                 =>$sheet->getCell( 'B' . $row )->getValue(),
                        'tranid'                =>$sheet->getCell( 'C' . $row )->getValue(),
                        'hn'                    =>$sheet->getCell( 'D' . $row )->getValue(),
                        'an'                    =>$sheet->getCell( 'E' . $row )->getValue(),
                        'cid'                   =>$sheet->getCell( 'F' . $row )->getValue(),
                        'fullname'              =>$sheet->getCell( 'G' . $row )->getValue(),
                        'hipdata_code'          =>$sheet->getCell( 'H' . $row )->getValue(),
                        'hcode'                 =>$sheet->getCell( 'I' . $row )->getValue(),

                        'vstdate'               =>$vstdate,
                        'regdate'               =>$regdate,
                        'no'                    =>$sheet->getCell( 'L' . $row )->getValue(),
                        'list'                  =>$sheet->getCell( 'M' . $row )->getValue(),
                        'qty'                   =>$sheet->getCell( 'N' . $row )->getValue(),

                        'unitprice'             =>$del_o,
                        'unitprice_max'         =>$del_p,
                        'price_request'         =>$del_q,

                        'pscode'                =>$sheet->getCell( 'R' . $row )->getValue(),
                        'percent'               =>$sheet->getCell( 'S' . $row )->getValue(),

                        'pay_amount'            =>$del_t,
                        'nonpay_amount'         =>$del_u,
                        'payplus_amount'        =>$del_v,
                        'payback_amount'        =>$del_w,
                        'filename'              =>$file_
                    ];


                    $startcount++;
                    // $file_

                }
                // DB::table('acc_stm_ti_excel')->insert($data);
                $check = Acc_stm_ti_total::where('STMdoc',$file_)->count();
                if ($check > 0) {
                    return response()->json([
                        'status'    => '100',
                    ]);
                } else {
                    $for_insert2 = array_chunk($data, length:1000);
                    foreach ($for_insert2 as $key => $data2_) {
                        acc_stm_ti_excel::insert($data2_);
                    }
                }


            } catch (Exception $e) {
                $error_code = $e->errorInfo[1];
                return back()->withErrors('There was a problem uploading the data!');
            }
            // return back()->withSuccess('Great! Data has been successfully uploaded.');
            return response()->json([
            'status'    => '200',
        ]);
    }
    public function upstm_tixml(Request $request)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->startdate;
        $enddate = $request->enddate;

        return view('account_pk.upstm_tixml',[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
        ]);
    }
    public function upstm_tixml_import(Request $request)
    {
            $tar_file_ = $request->file;
            $file_ = $request->file('file')->getClientOriginalName(); //ชื่อไฟล์
            $filename = pathinfo($file_, PATHINFO_FILENAME);
            $extension = pathinfo($file_, PATHINFO_EXTENSION);
            $xmlString = file_get_contents(($tar_file_));
            $xmlObject = simplexml_load_string($xmlString);
            $json = json_encode($xmlObject);
            $result = json_decode($json, true);

            // dd($result);
            @$stmAccountID = $result['stmAccountID'];
            @$hcode = $result['hcode'];
            @$hname = $result['hname'];
            @$AccPeriod = $result['AccPeriod'];
            @$STMdoc = $result['STMdoc'];
            @$dateStart = $result['dateStart'];
            @$dateEnd = $result['dateEnd'];
            @$dateData = $result['dateData'];
            @$dateIssue = $result['dateIssue'];
            @$acount = $result['acount'];
            @$Total_amount = $result['amount'];
            @$Total_thamount = $result['thamount'];
            @$STMdat = $result['STMdat'];
            @$TBills = $result['TBills']['TBill'];
            // TBills
            $bills_       = @$TBills;
                foreach ($bills_ as $value) {
                    $hreg = $value['hreg'];
                    $station = $value['station'];
                    $invno = $value['invno'];
                    $hn = $value['hn'];
                    $amount = $value['amount'];
                    $paid = $value['paid'];
                    $rid = $value['rid'];
                    $HDflag = $value['HDflag'];
                    $dttran = $value['dttran'];
                    $dttranDate = explode("T",$value['dttran']);
                    $dttdate = $dttranDate[0];
                    $dtttime = $dttranDate[1];
                    $checkc = Acc_stm_ti::where('hn', $hn)->where('vstdate', $dttdate)->count();
                    if ( $checkc > 0) {
                        // Acc_stm_ti::where('hn', $hn)->where('vstdate', $dttdate)
                        //     ->update([
                        //         'invno'            => $invno,
                        //         'dttran'           => $dttran,
                        //         'hn'               => $hn,
                        //         'amount'           => $amount,
                        //         'paid'             => $paid,
                        //         'rid'              => $rid,
                        //         'HDflag'           => $HDflag,
                        //         'vstdate'          => $dttdate
                        //     ]);
                        // Acc_stm_ti_total::where('HDBill_hn',$hn)->where('vstdate',$dttdate)
                        //     ->update([
                        //         'invno'                => $invno,
                        //         'HDBill_hn'            => $hn,
                        //         'STMdoc'               => @$STMdoc,
                        //         'vstdate'              => $dttdate,
                        //         'HDBill_TBill_paid'    => $paid,
                        //         'HDBill_TBill_rid'     => $rid,
                        //         'HDBill_TBill_HDflag'  => $HDflag,
                        //         'HDBill_TBill_amount'  => $amount
                        //     ]);
                    } else {
                            Acc_stm_ti::insert([
                                'invno'            => $invno,
                                'dttran'           => $dttran,
                                'hn'               => $hn,
                                'amount'           => $amount,
                                'paid'             => $paid,
                                'rid'              => $rid,
                                'HDflag'           => $HDflag,
                                'vstdate'          => $dttdate
                            ]);
                            Acc_stm_ti_total::insert([
                                'invno'                => $invno,
                                'HDBill_hn'            => $hn,
                                'STMdoc'               => @$STMdoc,
                                'vstdate'              => $dttdate,
                                'HDBill_TBill_paid'    => $paid,
                                'HDBill_TBill_rid'     => $rid,
                                'HDBill_TBill_HDflag'  => $HDflag,
                                'HDBill_TBill_amount'  => $amount,
                                'Total_amount'         => $amount
                            ]);
                    }
                }
                return redirect()->back();
    }
    public function upstm_tixml_sssimport(Request $request)
    {
        // $xml = $request->file;
        // $reader = XmlReader::fromString($xml);
        // $data_new = $reader->values();
        // $xml = $request->file;
        // $xmlString = file_get_contents(($tar_file_));
        // $xmlObject = simplexml_load_string($xmlString);
        // $json = json_encode($xmlObject);
        // $result = json_decode($json, true);
        // $xml = file_get_contents(($tar_file_));
        // $xml = XmlParser::load($tar_file_);
        // dd($data_new);
        // $xml2 = XmlParser::load($xml);
        // $json = json_encode($xml);
        // $result = json_decode($json, true);
        // dd($xml);
        // $tiofc = $xml->parse([
        //     'stmAccountID'     => ['tiofc' => 'tiofc.stmAccountID'],
        //     'hcode'            => ['tiofc' => 'tiofc.hcode'],
        //     'AccPeriod'        => ['tiofc' => 'tiofc::AccPeriod'],
        // ]);
        // $app = new Container();
        // $document = new Document($app);
        // $stub = new Reader($document);
        // $output = $stub->extract($xml);
        // $this->assertInstanceOf('\Orchestra\Parser\Xml\Document', $xml);
        // dd($xml);

        // ********************************************************************

            $tar_file_ = $request->file;

            $file_ = $request->file('file')->getClientOriginalName(); //ชื่อไฟล์ + นามสกุล
            $filename = pathinfo($file_, PATHINFO_FILENAME);  //ชื่อไฟล์ เพียวๆ
            $extension = pathinfo($file_, PATHINFO_EXTENSION);
            $xmlString = file_get_contents(($tar_file_));
            // $xmlString = file_get_contents(($file_));

            $xmlObject = simplexml_load_string($xmlString);
            $json = json_encode($xmlObject);
            $result = json_decode($json, true);

            // dd($result);

            @$stmAccountID = $result['stmAccountID'];
            @$hcode = $result['hcode'];
            @$hname = $result['hname'];
            @$AccPeriod = $result['AccPeriod'];
            @$STMdoc = $result['STMdoc'];
            @$dateStart = $result['dateStart'];
            @$dateEnd = $result['dateEnd'];
            @$dateData = $result['dateData'];
            // @$datedue = $result['datedue'];
            @$dateIssue = $result['dateIssue'];
            @$amount = $result['amount'];
            @$thamount = $result['thamount'];
            // dd(@$STMdoc);
            // @$dateStart = $result['dateStart'];
            // @$dateEnd = $result['dateEnd'];
            // @$dateData = $result['dateData'];
            // @$dateIssue = $result['dateIssue'];
            // @$acount = $result['acount'];
            // @$amount = $result['amount'];
            // @$thamount = $result['thamount'];
            // @$STMdat = $result['STMdat'];
            // @$HDBills = $result['HDBills'];

            @$STMdat   = $result['STMdat'];   // รวมยา epo
            // @$HDBill_ = $result['HDBill'];
            @$HDBills  = $result['HDBills'];
            @$TBill    = $result['HDBills']['HDBill'];
            // @$EPO      = $result['HDBills']['HDBill'];
            // @$TBills = $result['TBills']['TBill'];
            @$HDBillsTBill = $result['HDBills']['HDBill'];
            @$TBills       = $result['TBills']['TBill'];

            // dd(@$HDBillsTBill);
            $checkchead = Acc_stm_ti_totalhead::where('AccPeriod', @$AccPeriod)->count();
            if ($checkchead > 0) {
            } else {
                Acc_stm_ti_totalhead::insert([
                    'stmAccountID'    => @$stmAccountID,
                    'hcode'           => @$hcode,
                    'hname'           => @$hname,
                    'AccPeriod'       => @$AccPeriod,
                    'STMdoc'          => @$STMdoc,
                    'dateStart'       => @$dateStart,
                    'dateEnd'         => @$dateEnd,
                    'dateData'        => @$dateData,
                    'dateIssue'       => @$dateIssue,
                    'amount'          => @$amount,
                    'thamount'        => @$thamount
                ]);
            }
            $data_ = DB::table('acc_stm_ti_totalhead')->where('AccPeriod','=',@$AccPeriod)->first();
            $totalhead_id  = $data_->acc_stm_ti_totalhead_id;

            // dd(@$HDBillsTBill);
            $HDBillsTBill       = @$HDBillsTBill;
            $HDBillss           = @$HDBills;
            foreach ($HDBillsTBill as $key => $value) {

                    if (isset($value['EPO']['effHDs'])) {
                        $data_epo_hds   = $value['EPO']['effHDs'];
                    } else {
                        $data_epo_hds   = '';
                    }
                    if (isset($value['EPO']['effHCT'])) {
                        $data_epo_hct   = $value['EPO']['effHCT'];
                    } else {
                        $data_epo_hct   = '';
                    }
                    if (isset($value['EPO']['epoPay'])) {
                        $data_epo_pay   = $value['EPO']['epoPay'];
                    } else {
                        $data_epo_pay   = '';
                    }
                    if (isset($value['EPO']['epoAdm'])) {
                        $data_epo_adm   = $value['EPO']['epoAdm'];
                    } else {
                        $data_epo_adm   = '';
                    }

                    if (isset($value['pid'])) {
                        $data_cid   = $value['pid'];
                    } else {
                        $data_cid   = '';
                    }
                    $check_s   =  Acc_stm_ti_total::where('HDBill_pid',$value['pid'])->where('HDBill_wkno',$value['wkno'])->count();
                    if ($check_s < 1) {
                        // Acc_stm_ti_total::insert([
                        //     'acc_stm_ti_totalhead_id'    => $totalhead_id,
                        //     'HDBill_hreg'                => $value['hreg'],
                        //     'HDBill_hn'                  => $value['hn'],
                        //     'HDBill_name'                => $value['name'],
                        //     'HDBill_pid'                 => $value['pid'],
                        //     'HDBill_wkno'                => $value['wkno'],
                        //     'HDBill_hds'                 => $value['hds'],
                        //     'HDBill_quota'               => $value['quota'],
                        //     'HDBill_hdcharge'            => $value['hdcharge'],
                        //     'HDBill_payable'             => $value['payable'],
                        //     'HDBill_outstanding'         => $value['outstanding'],

                        //     'HDBill_EPO_effHDs'          => $data_epo_hds,
                        //     'HDBill_EPO_effHCT'          => $data_epo_hct,
                        //     'HDBill_EPO_epoPay'          => $data_epo_pay,
                        //     'HDBill_EPO_epoAdm'          => $data_epo_adm,
                        //     // 'invno'                      => $invno,
                        //     'STMdoc'                     => $filename
                        // ]);

                        $data_2                       = $value['TBill'];
                        // dd($data_2);
                        foreach ($data_2 as $value2) {

                                    if (isset($value2['hcode'])) {
                                        $hcode   = $value2['hcode'];
                                    } else {
                                        $hcode   = '';
                                    }
                                    if (isset($value2['station'])) {
                                        $station   = $value2['station'];
                                    } else {
                                        $station   = '';
                                    }
                                    if (isset($value2['hreg'])) {
                                        $hreg   = $value2['hreg'];
                                    } else {
                                        $hreg   = '';
                                    }
                                    if (isset($value2['wkno'])) {
                                        $wkno   = $value2['wkno'];
                                    } else {
                                        $wkno   = '';
                                    }
                                    if (isset($value2['hn'])) {
                                        $hn   = $value2['hn'];
                                    } else {
                                        $hn   = '';
                                    }
                                    if (isset($value2['invno'])) {
                                        $invno   = $value2['invno'];
                                    } else {
                                        $invno   = '';
                                    }
                                    if (isset($value2['amount'])) {
                                        $amount   = $value2['amount'];
                                    } else {
                                        $amount   = '';
                                    }
                                    if (isset($value2['paid'])) {
                                        $paid   = $value2['paid'];
                                    } else {
                                        $paid   = '';
                                    }
                                    if (isset($value2['rid'])) {
                                        $rid   = $value2['rid'];
                                    } else {
                                        $rid   = '';
                                    }
                                    if (isset($value2['hdrate'])) {
                                        $hdrate   = $value2['hdrate'];
                                    } else {
                                        $hdrate   = '';
                                    }
                                    if (isset($value2['hdcharge'])) {
                                        $hdcharge   = $value2['hdcharge'];
                                    } else {
                                        $hdrate   = '';
                                    }
                                    if (isset($value2['HDflag'])) {
                                        $HDflag   = $value2['HDflag'];
                                    } else {
                                        $HDflag   = '';
                                    }
                                    if (isset($value2['hdrate'])) {
                                        $hdrate   = $value2['hdrate'];
                                    } else {
                                        $hdrate   = '';
                                    }
                                    if (isset($value2['accp'])) {
                                        $accp   = $value2['accp'];
                                    } else {
                                        $accp   = '';
                                    }
                                    if (isset($value2['HDflag'])) {
                                        $HDflag   = $value2['HDflag'];
                                    } else {
                                        $HDflag   = '';
                                    }
                                    if (isset($value2['dttran'])) {
                                        $dttran   = $value2['dttran'];
                                        $dttranDate = explode("T",$value2['dttran']);
                                        $dttdate    = $dttranDate[0];
                                        $dtttime    = $dttranDate[1];
                                    } else {
                                        $hdrate       = '';
                                        $dttranDate   = '';
                                        $dttdate      = '';
                                    }


                                    if (isset($value2['EPOs']['EPOpay'])) {
                                        $EPOpay   = $value2['EPOs']['EPOpay'];
                                    } else {
                                        $EPOpay   = '';
                                    }
                                    if (isset($value2['EPOs']['EPOadm'])) {
                                        $EPOadm   = $value2['EPOs']['EPOadm'];
                                    } else {
                                        $EPOadm   = '';
                                    }
                                    // if (isset($value2['EPOpay'])) {
                                    //     $EPO_tt = $value2['EPOpay'];
                                    //     // dd($EPO_tt);
                                    //     $Total = ((int)$amount) + ((int)$EPO_tt);
                                    // } elseif (isset($value2['EPOs']['EPO']['epoPay'])){
                                    //     $EPO_tt = $value2['EPOs']['EPO']['epoPay'];
                                    //     $Total = ((int)$amount) + ((int)$EPO_tt);
                                    // } else {
                                    //     $EPO_tt = '';
                                    //     $Total = $amount;
                                    // }

                                    if ($wkno != '') {
                                        $check_sub = Acc_stm_ti_totalsub::where('HDBill_TBill_invno',$invno)->count();
                                        if ($check_sub > 0) {
                                            # code...
                                        } else {
                                            Acc_stm_ti_totalsub::insert([
                                                'wkno'                       => $value['wkno'],
                                                'HDBill_TBill_hcode'         => $hcode,
                                                'HDBill_TBill_station'       => $station,
                                                'HDBill_TBill_wkno'          => $wkno,
                                                'HDBill_TBill_hreg'          => $hreg,
                                                'HDBill_TBill_hn'            => $hn,
                                                'HDBill_TBill_invno'         => $invno,
                                                'HDBill_TBill_dttran'        => $dttdate,
                                                'HDBill_TBill_hdrate'        => $hdrate,
                                                'HDBill_TBill_hdcharge'      => $hdcharge,
                                                'HDBill_TBill_amount'        => $amount,
                                                'HDBill_TBill_paid'          => $paid,
                                                'HDBill_TBill_rid'           => $rid,
                                                'HDBill_TBill_accp'          => $accp,
                                                'HDBill_TBill_HDflag'        => $HDflag,
                                                // 'HDBill_TBill_totalamount'   => ((int)$amount) + ((int)$EPOpay),
                                                'HDBill_TBill_totalamount'   => ((int)$amount) + ((int)$EPOpay)+ ((int)$EPOadm),
                                                'STMdoc'                     => @$STMdoc,
                                            ]);
                                        }
                                    } else {
                                    }

                                    Acc_1102050101_3099::where('cid',$data_cid)->where('vstdate',$dttdate)
                                    ->update([
                                        'status'            => 'Y',
                                        'stm_money'         => ((int)$amount) + ((int)$EPOpay) + ((int)$EPOadm),
                                        'stm_trainid'       => $invno,
                                        'stm_total'         => ((int)$amount) + ((int)$EPOpay)+ ((int)$EPOadm),
                                        'STMdoc'            => $filename,
                                    ]);


                        }

                        Acc_stm_ti_total::insert([
                            'acc_stm_ti_totalhead_id'    => $totalhead_id,
                            'HDBill_hreg'                => $value['hreg'],
                            'HDBill_hn'                  => $value['hn'],
                            'HDBill_name'                => $value['name'],
                            'HDBill_pid'                 => $value['pid'],
                            'HDBill_wkno'                => $value['wkno'],
                            'HDBill_hds'                 => $value['hds'],
                            'HDBill_quota'               => $value['quota'],
                            'HDBill_hdcharge'            => $value['hdcharge'],
                            'HDBill_payable'             => $value['payable'],
                            'HDBill_outstanding'         => $value['outstanding'],
                            'HDBill_EPO_effHDs'          => $data_epo_hds,
                            'HDBill_EPO_effHCT'          => $data_epo_hct,
                            'HDBill_EPO_epoPay'          => $data_epo_pay,
                            'HDBill_EPO_epoAdm'          => $data_epo_adm,

                            'vstdate'                    => $dttdate,
                            'HDBill_TBill_HDflag'        => $HDflag,
                            'invno'                      => $invno,
                            'STMdoc'                     => $filename
                        ]);

                        if (isset($value['payable'])) {
                            $payable   = $value['payable'];
                        } else {
                            $payable   = '';
                        }
                        if (isset($data_epo_pay)) {
                            $dataepopay   = $data_epo_pay;
                        } else {
                            $dataepopay   = '';
                        }
                        if (isset($data_epo_adm)) {
                            $dataepoadm   = $data_epo_adm;
                        } else {
                            $dataepoadm   = '';
                        }

                        Acc_1102050101_3099::where('cid',$data_cid)->where('vstdate',$dttdate)
                        ->update([
                            'status'            => 'Y',
                            'stm_money'         => ((int)$amount) + ((int)$EPOpay) + ((int)$EPOadm),
                            'stm_trainid'       => $invno,
                            'stm_total'         => ((int)$amount) + ((int)$EPOpay)+ ((int)$EPOadm),
                            'STMdoc'            => $filename,
                        ]);

                    } else {
                        $data_2                       = $value['TBill'];
                        // dd($data_2);
                        foreach ($data_2 as $value2) {

                                    if (isset($value2['hcode'])) {
                                        $hcode   = $value2['hcode'];
                                    } else {
                                        $hcode   = '';
                                    }
                                    if (isset($value2['station'])) {
                                        $station   = $value2['station'];
                                    } else {
                                        $station   = '';
                                    }
                                    if (isset($value2['hreg'])) {
                                        $hreg   = $value2['hreg'];
                                    } else {
                                        $hreg   = '';
                                    }
                                    if (isset($value2['wkno'])) {
                                        $wkno   = $value2['wkno'];
                                    } else {
                                        $wkno   = '';
                                    }
                                    if (isset($value2['hn'])) {
                                        $hn   = $value2['hn'];
                                    } else {
                                        $hn   = '';
                                    }
                                    if (isset($value2['invno'])) {
                                        $invno   = $value2['invno'];
                                    } else {
                                        $invno   = '';
                                    }
                                    if (isset($value2['amount'])) {
                                        $amount   = $value2['amount'];
                                    } else {
                                        $amount   = '';
                                    }
                                    if (isset($value2['paid'])) {
                                        $paid   = $value2['paid'];
                                    } else {
                                        $paid   = '';
                                    }
                                    if (isset($value2['rid'])) {
                                        $rid   = $value2['rid'];
                                    } else {
                                        $rid   = '';
                                    }
                                    if (isset($value2['hdrate'])) {
                                        $hdrate   = $value2['hdrate'];
                                    } else {
                                        $hdrate   = '';
                                    }
                                    if (isset($value2['hdcharge'])) {
                                        $hdcharge   = $value2['hdcharge'];
                                    } else {
                                        $hdrate   = '';
                                    }
                                    if (isset($value2['HDflag'])) {
                                        $HDflag   = $value2['HDflag'];
                                    } else {
                                        $HDflag   = '';
                                    }
                                    if (isset($value2['hdrate'])) {
                                        $hdrate   = $value2['hdrate'];
                                    } else {
                                        $hdrate   = '';
                                    }
                                    if (isset($value2['accp'])) {
                                        $accp   = $value2['accp'];
                                    } else {
                                        $accp   = '';
                                    }
                                    if (isset($value2['HDflag'])) {
                                        $HDflag   = $value2['HDflag'];
                                    } else {
                                        $HDflag   = '';
                                    }
                                    if (isset($value2['dttran'])) {
                                        $dttran   = $value2['dttran'];
                                        $dttranDate = explode("T",$value2['dttran']);
                                        $dttdate    = $dttranDate[0];
                                        $dtttime    = $dttranDate[1];
                                    } else {
                                        $hdrate       = '';
                                        $dttranDate   = '';
                                        $dttdate      = '';
                                    }


                                    if (isset($value2['EPOs']['EPOpay'])) {
                                        $EPOpay   = $value2['EPOs']['EPOpay'];
                                    } else {
                                        $EPOpay   = '';
                                    }
                                    if (isset($value2['EPOs']['EPOadm'])) {
                                        $EPOadm   = $value2['EPOs']['EPOadm'];
                                    } else {
                                        $EPOadm   = '';
                                    }

                                    Acc_1102050101_3099::where('cid',$data_cid)->where('vstdate',$dttdate)
                                    ->update([
                                        'status'            => 'Y',
                                        'stm_money'         => ((int)$amount) + ((int)$EPOpay) + ((int)$EPOadm),
                                        'stm_trainid'       => $invno,
                                        'stm_total'         => ((int)$amount) + ((int)$EPOpay)+ ((int)$EPOadm),
                                        'STMdoc'            => $filename,
                                    ]);


                        }

                    }



            }

            return response()->json([
                'status'    => '200',
            ]);

    }
    public function upstm_tixml_sss(Request $request)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->startdate;
        $enddate = $request->enddate;

        return view('account_pk.upstm_tixml_sss',[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
        ]);
    }
    public function upstm_tixml_sssimport_old_ok(Request $request)
    {
            $tar_file_ = $request->file;
            $file_ = $request->file('file')->getClientOriginalName(); //ชื่อไฟล์

            // dd($file_);

            $filename = pathinfo($file_, PATHINFO_FILENAME);
            $extension = pathinfo($file_, PATHINFO_EXTENSION);
            $xmlString = file_get_contents(($tar_file_));
            $xmlObject = simplexml_load_string($xmlString);
            $json = json_encode($xmlObject);
            $result = json_decode($json, true);

            // dd($result);
            @$stmAccountID = $result['stmAccountID'];
            @$hcode = $result['hcode'];
            @$hname = $result['hname'];
            @$AccPeriod = $result['AccPeriod'];
            @$STMdoc = $result['STMdoc'];
            @$dateStart = $result['dateStart'];
            @$dateEnd = $result['dateEnd'];
            @$dateData = $result['dateData'];
            @$dateIssue = $result['dateIssue'];
            @$acount = $result['acount'];
            @$amount = $result['amount'];
            @$thamount = $result['thamount'];
            @$STMdat = $result['STMdat'];
            @$HDBills = $result['HDBills'];
            // @$TBills = $result['HDBills']['HDBill']['TBill']; //sss

            $checkchead = Acc_stm_ti_totalhead::where('stmAccountID', @$stmAccountID)->where('AccPeriod', @$AccPeriod)->count();
            if ($checkchead > 0) {
                # code...
            } else {
                Acc_stm_ti_totalhead::insert([
                    'stmAccountID'    => @$stmAccountID,
                    'hcode'           => @$hcode,
                    'hname'           => @$hname,
                    'AccPeriod'       => @$AccPeriod,
                    'STMdoc'          => @$STMdoc,
                    'dateStart'       => @$dateStart,
                    'dateEnd'         => @$dateEnd,
                    'dateData'         => @$dateData,
                    'dateIssue'       => @$dateIssue,
                    'acount'          => @$acount,
                    'amount'          => @$amount,
                    'thamount'        => @$thamount
                ]);
            }
            $bills_       = @$HDBills;
            // dd($bills_ );
                $tbill_ = $bills_['HDBill'];
                // dd($tbill_ );
                foreach ($tbill_ as $key => $value) {
                    $fullname     = $value['name'];
                    $cid          = $value['pid'];

                    $tbill        = $value['TBill'];
                    // dd($tbill);
                    foreach ($tbill as $value2) {
                            // $hcode      = $value2['hreg'];
                            if (isset($value2['hreg'])) {
                                $hcode   = $value2['hreg'];
                            } else {
                                $hcode   = '';
                            }
                            if (isset($value2['station'])) {
                                $station   = $value2['station'];
                            } else {
                                $station   = '';
                            }
                            if (isset($value2['hn'])) {
                                $hn   = $value2['hn'];
                            } else {
                                $hn   = '';
                            }
                            if (isset($value2['invno'])) {
                                $invno   = $value2['invno'];
                            } else {
                                $invno   = '';
                            }
                            if (isset($value2['amount'])) {
                                $amount   = $value2['amount'];
                            } else {
                                $amount   = '';
                            }
                            if (isset($value2['paid'])) {
                                $paid   = $value2['paid'];
                            } else {
                                $paid   = '';
                            }
                            if (isset($value2['rid'])) {
                                $rid   = $value2['rid'];
                            } else {
                                $rid   = '';
                            }
                            if (isset($value2['hdrate'])) {
                                $hdrate   = $value2['hdrate'];
                            } else {
                                $hdrate   = '';
                            }
                            if (isset($value2['hdcharge'])) {
                                $hdcharge   = $value2['hdcharge'];
                            } else {
                                $hdrate   = '';
                            }
                            if (isset($value2['HDflag'])) {
                                $HDflag   = $value2['HDflag'];
                            } else {
                                $HDflag   = '';
                            }
                            if (isset($value2['hdrate'])) {
                                $hdrate   = $value2['hdrate'];
                            } else {
                                $hdrate   = '';
                            }
                            if (isset($value2['dttran'])) {
                                $dttran   = $value2['dttran'];
                                $dttranDate = explode("T",$value2['dttran']);
                                $dttdate    = $dttranDate[0];
                                $dtttime    = $dttranDate[1];

                            } else {
                                $hdrate       = '';
                                $dttranDate   = '';
                                $dttdate      = '';
                            }


                            // dd($hcode );
                            // $station    = $value2['station'];
                            // $hn         = $value2['hn'];
                            // $invno      = $value2['invno'];
                            // $amount     = $value2['amount'];
                            // $paid       = $value2['paid'];
                            // dd($paid );
                            // $rid        = $value2['rid'];
                            // $hdrate     = $value2['hdrate'];
                            // $hdcharge   = $value2['hdcharge'];
                            // $HDflag     = $value2['HDflag'];
                            // $dttran     = $value2['dttran'];
                            // $dttranDate = explode("T",$value2['dttran']);
                            // $dttdate    = $dttranDate[0];
                            // $dtttime    = $dttranDate[1];
                            // dd($value2['EPOpay']);
                            if (isset($value2['EPOpay'])) {
                                $EPO_tt = $value2['EPOpay'];
                                // dd($EPO_tt);
                                $Total = ((int)$amount) + ((int)$EPO_tt);
                            } elseif (isset($value2['EPOs']['EPO']['epoPay'])){
                                $EPO_tt = $value2['EPOs']['EPO']['epoPay'];
                                $Total = ((int)$amount) + ((int)$EPO_tt);
                            } else {
                                $EPO_tt = '';
                                $Total = $amount;
                            }

                            $checkc     = Acc_stm_ti_total::where('hn', $hn)->where('vstdate', $dttdate)->count();
                            $datenow = date('Y-m-d');
                            if ( $checkc > 0) {
                                Acc_stm_ti_total::where('hn',$hn)->where('vstdate',$dttdate)
                                    ->update([
                                        'station'           => $station,
                                        'invno'             => $invno,
                                        'hn'                => $hn,
                                        'STMdoc'            => @$STMdoc,
                                        'dttran'            => $dttran,
                                        'vstdate'           => $dttdate,
                                        'paid'              => $paid,
                                        'rid'               => $rid,
                                        'cid'               => $cid,
                                        'fullname'          => $fullname,
                                        'EPOpay'            => $EPO_tt,
                                        // 'EPOpay'            => $EPOpay,
                                        'hdrate'            => $hdrate,
                                        'hdcharge'          => $hdcharge,
                                        'amount'            => $amount,
                                        'HDflag'            => $HDflag,
                                        'Total_amount'      => $Total,
                                        'date_save'         => $datenow
                                    ]);
                                    Acc_1102050101_3099::where('hn',$hn)->where('vstdate',$dttdate)
                                    ->update([
                                        'status'            => 'Y',
                                        'stm_money'       => $Total,
                                        'stm_trainid'     => $invno,
                                        'stm_total'       => $Total,
                                        'STMdoc'          => @$STMdoc,
                                    ]);
                            } else {
                                Acc_stm_ti_total::insert([
                                    'station'           => $station,
                                    'invno'             => $invno,
                                    'hn'                => $hn,
                                    'STMdoc'            => @$STMdoc,
                                    'dttran'            => $dttran,
                                    'vstdate'           => $dttdate,
                                    'paid'              => $paid,
                                    'rid'               => $rid,
                                    'cid'               => $cid,
                                    'fullname'          => $fullname,
                                    'EPOpay'            => $EPO_tt,
                                    // 'EPOpay'            => $EPOpay,
                                    'hdrate'            => $hdrate,
                                    'hdcharge'          => $hdcharge,
                                    'amount'            => $amount,
                                    'HDflag'            => $HDflag,
                                    'Total_amount'      => $Total,
                                    'date_save'         => $datenow
                                ]);
                                Acc_1102050101_3099::where('hn',$hn)->where('vstdate',$dttdate)
                                ->update([
                                    'status'            => 'Y',
                                    'stm_money'       => $Total,
                                    'stm_trainid'     => $invno,
                                    'stm_total'       => $Total,
                                    'STMdoc'          => @$STMdoc,
                                ]);
                            }
                    }
                }
                // return redirect()->back();
                return response()->json([
                    'status'    => '200',

                ]);
    }
    // 'success'   => 'Successfully uploaded.'
    public function upstm_sss_xml(Request $request)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->startdate;
        $enddate = $request->enddate;

        return view('account_pk.upstm_sss_xml',[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
        ]);
    }

    public function acc_setting(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql3')->select('
            SELECT pt.pttype_acc_id,pt.pttype_acc_code,pt.pttype_acc_name as ptname
            ,pt.pttype_acc_eclaimid,e.name as eclaimname
            ,e.ar_opd,e.ar_ipd
            from pttype_acc pt
            left join pttype_eclaim e on e.code = pt.pttype_acc_eclaimid
        ');
        // ,pt.pcode,pt.paidst,pt.hipdata_code,pt.max_debt_money,pt.nhso_code
        $aropd = Pttype_eclaim::where('pttype_eclaim.ar_opd','<>',NULL)->groupBy('pttype_eclaim.ar_opd')->get();
        $aripd = Pttype_eclaim::where('pttype_eclaim.ar_ipd','<>',NULL)->groupBy('pttype_eclaim.ar_ipd')->get();
        // left join pttype_eclaim e on e.code = ptt.pttype_eclaim_id
         return view('account_pk.acc_setting',[
            'datashow'      =>     $datashow,
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'aropd'         =>     $aropd,
            'aripd'         =>     $aripd,
         ]);
    }
    public function acc_setting_edit(Request $request,$id)
    {
        $type = Pttype_acc::find($id);

        return response()->json([
            'status'     => '200',
            'type'       =>  $type,
        ]);
    }
    public function acc_setting_update(Request $request)
    {
        $accid = $request->input('acc_id');
        $code = $request->input('ar_opd');

        $update = pttype_acc::find($accid);
        $update->pttype_acc_eclaimid = $code;
        $update->save();

        return response()->json([
            'status'     => '200',
        ]);
    }
    public function acc_setting_save(Request $request)
    {
        $accid = $request->input('insertpttype');
        $code = $request->input('insertar_opd');

        $add = pttype_acc::find($accid);
        $add->pttype_acc_eclaimid = $code;
        $add->save();

        return response()->json([
            'status'     => '200',
        ]);
    }

    public function aset_trimart(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('SELECT * from acc_trimart');
        $data['trimart'] = DB::table('acc_trimart')->get();
        $data['acc_trimart_liss'] = DB::table('acc_trimart_liss')->get();
         return view('account_pk.aset_trimart',$data,[
            'datashow'      =>     $datashow,
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
         ]);
    }
    public function aset_trimart_save(Request $request)
    {
        $name_ = Acc_trimart::where('acc_trimart_code',$request->acc_trimart_start)->where('active','=','Y')->first();
        $add = new Acc_trimart();
        $add->acc_trimart_code        = $name_->acc_trimart_code;
        $add->acc_trimart_name        = $name_->acc_trimart_name;
        $add->acc_trimart_start_date  = $request->input('acc_trimart_start_date');
        $add->acc_trimart_end_date    = $request->input('acc_trimart_end_date');
        $add->save();

        return response()->json([
            'status'     => '200',
        ]);
    }

    public function aset_trimart_edit(Request $request,$id)
    {
        $data_show = Acc_trimart::find($id);
        return response()->json([
            'status'               => '200',
            'data_show'            =>  $data_show,
        ]);
    }

    public function aset_trimart_update(Request $request)
    {
        $name_ = Acc_trimart::where('acc_trimart_code',$request->acc_trimart_code)->where('active','=','Y')->first();
        $id = $request->input('acc_trimart_id');

        $update = Acc_trimart::find($id);
        $update->acc_trimart_code       = $name_->acc_trimart_code;
        $update->acc_trimart_name       = $name_->acc_trimart_name;
        $update->acc_trimart_start_date = $request->input('acc_trimart_start_date');
        $update->acc_trimart_end_date   = $request->input('acc_trimart_end_date');
        $update->save();

        return response()->json([
            'status'     => '200',
        ]);
    }

    public function upstm_sss_excel(Request $request)
    {
        $datenow = date('Y-m-d');
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $datashow = DB::connection('mysql')->select('SELECT * FROM Acc_stm_sssexcel');
        $countc = DB::table('Acc_stm_sssexcel')->count();
        return view('account_pk.upstm_sss_excel',[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate,
            'datashow'      =>     $datashow,
            'countc'        =>     $countc
        ]);
    }
    function upstm_sss_excelsave(Request $request)
    {
        $the_file_ = $request->file('file_stm');

            try{
                // Cheet 1
                $spreadsheet = IOFactory::load($the_file_->getRealPath());
                $sheet        = $spreadsheet->setActiveSheetIndex(0);
                $row_limit    = $sheet->getHighestDataRow();
                $row_range    = range( '10', $row_limit );
                $startcount = '10';
                $data = array();
                foreach ($row_range as $row ) {
                    $data[] = [
                        'vn'                   =>$sheet->getCell( 'A' . $row )->getValue(),
                        'an'                   =>$sheet->getCell( 'B' . $row )->getValue(),
                        'hn'                   =>$sheet->getCell( 'C' . $row )->getValue(),
                        'cid'                  =>$sheet->getCell( 'D' . $row )->getValue(),
                        'ptname'               =>$sheet->getCell( 'E' . $row )->getValue(),
                        'vstdate'              =>$sheet->getCell( 'F' . $row )->getValue(),
                        'dchdate'              =>$sheet->getCell( 'G' . $row )->getValue(),
                        'pttype'               =>$sheet->getCell( 'H' . $row )->getValue(),
                        'nhso_docno'           =>$sheet->getCell( 'I' . $row )->getValue(),
                        'hospmain'             =>$sheet->getCell( 'J' . $row )->getValue(),
                        'income'               =>$sheet->getCell( 'K' . $row )->getValue(),
                        'claim'                =>$sheet->getCell( 'L' . $row )->getValue(),
                        'debit'                =>$sheet->getCell( 'M' . $row )->getValue(),
                        'stm'                  =>$sheet->getCell( 'N' . $row )->getValue(),
                        'difference'           =>$sheet->getCell( 'O' . $row )->getValue(),
                        'stm_no'               =>$sheet->getCell( 'P' . $row )->getValue(),
                        'date_save'            =>$sheet->getCell( 'Q' . $row )->getValue()
                    ];
                    $startcount++;
                }
                $for_insert = array_chunk($data, length:5000);
                foreach ($for_insert as $key => $data_) {
                    Acc_stm_sssexcel::insert($data_);
                }
            } catch (Exception $e) {
                $error_code = $e->errorInfo[1];
                return back()->withErrors('There was a problem uploading the data!');
            }
            return response()->json([
            'status'    => '200',
        ]);
    }
    public function upstm_sss_excelsend(Request $request)
    {
        try{
            $data_ = DB::connection('mysql')->select('SELECT * FROM acc_stm_sssexcel where an <>"" AND stm <>""');
            foreach ($data_ as $key => $value) {
                if ($value->an != '') {
                    $check = Acc_stm_sssnew::where('an','=',$value->an)->count();
                    if ($check > 0) {
                    } else {
                        $add = new Acc_stm_sssnew();
                        $add->vn            = $value->vn;
                        $add->an            = $value->an;
                        $add->hn            = $value->hn;
                        $add->cid           = $value->cid;
                        $add->ptname        = $value->ptname;
                        $add->vstdate       = $value->vstdate;
                        $add->dchdate       = $value->dchdate;
                        $add->vstdate       = $value->vstdate;
                        $add->dchdate       = $value->dchdate;
                        $add->pttype        = $value->pttype;
                        $add->nhso_docno    = $value->nhso_docno;
                        $add->hospmain      = $value->hospmain;
                        $add->income        = $value->income;
                        $add->claim         = $value->claim;
                        $add->debit         = $value->debit;
                        $add->stm           = $value->stm;
                        $add->difference    = $value->difference;
                        $add->stm_no        = $value->stm_no;
                        $add->date_save     = $value->date_save;
                        $add->save();
                    }
                }

                Acc_1102050101_304::where('an',$value->an)->update([
                    'stm'          => $value->stm,
                    'difference'   => $value->difference,
                    'stm_no'       => $value->stm_no,
                    'date_save'    => $value->date_save,
                ]);
            }
            } catch (Exception $e) {
                $error_code = $e->errorInfo[1];
                return back()->withErrors('There was a problem uploading the data!');
            }
            Acc_stm_sssexcel::truncate();
        return redirect()->back();
    }
    public function upstm_sss_excelsend307(Request $request)
    {
            try{
                $data_opd = DB::connection('mysql')->select(
                    'SELECT a.*
                    -- a.vn,a.debit,a.stm,a.difference,a.stm_no,a.date_save,b.vn as vn2
                        FROM acc_stm_sssexcel a
                        LEFT JOIN acc_1102050101_307 b ON b.vn = a.vn
                        WHERE a.vn IS NOT NULL AND a.stm IS NOT NULL
                    ');
                foreach ($data_opd as $key => $value) {
                    if ($value->vn != '') {
                        $check = Acc_stm_sssnew::where('vn','=',$value->vn)->count();
                        if ($check > 0) {
                        } else {
                            $add = new Acc_stm_sssnew();
                            $add->vn            = $value->vn;
                            $add->an            = $value->an;
                            $add->hn            = $value->hn;
                            $add->cid           = $value->cid;
                            $add->ptname        = $value->ptname;
                            $add->vstdate       = $value->vstdate;
                            $add->dchdate       = $value->dchdate;
                            $add->vstdate       = $value->vstdate;
                            $add->dchdate       = $value->dchdate;
                            $add->pttype        = $value->pttype;
                            $add->nhso_docno    = $value->nhso_docno;
                            $add->hospmain      = $value->hospmain;
                            $add->income        = $value->income;
                            $add->claim         = $value->claim;
                            $add->debit         = $value->debit;
                            $add->stm           = $value->stm;
                            $add->difference    = $value->difference;
                            $add->stm_no        = $value->stm_no;
                            $add->date_save     = $value->date_save;
                            $add->save();
                        }
                    }

                    Acc_1102050101_307::where('vn',$value->vn)->update([
                        'stm'          => $value->stm,
                        'difference'   => $value->difference,
                        'stm_no'       => $value->stm_no,
                        'date_save'    => $value->date_save,
                    ]);
                }


            } catch (Exception $e) {
                $error_code = $e->errorInfo[1];
                return back()->withErrors('There was a problem uploading the data!');
            }

            try{
                $data_ipd = DB::connection('mysql')->select(
                    'SELECT a.*
                    -- a.an,a.debit,a.stm,a.difference,a.stm_no,a.date_save,b.an as an2
                        FROM acc_stm_sssexcel a
                        LEFT JOIN acc_1102050101_307 b ON b.an = a.an
                        WHERE a.an IS NOT NULL AND a.stm IS NOT NULL
                    ');
                foreach ($data_ipd as $key => $value2) {
                    if ($value2->an != '') {
                        $check2 = Acc_stm_sssnew::where('an','=',$value2->an)->count();
                        if ($check2 > 0) {
                        } else {
                            $add2 = new Acc_stm_sssnew();
                            // $add2->vn            = $value2->vn;
                            $add2->an            = $value2->an;
                            $add2->hn            = $value2->hn;
                            $add2->cid           = $value2->cid;
                            $add2->ptname        = $value2->ptname;
                            $add2->vstdate       = $value2->vstdate;
                            $add2->dchdate       = $value2->dchdate;
                            $add2->vstdate       = $value2->vstdate;
                            $add2->dchdate       = $value2->dchdate;
                            $add2->pttype        = $value2->pttype;
                            $add2->nhso_docno    = $value2->nhso_docno;
                            $add2->hospmain      = $value2->hospmain;
                            $add2->income        = $value2->income;
                            $add2->claim         = $value2->claim;
                            $add2->debit         = $value2->debit;
                            $add2->stm           = $value2->stm;
                            $add2->difference    = $value2->difference;
                            $add2->stm_no        = $value2->stm_no;
                            $add2->date_save     = $value2->date_save;
                            $add2->save();
                        }
                    }

                    Acc_1102050101_307::where('an',$value2->an)->update([
                        'stm'          => $value2->stm,
                        'difference'   => $value2->difference,
                        'stm_no'       => $value2->stm_no,
                        'date_save'    => $value2->date_save,
                    ]);
                }
            } catch (Exception $e) {
                $error_code = $e->errorInfo[1];
                return back()->withErrors('There was a problem uploading the data!');
            }





            Acc_stm_sssexcel::truncate();
        return redirect()->back();
    }
    public function upstm_sss_excelsend308(Request $request)
    {
        try{
            $data_ = DB::connection('mysql')->select('SELECT * FROM acc_stm_sssexcel WHERE an IS NOT NULL AND stm IS NOT NULL');
            foreach ($data_ as $key => $value) {
                if ($value->an != '') {
                    $check = Acc_stm_sssnew::where('an','=',$value->an)->count();
                    if ($check > 0) {
                    } else {
                        $add = new Acc_stm_sssnew();
                        $add->vn            = $value->vn;
                        $add->an            = $value->an;
                        $add->hn            = $value->hn;
                        $add->cid           = $value->cid;
                        $add->ptname        = $value->ptname;
                        $add->vstdate       = $value->vstdate;
                        $add->dchdate       = $value->dchdate;
                        $add->vstdate       = $value->vstdate;
                        $add->dchdate       = $value->dchdate;
                        $add->pttype        = $value->pttype;
                        $add->nhso_docno    = $value->nhso_docno;
                        $add->hospmain      = $value->hospmain;
                        $add->income        = $value->income;
                        $add->claim         = $value->claim;
                        $add->debit         = $value->debit;
                        $add->stm           = $value->stm;
                        $add->difference    = $value->difference;
                        $add->stm_no        = $value->stm_no;
                        $add->date_save     = $value->date_save;
                        $add->save();
                    }
                }
                $data_own = Acc_1102050101_308::where('an',$value->an)->first();
                $dif = $data_own->nhso_ownright_pid;

                Acc_1102050101_308::where('an',$value->an)->update([
                    'stm'          => $value->stm,
                    'difference'   => $dif - $value->stm,
                    'stm_no'       => $value->stm_no,
                    'date_save'    => $value->date_save,
                ]);
            }
            } catch (Exception $e) {
                $error_code = $e->errorInfo[1];
                return back()->withErrors('There was a problem uploading the data!');
            }
            Acc_stm_sssexcel::truncate();
        return redirect()->back();
    }
    public function upstm_sss_excelsend309(Request $request)
    {
            try{
                $data_opd = DB::connection('mysql')->select(
                    'SELECT a.*
                    -- a.vn,a.debit,a.stm,a.difference,a.stm_no,a.date_save,b.vn as vn2
                        FROM acc_stm_sssexcel a
                        LEFT JOIN acc_1102050101_309 b ON b.vn = a.vn
                        WHERE a.vn IS NOT NULL AND a.stm IS NOT NULL
                    ');
                foreach ($data_opd as $key => $value) {
                    if ($value->vn != '') {
                        $check = Acc_stm_sssnew::where('vn','=',$value->vn)->count();
                        if ($check > 0) {
                        } else {
                            $add = new Acc_stm_sssnew();
                            $add->vn            = $value->vn;
                            $add->an            = $value->an;
                            $add->hn            = $value->hn;
                            $add->cid           = $value->cid;
                            $add->ptname        = $value->ptname;
                            $add->vstdate       = $value->vstdate;
                            $add->dchdate       = $value->dchdate;
                            $add->vstdate       = $value->vstdate;
                            $add->dchdate       = $value->dchdate;
                            $add->pttype        = $value->pttype;
                            $add->nhso_docno    = $value->nhso_docno;
                            $add->hospmain      = $value->hospmain;
                            $add->income        = $value->income;
                            $add->claim         = $value->claim;
                            $add->debit         = $value->debit;
                            $add->stm           = $value->stm;
                            $add->difference    = $value->difference;
                            $add->stm_no        = $value->stm_no;
                            $add->date_save     = $value->date_save;
                            $add->save();
                        }
                    }

                    Acc_1102050101_309::where('vn',$value->vn)->update([
                        'stm'          => $value->stm,
                        'difference'   => $value->difference,
                        'stm_no'       => $value->stm_no,
                        'date_save'    => $value->date_save,
                    ]);
                }


            } catch (Exception $e) {
                $error_code = $e->errorInfo[1];
                return back()->withErrors('There was a problem uploading the data!');
            }

            try{
                $data_ipd = DB::connection('mysql')->select(
                    'SELECT a.*
                    -- a.an,a.debit,a.stm,a.difference,a.stm_no,a.date_save,b.an as an2
                        FROM acc_stm_sssexcel a
                        LEFT JOIN acc_1102050101_307 b ON b.an = a.an
                        WHERE a.an IS NOT NULL AND a.stm IS NOT NULL
                    ');
                foreach ($data_ipd as $key => $value2) {
                    if ($value2->an != '') {
                        $check2 = Acc_stm_sssnew::where('an','=',$value2->an)->count();
                        if ($check2 > 0) {
                        } else {
                            $add2 = new Acc_stm_sssnew();
                            // $add2->vn            = $value2->vn;
                            $add2->an            = $value2->an;
                            $add2->hn            = $value2->hn;
                            $add2->cid           = $value2->cid;
                            $add2->ptname        = $value2->ptname;
                            $add2->vstdate       = $value2->vstdate;
                            $add2->dchdate       = $value2->dchdate;
                            $add2->vstdate       = $value2->vstdate;
                            $add2->dchdate       = $value2->dchdate;
                            $add2->pttype        = $value2->pttype;
                            $add2->nhso_docno    = $value2->nhso_docno;
                            $add2->hospmain      = $value2->hospmain;
                            $add2->income        = $value2->income;
                            $add2->claim         = $value2->claim;
                            $add2->debit         = $value2->debit;
                            $add2->stm           = $value2->stm;
                            $add2->difference    = $value2->difference;
                            $add2->stm_no        = $value2->stm_no;
                            $add2->date_save     = $value2->date_save;
                            $add2->save();
                        }
                    }

                    Acc_1102050101_307::where('an',$value2->an)->update([
                        'stm'          => $value2->stm,
                        'difference'   => $value2->difference,
                        'stm_no'       => $value2->stm_no,
                        'date_save'    => $value2->date_save,
                    ]);
                }
            } catch (Exception $e) {
                $error_code = $e->errorInfo[1];
                return back()->withErrors('There was a problem uploading the data!');
            }





            Acc_stm_sssexcel::truncate();
        return redirect()->back();
    }



}
