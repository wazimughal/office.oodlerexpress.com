<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\adminpanel\Users;
use App\Models\adminpanel\Groups;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

use App\Mail\EmailTemplate;
use Illuminate\Support\Facades\Mail;

class CustomersController extends Controller
{

    function __construct() {
        
        $this->users= new Users;
        $this->groups= new Groups;
      }
      public function addcustomers(){
        $user=Auth::user(); 
       
         return view('adminpanel/add_customers',compact('user'));
     }
     public function save_new_customer(Request $request){
       
        $validator=$request->validate([
            'firstname'=>'required',
            'lastname'=>'required',
            'email'=>'required|email|distinct|unique:users|min:5',
            'mobileno'=>'required',
            'business_name'=>'required',
            'business_phone'=>'required',
            'business_address'=>'required',
            'business_email'=>'required',
            
        ]);
        
        // User Information
        $this->users->name=$request['firstname'].' '.$request['lastname'];
        $this->users->firstname=$request['firstname'];
        $this->users->lastname=$request['lastname'];
        $this->users->email=$request['email'];
        $this->users->mobileno=$request['mobileno'];
        $this->users->designation=$request['designation'];
        $this->users->password=Hash::make($request['password']);

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
        $this->users->group_id=config('constants.groups.customer');
        
        if(isset($request['othercity']) && !empty($request['othercity']))
        $cityId = getOtherCity($request['othercity']);
        else
        $cityId=$request['city'];

        if(isset($request['otherstate']) && !empty($request['otherstate']))
        $state_id = getOtherstate($request['otherstate']);
        else
        $state_id=$request['state_id'];
        $this->users->state_id=$state_id;

        if(isset($request['otherzipcode']) && !empty($request['otherzipcode']))
        $zipcode = getOtherZipCode($request['otherzipcode']);
        else
        $zipcode=$request['zipcode_id'];
        $this->users->zipcode_id=$zipcode;

        $this->users->business_address=$request['business_address'];

        if(isset($request['othershipping']) && !empty($request['othershipping']))
        $shipping_cat = getOtherCategory($request['othershipping']);
        else
        $shipping_cat=$request['shipping_cat'];
        

        $this->users->shipping_cat=$shipping_cat;


        $mailData['body_message']='Welcome To Oodler Express . You are added as Customer in Oodler Express, Please login to our CRM using email '.$request['email'].' and the password <strong>'.$request['password'].'</strong>';
        $mailData['subject']='Welcom to Oodler Express (New Customer added)';
        $toEmail=[
            $request['email']
        ];
        if(Mail::to($toEmail)->send(new EmailTemplate($mailData)))
        $request->session()->flash('alert-info', 'Email Notification also sent  ');

        $request->session()->flash('alert-success', 'Customer added! Please Check in Customers Tab');
        $this->users->save();
                    // Activity Log
                    $activityComment='Mr.'.get_session_value('name').' Added new customer '.$this->users->name;
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$this->users->id,
                        'action_slug'=>'new_customer_added',
                        'comments'=>$activityComment,
                        'others'=>'users',
                        'created_at'=>date('Y-m-d H:I:s',time()),
                    );
                    $activityID=log_activity($activityData);

        return redirect()->back();
        
    }
    // List All the customers 
    public function customers($type=NULL){
        
        $user=Auth::user();
        if($type=='trash' && $user->group_id==config('constants.groups.admin')){
            $customersData=$this->users
            ->where('group_id', '=', config('constants.groups.customer'))
            ->where('is_active', '=', 2)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }
        else if($user->group_id==config('constants.groups.admin')){
            $customersData=$this->users
            ->where('group_id', '=', config('constants.groups.customer'))
            ->where('is_active', '=', 1)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));   
        }
        else{
            $customersData=$this->users
            ->where('group_id', '=', config('constants.groups.customer'))
            ->where('is_active', '=', 1)
            ->where('id',get_session_value('id'))
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }
            
        
            return view('adminpanel/customers',get_defined_vars());
    }
    public function editcustomer($id, Request $req){
        $user=Auth::user(); 
        if($user->group_id!=config('constants.groups.admin'))
        $id=$user->id;

        $userData=$this->users->where('id',$id)->get()->toarray();
        $userData=$userData[0];

        return view('adminpanel/edit_customer',get_defined_vars());
    }
    public function save_edit_customer($id,Request $request){
        
        $user=Auth::user(); 
        if($user->group_id!=config('constants.groups.admin'))
        $id=$user->id;

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
        ]);
        
        // User Information
        $data_to_update['name']=$this->users->name=$request['firstname'].' '.$request['lastname'];
        $data_to_update['firstname']=$this->users->firstname=$request['firstname'];
        $data_to_update['lastname']=$this->users->lastname=$request['lastname'];
        $data_to_update['mobileno']=$this->users->mobileno=$request['mobileno'];
        $data_to_update['designation']=$this->users->designation=$request['designation'];
        
        if(isset($request['password']) && !empty($request['password']))
        $data_to_update['password']=$this->users->password=Hash::make($request['password']);

        $data_to_update['group_id']=$this->users->group_id=config('constants.groups.customer');
       // Business Information 
       $data_to_update['business_name']=$this->users->business_name=$request['business_name'];
       $data_to_update['business_email']=$this->users->business_email=$request['business_email'];
       $data_to_update['business_phone']=$this->users->business_phone=$request['business_phone'];
       $data_to_update['years_in_business']=$this->users->years_in_business=$request['years_in_business'];
       $data_to_update['street']=$this->users->street=$request['street'];
       $data_to_update['how_often_shipping']=$this->users->how_often_shipping=$request['how_often_shipping']; //
       
        
        
        if(isset($request['othercity']) && !empty($request['othercity']))
        $data_to_update['city_id']=$cityId = getOtherCity($request['othercity']);
        else
        $data_to_update['city_id']=$cityId=$request['city'];

        if(isset($request['otherstate']) && !empty($request['otherstate']))
        $state_id = getOtherstate($request['otherstate']);
        else
        $state_id=$request['state_id'];
        $data_to_update['state_id']=$this->users->state_id=$state_id;

        if(isset($request['otherzipcode']) && !empty($request['otherzipcode']))
        $zipcode = getOtherZipCode($request['otherzipcode']);
        else
        $zipcode=$request['zipcode_id'];
        $data_to_update['zipcode_id']= $this->users->zipcode_id=$zipcode;

        $data_to_update['business_address']=$this->users->business_address=$request['business_address'];

        if(isset($request['othershipping']) && !empty($request['othershipping']))
        $shipping_cat = getOtherCategory($request['othershipping']);
        else
        $shipping_cat=$request['shipping_cat'];
        

        $data_to_update['shipping_cat']=$this->users->shipping_cat=$shipping_cat;
        // Change Password
        if(isset($request['password']) && !empty($request['password'])){
            $mailData['body_message']='Your Oodler Password is changed and now you can login to oodler CRM using password <strong>'.$request['password'].'</strong> and your email '.$customerData['email'].' as user name.';
            $mailData['subject']='Customer Password Changed on Oodler Express';
             $toEmail=[
                $customerData['email']
             ];
            if(Mail::to($toEmail)->send(new EmailTemplate($mailData)))
             $request->session()->flash('alert-info', 'Email Notification also sent to customer with password  ');
    
        }
        
        $request->session()->flash('alert-success', 'Customer data updated scucessfully ');
        
        $this->users->where('id',$id)->update($data_to_update);
                    // Activity Log
                    $activityComment='Mr.'.get_session_value('name').' added new customer '.$this->users->name. 'from the leads' ;
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$id,
                        'action_slug'=>'customer_updated',
                        'comments'=>$activityComment,
                        'others'=>'users',
                        'created_at'=>date('Y-m-d H:I:s',time()),
                    );
                    $activityID=log_activity($activityData);

        return redirect()->back();
        
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
            $dataArray['title']='Customer Deleted';
            $result=$this->users->where('id','=',$id)->update(array('is_active'=>2));             
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', Customer Deleted successfully!';
                // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'customer_deleted',
                'comments'=>'Mr.'.get_session_value('name').' deleted customer',
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
            $dataArray['title']='Customer Restored';
            $result=$this->users->where('id','=',$id)->update(array('is_active'=>1));             
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', Customer restored successfully!';
                // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'customer_restored',
                'comments'=>'Mr.'.get_session_value('name').' restored customer',
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
