<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\adminpanel\Users;
use App\Models\adminpanel\Groups;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

use App\Mail\EmailTemplate;
use Illuminate\Support\Facades\Mail;

class LeadsController extends Controller
{

    function __construct() {
        
        $this->users= new Users;
        $this->groups= new Groups;
      }

    public function addLeads(){
       $user=Auth::user(); 
        return view('adminpanel/add_leads',compact('user'));
    }
    public function save_new_lead(Request $request){
       
        $validator=$request->validate([
            'firstname'=>'required',
            'lastname'=>'required',
            'email'=>'required|email|distinct|unique:users|min:5',
            'mobileno'=>'required',
            'business_name'=>'required',
            'business_phone'=>'required',
            'business_address'=>'required',
            'business_email'=>'required',
            'shipping_cat'=>'required',
            
        ]);
        
        // User Information
        $this->users->name=$request['firstname'].' '.$request['lastname'];
        $this->users->firstname=$request['firstname'];
        $this->users->lastname=$request['lastname'];
        $this->users->email=$request['email'];
        $this->users->mobileno=$request['mobileno'];
        $this->users->designation=$request['designation'];
        $this->users->password=Hash::make(1234);

       // Business Information 
        $this->users->business_name=$request['business_name'];
        $this->users->business_email=$request['business_email'];
        $this->users->business_phone=$request['business_phone'];
        $this->users->years_in_business=$request['years_in_business'];
        $this->users->business_address=$request['business_address'];
        $this->users->street=$request['street'];
        $this->users->how_often_shipping=$request['how_often_shipping']; //
        $this->users->is_active=1;
        $this->users->created_at=time();
        $this->users->group_id=config('constants.groups.subscriber');

        $this->users->city=$this->users->city=$request['city'];
        $this->users->state=$this->users->state=$request['state'];
        $this->users->zipcode= $this->users->zipcode=$request['zipcode'];
        $this->users->shipping_cat=json_encode($request['shipping_cat']);
        $this->users->business_address=$request['business_address'];

       

        $mailData['body_message']='Welcome To Oodler Express . A lead have been genrated by Oodler Express, Contact us for User Name and Password';
        $mailData['subject']='Welcom to Oodler Express (New Lead added)';
        $toEmail=[
            config('constants.admin_email'),
            $request['email']
        ];
        if(Mail::to($toEmail)->send(new EmailTemplate($mailData)))
        $request->session()->flash('alert-info', 'Email Notification also sent  ');

        $request->session()->flash('alert-success', 'Lead Added! Please Check in Leads Tab');
        $this->users->save();
                    // Activity Log
                    $activityComment='Mr.'.get_session_value('name').' Added new Lead '.$this->users->name;
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$this->users->id,
                        'action_slug'=>'new_lead_added',
                        'comments'=>$activityComment,
                        'others'=>'users',
                        'created_at'=>date('Y-m-d H:I:s',time()),
                    );
                    $activityID=log_activity($activityData);

        return redirect()->back();
        
    }
    public function editLeads($id, Request $req){
        $user=Auth::user(); 

        $userData=$this->users->where('id',$id)->get()->toarray();
        $userData=$userData[0];

        return view('adminpanel/edit_leads',get_defined_vars());
    }
    
    public function save_editLeads($id,Request $request){
    //     p($request->all());
    //    dd('dfsaf');
        $validator=$request->validate([
            'firstname'=>'required',
            'lastname'=>'required',
            'mobileno'=>'required',
            'business_name'=>'required',
            'business_phone'=>'required',
            'business_address'=>'required',
            'business_email'=>'required',
            'shipping_cat'=>'required',

        ]);
        
        // User Information
        $data_to_update['name']=$this->users->name=$request['firstname'].' '.$request['lastname'];
        $data_to_update['firstname']=$this->users->firstname=$request['firstname'];
        $data_to_update['lastname']=$this->users->lastname=$request['lastname'];
        //$data_to_update['email']=$this->users->email=$request['email'];
        $data_to_update['mobileno']=$this->users->mobileno=$request['mobileno'];
        $data_to_update['designation']=$this->users->designation=$request['designation'];
        
       // Business Information 
       $data_to_update['business_name']=$this->users->business_name=$request['business_name'];
       $data_to_update['business_email']=$this->users->business_email=$request['business_email'];
       $data_to_update['business_phone']=$this->users->business_phone=$request['business_phone'];
       $data_to_update['years_in_business']=$this->users->years_in_business=$request['years_in_business'];
       $data_to_update['street']=$this->users->street=$request['street'];
       $data_to_update['how_often_shipping']=$this->users->how_often_shipping=$request['how_often_shipping']; //
       $data_to_update['city']=$this->users->city=$request['city'];
       $data_to_update['state']=$this->users->state=$request['state'];
       $data_to_update['zipcode']= $this->users->zipcode=$request['zipcode'];
       $data_to_update['business_address']=$this->users->business_address=$request['business_address'];
       $data_to_update['shipping_cat']=$this->users->shipping_cat=json_encode($request['shipping_cat']);

        $mailData['body_message']='Welcome To Oodler Express . A lead have been genrated by Oodler Express, Contact us for User Name and Password';
        $mailData['subject']='Welcom to Oodler Express (New Lead added)';
        // if(Mail::to("waximarshad@outlook.com")->send(new EmailTemplate($mailData)))
        // $request->session()->flash('alert-info', 'Email Notification also sent  ');

        $request->session()->flash('alert-success', 'Lead update! Please Check in Leads Tab');
        
        $this->users->where('id',$id)->update($data_to_update);
                    // Activity Log
                    $activityComment='Mr.'.get_session_value('name').' updated Lead '.$this->users->name;
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$id,
                        'action_slug'=>'lead_updated',
                        'comments'=>$activityComment,
                        'others'=>'users',
                        'created_at'=>date('Y-m-d H:I:s',time()),
                    );
                    $activityID=log_activity($activityData);

        return redirect()->back();
        
    }
    public function add_to_customer($id, Request $req){
        $user=Auth::user(); 
        if($user->group_id!=config('constants.groups.admin'))
        return false;

        $userData=$this->users->where('id',$id)->get()->toarray();
        $userData=$userData[0];

        return view('adminpanel/add_to_customer_leads',get_defined_vars());
    }
    
    public function save_add_to_customer($id,Request $request){
    
        $customerData=$this->users->where('id',$id)->get('email')->toArray();
        $customerData=$customerData[0];
        $validator=$request->validate([
            'firstname'=>'required',
            'lastname'=>'required',
            'mobileno'=>'required',
            'business_name'=>'required',
            'business_phone'=>'required',
            'business_address'=>'required',
            'business_email'=>'required',
            'password'=>'required',
            
        ]);
        
        // User Information
        $data_to_update['name']=$this->users->name=$request['firstname'].' '.$request['lastname'];
        $data_to_update['firstname']=$this->users->firstname=$request['firstname'];
        $data_to_update['lastname']=$this->users->lastname=$request['lastname'];
        $data_to_update['mobileno']=$this->users->mobileno=$request['mobileno'];
        $data_to_update['designation']=$this->users->designation=$request['designation'];
        $data_to_update['password']=$this->users->password=Hash::make($request['password']);
        $data_to_update['group_id']=$this->users->group_id=config('constants.groups.customer');
       // Business Information 
       $data_to_update['business_name']=$this->users->business_name=$request['business_name'];
       $data_to_update['business_email']=$this->users->business_email=$request['business_email'];
       $data_to_update['business_phone']=$this->users->business_phone=$request['business_phone'];
       $data_to_update['years_in_business']=$this->users->years_in_business=$request['years_in_business'];
       $data_to_update['street']=$this->users->street=$request['street'];
       $data_to_update['how_often_shipping']=$this->users->how_often_shipping=$request['how_often_shipping']; //
       
        
        
       $data_to_update['city']=$this->users->city=$request['city'];
       $data_to_update['state']=$this->users->state=$request['state'];
       $data_to_update['zipcode']= $this->users->zipcode=$request['zipcode'];
       $data_to_update['business_address']=$this->users->business_address=$request['business_address'];
       $data_to_update['shipping_cat']=$this->users->shipping_cat=json_encode($request['shipping_cat']);

        $mailData['body_message']='Welcome to Oodler Express. We are happy to serve your business!. Please note that you have been assigned access to our Online Portal. Use your email '.$customerData['email'].' as your username and the following password <strong>'.$request['password'].'</strong>';
        $mailData['subject']='New Account Details - Oodler Express';
         $toEmail=[
            $customerData['email']
         ];
        if(Mail::to($toEmail)->send(new EmailTemplate($mailData)))
         $request->session()->flash('alert-info', 'Email Notification also sent to customer with password  ');

        $request->session()->flash('alert-success', 'A new customer is added in the system');
        
        $this->users->where('id',$id)->update($data_to_update);
                    // Activity Log
                    $activityComment='Mr.'.get_session_value('name').' added new customer '.$this->users->name. 'from the leads' ;
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$id,
                        'action_slug'=>'add_to_customer',
                        'comments'=>$activityComment,
                        'others'=>'users',
                        'created_at'=>date('Y-m-d H:I:s',time()),
                    );
                    $activityID=log_activity($activityData);

        return redirect()->back();
        
    }
    

    // List All the Leads 
    public function leads($type=NULL){
        $user=Auth::user();
        if($user->group_id==config('constants.groups.admin')){
            if($type=='office'){
                $leadsData=$this->users
                ->where('group_id', '=', config('constants.groups.subscriber'))
                ->where('lead_by', '=', 0) // 0 is for office
                ->where('is_active', '=', 1)
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            }
            elseif($type=='web'){
                $leadsData=$this->users
                ->where('group_id', '=', config('constants.groups.subscriber'))
                ->where('lead_by', '=', 1) // 1 is for web
                ->where('is_active', '=', 1)
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            }
            elseif($type=='trashed'){
                $leadsData=$this->users
                ->where('group_id', '=', config('constants.groups.subscriber'))
                ->where('is_active', '=', 2)
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            }
            else{
                $leadsData=$this->users
                ->where('group_id', '=', config('constants.groups.subscriber'))
                ->where('is_active', '=', 1)
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            }

        }else{ // This section is for the user


            if($type=='office'){
                $leadsData=$this->users
                ->where('group_id', '=', config('constants.groups.subscriber'))
                ->where('lead_by', '=', 0) // 0 is for office
                ->where('id', get_session_value('id'))
                ->where('is_active', '=', 1)
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            }
            elseif($type=='web'){
                $leadsData=$this->users
                ->where('group_id', '=', config('constants.groups.subscriber'))
                ->where('lead_by', '=', 1) // 1 is for web
                ->where('is_active', '=', 1)
                ->where('id', get_session_value('id'))
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            }
            elseif($type=='trashed'){
                $leadsData=$this->users
                ->where('group_id', '=', config('constants.groups.subscriber'))
                ->where('is_active', '=', 2)
                ->where('id', get_session_value('id'))
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            }
            else{
                $leadsData=$this->users
                ->where('group_id', '=', config('constants.groups.subscriber'))
                ->where('is_active', '=', 1)
                ->where('id', get_session_value('id'))
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            }
        }
        
        return view('adminpanel/leads',compact('leadsData','user'));
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
       if(isset($req['action']) && $req['action']=='delete')
        {
            $dataArray['title']='Record Deleted';
            $dataArray['id']=$id;
            $result=$this->users->where('id','=',$id)->update(array('is_active'=>2));             
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', Record Deleted successfully!';
                  // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'lead_deleted',
                'comments'=>'Mr.'.get_session_value('name').' moved record to trash',
                'others'=>'users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            }
            
            else{
                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
            }
        }
       elseif(isset($req['action']) && $req['action']=='restore')
        {
            $dataArray['title']='Record Restored';
            $dataArray['id']=$id;
            $result=$this->users->where('id','=',$id)->update(array('is_active'=>1));             
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', Record Restored successfully!';
                  // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'lead_restored',
                'comments'=>'Mr.'.get_session_value('name').' moved record to trash',
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
