<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\adminpanel\Users;
use App\Models\adminpanel\Groups;
use App\Models\adminpanel\activitiestLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    function __construct() {
        
        $this->users= new Users;
        $this->groups= new Groups;
        
      }

    public function index(){
     
        return view('adminpanel/register');
    }
    public function activitylog(){
        $user=Auth::user();
        $this->activitylog= new activitiestLog;
        $activitylogData=$this->activitylog->with('userData')
        ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
     
    return view('adminpanel/activitylog',compact('activitylogData','user'));
     
    }

    public function logout(Request $request){
      
        $request->session()->flush();
        return redirect('/');
    }
    public function noaccess(){

        echo 'this page is not allowed to view';
        die();
    }

    public function login(){
        $data=array();
    
        return view('adminpanel/login', $data);
    }
    public function addUser(){
       $user=Auth::user(); 
        
        // if($user->group_id==config('constants.groups.admin'))
        //     $userGroups = getAllGroups();
        // else
        //     $userGroups = getGroups();
        
        return view('adminpanel/add_users',get_defined_vars());
    }
    public function edit_user_form($id){
       $user=Auth::user(); 
       $userData=$this->users->where('id',$id)->get()->toArray();
       $userData=$userData[0];
        return view('adminpanel/edit_user',get_defined_vars());
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'email'=>'required|email',
             'password'=>'required',
               
       ]);
       
       if(Auth::check()){
        $user=Auth::user();
        if ($user->group_id==config('constants.groups.admin'))
        return redirect()->route('admin.dashboard');
        else if ($user->group_id==config('constants.groups.driver'))
        return redirect()->route('admin.deliveries');
        else
        return redirect('/admin/quote/requested');

       }
        
 
        if (Auth::attempt(['email' => $request['email'], 'password' => $request['password'], 'is_active' => 1])) {

            $userData = $this->users
                                ->with('getGroups')
                                ->where('email', '=', $request['email'])
                                ->where('is_active', '=', 1)
                                ->get()->toArray();
                                //p($userData);

                            $this->user= $userData;
                            $request->session()->put('userData',$userData[0]);
                            $request->session()->put('user_id',$userData[0]['id']);
                            $request->session()->put('group_id',$userData[0]['group_id']);
                            $request->session()->put('role_slug',$userData[0]['get_groups']['slug']);
                            //$data = session()->all();
                            //p($data); die;
            // The user is active, not suspended, and exists.
            //$request->session()->flash('alert-success', 'Successfully Logged in');
            //return redirect()->intended('/admin/dashboard');
            if(Auth::check()){
                $user=Auth::user();
                if ($user->group_id==config('constants.groups.admin'))
                return redirect()->route('admin.dashboard');
                else if ($user->group_id==config('constants.groups.driver'))
                return redirect()->route('admin.deliveries');
                else
                return redirect('/admin/quote/requested');
        
               }
        }
        else{
            
            $request->session()->flash('alert-danger', 'Incorrect Email or Password');
            return redirect('/admin/login');
        }
        
    }
    
    public function getlogin(Request $request){
        $data=array();
     
        $request->validate([
           'email'=>'required|email',
            'password'=>'required',
              
      ]);
     
      $userData = $this->users
                                ->with('getGroups')
                                ->with('getOrganization')
                                ->where('email', '=', $request['email'])
                                //->where('password', '=', md5($request['password']))
                                ->where('is_active', '=', 1)
                                ->get()->toArray();
    //   echo $this->users->where('email', '=', $request['email'])
    //                     ->where('password', '=', md5($request['password']))
    //                     ->toSql();
    //echo $userData[0]['get_groups']['slug'];
     // p($userData); die;
                        if($userData && Hash::check($request['password'], $userData[0]['password'])){
                          echo 'I am in IF Part';
                          $this->user= $userData;
                            $request->session()->put('userData',$userData[0]);
                            $request->session()->put('user_id',$userData[0]['id']);
                            $request->session()->put('group_id',$userData[0]['group_id']);
                            $request->session()->put('role_slug',$userData[0]['get_groups']['slug']);
                            $request->session()->put('org_id',$userData[0]['org_id']);
                            
                            //p($request->session()->all());
                            
                             return redirect('/admin/dashboard/');
                        }
                        else{
                            echo 'I am in else';
                            $request->session()->flash('alert-danger', 'Incorrect Email or Password');
                            return redirect('/admin/login');
                        }

    
    }




    
    public function register(Request $request){
       
         $request->validate([
            'fullname'=>'required',
            'phone'=>'required',
            'email'=>'required|email|distinct|unique:users|min:5',
            'password'=>'required|',
            'password_confirmation'=>'required|same:password',
            'terms'=>'required',

        ]);
        
      
       // $groupsData = $this->groups->where('id', '=', config('constants.groups.staff'))->get()->toArray();
       //p($request->all()); die;
       $is_active=0;
        if(isset($request['is_active']) && $request['is_active']>0)
        $is_active=$request['is_active'];
        
        $this->users->name=$request['fullname'];
        $this->users->email=$request['email'];
        $this->users->cnic=$request['cnic'];
        $this->users->phone=$request['phone'];
        $this->users->password=Hash::make($request['password']);
        $this->users->is_active=$is_active;
       
        $this->users->created_at=time();
        $this->users->group_id=$request['group_id'];
        $request->session()->flash('alert-success', 'Successfully Registered! Please login');
        $this->users->save();

        return redirect()->back();
    }
    public function add_user_data(Request $request){
       
        $validator=$request->validate([
            'firstname'=>'required',
            'lastname'=>'required',
            'email'=>'required|email|distinct|unique:users|min:5',
            'phone'=>'required',
            'password'=>'required|',
            'password_confirmation'=>'required|same:password',
            
        ]);
        
        
        $this->users->firstname=$request['firstname'];
        $this->users->lastname=$request['lastname'];
        $this->users->name=$request['firstname'].' '.$request['lastname'];
        $this->users->email=$request['email'];
        $this->users->phone=$request['phone'];
        $this->users->password=Hash::make($request['password']);
        $this->users->is_active=1;
       
        $this->users->created_at=time();
        $this->users->group_id=config('constants.groups.admin');
        $request->session()->flash('alert-success', 'Successfully Registered! Please login');
        $this->users->save();
        return redirect()->back();
        
    }
    public function update_user_data($id,Request $request){
       
        $rules=[
            'firstname'=>'required',
            'lastname'=>'required',
            'phone'=>'required',
        ];

       if(!empty($request['email'])){
        $data['email']=$request['email'];
        $rules['email']='required|email|distinct|unique:users|min:5';
       }
        

        $validator=$request->validate($rules);
        
        $data['firstname']=$request['firstname'];
        $data['lastname']=$request['lastname'];
        $data['name']=$request['firstname'].' '.$request['lastname'];
        
        $data['phone']=$request['phone'];
        if(!empty($request['password']))
        $data['password']=Hash::make($request['password']);
        
        $request->session()->flash('alert-success', 'Successfully updated! Please login');
        $this->users->where('id',$id)->update($data);
        return redirect()->back();
        
    }

    // List All the users 
    public function users(){

        $user=Auth::user();
        
        //$usersData=$this->users->with('getGroups')->where('is_active','<',3)->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        $usersData=$this->users->with('getGroups')->where('is_active','<',3)->where('group_id',config('constants.groups.admin'))->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        //$usersData=$usersData->toArray();
        return view('adminpanel/users',compact('usersData','user'));
    }
    public function calenderSchedule(){
        $user=Auth::user();
        
        
        
        return view('adminpanel/calender_schedule',compact('user'));
    }
    public function UpdateUsersData($id,Request $request)
    {
        $dataArray['error']='No';
        

        $validated =  $request->validate([
            'name' => 'required',
            'group_id' => 'required'
            ]);
            if(!$validated){

                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
                echo json_encode($dataArray);
                die;

            }
     
        $data['name']=$request['name'];
        $data['group_id']=$request['group_id'];

        $groupData=Groups::find($request['group_id']);
       
        $groupData=$groupData->toArray();
        // p($groupData);
        // die;
        $dataArray['name']=$data['name'];
        $dataArray['id']=$id;
        $dataArray['group_title']=$groupData['title'];
        $dataArray['group_role']=$groupData['role'];
        
        $dataArray['msg']='Mr.'.get_session_value('name').', '.$data['name'].' record Successfully Updated !';
        $this->users->where('id', $id)
                    ->update($data);
        echo json_encode($dataArray);
        die;

    }
    public function DeleteUsersData($id){
        $dataArray['error']='No';
        $dataArray['title']='User';

        $result=$this->users->where('id','=',$id)->update(array('is_active'=>3));             
        if($result)
        $dataArray['msg']='Mr.'.get_session_value('name').', record delted successfully!';
        else{
            $dataArray['error']='Yes';
            $dataArray['msg']='There is some error ! Please fill all the required fields.';

        }
        echo json_encode($dataArray);
        die;
    }
    public function changeStatus($id, Request $req){
        //echo '<textarea cols="20" rows="6">';
        $dataArray['error']='No';
        $dataArray['title']='Active/in-Active User';

        if(!isset($req['action'])){
            $dataArray['error']='Yes';
            $dataArray['msg']='There is some error ! Please try again later!.';
            echo json_encode($dataArray);
            die;
        }

        $is_active=0;
        $dataArray['status_btn']='<a class="btn bg-gradient-secondary btn-flat btn-sm"><i class="fas fa-chart-line"></i> In-Active</a>';
        
        $dataArray['status_action_btn']='<button onClick="changeStatus('.$id.','.$req['counter'].',\'activate\')" type="button" class="btn btn-success btn-block btn-sm"><i class="fas fa-chart-line"></i> Activate</button>';
        $action='De-activated';

        if($req['action']=='activate'){
            $dataArray['status_btn']='<a class="btn btn-success btn-flat btn-sm"><i class="fas fa-chart-line"></i> Active</a>';

            $dataArray['status_action_btn']='<button onClick="changeStatus('.$id.','.$req['counter'].',\'deactivate\')" type="button" class="btn btn-warning btn-block btn-sm"><i class="fas fa-chart-line"></i>De-Activate</button>';
         
            $is_active=1;
            $action='Activated';
        }
        

        $result=$this->users->where('id','=',$id)->update(array('is_active'=>$is_active));             
        if($result)
        $dataArray['msg']='Mr.'.get_session_value('name').', User '.$action.' successfully!';
        else{
            $dataArray['error']='Yes';
            $dataArray['msg']='There is some error ! Please fill all the required fields.';
        }
        echo json_encode($dataArray);
        die;
    }
}
