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
use App\Models\Checkup_report;
use App\Models\Checkup_report_temp;
use App\Models\Acc_stm_prb;
use App\Models\Acc_stm_ti_totalhead;
use App\Models\Acc_stm_ti_excel;
use App\Models\Acc_stm_ofc;
use App\Models\acc_stm_ofcexcel;
use App\Models\Acc_stm_lgo;
use App\Models\Acc_stm_lgoexcel;
use App\Models\Check_sit_auto;
use App\Models\Acc_stm_ucs_excel;
use App\Models\Patient;
use App\Models\Checkup_config;
use App\Models\Checkup_lab;
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
use App\Models\Dent_appointment;
use App\Models\Dent_appointment_type;
use App\Models\P4p_workgroupset;
use SplFileObject;
use PHPExcel;
use PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Console\Output\Output;

date_default_timezone_set("Asia/Bangkok");


class CheckupController extends Controller
 {
    // ***************** 301********************************

    public function checkup_main(Request $request)
    {
        $startdate        = $request->startdate;
        $enddate          = $request->enddate;
        $dabudget_year    = DB::table('budget_year')->where('active','=',true)->first();
        $leave_month_year = DB::table('leave_month')->orderBy('MONTH_ID', 'ASC')->get();
        $date             = date('Y-m-d');
        $y                = date('Y') + 543;
        $newweek = date('Y-m-d', strtotime($date . ' -1 week')); //ย้อนหลัง 1 สัปดาห์
        $newDate = date('Y-m-d', strtotime($date . ' -5 months')); //ย้อนหลัง 5 เดือน
        $newyear = date('Y-m-d', strtotime($date . ' -1 year')); //ย้อนหลัง 1 ปี

        $data['users']    = DB::table('users')->get();
        $data_doctor      = DB::connection('mysql10')->select('
            SELECT code,CONCAT(pname,fname," ",lname) dentname
            FROM doctor
            WHERE position_id = "2"
            AND active = "Y"
        ');

        return view('checkup.checkup_main',$data,[
            'startdate'        => $startdate,
            'enddate'          => $enddate,
            'data_doctor'      => $data_doctor,
        ]);
    }

    // public function checkup_report(Request $request)
    // {
    //     $startdate        = $request->startdate;
    //     $enddate          = $request->enddate;
     
    //     $datenow             = date('Y-m-d');
    //     $data['date_now']    = date('Y-m-d');
    //     $dabudget_year    = DB::table('budget_year')->where('active','=',true)->first();
    //     $leave_month_year = DB::table('leave_month')->orderBy('MONTH_ID', 'ASC')->get();
    //     $date             = date('Y-m-d');
    //     $y                = date('Y') + 543;
    //     $newweek = date('Y-m-d', strtotime($date . ' -1 week')); //ย้อนหลัง 1 สัปดาห์
    //     $newDate = date('Y-m-d', strtotime($date . ' -5 months')); //ย้อนหลัง 5 เดือน
    //     $newyear = date('Y-m-d', strtotime($date . ' -1 year')); //ย้อนหลัง 1 ปี

    //     $data['users']    = DB::table('users')->get();
    //     $data['hn'] = DB::connection('mysql10')->select('SELECT hn,CONCAT(pname,fname," ",lname) as ptname FROM patient GROUP BY hn limit 1000' );

    //     $datepicker       = $request->datepicker;
    //     $chackup_hn       = $request->chackup_hn;
    //     if ($chackup_hn =='') {
    //         $checks    = DB::table('checkup_report_temp')->count();
    //         // $checks    = DB::table('checkup_report_temp')->where('vn',$value->vn)->count();
    //         $datashow    = DB::table('checkup_report_temp')->where('checkup_report_temp_id',1)->first();
    //         // $data['waist'] = $datashow->waist;
    //         $data_show_sub = DB::connection('mysql10')->select(
    //             'SELECT lh.vn , lh.hn ,p.sex ,v.age_y, lo.lab_items_code 
    //                         ,ifnull(if(lit.lab_items_name is null,lo.lab_items_name_ref,lit.lab_items_name),lit.lab_items_name) lab_items_name
    //                         ,lit.lab_items_display_name , lo.lab_order_result , lo.lab_items_normal_value_ref ,
    //                         lo.lab_items_sub_group_code , lh.sub_group_list
    //                         FROM lab_head lh
    //                         LEFT JOIN lab_order lo ON lo.lab_order_number = lh.lab_order_number
    //                         LEFT JOIN lab_items li ON li.lab_items_code = lo.lab_items_code
    //                         LEFT JOIN lab_itemsthai lit ON lit.lab_items_code = li.lab_items_code
    //                         LEFT JOIN patient p ON p.HN = lh.hn
    //                         LEFT JOIN vn_stat v ON v.vn = lh.vn
    //                         WHERE lh.order_date = "'.$datepicker.'"
    //                         AND lh.hn = "'.$chackup_hn.'" 
    //                         AND lo.lab_order_result IS NOT NULL
    //                         and lo.lab_order_result <> ""
    //                         AND lo.lab_items_code not in ("253","259","260","257","523")
    //                         order by lo.lab_items_sub_group_code,li.display_order,li.lab_items_name   limit 1
    //             ');
    //             // OR lit.lab_items_display_name = "" )

    //         // AND lh.hn = "'.$chackup_hn.'"
    //     } else {                   
        
    //         $data_show2 = DB::connection('mysql10')->select(
    //             'SELECT v.vn ,o.hn ,o.vstdate , o.vsttime ,k.department, CONCAT(p.pname,p.fname," ",p.lname) as ptname , p.cid ,pt.`name` as pttype,s.code ,s.`name` as sex , v.age_y, os.bw , os.height , os.waist 
    //             ,os.temperature , os.rr , os.pulse , os.bmi , os.bps , os.bpd,p.hometel
    //             FROM ovst o
    //             LEFT JOIN patient p ON p.hn = o.hn
    //             LEFT JOIN opdscreen os ON os.vn = o.vn
    //             LEFT JOIN vn_stat v ON v.vn = o.vn
    //             LEFT JOIN person pp ON pp.cid = v.cid
    //             LEFT JOIN pttype pt ON pt.pttype = o.pttype
    //             LEFT JOIN kskdepartment k ON k.depcode = o.main_dep
    //             LEFT JOIN sex s ON s.`code` = pp.sex
    //             WHERE o.hn = "'.$chackup_hn.'"
    //             AND o.vstdate = "'.$datepicker.'"  
    //             AND o.main_dep = "078"
    //             LIMIT 1   
    //         ');
    //         Checkup_report_temp::truncate();
    //         foreach ($data_show2 as $key => $value) {
    //             Checkup_report_temp::insert([ 
    //                     'vn'            => $value->vn,
    //                     'hn'            => $value->hn,
    //                     'vstdate'       => $value->vstdate,
    //                     'vsttime'       => $value->vsttime,
    //                     'department'    => $value->department,
    //                     'ptname'        => $value->ptname,           
    //                     'cid'           => $value->cid,
    //                     'pttype'        => $value->pttype,
    //                     'sex_code'      => $value->code,
    //                     'sex'           => $value->sex,
    //                     'age_y'         => $value->age_y,
    //                     'bw'            => $value->bw,
    //                     'height'        => $value->height,
    //                     'waist'         => $value->waist,
    //                     'temperature'   => $value->temperature,
    //                     'rr'            => $value->rr,
    //                     'pulse'         => $value->pulse,
    //                     'bmi'           => $value->bmi,
    //                     'bps'           => $value->bps,
    //                     'bpd'           => $value->bpd,
    //                     'hometel'       => $value->hometel,

    //             ]);

    //                 $checkss    = DB::table('checkup_report')->where('vn',$value->vn)->count();
    //                     if ($checkss > 0) {
    //                         # code...
    //                     } else {
    //                         Checkup_report::insert([ 
    //                             'vn'            => $value->vn,
    //                             'hn'            => $value->hn,
    //                             'vstdate'       => $value->vstdate,
    //                             'vsttime'       => $value->vsttime,
    //                             'department'    => $value->department,
    //                             'ptname'        => $value->ptname,           
    //                             'cid'           => $value->cid,
    //                             'pttype'        => $value->pttype,
    //                             'sex_code'      => $value->code,
    //                             'sex'           => $value->sex,
    //                             'age_y'         => $value->age_y,
    //                             'bw'            => $value->bw,
    //                             'height'        => $value->height,
    //                             'waist'         => $value->waist,
    //                             'temperature'   => $value->temperature,
    //                             'rr'            => $value->rr,
    //                             'pulse'         => $value->pulse,
    //                             'bmi'           => $value->bmi,
    //                             'bps'           => $value->bps,
    //                             'bpd'           => $value->bpd,
    //                             'hometel'       => $value->hometel,

    //                         ]);
    //                     }  
    //         }

    //                 $datashow    = DB::table('checkup_report_temp')->where('checkup_report_temp_id',1)->first();
    //                 $checks    = DB::table('checkup_report_temp')->count();

    //                 $data_show_sub = DB::connection('mysql10')->select(
    //                     // 'SELECT lh.vn , lh.hn ,p.sex ,v.age_y, lo.lab_items_code,lh.order_date
    //                     //     ,ifnull(if(lit.lab_items_name is null,lo.lab_items_name_ref,lit.lab_items_name),lit.lab_items_name) lab_items_name
    //                     //     ,lit.lab_items_display_name , lo.lab_order_result , lo.lab_items_normal_value_ref ,
    //                     //     lo.lab_items_sub_group_code , lh.sub_group_list
    //                     //     FROM lab_head lh
    //                     //     LEFT JOIN lab_order lo ON lo.lab_order_number = lh.lab_order_number
    //                     //     LEFT JOIN lab_items li ON li.lab_items_code = lo.lab_items_code
    //                     //     LEFT JOIN lab_itemsthai lit ON lit.lab_items_code = li.lab_items_code
    //                     //     LEFT JOIN patient p ON p.HN = lh.hn
    //                     //     LEFT JOIN vn_stat v ON v.vn = lh.vn
    //                     //     WHERE lh.order_date = "'.$datepicker.'"
    //                     //     AND lh.hn = "'.$chackup_hn.'" 
    //                     //     AND lo.lab_order_result IS NOT NULL
    //                     //     and lo.lab_order_result <> ""
    //                     //     AND lo.lab_items_code not in ("253","259","260","257","523")
    //                     //     order by lo.lab_items_sub_group_code,li.display_order,li.lab_items_name 
                        
    //                     'SELECT lh.vn, lh.hn, p.sex, v.age_y, lo.lab_items_code, lh.order_date,
    //                             COALESCE(lit.lab_items_name, lo.lab_items_name_ref) AS lab_items_name,
    //                             lit.lab_items_display_name, lo.lab_order_result, lo.lab_items_normal_value_ref,
    //                             lo.lab_items_sub_group_code, lh.sub_group_list
    //                             FROM lab_head lh
    //                             LEFT JOIN lab_order lo ON lo.lab_order_number = lh.lab_order_number
    //                             LEFT JOIN lab_items li ON li.lab_items_code = lo.lab_items_code
    //                             LEFT JOIN lab_itemsthai lit ON lit.lab_items_code = li.lab_items_code
    //                             LEFT JOIN patient p ON p.hn = lh.hn
    //                             LEFT JOIN vn_stat v ON v.vn = lh.vn
    //                             WHERE lh.order_date = "'.$datepicker.'"
    //                             AND lh.hn = "'.$chackup_hn.'" 
    //                             AND lo.lab_order_result IS NOT NULL
    //                             AND lo.lab_order_result <> ""
    //                             AND lo.lab_items_code NOT IN ("253", "259", "260", "257", "523")
    //                             ORDER BY lo.lab_items_sub_group_code, li.display_order, li.lab_items_name;

    //                     ');
    //                     // WHERE lh.order_date = "'.$datepicker.'" 
    //                     //     AND lh.hn = "'.$chackup_hn.'" 

    //                 // foreach ($data_show_sub as $key => $value_sub) {
                       
    //                 //         //Lab
    //                 //         $checkssub    = DB::table('checkup_lab')->where('vn',$value_sub->vn)->where('lab_items_code',$value_sub->lab_items_code)->count();
    //                 //         if ($checkssub > 0) { 
    //                 //             Checkup_lab::where('vn',$value_sub->vn)->update([
    //                 //                 'sex'    => $value_sub->sex,
    //                 //                 'age_y'  => $value_sub->age_y,
    //                 //             ]);
    //                 //         } else {

    //                 //             if ($value_sub->lab_items_code !='' || $value_sub->lab_items_code !='0') {
                                    
    //                 //                 // dd($value_sub->lab_items_code);
    //                 //                 // dd($datashowlab);
    //                 //                if ($value_sub->lab_items_code = '109') {
    //                 //                         $datashowlab    = DB::table('checkup_config')->where('lab_items_code',$value_sub->lab_items_code)->first();                                        

    //                 //                         $lab109_min     = $datashowlab->lab_items_normal_value_min;
    //                 //                         $lab109_max     = $datashowlab->lab_items_normal_value_max;

    //                 //                         // if ($value_sub->lab_items_code = '109' && $value_sub->lab_order_result > $lab109_min || $value_sub->lab_order_result < 150 ) {
    //                 //                         if ($value_sub->lab_order_result >= $lab109_min && $value_sub->lab_order_result <= $lab109_max) {
    //                 //                             $lab_order_result_new   = "ปกติ";
    //                 //                         } else {
    //                 //                             $lab_order_result_new   = "ผิดปกติ";
    //                 //                         }
    //                 //                } else {
    //                 //                         $lab_order_result_new   = "yyyy";
    //                 //                }   
                                    
    //                 //             } else {
    //                 //                 # code...
    //                 //             }                                
                                

    //                 //             Checkup_lab::insert([ 
    //                 //                 'vn'                           => $value_sub->vn,
    //                 //                 'hn'                           => $value_sub->hn,
    //                 //                 'sex'                          => $value_sub->sex,
    //                 //                 'age_y'                        => $value_sub->age_y,
    //                 //                 'order_date'                   => $value_sub->order_date,
    //                 //                 'lab_items_code'               => $value_sub->lab_items_code,
    //                 //                 'lab_items_name'               => $value_sub->lab_items_name,
    //                 //                 'lab_items_display_name'       => $value_sub->lab_items_display_name,

    //                 //                 'lab_order_result'             => $value_sub->lab_order_result, 

    //                 //                 // 'lab_items_name_ref'           => $value_sub->cid,
    //                 //                 'lab_items_normal_value_ref'   => $value_sub->lab_items_normal_value_ref,
    //                 //                 'lab_items_sub_group_code'     => $value_sub->lab_items_sub_group_code,
    //                 //                 'sub_group_list'               => $value_sub->sub_group_list,

    //                 //                 'lab_order_result_new'         => $lab_order_result_new,
                                    

    //                 //             ]);
                                
                                
                                


                              
    //                 //         } 
    //                 // } 

    //                 foreach ($data_show_sub as $key => $value_sub) {
    
    //                     // ตรวจสอบว่ามีข้อมูลใน checkup_lab หรือไม่
    //                     $checkssub = DB::table('checkup_lab')
    //                                     ->where('vn', $value_sub->vn)
    //                                     ->where('lab_items_code', $value_sub->lab_items_code)
    //                                     ->count();                       
                       

    //                     if ($checkssub > 0) { 
    //                         // if ($value_sub->lab_items_code != '' && $value_sub->lab_items_code != '0') {
                                
    //                         //     // ดึงค่าปกติจาก checkup_config
    //                         //     $datashowlab = DB::table('checkup_config')
    //                         //                      ->where('lab_items_code', $value_sub->lab_items_code)
    //                         //                      ->first();                    
    //                         //     if ($datashowlab) {
    //                         //         $lab_min = $datashowlab->lab_items_normal_value_min;
    //                         //         $lab_max = $datashowlab->lab_items_normal_value_max;
    //                         //         //<span class="badge bg-danger" style="font-size: 15px">ความดันโลหิตสูงระดับ 1</span>
    //                         //         // ตรวจสอบผลแลปว่าปกติหรือไม่
    //                         //         if ($value_sub->lab_order_result >= $lab_min && $value_sub->lab_order_result <= $lab_max) {
    //                         //             $lab_order_result_new = "ปกติ";
    //                         //         } else {
    //                         //             $lab_order_result_new = "ผิดปกติ";
    //                         //         }
    //                         //     } else {
    //                         //         $lab_order_result_new = "ไม่มีข้อมูลการตรวจ";
    //                         //     }
    //                         // } else {
    //                         //     $lab_order_result_new = "ข้อมูลไม่สมบูรณ์";
    //                         // }

    //                         // ถ้ามีข้อมูลแล้ว อัปเดตข้อมูลเพศ และอายุ
    //                         // Checkup_lab::where('vn', $value_sub->vn)->update([
    //                             // 'sex'    => $value_sub->sex,
    //                             // 'age_y'  => $value_sub->age_y,
    //                             // 'lab_order_result_new'         => $lab_order_result_new, 
    //                         // ]);


                            

    //                     } else {
    //                         // $checkdata = Checkup_lab::where('vn', $value_sub->vn)->count();
    //                         // dd($checkdata);

    //                         // ตรวจสอบว่าค่ารหัสแลปไม่ว่างเปล่า
    //                         if ($value_sub->lab_items_code != '' && $value_sub->lab_items_code != '0') {
                                
    //                             // ดึงค่าปกติจาก checkup_config
    //                             $datashowlab = DB::table('checkup_config')
    //                                              ->where('lab_items_code', $value_sub->lab_items_code)
    //                                              ->first();
                    
    //                             if ($datashowlab) {
    //                                 $lab_min = $datashowlab->lab_items_normal_value_min;
    //                                 $lab_max = $datashowlab->lab_items_normal_value_max;
    //                                 //<span class="badge bg-danger" style="font-size: 15px">ความดันโลหิตสูงระดับ 1</span>
    //                                 // ตรวจสอบผลแลปว่าปกติหรือไม่
    //                                 if ($value_sub->lab_order_result >= $lab_min && $value_sub->lab_order_result <= $lab_max) {
    //                                     $lab_order_result_new = "ปกติ";
    //                                 } else {
    //                                     $lab_order_result_new = "ผิดปกติ";
    //                                 }
    //                             } else {
    //                                 $lab_order_result_new = "ไม่มีข้อมูลการตรวจ";
    //                             }


    //                         } else {
    //                             $lab_order_result_new = "ข้อมูลไม่สมบูรณ์";
    //                         }
                    
                           
    //                         // if ($checkdata > 0) {
    //                         //     // แทรกข้อมูลเข้า checkup_lab
    //                         //     Checkup_lab::where('vn', $value_sub->vn)->update([
    //                         //         'vn'                           => $value_sub->vn,
    //                         //         'hn'                           => $value_sub->hn,
    //                         //         'sex'                          => $value_sub->sex,
    //                         //         'age_y'                        => $value_sub->age_y,
    //                         //         'order_date'                   => $value_sub->order_date,
    //                         //         'lab_items_code'               => $value_sub->lab_items_code,
    //                         //         'lab_items_name'               => $value_sub->lab_items_name,
    //                         //         'lab_items_display_name'       => $value_sub->lab_items_display_name,
    //                         //         'lab_order_result'             => $value_sub->lab_order_result, 
    //                         //         'lab_items_normal_value_ref'   => $value_sub->lab_items_normal_value_ref,
    //                         //         'lab_items_sub_group_code'     => $value_sub->lab_items_sub_group_code,
    //                         //         'sub_group_list'               => $value_sub->sub_group_list,
    //                         //         'lab_order_result_new'         => $lab_order_result_new, 
    //                         //     ]);
    //                         // } else {
    //                             // แทรกข้อมูลเข้า checkup_lab
    //                             Checkup_lab::insert([
    //                                 'vn'                           => $value_sub->vn,
    //                                 'hn'                           => $value_sub->hn,
    //                                 'sex'                          => $value_sub->sex,
    //                                 'age_y'                        => $value_sub->age_y,
    //                                 'order_date'                   => $value_sub->order_date,
    //                                 'lab_items_code'               => $value_sub->lab_items_code,
    //                                 'lab_items_name'               => $value_sub->lab_items_name,
    //                                 'lab_items_display_name'       => $value_sub->lab_items_display_name,
    //                                 'lab_order_result'             => $value_sub->lab_order_result, 
    //                                 'lab_items_normal_value_ref'   => $value_sub->lab_items_normal_value_ref,
    //                                 'lab_items_sub_group_code'     => $value_sub->lab_items_sub_group_code,
    //                                 'sub_group_list'               => $value_sub->sub_group_list,
    //                                 'lab_order_result_new'         => $lab_order_result_new,
    //                             ]);
    //                         // }
                            
                            
                            
    //                     }
    //                 }

                    
                    


 

    //     }


    //     $data_show_subnew = DB::connection('mysql')->select(
    //         'SELECT a.vn,a.hn,a.sex,a.order_date,a.lab_items_code,a.lab_items_name,a.lab_items_display_name,a.lab_order_result,a.lab_items_normal_value_ref,a.lab_items_sub_group_code
    //                 ,a.sub_group_list,a.labtype,a.age_y
    //                 ,b.lab_items_normal_value_max,b.lab_items_normal_value_min,b.mark,b.age,a.lab_order_result_new


    //                 FROM checkup_lab a
    //                 LEFT JOIN checkup_config b ON b.lab_items_code = a.lab_items_code AND a.sex = b.sex
    //                 WHERE a.hn = "'.$chackup_hn.'" AND a.order_date = "'.$datepicker.'"
    //                 GROUP BY a.lab_items_code
    //         ');



        
 
    //     return view('checkup.checkup_report',$data,[
    //         'datepicker'          => $datepicker,
    //         'datashow'            => $datashow,
    //         'data_show_sub'       => $data_show_sub,
    //         'data_show_subnew'    => $data_show_subnew,
    //         'checks'              => $checks,
    //         'chackup_hn'          => $chackup_hn
    //     ]);
    // }

    public function checkup_report(Request $request)
    {
        $startdate        = $request->startdate;
        $enddate          = $request->enddate;
     
        $datenow             = date('Y-m-d');
        $data['date_now']    = date('Y-m-d');
        $dabudget_year    = DB::table('budget_year')->where('active','=',true)->first();
        $leave_month_year = DB::table('leave_month')->orderBy('MONTH_ID', 'ASC')->get();
        $date             = date('Y-m-d');
        $y                = date('Y') + 543;
        $newweek = date('Y-m-d', strtotime($date . ' -1 week')); //ย้อนหลัง 1 สัปดาห์
        $newDate = date('Y-m-d', strtotime($date . ' -5 months')); //ย้อนหลัง 5 เดือน
        $newyear = date('Y-m-d', strtotime($date . ' -1 year')); //ย้อนหลัง 1 ปี

        $data['users']    = DB::table('users')->get();
        $data['hn'] = DB::connection('mysql10')->select('SELECT hn,CONCAT(pname,fname," ",lname) as ptname FROM patient GROUP BY hn limit 1000' );

        $datepicker       = $request->datepicker;
        $chackup_hn       = $request->chackup_hn;

        if ($chackup_hn =='') {
            $checks    = DB::table('checkup_report_temp')->count();
            
            $datashow    = DB::table('checkup_report_temp')->where('checkup_report_temp_id',1)->first();
            
            $data_show_sub = DB::connection('mysql10')->select(
                'SELECT lh.vn , lh.hn ,p.sex ,v.age_y, lo.lab_items_code,ifnull(if(lit.lab_items_name is null,lo.lab_items_name_ref,lit.lab_items_name),lit.lab_items_name) lab_items_name
                            ,lit.lab_items_display_name ,lo.lab_order_result , lo.lab_items_normal_value_ref,lo.lab_items_sub_group_code , lh.sub_group_list
                            FROM lab_head lh
                            LEFT JOIN lab_order lo ON lo.lab_order_number = lh.lab_order_number
                            LEFT JOIN lab_items li ON li.lab_items_code = lo.lab_items_code
                            LEFT JOIN lab_itemsthai lit ON lit.lab_items_code = li.lab_items_code
                            LEFT JOIN patient p ON p.HN = lh.hn
                            LEFT JOIN vn_stat v ON v.vn = lh.vn
                            WHERE lh.order_date = "'.$datepicker.'"
                            AND lh.hn = "'.$chackup_hn.'" 
                            AND lo.lab_order_result IS NOT NULL
                            and lo.lab_order_result <> ""
                            AND lo.lab_items_code not in ("253","259","260","257","523")
                            order by lo.lab_items_sub_group_code,li.display_order,li.lab_items_name limit 1
                ');

                $data_show_subnew = DB::connection('mysql')->select(
                    'SELECT a.vn,a.hn,a.sex,a.order_date,a.lab_items_code,a.lab_items_name,a.lab_items_display_name,a.lab_order_result,a.lab_items_normal_value_ref,a.lab_items_sub_group_code
                            ,a.sub_group_list,a.labtype,a.age_y,b.lab_items_normal_value_max,b.lab_items_normal_value_min,b.mark,b.age,a.lab_order_result_new
                            FROM checkup_lab a
                            LEFT JOIN checkup_config b ON b.lab_items_code = a.lab_items_code AND a.sex = b.sex
                            WHERE a.order_date = "'.$datepicker.'" AND a.hn = "'.$chackup_hn.'" 
                            GROUP BY a.lab_items_code LIMIT 1
                ');    
                
        } else {                   
        
                $data_show2 = DB::connection('mysql10')->select(
                    'SELECT v.vn ,o.hn ,o.vstdate , o.vsttime ,k.department, CONCAT(p.pname,p.fname," ",p.lname) as ptname , p.cid ,pt.`name` as pttype,p.sex ,s.`name` as namesex, v.age_y, os.bw , os.height , os.waist 
                    ,os.temperature , os.rr , os.pulse , os.bmi , os.bps , os.bpd,p.hometel
                    FROM ovst o
                    LEFT JOIN patient p ON p.hn = o.hn
                    LEFT JOIN opdscreen os ON os.vn = o.vn
                    LEFT JOIN vn_stat v ON v.vn = o.vn
                    LEFT JOIN person pp ON pp.cid = v.cid
                    LEFT JOIN pttype pt ON pt.pttype = o.pttype
                    LEFT JOIN kskdepartment k ON k.depcode = o.main_dep
                    LEFT JOIN sex s ON s.`code` = pp.sex
                    WHERE o.hn = "'.$chackup_hn.'"
                    AND o.vstdate = "'.$datepicker.'"  
                    AND o.main_dep = "078"
                    LIMIT 1   
                ');
                Checkup_report_temp::truncate();
                
                foreach ($data_show2 as $key => $value) {
                            Checkup_report_temp::insert([ 
                                    'vn'            => $value->vn,
                                    'hn'            => $value->hn,
                                    'vstdate'       => $value->vstdate,
                                    'vsttime'       => $value->vsttime,
                                    'department'    => $value->department,
                                    'ptname'        => $value->ptname,           
                                    'cid'           => $value->cid,
                                    'pttype'        => $value->pttype,
                                    'sex_code'      => $value->sex,
                                    'sex'           => $value->namesex,
                                    'age_y'         => $value->age_y,
                                    'bw'            => $value->bw,
                                    'height'        => $value->height,
                                    'waist'         => $value->waist,
                                    'temperature'   => $value->temperature,
                                    'rr'            => $value->rr,
                                    'pulse'         => $value->pulse,
                                    'bmi'           => $value->bmi,
                                    'bps'           => $value->bps,
                                    'bpd'           => $value->bpd,
                                    'hometel'       => $value->hometel,

                            ]);

                            $checkss    = DB::table('checkup_report')->where('vn',$value->vn)->count();
                            if ($checkss > 0) {
                                
                            } else {
                                Checkup_report::insert([ 
                                    'vn'            => $value->vn,
                                    'hn'            => $value->hn,
                                    'vstdate'       => $value->vstdate,
                                    'vsttime'       => $value->vsttime,
                                    'department'    => $value->department,
                                    'ptname'        => $value->ptname,           
                                    'cid'           => $value->cid,
                                    'pttype'        => $value->pttype,
                                    'sex_code'      => $value->sex,
                                    'sex'           => $value->namesex,
                                    'age_y'         => $value->age_y,
                                    'bw'            => $value->bw,
                                    'height'        => $value->height,
                                    'waist'         => $value->waist,
                                    'temperature'   => $value->temperature,
                                    'rr'            => $value->rr,
                                    'pulse'         => $value->pulse,
                                    'bmi'           => $value->bmi,
                                    'bps'           => $value->bps,
                                    'bpd'           => $value->bpd,
                                    'hometel'       => $value->hometel,

                                ]);
                            }  
                 }

                $datashow    = DB::table('checkup_report_temp')->where('checkup_report_temp_id',1)->first();
                $checks    = DB::table('checkup_report_temp')->count();

                $data_show_sub = DB::connection('mysql2')->select(
                    'SELECT lh.vn, lh.hn, p.sex, v.age_y, lo.lab_items_code, lh.order_date, COALESCE(lit.lab_items_name, lo.lab_items_name_ref) AS lab_items_name,
                            lit.lab_items_display_name, lo.lab_order_result, lo.lab_items_normal_value_ref, lo.lab_items_sub_group_code, lh.sub_group_list
                            ,pc.lab_items_normal_value_min,pc.lab_items_normal_value_max
                            FROM lab_head lh
                            LEFT JOIN lab_order lo ON lo.lab_order_number = lh.lab_order_number
                            LEFT JOIN lab_items li ON li.lab_items_code = lo.lab_items_code
                            LEFT JOIN lab_itemsthai lit ON lit.lab_items_code = li.lab_items_code
                            LEFT JOIN patient p ON p.hn = lh.hn
                            LEFT JOIN vn_stat v ON v.vn = lh.vn
                            INNER JOIN pkbackoffice.checkup_config pc ON pc.lab_items_code = li.lab_items_code AND pc.sex = p.sex
                            WHERE lh.order_date = "'.$datepicker.'"
                            AND lh.hn = "'.$chackup_hn.'" 
                            AND lo.lab_order_result IS NOT NULL
                            AND lo.lab_order_result <> ""
                            AND lo.lab_items_code NOT IN ("253", "259", "260", "257", "523")
                            GROUP BY li.lab_items_code
                            ORDER BY lo.lab_items_sub_group_code,li.display_order,li.lab_items_name;

                ');
                    // dd($data_show_sub);
                foreach ($data_show_sub as $key => $value_sub) {
                    // Checkup_lab::where('vn', $value_sub->vn)->delete();
                    // ตรวจสอบว่ามีข้อมูลใน checkup_lab หรือไม่
                    $checkssub = DB::table('checkup_lab')->where('vn', $value_sub->vn)->where('sex', $value_sub->sex)->where('lab_items_code', $value_sub->lab_items_code)->count();                    
                    if ($checkssub > 0) { 
                         
                        // ถ้ามีข้อมูลแล้ว อัปเดตข้อมูลเพศ และอายุ
                        // Checkup_lab::where('vn', $value_sub->vn)->update([
                        //     'sex'    => $value_sub->sex,
                        //     'age_y'  => $value_sub->age_y,
                        // ]);

                    } else {

                        if ($value_sub->lab_order_result == '-') { 
                            // แทรกข้อมูลเข้า checkup_lab
                            Checkup_lab::insert([
                                'vn'                           => $value_sub->vn,
                                'hn'                           => $value_sub->hn,
                                'sex'                          => $value_sub->sex,
                                'age_y'                        => $value_sub->age_y,
                                'order_date'                   => $value_sub->order_date,
                                'lab_items_code'               => $value_sub->lab_items_code,
                                'lab_items_name'               => $value_sub->lab_items_name,
                                'lab_items_display_name'       => $value_sub->lab_items_display_name,
                                'lab_order_result'             => $value_sub->lab_order_result, 
                                'lab_items_normal_value_ref'   => $value_sub->lab_items_normal_value_ref,
                                'lab_items_sub_group_code'     => $value_sub->lab_items_sub_group_code,
                                'sub_group_list'               => $value_sub->sub_group_list,
                                'lab_order_result_new'         => '',
                            ]);

                        } else {
                            // ตรวจสอบว่าค่ารหัสแลปไม่ว่างเปล่า
                            if ($value_sub->lab_items_code != '' && $value_sub->lab_items_code != '0') {                                   
                                        // ดึงค่าปกติจาก checkup_config                            
                                        $datashowlab = DB::table('checkup_config')->where('lab_items_code', $value_sub->lab_items_code)->where('sex', $value_sub->sex)->first();

                                        if ($datashowlab && is_numeric($datashowlab->lab_items_normal_value_min) && is_numeric($datashowlab->lab_items_normal_value_max)) {
                                            $lab_min = floatval($datashowlab->lab_items_normal_value_min);
                                            $lab_max = floatval($datashowlab->lab_items_normal_value_max);
                                        }
                                
                                    if ($datashowlab) {
                                                $lab_min = $datashowlab->lab_items_normal_value_min;
                                                $lab_max = $datashowlab->lab_items_normal_value_max;
                                                // ตรวจสอบผลแลปว่าปกติหรือไม่
                                                if ($value_sub->lab_order_result >= $lab_min && $value_sub->lab_order_result <= $lab_max) {
                                                    $lab_order_result_new = "ปกติ";
                                                } else {
                                                    $lab_order_result_new = "ผิดปกติ";
                                                }
                                    } else {
                                        $lab_order_result_new = $value_sub->lab_order_result;
                                    }   

                            }                
                            // แทรกข้อมูลเข้า checkup_lab
                            Checkup_lab::insert([
                                'vn'                           => $value_sub->vn,
                                'hn'                           => $value_sub->hn,
                                'sex'                          => $value_sub->sex,
                                'age_y'                        => $value_sub->age_y,
                                'order_date'                   => $value_sub->order_date,
                                'lab_items_code'               => $value_sub->lab_items_code,
                                'lab_items_name'               => $value_sub->lab_items_name,
                                'lab_items_display_name'       => $value_sub->lab_items_display_name,
                                'lab_order_result'             => $value_sub->lab_order_result, 
                                'lab_items_normal_value_ref'   => $value_sub->lab_items_normal_value_ref,
                                'lab_items_sub_group_code'     => $value_sub->lab_items_sub_group_code,
                                'sub_group_list'               => $value_sub->sub_group_list,
                                'lab_order_result_new'         => $lab_order_result_new,
                            ]);
                        }

                    }
                }


                $data_show_subnew = DB::connection('mysql')->select(
                    'SELECT a.vn,a.hn,a.sex,a.order_date,a.lab_items_code,a.lab_items_name,a.lab_items_display_name,a.lab_order_result,a.lab_items_normal_value_ref,a.lab_items_sub_group_code
                            ,a.sub_group_list,a.labtype,a.age_y,b.lab_items_normal_value_max,b.lab_items_normal_value_min,b.mark,b.age,a.lab_order_result_new
                            FROM checkup_lab a
                            LEFT JOIN checkup_config b ON b.lab_items_code = a.lab_items_code AND a.sex = b.sex
                            WHERE a.order_date = "'.$datepicker.'" AND a.hn = "'.$chackup_hn.'" 
                            GROUP BY a.lab_items_code
                ');    

        }


            
 
        return view('checkup.checkup_report',$data,[
            'datepicker'          => $datepicker,
            'datashow'            => $datashow,
            'data_show_sub'       => $data_show_sub,
            'data_show_subnew'    => $data_show_subnew,
            'checks'              => $checks,
            'chackup_hn'          => $chackup_hn
        ]);
    }

    public function checkup_report_detail(Request $request,$hn,$vstdate)
    {
        // $hn                 = $request->chackup_hn;
        // $vstdate            = $request->datepicker;
        // $data_show          = Patient::where('hn',$hn)->first();

        $data_show2 = DB::connection('mysql10')->select(
            'SELECT v.vn ,o.hn ,o.vstdate , o.vsttime ,k.department, CONCAT(p.pname,p.fname," ",p.lname) as ptname , p.cid ,pt.`name`,s.code ,s.`name` as sex , v.age_y, os.bw , os.height , os.waist 
            ,os.temperature , os.rr , os.pulse , os.bmi , os.bps , os.bpd,p.hometel
            FROM ovst o
            LEFT JOIN patient p ON p.hn = o.hn
            LEFT JOIN opdscreen os ON os.vn = o.vn
            LEFT JOIN vn_stat v ON v.vn = o.vn
            LEFT JOIN person pp ON pp.cid = v.cid
            LEFT JOIN pttype pt ON pt.pttype = o.pttype
            LEFT JOIN kskdepartment k ON k.depcode = o.main_dep
            LEFT JOIN sex s ON s.`code` = pp.sex
            WHERE o.hn = "'.$hn.'"
            AND o.vstdate = "'.$vstdate.'"  
            AND o.main_dep = "078"
            LIMIT 1           
            
        ');
        // WHERE o.hn = "'.$hn.'" AND o.vstdate = "'.$vstdate.'" 
        foreach ($data_show2 as $key => $value) {
            $vn            = $value->vn;
            $hn            = $value->hn;
            $vstdate       = $value->vstdate;
            $vsttime       = $value->vsttime;
            $department    = $value->department;
            $ptname        = $value->ptname;            
            $cid           = $value->cid;
            $name          = $value->name;
            $sex_code      = $value->code;
            $sex           = $value->sex;
            $age_y         = $value->age_y;
            $bw            = $value->bw;
            $height        = $value->height;
            $waist         = $value->waist;
            $temperature   = $value->temperature;
            $rr            = $value->rr;
            $pulse         = $value->pulse;
            $bmi           = $value->bmi;
            $bps           = $value->bps;
            $bpd           = $value->bpd;
            $hometel       = $value->hometel;
            
        }

        if ($waist > 90 && $sex_code = 1 ) {
            $color_waist = '<span class="badge bg-danger text-dark" style="font-size: 15px">อ้วนลงพุง</span>';# code...        
        }elseif ($waist = 90 && $sex_code = 1 ) {
            $color_waist = '<span class="badge bg-warning" style="font-size: 15px">เสี่ยงอ้วนลงพุง</span>';
        }elseif ($waist > 80 && $sex_code = 2 ) {
            $color_waist = '<span class="badge bg-danger" style="font-size: 15px">อ้วนลงพุง</span>';
        }elseif ($waist = 80 && $sex_code = 2 ) {
            $color_waist = '<span class="badge bg-warning" style="font-size: 15px">เสี่ยงอ้วนลงพุง</span>';
        } else {
            $color_waist = '<span class="badge bg-success" style="font-size: 15px">ปกติ</span>';# code...
        }
        

        if ($bmi < 18.5) {
        }elseif ($bmi >= 23 && $bmi <= 24.99) {
            $color = '<span class="badge bg-primary" style="font-size: 15px">น้ำหนักเริ่มเกินเกณฑ์ 2</span>';
        }elseif ($bmi >= 25 && $bmi <= 29.9) {
            $color = '<span class="badge bg-warning text-dark" style="font-size: 15px">อ้วนระดับ 2</span>';
        }elseif ($bmi >= 30) {
            $color = '<span class="badge bg-danger" style="font-size: 15px">อ้วนระดับ 3</span>';
        } else {
            $color = '<span class="badge bg-success" style="font-size: 15px">ปกติ</span>';
        }



        
        $output='
        <div class="row">
            <div class ="col-md-1" style="font-size: 14px">HN :</div>    
            <div class ="col-md-1" style="font-size: 14px">
                <label for=""> '.$hn. '</label>                
            </div> 
            <div class ="col-md-1" style="font-size: 14px">ชื่อ-สกุล :</div>    
            <div class ="col-md-2" style="font-size: 14px">
                <label for=""> '.$ptname. '</label>
            </div>
            <div class ="col-md-1" style="font-size: 14px">เพศ :</div>    
            <div class ="col-md-1" style="font-size: 14px">
                <label for=""> '.$sex. '</label>
            </div>
            <div class ="col-md-1" style="font-size: 14px">อายุ :</div>    
            <div class ="col-md-1" style="font-size: 14px">
                <label for=""> '.$age_y. ' &nbsp; ปี</label>
            </div>
            <div class ="col-md-1" style="font-size: 14px">เลขบัตร :</div>    
            <div class ="col-md-2" style="font-size: 14px">
                <label for=""> '.$cid. '</label>
            </div>  
        </div>
        
        <div class="row">
            <div class ="col-md-1" style="font-size: 14px">น้ำหนัก :</div>    
            <div class ="col-md-1" style="font-size: 14px">
                <label for=""> '.$bw. ' &nbsp;Kg.</label>
            </div> 
            <div class ="col-md-1" style="font-size: 14px">ส่วนสูง :</div>    
            <div class ="col-md-2" style="font-size: 14px">
                <label for=""> '.$height. ' &nbsp;Cm.</label>
            </div>
            <div class ="col-md-1" style="font-size: 14px">รอบเอว :</div>    
            <div class ="col-md-1" style="font-size: 14px">
                <label for=""> '.$waist. ' &nbsp;Cm.</label>
            </div>
            <div class ="col-md-1"> 
              '. $color_waist.'
            </div>
            <div class ="col-md-1" style="font-size: 14px">อุณหภูมิ :</div>    
            <div class ="col-md-1" style="font-size: 14px">
                <label for=""> '.$temperature. ' &nbsp;C</label>
            </div>
            <div class ="col-md-1" style="font-size: 14px">อัตราการหายใจ :</div>    
            <div class ="col-md-1" style="font-size: 14px">
                <label for=""> '.$rr. ' &nbsp; / m</label>
            </div>  
        </div>

        <div class="row">
            <div class ="col-md-1" style="font-size: 14px">ชีพจร :</div>    
            <div class ="col-md-1" style="font-size: 14px">
                <label for=""> '.$pulse. ' &nbsp; / m</label>
            </div>
            <div class ="col-md-1" style="font-size: 14px" >BMI :</div>    
            <div class ="col-md-1">
                <label for=""> '.$bmi. ' </label>
            </div>
            <div class ="col-md-1"> 
              '. $color.'
            </div> 
              <div class ="col-md-1" style="font-size: 14px">ความดันโลหิต :</div>    
            <div class ="col-md-1">
                <label for=""> '.$bps. ' &nbsp;/ '.$bpd. '</label>
            </div>
            <div class ="col-md-1" style="font-size: 14px">
                <label for="">ปกติ</label>
            </div>                                     
        </div>

        <div class="row">
            <div class ="col-md-1" style="font-size: 14px">ตรวจวัดสายตา :</div>    
            <div class ="col-md-3">
                <label for=""> (&nbsp;&nbsp;) &nbsp; ปกติ &nbsp;&nbsp;&nbsp; (&nbsp;&nbsp;) &nbsp; ไม่ปกติ............................................... (&nbsp;&nbsp;) &nbsp; ไม่ได้ตรวจ</label>
            </div> 
            <div class ="col-md-1" style="font-size: 14px">ผลตรวจ X-RAY ปอด :</div>    
            <div class ="col-md-3">
                <label for=""> (&nbsp;&nbsp;) &nbsp; ปกติ ............................................... &nbsp;&nbsp;&nbsp; (&nbsp;&nbsp;) &nbsp; ผิดปกติ............................................... </label>
            </div>              
        </div>
        
        ';
        echo $output;   
        
    }

   


 }
