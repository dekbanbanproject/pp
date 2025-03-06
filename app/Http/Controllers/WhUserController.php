<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\support\Facades\Hash;
use Illuminate\support\Facades\Validator;
use App\Models\User;
use App\Models\Department;
use App\Models\Departmentsub;
use App\Models\Departmentsubsub;
use App\Models\Products_vendor;
use App\Models\Status;
use App\Models\Position;
use App\Models\Wh_log;
use App\Models\Products;
use App\Models\Products_type;
use App\Models\Product_group;
use App\Models\Product_unit;
use App\Models\Products_category;
use App\Models\Article;
use App\Models\Product_prop;
use App\Models\Product_decline;
use App\Models\Department_sub_sub;
use App\Models\Products_request;
use App\Models\Products_request_sub;
use App\Models\Leave_leader;
use App\Models\Leave_leader_sub;
use App\Models\Book_type;
use App\Models\Book_import_fam;
use App\Models\Book_signature;
use App\Models\Bookrep;
use App\Models\Wh_request_subpay;

use App\Models\Wh_stock_dep_sub;
use App\Models\Wh_stock_export;
use App\Models\Wh_stock_dep;
use App\Models\Wh_stock_sub;
use App\Models\Wh_request;
use App\Models\Wh_request_sub;
use App\Models\Article_status;
use App\Models\Air_supplies;
use App\Models\Wh_product_sub;
use App\Models\Wh_stock;
use App\Models\Wh_recieve;
use App\Models\Wh_pay;
use App\Models\Wh_pay_sub;
use App\Models\Product_method;
use App\Models\Product_buy;
use App\Models\Warehouse_inven;
use App\Models\Warehouse_inven_person;
use App\Models\Warehouse_rep;
use App\Models\Warehouse_rep_sub;
use App\Models\Warehouse_recieve;
use App\Models\Wh_config_store;
use App\Models\Wh_stock_dep_sub_export;
use App\Models\Wh_unit;
use App\Models\Wh_product;
use Illuminate\Support\Facades\File;
use DataTables;
use PDF;
use Auth;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use Intervention\Image\ImageManagerStatic as Image;

class WhUserController extends Controller
{
    public static function ref_request_number()
    {
        $year = date('Y');
        $bgs_year      = DB::table('budget_year')->where('years_now','Y')->first();
        $bg_yearnow    = $bgs_year->leave_year_id;
        $maxnumber     = DB::table('wh_request')->max('wh_request_id');
        if ($maxnumber != '' ||  $maxnumber != null) {
            $refmax = DB::table('wh_request')->where('wh_request_id', '=', $maxnumber)->first();
            if ($refmax->request_no != '' ||  $refmax->request_no != null) {
                $maxref = substr($refmax->request_no, -7) + 1;
            } else {
                $maxref = 1;
            }
            $ref = str_pad($maxref, 8, "0", STR_PAD_LEFT);
        } else {
            $ref = '00000001';
        }
        $ye = date('Y') + 543;
        $y = substr($ye, -4);
        $ynew   = substr($bg_yearnow,2,2);
        $refnumber = $ynew.''.$ref;
        return $refnumber;
    }
    public static function ref_pay_number()
    {
        $year = date('Y');
        $bgs_year      = DB::table('budget_year')->where('years_now','Y')->first();
        $bg_yearnow    = $bgs_year->leave_year_id;
        $maxnumber     = DB::table('wh_pay')->max('wh_pay_id');
        if ($maxnumber != '' ||  $maxnumber != null) {
            $refmax = DB::table('wh_pay')->where('wh_pay_id', '=', $maxnumber)->first();
            if ($refmax->pay_no != '' ||  $refmax->pay_no != null) {
                $maxref = substr($refmax->pay_no, -7) + 1;
            } else {
                $maxref = 1;
            }
            $ref = str_pad($maxref, 8, "0", STR_PAD_LEFT);
        } else {
            $ref = '00000001';
        }
        $ye = date('Y') + 543;
        $y = substr($ye, -4);
        $ynew   = substr($bg_yearnow,2,2);
        $ref_number = $ynew.''.$ref;
        return $ref_number;


    }
    public static function pt_name()
    {
        $year = date('Y');
        $bgs_year      = DB::table('budget_year')->where('years_now','Y')->first();
        $bg_yearnow    = $bgs_year->leave_year_id;
        $iduser        = Auth::user()->id;
        $ptnames         = DB::select(
            'SELECT CONCAT(b.prefix_name,a.fname,"  ",a.lname) as ptname
            FROM users a
            LEFT JOIN users_prefix b ON b.prefix_id = a.pname
            WHERE a.id = "'.$iduser.'"
        ');
        foreach ($ptnames as $key => $value) {
            $ptnames = $value->ptname;
        }
        $ptnamess = $ptnames;
        return $ptnamess;


    }
    public function wh_dashboard(Request $request)
    {
        $startdate = $request->datepicker;
        $enddate = $request->datepicker2;
        $data['q'] = $request->query('q');
        $query = User::select('users.*')
            ->where(function ($query) use ($data) {
                $query->where('pname', 'like', '%' . $data['q'] . '%');
                $query->orwhere('fname', 'like', '%' . $data['q'] . '%');
                $query->orwhere('lname', 'like', '%' . $data['q'] . '%');
                $query->orwhere('tel', 'like', '%' . $data['q'] . '%');
                $query->orwhere('username', 'like', '%' . $data['q'] . '%');
            });
        $data['users'] = $query->orderBy('id', 'DESC')->paginate(10);
        $data['department'] = Department::get();
        $data['department_sub'] = Departmentsub::get();
        $data['department_sub_sub'] = Departmentsubsub::get();
        $data['position'] = Position::get();
        $data['status'] = Status::get();
        $data['wh_stock_list'] = DB::table('wh_stock_list')->where('stock_type','=','1')->get();

        return view('wh.wh_dashboard', $data);
    }
    public function wh_plan(Request $request)
    {
        $startdate  = $request->datepicker;
        $enddate    = $request->datepicker2;
        $data['q']  = $request->query('q');
        $query = User::select('users.*')
            ->where(function ($query) use ($data) {
                $query->where('pname', 'like', '%' . $data['q'] . '%');
                $query->orwhere('fname', 'like', '%' . $data['q'] . '%');
                $query->orwhere('lname', 'like', '%' . $data['q'] . '%');
                $query->orwhere('tel', 'like', '%' . $data['q'] . '%');
                $query->orwhere('username', 'like', '%' . $data['q'] . '%');
            });
        $data['users']              = $query->orderBy('id', 'DESC')->paginate(10);
        $data['department']         = Department::get();
        $data['department_sub']     = Departmentsub::get();
        $data['department_sub_sub'] = Departmentsubsub::get();
        $data['position']           = Position::get();
        $data['status']             = Status::get();
        // $data['wh_product']         = Wh_product::get();
        $yy1                        = date('Y') + 543;
        $yy2                        = date('Y') + 542;
        $yy3                        = date('Y') + 541;

        $data['wh_product']         = DB::select(
            'SELECT a.pro_id,a.pro_num,a.pro_year,a.pro_code,a.pro_name,b.wh_type_name,c.wh_unit_name ,a.active
                ,IFNULL(d.wh_unit_pack_qty,"1") as wh_unit_pack_qty
                ,IFNULL(d.wh_unit_pack_name,c.wh_unit_name) as unit_name
                ,e.*
                ,(SELECT total_plan FROM wh_plan WHERE pro_id = a.pro_id AND wh_plan_year = "'. $yy3.'") plan_65
                ,(SELECT total_plan FROM wh_plan WHERE pro_id = a.pro_id AND wh_plan_year = "'. $yy2.'") plan_66
                ,(SELECT total_plan FROM wh_plan WHERE pro_id = a.pro_id AND wh_plan_year = "'. $yy1.'") plan_67
                FROM wh_product a
                LEFT JOIN wh_type b ON b.wh_type_id = a.pro_type
                LEFT JOIN wh_unit c ON c.wh_unit_id = a.unit_id
                LEFT JOIN wh_unit_pack d ON d.wh_unit_id = a.pro_id
                LEFT JOIN wh_plan e ON e.pro_id = a.pro_id
            WHERE a.active ="Y"
            GROUP BY a.pro_id
        ');
        $data['wh_stock_list'] = DB::table('wh_stock_list')->where('stock_type','=','1')->get();

        return view('wh.wh_plan', $data,[
            'startdate'  => $startdate,
            'enddate'    => $enddate,
        ]);
    }
    public function wh_sub_main(Request $request)
    {
        $startdate  = $request->datepicker;
        $enddate    = $request->datepicker2;
        $id         =  Auth::user()->dep_subsubtrueid;
        $data['department']         = Department::get();
        $data['department_sub']     = Departmentsub::get();
        $data['department_sub_sub'] = Departmentsubsub::get();
        $data['position']           = Position::get();
        $data['status']             = Status::get();
        // $data['wh_product']         = Wh_product::get();
        $yy1                        = date('Y') + 543;
        $yy2                        = date('Y') + 542;
        $yy3                        = date('Y') + 541;
        $bgs_year      = DB::table('budget_year')->where('years_now','Y')->first();
        $bg_yearnow    = $bgs_year->leave_year_id;

        $data['wh_stock_sub']         = DB::select(
            'SELECT e.stock_list_subid,f.DEPARTMENT_SUB_SUB_NAME,a.pro_year,a.pro_id,a.pro_code,a.pro_name,b.wh_type_name,c.wh_unit_name,g.one_price
                ,(SELECT SUM(s.qty_pay) FROM wh_stock_dep_sub s LEFT JOIN wh_stock_dep sm ON sm.wh_stock_dep_id = s.wh_stock_dep_id WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$id.'" AND sm.active ="REPEXPORT") AS stock_rep
                ,(SELECT SUM(s.one_price) FROM wh_stock_dep_sub s LEFT JOIN wh_stock_dep sm ON sm.wh_stock_dep_id = s.wh_stock_dep_id WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$id.'" AND sm.active ="REPEXPORT") AS sum_one_price
                ,(SELECT SUM(s.total_price) FROM wh_stock_dep_sub s LEFT JOIN wh_stock_dep sm ON sm.wh_stock_dep_id = s.wh_stock_dep_id WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$id.'" AND sm.active ="REPEXPORT") AS sum_stock_price
                ,(SELECT SUM(qty_pay_sub) FROM wh_pay_sub WHERE pro_id = e.pro_id AND stock_list_subid = "'.$id.'") as stock_pay
                ,a.active ,IFNULL(d.wh_unit_pack_qty,"1") as wh_unit_pack_qty ,IFNULL(d.wh_unit_pack_name,c.wh_unit_name) as unit_name
                FROM wh_stock_sub e
                LEFT JOIN wh_product a ON a.pro_id = e.pro_id
                LEFT JOIN wh_type b ON b.wh_type_id = a.pro_type
                LEFT JOIN wh_unit c ON c.wh_unit_id = a.unit_id
                LEFT JOIN wh_unit_pack d ON d.wh_unit_id = a.pro_id
                LEFT JOIN wh_stock_dep_sub g ON g.pro_id = a.pro_id AND g.stock_list_subid = e.stock_list_subid
                LEFT JOIN department_sub_sub f ON f.DEPARTMENT_SUB_SUB_ID = e.stock_list_subid
                WHERE a.active ="Y"
                AND e.stock_list_subid ="'.$id.'" AND e.stock_year ="'.$bg_yearnow.'"
                GROUP BY e.pro_id
        ');

        $data['wh_stock_list'] = DB::table('wh_stock_list')->where('stock_type','=','1')->get();
        // $data_main             = DB::table('department_sub_sub')->where('DEPARTMENT_SUB_SUB_ID','=',$id)->first();
        $data_main             = DB::select(
            'SELECT a.DEPARTMENT_NAME,b.DEPARTMENT_SUB_NAME,c.DEPARTMENT_SUB_SUB_NAME
            FROM department a
            LEFT JOIN department_sub b ON b.DEPARTMENT_ID = a.DEPARTMENT_ID
            LEFT JOIN department_sub_sub c ON c.DEPARTMENT_SUB_ID = b.DEPARTMENT_SUB_ID
            WHERE c.DEPARTMENT_SUB_SUB_ID = "'.$id.'"
        ');
        foreach ($data_main as $key => $value) {
            $data['stock_bigname']    = $value->DEPARTMENT_NAME;
            $data['stock_name']       = $value->DEPARTMENT_SUB_SUB_NAME;
        }

        return view('wh_user.wh_sub_main', $data,[
            'startdate'   => $startdate,
            'enddate'     => $enddate,
            'bg_yearnow'  => $bg_yearnow,
        ]);
    }
    public function wh_sub_report(Request $request)
    {
        $startdate                 = $request->startdate;
        $enddate                   = $request->enddate;
        $datenow                   = date('Y-m-d');
        $data['date_now']          = date('Y-m-d');
        $months                    = date('m');
        $year                      = date('Y');
        $newday                     = date('Y-m-d', strtotime($datenow . ' -5 Day')); //ย้อนหลัง 1 วัน
        $newweek                    = date('Y-m-d', strtotime($datenow . ' -1 week')); //ย้อนหลัง 1 สัปดาห์
        $newDate                    = date('Y-m-d', strtotime($datenow . ' -2 months')); //ย้อนหลัง 2 เดือน
        $newyear                    = date('Y-m-d', strtotime($datenow . ' -1 year')); //ย้อนหลัง 1 ปี

        $data['department']         = Department::get();
        $data['department_sub']     = Departmentsub::get();
        $data['department_sub_sub'] = Departmentsubsub::get();
        $data['position']           = Position::get();
        $data['status']             = Status::get();
        $data['air_supplies']       = Air_supplies::where('active','=','Y')->get();
        $data['wh_stock_list']      = DB::table('wh_stock_list')->where('stock_type','1')->get();
        $dep_subsubtrueid           =  Auth::user()->dep_subsubtrueid;
        $data['m']                  = date('H');
        $data['mm']                 = date('H:m:s');
        $data['datefull']           = date('Y-m-d H:m:s');
        $data['monthsnew']          = substr($months,1,2);

        $yy1                        = date('Y') + 543;
        $yy2                        = date('Y') + 542;
        $yy3                        = date('Y') + 541;
        $bgs_year      = DB::table('budget_year')->where('years_now','Y')->first();
        $bg_yearnow    = $bgs_year->leave_year_id;
        // dd($startdate);
        // ,(SELECT s.one_price FROM wh_stock_dep_sub s LEFT JOIN wh_request sm ON sm.wh_request_id = s.wh_request_id
        // WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$dep_subsubtrueid.'" AND sm.active ="REPEXPORT" AND sm.request_date BETWEEN "'.$newDate.'" AND "'.$datenow.'") AS request_one_price
        // AND sm.request_date BETWEEN "'.$newDate.'" AND "'.$datenow.'"
        if ($startdate =='') {
            $data['wh_stock_sub']         = DB::select(
                'SELECT e.wh_stock_sub_id,e.stock_list_subid,w.wh_request_id,w.request_date,f.DEPARTMENT_SUB_SUB_NAME,a.pro_year,a.pro_id,a.pro_code,a.pro_name,b.wh_type_name,c.wh_unit_name,g.one_price
                    ,(SELECT SUM(s.qty) FROM wh_stock_dep_sub s LEFT JOIN wh_request sm ON sm.wh_request_id = s.wh_request_id
                    WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$dep_subsubtrueid.'" AND sm.active ="REPEXPORT") AS request_qty
                    ,(SELECT SUM(s.qty_pay) FROM wh_stock_dep_sub s LEFT JOIN wh_request sm ON sm.wh_request_id = s.wh_request_id
                    WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$dep_subsubtrueid.'" AND sm.active ="REPEXPORT") AS request_qty_pay
                    ,"" AS request_one_price
                    ,(SELECT SUM(s.total_price) FROM wh_stock_dep_sub s LEFT JOIN wh_request sm ON sm.wh_request_id = s.wh_request_id
                    WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$dep_subsubtrueid.'" AND sm.active ="REPEXPORT") AS request_total_price

                    ,(SELECT SUM(s.qty_pay) FROM wh_stock_dep_sub s LEFT JOIN wh_stock_dep sm ON sm.wh_stock_dep_id = s.wh_stock_dep_id WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$dep_subsubtrueid.'" AND sm.active ="REPEXPORT") AS stock_rep

                    ,(SELECT SUM(s.one_price) FROM wh_stock_dep_sub s LEFT JOIN wh_stock_dep sm ON sm.wh_stock_dep_id = s.wh_stock_dep_id WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$dep_subsubtrueid.'" AND sm.active ="REPEXPORT") AS sum_one_price
                    ,(SELECT SUM(s.total_price) FROM wh_stock_dep_sub s LEFT JOIN wh_stock_dep sm ON sm.wh_stock_dep_id = s.wh_stock_dep_id WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$dep_subsubtrueid.'" AND sm.active ="REPEXPORT") AS sum_stock_price
                    ,(SELECT SUM(qty_pay_sub) FROM wh_pay_sub WHERE pro_id = e.pro_id AND stock_list_subid = "'.$dep_subsubtrueid.'" AND pay_year ="'.$bg_yearnow.'") as stock_pay

                    ,(SELECT SUM(total_price) FROM wh_pay_sub WHERE pro_id = e.pro_id AND stock_list_subid = "'.$dep_subsubtrueid.'" AND pay_year ="'.$bg_yearnow.'") as stock_pay_total_price
                    ,a.active ,IFNULL(d.wh_unit_pack_qty,"1") as wh_unit_pack_qty ,IFNULL(d.wh_unit_pack_name,c.wh_unit_name) as unit_name
                    FROM wh_stock_sub e
                    LEFT JOIN wh_product a ON a.pro_id = e.pro_id
                    LEFT JOIN wh_type b ON b.wh_type_id = a.pro_type
                    LEFT JOIN wh_unit c ON c.wh_unit_id = a.unit_id
                    LEFT JOIN wh_unit_pack d ON d.wh_unit_id = a.pro_id
                    LEFT JOIN wh_stock_dep_sub g ON g.pro_id = a.pro_id AND g.stock_list_subid = e.stock_list_subid
                    LEFT JOIN wh_request w ON w.wh_request_id = g.wh_request_id
                    LEFT JOIN department_sub_sub f ON f.DEPARTMENT_SUB_SUB_ID = e.stock_list_subid
                    WHERE a.active ="Y"
                    AND e.stock_list_subid ="'.$dep_subsubtrueid.'" AND e.stock_year ="'.$bg_yearnow.'"
                    GROUP BY e.pro_id
            ');
            // AND w.request_date BETWEEN "'.$newDate.'" AND "'.$datenow.'"
            // $data['wh_stock_sub']         = DB::select(
            //     'SELECT e.stock_list_subid,f.DEPARTMENT_SUB_SUB_NAME,a.pro_year,a.pro_id,a.pro_code,a.pro_name,b.wh_type_name,c.wh_unit_name,g.one_price
            //         ,(SELECT SUM(s.qty_pay) FROM wh_stock_dep_sub s LEFT JOIN wh_stock_dep sm ON sm.wh_stock_dep_id = s.wh_stock_dep_id WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$dep_subsubtrueid.'" AND sm.active ="REPEXPORT") AS stock_rep
            //         ,(SELECT SUM(s.one_price) FROM wh_stock_dep_sub s LEFT JOIN wh_stock_dep sm ON sm.wh_stock_dep_id = s.wh_stock_dep_id WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$dep_subsubtrueid.'" AND sm.active ="REPEXPORT") AS sum_one_price
            //         ,(SELECT SUM(s.total_price) FROM wh_stock_dep_sub s LEFT JOIN wh_stock_dep sm ON sm.wh_stock_dep_id = s.wh_stock_dep_id WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$dep_subsubtrueid.'" AND sm.active ="REPEXPORT") AS sum_stock_price
            //         ,(SELECT SUM(qty_pay_sub) FROM wh_pay_sub WHERE pro_id = e.pro_id AND stock_list_subid = "'.$dep_subsubtrueid.'") as stock_pay
            //         ,a.active ,IFNULL(d.wh_unit_pack_qty,"1") as wh_unit_pack_qty ,IFNULL(d.wh_unit_pack_name,c.wh_unit_name) as unit_name
            //         FROM wh_stock_sub e
            //         LEFT JOIN wh_product a ON a.pro_id = e.pro_id
            //         LEFT JOIN wh_type b ON b.wh_type_id = a.pro_type
            //         LEFT JOIN wh_unit c ON c.wh_unit_id = a.unit_id
            //         LEFT JOIN wh_unit_pack d ON d.wh_unit_id = a.pro_id
            //         LEFT JOIN wh_stock_dep_sub g ON g.pro_id = a.pro_id AND g.stock_list_subid = e.stock_list_subid
            //         LEFT JOIN department_sub_sub f ON f.DEPARTMENT_SUB_SUB_ID = e.stock_list_subid
            //         WHERE a.active ="Y"
            //         AND e.stock_list_subid ="'.$dep_subsubtrueid.'" AND e.stock_year ="'.$bg_yearnow.'"
            //         GROUP BY e.pro_id
            // ');
            // ,(SELECT one_price FROM wh_pay_sub WHERE pro_id = e.pro_id AND stock_list_subid = "'.$dep_subsubtrueid.'" AND pay_year ="'.$bg_yearnow.'") as stock_pay_one_price
        } else {


            $data['wh_stock_sub']         = DB::select(
                'SELECT e.wh_stock_sub_id,e.stock_list_subid,w.wh_request_id,w.request_date,f.DEPARTMENT_SUB_SUB_NAME,a.pro_year,a.pro_id,a.pro_code,a.pro_name,b.wh_type_name,c.wh_unit_name,g.one_price
                    ,(SELECT SUM(s.qty) FROM wh_stock_dep_sub s LEFT JOIN wh_request sm ON sm.wh_request_id = s.wh_request_id
                    WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$dep_subsubtrueid.'" AND sm.active ="REPEXPORT" AND sm.request_date BETWEEN "'.$startdate.'" AND "'.$enddate.'") AS request_qty
                    ,(SELECT SUM(s.qty_pay) FROM wh_stock_dep_sub s LEFT JOIN wh_request sm ON sm.wh_request_id = s.wh_request_id
                    WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$dep_subsubtrueid.'" AND sm.active ="REPEXPORT" AND sm.request_date BETWEEN "'.$startdate.'" AND "'.$enddate.'") AS request_qty_pay
                   ,"" AS request_one_price
                    ,(SELECT SUM(s.total_price) FROM wh_stock_dep_sub s LEFT JOIN wh_request sm ON sm.wh_request_id = s.wh_request_id
                    WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$dep_subsubtrueid.'" AND sm.active ="REPEXPORT" AND sm.request_date BETWEEN "'.$startdate.'" AND "'.$enddate.'") AS request_total_price

                    ,(SELECT SUM(s.qty_pay) FROM wh_stock_dep_sub s LEFT JOIN wh_stock_dep sm ON sm.wh_stock_dep_id = s.wh_stock_dep_id WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$dep_subsubtrueid.'" AND sm.active ="REPEXPORT") AS stock_rep
                    ,(SELECT SUM(s.one_price) FROM wh_stock_dep_sub s LEFT JOIN wh_stock_dep sm ON sm.wh_stock_dep_id = s.wh_stock_dep_id WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$dep_subsubtrueid.'" AND sm.active ="REPEXPORT") AS sum_one_price
                    ,(SELECT SUM(s.total_price) FROM wh_stock_dep_sub s LEFT JOIN wh_stock_dep sm ON sm.wh_stock_dep_id = s.wh_stock_dep_id WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$dep_subsubtrueid.'" AND sm.active ="REPEXPORT") AS sum_stock_price

                    ,(SELECT SUM(pp.qty_pay_sub) FROM wh_pay_sub pp LEFT JOIN wh_pay ppp ON ppp.wh_pay_id = pp.wh_pay_id WHERE pp.pro_id = e.pro_id AND ppp.pay_date BETWEEN "'.$startdate.'" AND "'.$enddate.'" AND pp.stock_list_subid = "'.$dep_subsubtrueid.'" AND pp.pay_year ="'.$bg_yearnow.'") as stock_pay

                    ,(SELECT SUM(pp.total_price) FROM wh_pay_sub pp LEFT JOIN wh_pay ppp ON ppp.wh_pay_id = pp.wh_pay_id WHERE pp.pro_id = e.pro_id AND ppp.pay_date BETWEEN "'.$startdate.'" AND "'.$enddate.'" AND pp.stock_list_subid = "'.$dep_subsubtrueid.'" AND pp.pay_year ="'.$bg_yearnow.'") as stock_pay_total_price

                    ,a.active ,IFNULL(d.wh_unit_pack_qty,"1") as wh_unit_pack_qty ,IFNULL(d.wh_unit_pack_name,c.wh_unit_name) as unit_name
                    FROM wh_stock_sub e
                    LEFT JOIN wh_product a ON a.pro_id = e.pro_id
                    LEFT JOIN wh_type b ON b.wh_type_id = a.pro_type
                    LEFT JOIN wh_unit c ON c.wh_unit_id = a.unit_id
                    LEFT JOIN wh_unit_pack d ON d.wh_unit_id = a.pro_id
                    LEFT JOIN wh_stock_dep_sub g ON g.pro_id = a.pro_id AND g.stock_list_subid = e.stock_list_subid
                    LEFT JOIN wh_request w ON w.wh_request_id = g.wh_request_id
                    LEFT JOIN department_sub_sub f ON f.DEPARTMENT_SUB_SUB_ID = e.stock_list_subid
                    WHERE a.active ="Y" AND w.request_date BETWEEN "'.$startdate.'" AND "'.$enddate.'"
                    AND e.stock_list_subid ="'.$dep_subsubtrueid.'" AND e.stock_year ="'.$bg_yearnow.'"
                    GROUP BY e.pro_id
            ');

            // ,(SELECT s.one_price FROM wh_stock_dep_sub s LEFT JOIN wh_request sm ON sm.wh_request_id = s.wh_request_id
            // WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$dep_subsubtrueid.'" AND sm.active ="REPEXPORT" AND sm.request_date BETWEEN "'.$startdate.'" AND "'.$enddate.'") AS request_one_price

            // $data['wh_stock_sub']         = DB::select(
            //     'SELECT e.wh_stock_sub_id,e.stock_list_subid,f.DEPARTMENT_SUB_SUB_NAME,a.pro_year,a.pro_id,a.pro_code,a.pro_name,b.wh_type_name,c.wh_unit_name,g.one_price
            //         ,(SELECT SUM(s.qty) FROM wh_stock_dep_sub s LEFT JOIN wh_request sm ON sm.wh_request_id = s.wh_request_id WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$dep_subsubtrueid.'" AND sm.active ="REPEXPORT") AS request_qty
            //         ,(SELECT s.one_price FROM wh_stock_dep_sub s LEFT JOIN wh_request sm ON sm.wh_request_id = s.wh_request_id WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$dep_subsubtrueid.'" AND sm.active ="REPEXPORT") AS request_one_price
            //         ,(SELECT SUM(s.total_price) FROM wh_stock_dep_sub s LEFT JOIN wh_request sm ON sm.wh_request_id = s.wh_request_id WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$dep_subsubtrueid.'" AND sm.active ="REPEXPORT") AS request_total_price

            //         ,(SELECT SUM(qty) FROM wh_pay_sub WHERE pro_id = e.pro_id AND stock_list_subid = "'.$dep_subsubtrueid.'" AND pay_year ="'.$bg_yearnow.'") as stock_pay
            //         ,(SELECT one_price FROM wh_pay_sub WHERE pro_id = e.pro_id AND stock_list_subid = "'.$dep_subsubtrueid.'" AND pay_year ="'.$bg_yearnow.'") as stock_pay_one_price
            //         ,(SELECT SUM(total_price) FROM wh_pay_sub WHERE pro_id = e.pro_id AND stock_list_subid = "'.$dep_subsubtrueid.'" AND pay_year ="'.$bg_yearnow.'") as stock_pay_total_price

            //         ,a.active
            //         FROM wh_stock_sub e
            //         LEFT JOIN wh_product a ON a.pro_id = e.pro_id
            //         LEFT JOIN wh_type b ON b.wh_type_id = a.pro_type
            //         LEFT JOIN wh_unit c ON c.wh_unit_id = a.unit_id
            //         LEFT JOIN wh_unit_pack d ON d.wh_unit_id = a.pro_id
            //         LEFT JOIN wh_stock_dep_sub g ON g.pro_id = a.pro_id AND g.stock_list_subid = e.stock_list_subid
            //         LEFT JOIN department_sub_sub f ON f.DEPARTMENT_SUB_SUB_ID = e.stock_list_subid
            //         WHERE a.active ="Y"
            //         AND e.stock_list_subid ="'.$dep_subsubtrueid.'" AND e.stock_year ="'.$bg_yearnow.'"
            //         GROUP BY e.pro_id
            // ');
        }


        // ,(SELECT SUM(s.qty_pay) FROM wh_stock_dep_sub s LEFT JOIN wh_stock_dep sm ON sm.wh_stock_dep_id = s.wh_stock_dep_id WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$dep_subsubtrueid.'" AND sm.active ="REPEXPORT") AS stock_rep

        $data_main             = DB::select(
            'SELECT a.DEPARTMENT_NAME,b.DEPARTMENT_SUB_NAME,c.DEPARTMENT_SUB_SUB_NAME
            FROM department a
            LEFT JOIN department_sub b ON b.DEPARTMENT_ID = a.DEPARTMENT_ID
            LEFT JOIN department_sub_sub c ON c.DEPARTMENT_SUB_ID = b.DEPARTMENT_SUB_ID
            WHERE c.DEPARTMENT_SUB_SUB_ID = "'.$dep_subsubtrueid.'"
        ');
        foreach ($data_main as $key => $value) {
            $data['stock_bigname']    = $value->DEPARTMENT_NAME;
            $data['stock_name']       = $value->DEPARTMENT_SUB_SUB_NAME;
        }

        return view('wh_user.wh_sub_report',$data,[
            'startdate'     => $startdate,
            'enddate'       => $enddate,
            'bg_yearnow'    => $bg_yearnow,
        ]);
    }
    public function wh_sub_main_rp(Request $request)
    {
        $startdate                 = $request->startdate;
        $enddate                   = $request->enddate;
        $datenow                   = date('Y-m-d');
        $data['date_now']          = date('Y-m-d');
        $months                    = date('m');
        $year                      = date('Y');
        $newday                     = date('Y-m-d', strtotime($datenow . ' -5 Day')); //ย้อนหลัง 1 วัน
        $newweek                    = date('Y-m-d', strtotime($datenow . ' -1 week')); //ย้อนหลัง 1 สัปดาห์
        $newDate                    = date('Y-m-d', strtotime($datenow . ' -2 months')); //ย้อนหลัง 1 เดือน
        $newyear                    = date('Y-m-d', strtotime($datenow . ' -1 year')); //ย้อนหลัง 1 ปี

        $data['department']         = Department::get();
        $data['department_sub']     = Departmentsub::get();
        $data['department_sub_sub'] = Departmentsubsub::get();
        $data['position']           = Position::get();
        $data['status']             = Status::get();
        $data['air_supplies']       = Air_supplies::where('active','=','Y')->get();
        $data['wh_stock_list']      = DB::table('wh_stock_list')->where('stock_type','1')->get();
        $userid                     =  Auth::user()->id;
        $dep_subsubtrueid           =  Auth::user()->dep_subsubtrueid;
        $data['m']                  = date('H');
        $data['mm']                 = date('H:m:s');
        $data['datefull']           = date('Y-m-d H:m:s');
        $data['monthsnew']          = substr($months,1,2);

        $yy1                        = date('Y') + 543;
        $yy2                        = date('Y') + 542;
        $yy3                        = date('Y') + 541;
        $bgs_year      = DB::table('budget_year')->where('years_now','Y')->first();
        $bg_yearnow    = $bgs_year->leave_year_id;

        $user_store      = Wh_config_store::where('user_id',$userid)->first();

        // $data['wh_product']         = DB::select(
        //     'SELECT a.pro_id,a.pro_num,a.pro_year,a.pro_code,a.pro_name,b.wh_type_name,c.wh_unit_name ,e.stock_qty,e.stock_rep,e.stock_pay,e.stock_total,e.stock_price,a.active
        //         ,IFNULL(d.wh_unit_pack_qty,"1") as wh_unit_pack_qty
        //         ,IFNULL(d.wh_unit_pack_name,c.wh_unit_name) as unit_name,f.stock_list_name
        //         FROM wh_stock e
        //         LEFT JOIN wh_product a ON a.pro_id = e.pro_id
        //         LEFT JOIN wh_type b ON b.wh_type_id = a.pro_type
        //         LEFT JOIN wh_unit c ON c.wh_unit_id = a.unit_id
        //         LEFT JOIN wh_unit_pack d ON d.wh_unit_id = a.pro_id
        //         LEFT JOIN wh_stock_list f ON f.stock_list_id = e.stock_list_id
        //     WHERE a.active ="Y" AND e.stock_year ="'.$bg_yearnow.'"
        //     GROUP BY e.pro_id
        // ');
        $data['wh_stock_list']      = DB::table('wh_stock_list')->where('stock_type','=','1')->get();


        if ($startdate == '') {
            $check_config    = Wh_config_store::where('user_id',$userid)->count();
            // dd($check_config);
            if ($check_config > 0) {
                $data['wh_request']         = DB::select(
                    'SELECT r.wh_request_id,r.year,r.request_date,r.repin_date,r.request_time,r.request_no,r.stock_list_id,r.active ,s.stock_list_name
                    ,(SELECT DEPARTMENT_SUB_SUB_NAME FROM department_sub_sub WHERE DEPARTMENT_SUB_SUB_ID = r.stock_list_subid) as DEPARTMENT_SUB_SUB_NAME
                    ,r.request_po,concat(u.fname," ",u.lname) as ptname ,r.total_price
                    ,(SELECT concat(uu.fname," ",uu.lname) FROM users uu LEFT JOIN wh_stock_export w ON w.user_export_send = uu.id WHERE wh_request_id = r.wh_request_id) as ptname_send
                    ,(SELECT concat(uuu.fname," ",uuu.lname) FROM users uuu LEFT JOIN wh_stock_export ww ON ww.user_export_rep = uuu.id WHERE wh_request_id = r.wh_request_id) as ptname_rep
                    FROM wh_request r
                    LEFT JOIN wh_stock_list s ON s.stock_list_id = r.stock_list_id
                    LEFT JOIN users u ON u.id = r.user_request
                    WHERE r.stock_list_subid IN(SELECT stock_list_subid FROM wh_config_store WHERE user_id = "'.$userid.'")
                   
                    ORDER BY r.wh_request_id DESC LIMIT 50
                ');
                // AND r.active NOT IN("PAYNOW","CONFIRMPAYNOW","ADDPAYNOW")
                $data['wh_count']           = DB::table('wh_request')->where('stock_list_subid','=',$dep_subsubtrueid)->whereNotIn('active',['REPEXPORT'])->count();
                // ="'.$dep_subsubtrueid.'" AND r.user_request
                // $datawh_count                  = DB::select('SELECT COUNT(wh_request_id) as Cwh_request_id FROM wh_request WHERE active ="REPEXPORT" AND user_request IN(SELECT user_id FROM wh_config_store WHERE user_id = "'.$userid.'")');
                // foreach ($datawh_count as $key => $val_1) {
                //     $data['wh_count']           = $val_1->Cwh_request_id;
                // }
                // ->where('stock_list_subid','=',$dep_subsubtrueid)->whereNotIn('active',['REPEXPORT'])->count();
            } else {
                $data['wh_request']         = DB::select(
                    'SELECT r.wh_request_id,r.year,r.request_date,r.repin_date,r.request_time,r.request_no,r.stock_list_id,r.active ,s.stock_list_name
                    ,(SELECT DEPARTMENT_SUB_SUB_NAME FROM department_sub_sub WHERE DEPARTMENT_SUB_SUB_ID = r.stock_list_subid) as DEPARTMENT_SUB_SUB_NAME
                    ,r.request_po,concat(u.fname," ",u.lname) as ptname ,r.total_price
                    ,(SELECT concat(uu.fname," ",uu.lname) FROM users uu LEFT JOIN wh_stock_export w ON w.user_export_send = uu.id WHERE wh_request_id = r.wh_request_id) as ptname_send
                    ,(SELECT concat(uuu.fname," ",uuu.lname) FROM users uuu LEFT JOIN wh_stock_export ww ON ww.user_export_rep = uuu.id WHERE wh_request_id = r.wh_request_id) as ptname_rep
                    FROM wh_request r
                    LEFT JOIN wh_stock_list s ON s.stock_list_id = r.stock_list_id
                    LEFT JOIN users u ON u.id = r.user_request
                    WHERE r.stock_list_subid ="'.$dep_subsubtrueid.'" 
                    ORDER BY r.wh_request_id DESC LIMIT 50
                ');
                $data['wh_count']           = DB::table('wh_request')->where('stock_list_subid','=',$dep_subsubtrueid)->whereNotIn('active',['REPEXPORT'])->count();
            }
            // AND r.active NOT IN("PAYNOW","CONFIRMPAYNOW","ADDPAYNOW")
        } else {
            $check_config    = Wh_config_store::where('user_id',$userid)->count();
            if ($check_config > 0) {
                $data['wh_request']         = DB::select(
                    'SELECT r.wh_request_id,r.year,r.request_date,r.repin_date,r.request_time,r.request_no,r.stock_list_id,r.active ,s.stock_list_name
                    ,(SELECT DEPARTMENT_SUB_SUB_NAME FROM department_sub_sub WHERE DEPARTMENT_SUB_SUB_ID = r.stock_list_subid) as DEPARTMENT_SUB_SUB_NAME
                    ,r.request_po,concat(u.fname," ",u.lname) as ptname ,r.total_price
                    ,(SELECT concat(uu.fname," ",uu.lname) FROM users uu LEFT JOIN wh_stock_export w ON w.user_export_send = uu.id WHERE wh_request_id = r.wh_request_id) as ptname_send
                    ,(SELECT concat(uuu.fname," ",uuu.lname) FROM users uuu LEFT JOIN wh_stock_export ww ON ww.user_export_rep = uuu.id WHERE wh_request_id = r.wh_request_id) as ptname_rep
                    FROM wh_request r
                    LEFT JOIN wh_stock_list s ON s.stock_list_id = r.stock_list_id
                    LEFT JOIN users u ON u.id = r.user_request
                    WHERE r.stock_list_subid IN(SELECT stock_list_subid FROM wh_config_store WHERE user_id = "'.$userid.'")
                    AND r.request_date BETWEEN "'.$startdate.'" AND "'.$enddate.'" 
                    ORDER BY r.wh_request_id DESC
                ');
                // $datawh_count                  = DB::select('SELECT COUNT(wh_request_id) as Cwh_request_id FROM wh_request WHERE active ="REPEXPORT" AND user_request = "'.$userid.'"');
                // foreach ($datawh_count as $key => $val_1) {
                //     $data['wh_count']           = $val_1->Cwh_request_id;
                // }
                $data['wh_count']           = DB::table('wh_request')->where('stock_list_subid','=',$dep_subsubtrueid)->whereNotIn('active',['REPEXPORT'])->count();
            } else {
                $data['wh_request']         = DB::select(
                    'SELECT r.wh_request_id,r.year,r.request_date,r.repin_date,r.request_time,r.request_no,r.stock_list_id,r.active ,s.stock_list_name
                    ,(SELECT DEPARTMENT_SUB_SUB_NAME FROM department_sub_sub WHERE DEPARTMENT_SUB_SUB_ID = r.stock_list_subid) as DEPARTMENT_SUB_SUB_NAME
                    ,r.request_po,concat(u.fname," ",u.lname) as ptname ,r.total_price
                    ,(SELECT concat(uu.fname," ",uu.lname) FROM users uu LEFT JOIN wh_stock_export w ON w.user_export_send = uu.id WHERE wh_request_id = r.wh_request_id) as ptname_send
                    ,(SELECT concat(uuu.fname," ",uuu.lname) FROM users uuu LEFT JOIN wh_stock_export ww ON ww.user_export_rep = uuu.id WHERE wh_request_id = r.wh_request_id) as ptname_rep
                    FROM wh_request r
                    LEFT JOIN wh_stock_list s ON s.stock_list_id = r.stock_list_id
                    LEFT JOIN users u ON u.id = r.user_request
                    WHERE r.stock_list_subid ="'.$dep_subsubtrueid.'" 
                    AND r.request_date BETWEEN "'.$startdate.'" AND "'.$enddate.'"
                    ORDER BY r.wh_request_id DESC
                ');
                $data['wh_count']           = DB::table('wh_request')->where('stock_list_subid','=',$dep_subsubtrueid)->whereNotIn('active',['REPEXPORT'])->count();
            }


        }

        // dd($data['wh_count']);

        // ,(SELECT SUM(total_price) FROM wh_request_sub WHERE wh_request_id = r.wh_request_id) as total_price
        $data_main             = DB::select(
            'SELECT a.DEPARTMENT_NAME,b.DEPARTMENT_SUB_NAME,c.DEPARTMENT_SUB_SUB_NAME
            FROM department a
            LEFT JOIN department_sub b ON b.DEPARTMENT_ID = a.DEPARTMENT_ID
            LEFT JOIN department_sub_sub c ON c.DEPARTMENT_SUB_ID = b.DEPARTMENT_SUB_ID
            WHERE c.DEPARTMENT_SUB_SUB_ID = "'.$dep_subsubtrueid.'"
        ');
        foreach ($data_main as $key => $value) {
            $data['stock_bigname']    = $value->DEPARTMENT_NAME;
            $data['stock_name']       = $value->DEPARTMENT_SUB_SUB_NAME;
        }

        return view('wh_user.wh_sub_main_rp',$data,[
            'startdate'     => $startdate,
            'enddate'       => $enddate,
            'bg_yearnow'    => $bg_yearnow,
        ]);
    }
    public function wh_sub_main_rprint(Request $request, $id)
    {
        $data['department']         = Department::get();
        $data['department_sub']     = Departmentsub::get();
        $data['department_sub_sub'] = Departmentsubsub::get();
        $data['position']           = Position::get();
        $data['status']             = Status::get();
        $data['air_supplies']       = Air_supplies::where('active','=','Y')->get();
        $data['wh_stock_list']      = DB::table('wh_stock_list')->where('stock_type','1')->get();
        $dep_subsubtrueid           =  Auth::user()->dep_subsubtrueid;
        $data['m']                  = date('H');
        $data['mm']                 = date('H:m:s');
        $data['datefull']           = date('Y-m-d H:m:s');
        $org                        = DB::table('orginfo')->where('orginfo_id', '=', 1)
                                    ->leftjoin('users', 'users.id', '=', 'orginfo.orginfo_manage_id')
                                    ->leftjoin('users_prefix', 'users_prefix.prefix_code', '=', 'users.pname')
                                    ->first();
        $rong                       = $org->prefix_name . ' ' . $org->fname . '  ' . $org->lname;
        $orgpo                      = DB::table('orginfo')->where('orginfo_id', '=', 1)
                                    ->leftjoin('users', 'users.id', '=', 'orginfo.orginfo_po_id')
                                    ->leftjoin('users_prefix', 'users_prefix.prefix_code', '=', 'users.pname')
                                    ->first();
        $po                         = $orgpo->prefix_name . ' ' . $orgpo->fname . '  ' . $orgpo->lname;
        $rong                       = DB::table('orginfo')->where('orginfo_id', '=', 1)
                                    ->leftjoin('users', 'users.id', '=', 'orginfo.orginfo_manage_id')
                                    ->leftjoin('users_prefix', 'users_prefix.prefix_code', '=', 'users.pname')
                                    ->first();
        $rong_bo                    = $rong->prefix_name . ' ' . $rong->fname . '  ' . $rong->lname;
        $sigrong_                   = $rong->signature; //หัวหน้าบริหาร

        $bgs_year                   = DB::table('budget_year')->where('years_now','Y')->first();
        $bg_yearnow                 = $bgs_year->leave_year_id;
        $iduser                     = Auth::user()->id;
        $count_                     = DB::table('users')->where('id', '=', $iduser)->count();
        $wh_request                 = DB::table('wh_request')
                                    ->leftjoin('department_sub_sub', 'department_sub_sub.DEPARTMENT_SUB_SUB_ID', '=', 'wh_request.stock_list_subid')
                                    ->leftjoin('wh_stock_list', 'wh_stock_list.stock_list_id', '=', 'wh_request.stock_list_id')
                                    ->where('wh_request_id',$id)->first();
        $data_uer                   = DB::table('wh_request')
                                    ->leftjoin('wh_stock_list', 'wh_stock_list.stock_list_id', '=', 'wh_request.stock_list_id')
                                    ->leftjoin('users', 'users.id', '=', 'wh_stock_list.userid')
                                    ->leftjoin('users_prefix', 'users_prefix.prefix_id', '=', 'users.pname')
                                    ->leftjoin('position', 'position.POSITION_ID', '=', 'users.position_id')
                                    ->leftjoin('user_level', 'user_level.user_level_id', '=', 'users.user_level_id')
                                    ->where('wh_request_id',$id)->first();
        $data_uerreq                = DB::table('wh_request')
                                    ->leftjoin('wh_stock_list', 'wh_stock_list.stock_list_id', '=', 'wh_request.stock_list_id')
                                    ->leftjoin('users', 'users.id', '=', 'wh_request.user_request')
                                    ->leftjoin('users_prefix', 'users_prefix.prefix_id', '=', 'users.pname')
                                    ->leftjoin('position', 'position.POSITION_ID', '=', 'users.position_id')
                                    ->leftjoin('department_sub_sub', 'department_sub_sub.DEPARTMENT_SUB_SUB_ID', '=', 'users.dep_subsubtrueid')
                                    ->where('wh_request_id',$id)->first();
                            // dd($data_uerreq->LEADER_ID);
        $data_head                  = DB::table('users')
                                    ->leftjoin('users_prefix', 'users_prefix.prefix_id', '=', 'users.pname')
                                    ->leftjoin('department_sub_sub', 'department_sub_sub.DEPARTMENT_SUB_SUB_ID', '=', 'users.dep_subsubtrueid')
                                    ->where('id',$data_uerreq->LEADER_ID)->first();

        $data_userpay               = DB::table('users')
                                    ->leftjoin('users_prefix', 'users_prefix.prefix_id', '=', 'users.pname')
                                    ->leftjoin('wh_stock_list', 'wh_stock_list.userpay', '=', 'users.id')
                                    ->where('stock_list_id',$wh_request->stock_list_id)->first();
        if ($count_ != 0) {
            $signa = DB::table('users')->where('id', '=', $iduser)->leftjoin('users_prefix', 'users_prefix.prefix_id', '=', 'users.pname')->first();
                // ->orwhere('com_repaire_no','=',$dataedit->com_repaire_no)
            $ptname   = $signa->prefix_name . '' . $signa->fname . '  ' . $signa->lname;
            $position = $signa->position_name;
            $siguser  = $signa->signature; //ผู้รองขอ
            $sigrong = $sigrong_;
            $sigstaff = '';
            $sighn = '';
            $sigpo = '';
        } else {
            $sigrong = '';
            $siguser = '';
            $sigstaff = '';
            $sighn = '';
            $sigpo = '';
        }
        $data['request_sub']     = DB::select(
            'SELECT *
            FROM wh_request_sub
            WHERE wh_request_id = "'.$id.'" AND qty > 0
            GROUP BY pro_id
            ORDER BY pro_id ASC
        ');

        $PAGE_COUNT      = DB::table('wh_request_sub')->where('wh_request_id', '=', $id)->count();
        $pdf = PDF::loadView('wh_user.wh_sub_main_rprint',$data,[
            'bg_yearnow'   => $bg_yearnow,
            'siguser'      => $siguser,
            'sigrong'      => $sigrong,
            'position'     => $position,
            'ptname'       => $ptname,
            'po'           => $po,
            'rong_bo'      => $rong_bo,
            'id'           => $id,
            'data_uer'     => $data_uer,
            'data_uerreq'  => $data_uerreq,
            'data_head'    => $data_head,
            'wh_request'   => $wh_request,
            'data_userpay' => $data_userpay,
            ])->setPaper('a4', 'portrait');
        return $pdf->stream();
    }
    public function wh_request_add(Request $request)
    {
        $startdate           = $request->datepicker;
        $enddate             = $request->datepicker2;
        $iduser              = Auth::user()->id;
        $subsubtrueid        = Auth::user()->dep_subsubtrueid;
        $data_sub            = Departmentsubsub::where('DEPARTMENT_SUB_SUB_ID',$subsubtrueid)->first();
        $data['depsubsub']   = $data_sub->DEPARTMENT_SUB_SUB_NAME;

        $data['date_now']          = date('Y-m-d');
        $data['department']         = Department::get();
        $data['department_sub']     = Departmentsub::get();
        $data['department_sub_sub'] = Departmentsubsub::get();
        $data['position']           = Position::get();
        $data['status']             = Status::get();
        $data['user']               = User::get();
        $yy1                        = date('Y') + 543;
        $yy2                        = date('Y') + 542;
        $yy3                        = date('Y') + 541;
        $data['m']                  = date('H');
        $data['mm']                 = date('H:m:s');
        $data['datefull']           = date('Y-m-d H:m:s');
        $months                     = date('m');
        $data['monthsnew']          = substr($months,1,2);
        $bgs_year      = DB::table('budget_year')->where('years_now','Y')->first();
        $bg_yearnow    = $bgs_year->leave_year_id;
        $data['air_supplies']       = Air_supplies::where('active','=','Y')->get();

        $data['wh_stock_list'] = DB::table('wh_stock_list')->where('stock_type','=','1')->where('active','=','Y')->get();
        // $data_edit             = DB::table('wh_request')->where('wh_request_id','=',$id)->first();
        // $data['stock_name']    = $data_main->stock_list_name;

        // $subsubid         =  Auth::user()->dep_subsubtrueid;
        // Wh_request::insert([
            // 'year'                 => $request->bg_yearnow,
            // 'request_date'         => $request->request_date,
            // 'request_time'         => $request->request_time,
            // 'request_no'           => $request->request_no,
            // 'stock_list_id'        => $request->stock_list_id,
            // 'stock_list_subid'     => $subsubid,
            // 'user_request'         => Auth::user()->id
        // ]);

        return view('wh_user.wh_request_add', $data,[
            'startdate'   => $startdate,
            'enddate'     => $enddate,
            'bg_yearnow'  => $bg_yearnow,
        ]);
    }
    public function wh_request_save(Request $request)
    {
        // $year                = date('Y')+ 543;
        // $ynew             = substr($request->bg_yearnow,2,2);
        $subsubid         =  Auth::user()->dep_subsubtrueid;
        Wh_request::insert([
            'year'                 => $request->bg_yearnow,
            'request_date'         => $request->request_date,
            'request_time'         => $request->request_time,
            'request_no'           => $request->request_no,
            'stock_list_id'        => $request->stock_list_id,
            'stock_list_subid'     => $subsubid,
            'user_request'         => Auth::user()->id
        ]);
        return response()->json([
            'status'    => '200'
        ]);
    }
    public function wh_request_edit(Request $request,$id)
    {
        $startdate  = $request->datepicker;
        $enddate    = $request->datepicker2;

        $data['department']         = Department::get();
        $data['department_sub']     = Departmentsub::get();
        $data['department_sub_sub'] = Departmentsubsub::get();
        $data['position']           = Position::get();
        $data['status']             = Status::get();
        $data['user']               = User::get();
        $iduser                     = Auth::user()->id;
        $subsubtrueid               = Auth::user()->dep_subsubtrueid;


        $yy1                        = date('Y') + 543;
        $yy2                        = date('Y') + 542;
        $yy3                        = date('Y') + 541;
        $bgs_year      = DB::table('budget_year')->where('years_now','Y')->first();
        $bg_yearnow    = $bgs_year->leave_year_id;
        $data['air_supplies']       = Air_supplies::where('active','=','Y')->get();

        $data['wh_stock_list'] = DB::table('wh_stock_list')->where('stock_type','=','1')->where('active','=','Y')->get();

        $data_edit             = DB::table('wh_request')
            ->leftJoin('users','users.id','=','wh_request.user_request')
            ->where('wh_request_id','=',$id)->first();
            $data_sub                   = Departmentsubsub::where('DEPARTMENT_SUB_SUB_ID',$data_edit->stock_list_subid)->first();
            $data['depsubsub']          = $data_sub->DEPARTMENT_SUB_SUB_NAME;

        return view('wh_user.wh_request_edit', $data,[
            'startdate'  => $startdate,
            'enddate'    => $enddate,
            'data_edit'  => $data_edit,
        ]);
    }
    public function wh_request_update(Request $request)
    {
        $id            = $request->wh_request_id;
        // $ynew          = substr($request->bg_yearnow,2,2);
        Wh_request::where('wh_request_id',$id)->update([
            'year'                 => $request->bg_yearnow,
            'request_date'         => $request->request_date,
            'request_time'         => $request->request_time,
            'request_no'           => $request->request_no,
            'stock_list_id'        => $request->stock_list_id,
            // 'user_request'         => Auth::user()->id
        ]);
        return response()->json([
            'status'    => '200'
        ]);
    }
    public function wh_request_addsub(Request $request,$id)
    {
        $startdate  = $request->datepicker;
        $enddate    = $request->datepicker2;

        $data['department']         = Department::get();
        $data['department_sub']     = Departmentsub::get();
        $data['department_sub_sub'] = Departmentsubsub::get();
        $data['position']           = Position::get();
        $data['status']             = Status::get();
        $yy1                        = date('Y') + 543;
        $yy2                        = date('Y') + 542;
        $yy3                        = date('Y') + 541;
        $bgs_year                   = DB::table('budget_year')->where('years_now','Y')->first();
        $bg_yearnow                 = $bgs_year->leave_year_id;
        $data['air_supplies']       = Air_supplies::where('active','=','Y')->get();

        $data_edit                  = DB::table('wh_request')->where('wh_request_id','=',$id)->first();
        $data['wh_request_id']      = $data_edit->wh_request_id;
        $data['data_year']          = $data_edit->year;
        $datastock_list_id          = $data_edit->stock_list_id;
        // $datastock_list_id          = $data_edit->stock_list_id;
        $data['stock_list_id']      = $data_edit->stock_list_id;
        $subsubid                   =  Auth::user()->dep_subsubtrueid;
        // $data_wh_stock_list         = DB::table('wh_stock_list')->where('stock_list_id',$datastock_list_id)->first();
        // $data['stock_name']         = $data_wh_stock_list->stock_list_name;
        $data_count                 = DB::table('wh_request')->where('wh_request_id','=',$id)->count();

        $data_debsubsub             = DB::table('department_sub_sub')->where('DEPARTMENT_SUB_SUB_ID','=',$subsubid)->first();
        $data['supsup_id']          = $data_debsubsub->DEPARTMENT_SUB_SUB_ID;
        $data['supsup_name']        = $data_debsubsub->DEPARTMENT_SUB_SUB_NAME;
        // $data['supplies_tax']       = $data_debsubsub->supplies_tax;

        $data['wh_product']         = DB::select(
            'SELECT a.pro_id,a.pro_code,a.pro_num,a.pro_year,a.pro_name,b.wh_type_id,b.wh_type_name,c.wh_unit_name,e.stock_qty,e.stock_rep,e.stock_pay,e.stock_total,e.stock_price,a.active
                ,IFNULL(d.wh_unit_pack_qty,"1") as wh_unit_pack_qty ,IFNULL(d.wh_unit_pack_name,c.wh_unit_name) as unit_name,f.stock_list_name

                FROM wh_stock e
                LEFT JOIN wh_product a ON a.pro_id = e.pro_id
                LEFT JOIN wh_type b ON b.wh_type_id = a.pro_type
                LEFT JOIN wh_unit c ON c.wh_unit_id = a.unit_id
                LEFT JOIN wh_unit_pack d ON d.wh_unit_id = a.pro_id
                LEFT JOIN wh_stock_list f ON f.stock_list_id = e.stock_list_id
            WHERE a.active ="Y" AND e.stock_year ="'.$bg_yearnow.'" AND e.stock_list_id IN("'.$datastock_list_id.'")
            GROUP BY e.pro_id
        ');
        // AND b.wh_type_id = "'.$datastock_list_id.'"
        $data['wh_request_sub']      = DB::select(
            'SELECT a.*,b.pro_code
            ,(SELECT SUM(qty) FROM wh_recieve_sub WHERE pro_id = a.pro_id AND request_year ="'.$bg_yearnow.'" AND stock_list_id IN("'.$datastock_list_id.'")) AS stock_rep
            ,(SELECT SUM(qty_pay) FROM wh_stock_export_sub WHERE pro_id = a.pro_id AND export_sub_year ="'.$bg_yearnow.'" AND stock_list_id IN("'.$datastock_list_id.'")) as stock_pay
            FROM wh_request_sub a
            LEFT JOIN wh_product b ON b.pro_id = a.pro_id
            WHERE wh_request_id = "'.$id.'"
            GROUP BY a.pro_id
            ');
        $year                        = substr(date("Y"),2) + 43;
        $mounts                      = date('m');
        $day                         = date('d');
        $time                        = date("His");
        $data['lot_no']              = $year.''.$mounts.''.$day.''.$time;

        return view('wh_user.wh_request_addsub', $data,[
            'startdate'  => $startdate,
            'enddate'    => $enddate,
            'data_edit'  => $data_edit,
        ]);
    }
    public function wh_request_addsubdetail(Request $request)
    {
        $pro_id                 =  $request->pro_id;

        $data_show          = wh_product_sub::where('pro_id',$pro_id)->get();
        // $color              = $data_show->pro_color;
        // $pro_brand          = $data_show->pro_brand;
        // $pro_type           = $data_show->pro_type;
        // $output='<label for="">เลขบัตรประชาชน  :   '.$data_cid. '&nbsp;&nbsp;&nbsp; || &nbsp;&nbsp;&nbsp;ชื่อ-นามสกุล :' .$data_ptname.'&nbsp;&nbsp;&nbsp; || &nbsp;&nbsp;&nbsp;  เบอร์โทร  : ' .$data_hometel.'</label>' ;
        // $output='<label for="">เลขบัตรประชาชน  :   '.$color. '&</label>' ;
        $i = 1;
        foreach ($data_show as $key => $value) {
            if ($value->pro_color == '') {
                $output='
                    <input class="form-check-input" type="checkbox" id="pro_brand" name="pro_brand"/>&nbsp;&nbsp;ยี่ห้อ -'.$value->pro_brand.'<br>
                ';
                echo $output;
            } else {
                if ($value->pro_brand == '') {
                    $output='
                        <input class="form-check-input" type="checkbox" id="pro_color" name="pro_color"/>&nbsp;&nbsp;สี -'.$value->pro_color.'<br>
                    ';
                    echo $output;
                } else {
                    $output='
                        <input class="form-check-input" type="checkbox" id="pro_color" name="pro_color"/>&nbsp;&nbsp;ยี่ห้อ -'.$value->pro_brand.' || สี - '.$value->pro_color.'<br>
                    ';
                    echo $output;
                }
            }

        }

    }
    public function wh_request_addsubpic(Request $request,$id)
    {
        $startdate  = $request->datepicker;
        $enddate    = $request->datepicker2;

        $data['department']         = Department::get();
        $data['department_sub']     = Departmentsub::get();
        $data['department_sub_sub'] = Departmentsubsub::get();
        $data['position']           = Position::get();
        $data['status']             = Status::get();
        $yy1                        = date('Y') + 543;
        $yy2                        = date('Y') + 542;
        $yy3                        = date('Y') + 541;
        $bgs_year                   = DB::table('budget_year')->where('years_now','Y')->first();
        $bg_yearnow                 = $bgs_year->leave_year_id;
        $data['air_supplies']       = Air_supplies::where('active','=','Y')->get();

        $data_edit                  = DB::table('wh_request')->where('wh_request_id','=',$id)->first();
        $data['wh_request_id']      = $data_edit->wh_request_id;
        $data['data_year']          = $data_edit->year;
        $datastock_list_id          = $data_edit->stock_list_id;
        $data['stock_list_id']      = $data_edit->stock_list_id;
        $subsubid                   =  Auth::user()->dep_subsubtrueid;
        // $data_wh_stock_list         = DB::table('wh_stock_list')->where('stock_list_id',$datastock_list_id)->first();
        // $data['stock_name']         = $data_wh_stock_list->stock_list_name;

        $data_debsubsub             = DB::table('department_sub_sub')->where('DEPARTMENT_SUB_SUB_ID','=',$subsubid)->first();
        $data['supsup_id']          = $data_debsubsub->DEPARTMENT_SUB_SUB_ID;
        $data['supsup_name']        = $data_debsubsub->DEPARTMENT_SUB_SUB_NAME;
        // $data['supplies_tax']       = $data_debsubsub->supplies_tax;

        $data['wh_product_1']         = DB::select(
            'SELECT a.pro_id,a.pro_code,a.pro_num,a.pro_year,a.pro_name,b.wh_type_id,b.wh_type_name,c.wh_unit_name,e.stock_qty,e.stock_rep,e.stock_pay,e.stock_total,e.stock_price,a.active
            ,IFNULL(d.wh_unit_pack_qty,"1") as wh_unit_pack_qty ,IFNULL(d.wh_unit_pack_name,c.wh_unit_name) as unit_name,a.img_base
                FROM wh_stock e
                LEFT JOIN wh_product a ON a.pro_id = e.pro_id
                LEFT JOIN wh_type b ON b.wh_type_id = a.pro_type
                LEFT JOIN wh_unit c ON c.wh_unit_id = a.unit_id
                LEFT JOIN wh_unit_pack d ON d.wh_unit_id = a.pro_id
                LEFT JOIN wh_stock_list f ON f.stock_list_id = e.stock_list_id
            WHERE a.active ="Y" AND e.stock_year ="'.$bg_yearnow.'" AND e.stock_list_id = "'.$datastock_list_id.'"
            GROUP BY e.pro_id
        ');
        // AND a.pro_type = "1"
        $data['wh_product_2']         = DB::select(
            'SELECT a.pro_id,a.pro_code,a.pro_num,a.pro_year,a.pro_name,b.wh_type_id,b.wh_type_name,c.wh_unit_name,e.stock_qty,e.stock_rep,e.stock_pay,e.stock_total,e.stock_price,a.active
            ,IFNULL(d.wh_unit_pack_qty,"1") as wh_unit_pack_qty ,IFNULL(d.wh_unit_pack_name,c.wh_unit_name) as unit_name,a.img_base
                FROM wh_stock e
                LEFT JOIN wh_product a ON a.pro_id = e.pro_id
                LEFT JOIN wh_type b ON b.wh_type_id = a.pro_type
                LEFT JOIN wh_unit c ON c.wh_unit_id = a.unit_id
                LEFT JOIN wh_unit_pack d ON d.wh_unit_id = a.pro_id
                LEFT JOIN wh_stock_list f ON f.stock_list_id = e.stock_list_id
            WHERE a.active ="Y" AND e.stock_year ="'.$bg_yearnow.'" AND e.stock_list_id = "'.$datastock_list_id.'"
            GROUP BY e.pro_id
        ');
        // AND a.pro_type IN("3")
        $data['wh_product_3']         = DB::select(
            'SELECT a.pro_id,a.pro_code,a.pro_num,a.pro_year,a.pro_name,b.wh_type_id,b.wh_type_name,c.wh_unit_name,e.stock_qty,e.stock_rep,e.stock_pay,e.stock_total,e.stock_price,a.active
            ,IFNULL(d.wh_unit_pack_qty,"1") as wh_unit_pack_qty ,IFNULL(d.wh_unit_pack_name,c.wh_unit_name) as unit_name,a.img_base
                FROM wh_stock e
                LEFT JOIN wh_product a ON a.pro_id = e.pro_id
                LEFT JOIN wh_type b ON b.wh_type_id = a.pro_type
                LEFT JOIN wh_unit c ON c.wh_unit_id = a.unit_id
                LEFT JOIN wh_unit_pack d ON d.wh_unit_id = a.pro_id
                LEFT JOIN wh_stock_list f ON f.stock_list_id = e.stock_list_id
            WHERE a.active ="Y" AND e.stock_year ="'.$bg_yearnow.'" AND e.stock_list_id = "'.$datastock_list_id.'"
            GROUP BY e.pro_id
        ');
        // AND a.pro_type IN("18")
        $data['wh_product_4']         = DB::select(
            'SELECT a.pro_id,a.pro_code,a.pro_num,a.pro_year,a.pro_name,b.wh_type_id,b.wh_type_name,c.wh_unit_name,e.stock_qty,e.stock_rep,e.stock_pay,e.stock_total,e.stock_price,a.active
            ,IFNULL(d.wh_unit_pack_qty,"1") as wh_unit_pack_qty ,IFNULL(d.wh_unit_pack_name,c.wh_unit_name) as unit_name,a.img_base
                FROM wh_stock e
                LEFT JOIN wh_product a ON a.pro_id = e.pro_id
                LEFT JOIN wh_type b ON b.wh_type_id = a.pro_type
                LEFT JOIN wh_unit c ON c.wh_unit_id = a.unit_id
                LEFT JOIN wh_unit_pack d ON d.wh_unit_id = a.pro_id
                LEFT JOIN wh_stock_list f ON f.stock_list_id = e.stock_list_id
            WHERE a.active ="Y" AND e.stock_year ="'.$bg_yearnow.'" AND e.stock_list_id = "'.$datastock_list_id.'"
            GROUP BY e.pro_id
        ');
        // AND a.pro_type IN("22")
        $data['wh_product_5']         = DB::select(
            'SELECT a.pro_id,a.pro_code,a.pro_num,a.pro_year,a.pro_name,b.wh_type_id,b.wh_type_name,c.wh_unit_name,e.stock_qty,e.stock_rep,e.stock_pay,e.stock_total,e.stock_price,a.active
            ,IFNULL(d.wh_unit_pack_qty,"1") as wh_unit_pack_qty ,IFNULL(d.wh_unit_pack_name,c.wh_unit_name) as unit_name,a.img_base
                FROM wh_stock e
                LEFT JOIN wh_product a ON a.pro_id = e.pro_id
                LEFT JOIN wh_type b ON b.wh_type_id = a.pro_type
                LEFT JOIN wh_unit c ON c.wh_unit_id = a.unit_id
                LEFT JOIN wh_unit_pack d ON d.wh_unit_id = a.pro_id
                LEFT JOIN wh_stock_list f ON f.stock_list_id = e.stock_list_id
            WHERE a.active ="Y" AND e.stock_year ="'.$bg_yearnow.'" AND e.stock_list_id = "'.$datastock_list_id.'"
            GROUP BY e.pro_id
        ');
        // AND a.pro_type IN("44")
        $data['productsub']  = DB::select('SELECT * FROM wh_product_sub WHERE pro_type IN("44") GROUP BY pro_brand');

        $data['wh_request_sub']      = DB::select('SELECT * FROM wh_request_sub WHERE wh_request_id = "'.$id.'" ORDER BY wh_request_sub_id DESC');
            // SELECT a.*,b.pro_code
            // ,(SELECT SUM(qty) FROM wh_recieve_sub WHERE pro_id = a.pro_id AND request_year ="'.$bg_yearnow.'" AND stock_list_id ="'.$datastock_list_id.'") AS stock_rep
            // ,(SELECT SUM(qty_pay) FROM wh_stock_export_sub WHERE pro_id = a.pro_id AND export_sub_year ="'.$bg_yearnow.'" AND stock_list_id ="'.$datastock_list_id.'") as stock_pay
            // FROM wh_request_sub a
            // LEFT JOIN wh_product b ON b.pro_id = a.pro_id
            // WHERE wh_request_id = "'.$id.'"
            // GROUP BY a.pro_id
        $year                        = substr(date("Y"),2) + 43;
        $mounts                      = date('m');
        $day                         = date('d');
        $time                        = date("His");
        $data['lot_no']              = $year.''.$mounts.''.$day.''.$time;

        return view('wh_user.wh_request_addsubpic', $data,[
            'startdate'  => $startdate,
            'enddate'    => $enddate,
            'data_edit'  => $data_edit,
        ]);
    }
    public function wh_picid(Request $request, $id)
    {
        $product = Wh_product::find($id);

        return response()->json([
            'status'       => '200',
            'product'      =>  $product,
        ]);
    }
    public function wh_picidtwo(Request $request, $id)
    {
        $product     = Wh_product::find($id);
        // $productsub  = Wh_product_sub::where('pro_id',$id)->first();
        $productsub  = Wh_product_sub::where('pro_id',$id)->get();
        // $product  = DB::select(
        //     'SELECT a.pro_id,a.pro_color,a.pro_brand,(SELECT pro_name FROM wh_product WHERE pro_id = a.pro_id) as pro_name
        //         FROM wh_product_sub a
        //     WHERE a.pro_id ="'.$id.'" LIMIT 1
        // ');
        // dd($product);
        // foreach ($product as $key => $value) {
        //     $pro_id    = $value->pro_id;
        //     $pro_name  = $value->pro_name;
        // }

        return response()->json([
            'status'          => '200',
            'product'         =>  $product,
            'pro_id'          =>  $id,
            'productsub'      =>  $productsub,
        ]);
    }
    // loadtotal
    public function wh_loadtotal(Request $request)
    {
        $year                        = substr(date("Y"),2) + 43;
        $mounts                      = date('m');
        $day                         = date('d');
        $time                        = date("His");
        $lot_no                      = $year.''.$mounts.''.$day.''.$time;
        $id                          = $request->wh_request_id;
        $wh_request_sub              = DB::select('SELECT COUNT(wh_request_sub_id) as wh_request_sub_id FROM wh_request_sub WHERE wh_request_id = "'.$id.'"');
        foreach ($wh_request_sub as $key => $value) {
            $total   = $value->wh_request_sub_id;
        }

        return response()->json([
            'status'    => '200',
             'total'    => $total
        ]);
    }
    public function wh_request_addsubpic_save(Request $request)
    {
        $ynew          = substr($request->bg_yearnow,2,2);
        $idpro         = $request->edit_proid;
        $pro           = Wh_product::where('pro_id',$idpro)->first();
        $proid         = $pro->pro_id;
        $procode       = $pro->pro_code;
        $proname       = $pro->pro_name;
        $unitid        = $pro->unit_id;

        $pro_brannew  = $request->wh_product_sub_id;
        if ($pro_brannew != '') {
            $idprosub     = Wh_product_sub::where('wh_product_sub_id',$pro_brannew)->first();
        }


        $unit          = Wh_unit::where('wh_unit_id',$unitid)->first();
        $idunit        = $unit->wh_unit_id;
        $nameunit      = $unit->wh_unit_name;

        // $bgs_year      = DB::table('budget_year')->where('years_now','Y')->first();
        // $bg_yearnow    = $bgs_year->leave_year_id;
        // $pro_check     = Wh_request_sub::where('wh_request_id',$request->wh_request_id)->where('pro_id',$proid)->where('request_year',$request->data_year)->where('stock_list_id',$request->stock_list_id)->where('stock_list_subid',$request->supsup_id)->count();
        $pro_check     = Wh_request_sub::where('wh_request_id',$request->wh_request_id)->where('pro_id',$proid)->where('request_year',$request->data_year)->where('stock_list_subid',$request->stock_list_subid)->count();
        if ($pro_check > 0) {
            Wh_request_sub::where('wh_request_id',$request->wh_request_id)->where('pro_id',$proid)->where('request_year',$request->data_year)->where('stock_list_subid',$request->stock_list_subid)->update([
                'qty'                  => $request->edit_qty,
                // 'stock_list_id'        => $request->stock_list_id,
                // 'stock_list_subid'     => $request->stock_list_subid,
                // 'request_year'         => $request->data_year,
                'pro_brand'            => $request->wh_product_sub_id,
                'pro_kind'             => $request->edit_pro_kind,
                'one_price'            => $request->one_price,
                'total_price'          => $request->one_price*$request->edit_qty,
                // 'lot_no'               => $request->lot_no,
                'user_id'              => Auth::user()->id
            ]);
        } else {
            Wh_request_sub::insert([
                'wh_request_id'        => $request->wh_request_id,
                // 'stock_list_id'        => $request->stock_list_id,
                'stock_list_subid'     => $request->stock_list_subid,
                'request_year'         => $request->data_year,
                'pro_id'               => $proid,
                'pro_code'             => $procode,
                'pro_name'             => $proname,
                'pro_kind'             => $request->edit_pro_kind,
                'pro_brand'            => $request->wh_product_sub_id,
                'unit_id'              => $idunit,
                'unit_name'            => $nameunit,
                'qty'                  => $request->edit_qty,
                'one_price'            => $request->one_price,
                'total_price'          => $request->one_price*$request->edit_qty,
                'lot_no'               => $request->lot_no,
                'user_id'              => Auth::user()->id
            ]);
        }
        return response()->json([
            'status'    => '200'
        ]);
        // return back();
    }
    public function load_shooping_table(Request $request)
    {
        $id  = $request->wh_request_id;
        // $wh_request_sub      = DB::select('SELECT * FROM wh_request_sub WHERE wh_request_id = "'.$id.'" ORDER BY wh_request_sub_id DESC');
        $wh_request_sub      = DB::select(
            'SELECT a.*,b.wh_kind_name,c.pro_brand
            FROM wh_request_sub a
            LEFT JOIN wh_kind b ON b.wh_kind_id = a.pro_kind
            LEFT JOIN wh_product_sub c ON c.wh_product_sub_id = a.pro_brand
            WHERE a.wh_request_id = "'.$id.'"
            ORDER BY a.wh_request_sub_id DESC
        ');
        $output='
                <table class="table table-bordered table-hover table-sm mt-2" style="border-collapse: collapse;border:solid 1px #93d0f3;border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center" style="background-color: rgb(255, 251, 228);font-size: 12px;">ลำดับ</th>
                                <th class="text-center" style="background-color: rgb(174, 236, 245);font-size: 12px;">รหัส</th>
                                <th class="text-center" style="background-color: rgb(174, 236, 245);font-size: 12px;">รายการ</th>
                                <th class="text-center" style="background-color: rgb(187, 250, 221);font-size: 12px;">ยี่ห้อ</th>
                                <th class="text-center" style="background-color: rgb(187, 250, 221);font-size: 12px;">ชนิด</th>
                                <th class="text-center" style="background-color: rgb(187, 250, 221);font-size: 12px;">จำนวน</th>
                                <th class="text-center" style="background-color: rgb(174, 236, 245);font-size: 12px;">หน่วยนับ</th>
                                <th class="text-center" style="background-color: rgb(252, 20, 78);font-size: 12px;color: #ffffff;">ลบ</th>
                            </tr>
                        </thead>
                    <tbody>
                        ';
                        $i = 1;
                        foreach ($wh_request_sub as $key => $value) {
                            $output.='
                              <tr id="tr_'.$value->wh_request_sub_id.'">
                                <td class="text-center" width:10%;>'.$i++.'</td>
                                <td class="text-center" width:10%;>'.$value->pro_code.'</td>
                                <td >'.$value->pro_name.'</td>
                                <td class="text-center" width:10%;>'.$value->pro_brand.'</td>
                                <td class="text-center" width:10%;>'.$value->wh_kind_name.'</td>
                                <td class="text-center" width:10%;>'.$value->qty.'</td>
                                <td class="text-center" width:10%;>'.$value->unit_name.'</td>
                                <td class="text-center" width="5%">
                                    <button type="button" class="ladda-button me-2 btn-pill btn btn-sm btn-danger card_prs" onclick="select_destpic('.$value->wh_request_sub_id.')">
                                        <i class="fa-solid fa-trash-arrow-up text-white" style="font-size: 13px;"></i>
                                    </button>
                                </td>
                            </tr>';
                        }
                        $output.='
                    </tbody>
                </table>
        ';
        // <input type="checkbox" class="dcheckboxsm sub_chk" data-id="'.$value->wh_request_sub_id.'">
        echo $output;
    }
    public function load_shooping_tables(Request $request)
    {
        $id  = $request->wh_request_id;
        // $wh_request_sub      = DB::select('SELECT a.*,b.wh_kind_name FROM wh_request_sub a LEFT JOIN wh_kind b ON b.wh_kind_id = a.pro_kind WHERE a.wh_request_id = "'.$id.'" ORDER BY a.wh_request_sub_id DESC');
        $wh_request_sub      = DB::select(
            'SELECT a.*,b.wh_kind_name,c.pro_brand
            FROM wh_request_sub a
            LEFT JOIN wh_kind b ON b.wh_kind_id = a.pro_kind
            LEFT JOIN wh_product_sub c ON c.wh_product_sub_id = a.pro_brand
            WHERE a.wh_request_id = "'.$id.'"
            ORDER BY a.wh_request_sub_id DESC
        ');
        $output='
                <table class="table table-bordered table-hover table-sm mt-2" style="border-collapse: collapse;border:solid 1px #93d0f3;border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center" style="background-color: rgb(255, 251, 228);font-size: 12px;">ลำดับ</th>
                                <th class="text-center" style="background-color: rgb(174, 236, 245);font-size: 12px;">รหัส</th>
                                <th class="text-center" style="background-color: rgb(174, 236, 245);font-size: 12px;">รายการ</th>
                                <th class="text-center" style="background-color: rgb(187, 250, 221);font-size: 12px;">ยี่ห้อ</th>
                                <th class="text-center" style="background-color: rgb(187, 250, 221);font-size: 12px;">ชนิด</th>
                                <th class="text-center" style="background-color: rgb(187, 250, 221);font-size: 12px;">จำนวน</th>
                                <th class="text-center" style="background-color: rgb(174, 236, 245);font-size: 12px;">หน่วยนับ</th>
                                <th class="text-center" style="background-color: rgb(174, 236, 245);font-size: 12px;">ลบ</th>
                            </tr>
                        </thead>
                    <tbody>
                        ';
                        // <th width="5%" class="text-center">
                        //             <button type="button" class="ladda-button me-2 btn-pill btn btn-sm btn-danger card_prs">
                        //                 <i class="fa-solid fa-trash-arrow-up text-white" style="font-size: 13px;"></i>
                        //             </button>
                        //          </th>
                        $i = 1;
                        foreach ($wh_request_sub as $key => $value) {
                            $output.='
                              <tr id="tr_'.$value->wh_request_sub_id.'">
                                <td class="text-center" width:10%;>'.$i++.'</td>
                                <td class="text-center" width:10%;>'.$value->pro_code.'</td>
                                <td >'.$value->pro_name.'</td>
                                <td class="text-center" width:10%;>'.$value->pro_brand.'</td>
                                <td class="text-center" width:10%;>'.$value->wh_kind_name.'</td>
                                <td class="text-center" width:10%;>'.$value->qty.'</td>
                                <td class="text-center" width:10%;>'.$value->unit_name.'</td>
                                <td class="text-center" width="5%">
                                    <button type="button" class="ladda-button me-2 btn-pill btn btn-sm btn-danger card_prs" onclick="select_destpic('.$value->wh_request_sub_id.')">
                                        <i class="fa-solid fa-trash-arrow-up text-white" style="font-size: 13px;"></i>
                                    </button>
                                </td>
                            </tr>';
                        }
                        $output.='
                    </tbody>
                </table>
        ';
        // <input type="checkbox" class="dcheckboxsm sub_chk" data-id="'.$value->wh_request_sub_id.'">
        echo $output;
    }
    public function wh_request_addsubpicdestroy(Request $request)
    {
        $id             = $request->ids;
        Wh_request_sub::whereIn('wh_request_sub_id',explode(",",$id))->delete();
        return response()->json([
            'status'    => '200'
        ]);
    }
    public function wh_request_picdestroy(Request $request)
    {
        $id             = $request->wh_request_sub_id;
        Wh_request_sub::whereIn('wh_request_sub_id',explode(",",$id))->delete();
        return response()->json([
            'status'    => '200'
        ]);
    }

    public function load_datauser_table(Request $request)
    {
        $id  = $request->wh_request_id;
        $wh_request_sub      = DB::select('SELECT * FROM wh_request_sub WHERE wh_request_id = "'.$id.'" ORDER BY wh_request_sub_id DESC');

        $output='
                <table id="Tabledit" class="table table-bordered border-primary table-hover table-sm" style="border-collapse: collapse;border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center" style="background-color: rgb(255, 251, 228);font-size: 12px;">ลำดับ</th>
                                <th class="text-center" style="background-color: rgb(174, 236, 245);font-size: 12px;">รหัส</th>
                                <th class="text-center" style="background-color: rgb(174, 236, 245);font-size: 12px;">รายการ</th>
                                <th class="text-center" style="background-color: rgb(174, 236, 245);font-size: 12px;">หน่วยนับ</th>
                                <th class="text-center" style="background-color: rgb(187, 250, 221);font-size: 12px;">จำนวน</th>
                                <th width="5%" class="text-center"><input type="checkbox" class="dcheckbox" name="stamp" id="stamp"> </th>
                            </tr>
                        </thead>
                    <tbody>
                        ';

                        $i = 1;
                        foreach ($wh_request_sub as $key => $value) {

                            $output.='
                              <tr id="tr_'.$value->wh_request_sub_id.'">
                                <td class="text-center" width:10%;>'.$i++.'</td>
                                <td class="text-center" width:10%;>'.$value->pro_code.'</td>
                                <td >'.$value->pro_name.'</td>
                                <td class="text-center" width:10%;>'.$value->unit_name.'</td>
                                <td class="text-center" width:10%;>'.$value->qty.'</td>
                                <td class="text-center" width="5%"><input type="checkbox" class="dcheckbox sub_chk" data-id="'.$value->wh_request_sub_id.'"> </td>
                            </tr>';
                        }
                        $output.='
                    </tbody>
                </table>

        ';
        echo $output;
    }
    public function load_data_usersum(Request $request)
    {
        $year                        = substr(date("Y"),2) + 43;
        $mounts                      = date('m');
        $day                         = date('d');
        $time                        = date("His");
        $lot_no                      = $year.''.$mounts.''.$day.''.$time;
        $id                          = $request->wh_request_id;
        $wh_request_sub              = DB::select('SELECT COUNT(wh_request_sub_id) as wh_request_sub_id FROM wh_request_sub WHERE wh_request_id = "'.$id.'"');
        foreach ($wh_request_sub as $key => $value) {
            $total   = $value->wh_request_sub_id;
        }

        return response()->json([
            'status'    => '200',
             'total'    => $total
        ]);
    }
    public function wh_request_addsub_save(Request $request)
    {
        $ynew          = substr($request->bg_yearnow,2,2);
        $idpro         = $request->pro_id;
        $pro           = Wh_product::where('pro_id',$idpro)->first();
        $proid         = $pro->pro_id;
        $procode       = $pro->pro_code;
        $proname       = $pro->pro_name;
        $unitid        = $pro->unit_id;

        $unit          = Wh_unit::where('wh_unit_id',$unitid)->first();
        $idunit        = $unit->wh_unit_id;
        $nameunit      = $unit->wh_unit_name;

        // $bgs_year      = DB::table('budget_year')->where('years_now','Y')->first();
        // $bg_yearnow    = $bgs_year->leave_year_id;
        // $pro_check     = Wh_request_sub::where('wh_request_id',$request->wh_request_id)->where('pro_id',$proid)->where('request_year',$request->data_year)->where('stock_list_id',$request->stock_list_id)->where('stock_list_subid',$request->supsup_id)->count();
        $pro_check     = Wh_request_sub::where('wh_request_id',$request->wh_request_id)->where('pro_id',$proid)->where('request_year',$request->data_year)->where('stock_list_subid',$request->stock_list_subid)->count();
        if ($pro_check > 0) {
            Wh_request_sub::where('wh_request_id',$request->wh_request_id)->where('pro_id',$proid)->where('request_year',$request->data_year)->where('stock_list_subid',$request->stock_list_subid)->update([
                'qty'                  => $request->qty,
                'stock_list_id'        => $request->stock_list_id,
                'stock_list_subid'     => $request->stock_list_subid,
                'request_year'         => $request->data_year,
                'one_price'            => $request->one_price,
                'total_price'          => $request->one_price*$request->qty,
                'lot_no'               => $request->lot_no,
                'user_id'              => Auth::user()->id
            ]);
        } else {
            Wh_request_sub::insert([
                'wh_request_id'        => $request->wh_request_id,
                'stock_list_id'        => $request->stock_list_id,
                'stock_list_subid'     => $request->stock_list_subid,
                'request_year'         => $request->data_year,
                'pro_id'               => $proid,
                'pro_code'             => $procode,
                'pro_name'             => $proname,
                'unit_id'              => $idunit,
                'unit_name'            => $nameunit,
                'qty'                  => $request->qty,
                'one_price'            => $request->one_price,
                'total_price'          => $request->one_price*$request->qty,
                'lot_no'               => $request->lot_no,
                'user_id'              => Auth::user()->id
            ]);
        }
        return response()->json([
            'status'    => '200'
        ]);
        // return back();

    }
    public function wh_request_destroy(Request $request)
    {
        $id             = $request->ids;
        // $wh_re          = DB::table('wh_recieve_sub')->where('wh_recieve_sub_id','=',$id)->first();
        // $wh_recieve_id  = $wh_re->wh_recieve_id;

        // $wh_re_sum      = DB::table('wh_recieve_sub')->where('wh_recieve_sub_id','=',$id)->sum('total_price');
        // $sum_total       = Wh_recieve_sub::where('wh_recieve_id',$wh_recieve_id)->sum('total_price');
        // Wh_recieve::where('wh_recieve_id',$wh_recieve_id)->update([
        //     'total_price'  => $sum_total
        // ]);

        Wh_request_sub::whereIn('wh_request_sub_id',explode(",",$id))->delete();

        return response()->json([
            'status'    => '200'
        ]);
    }
    public function wh_request_updatestock(Request $request)
    {
        $id              = $request->wh_request_id;
        $data_year       = $request->data_year;
        // $stock_list_id   = $request->stock_list_id;
        $supsup_id       = $request->supsup_id;

        // $sum_total       = Wh_request_sub::where('wh_request_id',$id)->sum('total_price');
        Wh_request::where('wh_request_id',$id)->update([
            // 'total_price'  => $sum_total,
            'active'       => 'APPREQUEST',
        ]);

        // $idpro         = $request->pro_id;
        // $pro           = Wh_product::where('pro_id',$idpro)->first();
        // $proid         = $pro->pro_id;
        // $proname       = $pro->pro_name;
        // $unitid        = $pro->unit_id;

        // $unit          = Wh_unit::where('wh_unit_id',$unitid)->first();
        // $idunit        = $unit->wh_unit_id;
        // $nameunit      = $unit->wh_unit_name;

        return response()->json([
            'status'    => '200'
        ]);

    }
    public function wh_request_edittable(Request $request)
    {
        if ($request->ajax()) {
            if ($request->action == 'Edit') {
                $data  = array(
                    // 'lot_no'        => $request->lot_no,
                    'qty'           => $request->qty,
                    // 'one_price'     => $request->one_price,
                    // 'total_price'   => $request->qty * $request->one_price,
                );
                DB::connection('mysql')->table('wh_request_sub')->where('wh_request_sub_id', $request->wh_request_sub_id)->update($data);
            }
            return response()->json([
                'status'     => '200'
            ]);
        }
    }
    public function wh_approve_stock(Request $request,$id)
    {
            $userid                     =  Auth::user()->id;
            $datenow                    = date('Y-m-d');
            $data['m']                  = date('H');
            $mm                         = date('H:m:s');
            $data['datefull']           = date('Y-m-d H:m:s');
            // $data['monthsnew']          = substr($months,1,2);

            Wh_stock_dep::where('wh_request_id',$id)->update([
                'active'           => 'REPEXPORT',
                'user_export_rep'  => $userid
            ]);

            Wh_stock_export::where('wh_request_id',$id)->update([
                'active'           => 'REPEXPORT',
                'user_export_rep'  => $userid
            ]);

            Wh_request::where('wh_request_id',$id)->update([
                'active'       => 'REPEXPORT',
                'repin_date'   => $datenow,
                'repin_time'   => $mm
            ]);

            $ip = $request->ip();
            $datenow               = date('Y-m-d');
            // $data['m']             = date('H');
            // $mm                    = date('H:m:s');
            Wh_log::insert([
                'datesave'        => $datenow,
                'ip'              => $ip,
                'timesave'        => $mm,
                'wh_request_id'   => $id,
                'userid'          => Auth::user()->id,
                'comment'         => 'ยืนยันการรับเข้าคลังย่อย(ขั้นตอนสุดท้าย)'
            ]);

            return response()->json([
                'status'     => '200'
            ]);

    }
    public function wh_cancel_req(Request $request,$id)
    {
            $userid                     =  Auth::user()->id;
            $datenow                    = date('Y-m-d');
            $data['m']                  = date('H');
            $mm                         = date('H:m:s');
            $data['datefull']           = date('Y-m-d H:m:s');
            // $data['monthsnew']          = substr($months,1,2);

            // Wh_stock_dep::where('wh_request_id',$id)->update([
            //     'active'           => 'REPEXPORT',
            //     'user_export_rep'  => $userid
            // ]);

            // Wh_stock_export::where('wh_request_id',$id)->update([
            //     'active'           => 'REPEXPORT',
            //     'user_export_rep'  => $userid
            // ]);

            Wh_request::where('wh_request_id',$id)->update([
                'active'       => 'CANCEL',
                // 'repin_date'   => $datenow,
                // 'repin_time'   => $mm
            ]);

            $ip = $request->ip();
            $datenow               = date('Y-m-d');
            // $data['m']             = date('H');
            // $mm                    = date('H:m:s');
            Wh_log::insert([
                'datesave'        => $datenow,
                'ip'              => $ip,
                'timesave'        => $mm,
                'wh_request_id'   => $id,
                'userid'          => Auth::user()->id,
                'comment'         => 'ยกเลิกใบเบิก'
            ]);

            return response()->json([
                'status'     => '200'
            ]);

    }
    // ****************************** ตัดจ่าย **************************************
    public function wh_sub_main_pay(Request $request)
    {
        $startdate                 = $request->startdate;
        $enddate                   = $request->enddate;
        $datenow                   = date('Y-m-d');
        $data['date_now']          = date('Y-m-d');
        $months                    = date('m');
        $year                      = date('Y');
        $newday                     = date('Y-m-d', strtotime($datenow . ' -5 Day')); //ย้อนหลัง 1 วัน
        $newweek                    = date('Y-m-d', strtotime($datenow . ' -1 week')); //ย้อนหลัง 1 สัปดาห์
        $newDate                    = date('Y-m-d', strtotime($datenow . ' -1 months')); //ย้อนหลัง 1 เดือน
        $newyear                    = date('Y-m-d', strtotime($datenow . ' -1 year')); //ย้อนหลัง 1 ปี

        $data['department']         = Department::get();
        $data['department_sub']     = Departmentsub::get();
        $data['department_sub_sub'] = Departmentsubsub::get();
        $data['position']           = Position::get();
        $data['status']             = Status::get();
        $data['air_supplies']       = Air_supplies::where('active','=','Y')->get();
        $data['wh_stock_list']      = DB::table('wh_stock_list')->where('stock_type','1')->get();
        $dep_subsubtrueid           =  Auth::user()->dep_subsubtrueid;
        $data['m']                  = date('H');
        $data['mm']                 = date('H:m:s');
        $data['datefull']           = date('Y-m-d H:m:s');
        $data['monthsnew']          = substr($months,1,2);
        $yy1                        = date('Y') + 543;
        $yy2                        = date('Y') + 542;
        $yy3                        = date('Y') + 541;
        $bgs_year      = DB::table('budget_year')->where('years_now','Y')->first();
        $bg_yearnow    = $bgs_year->leave_year_id;


        $data['wh_stock_list']      = DB::table('wh_stock_list')->where('stock_type','=','1')->get();

        if ($startdate == '') {

            $data['wh_pay']         = DB::select(
                'SELECT r.wh_pay_id,r.year,r.pay_date,r.pay_time,r.pay_no,r.stock_list_subid,r.total_price,c.DEPARTMENT_SUB_SUB_NAME
                ,r.pay_po,concat(u.fname," ",u.lname) as ptname,r.active

                FROM wh_pay r
                LEFT JOIN department_sub_sub c ON c.DEPARTMENT_SUB_SUB_ID = r.stock_list_subid
                LEFT JOIN users u ON u.id = r.user_pay
                WHERE r.stock_list_subid ="'.$dep_subsubtrueid.'"
                ORDER BY r.wh_pay_id DESC
            ');
            // AND r.pay_date BETWEEN "'.$newDate.'" AND "'.$datenow.'"
            // ,(SELECT concat(uu.fname," ",uu.lname) FROM users uu LEFT JOIN wh_stock_export w ON w.user_export_send = uu.id WHERE wh_request_id = r.wh_request_id) as ptname_send
                // ,(SELECT concat(uuu.fname," ",uuu.lname) FROM users uuu LEFT JOIN wh_stock_export ww ON ww.user_export_rep = uuu.id WHERE wh_request_id = r.wh_request_id) as ptname_rep
        } else {
            $data['wh_pay']         = DB::select(
                'SELECT r.wh_pay_id,r.year,r.pay_date,r.pay_time,r.pay_no,r.stock_list_subid,r.total_price,c.DEPARTMENT_SUB_SUB_NAME
                ,r.pay_po,concat(u.fname," ",u.lname) as ptname,r.active

                FROM wh_pay r
                LEFT JOIN department_sub_sub c ON c.DEPARTMENT_SUB_SUB_ID = r.stock_list_subid
                LEFT JOIN users u ON u.id = r.user_pay
                WHERE r.stock_list_subid ="'.$dep_subsubtrueid.'" AND r.pay_date BETWEEN "'.$startdate.'" AND "'.$enddate.'"
                ORDER BY r.wh_pay_id DESC
            ');

        }

        $data_main             = DB::select(
            'SELECT a.DEPARTMENT_NAME,b.DEPARTMENT_SUB_NAME,c.DEPARTMENT_SUB_SUB_NAME
            FROM department a
            LEFT JOIN department_sub b ON b.DEPARTMENT_ID = a.DEPARTMENT_ID
            LEFT JOIN department_sub_sub c ON c.DEPARTMENT_SUB_ID = b.DEPARTMENT_SUB_ID
            WHERE c.DEPARTMENT_SUB_SUB_ID = "'.$dep_subsubtrueid.'"
        ');
        foreach ($data_main as $key => $value) {
            $data['stock_bigname']    = $value->DEPARTMENT_NAME;
            $data['stock_name']       = $value->DEPARTMENT_SUB_SUB_NAME;
        }

        return view('wh_user.wh_sub_main_pay',$data,[
            'startdate'     => $startdate,
            'enddate'       => $enddate,
            'bg_yearnow'    => $bg_yearnow,
        ]);
    }
    public function wh_sub_main_paysave(Request $request)
    {
        Wh_pay::insert([
            'year'                 => $request->bg_yearnow,
            'pay_date'             => $request->pay_date,
            'pay_time'             => $request->pay_time,
            'pay_no'               => $request->pay_no,
            'stock_list_subid'     => Auth::user()->dep_subsubtrueid,
            'user_pay'             => Auth::user()->id
        ]);
        return response()->json([
            'status'    => '200'
        ]);
    }
    public function wh_sub_main_payadd(Request $request,$id)
    {
        $startdate  = $request->datepicker;
        $enddate    = $request->datepicker2;

        $data['department']         = Department::get();
        $data['department_sub']     = Departmentsub::get();
        $data['department_sub_sub'] = Departmentsubsub::get();
        $data['position']           = Position::get();
        $data['status']             = Status::get();
        $yy1                        = date('Y') + 543;
        $yy2                        = date('Y') + 542;
        $yy3                        = date('Y') + 541;
        $bgs_year                   = DB::table('budget_year')->where('years_now','Y')->first();
        $bg_yearnow                 = $bgs_year->leave_year_id;
        $data['air_supplies']       = Air_supplies::where('active','=','Y')->get();


        $data_edit                  = DB::table('wh_pay')->where('wh_pay_id','=',$id)->first();
        $data['wh_pay_id']          = $data_edit->wh_pay_id;
        $data['data_year']          = $data_edit->year;
        $datastock_list_id          = $data_edit->stock_list_id;
        $stock_list_subid           = $data_edit->stock_list_subid;

        // $data['stock_list_id']      = $data_edit->stock_list_id;
        $subsubid                   =  Auth::user()->dep_subsubtrueid;
        // $data_wh_stock_list         = DB::table('wh_stock_list')->where('stock_list_id',$datastock_list_id)->first();
        // $data['stock_name']         = $data_wh_stock_list->stock_list_name;

        $data_debsubsub             = DB::table('department_sub_sub')->where('DEPARTMENT_SUB_SUB_ID','=',$subsubid)->first();
        $data['supsup_id']          = $data_debsubsub->DEPARTMENT_SUB_SUB_ID;
        $data['supsup_name']        = $data_debsubsub->DEPARTMENT_SUB_SUB_NAME;
        // $data['supplies_tax']       = $data_debsubsub->supplies_tax;

        $data['wh_product']         = DB::select(
            'SELECT g.wh_stock_dep_sub_id,a.pro_id,a.pro_code,a.pro_year,a.pro_name,b.wh_type_name,c.wh_unit_name,a.active

            ,(SELECT SUM(qty_pay) FROM wh_stock_dep_sub WHERE stock_list_subid = e.stock_list_subid AND pro_id = e.pro_id) as qty_reppay
           ,"0" as qty_pay
            ,g.lot_no
            ,IFNULL(d.wh_unit_pack_name,c.wh_unit_name) as unit_name,f.DEPARTMENT_SUB_SUB_NAME
            FROM wh_stock_sub e
            LEFT JOIN wh_product a ON a.pro_id = e.pro_id
            LEFT JOIN wh_type b ON b.wh_type_id = a.pro_type
            LEFT JOIN wh_unit c ON c.wh_unit_id = a.unit_id
            LEFT JOIN wh_unit_pack d ON d.wh_unit_id = a.pro_id
            LEFT JOIN wh_stock_dep_sub g ON g.pro_id = a.pro_id
            LEFT JOIN department_sub_sub f ON f.DEPARTMENT_SUB_SUB_ID = e.stock_list_subid
            WHERE a.active ="Y" AND e.stock_year = "'.$bg_yearnow.'" AND e.stock_list_subid = "'.$subsubid.'"
            GROUP BY g.lot_no
            ORDER BY e.pro_id ASC
        ');
        // ,SUM(g.qty_pay) as qty_reppay
        // ,(SELECT qty FROM wh_pay_sub WHERE pro_id = e.pro_id AND lot_no = g.lot_no) as qty_pay
        $data['wh_pay_sub']      = DB::select(
            'SELECT a.*
            FROM wh_pay_sub a WHERE a.wh_pay_id = "'.$id.'"
            GROUP BY a.wh_pay_sub_id
        ');

        $data['wh_stock']      = DB::select(
            'SELECT a.wh_stock_sub_id,a.stock_year,a.stock_list_subid,a.stock_list_subname,a.pro_id,a.pro_code,a.pro_name,a.unit_id,a.unit_name
            ,b.qty,SUM(b.qty_pay) as qty_stock,c.qty_pay_sub,c.total_stock,b.one_price,b.total_price,b.lot_no
            ,(SELECT SUM(qty_pay) FROM wh_stock_dep_sub WHERE stock_list_subid = a.stock_list_subid AND pro_id = a.pro_id) as total_rep
			,(SELECT SUM(qty_pay_sub) FROM wh_stock_dep_sub_export WHERE stock_list_subid = a.stock_list_subid AND pro_id = a.pro_id) as total_pay
			,(SELECT SUM(qty_pay) FROM wh_stock_dep_sub WHERE stock_list_subid = a.stock_list_subid AND pro_id = a.pro_id)-(SELECT SUM(qty_pay_sub) FROM wh_stock_dep_sub_export WHERE stock_list_subid = a.stock_list_subid AND pro_id = a.pro_id) as totall_all
            -- ,(SUM(b.qty_pay)-c.qty_pay_sub) as stocktotal
            -- ,( (SUM(b.qty_pay))- (SUM(c.qty_pay_sub)) ) as stocktotal
            -- ,(SELECT SUM(qty_pay) FROM wh_stock_dep_sub WHERE stock_list_subid = a.stock_list_subid AND pro_id = a.pro_id) as total_stocknew
            -- ,(SELECT SUM(qty_pay) FROM wh_stock_dep_sub WHERE stock_list_subid = a.stock_list_subid AND pro_id = a.pro_id) as total_stocknew
            -- ,(SELECT SUM(s.qty_pay) FROM wh_stock_dep_sub s LEFT JOIN wh_stock_dep sm ON sm.wh_stock_dep_id = s.wh_stock_dep_id WHERE s.pro_id = a.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$id.'" AND sm.active ="REPEXPORT") AS stock_rep
            -- ,(SELECT SUM(s.one_price) FROM wh_stock_dep_sub s LEFT JOIN wh_stock_dep sm ON sm.wh_stock_dep_id = s.wh_stock_dep_id WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$id.'" AND sm.active ="REPEXPORT") AS sum_one_price
            -- ,(SELECT SUM(s.total_price) FROM wh_stock_dep_sub s LEFT JOIN wh_stock_dep sm ON sm.wh_stock_dep_id = s.wh_stock_dep_id WHERE s.pro_id = e.pro_id AND s.stock_sub_year ="'.$bg_yearnow.'" AND s.stock_list_subid ="'.$id.'" AND sm.active ="REPEXPORT") AS sum_stock_price
            -- ,(SELECT SUM(qty_pay_sub) FROM wh_pay_sub WHERE pro_id = e.pro_id AND stock_list_subid = "'.$id.'") as stock_pay

            FROM wh_stock_sub a
            LEFT JOIN wh_stock_dep_sub b ON b.pro_id = a.pro_id AND b.stock_list_subid = a.stock_list_subid
            LEFT JOIN wh_stock_dep_sub_export c ON c.lot_no = b.lot_no AND c.stock_list_subid = a.stock_list_subid
            WHERE a.stock_list_subid = "'.$stock_list_subid.'"
            GROUP BY a.pro_id

        ');
        // AND c.total_stock > 0
        // AND (b.qty_pay-c.qty_pay_sub) > 0
        $year                        = substr(date("Y"),2) + 43;
        $mounts                      = date('m');
        $day                         = date('d');
        $time                        = date("His");
        $data['lot_no']              = $year.''.$mounts.''.$day.''.$time;

        return view('wh_user.wh_sub_main_payadd', $data,[
            'startdate'  => $startdate,
            'enddate'    => $enddate,
            'data_edit'  => $data_edit,
        ]);
    }
    public function wh_sub_main_paysub_savenew(Request $request)
    {
        $wh_pay_id           = $request->wh_pay_id;
        $pay_sub             = Wh_stock_dep_sub_export::where('wh_pay_id',$wh_pay_id)->get();
        foreach ($pay_sub as $key => $value) {
            $pro_check     = Wh_pay_sub::where('wh_pay_id',$wh_pay_id)->where('pro_id',$value->pro_id)->count();
            if ($pro_check > 0) {
                Wh_pay_sub::where('wh_pay_id',$wh_pay_id)->where('pro_id',$value->pro_id)->update([
                    'qty'                        => $value->qty,
                    'qty_stock'                  => $value->qty_stock,
                    'qty_pay_sub'                => $value->qty_pay_sub,
                    'one_price'                  => $value->one_price,
                    'total_price'                => $value->one_price*$value->qty_pay_sub,
                    'lot_no'                     => $value->lot_no,
                    'date_export'                => $value->date_export,
                    'user_id'                    => Auth::user()->id
                ]);
            } else {
                Wh_pay_sub::insert([
                    'wh_stock_dep_id'            => $value->wh_stock_dep_id,
                    'wh_pay_id'                  => $wh_pay_id,
                    'wh_stock_export_sub_id'     => $value->wh_stock_export_sub_id,
                    'wh_stock_export_id'         => $value->wh_stock_export_id,
                    'wh_request_id'              => $value->wh_request_id,
                    'stock_list_id'              => $value->stock_list_id,
                    'stock_list_subid'           => $value->stock_list_subid,
                    'pay_year'                   => $value->stock_sub_year,
                    'pro_id'                     => $value->pro_id,
                    'pro_code'                   => $value->pro_code,
                    'pro_name'                   => $value->pro_name,
                    'unit_id'                    => $value->unit_id,
                    'unit_name'                  => $value->unit_name,
                    'qty'                        => $value->qty,
                    'qty_stock'                  => $value->qty_stock,
                    'qty_pay_sub'                => $value->qty_pay_sub,
                    'one_price'                  => $value->one_price,
                    'total_price'                => $value->one_price*$value->qty_pay_sub,
                    'lot_no'                     => $value->lot_no,
                    'date_export'                => $value->date_export,
                    'user_id'                    => Auth::user()->id
                ]);
            }
        }

        $total_price     = Wh_pay_sub::where('wh_pay_id',$request->wh_pay_id)->sum('total_price');
        Wh_pay::where('wh_pay_id',$request->wh_pay_id)->update([
            'total_price'               => $total_price,
            'active'                    => 'APPROVE',
            'user_export_pay'           => Auth::user()->id
        ]);
        // APPROVE


        return response()->json([
            'status'    => '200'
        ]);

    }
    public function wh_sub_main_paysub_save(Request $request)
    {
        $ynew          = substr($request->bg_yearnow,2,2);
        // $idpro         = $request->pro_id;
        // $pro           = Wh_product::where('pro_id',$idpro)->first();
        // $proid         = $pro->pro_id;
        // $proname       = $pro->pro_name;
        // $unitid        = $pro->unit_id;
        $wh_stock_dep_sub_id = $request->wh_stock_dep_sub_id;
        $dep_sub             = Wh_stock_dep_sub::where('wh_stock_dep_sub_id',$wh_stock_dep_sub_id)->first();
        $proid               = $dep_sub->pro_id;
        $procode             = $dep_sub->pro_code;
        $proname             = $dep_sub->pro_name;
        $unitid              = $dep_sub->unit_id;
        $nameunit            = $dep_sub->unit_name;
        $one_price           = $dep_sub->one_price;
        $lot_no              = $dep_sub->lot_no;
        $depsubsubid         = Auth::user()->dep_subsubtrueid;
        $wh_pay_id           = $request->wh_pay_id;

        $pro_check            = Wh_pay_sub::where('wh_pay_id',$wh_pay_id)->where('pro_id',$proid)->where('pay_year',$request->data_year)->where('stock_list_subid',$depsubsubid)->where('lot_no',$lot_no)->count();
        // $pro_check     = Wh_pay_sub::where('wh_pay_id',$request->wh_pay_id)->where('pro_id',$proid)->where('pay_year',$request->data_year)->where('stock_list_subid',$depsubsubid)->count();
        if ($pro_check > 0) {
            Wh_pay_sub::where('wh_pay_id',$wh_pay_id)->where('pro_id',$proid)->where('pay_year',$request->data_year)->where('stock_list_subid',$depsubsubid)->where('lot_no',$lot_no)->update([
                // 'wh_stock_dep_sub_id'  => $wh_stock_dep_sub_id,
                // 'wh_pay_id'            => $wh_pay_id,
                // 'stock_list_subid'     => $depsubsubid,
                // 'pay_year'             => $request->data_year,
                // 'pro_id'               => $proid,
                // 'pro_code'             => $procode,
                // 'pro_name'             => $proname,
                'unit_id'              => $unitid,
                'unit_name'            => $nameunit,
                'qty'                  => $request->qty,
                'one_price'            => $one_price,
                'total_price'          => $one_price*$request->qty,
                // 'lot_no'               => $lot_no,
                'user_id'              => Auth::user()->id
            ]);
        } else {
            Wh_pay_sub::insert([
                'wh_stock_dep_sub_id'  => $wh_stock_dep_sub_id,
                'wh_pay_id'            => $wh_pay_id,
                'stock_list_subid'     => $depsubsubid,
                'pay_year'             => $request->data_year,
                'pro_id'               => $proid,
                'pro_code'             => $procode,
                'pro_name'             => $proname,
                'unit_id'              => $unitid,
                'unit_name'            => $nameunit,
                'qty'                  => $request->qty,
                'one_price'            => $one_price,
                'total_price'          => $one_price*$request->qty,
                'lot_no'               => $lot_no,
                'user_id'              => Auth::user()->id
            ]);
        }

        return back();

    }
    function wh_subpay_select_lot(Request $request)
    {
        $wh_pay_id   = $request->get('wh_pay_id');
        $pro_id      = $request->get('pro_id');
        $stock_          = DB::table('wh_pay')->where('wh_pay_id', '=', $wh_pay_id)->first();
        $stock_list_subid   = $stock_->stock_list_subid;
        $count           = $request->get('count');
        $detailstocks      = DB::select(
            'SELECT a.wh_stock_sub_id,b.wh_stock_dep_sub_id,a.stock_year,a.stock_list_subid,a.stock_list_subname,a.pro_id,a.pro_code,a.pro_name,a.unit_id,a.unit_name
            ,b.qty,b.qty_pay,c.qty_pay_sub,b.one_price,b.total_price,b.lot_no,b.qty_pay as qty_stock
            -- ,(SUM(b.qty_pay)-SUM(c.qty_pay_sub)) as stocktotal
            -- ,(SELECT SUM(qty_pay) FROM wh_stock_dep_sub WHERE stock_list_subid = a.stock_list_subid AND lot_no = b.lot_no) as total_stocknew
            ,(SELECT SUM(qty_pay) FROM wh_stock_dep_sub WHERE stock_list_subid = a.stock_list_subid AND pro_id = a.pro_id) as total_rep
			,(SELECT SUM(qty_pay_sub) FROM wh_stock_dep_sub_export WHERE stock_list_subid = a.stock_list_subid AND pro_id = a.pro_id) as total_pay
			,(SELECT SUM(qty_pay) FROM wh_stock_dep_sub WHERE stock_list_subid = a.stock_list_subid AND pro_id = a.pro_id)-(SELECT SUM(qty_pay_sub) FROM wh_stock_dep_sub_export WHERE stock_list_subid = a.stock_list_subid AND pro_id = a.pro_id) as totall_all

            FROM wh_stock_sub a
            LEFT JOIN wh_stock_dep_sub b ON b.pro_id = a.pro_id AND b.stock_list_subid = a.stock_list_subid
            LEFT JOIN wh_stock_dep_sub_export c ON c.lot_no = b.lot_no AND c.stock_list_subid = a.stock_list_subid
            WHERE a.stock_list_subid = "'.$stock_list_subid.'" AND a.pro_id = "'.$pro_id.'"
            GROUP BY a.pro_id
        ');

        $output = '
                <table class="table-bordered table-striped table-vcenter" style="width: 100%;">
                    <thead style="background-color: rgb(43, 86, 136)">
                        <tr>
                            <td style="text-align: center;border: 1px solid black;font-size: 13px;color:white;" width="20%">รหัส</td>
                            <td style="text-align: center;border: 1px solid black;font-size: 13px;color:white;" >รายการวัสดุ</td>
                            <td style="text-align: center;border: 1px solid black;font-size: 13px;color:white;" >ราคา</td>
                            <td style="text-align: center;border: 1px solid black;font-size: 13px;color:white;" >LOT</td>
                            <td style="text-align: center;border: 1px solid black;font-size: 13px;color:white;" width="12%">คงเหลือ</td>
                            <td style="text-align: center;border: 1px solid black;font-size: 13px;color:white;" width="12%">จ่าย</td>
                            <td style="text-align: center;border: 1px solid black;font-size: 13px;color:white;" width="6%">เลือก</td>
                        </tr>
                    </thead>
                    <tbody id="myTable">';
                        foreach ($detailstocks as $item) {

                            if ($item->total_pay == '') {
                                $output .= '
                                    <tr height="20">
                                        <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;">' . $item->pro_code . '</td>
                                        <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="left" >' . $item->pro_name . '</td>
                                        <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="center" >' . $item->one_price . '</td>
                                        <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="center" >' . $item->lot_no . '</td>
                                        <td class="text-font" style="border: 1px solid black;padding-right:10px;font-size: 13px;" align="right" >' . $item->total_rep. '</td>
                                        <td class="text-font" style="border: 1px solid black;padding-right:10px;font-size: 13px;" align="right" ><input class="form-control form-control-sm" name="qty_sub_pay'.$item->wh_stock_dep_sub_id.'" id="qty_sub_pay'.$item->wh_stock_dep_sub_id.'" type="text"></td>
                                        <td class="text-font" style="border: 1px solid black;" align="center" >
                                        <button type="button" class="btn btn-outline-primary btn-sm"  style="font-family: \'Kanit\', sans-serif; font-size: 13px;font-weight:normal;" onclick="selectsuppay('.$item->wh_stock_dep_sub_id.','.$count.')">เลือก</button></td>
                                    </tr>';
                            } else {
                                $output .= '
                                    <tr height="20">
                                        <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;">' . $item->pro_code . '</td>
                                        <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="left" >' . $item->pro_name . '</td>
                                        <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="center" >' . $item->one_price . '</td>
                                        <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="center" >' . $item->lot_no . '</td>
                                        <td class="text-font" style="border: 1px solid black;padding-right:10px;font-size: 13px;" align="right" >' . $item->totall_all. '</td>
                                        <td class="text-font" style="border: 1px solid black;padding-right:10px;font-size: 13px;" align="right" ><input class="form-control form-control-sm" name="qty_sub_pay'.$item->wh_stock_dep_sub_id.'" id="qty_sub_pay'.$item->wh_stock_dep_sub_id.'" type="text"></td>
                                        <td class="text-font" style="border: 1px solid black;" align="center" >
                                        <button type="button" class="btn btn-outline-primary btn-sm"  style="font-family: \'Kanit\', sans-serif; font-size: 13px;font-weight:normal;" onclick="selectsuppay('.$item->wh_stock_dep_sub_id.','.$count.')">เลือก</button></td>
                                    </tr>';
                            }


                        // $output .= '
                        //     <tr height="20">
                        //         <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;">' . $item->pro_code . '</td>
                        //         <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="left" >' . $item->pro_name . '</td>
                        //         <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="center" >' . $item->one_price . '</td>
                        //         <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="center" >' . $item->lot_no . '</td>
                        //         <td class="text-font" style="border: 1px solid black;padding-right:10px;font-size: 13px;" align="right" >' . $item->totall_all. '</td>
                        //         <td class="text-font" style="border: 1px solid black;padding-right:10px;font-size: 13px;" align="right" ><input class="form-control form-control-sm" name="qty_sub_pay'.$item->wh_stock_dep_sub_id.'" id="qty_sub_pay'.$item->wh_stock_dep_sub_id.'" type="text"></td>
                        //         <td class="text-font" style="border: 1px solid black;" align="center" >
                        //         <button type="button" class="btn btn-outline-primary btn-sm"  style="font-family: \'Kanit\', sans-serif; font-size: 13px;font-weight:normal;" onclick="selectsuppay('.$item->wh_stock_dep_sub_id.','.$count.')">เลือก</button></td>
                        //     </tr>';
                        }
                    $output .= '</tbody>
                    </table>';
        echo $output;
    }
    public function wh_subpay_select_lotsave(Request $request)
    {
        $depsubsubid         = Auth::user()->dep_subsubtrueid;
        $iduser              = Auth::user()->id;
        $wh_pay_id           = $request->wh_pay_id;
        $qty_pay_sub         = $request->qty_sub_pay;
        $wh_stock_dep_sub_id = $request->wh_stock_dep_sub_id;
        $dep_sub             = Wh_stock_dep_sub::where('wh_stock_dep_sub_id',$wh_stock_dep_sub_id)->first();
        $proid               = $dep_sub->pro_id;
        $procode             = $dep_sub->pro_code;
        $proname             = $dep_sub->pro_name;
        $unitid              = $dep_sub->unit_id;
        $unitname            = $dep_sub->unit_name;
        $qty                 = $dep_sub->qty;
        $qty_stock           = $dep_sub->qty_pay;
        $one_price           = $dep_sub->one_price;
        $total_price         = $dep_sub->total_price;
        $lot_no              = $dep_sub->lot_no;
        $wh_stock_dep_id          = $dep_sub->wh_stock_dep_id;
        $wh_stock_export_sub_id   = $dep_sub->wh_stock_export_sub_id;
        $wh_stock_export_id       = $dep_sub->wh_stock_export_id;
        $wh_request_id            = $dep_sub->wh_request_id;
        $stock_list_id            = $dep_sub->stock_list_id;
        $stock_list_subid         = $dep_sub->stock_list_subid;
        $stock_sub_year           = $dep_sub->stock_sub_year;
        $datenow                  = date('Y-m-d');

        // $data['wh_stock']      = DB::select(
        //     'SELECT a.wh_stock_sub_id,a.stock_year,a.stock_list_subid,a.stock_list_subname,a.pro_id,a.pro_code,a.pro_name,a.unit_id,a.unit_name
        //     ,b.qty,SUM(b.qty_pay) as qty_stock,c.qty_pay_sub,c.total_stock,b.one_price,b.total_price,b.lot_no
        //     ,(SUM(b.qty_pay)-c.qty_pay_sub) as stocktotal
        //     FROM wh_stock_sub a
        //     LEFT JOIN wh_stock_dep_sub b ON b.pro_id = a.pro_id AND b.stock_list_subid = a.stock_list_subid
        //     LEFT JOIN wh_stock_dep_sub_export c ON c.lot_no = b.lot_no AND c.stock_list_subid = a.stock_list_subid
        //     WHERE a.stock_list_subid = "'.$stock_list_subid.'"
        //     GROUP BY a.pro_id

        // ');


        // $count                    = Wh_stock_dep_sub_export::where('wh_stock_dep_id',$wh_stock_dep_id)->where('wh_pay_id',$wh_pay_id)->where('pro_id',$proid)->count();
        $count                    = Wh_stock_dep_sub_export::where('wh_pay_id',$wh_pay_id)->where('pro_id',$proid)->where('lot_no',$lot_no)->count();
        if ($count > 0) {
            // $count_stock              = Wh_stock_dep_sub::where('stock_list_subid',$stock_list_subid)->where('pro_id',$proid)->where('lot_no',$lot_no)->first();
            // Wh_stock_dep_sub_export::where('wh_stock_dep_id',$wh_stock_dep_id)->where('wh_pay_id',$wh_pay_id)->where('pro_id',$proid)->update([
            // Wh_stock_dep_sub_export::where('wh_pay_id',$wh_pay_id)->where('pro_id',$proid)->update([
            //     'qty_pay_sub'             => $qty_pay_sub,
            //     'total_price'             => $qty_pay_sub*$one_price,
            //     'total_stock'             => $count_stock->qty_pay-$qty_pay_sub,
            //     'lot_no'                  => $lot_no,
            //     'user_id'                 => $iduser,
            //     'date_export'             => $datenow,
            // ]);
        } else {
            $count_stock                  = Wh_stock_dep_sub::where('stock_list_subid',$stock_list_subid)->where('pro_id',$proid)->first();
            $sum_repqty                   = Wh_stock_dep_sub::where('stock_list_subid',$stock_list_subid)->where('pro_id',$proid)->sum('qty_pay');
            $sum_payqty                   = Wh_stock_dep_sub_export::where('stock_list_subid',$stock_list_subid)->where('pro_id',$proid)->sum('qty_pay_sub');
            Wh_stock_dep_sub_export::insert([
                'wh_pay_id'               => $wh_pay_id,
                'wh_stock_dep_id'         => $wh_stock_dep_id,
                'wh_stock_export_sub_id'  => $wh_stock_export_sub_id,
                'wh_stock_export_id'      => $wh_stock_export_id,
                'wh_request_id'           => $wh_request_id,
                'stock_list_id'           => $stock_list_id,
                'stock_list_subid'        => $stock_list_subid,
                'stock_sub_year'          => $stock_sub_year,

                'pro_id'                  => $proid,
                'pro_code'                => $procode,
                'pro_name'                => $proname,
                'qty'                     => $qty,
                'qty_stock'               => $sum_repqty-$sum_payqty,

                'qty_pay_sub'             => $qty_pay_sub,
                // 'total_stock'             => $count_stock->qty_pay-$qty_pay_sub,
                'total_stock'             => ($sum_repqty-$sum_payqty)-$qty_pay_sub,
                'unit_id'                 => $unitid,
                'unit_name'               => $unitname,
                'one_price'               => $one_price,
                'total_price'             => $qty_pay_sub*$one_price,
                'lot_no'                  => $lot_no,
                'user_id'                 => $iduser,
                'date_export'             => $datenow,
            ]);
        }

        //     Wh_request_sub::where('wh_request_id',$wh_request_id)->where('pro_id',$idrecieve_sub->pro_id)->update([
        //         'lot_no'            => $idrecieve_sub->lot_no,
        //     ]);
        // }

        return response()->json([
            'status'     => '200'
        ]);

    }
    public function load_data_table_pay(Request $request)
    {
        // {{url('wh_recieve_destroy')}}
        // <img src="'.{{ asset('images/removewhite.png') }}.'" class="me-2 ms-2" height="18px" width="18px">
        $id              = $request->wh_pay_id;
        $wh_pay_sub      = DB::select('SELECT * FROM wh_stock_dep_sub_export WHERE wh_pay_id = "'.$id.'" ORDER BY wh_stock_dep_sub_export_id DESC');
        $count           = $request->get('count');

        $output='
                <table class="table table-bordered border-primary table-hover table-sm" style="border-collapse: collapse;border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center" style="background-color: rgb(255, 251, 228);font-size: 12px;">ลำดับ</th>

                                <th class="text-center" style="background-color: rgb(174, 236, 245);font-size: 12px;">รายการ</th>
                                <th class="text-center" style="background-color: rgb(174, 236, 245);font-size: 12px;">หน่วยนับ</th>
                                <th class="text-center" style="background-color: rgb(250, 194, 187);font-size: 12px;">LOT</th>
                                <th class="text-center" style="background-color: rgb(187, 250, 221);font-size: 12px;">จำนวน</th>
                                <th class="text-center" style="background-color: rgb(222, 201, 248);font-size: 12px;" width="10%">ราคา</th>
                                <th class="text-center" style="background-color: rgb(248, 201, 221);font-size: 12px;" width="10%">ราคารวม</th>
                                <th width="5%" class="text-center"> ลบ </th>
                            </tr>
                        </thead>
                    <tbody>
                        ';

                        $i = 1;
                        foreach ($wh_pay_sub as $key => $value) {

                            $output.='
                              <tr id="tr_'.$value->wh_stock_dep_sub_export_id.'">
                                <td class="text-center">'.$i++.'</td>

                                <td>'.$value->pro_name.'</td>
                                <td class="text-center">'.$value->unit_name.'</td>
                                <td class="text-center" width="width="5%"%">'.$value->lot_no.'</td>
                                <td class="text-center" width="10%">'.$value->qty_pay_sub.'</td>
                                <td class="text-center" width="10%">'.number_format($value->one_price, 2).'</td>
                                <td class="text-center" width="10%">'.number_format($value->total_price, 2).'</td>
                                <td class="text-center" width="5%">
                                    <button type="button" class="ladda-button me-2 btn-pill btn btn-sm btn-danger btn-sm"  style="font-family: \'Kanit\', sans-serif; font-size: 13px;font-weight:normal;" onclick="whsubpay_destroy('.$value->wh_stock_dep_sub_export_id.','.$count.')"> <i class="fa-solid fa-trash-can text-white"></i> </button>
                                 </td>
                            </tr>';
                        }

                        $output.='
                    </tbody>
                </table>

        ';
        echo $output;
    }
    // <th class="text-center" style="background-color: rgb(174, 236, 245);font-size: 12px;">รหัส</th>
    // <td>'.$value->wh_stock_dep_sub_export_id.'</td>
    public function wh_sub_pay_destroy(Request $request)
    {
        $id  = $request->wh_stock_dep_sub_export_id;
        wh_stock_dep_sub_export::whereIn('wh_stock_dep_sub_export_id',explode(",",$id))->delete();

        return response()->json([
            'status'    => '200'
        ]);
    }
    public function wh_sub_main_payedittable(Request $request)
    {
        if ($request->ajax()) {
            if ($request->action == 'Edit') {
                $data  = array(
                    // 'lot_no'        => $request->lot_no,
                    'qty'           => $request->qty,
                    // 'one_price'     => $request->one_price,
                    // 'total_price'   => $request->qty * $request->one_price,
                );
                DB::connection('mysql')->table('wh_pay_sub')->where('wh_pay_sub_id', $request->wh_pay_sub_id)->update($data);
            }
            return response()->json([
                'status'     => '200'
            ]);
        }
    }
    public function wh_sub_main_paysub_update(Request $request)
    {
        $id              = $request->wh_pay_id;

        $sum_total       = Wh_pay_sub::where('wh_pay_id',$id)->sum('total_price');
        Wh_pay::where('wh_pay_id',$id)->update([
            'total_price'  => $sum_total,
            'active'       => 'APPROVE',
        ]);

        return response()->json([
            'status'    => '200'
        ]);

    }
    // **************************** DATAIL *********************************
    public function wh_sub_main_detail(Request $request)
    {
        $id            = $request->wh_request_id;
        // $data_sub      = DB::table('wh_request_sub')
        // ->leftjoin('users', 'users.user_id', '=', 'wh_request_sub.user_id')
        // ->where('wh_request_sub.wh_request_id',$id)->get();
        // dd($id);
        $datarep_      = Wh_request_sub::where('wh_request_id',$id)->first();
        $datarep       = $datarep_->request_no;
        $count        = Wh_request_subpay::where('wh_request_id',$id)->count();
        // dd($count);
        if ($count > 0) {
            $data_subpay     = DB::select(
                'SELECT a.*,concat(u.fname," ",u.lname) as ptname
                FROM wh_request_subpay a
                LEFT JOIN users u ON u.id = a.user_id
                WHERE a.wh_request_id = "'.$id.'"
                GROUP BY a.pro_id
                ORDER BY a.pro_id ASC
            ');
            $output = '
            <table class="table table-sm table-bordered table-striped" style="width: 100%;">
                <thead style="background-color: #CCCCFF">
                    <tr>
                        <td style="text-align: center;border: 1px solid black;font-size: 13px;color:#6495ED;" width="10%">ลำดับ</td>
                        <td style="text-align: center;border: 1px solid black;font-size: 13px;color:#6495ED;" >รายการวัสดุ</td>
                        <td style="text-align: center;border: 1px solid black;font-size: 13px;color:#6495ED;" width="7%">จำนวน(เบิก)</td>
                        <td style="text-align: center;border: 1px solid black;font-size: 13px;color:#6495ED;" width="7%">จำนวน(จ่าย)</td>
                        <td style="text-align: center;border: 1px solid black;font-size: 13px;color:#6495ED;" width="10%">หน่วยนับ</td>
                        <td style="text-align: center;border: 1px solid black;font-size: 13px;color:#6495ED;" width="10%">ผู้จ่าย</td>
                    </tr>
                </thead>
                <tbody id="myTable">';
                    foreach ($data_subpay as $itemp) {
                    $output .= '
                        <tr height="20">
                            <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="center" width="10%">' . $itemp->pro_code . '</td>
                            <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="left">' . $itemp->pro_name . '</td>
                            <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="center" width="7%">' . $itemp->qty . '</td>
                            <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="center" width="7%">' . $itemp->qty_pay . '</td>
                            <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="center" width="10%">' . $itemp->unit_name . '</td>
                            <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="center" width="10%">' . $itemp->ptname . '</td>
                        </tr>';
                    }
                $output .= '</tbody>
                </table>';
            echo $output;
        } else {
            $data_sub     = DB::select(
                'SELECT a.*,concat(u.fname," ",u.lname) as ptname
                FROM wh_request_sub a
                LEFT JOIN users u ON u.id = a.user_id
                WHERE a.wh_request_id = "'.$id.'"
                GROUP BY a.pro_id
                ORDER BY a.pro_id ASC
            ');
            $output = '
            <table class="table table-sm table-bordered table-striped" style="width: 100%;">
                <thead style="background-color: #CCCCFF">
                    <tr>
                        <td style="text-align: center;border: 1px solid black;font-size: 13px;color:#6495ED;" width="10%">ลำดับ</td>
                        <td style="text-align: center;border: 1px solid black;font-size: 13px;color:#6495ED;" >รายการวัสดุ</td>
                        <td style="text-align: center;border: 1px solid black;font-size: 13px;color:#6495ED;" width="10%">จำนวน(เบิก)</td>
                        <td style="text-align: center;border: 1px solid black;font-size: 13px;color:#6495ED;" width="10%">จำนวน(จ่าย)</td>
                        <td style="text-align: center;border: 1px solid black;font-size: 13px;color:#6495ED;" width="10%">หน่วยนับ</td>
                        <td style="text-align: center;border: 1px solid black;font-size: 13px;color:#6495ED;" width="20%">ผู้เบิก</td>
                    </tr>
                </thead>
                <tbody id="myTable">';
                    foreach ($data_sub as $item) {
                    $output .= '
                        <tr height="20">
                            <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;">' . $item->pro_code . '</td>
                            <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="left" >' . $item->pro_name . '</td>
                            <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="center" >' . $item->qty . '</td>
                            <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="center" >' . $item->qty_pay . '</td>
                            <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="center" >' . $item->unit_name . '</td>
                            <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="center" >' . $item->ptname . '</td>
                        </tr>';
                    }
                $output .= '</tbody>
                </table>';
            echo $output;
        }



    }
    // **************************** DATAIL PAY *********************************
    public function wh_sub_main_paydetail(Request $request)
    {
        $id            = $request->wh_pay_id;
        // $data_sub      = DB::table('wh_request_sub')
        // ->leftjoin('users', 'users.user_id', '=', 'wh_request_sub.user_id')
        // ->where('wh_request_sub.wh_request_id',$id)->get();

        $data_sub     = DB::select(
            'SELECT a.*,concat(u.fname," ",u.lname) as ptname
            FROM wh_pay_sub a
            LEFT JOIN users u ON u.id = a.user_id
            WHERE a.wh_pay_id = "'.$id.'"
            GROUP BY a.pro_id
            ORDER BY a.pro_id ASC
        ');

        // <td style="text-align: center;border: 1px solid black;font-size: 13px;color:#6495ED;" width="10%">จำนวน(เบิก)</td>
        // <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="center" >' . $item->qty . '</td>
        $output = '
        <table class="table-bordered table-striped table-vcenter" style="width: 100%;">
            <thead style="background-color: #CCCCFF">
                <tr>
                    <td style="text-align: center;border: 1px solid black;font-size: 13px;color:#6495ED;" width="10%">ลำดับ</td>
                    <td style="text-align: center;border: 1px solid black;font-size: 13px;color:#6495ED;" >รายการวัสดุ</td>

                    <td style="text-align: center;border: 1px solid black;font-size: 13px;color:#6495ED;" width="10%">จำนวน(จ่าย)</td>
                    <td style="text-align: center;border: 1px solid black;font-size: 13px;color:#6495ED;" width="10%">หน่วยนับ</td>
                    <td style="text-align: center;border: 1px solid black;font-size: 13px;color:#6495ED;" width="20%">ผู้ตัดจ่าย</td>
                </tr>
            </thead>
            <tbody id="myTable">';
                foreach ($data_sub as $item) {
                    // $data_pay     = DB::select('SELECT qty_pay FROM wh_request_subpay WHERE wh_request_id = "'.$item->wh_request_id.'" AND pro_code = "'.$item->pro_code.'"');
                    $data_pay     = Wh_request_subpay::where('wh_request_id',$item->wh_request_id)->where('pro_code',$item->pro_code)->first();
                    $qty_pay      = $data_pay->qty_pay;
                $output .= '
                    <tr height="20">
                        <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;">' . $item->pro_code . '</td>
                        <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="left" >' . $item->pro_name . '</td>

                        <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="center" >' . $item->qty_pay_sub . '</td>
                        <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="center" >' . $item->unit_name . '</td>
                        <td class="text-font" style="border: 1px solid black;padding-left:10px;font-size: 13px;" align="center" >' . $item->ptname . '</td>
                    </tr>';
                }
            $output .= '</tbody>
            </table>';
        echo $output;

    }







}
