<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\adminpanel\Users;
use App\Models\adminpanel\Groups;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

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
     public function SaveCustomersData(Request $request){
       
        $validator=$request->validate([
            'firstname'=>'required',
            'lastname'=>'required',
            'email'=>'required|email|distinct|unique:users|min:5',
            'mobileno'=>'required|distinct|unique:users|min:5',
            'phone'=>'required',
            'password'=>'required',
            'business_mobile'=>'required|distinct|unique:users|min:5',
            'business_phone'=>'required',
            'business_name'=>'required',
            'business_address'=>'required',
            'subject'=>'required',
            'message'=>'required',
            'city'=>'required',
        ]);
        
        
        $this->users->name=$request['firstname'].' '.$request['lastname'];
        $this->users->firstname=$request['firstname'];
        $this->users->lastname=$request['lastname'];
        $this->users->email=$request['email'];
        $this->users->mobileno=$request['mobileno'];
        $this->users->phone=$request['phone'];
        $this->users->business_name=$request['business_name'];
        $this->users->business_address=$request['business_address'];
        $this->users->business_mobile=$request['business_mobile'];
        $this->users->business_phone=$request['business_phone'];
        $this->users->subject=$request['subject'];
        $this->users->message=$request['message'];
        $this->users->is_active=1;
        $this->users->password=Hash::make($request['password']);

        $this->users->created_at=time();
        $this->users->group_id=config('constants.groups.customer');
       
        if(isset($request['othercity']) && !empty($request['othercity']))
        $cityId = getOtherCity($request['othercity']);
        else
        $cityId=$request['city'];

        $this->users->city_id=$cityId;
  
        $request->session()->flash('alert-success', 'Customer Added! Please Check in customers list Tab');
        $this->users->save();
        
                    // Activity Log
                    $activityComment='Mr.'.get_session_value('name').' Added new customer '.$this->users->name;
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$this->users->id,
                        'action_slug'=>'customer_added',
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
        
        if($type=='pending'){
            $customersData=$this->users
            ->where('group_id', '=', config('constants.groups.customer'))
            ->where('status', '=', config('constants.lead_status.pending'))
            ->where('is_active', '=', 1)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }
        elseif($type=='approved'){
            $customersData=$this->users
            ->where('group_id', '=', config('constants.groups.customer'))
            ->where('status', '=', config('constants.lead_status.approved'))
            ->where('is_active', '=', 1)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }
        elseif($type=='cancelled'){
            $customersData=$this->users
            ->where('group_id', '=', config('constants.groups.customer'))
            ->where('status', '=', config('constants.lead_status.cancelled'))
            ->where('is_active', '=', 1)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }
        else{
            $customersData=$this->users
            ->where('group_id', '=', config('constants.groups.customer'))
            ->where('is_active', '=', 1)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }
        
        return view('adminpanel/customers',compact('customersData','user'));
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
        if(isset($req['action']) && $req['action']=='changestatus'){ 
            $dataArray['title']='Lead Status Updated ';
            $activityComment='Mr.'.get_session_value('name').' moved customer to approved/pending/cancelled';

            if(config('constants.lead_status.pending')==$req['status']){
            $dataArray['status_btn']='<a disabled="" class="btn bg-gradient-danger btn-flat btn-sm"><i class="fas fa-chart-line"></i> Pending</a>';
            $activityComment='Mr.'.get_session_value('name').' moved customer to pending';
            }
            else if(config('constants.lead_status.approved')==$req['status']){
            $dataArray['status_btn']='<a disabled="" class="btn bg-gradient-success btn-flat btn-sm"><i class="fas fa-chart-line"></i> Approved</a>';
            $activityComment='Mr.'.get_session_value('name').' moved customer to approved';
            }
            else if(config('constants.lead_status.cancelled')==$req['status']){
            $dataArray['status_btn']='<a disabled="" class="btn bg-gradient-secondary btn-flat btn-sm"><i class="fas fa-chart-line"></i> Cancelled</a>';
            $activityComment='Mr.'.get_session_value('name').' moved customer to cancelled';
            }
            $result=$this->users->where('id','=',$id)->update(array('status'=>$req['status']));             
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', customer '.$req['alertmsg'].' successfully!';
                
                $activityData=array(
                    'user_id'=>get_session_value('id'),
                    'action_taken_on_id'=>$id,
                    'action_slug'=>'customer_status_changed',
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
            
        }
        else if(isset($req['action']) && $req['action']=='trash')
        {
            $dataArray['title']='Record Trashed';
            $result=$this->users->where('id','=',$id)->update(array('is_active'=>2));             
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', Record Trashed successfully!';
                  // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'customer_trashed',
                'comments'=>'Mr.'.get_session_value('name').' moved customer to trash',
                'others'=>'users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            }
            
            else{
                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
            }
        }
        else if(isset($req['action']) && $req['action']=='delete')
        {
            $dataArray['title']='Record Deleted';
            $result=$this->users->where('id','=',$id)->update(array('is_active'=>3));             
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', Record Deleted successfully!';
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
        else if(isset($req['action']) && $req['action'] =='viewLeadData'){
            $dataArray['error']='No';
            $dataArray['msg']='Lead Successfully Updated';
            $dataArray['title']='Leads Panel';
            $leadsData=$this->users->where('id',$req['id'])->get()->toArray();
            $leadsData=$leadsData[0];
            
            $leadHTML='<div class="container">
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Name</strong>
                </div>
                <div class="col-5">
                    '.$leadsData['name'].'</div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Email</strong>
                </div>
                <div class="col-5">
                    '.$leadsData['email'].'</div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Mobile No.</strong>
                </div>
                <div class="col-5">
                    '.$leadsData['mobileno'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Phone</strong>
                </div>
                <div class="col-5">
                    '.$leadsData['phone'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5"><strong>Business Name</strong></div>
                <div class="col-5">
                    '.$leadsData['business_name'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Business Address</strong>
                </div>
                <div class="col-5">
                    '.$leadsData['business_address'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Business Phone</strong>
                </div>
                <div class="col-5">
                    '.$leadsData['business_phone'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Business Mobile.</strong>
                </div>
                <div class="col-5">
                    '.$leadsData['business_mobile'].'
                    </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Lead Subject</strong>
                </div>
                <div class="col-5">
                    '.$leadsData['subject'].'
                    </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Lead Message</strong>
                </div>
                <div class="col-5">
                    '.$leadsData['message'].'
                    </div>
                <div class="col-1">&nbsp;</div>
            </div>
        </div>';
            $dataArray['res']=$leadHTML;
        }
        else if(isset($req['action']) && $req['action'] =='SaveAddtoCustomerForm'){
            $dataArray['error']='No';
            $dataArray['msg']='customer Successfully Updated';
            $dataArray['title']='Leads Panel';
            $dataArray['actionType']='move_to_customer';
            
            $LeadData=array();
            $dataArray['firstname']=$req['firstname'];
            $dataArray['lastname']=$req['lastname'];
            $dataArray['name']=$req['firstname'].' '.$req['lastname'];
            $dataArray['mobileno']=$req['mobileno'];
            $dataArray['phone']=$req['phone'];
            $dataArray['business_name']=$req['business_name'];
            $dataArray['business_address']=$req['business_address'];
            $dataArray['business_mobile']=$req['business_mobile'];
            $dataArray['business_phone']=$req['business_phone'];
            $dataArray['id']=$req['lead_id'];
            if(isset($req['othercity']) && !empty($req['othercity']))
                $cityId = getOtherCity($req['othercity']);
            else
                $cityId=$req['city'];

            $this->users->where('id', $req['lead_id'])->update(
                array(
                    'firstname'=>$req['firstname'],
                    'lastname'=>$req['lastname'],
                    'name'=>$req['firstname'].' '.$req['lastname'],
                    'mobileno'=>$req['mobileno'],
                    'phone'=>$req['phone'],
                    'business_name'=>$req['business_name'],
                    'business_address'=>$req['business_address'],
                    'business_mobile'=>$req['business_mobile'],
                    'business_mobile'=>$req['business_mobile'],
                    'group_id'=>config('constants.groups.customer'),
                    'city_id'=>$cityId)
            );
            // Activity Logged
            $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$req['lead_id'],
                'action_slug'=>'customer_updated',
                'comments'=>'Mr.'.get_session_value('name').' updated a customer Mr.'.$req['firstname'].' '.$req['lastname'],
                'others'=>'users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            
            echo json_encode($dataArray);
            die;

        }
        else if(isset($req['action']) && $req['action'] =='SaveEditFormLead'){
            $dataArray['error']='No';
            $dataArray['msg']='Lead Successfully Updated';
            $dataArray['title']='Leads Panel';
            //$dataArray['formdata']=$req->all();

            // $this->venue_users->user_id=$req['id'];
            // $this->venue_users->venue_group_id=$req['venue_group_id'];
            // $this->venue_users->save();
    
            $this->venue_users->where('id', $req['venue_user_id'])->update(array('venue_group_id'=>$req['venue_group_id']));
            $LeadData=array();
            $dataArray['firstname']=$req['firstname'];
            $dataArray['lastname']=$req['lastname'];
            $dataArray['name']=$req['firstname'].' '.$req['lastname'];
            $dataArray['mobileno']=$req['mobileno'];
            $dataArray['phone']=$req['phone'];
            $dataArray['id']=$req['lead_id'];
            $dataArray['lead_type']=$req['lead_type'];
            
            $this->users->where('id', $req['lead_id'])->update(array(
                'firstname'=>$req['firstname'],
                'lastname'=>$req['lastname'],
                'name'=>$req['firstname'].' '.$req['lastname'],
                'mobileno'=>$req['mobileno'],
                'phone'=>$req['phone'],
                'lead_type'=>$req['lead_type'],

            ));
             // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$req['lead_id'],
                'action_slug'=>'lead_updated',
                'comments'=>'Mr.'.get_session_value('name').' updated Lead having name Mr.'.$req['firstname'].' '.$req['lastname'],
                'others'=>'users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));

            $leadType=config('constants.lead_types.'.$req['lead_type']);
            $dataArray['lead_type_tile']=$leadType['title'];
            $dataArray['venue_group_name']=$req['venue_group_name'];
            echo json_encode($dataArray);
            die;

        }
        else if(isset($req['action']) && $req['action'] =='updateLeadForm'){
            $dataArray['error']='No';
           
            $data=$this->users->where('id',$req['id'])->get()->toArray();
           $data=$data[0];
            $csrf_token = csrf_token();
            
$formHtml='<form id="EditLeadForm"
                                                                            method="GET"
                                                                            action=""
                                                                            onsubmit="return updateLead('. $data['id'].','. $req['counter'].')">
                                                                            <input type="hidden" name="_token" value="'.$csrf_token.'" />
                                                                            <input type="hidden" name="action" value="SaveEditFormLead" />
                                                                            <input type="hidden" name="lead_id" value="'.$data['id'].'" />
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" name="firstname"
                                                                                            class="form-control"
                                                                                            placeholder="Enter Name"
                                                                                            value="'. $data['firstname'].'"
                                                                                            required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" name="lastname"
                                                                                            class="form-control"
                                                                                            placeholder="Enter Name"
                                                                                            value="'. $data['lastname'].'"
                                                                                            required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input disabled readonly type="text"
                                                                                            name="email"
                                                                                            class="form-control"
                                                                                            placeholder="Enter Email"
                                                                                            value="'. $data['email'].'"
                                                                                            required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <input type="text"
                                                                                        name="mobileno"
                                                                                        class="form-control"
                                                                                        placeholder="Mobile No."
                                                                                        value="'. $data['mobileno'].'"
                                                                                        required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <input  type="text" name="phone" class="form-control" placeholder="Phone No." value="'. $data['phone'].'" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-5">&nbsp;</div>
                                                                                <div class="col-2">
                                                                                    <button type="submit"
                                                                                        class="btn btn-outline-success btn-block btn-lg"><i
                                                                                            class="fa fa-save"></i>
                                                                                        Save Changes</button>
                                                                                </div>
                                                                                <div class="col-5">&nbsp;</div>

                                                                            </div>
                                                                        </form>';
            $dataArray['formdata']=$formHtml;
        }
        else if(isset($req['action']) && $req['action'] =='editCustomerForm'){
            $dataArray['error']='No';
            
            $data=$this->users->where('id',$req['id'])->get()->toArray();
            $data=$data[0];
            $csrf_token = csrf_token();
            
$formHtml='<form id="EditCustomerForm"
                                                                            method="GET"
                                                                            action=""
                                                                            onsubmit="return updateCustomer('. $data['id'].','. $req['counter'].')">
                                                                            <input type="hidden" name="_token" value="'.$csrf_token.'" />
                                                                            <input type="hidden" name="action" value="SaveAddtoCustomerForm" />
                                                                            <input type="hidden" name="lead_id" value="'.$data['id'].'" />
                                                                                                                        
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" name="firstname"
                                                                                            class="form-control"
                                                                                            placeholder="Enter Name"
                                                                                            value="'. $data['firstname'].'"
                                                                                            required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" name="lastname"
                                                                                            class="form-control"
                                                                                            placeholder="Enter Name"
                                                                                            value="'. $data['lastname'].'"
                                                                                            required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input disabled readonly type="text"
                                                                                            name="email"
                                                                                            class="form-control"
                                                                                            placeholder="Enter Email"
                                                                                            value="'. $data['email'].'"
                                                                                            required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <input type="text"
                                                                                        name="mobileno"
                                                                                        class="form-control"
                                                                                        placeholder="Mobile No."
                                                                                        value="'. $data['mobileno'].'"
                                                                                        required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <input  type="text" name="phone" class="form-control" placeholder="Phone No." value="'. $data['phone'].'" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <input  type="text" name="business_name" class="form-control" placeholder="Business Name " value="'. $data['business_name'].'" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <input  type="text" name="business_address" class="form-control" placeholder="Business Address" value="'. $data['business_address'].'" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <input  type="text" name="business_mobile" class="form-control" placeholder="Business Mobile No." value="'. $data['business_mobile'].'" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <input  type="text" name="business_phone" class="form-control" placeholder="Business Phone No." value="'. $data['business_phone'].'" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                    <select id="city" onChange="changeCity()" name="city" class="form-control select2bs4" placeholder="Select Venue Group">'.getCitiesOptions($data['city_id']).'</select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div id="othercity"></div>
                                                                            
                                                                            <div class="row form-group">
                                                                                <div class="col-4">&nbsp;</div>
                                                                                <div class="col-4">
                                                                                    <button type="submit"
                                                                                        class="btn btn-outline-success btn-block btn-lg"><i
                                                                                            class="fa fa-save"></i> Save Changes</button>
                                                                                </div>
                                                                                <div class="col-4">&nbsp;</div>

                                                                            </div>
                                                                        </form>';
            $dataArray['formdata']=$formHtml;
        }
      
        echo json_encode($dataArray);
        die;
    }

 
}
