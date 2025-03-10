<?php
use App\Models\Person;
use App\Models\Org;
use Illuminate\Support\Facades\DB;
//เมื่อมีการเพิ่ม Function ใหม่ให้สั้ง composer dump-autoload

function getDateTimeDiff($date){
    $now_timestamp     = strtotime(date('Y-m-d H:i:s'));
    $diff_timestamp    =$now_timestamp - strtotime($date);

    if ($diff_timestamp < 60) {
        return 'few seconds ago';
    } else if ($diff_timestamp >= 60 && $diff_timestamp < 3600) {
        return round($diff_timestamp / 60). 'min ago';
    } else if ($diff_timestamp >= 3600 && $diff_timestamp < 86400) {
        return round($diff_timestamp / 3600). 'hours ago';
    } else if ($diff_timestamp >= 86400 && $diff_timestamp < (86400 *30)) {
        return round($diff_timestamp / 86400). 'days ago';

    } else if ($diff_timestamp >= (86400 *30) && $diff_timestamp < (86400 *365)) {
        return round($diff_timestamp / (86400 *30)). 'months ago';
    } else {
        return round($diff_timestamp / (86400 *365)). 'years ago';
    }


}

function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824)
    {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    }
    elseif ($bytes >= 1048576)
    {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    }
    elseif ($bytes >= 1024)
    {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    }
    elseif ($bytes > 1)
    {
        $bytes = $bytes . ' bytes';
    }
    elseif ($bytes == 1)
    {
        $bytes = $bytes . ' byte';
    }
    else
    {
        $bytes = '0 bytes';
    }

    return $bytes;
}


//สร้าง File PDF
function viewPdf($html){
    $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
    $fontDirs = $defaultConfig['fontDir'];
    $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
    $fontData = $defaultFontConfig['fontdata'];

   $mpdf = new \Mpdf\Mpdf([
       'fontDir' => array_merge($fontDirs, [ public_path('fonts/'),]),
       // ตจั้งค่า Fonts
       'fontdata' => $fontData + [
           'sarabun_new' => [
           'R' => 'THSarabunNew.ttf',
           'I' => 'THSarabunNew Italic.ttf',
           'B' => 'THSarabunNew Bold.ttf',
           ],
       ],
       'default_font' => 'sarabun_new',
       ]);
       $stylesheet1 = public_path('css/pdf.css'); // external css
    //    $mpdf->WriteHTML($stylesheet,1);
       $stylesheet = file_get_contents($stylesheet1); // external css
       $mpdf->WriteHTML($stylesheet,1);
       $mpdf->WriteHTML($html);
       $mpdf->Output();
       return $mpdf->Output();
}



function thainumDigit($num){
    return str_replace(array( '0' , '1' , '2' , '3' , '4' , '5' , '6' ,'7' , '8' , '9' ),array( "o" , "๑" , "๒" , "๓" , "๔" , "๕" , "๖" , "๗" , "๘" , "๙" ),$num);
}

function convert($number){
    $txtnum1 = array('ศูนย์','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า','สิบ');
    $txtnum2 = array('','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน');
    $number = str_replace(",","",$number);
    $number = str_replace(" ","",$number);
    $number = str_replace("บาท","",$number);
    $number = explode(".",$number);
    if(sizeof($number)>2){
    return '';
    exit;
    }
    $strlen = strlen($number[0]);
    $convert = '';
    for($i=0;$i<$strlen;$i++){
        $n = substr($number[0], $i,1);
        if($n!=0){
            if($i==($strlen-1) AND $n==1){ $convert .= 'เอ็ด'; }
            elseif($i==($strlen-2) AND $n==2){  $convert .= 'ยี่'; }
            elseif($i==($strlen-2) AND $n==1){ $convert .= ''; }
            else{ $convert .= $txtnum1[$n]; }
            $convert .= $txtnum2[$strlen-$i-1];
        }
    }

    $convert .= 'บาท';
    if($number[1]=='0' OR $number[1]=='00' OR
    $number[1]==''){
    $convert .= 'ถ้วน';
    }else{
    $strlen = strlen($number[1]);
    for($i=0;$i<$strlen;$i++){
    $n = substr($number[1], $i,1);
        if($n!=0){
        if($i==($strlen-1) AND $n==1){$convert
        .= 'เอ็ด';}
        elseif($i==($strlen-2) AND
        $n==2){$convert .= 'ยี่';}
        elseif($i==($strlen-2) AND
        $n==1){$convert .= '';}
        else{ $convert .= $txtnum1[$n];}
        $convert .= $txtnum2[$strlen-$i-1];
        }
    }
    $convert .= 'สตางค์';
    }
    return $convert;
    }

// Format Display วันที่
function formate($strDate)
    {
    if($strDate == '' || $strDate == null || $strDate == '0000-00-00'){

        $date = '';

    }else{

        $strYear = date("Y",strtotime($strDate));
        $strMonth= date("m",strtotime($strDate));
        $strDay= date("d",strtotime($strDate));
        $date = $strDay."/".$strMonth."/".$strYear;
    }

    return $date;

    }


    function formatetime($strtime)
    {
    $H = substr($strtime, 0, 5);
    return $H;
    }
// รับค่าปีงบประมาณปัจจุบัน
function getBudgetYear()
{
    if(date('m') > 9){
        $year = date('Y') + 544;
    }else{
        $year = date('Y') + 543;
    }
    return $year;
}
//รับค่าปีจาก ปีปัจจุบัน ย้อนหลังไป ตามที่กำหนด(ปี) ค่าเริ่มต้น 10 ปี
function getYearAmount($amontbefore = 10)
{
    $year = date('Y') + 543;
    for($i = $year ;$i > $year - $amontbefore; $i--){
        $year_result[$i] = $i;
    }
    return $year_result;
}
//รับค่าปีจาก ปีงบประมาณปัจจุบัน ย้อนหลังไป ตามที่กำหนด(ปี) ค่าเริ่มต้น 10 ปี เพิ่มทั้งหมด
function getBudgetYearAmount_all($amontbefore = 10)
{
    $yearbudget = getBudgetYear();
    $year['all'] = 'ทั้งหมด';
    for($i = $yearbudget ;$i > $yearbudget - $amontbefore; $i--){
        $year[$i] = $i;
    }
    return $year;
}
//รับค่าปีจาก ปีงบประมาณปัจจุบัน ย้อนหลังไป ตามที่กำหนด(ปี) ค่าเริ่มต้น 10 ปี
function getBudgetYearAmount($amontbefore = 10)
{
    $yearbudget = getBudgetYear();
    for($i = $yearbudget ;$i > $yearbudget - $amontbefore; $i--){
        $year[$i] = $i;
    }
    return $year;
}
// คำนวนอายุ Y-m-d
function getAge($birthday)
{
    $then = strtotime($birthday);
    return(floor((time()-$then)/31556926));
}

function dateThaifromFull($strDate)
    {
    $strYear = date("Y",strtotime($strDate))+543;
    $strMonth= date("n",strtotime($strDate));
    $strDay= date("j",strtotime($strDate));

    $strMonthCut = Array("","มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤษจิกายน","ธันวาคม");
    $strMonthThai=$strMonthCut[$strMonth];
    return $strDay.' '.$strMonthThai.'  พ.ศ. '.$strYear;
    }

function shortMonthThai($month)
{
    $month = (int)$month;
    $strMonthCut    = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
    return !empty($strMonthCut[$month])?$strMonthCut[$month]:'';
}
// แปลงวันที่ภาษาไทย
function DateThai($strDate)
{
    if($strDate == '' || $strDate == null || $strDate == '0000-00-00'){
        $datethai = '';
    }else{
        $strYear = date("Y",strtotime($strDate))+543;
        $strMonth= date("n",strtotime($strDate));
        $strDay= date("j",strtotime($strDate));
        $strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
        $strMonthThai=$strMonthCut[$strMonth];
        $datethai = $strDate ? ($strDay.' '.$strMonthThai.' '.$strYear) : '-';
    }
    return $datethai;
}

function DatetimeThai($strDate)
{
    if($strDate == '' || $strDate == null || $strDate == '0000-00-00 00:00:00'){
        $datethai = '-';
    }else{
        $strYear = date("Y",strtotime($strDate))+543;
        $strMonth= date("n",strtotime($strDate));
        $strDay= date("j",strtotime($strDate));
        $strTime = date("H:i:s น.",strtotime($strDate));
        $strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
        $strMonthThai=$strMonthCut[$strMonth];
        $datethai = $strDate ? ($strDay.' '.$strMonthThai.' '.$strYear .' '.$strTime) : '-';
    }
    return $datethai;
}
function DatetimeThainew($strDate)
{
    if($strDate == '' || $strDate == null || $strDate == '0000-00-00 00:00:00'){
        $datethai = '-';
    }else{
        $strYear = date("Y",strtotime($strDate));
        $strMonth= date("n",strtotime($strDate));
        $strDay= date("j",strtotime($strDate));
        $strTime = date("H:i:s",strtotime($strDate));
        // $strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
        // $strMonthThai=$strMonthCut[$strMonth];
        $datethai = $strDate ? ($strYear.'-'.$strMonth.'-'.$strDay .'-'.$strTime) : '-';
    }
    return $datethai;
}

//แปลง พ.ศ เป็น ค.ศ ลง  Database
  function DateThaiToEn($date)
{
    $strDate =  explode("/", $date);
    $strYear = ($strDate[2]) - 543;
    $strMonth= $strDate[1];
    $strDay= $strDate[0];

    return $strDate ? $strYear.'-'.$strMonth.'-'.$strDay : null;
  }

  function DateEnToThai($date)
  {
      if($date){
          $strDate =  explode("-", $date);
          $strYear = ($strDate[0]) + 543;
          $strMonth = $strDate[1];
          $strDay= $strDate[2];

          return $strDay.'/'.$strMonth.'/'.$strYear;
        // return '10/20/2563';
        }else{
            return false;
        }
    }
    // covert datepicker พ.ศ. => ค.ศ.
    function pickerThToEn($date){
        if(!empty($date)){
            $strDate    =  explode("/", $date);
            $strDay     = $strDate[0];
            $strMonth   = $strDate[1];
            $strYear    = $strDate[2];
            if($strYear > 2500){
                $strYear -= 543;
            }
            return $strDay.'/'.$strMonth.'/'.$strYear;
          }else{
            return '';
          }
    }
    //แปลง Thai date to en date แต่ต้องเช็คก่อนว่าเป็น year thai หรือไม่
    function CheckDatethaiParse($date){
        if($date){
            $strDate    =  explode("/", $date);
            $strDay     = $strDate[0];
            $strMonth   = $strDate[1];
            $strYear    = $strDate[2];
            if($strYear > 2500){
                $strYear -= 543;
            }
            return $strYear.'-'.$strMonth.'-'.$strDay;
          }else{
            return false;
          }
    }
    //แปลงใส่ Datepicker
    function toDatePicker($date)
    {
        if($date){
            $strDate =  explode("-", $date);
            $strYear = $strDate[0];
            $strMonth = $strDate[1];
            $strDay= $strDate[2];

            return $strDay.'/'.$strMonth.'/'.$strYear;
          }else{
              return false;
          }
      }

      function datepickerTodate($date)
      {
        if($date){
            $strDate =  explode("/", $date);
            $strDay= $strDate[0];
            $strMonth = $strDate[1];
            $strYear = $strDate[2];

            return $strYear.'-'.$strMonth.'-'.$strDay;
          }else{
            return false;
          }
      }

function DateThairetire($strDate)
{

  $strMonth= date("n",strtotime($strDate));
  if($strMonth  > 9){
    $strYear = date("Y",strtotime($strDate))+543+61;
  }else{
    $strYear = date("Y",strtotime($strDate))+543+60;
  }

  return "30 ก.ย. $strYear";
}


function getAgeretire($birthday) {
  $then = strtotime($birthday);

  return(60-(floor((time()-$then)/31556926)));
}


    // ดึงชื่อและนามสกุลผู้ใช้งานระบบ
    function userInfo(){
        $id = Auth::user()->PERSON_ID;
        $model =  Person::where('ID','=',$id)->first();
        return $model->HR_PREFIX_NAME.' '.$model->HR_FNAME.' '.$model->HR_LNAME;

    }

function Infohostname(){
    $namehos = DB::table('info_org')->where('ORG_ID','=',1)->first();

    return $module;
}

function json_encode_u($variable)
{
    return json_encode($variable , JSON_UNESCAPED_UNICODE);
}


function DateDiff($strDate1,$strDate2)
{
return (strtotime($strDate2) - strtotime($strDate1))/  ( 60 * 60 * 24 );  // 1 day = 60*60*24
}
function TimeDiff($strTime1,$strTime2)
{
return (strtotime($strTime2) - strtotime($strTime1))/  ( 60 * 60 ); // 1 Hour =  60*60
}
function DateTimeDiff($strDateTime1,$strDateTime2)
{
return (strtotime($strDateTime2) - strtotime($strDateTime1))/  ( 60 * 60 ); // 1 Hour =  60*60
}

            // $start = new DateTime('first day of this month midnight');
            // $end   = new DateTime('first day of next month midnight');
            // echo $start->format('Y-m-d H:i:s'); // 2022-12-01 00:00:00
            // echo $end->format('Y-m-d H:i:s'); // 2023-01-01 00:00:00
            // $interval = new DateInterval('P1D'); // ระยะเวลาห่างกัน 1 วัน
            // $period = new DatePeriod($start, $interval, $end);
            // foreach ($period as $dt) {
            //     echo $dt->format("Y-m-d")."<br>";
            // }

            // วนลูปแสดงวันที่ 1 ถึงวันสิ้นเดือนของเดือนปัจจุบัน
            // เนื่องจากรูปแบบการใช้งานข้างต้นใน PHP ที่ไม่ใช่เวอร์ชั่นที่ 8.2 จะไม่มี
            // DatePeriod::INCLUDE_END_DATE หรือไม่รวมวันที่สุดท้ายในค่าเริ่มต้น
            // การกำหนดวันสุดท้ายของเดือน จึงใช้เป็นการกำหนดวันแรกของเดือนถัดไปแทน เพื่อ
            // ให้รวมวันสุดท้ายของเดือนเข้ามาด้วย

            // กรณีเป็น PHP เวอร์ชั่น 8.2 เราสามารถกำหนดแบบนี้แทนได้

            // $start = new DateTime('first day of this month midnight');
            // $end   = new DateTime('last day of this month midnight');
            // echo $start->format('Y-m-d H:i:s'); // 2022-12-01 00:00:00
            // echo $end->format('Y-m-d H:i:s'); // 2022-12-31 00:00:00
            // $interval = new DateInterval('P1D'); // ระยะเวลาห่างกัน 1 วัน
            // $period = new DatePeriod($start, $interval, $end,
            //  DatePeriod::INCLUDE_END_DATE);



        // $start = new DateTime('first day of january midnight');
        // echo $start->format('Y-m-d H:i:s'); // 2022-01-01 00:00:00
        // $interval = new DateInterval('P1M'); // ระยะเวลา 1 เดือน
        // $recurrences = 11; // ทำซ้ำ 11 ครั้ง
        // $period = new DatePeriod($start, $interval, $recurrences);
        // foreach ($period as $dt) {
        //     echo $dt->format("Y-m")."<br>";
        // }
        // // ผลลัพธ์เราก็ไจะได้วันที่ 1 ของแต่ละเดือน ในปีนั้นๆ มาใช้งานได้
        // // ถ้าต้องการแสดงแค่ 3 เดือน 6 เดือน 9 เดือน ก็แค่เปลี่ยนตัวเลขจำนวนการทำซ้ำ ตามต้องการ
        // // ข้อสังเกต ระยะเวลาที่กำหนดด้วย "P1M" ระบบจะพิจารณาตามวันที่จริง เพราะข้อมูลช่วงของ
        // // วันที่จะต้องเป็นช่วงเวลาจริงๆ แต่ถ้าเราต้องการ วันที่ตามจำนวนวัน เช่น กรณี กำหนดผ่อนชำระ
        // // ทุกๆ 30 วัน และกำหนดเป็น "P30D" ค่าที่ได้จะนับเป็นวัน บางเดือนอาจจะมี 2 ค่าก็ได้ เช่น
        // // วันที่ 1 และอีก 30 วันก็เป็นวันที่ 31 ดูตัวอย่างด้านล่าง

        // $start = new DateTime('first day of january midnight');
        // echo $start->format('Y-m-d H:i:s'); // 2022-01-01 00:00:00
        // $interval = new DateInterval('P30D'); // ทุกๆ 30 วัน
        // $recurrences = 12; // รวม 12 รอบบิล
        // // เราไม่รวมวันที่แรกหรือวันที่ 1 กำหนด้วย DatePeriod::EXCLUDE_START_DATE
        // $period = new DatePeriod($start, $interval, $recurrences,
        //                 DatePeriod::EXCLUDE_START_DATE);
        // foreach ($period as $dt) {
        //     echo $dt->format("Y-m-d")."<br>";
        // }
