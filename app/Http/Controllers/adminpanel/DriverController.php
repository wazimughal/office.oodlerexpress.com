<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\adminpanel\Users;
use App\Models\adminpanel\Groups;
use App\Models\adminpanel\FilesManage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;


// Used for Email Section
use App\Mail\EmailTemplate;
use Illuminate\Support\Facades\Mail;

class DriverController extends Controller
{
    

    function __construct() {
        
        $this->users= new Users;
        $this->groups= new Groups;
        $this->files= new FilesManage;
      }
      public function adddrivers(){
        $user=Auth::user(); 
        
         return view('adminpanel/add_drivers',compact('user'));
     }
      public function edit_driver($id){
        $user=Auth::user(); 
        $where_clasue=[
            'id'=>$id
        ];
        $driverData=
                $this->users
                ->where($where_clasue)
                ->get()
                ->toArray();

                if(!empty($driverData))
                $driverData=$driverData[0];

         return view('adminpanel.edit_drivers',get_defined_vars());
     }
      public function save_edit_driver($id, Request $req){
        
            $user=Auth::user(); 
            $validator=$req->validate([
                'firstname'=>'required',
                'lastname'=>'required',
                'phone'=>'required',
                'city'=>'required',
                'zipcode'=>'required',
                'address'=>'required',
            ]);

            $dataArray['firstname']=$req['firstname'];
            $dataArray['lastname']=$req['lastname'];
            $dataArray['name']=$req['firstname'].' '.$req['lastname'];
            $dataArray['phone']=$req['phone'];
            $dataArray['address']=$req['address'];
            $dataArray['city_id']=$req['city'];
            $dataArray['zipcode_id']=$req['zipcode'];

            if(isset($req['license_no']) && !empty($req['license_no']))
            $dataArray['license_no']=$req['license_no'];

            if(isset($req['password']) && $req['password']!='')
            $dataArray['password']=$req['password'];
            // Get The City ID from table Cities
            if(isset($req['othercity']) && !empty($req['othercity'])){
                $cityId = getOtherCity($req['othercity']);
                $dataArray['city_id']=$req['othercity'];
            } else
            $dataArray['city_id']=$req['city'];

            // Get the Zipcode id from the table zipcode
            if(isset($req['otherzipcode']) && !empty($req['otherzipcode'])){
                $zipcode_id = getOtherZipCode($req['otherzipcode']);
                $dataArray['zipcode_id']=$req['otherzipcode'];
            }
            else
            $dataArray['zipcode_id']=$req['zipcode'];                

                $this->users->where('id', $id)->update( $dataArray);
           
      
            if(isset($req['password']) && $req['password']!=''){
                $driverData=$this->users->where('id',$id)->get()->toArray();
                $driverData=$driverData[0];
                  // Email Section
                $mailData['subject']='Password changed for driver having License No.:'.$driverData['license_no'] ;  
                $mailData['body_message']='Your Password changed so Now You can login in CRM using password <strong>'.$req['password'].'</strong> and the email <strong>'.$driverData['email'].'</strong> as user name.';
                $emailAdd=[
                    $driverData['email'],
                ];
            

                if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
                    $req->session()->flash('alert-warning', 'Email Notification sent');
                } 

            }
      
            
            $req->session()->flash('alert-success', 'Driver data updated Successfully !');

        // Activity Log
        $activityComment='Mr.'.get_session_value('name').' updated driver data';
        $activityData=array(
            'user_id'=>get_session_value('id'),
            'action_taken_on_id'=>$id,
            'action_slug'=>'driver_updated',
            'comments'=>$activityComment,
            'others'=>'users',
            'created_at'=>date('Y-m-d H:I:s',time()),
        );
        $activityID=log_activity($activityData);

        return redirect()->back();
     }
    public function add_documents($id){
        $user=Auth::user(); 
        $userData=$this->users->where('id',$id)->with('files')->with('city')->with('ZipCode')->with('getGroups')->get()->toArray();
       
         return view('adminpanel/uploadform',compact('user','userData'));
         return view('adminpanel/add_driver_documents',compact('user','userData'));
     }
    public function upload_documents($id,Request $request){
        $user=Auth::user();
            $image = $request->file('file');
            $imageExt=$image->extension();
            $imageName = time().'.'.$imageExt;

     

            $image->move(public_path('uploads'),$imageName);
            $orginalImageName=$image->getClientOriginalName();
        
        //return response()->json(['success'=>$imageName]);

            $this->files->name=$orginalImageName;
            $this->files->slug=phpslug($imageName);
            $this->files->path=url('uploads').'/'.$imageName;
            $this->files->description=$orginalImageName.' file uploaded';
            $this->files->otherinfo=$imageExt;
            $this->files->user_id=$id;
            $this->files->save();
        //             ->update($data);
        // $this->files->where('id', $id)
        //             ->update($data);

                    // Activity Log
                    $activityComment='Mr.'.get_session_value('name').' uploaded documents for driver';
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$id,
                        'action_slug'=>'driver_documents_added',
                        'comments'=>$activityComment,
                        'others'=>'files',
                        'created_at'=>date('Y-m-d H:I:s',time()),
                    );
                    $activityID=log_activity($activityData);

        return response()->json(['success'=>$imageName]);

        
     }
     public function add_new_driver(Request $request){
       $user=Auth::user();
       if($user->group_id!=config('constants.groups.admin'))
       return false;
       
        $validator=$request->validate([
            'firstname'=>'required',
            'lastname'=>'required',
            'password'=>'required',
            'email'=>'required|email|distinct|unique:users|min:5',
            'phone'=>'required',
            'license_no'=>'required|distinct|unique:users|min:5',
            'city'=>'required',
            'address'=>'required',
        ]);
        
        
        $this->users->name=$request['firstname'].' '.$request['lastname'];
        $this->users->firstname=$request['firstname'];
        $this->users->lastname=$request['lastname'];
        $this->users->email=$request['email'];
        $this->users->phone=$request['phone'];
        
        
        $this->users->license_no=$request['license_no'];
        $this->users->address=$request['address'];
        $this->users->is_active=1;
        $this->users->password=Hash::make($request['password']);

        $this->users->created_at=time();
        $this->users->group_id=config('constants.groups.driver');
       
        if(isset($request['othercity']) && !empty($request['othercity']))
        $cityId = getOtherCity($request['othercity']);
        else
        $cityId=$request['city'];
        $this->users->city_id=$cityId;

        if(isset($request['otherzipcode']) && !empty($request['otherzipcode']))
        $zipcode = getOtherZipCode($request['otherzipcode']);
        else
        $zipcode=$request['zipcode'];
        $this->users->zipcode_id=$zipcode;
  
        $request->session()->flash('alert-success', 'driver Added! Please Check in drivers list Tab');
        $this->users->save();

        // Email Section
        $mailData['subject']='New Driver added having License No.:'.$request['license_no'] ;  
        $mailData['body_message']='Mr.'.$this->users->name.' driver added in the system. you can login in CRM using password <strong>'.$request['password'].'</strong> and the email <strong>'.$request['email'].'</strong> as user name.';
        $emailAdd=[
            config('constants.admin_email'),
            $request['email'],
        ];
       

        if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
            $request->session()->flash('alert-warning', 'Email Notification sent');
        }
       
        // Activity Log
                    $activityComment='Mr.'.get_session_value('name').' Added new driver '.$this->users->name;
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$this->users->id,
                        'action_slug'=>'driver_added',
                        'comments'=>$activityComment,
                        'others'=>'users',
                        'created_at'=>date('Y-m-d H:I:s',time()),
                    );
                    $activityID=log_activity($activityData);

        return redirect()->back();
        
    }
    // List All the drivers 
    public function drivers($type=NULL){
        $user=Auth::user();

        $where_clasue=[
            'group_id'=> config('constants.groups.driver'),
            'is_active'=> 1,
        ];
        if($type=='trashed')
        $where_clasue['is_active']=2;

        if($user->group_id!=config('constants.groups.admin'))
        $where_clasue['id']=get_session_value('id');

        
            $driversData=$this->users->with('City')->with('ZipCode')
            ->where($where_clasue)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
       
        return view('adminpanel/drivers',compact('driversData','user'));
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

                    $activityComment='Mr.'.get_session_value('name').' updated User '.$data['name'].' Record';
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$id,
                        'action_slug'=>'user_record_updated',
                        'comments'=>$activityComment,
                        'others'=>'users',
                        'created_at'=>date('Y-m-d H:I:s',time()),
                    );
                    $activityID=log_activity($activityData);
        echo json_encode($dataArray);
        die;

    }
    public function DeleteLeadssData($id){
        $dataArray['error']='No';
        $dataArray['title']='User';

        $result=$this->users->where('id','=',$id)->update(array('is_active'=>3));             
        if($result){
            $dataArray['msg']='Mr.'.get_session_value('name').', record delted successfully!';

            $activityComment='Mr.'.get_session_value('name').' moved lead to approved/pending/cancelled';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'lead_status_changed',
                'comments'=>$activityComment,
                'others'=>'users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);
        }
        
        else{
            $dataArray['error']='Yes';
            $dataArray['msg']='There is some error ! Please fill all the required fields.';

        }
        echo json_encode($dataArray);
        die;
    }
    public function ajaxcall($id, Request $req){
        $dataArray['error']='No';
        $dataArray['title']='Action Taken';
        
        if(!isset($req['action'])){
            $dataArray['error']='Yes';
            $dataArray['msg']='There is some error ! Please try again later!.';
            echo json_encode($dataArray);
            die;
        }
        if(isset($req['action']) && $req['action']=='delteFile'){ 
            $dataArray['title']='ٖFile deleted';
            $fileData=$this->files->where('id','=',$id)->get()->toArray();
            if($fileData){
                $fileData=$fileData[0];

              $filePath=public_path('uploads').'/'.$fileData['slug'];
              
                unlink($filePath);
                
           
                $file=$this->files->where('id', $id)->delete();
                $dataArray['msg']='Mr.'.get_session_value('name').', deleted  '.$fileData['name'].' successfully!';
                $activityComment=$fileData['name'].' File delted ';
                $activityData=array(
                    'user_id'=>get_session_value('id'),
                    'action_taken_on_id'=>$id,
                    'action_slug'=>'file_deleted',
                    'comments'=>$activityComment,
                    'others'=>'files',
                    'created_at'=>date('Y-m-d H:I:s',time()),
                );
                $activityID=log_activity($activityData);
                $dataArray['error']='No';
            }
            
            else{
                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
            }
            
        }
        else if(isset($req['action']) && $req['action']=='delete')
        {
            $dataArray['title']='Record Deleted';
            $result=$this->users->where('id','=',$id)->update(array('is_active'=>2));             
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', Record Deleted successfully!';
                // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'driver_deleted',
                'comments'=>'Mr.'.get_session_value('name').' deleted driver',
                'others'=>'users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            }
            
            else{
                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
            }

        }
        else if(isset($req['action']) && $req['action']=='restore')
        {
            $dataArray['title']='Record restored';
            $result=$this->users->where('id','=',$id)->update(array('is_active'=>1));             
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', Record restored successfully!';
                // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'driver_restored',
                'comments'=>'Mr.'.get_session_value('name').' restored driver',
                'others'=>'users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            }
            
            else{
                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
            }

        }
        
        echo json_encode($dataArray);
        die;
    }

 
}
