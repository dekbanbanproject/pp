<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\support\Facades\Hash;
use Illuminate\support\Facades\Validator;
use App\Models\User;
use App\Models\Operate_time;
use App\Models\Operate_time_dss;
use App\Models\Operate_time_dsst;
use App\Models\Operate_time_dep;
use App\Models\Operate_time_dept;
// use App\Models\Operate_time_dss;
// use App\Models\Operate_time_dsst;
use PDF;
use Auth;
use setasign\Fpdi\Fpdi;
use App\Models\Budget_year;
use Illuminate\Support\Facades\File;
use DataTables;
use Intervention\Image\ImageManagerStatic as Image;

class TimerController extends Controller
{
    public function time_index(Request $request)
    {
        $debsubsubid         = Auth::user()->dep_subsubtrueid;
        $startdate           = $request->startdate;
        $enddate             = $request->enddate;
        $debsubsub           = $request->department_subsub_id;

        $deb                 = Auth::user()->dep_id;
        $startdate4          = $request->startdate4;
        $enddate4            = $request->enddate4;
        $debid               = $request->department_id;

        // $debsub              = $request->HR_DEPARTMENT_SUB_ID;

        $data['date_nows']   = date('Y-m-d');
        $datenow             = date('Y-m-d');
        $months              = date('m');
        $year                = date('Y');
        $newday              = date('Y-m-d', strtotime($datenow . ' -1 Day')); //ย้อนหลัง 1 วัน
        $iduser              = Auth::user()->id;


        if ($startdate =='') {
            // *************** หน่วยงาน ******************************
            Operate_time_dss::where('user_id',$iduser)->delete();
            Operate_time_dsst::where('user_id',$iduser)->delete();
            $show_subsub = DB::connection('mysql6')->select(
                'SELECT p.ID,c.CHEACKIN_DATE
                        ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                        ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                        ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname ,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                        ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME ,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                    FROM checkin_index c
                    LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                    LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                    LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                    LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                    LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                    LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                    LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                    WHERE c.CHEACKIN_DATE BETWEEN "'.$newday.'" and "'.$datenow.'"
                    AND d.HR_DEPARTMENT_SUB_SUB_ID = "'.$debsubsubid.'"
                    GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                    ORDER BY c.CHEACKIN_DATE,d.HR_DEPARTMENT_SUB_SUB_ID,p.ID,ot.OPERATE_TYPE_ID
            ');
            foreach ($show_subsub as $key => $value) {
                $start = strtotime($value->CHEACKINTIME);
                $end = strtotime($value->CHEACKOUTTIME);
                if ($end == '') {
                    $tot = '';
                }elseif ($start == '') {
                    $tot = '';
                }elseif ($end < $start) {
                    $tot = '';
                } else {
                    $tot_ = ($end - $start) / 3600;
                    $tot = number_format($tot_,2);
                }
                $date1 = date_create($value->CHEACKINTIME);
                $date2 = date_create($value->CHEACKOUTTIME);

                $diff = date_diff($date1, $date2);
                $totalhr = $diff->format('%R%H ชม.');

                Operate_time_dss::insert([
                    'operate_time_dss_date'        => $value->CHEACKIN_DATE,
                    'operate_time_dss_personid'    => $value->ID,
                    'operate_time_dss_person'      => $value->hrname,
                    'operate_time_dss_dssid'       => $value->HR_DEPARTMENT_SUB_SUB_ID,
                    'operate_time_dss_dss'         => $value->HR_DEPARTMENT_SUB_SUB_NAME,
                    'operate_time_dss_typeid'      => $value->OPERATE_TYPE_ID,
                    'operate_time_dss_typename'    => $value->OPERATE_TYPE_NAME,
                    'operate_time_dss_in'          => $value->CHEACKINTIME,
                    'operate_time_dss_out'         => $value->CHEACKOUTTIME,
                    'operate_time_dss_otin'        => '',
                    'operate_time_dss_otout'       => '',
                    'totaltime_narmal'             => $tot,
                    'totaltime_ot'                 => '',
                    'user_id'                      => $iduser
                ]);
            }
            Operate_time_dsst::insert([
                'start_date'         => $startdate,
                'end_date'           => $enddate,
                'depsubsubid'        => $debsubsub,
                'user_id'            => $iduser
            ]);

            // *************** กลุ่มภารกิจ ******************************
            Operate_time_dep::where('user_id',$iduser)->delete();
            Operate_time_dept::where('user_id',$iduser)->delete();
            $show_dep = DB::connection('mysql6')->select(
                'SELECT p.ID,c.CHEACKIN_DATE,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                    ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                    ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                    ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                FROM checkin_index c
                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                WHERE c.CHEACKIN_DATE BETWEEN "'.$newday.'" and "'.$datenow.'"
                AND hs.HR_DEPARTMENT_ID = "'.$deb.'"
                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                ORDER BY c.CHEACKIN_DATE,d.HR_DEPARTMENT_SUB_SUB_ID,p.ID,ot.OPERATE_TYPE_ID
            ');
            foreach ($show_dep as $key => $value2) {
                $start2 = strtotime($value2->CHEACKINTIME);
                $end2 = strtotime($value2->CHEACKOUTTIME);
                if ($end2 == '') {
                    $tot2 = '';
                }elseif ($start == '') {
                    $tot2 = '';
                }elseif ($end2 < $start2) {
                    $tot2 = '';
                } else {
                    $tot2_ = ($end2 - $start2) / 3600;
                    $tot2 = number_format($tot2_,2);
                }
                $date11 = date_create($value2->CHEACKINTIME);
                $date22 = date_create($value2->CHEACKOUTTIME);

                $diff2 = date_diff($date11, $date22);
                $totalhr2 = $diff2->format('%R%H ชม.');
                Operate_time_dep::insert([
                    'operate_time_dep_date'        => $value2->CHEACKIN_DATE,
                    'operate_time_dep_personid'    => $value2->ID,
                    'operate_time_dep_person'      => $value2->hrname,
                    'operate_time_depid'           => $value2->HR_DEPARTMENT_ID,
                    'operate_time_depname'         => $value2->HR_DEPARTMENT_NAME,
                    'operate_time_dep_typeid'      => $value2->OPERATE_TYPE_ID,
                    'operate_time_dep_typename'    => $value2->OPERATE_TYPE_NAME,
                    'operate_time_dep_in'          => $value2->CHEACKINTIME,
                    'operate_time_dep_out'         => $value2->CHEACKOUTTIME,
                    'operate_time_dep_otin'        => '',
                    'operate_time_dep_otout'       => '',
                    'totaltime_narmal'             => $tot2,
                    'totaltime_ot'                 => '',
                    'user_id'                      => $iduser
                ]);
            }
            Operate_time_dept::insert([
                'start_date'         => $startdate,
                'end_date'           => $enddate,
                'depid'              => $debid,
                'user_id'            => $iduser
            ]);




        } else {
             // *************** หน่วยงาน ******************************
            Operate_time_dss::where('user_id',$iduser)->delete();
            Operate_time_dsst::where('user_id',$iduser)->delete();
            if ($debsubsub == '') {
                $show_subsub = DB::connection('mysql6')->select(
                    'SELECT p.ID,c.CHEACKIN_DATE
                            ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                            ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                            ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname ,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                            ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME ,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                        FROM checkin_index c
                        LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                        LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                        LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                        LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                        LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                        LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                        LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                        WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                        AND d.HR_DEPARTMENT_SUB_SUB_ID = "'.$debsubsubid.'"
                        GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                        ORDER BY c.CHEACKIN_DATE,d.HR_DEPARTMENT_SUB_SUB_ID,p.ID,ot.OPERATE_TYPE_ID
                ');
                foreach ($show_subsub as $key => $value) {
                    $start = strtotime($value->CHEACKINTIME);
                    $end = strtotime($value->CHEACKOUTTIME);
                    if ($end == '') {
                        $tot = '';
                    }elseif ($start == '') {
                        $tot = '';
                    }elseif ($end < $start) {
                        $tot = '';
                    } else {
                        $tot_ = ($end - $start) / 3600;
                        $tot = number_format($tot_,2);
                    }
                    $date1 = date_create($value->CHEACKINTIME);
                    $date2 = date_create($value->CHEACKOUTTIME);

                    $diff = date_diff($date1, $date2);
                    $totalhr = $diff->format('%R%H ชม.');

                    Operate_time_dss::insert([
                        'operate_time_dss_date'        => $value->CHEACKIN_DATE,
                        'operate_time_dss_personid'    => $value->ID,
                        'operate_time_dss_person'      => $value->hrname,
                        'operate_time_dss_dss'         => $value->HR_DEPARTMENT_SUB_SUB_NAME,
                        'operate_time_dss_typeid'      => $value->OPERATE_TYPE_ID,
                        'operate_time_dss_typename'    => $value->OPERATE_TYPE_NAME,
                        'operate_time_dss_in'          => $value->CHEACKINTIME,
                        'operate_time_dss_out'         => $value->CHEACKOUTTIME,
                        'operate_time_dss_otin'        => '',
                        'operate_time_dss_otout'       => '',
                        'totaltime_narmal'             => $tot,
                        'totaltime_ot'                 => '',
                        'user_id'                      => $iduser
                    ]);
                }
                Operate_time_dsst::insert([
                    'start_date'         => $startdate,
                    'end_date'           => $enddate,
                    'depsubsubid'        => $debsubsub,
                    'user_id'            => $iduser
                ]);
            } else {
                $show_subsub = DB::connection('mysql6')->select(
                    'SELECT p.ID,c.CHEACKIN_DATE,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                            ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                            ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname ,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                            ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME ,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                        FROM checkin_index c
                        LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                        LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                        LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                        LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                        LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                        LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                        LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                        WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                        AND d.HR_DEPARTMENT_SUB_SUB_ID = "'.$debsubsub.'"
                        GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                        ORDER BY c.CHEACKIN_DATE,d.HR_DEPARTMENT_SUB_SUB_ID,p.ID,ot.OPERATE_TYPE_ID
                ');
                foreach ($show_subsub as $key => $value) {
                    $start = strtotime($value->CHEACKINTIME);
                    $end = strtotime($value->CHEACKOUTTIME);
                    if ($end == '') {
                        $tot = '';
                    }elseif ($start == '') {
                        $tot = '';
                    }elseif ($end < $start) {
                        $tot = '';
                    } else {
                        $tot_ = ($end - $start) / 3600;
                        $tot = number_format($tot_,2);
                    }
                    $date1 = date_create($value->CHEACKINTIME);
                    $date2 = date_create($value->CHEACKOUTTIME);

                    $diff = date_diff($date1, $date2);
                    $totalhr = $diff->format('%R%H ชม.');

                    Operate_time_dss::insert([
                        'operate_time_dss_date'        => $value->CHEACKIN_DATE,
                        'operate_time_dss_personid'    => $value->ID,
                        'operate_time_dss_person'      => $value->hrname,
                        'operate_time_dss_dssid'       => $value->HR_DEPARTMENT_SUB_SUB_ID,
                        'operate_time_dss_dss'         => $value->HR_DEPARTMENT_SUB_SUB_NAME,
                        'operate_time_dss_typeid'      => $value->OPERATE_TYPE_ID,
                        'operate_time_dss_typename'    => $value->OPERATE_TYPE_NAME,
                        'operate_time_dss_in'          => $value->CHEACKINTIME,
                        'operate_time_dss_out'         => $value->CHEACKOUTTIME,
                        'operate_time_dss_otin'        => '',
                        'operate_time_dss_otout'       => '',
                        'totaltime_narmal'             => $tot,
                        'totaltime_ot'                 => '',
                        'user_id'                      => $iduser
                    ]);
                }
                Operate_time_dsst::insert([
                    'start_date'         => $startdate,
                    'end_date'           => $enddate,
                    'depsubsubid'        => $debsubsub,
                    'user_id'            => $iduser
                ]);
            }

             // *************** กลุ่มภารกิจ ******************************
             Operate_time_dep::where('user_id',$iduser)->delete();
             Operate_time_dept::where('user_id',$iduser)->delete();
            if ($debid =='') {
                $show_dep = DB::connection('mysql6')->select(
                    'SELECT p.ID,c.CHEACKIN_DATE,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                        ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                        ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                        ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                    FROM checkin_index c
                    LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                    LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                    LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                    LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                    LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                    LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                    LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                    WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                    AND hs.HR_DEPARTMENT_ID = "'.$deb.'"
                    GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                    ORDER BY c.CHEACKIN_DATE,d.HR_DEPARTMENT_SUB_SUB_ID,p.ID,ot.OPERATE_TYPE_ID
                ');
                foreach ($show_dep as $key => $value2) {
                    $start3 = strtotime($value2->CHEACKINTIME);
                    $end3 = strtotime($value2->CHEACKOUTTIME);
                    if ($end3 == '') {
                        $tot3 = '';
                    }elseif ($start3 == '') {
                        $tot3 = '';
                    }elseif ($end3 < $start3) {
                        $tot3 = '';
                    } else {
                        $tot3_ = ($end3 - $start3) / 3600;
                        $tot3 = number_format($tot3_,2);
                    }
                    $date33 = date_create($value2->CHEACKINTIME);
                    $date33 = date_create($value2->CHEACKOUTTIME);

                    $diff33 = date_diff($date33, $date33);
                    $totalhr33 = $diff33->format('%R%H ชม.');

                    Operate_time_dep::insert([
                        'operate_time_dep_date'        => $value2->CHEACKIN_DATE,
                        'operate_time_dep_personid'    => $value2->ID,
                        'operate_time_dep_person'      => $value2->hrname,
                        'operate_time_depid'           => $value2->HR_DEPARTMENT_ID,
                        'operate_time_depname'         => $value2->HR_DEPARTMENT_NAME,
                        'operate_time_dep_typeid'      => $value2->OPERATE_TYPE_ID,
                        'operate_time_dep_typename'    => $value2->OPERATE_TYPE_NAME,
                        'operate_time_dep_in'          => $value2->CHEACKINTIME,
                        'operate_time_dep_out'         => $value2->CHEACKOUTTIME,
                        'operate_time_dep_otin'        => '',
                        'operate_time_dep_otout'       => '',
                        'totaltime_narmal'             => $tot3,
                        'totaltime_ot'                 => '',
                        'user_id'                      => $iduser
                    ]);
                }
                Operate_time_dept::insert([
                    'start_date'         => $startdate,
                    'end_date'           => $enddate,
                    'depid'              => $debid,
                    'user_id'            => $iduser
                ]);
            } else {
                $show_dep = DB::connection('mysql6')->select(
                    'SELECT p.ID,c.CHEACKIN_DATE,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                        ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                        ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                        ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                    FROM checkin_index c
                    LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                    LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                    LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                    LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                    LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                    LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                    LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                    WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                    AND hs.HR_DEPARTMENT_ID = "'.$debid.'"
                    GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                    ORDER BY c.CHEACKIN_DATE,d.HR_DEPARTMENT_SUB_SUB_ID,p.ID,ot.OPERATE_TYPE_ID
                ');
                foreach ($show_dep as $key => $value2) {
                    $start3 = strtotime($value2->CHEACKINTIME);
                    $end3 = strtotime($value2->CHEACKOUTTIME);
                    if ($end3 == '') {
                        $tot3 = '';
                    }elseif ($start3 == '') {
                        $tot3 = '';
                    }elseif ($end3 < $start3) {
                        $tot3 = '';
                    } else {
                        $tot3_ = ($end3 - $start3) / 3600;
                        $tot3 = number_format($tot3_,2);
                    }
                    $date33 = date_create($value2->CHEACKINTIME);
                    $date33 = date_create($value2->CHEACKOUTTIME);

                    $diff33 = date_diff($date33, $date33);
                    $totalhr33 = $diff33->format('%R%H ชม.');

                    Operate_time_dep::insert([
                        'operate_time_dep_date'        => $value2->CHEACKIN_DATE,
                        'operate_time_dep_personid'    => $value2->ID,
                        'operate_time_dep_person'      => $value2->hrname,
                        'operate_time_depid'           => $value2->HR_DEPARTMENT_ID,
                        'operate_time_depname'         => $value2->HR_DEPARTMENT_NAME,
                        'operate_time_dep_typeid'      => $value2->OPERATE_TYPE_ID,
                        'operate_time_dep_typename'    => $value2->OPERATE_TYPE_NAME,
                        'operate_time_dep_in'          => $value2->CHEACKINTIME,
                        'operate_time_dep_out'         => $value2->CHEACKOUTTIME,
                        'operate_time_dep_otin'        => '',
                        'operate_time_dep_otout'       => '',
                        'totaltime_narmal'             => $tot3,
                        'totaltime_ot'                 => '',
                        'user_id'                      => $iduser
                    ]);
                }
                Operate_time_dept::insert([
                    'start_date'         => $startdate,
                    'end_date'           => $enddate,
                    'depid'              => $debid,
                    'user_id'            => $iduser
                ]);
            }


        }

        $datashow_ = DB::connection('mysql6')->select(
            'SELECT p.ID,CONCAT(DAY(c.CHEACKIN_DATE), "-",MONTH(c.CHEACKIN_DATE), "-", YEAR(c.CHEACKIN_DATE)+543) AS CHEACKIN_DATE
                    ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                    ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                    ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                    ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                FROM checkin_index c
                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"

                AND d.HR_DEPARTMENT_SUB_SUB_ID = "'.$debsubsub.'"
                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                ORDER BY c.CHEACKIN_DATE,d.HR_DEPARTMENT_SUB_SUB_ID,p.ID,ot.OPERATE_TYPE_ID
        ');
        // AND hs.HR_DEPARTMENT_SUB_ID = "'.$debsub.'"

        $department = DB::connection('mysql6')->select('SELECT * FROM hrd_department');
        $department_sub = DB::connection('mysql6')->select('SELECT * FROM hrd_department_sub');
        $department_subsub = DB::connection('mysql6')->select('SELECT * FROM hrd_department_sub_sub');


        return view('timer.time_index',$data, [
            'datashow_'        => $datashow_,
            'startdate'        => $startdate,
            'enddate'          => $enddate,
            'department'       => $department,
            'department_sub'   => $department_sub,
            'department_subsub'=> $department_subsub,
            'debid'            => $debid,
            // 'debsub'           => $debsub,
            'debsubsub'        => $debsubsub,
            'show_subsub'      => $show_subsub,
            'show_dep'         => $show_dep

        ]);
    }

    public function time_index_search(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $deb = $request->HR_DEPARTMENT_ID;
        $debsub = $request->HR_DEPARTMENT_SUB_ID;
        $debsubsub = $request->HR_DEPARTMENT_SUB_SUB_ID;

        // dd($debsub);
        $datashow_ = DB::connection('mysql6')->select('
            SELECT p.ID,c.CHEACKIN_DATE
            ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
            ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
            ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,hp.HR_POSITION_NAME,ct.CHECKIN_TYPE_NAME
            ,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_NAME,d.HR_DEPARTMENT_SUB_ID
            ,ot.OPERATE_TYPE_NAME,c.CHECKIN_TYPE_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_NAME,ot.OPERATE_TYPE_NAME
            FROM checkin_index c
            LEFT JOIN checkin_type ct on ct.CHECKIN_TYPE_ID=c.CHECKIN_TYPE_ID
            LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
            LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
            LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
            LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
            LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
            LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
            LEFT JOIN hrd_position hp on hp.HR_POSITION_ID=p.HR_POSITION_ID
            WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
            AND d.HR_DEPARTMENT_SUB_ID = "'.$debsub.'"
            AND d.HR_DEPARTMENT_SUB_SUB_ID = "'.$debsubsub.'"
            GROUP BY p.ID,j.OPERATE_JOB_ID,c.CHEACKIN_DATE
            ORDER BY c.CHEACKIN_DATE,c.CHECKIN_TYPE_ID
        ');

        Operate_time::truncate();
        foreach ($datashow_ as $key => $value) {

            // $add = new Operate_time();
            // $add->operate_time_date = $value->CHEACKIN_DATE;
            // $add->operate_time_personid = $value->ID;
            // $add->operate_time_person = $value->hrname;
            // $add->operate_time_typeid = $value->CHECKIN_TYPE_ID;
            // $add->operate_time_in = $value->CHEACKINTIME;
            // $add->operate_time_out = $value->CHEACKOUTTIME;
            // $add->save();
            $start = strtotime($value->CHEACKINTIME);
            $end = strtotime($value->CHEACKOUTTIME);

            if ($end == '') {
                $tot = '';
            }elseif ($start == '') {
                $tot = '';
            }elseif ($end < $start) {
                $tot = '';
            } else {
                $tot_ = ($end - $start) / 3600;
                $tot = number_format($tot_,2);
            }


            $date1 = date_create($value->CHEACKINTIME);
            $date2 = date_create($value->CHEACKOUTTIME);

            $diff = date_diff($date1, $date2);
            $totalhr = $diff->format('%R%H ชม.');

            Operate_time::insert([
                'operate_time_date'        => $value->CHEACKIN_DATE,
                'operate_time_personid'    => $value->ID,
                'operate_time_person'      => $value->hrname,
                'operate_time_typeid'      => $value->CHECKIN_TYPE_ID,
                'operate_time_typename'    => $value->CHECKIN_TYPE_NAME,
                'operate_time_in'          => $value->CHEACKINTIME,
                'operate_time_out'         => $value->CHEACKOUTTIME,
                'operate_time_otin'        => '',
                'operate_time_otout'       => '',
                'totaltime_narmal'         => $tot,
                'totaltime_ot'             => ''
            ]);
        }
        $department = DB::connection('mysql6')->select('
            SELECT * FROM hrd_department
        ');
        $department_sub = DB::connection('mysql6')->select('
            SELECT * FROM hrd_department_sub
        ');
        $department_subsub = DB::connection('mysql6')->select('
            SELECT * FROM hrd_department_sub_sub
        ');

        return view('timer.time_index', [
            'datashow_'        => $datashow_,
            'startdate'        => $startdate,
            'enddate'          => $enddate,
            'department'       => $department,
            'department_sub'   => $department_sub,
            'department_subsub'=> $department_subsub,
            'deb'              => $deb,
            'debsub'           => $debsub,
            'debsubsub'        => $debsubsub
        ]);
    }
    public function time_index_excel (Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;


        $deb = Auth::user()->dep_id;
        // $debsub = Auth::user()->dep_subid;
        // $debsubsub = Auth::user()->dep_subsubtrueid;
        $debsub_id = $request->HR_DEPARTMENT_SUB_ID;
        $debsubsub_id = $request->HR_DEPARTMENT_SUB_SUB_ID;
        $debname_ = DB::connection('mysql6')->table('hrd_department')->where('HR_DEPARTMENT_ID', '=', $deb)->first();
        $debsubname_ = DB::connection('mysql6')->table('hrd_department_sub')->where('HR_DEPARTMENT_SUB_ID', '=', $debsub_id)->first();
        $debsubsubname_ = DB::connection('mysql6')->table('hrd_department_sub_sub')->where('HR_DEPARTMENT_SUB_SUB_ID', '=', $debsubsub_id)->first();
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();

            $export = DB::connection('mysql6')->select('
                    SELECT p.ID
                        ,CONCAT(DAY(c.CHEACKIN_DATE), "-",MONTH(c.CHEACKIN_DATE), "-", YEAR(c.CHEACKIN_DATE)+543) AS CHEACKIN_DATE
                        ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                        ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                        ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname
                        ,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                        ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME
                        ,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                    FROM checkin_index c
                    LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                    LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                    LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                    LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                    LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                    LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                    LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                    WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"

                    AND hs.HR_DEPARTMENT_SUB_ID = "'.$debsub_id.'"
                    AND d.HR_DEPARTMENT_SUB_SUB_ID = "'.$debsubsub_id.'"
                    GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                    ORDER BY c.CHEACKIN_DATE,d.HR_DEPARTMENT_SUB_SUB_ID,p.ID,ot.OPERATE_TYPE_ID
            ');

            $department = DB::connection('mysql6')->select('
                SELECT * FROM hrd_department
            ');
            $department_sub = DB::connection('mysql6')->select('
                SELECT * FROM hrd_department_sub
            ');
            $department_subsub = DB::connection('mysql6')->select('
                SELECT * FROM hrd_department_sub_sub
            ');
            // $export = DB::connection('mysql')->select('
            //     SELECT * FROM operate_time
            // ');
        return view('timer.time_index_excel',[
            // 'datashow_'        => $datashow_,
            'startdate'        => $startdate,
            'enddate'          => $enddate,
            // 'department_sub'   => $department_sub,
            // 'debsub'           => $debsub,
            // 'debsubsub'        => $debsubsub,
            'export'           => $export,
            'debname'          => $debname_->HR_DEPARTMENT_NAME,
            'debsubname'       => $debsubname_->HR_DEPARTMENT_SUB_NAME,
            'debsubsubname'    => $debsubsubname_->HR_DEPARTMENT_SUB_SUB_NAME,
        ]);
    }
    public function time_dashboard(Request $request)
    {
        $department_ = DB::connection('mysql6')->select('
            SELECT * FROM hrd_department
        ');
        $dep_count_all_ = DB::connection('mysql6')->select('
            SELECT COUNT(DISTINCT p.ID) as COuntid
                FROM checkin_index c
                LEFT JOIN checkin_type ct on ct.CHECKIN_TYPE_ID=c.CHECKIN_TYPE_ID
                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID = p.HR_DEPARTMENT_ID
                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID

                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                LEFT JOIN hrd_position hp on hp.HR_POSITION_ID=p.HR_POSITION_ID
                WHERE c.CHEACKIN_DATE = CURDATE()
        ');
        foreach ($dep_count_all_ as $key => $value) {
        $dep_count_all = $value->COuntid;
        }

        $datashow_ = DB::connection('mysql6')->select('
            SELECT p.ID,c.CHEACKIN_DATE
                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,hp.HR_POSITION_NAME
                ,ot.OPERATE_TYPE_NAME,d.HR_DEPARTMENT_SUB_SUB_NAME
                FROM checkin_index c
                LEFT JOIN checkin_type ct on ct.CHECKIN_TYPE_ID=c.CHECKIN_TYPE_ID
                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID = p.HR_DEPARTMENT_ID
                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                LEFT JOIN hrd_position hp on hp.HR_POSITION_ID=p.HR_POSITION_ID
                WHERE c.CHEACKIN_DATE = CURDATE()
                GROUP BY c.CHECKIN_PERSON_ID
                ORDER BY CHEACKINTIME DESC
        ');

        $per_ = DB::connection('mysql6')->select('
            SELECT COUNT(DISTINCT ID) as cc
            FROM hrd_person WHERE HR_STATUS_ID ="1"
        ');
        foreach ($per_ as $key => $value3) {
            $per = $value3->cc;
        }
        // LEFT JOIN users u on u.PERSON_ID = p.ID
        // dd( $dep_count_all);
        return view('timer.time_dashboard', [
            'department'    =>  $department_,
            'dep_count_all' =>  $dep_count_all,
            'datashow_'     =>  $datashow_,
            'per'     =>  $per
        ]);
    }
    public function time_dashboard_excel(Request $request)
    {
        $deb = Auth::user()->dep_id;
        $debsub = Auth::user()->dep_subid;
        $debsubsub = Auth::user()->dep_subsubtrueid;
        $debname_ = DB::connection('mysql6')->table('hrd_department')->where('HR_DEPARTMENT_ID', '=', $deb)->first();
        $debsubname_ = DB::connection('mysql6')->table('hrd_department_sub')->where('HR_DEPARTMENT_SUB_ID', '=', $debsub)->first();
        $debsubsubname_ = DB::connection('mysql6')->table('hrd_department_sub_sub')->where('HR_DEPARTMENT_SUB_SUB_ID', '=', $debsubsub)->first();
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();

        $export = DB::connection('mysql6')->select('
            SELECT p.ID,c.CHEACKIN_DATE
                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,hp.HR_POSITION_NAME
                ,ot.OPERATE_TYPE_NAME,d.HR_DEPARTMENT_SUB_SUB_NAME
                FROM checkin_index c
                LEFT JOIN checkin_type ct on ct.CHECKIN_TYPE_ID=c.CHECKIN_TYPE_ID
                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID = p.HR_DEPARTMENT_ID
                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID = p.HR_DEPARTMENT_SUB_SUB_ID
                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                LEFT JOIN hrd_position hp on hp.HR_POSITION_ID=p.HR_POSITION_ID
                WHERE c.CHEACKIN_DATE = CURDATE()
                GROUP BY c.CHECKIN_PERSON_ID
                ORDER BY CHEACKINTIME DESC
        ');

        return view('timer.time_dashboard_excel', [
            'export'        =>  $export,
            'debname'          => $debname_->HR_DEPARTMENT_NAME,
            'debsubname'       => $debsubname_->HR_DEPARTMENT_SUB_NAME,
            'debsubsubname'    => $debsubsubname_->HR_DEPARTMENT_SUB_SUB_NAME,
            'org'              => $org_->orginfo_name,

        ]);
    }
    public function time_dashboard_detail(Request $request,$id)
    {
            $dep_show_all_ = DB::connection('mysql6')->select('
                SELECT h.HR_DEPARTMENT_ID,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME
                    FROM checkin_index c
                    LEFT JOIN checkin_type ct on ct.CHECKIN_TYPE_ID=c.CHECKIN_TYPE_ID
                    LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                    LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID = p.HR_DEPARTMENT_ID
                    LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                    LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID = p.HR_DEPARTMENT_SUB_ID
                    LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                    LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                    LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                    LEFT JOIN hrd_position hp on hp.HR_POSITION_ID=p.HR_POSITION_ID
                    WHERE c.CHEACKIN_DATE = CURDATE()
                    AND h.HR_DEPARTMENT_ID ="'.$id.'"
                    GROUP BY hs.HR_DEPARTMENT_SUB_ID
            ');

            $datashow_ = DB::connection('mysql6')->select('
                SELECT p.ID,c.CHEACKIN_DATE
                    ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                    ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                    ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,hp.HR_POSITION_NAME
                    ,ot.OPERATE_TYPE_NAME,d.HR_DEPARTMENT_SUB_SUB_NAME
                    FROM checkin_index c
                    LEFT JOIN checkin_type ct on ct.CHECKIN_TYPE_ID=c.CHECKIN_TYPE_ID
                    LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                    LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID = p.HR_DEPARTMENT_ID
                    LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                    LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                    LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                    LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                    LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                    LEFT JOIN hrd_position hp on hp.HR_POSITION_ID=p.HR_POSITION_ID
                    WHERE c.CHEACKIN_DATE = CURDATE()
                    AND h.HR_DEPARTMENT_ID ="'.$id.'"
                    GROUP BY c.CHECKIN_PERSON_ID
                    ORDER BY CHEACKINTIME DESC
            ');
            $dep_ = DB::connection('mysql6')->select('
                SELECT * FROM hrd_department WHERE HR_DEPARTMENT_ID = "'.$id.'"
            ');
            foreach ($dep_ as $key => $val) {
                $dep = $val->HR_DEPARTMENT_NAME;
            }
        return view('timer.time_dashboard_detail', [
            'dep_show_all'  =>  $dep_show_all_,
            'datashow_'     =>  $datashow_,
            'dep'           =>  $dep,
            'id'            =>  $id
        ]);
    }
    public function time_dashboard_detail_excel(Request $request,$id)
    {
        $deb = Auth::user()->dep_id;
        $debsub = Auth::user()->dep_subid;
        $debsubsub = Auth::user()->dep_subsubtrueid;
        $debname_ = DB::connection('mysql6')->table('hrd_department')->where('HR_DEPARTMENT_ID', '=', $deb)->first();
        $debsubname_ = DB::connection('mysql6')->table('hrd_department_sub')->where('HR_DEPARTMENT_SUB_ID', '=', $debsub)->first();
        $debsubsubname_ = DB::connection('mysql6')->table('hrd_department_sub_sub')->where('HR_DEPARTMENT_SUB_SUB_ID', '=', $debsubsub)->first();
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();

        $export = DB::connection('mysql6')->select('
            SELECT p.ID,c.CHEACKIN_DATE
                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,hp.HR_POSITION_NAME
                ,ot.OPERATE_TYPE_NAME,d.HR_DEPARTMENT_SUB_SUB_NAME
                FROM checkin_index c
                LEFT JOIN checkin_type ct on ct.CHECKIN_TYPE_ID=c.CHECKIN_TYPE_ID
                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID = p.HR_DEPARTMENT_ID
                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID = p.HR_DEPARTMENT_SUB_SUB_ID
                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                LEFT JOIN hrd_position hp on hp.HR_POSITION_ID=p.HR_POSITION_ID
                WHERE c.CHEACKIN_DATE = CURDATE()
                AND h.HR_DEPARTMENT_ID ="'.$id.'"
                GROUP BY c.CHECKIN_PERSON_ID
                ORDER BY CHEACKINTIME DESC
        ');
        $dep_ = DB::connection('mysql6')->select('
            SELECT * FROM hrd_department WHERE HR_DEPARTMENT_ID = "'.$id.'"
        ');
        foreach ($dep_ as $key => $val) {
            $depname = $val->HR_DEPARTMENT_NAME;
        }

        return view('timer.time_dashboard_detail_excel', [
            'export'        =>  $export,
            'debname'          => $debname_->HR_DEPARTMENT_NAME,
            'debsubname'       => $debsubname_->HR_DEPARTMENT_SUB_NAME,
            'debsubsubname'    => $debsubsubname_->HR_DEPARTMENT_SUB_SUB_NAME,
            'org'              => $org_->orginfo_name,
            'depname'        =>  $depname,
        ]);
    }
    public function time_dashboard_detail_sub(Request $request,$id)
    {
            $depsub_show_all_ = DB::connection('mysql6')->select('
                SELECT h.HR_DEPARTMENT_ID,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID
                    FROM checkin_index c
                    LEFT JOIN checkin_type ct on ct.CHECKIN_TYPE_ID=c.CHECKIN_TYPE_ID
                    LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                    LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID = p.HR_DEPARTMENT_ID
                    LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                    LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID = p.HR_DEPARTMENT_SUB_ID
                    LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                    LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                    LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                    LEFT JOIN hrd_position hp on hp.HR_POSITION_ID=p.HR_POSITION_ID
                    WHERE c.CHEACKIN_DATE = CURDATE()
                    AND  hs.HR_DEPARTMENT_SUB_ID ="'.$id.'"
                    GROUP BY d.HR_DEPARTMENT_SUB_SUB_ID
            ');

            $datashow_ = DB::connection('mysql6')->select('
                SELECT p.ID,c.CHEACKIN_DATE
                    ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                    ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                    ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,hp.HR_POSITION_NAME
                    ,ot.OPERATE_TYPE_NAME
                    FROM checkin_index c
                    LEFT JOIN checkin_type ct on ct.CHECKIN_TYPE_ID=c.CHECKIN_TYPE_ID
                    LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                    LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID = p.HR_DEPARTMENT_ID
                    LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                    LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                    LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                    LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                    LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                    LEFT JOIN hrd_position hp on hp.HR_POSITION_ID=p.HR_POSITION_ID
                    WHERE c.CHEACKIN_DATE = CURDATE()
                    AND hs.HR_DEPARTMENT_SUB_ID ="'.$id.'"
                    GROUP BY c.CHECKIN_PERSON_ID
                    ORDER BY CHEACKINTIME DESC
            ');
            $depsub_ = DB::connection('mysql6')->select('
                SELECT * FROM hrd_department_sub WHERE HR_DEPARTMENT_SUB_ID = "'.$id.'"
            ');
            foreach ($depsub_ as $key => $val) {
                $depsub = $val->HR_DEPARTMENT_SUB_NAME;
            }

        return view('timer.time_dashboard_detail_sub', [
            'depsub_show_all'  =>  $depsub_show_all_,
            'datashow_'        =>  $datashow_,
            'depsub'           =>  $depsub,
            'id'               =>  $id
        ]);
    }
    public function time_dashboard_detail_subexcel(Request $request,$id)
    {
        $deb = Auth::user()->dep_id;
        $debsub = Auth::user()->dep_subid;
        $debsubsub = Auth::user()->dep_subsubtrueid;
        $debname_ = DB::connection('mysql6')->table('hrd_department')->where('HR_DEPARTMENT_ID', '=', $deb)->first();
        $debsubname_ = DB::connection('mysql6')->table('hrd_department_sub')->where('HR_DEPARTMENT_SUB_ID', '=', $debsub)->first();
        $debsubsubname_ = DB::connection('mysql6')->table('hrd_department_sub_sub')->where('HR_DEPARTMENT_SUB_SUB_ID', '=', $debsubsub)->first();
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();

        $export = DB::connection('mysql6')->select('
            SELECT p.ID,c.CHEACKIN_DATE
                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,hp.HR_POSITION_NAME
                ,ot.OPERATE_TYPE_NAME,d.HR_DEPARTMENT_SUB_SUB_NAME
                FROM checkin_index c
                LEFT JOIN checkin_type ct on ct.CHECKIN_TYPE_ID=c.CHECKIN_TYPE_ID
                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID = p.HR_DEPARTMENT_ID
                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID = p.HR_DEPARTMENT_SUB_SUB_ID
                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                LEFT JOIN hrd_position hp on hp.HR_POSITION_ID=p.HR_POSITION_ID
                WHERE c.CHEACKIN_DATE = CURDATE()
                AND hs.HR_DEPARTMENT_SUB_ID ="'.$id.'"
                GROUP BY c.CHECKIN_PERSON_ID
                ORDER BY CHEACKINTIME DESC

        ');
        $depsub_ = DB::connection('mysql6')->select('
                SELECT * FROM hrd_department_sub WHERE HR_DEPARTMENT_SUB_ID = "'.$id.'"
        ');
        foreach ($depsub_ as $key => $val) {
            $depsubname = $val->HR_DEPARTMENT_SUB_NAME;
        }

        return view('timer.time_dashboard_detail_subexcel', [
            'export'        =>  $export,
            'debname'          => $debname_->HR_DEPARTMENT_NAME,
            'debsubname'       => $debsubname_->HR_DEPARTMENT_SUB_NAME,
            'debsubsubname'    => $debsubsubname_->HR_DEPARTMENT_SUB_SUB_NAME,
            'org'              => $org_->orginfo_name,
            'depsubname'       =>  $depsubname,
        ]);
    }
    public function time_dashboard_detail_sub_person(Request $request,$id)
    {
            $depsub_person_show_all_ = DB::connection('mysql6')->select('
                SELECT p.ID,c.CHEACKIN_DATE
                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,hp.HR_POSITION_NAME,ct.CHECKIN_TYPE_NAME
                ,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_NAME,d.HR_DEPARTMENT_SUB_ID
                ,ot.OPERATE_TYPE_NAME

                    FROM checkin_index c
                    LEFT JOIN checkin_type ct on ct.CHECKIN_TYPE_ID=c.CHECKIN_TYPE_ID
                    LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                    LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID = p.HR_DEPARTMENT_ID
                    LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                    LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID = p.HR_DEPARTMENT_SUB_ID
                    LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                    LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                    LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                    LEFT JOIN hrd_position hp on hp.HR_POSITION_ID=p.HR_POSITION_ID


                    WHERE c.CHEACKIN_DATE = CURDATE()
                    AND  d.HR_DEPARTMENT_SUB_SUB_ID ="'.$id.'"
                    GROUP BY p.ID,j.OPERATE_JOB_ID,c.CHEACKIN_DATE
                    ORDER BY c.CHEACKIN_DATE,c.CHECKIN_TYPE_ID
            ');
            // h.HR_DEPARTMENT_ID,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID

        return view('timer.time_dashboard_detail_sub_person', [
            'depsub_person_show_all' =>  $depsub_person_show_all_
        ]);
    }
    public function time_dep(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $deb = $request->department_id;
        $debsub = $request->department_sub_id;
        $debsubsub = $request->department_subsub_id;
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();

        if ($deb != '') {
            $datashow_ = DB::connection('mysql6')->select(
                'SELECT p.ID,c.CHEACKIN_DATE,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                    ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                    ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                    ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                FROM checkin_index c
                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                AND hs.HR_DEPARTMENT_ID = "'.$deb.'"
                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                ORDER BY c.CHEACKIN_DATE,d.HR_DEPARTMENT_SUB_SUB_ID,p.ID,ot.OPERATE_TYPE_ID
            ');
            // ,CONCAT(DAY(c.CHEACKIN_DATE), "-",MONTH(c.CHEACKIN_DATE), "-", YEAR(c.CHEACKIN_DATE)+543) AS CHEACKIN_DATE
        } else if($debsub != ''){
            $datashow_ = DB::connection('mysql6')->select('
                    SELECT p.ID ,c.CHEACKIN_DATE
                        ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                        ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                        ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname
                        ,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                        ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME
                        ,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                    FROM checkin_index c
                    LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                    LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                    LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                    LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                    LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                    LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                    LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                    WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                    AND hs.HR_DEPARTMENT_SUB_ID = "'.$debsub.'"
                    GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                    ORDER BY c.CHEACKIN_DATE,d.HR_DEPARTMENT_SUB_SUB_ID,p.ID,ot.OPERATE_TYPE_ID
            ');
        }else{
            $datashow_ = DB::connection('mysql6')->select('
                    SELECT p.ID ,c.CHEACKIN_DATE
                        ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                        ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                        ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname
                        ,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                        ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME
                        ,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                    FROM checkin_index c
                    LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                    LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                    LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                    LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                    LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                    LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                    LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                    WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                    AND d.HR_DEPARTMENT_SUB_SUB_ID = "'.$debsubsub.'"
                    GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                    ORDER BY c.CHEACKIN_DATE,d.HR_DEPARTMENT_SUB_SUB_ID,p.ID,ot.OPERATE_TYPE_ID
            ');
        }




        $department = DB::connection('mysql6')->select('
            SELECT * FROM hrd_department
        ');
        $department_sub = DB::connection('mysql6')->select('
            SELECT * FROM hrd_department_sub
        ');
        $department_subsub = DB::connection('mysql6')->select('
            SELECT * FROM hrd_department_sub_sub
        ');

        return view('timer.time_index', [
            'datashow_'        => $datashow_,
            'startdate'        => $startdate,
            'enddate'          => $enddate,
            'department'       => $department,
            'department_sub'   => $department_sub,
            'department_subsub'=> $department_subsub,
            'deb'              => $deb,
            'debsub'           => $debsub,
            'debsubsub'        => $debsubsub
        ]);
    }
    public function time_dep_excel(Request $request,$deb,$startdate,$enddate)
    {
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();
        $org = $org_->orginfo_name;
        $export = DB::connection('mysql6')->select('
                SELECT p.ID,c.CHEACKIN_DATE
                    ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                    ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                    ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname
                    ,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                    ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME
                    ,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                FROM checkin_index c
                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                AND hs.HR_DEPARTMENT_ID = "'.$deb.'"
                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                ORDER BY c.CHEACKIN_DATE,d.HR_DEPARTMENT_SUB_SUB_ID,p.ID,ot.OPERATE_TYPE_ID
        ');
        $debname_ = DB::connection('mysql6')->table('hrd_department')->where('HR_DEPARTMENT_ID', '=', $deb)->first();
        // $debsubname_ = DB::connection('mysql6')->table('hrd_department_sub')->where('HR_DEPARTMENT_SUB_ID', '=', $debsub)->first();
        // $debsubsubname_ = DB::connection('mysql6')->table('hrd_department_sub_sub')->where('HR_DEPARTMENT_SUB_SUB_ID', '=', $debsubsub)->first();
        // $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();

        $department = DB::connection('mysql6')->select('
            SELECT * FROM hrd_department
        ');

        return view('timer.time_dep_excel', [
            'export'           =>  $export,
            'deb'              => $debname_->HR_DEPARTMENT_NAME,
            'org'              => $org,
            'startdate'        => $startdate,
            'enddate'          => $enddate,
        ]);
    }

    public function time_depsub(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $deb = $request->department_id;
        $debsub = $request->department_sub_id;
        $debsubsub = $request->department_subsub_id;
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();

        $datashow_ = DB::connection('mysql6')->select('
                SELECT p.ID,c.CHEACKIN_DATE
                    ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                    ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                    ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname
                    ,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                    ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME
                    ,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                FROM checkin_index c
                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                AND hs.HR_DEPARTMENT_SUB_ID = "'.$debsub.'"
                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                ORDER BY c.CHEACKIN_DATE,d.HR_DEPARTMENT_SUB_SUB_ID,p.ID,ot.OPERATE_TYPE_ID
        ');
        $department_sub = DB::connection('mysql6')->select('
            SELECT * FROM hrd_department_sub
        ');

        return view('timer.time_depsub', [
            'datashow_'        => $datashow_,
            'startdate'        => $startdate,
            'enddate'          => $enddate,

            'department_sub'   => $department_sub,

            'deb'              => $deb,
            'debsub'           => $debsub,
            'debsubsub'        => $debsubsub
        ]);
    }

    public function time_depsub_excel(Request $request,$deb,$startdate,$enddate)
    {
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();
        $org = $org_->orginfo_name;
        $export = DB::connection('mysql6')->select('
                SELECT p.ID,c.CHEACKIN_DATE
                    ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                    ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                    ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname
                    ,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                    ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME
                    ,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                FROM checkin_index c
                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                AND hs.HR_DEPARTMENT_SUB_ID = "'.$deb.'"
                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                ORDER BY c.CHEACKIN_DATE,d.HR_DEPARTMENT_SUB_SUB_ID,p.ID,ot.OPERATE_TYPE_ID
        ');

        $debsubname_ = DB::connection('mysql6')->table('hrd_department_sub')->where('HR_DEPARTMENT_SUB_ID', '=', $deb)->first();

        $department = DB::connection('mysql6')->select('
            SELECT * FROM hrd_department
        ');

        return view('timer.time_depsub_excel', [
            'export'           =>  $export,
            'deb'              => $debsubname_->HR_DEPARTMENT_SUB_NAME,
            'org'              => $org,
            'startdate'        => $startdate,
            'enddate'          => $enddate,
        ]);
    }

    public function time_depsubsub(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $deb = $request->department_id;
        $debsub = $request->department_sub_id;
        $debsubsub = $request->department_subsub_id;
        // dd($debsubsub);
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();

        $datashow_ = DB::connection('mysql6')->select('
                SELECT p.ID,c.CHEACKIN_DATE
                    ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                    ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                    ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname
                    ,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                    ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME
                    ,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                FROM checkin_index c
                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                AND d.HR_DEPARTMENT_SUB_SUB_ID = "'.$debsubsub.'"
                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                ORDER BY c.CHEACKIN_DATE,d.HR_DEPARTMENT_SUB_SUB_ID,p.ID,ot.OPERATE_TYPE_ID
        ');
        $department_subsub = DB::connection('mysql6')->select('
            SELECT * FROM hrd_department_sub_sub
        ');

        return view('timer.time_depsubsub', [
            'datashow_'        => $datashow_,
            'startdate'        => $startdate,
            'enddate'          => $enddate,
            'department_subsub'   => $department_subsub,
            'deb'              => $deb,
            'debsub'           => $debsub,
            'debsubsub'        => $debsubsub
        ]);
    }


    public function time_depsubsub_excel_new(Request $request)
    {
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();
        $org = $org_->orginfo_name;


        $iduser              = Auth::user()->id;
        $deb                 = Auth::user()->dep_subsubtrueid;
        $d_count           = Operate_time_dsst::where('user_id', '=', $iduser)->where('depsubsubid', '!=', NULL)->count();

        if ($d_count > 0) {
            $debsubsubname_new  = Operate_time_dsst::where('user_id', '=', $iduser)->first();
            $dapid              = $debsubsubname_new->depsubsubid;

            $debsubsubname_last = DB::connection('mysql')->table('department_sub_sub')->where('DEPARTMENT_SUB_SUB_ID', '=', $dapid)->first();
            $debsubsub_name     = $debsubsubname_last->DEPARTMENT_SUB_SUB_NAME;
        } else {
            $debsubsub_name     = '';
        }
        $export = DB::connection('mysql')->select('SELECT * FROM operate_time_dss WHERE user_id = "'.$iduser .'"');
        return view('timer.time_depsubsub_excel_new', [
            'export'           => $export,
            // 'deb'              => $dap,
            'org'              => $org,
            'debsubsub_name'   => $debsubsub_name,
        ]);
    }
    public function time_dep_excel_new(Request $request)
    {
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();
        $org = $org_->orginfo_name;


        $iduser              = Auth::user()->id;
        $deb                 = Auth::user()->dep_id;
        $d_count             = Operate_time_dept::where('user_id', '=', $iduser)->where('depid', '!=', NULL)->count();

        if ($d_count > 0) {
            $debsubsubname_new  = Operate_time_dept::where('user_id', '=', $iduser)->first();
            $dapid              = $debsubsubname_new->depid;

            $debsubsubname_last = DB::connection('mysql')->table('department')->where('DEPARTMENT_ID', '=', $dapid)->first();
            $deb_name           = $debsubsubname_last->DEPARTMENT_NAME;
        } else {
            $deb_name     = '';
        }
        $export = DB::connection('mysql')->select('SELECT * FROM operate_time_dep WHERE user_id = "'.$iduser .'"');
        return view('timer.time_dep_excel_new', [
            'export'           => $export,
            // 'deb'              => $dap,
            'org'              => $org,
            'deb_name'         => $deb_name,
        ]);
    }



    public function time_backot_dep(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        // $deb = $request->HR_DEPARTMENT_ID;
        $deb = $request->department_id;
        $debsub = $request->department_sub_id;
        $debsubsub = $request->department_subsub_id;
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();

        if ($startdate == '') {
                $datashow_ = DB::connection('mysql6')->select('
                    SELECT p.ID,c.CHEACKIN_DATE
                            ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                            ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                            ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname
                            ,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                            ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME
                            ,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                            FROM checkin_index c
                            LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                            LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                            LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                            LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                            LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                            LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                            LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                            WHERE c.CHEACKIN_DATE = CURDATE()
                            AND ot.OPERATE_TYPE_ID NOT IN ("1","2","5","7")
                            AND h.HR_DEPARTMENT_ID = "'.$deb.'"
                            AND ot.OPERATE_TYPE_NAME REGEXP "นอกเวลา|วันหยุด"
                            GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                            ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID
                ');
        } else {
                $datashow_ = DB::connection('mysql6')->select('
                        SELECT p.ID,c.CHEACKIN_DATE
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname
                                ,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                                ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME
                                ,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                                FROM checkin_index c
                                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                                WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                                AND ot.OPERATE_TYPE_ID NOT IN ("1","2","5","7")
                                AND h.HR_DEPARTMENT_ID = "'.$deb.'"
                                AND ot.OPERATE_TYPE_NAME REGEXP "นอกเวลา|วันหยุด"
                                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                                ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID
                ');
        }

        $department = DB::connection('mysql6')->select('
            SELECT * FROM hrd_department
        ');

        return view('timer.time_backot_dep', [
            'datashow_'        => $datashow_,
            'startdate'        => $startdate,
            'enddate'          => $enddate,
            'deb'              => $deb,
            'department'       => $department,
        ]);
    }
    public function time_backot_depexcel(Request $request,$deb,$startdate,$enddate)
    {
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();
        $org = $org_->orginfo_name;

        if ($startdate == '') {
                $export = DB::connection('mysql6')->select('
                    SELECT p.ID,c.CHEACKIN_DATE
                            ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                            ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                            ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname
                            ,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                            ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME
                            ,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                            FROM checkin_index c
                            LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                            LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                            LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                            LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                            LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                            LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                            LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                            WHERE c.CHEACKIN_DATE = CURDATE()
                            AND ot.OPERATE_TYPE_ID NOT IN ("1","2","5","7")
                            AND h.HR_DEPARTMENT_ID = "'.$deb.'"
                            AND ot.OPERATE_TYPE_NAME REGEXP "นอกเวลา|วันหยุด"
                            GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                            ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID
                ');
        } else {
                $export = DB::connection('mysql6')->select('
                        SELECT p.ID,c.CHEACKIN_DATE
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname
                                ,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                                ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME
                                ,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                                FROM checkin_index c
                                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                                WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                                AND ot.OPERATE_TYPE_ID NOT IN ("1","2","5","7")
                                AND h.HR_DEPARTMENT_ID = "'.$deb.'"
                                AND ot.OPERATE_TYPE_NAME REGEXP "นอกเวลา|วันหยุด"
                                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                                ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID
                ');
        }

        $debname_ = DB::connection('mysql6')->table('hrd_department')->where('HR_DEPARTMENT_ID', '=', $deb)->first();

        return view('timer.time_backot_depexcel', [
            'export'           =>  $export,
            'deb'              => $debname_->HR_DEPARTMENT_NAME,
            'org'              => $org,
            'startdate'        => $startdate,
            'enddate'          => $enddate,
        ]);
    }

    public function time_backot_depsub(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        // $debsub = $request->HR_DEPARTMENT_SUB_ID;
        $deb = $request->department_id;
        $debsub = $request->department_sub_id;
        $debsubsub = $request->department_subsub_id;
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();

        if ($startdate == '') {
                $datashow_ = DB::connection('mysql6')->select('
                    SELECT p.ID,c.CHEACKIN_DATE
                            ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                            ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                            ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname
                            ,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                            ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME
                            ,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                            FROM checkin_index c
                            LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                            LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                            LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                            LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                            LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                            LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                            LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                            WHERE c.CHEACKIN_DATE = CURDATE()
                            AND ot.OPERATE_TYPE_ID NOT IN ("1","2","5","7")
                            AND hs.HR_DEPARTMENT_SUB_ID = "'.$debsub.'"
                            AND ot.OPERATE_TYPE_NAME REGEXP "นอกเวลา|วันหยุด"
                            GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                            ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID
                ');
        } else {
                $datashow_ = DB::connection('mysql6')->select('
                        SELECT p.ID,c.CHEACKIN_DATE
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname
                                ,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                                ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME
                                ,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                                FROM checkin_index c
                                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                                WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                                AND ot.OPERATE_TYPE_ID NOT IN ("1","2","5","7")
                                AND hs.HR_DEPARTMENT_SUB_ID = "'.$debsub.'"
                                AND ot.OPERATE_TYPE_NAME REGEXP "นอกเวลา|วันหยุด"
                                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                                ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID
                ');
        }
        $department_sub = DB::connection('mysql6')->select('
            SELECT * FROM hrd_department_sub
        ');

        return view('timer.time_backot_depsub', [
            'datashow_'        => $datashow_,
            'startdate'        => $startdate,
            'enddate'          => $enddate,
            'debsub'           => $debsub,
            'department_sub'   => $department_sub,
        ]);
    }
    public function time_backot_depsubexcel(Request $request,$debsub,$startdate,$enddate)
    {
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();
        $org = $org_->orginfo_name;

        if ($startdate == '') {
                $export = DB::connection('mysql6')->select('
                    SELECT p.ID,c.CHEACKIN_DATE
                            ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                            ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                            ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname
                            ,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                            ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME
                            ,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                            FROM checkin_index c
                            LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                            LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                            LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                            LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                            LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                            LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                            LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                            WHERE c.CHEACKIN_DATE = CURDATE()
                            AND ot.OPERATE_TYPE_ID NOT IN ("1","2","5","7")
                            AND hs.HR_DEPARTMENT_SUB_ID = "'.$debsub.'"
                            AND ot.OPERATE_TYPE_NAME REGEXP "นอกเวลา|วันหยุด"
                            GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                            ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID
                ');
        } else {
                $export = DB::connection('mysql6')->select('
                        SELECT p.ID,c.CHEACKIN_DATE
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname
                                ,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                                ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME
                                ,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                                FROM checkin_index c
                                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                                WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                                AND ot.OPERATE_TYPE_ID NOT IN ("1","2","5","7")
                                AND hs.HR_DEPARTMENT_SUB_ID = "'.$debsub.'"
                                AND ot.OPERATE_TYPE_NAME REGEXP "นอกเวลา|วันหยุด"
                                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                                ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID
                ');
        }

        $debsubname_ = DB::connection('mysql6')->table('hrd_department_sub')->where('HR_DEPARTMENT_SUB_ID', '=', $debsub)->first();

        return view('timer.time_backot_depsubexcel', [
            'export'           =>  $export,
            'deb'              => $debsubname_->HR_DEPARTMENT_SUB_NAME,
            'org'              => $org,
            'startdate'        => $startdate,
            'enddate'          => $enddate,
        ]);
    }

    public function time_backot_depsubsub(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        // $debsubsub = $request->HR_DEPARTMENT_SUB_SUB_ID;
        $deb = $request->department_id;
        $debsub = $request->department_sub_id;
        $debsubsub = $request->department_subsub_id;
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();

        if ($startdate == '') {
                $datashow_ = DB::connection('mysql6')->select('
                    SELECT p.ID,c.CHEACKIN_DATE
                            ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                            ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                            ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname
                            ,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                            ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME
                            ,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                            FROM checkin_index c
                            LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                            LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                            LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                            LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                            LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                            LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                            LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                            WHERE c.CHEACKIN_DATE = CURDATE()
                            AND ot.OPERATE_TYPE_ID NOT IN ("1","2","5","7","17","18","19")
                            AND d.HR_DEPARTMENT_SUB_SUB_ID = "'.$debsubsub.'"
                            AND ot.OPERATE_TYPE_NAME REGEXP "นอกเวลา|วันหยุด"
                            GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                            ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID
                ');
        } else {
                $datashow_ = DB::connection('mysql6')->select('
                        SELECT p.ID,c.CHEACKIN_DATE
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname
                                ,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                                ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME
                                ,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                                FROM checkin_index c
                                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                                WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                                AND ot.OPERATE_TYPE_ID NOT IN ("1","2","5","7","17","18","19")
                                AND d.HR_DEPARTMENT_SUB_SUB_ID = "'.$debsubsub.'"
                                AND ot.OPERATE_TYPE_NAME REGEXP "นอกเวลา|วันหยุด"
                                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                                ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID
                ');
        }
        // AND d.HR_DEPARTMENT_SUB_SUB_ID = "29"

        $department_subsub = DB::connection('mysql6')->select('
            SELECT * FROM hrd_department_sub_sub
        ');

        return view('timer.time_backot_depsubsub', [
            'datashow_'        => $datashow_,
            'startdate'        => $startdate,
            'enddate'          => $enddate,
            'debsubsub'        => $debsubsub,
            'department_subsub'=> $department_subsub,
        ]);
    }
    public function time_backot_depsubsubexcel(Request $request,$debsubsub,$startdate,$enddate)
    {
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();
        $org = $org_->orginfo_name;

        if ($startdate == '') {
                $export = DB::connection('mysql6')->select('
                    SELECT p.ID,c.CHEACKIN_DATE
                            ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                            ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                            ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname
                            ,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                            ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME
                            ,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                            FROM checkin_index c
                            LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                            LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                            LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                            LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                            LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                            LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                            LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                            WHERE c.CHEACKIN_DATE = CURDATE()
                            AND ot.OPERATE_TYPE_ID NOT IN ("1","2","5","7")
                            AND d.HR_DEPARTMENT_SUB_SUB_ID = "'.$debsubsub.'"
                            AND ot.OPERATE_TYPE_NAME REGEXP "นอกเวลา|วันหยุด"
                            GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                            ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID
                ');
        } else {
                $export = DB::connection('mysql6')->select('
                        SELECT p.ID,c.CHEACKIN_DATE
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname
                                ,h.HR_DEPARTMENT_ID,h.HR_DEPARTMENT_NAME
                                ,hs.HR_DEPARTMENT_SUB_ID,hs.HR_DEPARTMENT_SUB_NAME,d.HR_DEPARTMENT_SUB_SUB_ID,d.HR_DEPARTMENT_SUB_SUB_NAME
                                ,ot.OPERATE_TYPE_NAME,ot.OPERATE_TYPE_ID
                                FROM checkin_index c
                                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                                WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                                AND ot.OPERATE_TYPE_ID NOT IN ("1","2","5","7")
                                AND d.HR_DEPARTMENT_SUB_SUB_ID = "'.$debsubsub.'"
                                AND ot.OPERATE_TYPE_NAME REGEXP "นอกเวลา|วันหยุด"
                                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                                ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID
                ');
        }

        $debsubsubname_ = DB::connection('mysql6')->table('hrd_department_sub_sub')->where('HR_DEPARTMENT_SUB_SUB_ID', '=', $debsubsub)->first();
        $department = DB::connection('mysql6')->select('
            SELECT * FROM hrd_department_sub_sub
        ');

        return view('timer.time_backot_depsubsubexcel', [
            'export'           =>  $export,
            'deb'              => $debsubsubname_->HR_DEPARTMENT_SUB_SUB_NAME,
            'org'              => $org,
            'startdate'        => $startdate,
            'enddate'          => $enddate,
        ]);
    }

    public function time_nurs_dep(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $deb = $request->HR_DEPARTMENT_ID;
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();

        if ($startdate == '') {
                $datashow_ = DB::connection('mysql6')->select('
                            SELECT p.ID,c.CHEACKIN_DATE
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,d.HR_DEPARTMENT_SUB_SUB_NAME,hs.HR_DEPARTMENT_SUB_NAME
                                ,h.HR_DEPARTMENT_ID ,h.HR_DEPARTMENT_NAME,hs.HR_DEPARTMENT_SUB_ID ,d.HR_DEPARTMENT_SUB_SUB_ID ,ot.OPERATE_TYPE_ID,ot.OPERATE_TYPE_NAME
                                FROM checkin_index c
                                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                                WHERE c.CHEACKIN_DATE = CURDATE()
                                AND h.HR_DEPARTMENT_ID = "'.$deb.'"
                                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                                ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID
                ');
        } else {
                $datashow_ = DB::connection('mysql6')->select('
                            SELECT p.ID,c.CHEACKIN_DATE
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,d.HR_DEPARTMENT_SUB_SUB_NAME,hs.HR_DEPARTMENT_SUB_NAME
                                ,h.HR_DEPARTMENT_ID ,h.HR_DEPARTMENT_NAME,hs.HR_DEPARTMENT_SUB_ID ,d.HR_DEPARTMENT_SUB_SUB_ID ,ot.OPERATE_TYPE_ID,ot.OPERATE_TYPE_NAME
                                FROM checkin_index c
                                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                                WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                                AND h.HR_DEPARTMENT_ID = "'.$deb.'"
                                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                                ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID

                ');
        }

        $department = DB::connection('mysql6')->select('
            SELECT * FROM hrd_department
        ');

        return view('timer.time_nurs_dep', [
            'datashow_'        => $datashow_,
            'startdate'        => $startdate,
            'enddate'          => $enddate,
            'deb'              => $deb,
            'department'       => $department,
        ]);
    }
    public function time_nurs_depexcel(Request $request,$deb,$startdate,$enddate)
    {
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();
        $org = $org_->orginfo_name;

        if ($startdate == '') {
                $export = DB::connection('mysql6')->select('
                            SELECT p.ID,c.CHEACKIN_DATE
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,d.HR_DEPARTMENT_SUB_SUB_NAME,hs.HR_DEPARTMENT_SUB_NAME
                                ,h.HR_DEPARTMENT_ID ,h.HR_DEPARTMENT_NAME,hs.HR_DEPARTMENT_SUB_ID ,d.HR_DEPARTMENT_SUB_SUB_ID ,ot.OPERATE_TYPE_ID,ot.OPERATE_TYPE_NAME
                                FROM checkin_index c
                                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                                WHERE c.CHEACKIN_DATE = CURDATE()
                                AND h.HR_DEPARTMENT_ID = "'.$deb.'"
                                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                                ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID
                ');
        } else {
                $export = DB::connection('mysql6')->select('
                        SELECT p.ID,c.CHEACKIN_DATE
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,d.HR_DEPARTMENT_SUB_SUB_NAME,hs.HR_DEPARTMENT_SUB_NAME
                                ,h.HR_DEPARTMENT_ID ,h.HR_DEPARTMENT_NAME,hs.HR_DEPARTMENT_SUB_ID ,d.HR_DEPARTMENT_SUB_SUB_ID ,ot.OPERATE_TYPE_ID,ot.OPERATE_TYPE_NAME
                                FROM checkin_index c
                                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                                WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                                AND h.HR_DEPARTMENT_ID = "'.$deb.'"
                                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                                ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID

                ');
        }

        $debname_ = DB::connection('mysql6')->table('hrd_department')->where('HR_DEPARTMENT_ID', '=', $deb)->first();

        return view('timer.time_nurs_depexcel', [
            'export'           =>  $export,
            'deb'              => $debname_->HR_DEPARTMENT_NAME,
            'org'              => $org,
            'startdate'        => $startdate,
            'enddate'          => $enddate,
        ]);
    }

    public function time_nurs_depsub(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $debsub = $request->HR_DEPARTMENT_SUB_ID;
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();

        if ($startdate == '') {
                $datashow_ = DB::connection('mysql6')->select('
                            SELECT p.ID,c.CHEACKIN_DATE
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,d.HR_DEPARTMENT_SUB_SUB_NAME,hs.HR_DEPARTMENT_SUB_NAME
                                ,h.HR_DEPARTMENT_ID ,h.HR_DEPARTMENT_NAME,hs.HR_DEPARTMENT_SUB_ID ,d.HR_DEPARTMENT_SUB_SUB_ID ,ot.OPERATE_TYPE_ID,ot.OPERATE_TYPE_NAME
                                FROM checkin_index c
                                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                                WHERE c.CHEACKIN_DATE = CURDATE()
                                AND hs.HR_DEPARTMENT_SUB_ID = "'.$debsub.'"
                                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                                ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID DESC
                ');
        } else {
                $datashow_ = DB::connection('mysql6')->select('
                            SELECT p.ID,c.CHEACKIN_DATE
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,d.HR_DEPARTMENT_SUB_SUB_NAME,hs.HR_DEPARTMENT_SUB_NAME
                                ,h.HR_DEPARTMENT_ID ,h.HR_DEPARTMENT_NAME,hs.HR_DEPARTMENT_SUB_ID ,d.HR_DEPARTMENT_SUB_SUB_ID ,ot.OPERATE_TYPE_ID,ot.OPERATE_TYPE_NAME
                                FROM checkin_index c
                                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                                WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                                AND hs.HR_DEPARTMENT_SUB_ID = "'.$debsub.'"
                                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                                ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID DESC

                ');
        }

        $department_sub = DB::connection('mysql6')->select('
            SELECT * FROM hrd_department_sub
        ');

        return view('timer.time_nurs_depsub', [
            'datashow_'        => $datashow_,
            'startdate'        => $startdate,
            'enddate'          => $enddate,
            'debsub'           => $debsub,
            'department_sub'   => $department_sub,
        ]);
    }
    public function time_nurs_depsubexcel(Request $request,$debsup,$startdate,$enddate)
    {
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();
        $org = $org_->orginfo_name;

        if ($startdate == '') {
                $export = DB::connection('mysql6')->select('
                            SELECT p.ID,c.CHEACKIN_DATE
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,d.HR_DEPARTMENT_SUB_SUB_NAME,hs.HR_DEPARTMENT_SUB_NAME
                                ,h.HR_DEPARTMENT_ID ,h.HR_DEPARTMENT_NAME,hs.HR_DEPARTMENT_SUB_ID ,d.HR_DEPARTMENT_SUB_SUB_ID ,ot.OPERATE_TYPE_ID,ot.OPERATE_TYPE_NAME
                                FROM checkin_index c
                                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                                WHERE c.CHEACKIN_DATE = CURDATE()
                                AND hs.HR_DEPARTMENT_SUB_ID = "'.$debsup.'"
                                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                                ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID DESC
                ');
        } else {
                $export = DB::connection('mysql6')->select('
                        SELECT p.ID,c.CHEACKIN_DATE
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,d.HR_DEPARTMENT_SUB_SUB_NAME,hs.HR_DEPARTMENT_SUB_NAME
                                ,h.HR_DEPARTMENT_ID ,h.HR_DEPARTMENT_NAME,hs.HR_DEPARTMENT_SUB_ID ,d.HR_DEPARTMENT_SUB_SUB_ID ,ot.OPERATE_TYPE_ID,ot.OPERATE_TYPE_NAME
                                FROM checkin_index c
                                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                                WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                                AND hs.HR_DEPARTMENT_SUB_ID = "'.$debsup.'"
                                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                                ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID DESC

                ');
        }

        $debname_ = DB::connection('mysql6')->table('hrd_department_sub')->where('HR_DEPARTMENT_SUB_ID', '=', $debsup)->first();

        return view('timer.time_nurs_depsubexcel', [
            'export'           =>  $export,
            'deb'              => $debname_->HR_DEPARTMENT_SUB_NAME,
            'org'              => $org,
            'startdate'        => $startdate,
            'enddate'          => $enddate,
        ]);
    }

    public function time_nurs_depsubsub(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $debsubsub = $request->HR_DEPARTMENT_SUB_SUB_ID;
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();

        if ($startdate == '') {
                $datashow_ = DB::connection('mysql6')->select('
                            SELECT p.ID,c.CHEACKIN_DATE
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,d.HR_DEPARTMENT_SUB_SUB_NAME,hs.HR_DEPARTMENT_SUB_NAME
                                ,h.HR_DEPARTMENT_ID ,h.HR_DEPARTMENT_NAME,hs.HR_DEPARTMENT_SUB_ID ,d.HR_DEPARTMENT_SUB_SUB_ID ,ot.OPERATE_TYPE_ID,ot.OPERATE_TYPE_NAME
                                FROM checkin_index c
                                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                                WHERE c.CHEACKIN_DATE = CURDATE()
                                AND d.HR_DEPARTMENT_SUB_SUB_ID = "'.$debsubsub.'"
                                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                                ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID DESC
                ');
        } else {
                $datashow_ = DB::connection('mysql6')->select('
                            SELECT p.ID,c.CHEACKIN_DATE
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,d.HR_DEPARTMENT_SUB_SUB_NAME,hs.HR_DEPARTMENT_SUB_NAME
                                ,h.HR_DEPARTMENT_ID ,h.HR_DEPARTMENT_NAME,hs.HR_DEPARTMENT_SUB_ID ,d.HR_DEPARTMENT_SUB_SUB_ID ,ot.OPERATE_TYPE_ID,ot.OPERATE_TYPE_NAME
                                FROM checkin_index c
                                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                                WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                                AND d.HR_DEPARTMENT_SUB_SUB_ID = "'.$debsubsub.'"
                                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                                ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID DESC

                ');
        }

        $department_subsub = DB::connection('mysql6')->select('
            SELECT * FROM hrd_department_sub_sub
        ');

        return view('timer.time_nurs_depsubsub', [
            'datashow_'        => $datashow_,
            'startdate'        => $startdate,
            'enddate'          => $enddate,
            'debsubsub'           => $debsubsub,
            'department_subsub'   => $department_subsub,
        ]);
    }
    public function time_nurs_depsubsubexcel(Request $request,$debsubsub,$startdate,$enddate)
    {
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();
        $org = $org_->orginfo_name;

        if ($startdate == '') {
                $export = DB::connection('mysql6')->select('
                            SELECT p.ID,c.CHEACKIN_DATE
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,d.HR_DEPARTMENT_SUB_SUB_NAME,hs.HR_DEPARTMENT_SUB_NAME
                                ,h.HR_DEPARTMENT_ID ,h.HR_DEPARTMENT_NAME,hs.HR_DEPARTMENT_SUB_ID ,d.HR_DEPARTMENT_SUB_SUB_ID ,ot.OPERATE_TYPE_ID,ot.OPERATE_TYPE_NAME
                                FROM checkin_index c
                                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                                WHERE c.CHEACKIN_DATE = CURDATE()
                                AND d.HR_DEPARTMENT_SUB_SUB_ID = "'.$debsubsub.'"
                                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                                ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID DESC
                ');
        } else {
                $export = DB::connection('mysql6')->select('
                        SELECT p.ID,c.CHEACKIN_DATE
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,d.HR_DEPARTMENT_SUB_SUB_NAME,hs.HR_DEPARTMENT_SUB_NAME
                                ,h.HR_DEPARTMENT_ID ,h.HR_DEPARTMENT_NAME,hs.HR_DEPARTMENT_SUB_ID ,d.HR_DEPARTMENT_SUB_SUB_ID ,ot.OPERATE_TYPE_ID,ot.OPERATE_TYPE_NAME
                                FROM checkin_index c
                                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                                WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                                AND d.HR_DEPARTMENT_SUB_SUB_ID = "'.$debsubsub.'"
                                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                                ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID DESC

                ');
        }

        $debname_ = DB::connection('mysql6')->table('hrd_department_sub_sub')->where('HR_DEPARTMENT_SUB_SUB_ID', '=', $debsubsub)->first();

        return view('timer.time_nurs_depsubsubexcel', [
            'export'           =>  $export,
            'deb'              => $debname_->HR_DEPARTMENT_SUB_SUB_NAME,
            'org'              => $org,
            'startdate'        => $startdate,
            'enddate'          => $enddate,
        ]);
    }

    public function time_nurs_person(Request $request)
    {
        $startdate = $request->startdate;
        $enddate   = $request->enddate;
        $iduser    = $request->USERID;
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();

        if ($startdate == '') {
                $datashow_ = DB::connection('mysql6')->select('
                            SELECT p.ID,c.CHEACKIN_DATE
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,d.HR_DEPARTMENT_SUB_SUB_NAME,hs.HR_DEPARTMENT_SUB_NAME
                                ,h.HR_DEPARTMENT_ID ,h.HR_DEPARTMENT_NAME,hs.HR_DEPARTMENT_SUB_ID ,d.HR_DEPARTMENT_SUB_SUB_ID ,ot.OPERATE_TYPE_ID,ot.OPERATE_TYPE_NAME
                                FROM checkin_index c
                                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                                WHERE c.CHEACKIN_DATE = CURDATE()
                                AND p.ID = "'.$iduser.'"
                                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                                ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID DESC
                ');
        } else {
                $datashow_ = DB::connection('mysql6')->select('
                            SELECT p.ID,c.CHEACKIN_DATE
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,d.HR_DEPARTMENT_SUB_SUB_NAME,hs.HR_DEPARTMENT_SUB_NAME
                                ,h.HR_DEPARTMENT_ID ,h.HR_DEPARTMENT_NAME,hs.HR_DEPARTMENT_SUB_ID ,d.HR_DEPARTMENT_SUB_SUB_ID ,ot.OPERATE_TYPE_ID,ot.OPERATE_TYPE_NAME
                                FROM checkin_index c
                                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                                WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                                AND p.ID = "'.$iduser.'"
                                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                                ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID DESC

                ');
        }

        $department_subsub = DB::connection('mysql6')->select('
            SELECT * FROM hrd_department_sub_sub
        ');
        $data_users = DB::connection('mysql6')->select('
            SELECT * FROM hrd_person
        ');

        return view('timer.time_nurs_person', [
            'datashow_'           => $datashow_,
            'startdate'           => $startdate,
            'enddate'             => $enddate,
            'idusershow'          => $iduser,
            'department_subsub'   => $department_subsub,
            'data_users'          => $data_users,
        ]);
    }

    public function time_nurs_personexcel(Request $request,$idusershow,$startdate,$enddate)
    {
        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();
        $org = $org_->orginfo_name;

        if ($startdate == '') {
                $export = DB::connection('mysql6')->select('
                            SELECT p.ID,c.CHEACKIN_DATE
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,d.HR_DEPARTMENT_SUB_SUB_NAME,hs.HR_DEPARTMENT_SUB_NAME
                                ,h.HR_DEPARTMENT_ID ,h.HR_DEPARTMENT_NAME,hs.HR_DEPARTMENT_SUB_ID ,d.HR_DEPARTMENT_SUB_SUB_ID ,ot.OPERATE_TYPE_ID,ot.OPERATE_TYPE_NAME
                                FROM checkin_index c
                                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                                WHERE c.CHEACKIN_DATE = CURDATE()
                                AND p.ID = "'.$idusershow.'"
                                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                                ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID DESC
                ');
        } else {
                $export = DB::connection('mysql6')->select('
                        SELECT p.ID,c.CHEACKIN_DATE
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                                ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                                ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,d.HR_DEPARTMENT_SUB_SUB_NAME,hs.HR_DEPARTMENT_SUB_NAME
                                ,h.HR_DEPARTMENT_ID ,h.HR_DEPARTMENT_NAME,hs.HR_DEPARTMENT_SUB_ID ,d.HR_DEPARTMENT_SUB_SUB_ID ,ot.OPERATE_TYPE_ID,ot.OPERATE_TYPE_NAME
                                FROM checkin_index c
                                LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                                LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                                LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                                LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                                LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                                LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                                LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                                WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                                AND p.ID = "'.$idusershow.'"
                                GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                                ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID DESC

                ');
        }

        $debn_ = DB::connection('mysql6')->table('hrd_person')->where('ID', '=', $idusershow)->first();
        $dss      = $debn_->HR_DEPARTMENT_SUB_SUB_ID;

        $debname_ = DB::connection('mysql6')->table('hrd_department_sub_sub')->where('HR_DEPARTMENT_SUB_SUB_ID', '=', $dss)->first();
       
        $newmonth       = date("m", strtotime($startdate));
        $newyears_       = date("Y", strtotime($startdate));
        $newyears       =  $newyears_+543;  
        return view('timer.time_nurs_personexcel', [
            'export'           =>  $export,
            'deb'              => $debname_->HR_DEPARTMENT_SUB_SUB_NAME,
            'org'              => $org,
            'startdate'        => $startdate,
            'enddate'          => $enddate,
            'm_'               => $newmonth,
            'newyears'         => $newyears,
        ]);
    }

    public function time_nurs_person_pdf(Request $request,$id,$startdate,$enddate)
    {
        // $startdate     = $request->startdate;
        // $enddate       = $request->enddate;
        $date_now      = date('Y-m-d');
        $y             = date('Y') + 543;
        $months        = date('m');
        $newdays       = date('Y-m-d', strtotime($date_now . ' -1 days')); //ย้อนหลัง 1 วัน
        $newweek       = date('Y-m-d', strtotime($date_now . ' -1 week')); //ย้อนหลัง 1 สัปดาห์
        $newDate       = date('Y-m-d', strtotime($date_now . ' -1 months')); //ย้อนหลัง 1 เดือน
        $newyear       = date('Y-m-d', strtotime($date_now . ' -1 year')); //ย้อนหลัง 1 ปี
        $yearnew       = date('Y');
        $year_old      = date('Y')-1;
        $months_old    = ('10');
        $startdate_b   = (''.$year_old.'-10-01');
        $enddate_b     = (''.$yearnew.'-09-30');
        $iduser        = Auth::user()->id;
        $month_id_      = $request->month_id;
        $bgs_year      = DB::table('budget_year')->where('years_now','Y')->first();
        $bg_yearnow    = $bgs_year->leave_year_id;
 
        $datashow = DB::connection('mysql6')->select('
                SELECT p.ID,c.CHEACKIN_DATE
                        ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "1" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKINTIME
                        ,SUBSTRING_INDEX(GROUP_CONCAT((SELECT CONCAT(c.CHEACKIN_TIME) WHERE c.CHECKIN_TYPE_ID = "2" AND c.CHECKIN_PERSON_ID = p.ID)),",",1) AS CHEACKOUTTIME
                        ,CONCAT(f.HR_PREFIX_NAME,p.HR_FNAME," ",p.HR_LNAME) as hrname,d.HR_DEPARTMENT_SUB_SUB_NAME,hs.HR_DEPARTMENT_SUB_NAME
                        ,h.HR_DEPARTMENT_ID ,h.HR_DEPARTMENT_NAME,hs.HR_DEPARTMENT_SUB_ID ,d.HR_DEPARTMENT_SUB_SUB_ID ,ot.OPERATE_TYPE_ID,ot.OPERATE_TYPE_NAME
                        FROM checkin_index c
                        LEFT JOIN hrd_person p on p.ID=c.CHECKIN_PERSON_ID
                        LEFT JOIN hrd_department h on h.HR_DEPARTMENT_ID=p.HR_DEPARTMENT_ID
                        LEFT JOIN hrd_department_sub hs on hs.HR_DEPARTMENT_SUB_ID=p.HR_DEPARTMENT_SUB_ID
                        LEFT JOIN hrd_department_sub_sub d on d.HR_DEPARTMENT_SUB_SUB_ID=p.HR_DEPARTMENT_SUB_SUB_ID
                        LEFT JOIN operate_job j on j.OPERATE_JOB_ID=c.OPERATE_JOB_ID
                        LEFT JOIN operate_type ot on ot.OPERATE_TYPE_ID=j.OPERATE_JOB_TYPE_ID
                        LEFT JOIN hrd_prefix f on f.HR_PREFIX_ID=p.HR_PREFIX_ID
                        WHERE c.CHEACKIN_DATE BETWEEN "'.$startdate.'" and "'.$enddate.'"
                        AND p.ID = "'.$id.'"
                        GROUP BY p.ID,ot.OPERATE_TYPE_ID,c.CHEACKIN_DATE
                        ORDER BY c.CHEACKIN_DATE,p.ID,ot.OPERATE_TYPE_ID DESC

        ');

        $data['month_show']          = DB::table('months')->get();
        $data['air_plan_month']      = DB::table('air_plan_month')->where('active','Y')->get();

        $org_ = DB::connection('mysql')->table('orginfo')->where('orginfo_id', '=', 1)->first();
        $org = $org_->orginfo_name;

        // ผอ **************
        $orgpo = DB::table('orginfo')->where('orginfo_id', '=', 1)
            ->leftjoin('users', 'users.id', '=', 'orginfo.orginfo_po_id')
            ->leftjoin('users_prefix', 'users_prefix.prefix_code', '=', 'users.pname')
            ->first();
        $po = $orgpo->prefix_name . ' ' . $orgpo->fname . '  ' . $orgpo->lname;

        // รอง บ
        $rong = DB::table('orginfo')->where('orginfo_id', '=', 1)
        ->leftjoin('users', 'users.id', '=', 'orginfo.orginfo_manage_id')
        ->leftjoin('users_prefix', 'users_prefix.prefix_code', '=', 'users.pname')
        ->first();
        $rong_bo  = $rong->prefix_name . ' ' . $rong->fname . '  ' . $rong->lname;
        $rong_bo  = $rong->prefix_name . ' ' . $rong->fname . '  ' . $rong->lname;
        $sigrong_ = $rong->signature; //หัวหน้าบริหาร

         // ผู้รายงาน **************
         $count_ = DB::table('users')->where('id', '=', $iduser)->count();
         if ($count_ !='') {
            $signa = DB::table('users')->where('id', '=', $iduser)->leftjoin('users_prefix', 'users_prefix.prefix_id', '=', 'users.pname')->first();
            $ptname   = $signa->prefix_name . '' . $signa->fname . '  ' . $signa->lname;
            $position = $signa->position_name;
            $siguser  = $signa->signature; //ผู้รองขอ
            // $sigstaff = $signature->signature_name_stafftext; //เจ้าหน้าที่
            // $sigrep = $signature->signature_name_reptext; //ผู้รับงาน
            // $sighn = $signature->signature_name_hntext; //หัวหน้า
            $sigrong = $sigrong_; //หัวหน้าบริหาร
         } else {
             # code...
         }

         $debn_ = DB::connection('mysql6')->table('hrd_person')->where('ID', '=', $id)->first();
         $dss      = $debn_->HR_DEPARTMENT_SUB_SUB_ID;

         $debname_ = DB::connection('mysql6')->table('hrd_department_sub_sub')->where('HR_DEPARTMENT_SUB_SUB_ID', '=', $dss)->first();
        //  = strtotime($DateStart);
        // $strtime = strtotime($startdate);
        // $caltime=strtotime("Month",$strtime);
         $newyears_       = date("Y", strtotime($startdate));
         $newmonth        = date("m", strtotime($startdate));
         $newyears        =  $newyears_+543;   
        //  dd($newyears);

        $pdf = PDF::loadView('timer.time_nurs_person_pdf',[
            'startdate'     => $startdate,
            'enddate'       => $enddate,
            'datashow'      => $datashow,
            'month_id'      => $month_id_,
            'rong_bo'       => $rong_bo,
            'po'            => $po,
            'position'      => $position,
            'debname'       => $debname_->HR_DEPARTMENT_SUB_SUB_NAME,
            'org'           => $org,
            'm_'            => $newmonth,
            'newyears'      => $newyears,
        ])
        ->setPaper('a4', 'landscape');

        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf ->get_canvas();
        $canvas->page_text(510, 12, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(255, 0, 0));
        return @$pdf->stream();
    }

}
