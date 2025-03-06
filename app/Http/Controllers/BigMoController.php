<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
 
use Illuminate\support\Facades\Validator;
use App\Models\User;
use App\Models\Department;
use App\Models\Departmentsub;
use App\Models\Departmentsubsub;
use App\Models\Position;
use App\Models\Status;
use App\Models\Users_prefix;
use App\Models\Users_kind_type;
use App\Models\Users_group;
use Illuminate\Support\Facades\File;
use DataTables;
use Illuminate\Support\Facades\Hash;
use Auth;

class BigMoController extends Controller
{   
public function user_main(Request $request)
{   
    $data['users'] = User::get();
    $data['department'] = Department::get();
    $data['department_sub'] = Departmentsub::get();
    $data['department_sub_sub'] = Departmentsubsub::get();
    $data['position'] = Position::get();
    $data['status'] = Status::get();
    return view('user_main',$data);
}
public function user_data(Request $request)
{   
    $data['q'] = $request->query('q');
    $query = User::select('users.*')
    ->where(function ($query) use ($data){
        $query->where('pname','like','%'.$data['q'].'%');
        $query->orwhere('fname','like','%'.$data['q'].'%');
        $query->orwhere('lname','like','%'.$data['q'].'%');
        $query->orwhere('tel','like','%'.$data['q'].'%');
        $query->orwhere('username','like','%'.$data['q'].'%');
    });
    $data['users'] = $query->orderBy('id','DESC')->paginate(10);
    $data['department'] = Department::get();
    $data['department_sub'] = Departmentsub::get();
    $data['department_sub_sub'] = Departmentsubsub::get();
    $data['position'] = Position::get();
    $data['status'] = Status::get();
    return view('user.user_data',$data);
}
 

public function profile_edit(Request $request,$id)
{   
    $data['q'] = $request->query('q');
    $query = User::select('users.*')
    // ->leftjoin('store_manager','store_manager.store_id','=','users.store_id')
    ->where(function ($query) use ($data){
        $query->where('pname','like','%'.$data['q'].'%');
        $query->orwhere('fname','like','%'.$data['q'].'%');
        $query->orwhere('lname','like','%'.$data['q'].'%');
        $query->orwhere('tel','like','%'.$data['q'].'%');
        $query->orwhere('username','like','%'.$data['q'].'%');
    });
    $data['users'] = $query->orderBy('id','DESC')->get();
    $data['department'] = Department::get();
    $data['department_sub'] = Departmentsub::get();
    $data['department_sub_sub'] = Departmentsubsub::get();
    $data['position'] = Position::get();
    $data['status'] = Status::get();
    $data['users_prefix'] = Users_prefix::get();
    $data['users_kind_type'] = Users_kind_type::get();
    $data['users_group'] = Users_group::get();

    $dataedit = User::where('id','=',$id)->first();

    return view('user.profile_edit',$data,[
        'dataedits'=>$dataedit
    ]);
} 
public function profile_update(Request $request)
{
    $date =  date('Y');
    $maxid = User::max('id');
    $idfile = $maxid+1;
    $fname = $request->fullname;
    $lname = $request->lname;
    $pname = $request->pname;
    // dd($fname);
    $idper = $request->input('id');
    $usernameup = $request->input('username');
    $count_check = User::where('username','=',$usernameup)->count(); 
         
            $update            = User::find($idper);
            $update->fname     = $fname;
            $update->lname     = $lname;     
            $update->pname     = $pname; 
            $update->cid       = $request->cid;   
            $update->username  = $usernameup; 
            // $update->money = $request->money;
            $update->line_token = $request->line_token;

            $pass               = $request->password;
            $update->password   = Hash::make($pass);
            $update->passapp    = $pass;
            // $update->password = Hash::make($request->password);
            // $update->member_id =  'MEM'. $date .'-'.$idfile;

            // $depid = $request->dep_id; 
            // $iddep = DB::table('department')->where('DEPARTMENT_ID','=',$depid)->first();
            // $update->dep_id = $iddep->DEPARTMENT_ID; 
            // $update->dep_name = $iddep->DEPARTMENT_NAME; 

            // $depsubid = $request->dep_subid; 
            // $iddepsub = DB::table('department_sub')->where('DEPARTMENT_SUB_ID','=',$depsubid)->first();
            // $update->dep_subid = $iddepsub->DEPARTMENT_SUB_ID; 
            // $update->dep_subname = $iddepsub->DEPARTMENT_SUB_NAME; 

            // $depsubsubid = $request->dep_subsubid; 
            // $iddepsubsub = DB::table('department_sub_sub')->where('DEPARTMENT_SUB_SUB_ID','=',$depsubsubid)->first();
            // $update->dep_subsubid = $iddepsubsub->DEPARTMENT_SUB_SUB_ID;
            // $update->dep_subsubname = $iddepsubsub->DEPARTMENT_SUB_SUB_NAME;    

            // $depsubsubtrueid = $request->dep_subsubtrueid; 
            // $iddepsubsubtrue = DB::table('department_sub_sub')->where('DEPARTMENT_SUB_SUB_ID','=',$depsubsubtrueid)->first();
            // $update->dep_subsubtrueid = $iddepsubsubtrue->DEPARTMENT_SUB_SUB_ID; 
            // $update->dep_subsubtruename = $iddepsubsubtrue->DEPARTMENT_SUB_SUB_NAME; 

            // $posid = $request->position_id; 
            // $idpos = DB::table('position')->where('POSITION_ID','=',$posid)->first();
            // $update->position_id = $idpos->POSITION_ID;
            // $update->position_name = $idpos->POSITION_NAME; 

            // $typid = $request->users_type_id; 
            // $typ = DB::table('users_kind_type')->where('users_kind_type_id','=',$typid)->first();
            // $update->users_type_id = $typ->users_kind_type_id;
            // $update->users_type_name = $typ->users_kind_type_name;

            // $groupid = $request->users_group_id; 
            // $idgroup = DB::table('users_group')->where('users_group_id','=',$groupid)->first();
            // $update->users_group_id = $idgroup->users_group_id;
            // $update->users_group_name = $idgroup->users_group_name;

            // $update->start_date = $request->start_date;
            // $update->end_date = $request->end_date;
            // $update->status = $request->status;
            $signature          = $request->input('signature2'); 
            if ($signature !='') { 
                $update->signature    = $signature;
            }
             
            
            if ($request->hasfile('img')) {
                $description = 'storage/person/'.$update->img;
                if (File::exists($description))
                {
                    File::delete($description);
                }
                $file = $request->file('img');
                $extention_ = $file->getClientOriginalExtension();
                $filename = time().'.'.$extention_; 
                $request->img->storeAs('person',$filename,'public'); 
                $update->img = $filename;
                $update->img_name = $filename;
                // dd($extention_); 
                if ($extention_ =='.jpg') {
                    $file64 = "data:image/jpg;base64,".base64_encode(file_get_contents($request->file('img'))); 
                } else {
                    $file64 = "data:image/png;base64,".base64_encode(file_get_contents($request->file('img'))); 
                }                       
                if ($file64 != '') {
                    $update->img_base       = $file64;
                }
            }
            $update->save();

            return response()->json([
                'status'     => '200'
            ]);
      
  
    
}
public function password_update(Request $request)
{
    $idper =  Auth::user()->id;
       
    $update = User::find($idper);

    // $update->password = Hash::make($request->password);

    $pass               = $request->password;
    $update->password   = Hash::make($pass);
    $update->passapp    = $pass;

    
    $update->save();

    return response()->json([
        'status'     => '200'
    ]);
      
  
    
}


public function user_pass(Request $request)
{   
    $data['status'] = Status::get();
    $data['user'] = User::get();

    $username         = $request->username;
    $pass             = $request->password;

// $decrypt= Crypt::decrypt($data->password);  
// $password_plaintext = "123456";

// $password_hash = password_hash( $password_plaintext, PASSWORD_DEFAULT, [ 'cost' => 11 ] );

// dd( password_get_info( $password_hash ) );

// $hash = "$2y$10$F5Dqf3kBFhZd/HyJmlnfeuV1NrnovJWgD9FJBGJ7OQJdyuSpZAa7a";
// $verify = password_verify($plaintext_password, $hash);
// password_verify($data->password); 
// dd($plaintext_password);

// $plaintext_password = "123456";

// $hash =
// "$2y$10$F5Dqf3kBFhZd/HyJmlnfeuV1NrnovJWgD9FJBGJ7OQJdyuSpZAa7a";

// $verify = password_verify($plaintext_password, $hash);

// if ($verify) {
// 	echo 'Password Verified!';
// } else {
// 	echo 'Incorrect Password!';
// }


    return view('bigmo.user_pass',$data);
}

}