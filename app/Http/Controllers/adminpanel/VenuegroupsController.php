<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\adminpanel\Users;
use App\Models\adminpanel\Groups;
use App\Models\adminpanel\Venue_groups;
use App\Models\adminpanel\venue_users;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class VenuegroupsController extends Controller
{
   
    

    function __construct() {
        
        $this->users= new Users;
        $this->groups= new Groups;
        $this->venueGroup= new Venue_groups;
        $this->venue_users= new venue_users;
      }
      public function addvenuegroups(){
        $user=Auth::user(); 
        $leadsTypes=getTypesOfLeads();
        
        $VenueGroupData = $this->venueGroup->orderBy('created_at', 'desc')->with('ownerinfo')->get();
        if($VenueGroupData)
        $VenueGroupData= $VenueGroupData->toArray();
        else
        $VenueGroupData=array();
        
 
         return view('adminpanel/add_venuegroups',compact('user','VenueGroupData','leadsTypes'));
     }
     public function SavevenuegroupsData(Request $request){
       
        $validator=$request->validate([
            'firstname'=>'required',
            'lastname'=>'required',
            'email'=>'required|email|distinct|unique:users|min:5',
            'mobileno'=>'required|distinct|unique:users|min:5',
            'vg_name'=>'required',
            'hod_name'=>'required',
            'hod_phone'=>'required',
            'description'=>'required',
            'city'=>'required',
            'vg_address'=>'required',
        ]);
        
        
        $this->users->name=$request['firstname'].' '.$request['lastname'];
        $this->users->firstname=$request['firstname'];
        $this->users->lastname=$request['lastname'];
        $this->users->email=$request['email'];
        $this->users->mobileno=$request['mobileno'];
        $this->users->unitnumber=$request['unitnumber'];
        $this->users->is_active=1;
        $this->users->password=Hash::make('12345678');
        $this->users->created_at=date('Y-m-d H:I:s',time());
        $this->users->group_id=config('constants.groups.venue_group_hod');
       
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
  
        // $this->venueGroup= new Venue_groups;
        // $this->venue_users= new venue_users;
  


        $request->session()->flash('alert-success', 'venuegroup Added! Please Check in venuegroups list Tab');
        $this->users->save();
        $request['lang']='30.0358172';
        $request['lat']='72.3670309';
        $request['hod_designation']='Genral Manager';
        $this->venueGroup->name=$request['vg_name'];
        $this->venueGroup->lang=$request['lang'];
        $this->venueGroup->lat=$request['lat'];
        $this->venueGroup->address=$request['vg_address'];
        $this->venueGroup->hod_name=$request['hod_name'];
        $this->venueGroup->description=$request['description'];
        $this->venueGroup->hod_phone=$request['hod_phone'];
        $this->venueGroup->hod_designation=$request['hod_designation'];
        $this->venueGroup->is_active=1;
        $this->venueGroup->user_id=$this->users->id;
        $this->venueGroup->save();
        // Activity Log
                    $activityComment='Mr.'.get_session_value('name').' Added a new venuegroup '.$this->venueGroup->name;
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$this->users->id,
                        'action_slug'=>'venuegroup_added',
                        'comments'=>$activityComment,
                        'others'=>'users',
                        'created_at'=>date('Y-m-d H:I:s',time()),
                    );
                    $activityID=log_activity($activityData);

        return redirect()->back();
        
    }
    // List All the venuegroups 
    public function venuegroups($type=NULL){
        $user=Auth::user();
        
            $venuegroupsData=$this->users->with('City')->with('ZipCode')->with('VenueGroup')
            ->where('group_id', '=', config('constants.groups.venue_group_hod'))
            ->where('is_active', '=', 1)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
       
        return view('adminpanel/venuegroups',compact('venuegroupsData','user'));
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
            $activityComment='Mr.'.get_session_value('name').' moved venuegroup to approved/pending/cancelled';

            if(config('constants.lead_status.pending')==$req['status']){
            $dataArray['status_btn']='<a disabled="" class="btn bg-gradient-danger btn-flat btn-sm"><i class="fas fa-chart-line"></i> Pending</a>';
            $activityComment='Mr.'.get_session_value('name').' moved venuegroup to pending';
            }
            else if(config('constants.lead_status.approved')==$req['status']){
            $dataArray['status_btn']='<a disabled="" class="btn bg-gradient-success btn-flat btn-sm"><i class="fas fa-chart-line"></i> Approved</a>';
            $activityComment='Mr.'.get_session_value('name').' moved venuegroup to approved';
            }
            else if(config('constants.lead_status.cancelled')==$req['status']){
            $dataArray['status_btn']='<a disabled="" class="btn bg-gradient-secondary btn-flat btn-sm"><i class="fas fa-chart-line"></i> Cancelled</a>';
            $activityComment='Mr.'.get_session_value('name').' moved venuegroup to cancelled';
            }
            $result=$this->users->where('id','=',$id)->update(array('status'=>$req['status']));             
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', venuegroup '.$req['alertmsg'].' successfully!';
                
                $activityData=array(
                    'user_id'=>get_session_value('id'),
                    'action_taken_on_id'=>$id,
                    'action_slug'=>'venuegroup_status_changed',
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
                'action_slug'=>'venuegroup_trashed',
                'comments'=>'Mr.'.get_session_value('name').' moved venuegroup to trash',
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
                'action_slug'=>'venuegroup_deleted',
                'comments'=>'Mr.'.get_session_value('name').' deleted venuegroup',
                'others'=>'users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            }
            
            else{
                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
            }

        }
        else if(isset($req['action']) && $req['action'] =='viewVenueGroupData'){
            $dataArray['error']='No';
            $dataArray['msg']='Lead Successfully Updated';
            $dataArray['title']='Leads Panel';
           
            $data=$this->users->with('City')->with('ZipCode')->with('VenueGroup')
            ->where('group_id', '=', config('constants.groups.venue_group_hod'))
            ->where('id', '=', $req['id'])
            ->where('zipcode_id', '!=', NULL)
            ->where('city_id', '!=', NULL)
            ->orderBy('created_at', 'desc')->get()->toArray();
            $venuegroupData=$data[0];
            
            //p($venuegroupData); dd('bss');
            $leadHTML='<div class="container">
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Name</strong>
                </div>
                <div class="col-5">
                    '.$venuegroupData['name'].'</div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Email</strong>
                </div>
                <div class="col-5">
                    '.$venuegroupData['email'].'</div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Mobile No.</strong>
                </div>
                <div class="col-5">
                    '.$venuegroupData['mobileno'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Venue Group Name</strong>
                </div>
                <div class="col-5">
                    '.$venuegroupData['venue_group']['name'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Venue Group Manager Name</strong>
                </div>
                <div class="col-5">
                    '.$venuegroupData['venue_group']['hod_name'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Manager Phone</strong>
                </div>
                <div class="col-5">
                    '.$venuegroupData['venue_group']['hod_phone'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Description</strong>
                </div>
                <div class="col-5">
                    '.$venuegroupData['venue_group']['description'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>City</strong>
                </div>
                <div class="col-5">
                    '.$venuegroupData['city']['name'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Zip Code</strong>
                </div>
                <div class="col-5">
                    '.$venuegroupData['zip_code']['code'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5"><strong>Venue Group Address</strong></div>
                <div class="col-5">
                    '.$venuegroupData['venue_group']['address'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            
        </div>';
            $dataArray['res']=$leadHTML;
        }
        else if(isset($req['action']) && $req['action'] =='SaveAddtovenuegroupForm'){
            $dataArray['error']='No';
            $dataArray['msg']='venuegroup Successfully Updated';
            $dataArray['title']='Leads Panel';
            $dataArray['actionType']='move_to_venuegroup';
            //$dataArray['formdata']=$req->all();

            $dataArray['firstname']=$req['firstname'];
            $dataArray['lastname']=$req['lastname'];
            $dataArray['name']=$req['firstname'].' '.$req['lastname'];
            $dataArray['mobileno']=$req['mobileno'];
            $dataArray['hod_name']=$req['hod_name'];
            $dataArray['hod_phone']=$req['hod_phone'];
            $dataArray['gv_address']=$req['gv_address'];
            $dataArray['description']=$req['description'];
            $dataArray['gv_name']=$req['gv_name'];
            $dataArray['id']=$req['uid'];
            $dataArray['venuegroup_id']=$req['venuegroup_id'];
            $dataArray['city']=$req['cityname'];
            $dataArray['zipcode']=$req['zipcode_no'];
            // Get The City ID from table Cities
            if(isset($req['othercity']) && !empty($req['othercity'])){
                $cityId = getOtherCity($req['othercity']);
                $dataArray['city']=$req['othercity'];
            } else
                $cityId=$req['city'];

            // Get the Zipcode id from the table zipcode
            if(isset($req['otherzipcode']) && !empty($req['otherzipcode'])){
                $zipcode_id = getOtherZipCode($req['otherzipcode']);
                $dataArray['zipcode']=$req['otherzipcode'];
            }
            else
                $zipcode_id=$req['zipcode_id'];                

            $this->users->where('id', $req['uid'])->update(
                array(
                    'firstname'=>$req['firstname'],
                    'lastname'=>$req['lastname'],
                    'name'=>$req['firstname'].' '.$req['lastname'],
                    'mobileno'=>$req['mobileno'],
                    'city_id'=>$cityId,
                    'zipcode_id'=>$zipcode_id,
                    )
            );
            $this->venueGroup->where('id', $req['venuegroup_id'])->update(
                array(
                    'name'=>$req['gv_name'],
                    'address'=>$req['vg_address'],
                    'hod_name'=>$req['hod_name'],
                    'description'=>$req['description'],
                    'hod_phone'=>$req['hod_phone'],
                    )
            );
            
            // Activity Logged
            $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$req['uid'],
                'action_slug'=>'venuegroup_updated',
                'comments'=>'Mr.'.get_session_value('name').' updated a Venue Group Mr.'.$req['firstname'].' '.$req['lastname'],
                'others'=>'venuegroup',
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
           
            $data=getLeadWithVenuebyID($req['id']);
           // p($data); die;
            $csrf_token = csrf_token();
           
        
$formHtml='<form id="EditvenuegroupForm"
                                                                            method="GET"
                                                                            action=""
                                                                            onsubmit="return updatevenuegroup('. $data['id'].','. $req['counter'].')">
                                                                            <input type="hidden" name="_token" value="'.$csrf_token.'" />
                                                                            <input type="hidden" name="action" value="SaveEditFormvenuegroup" />
                                                                            <input type="hidden" name="vg_user_id" value="'.$data['id'].'" />
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
                                                                                    <select id="venue_group_id" name="venue_group_id" onchange="$(\'#venue_group_name\').val($(\'#venue_group_id option:selected\').text())" class="form-control select2bs4" placeholder="Select Venue Group">'.$venueGroupOptions.'</select>
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
        else if(isset($req['action']) && $req['action'] =='editvenuegroupForm'){
            $dataArray['error']='No';
           
            //$data=getLeadWithVenuebyID($req['id']);
            $data=$this->users->with('City')->with('ZipCode')->with('VenueGroup')
            ->where('group_id', '=', config('constants.groups.venue_group_hod'))
            ->where('id', '=', $req['id'])
            ->where('zipcode_id', '!=', NULL)
            ->where('city_id', '!=', NULL)
            ->orderBy('created_at', 'desc')->get()->toArray();
            $data=$data[0];
            //p($data); die;
            $csrf_token = csrf_token();
            
        
$formHtml='<form id="EditvenuegroupForm"
                                                                            method="GET" action="" onsubmit="return updatevenuegroup('. $data['id'].','. $req['counter'].')">
                                                                            <input type="hidden" name="_token" value="'.$csrf_token.'" />
                                                                            <input type="hidden" name="action" value="SaveAddtovenuegroupForm" />
                                                                            <input type="hidden" name="venuegroup_id" value="'.$data['venue_group']['id'].'" />
                                                                            <input type="hidden" name="uid" value="'.$data['id'].'" />
                                                                            <input type="hidden" id="cityname" name="cityname" value="'.$data['city']['name'].'" />
                                                                            <input id="zipcode_no" type="hidden" name="zipcode_no" value="'.$data['zip_code']['code'].'" />
                                                                            
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
                                                                                    <input  type="text" name="gv_name" class="form-control" placeholder="Unit Number" value="'. $data['venue_group']['name'].'" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <input  type="text" name="hod_name" class="form-control" placeholder="Group Venue Manager Name" value="'. $data['venue_group']['hod_name'].'" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <input  type="text" name="hod_phone" class="form-control" placeholder="Manager Phone" value="'. $data['venue_group']['hod_phone'].'" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <input  type="text" name="description" class="form-control" placeholder="Group Venue Description" value="'. $data['venue_group']['description'].'" required>
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
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                    <select id="zipcode_id" onChange="changezipcode()" name="zipcode_id" class="form-control select2bs4" placeholder="Select Venue Group">'.getzipcodeOptions($data['zipcode_id']).'</select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div id="otherzipcode"></div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <input  type="text" name="vg_address" class="form-control" placeholder="Venue Group Address" value="'. $data['venue_group']['address'].'" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
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
