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
     public function SavedriversData(Request $request){
       
        $validator=$request->validate([
            'firstname'=>'required',
            'lastname'=>'required',
            'email'=>'required|email|distinct|unique:users|min:5',
            'mobileno'=>'required|distinct|unique:users|min:5',
            'phone'=>'required',
            'license_no'=>'required|distinct|unique:users|min:5',
            'city'=>'required',
            'address'=>'required',
        ]);
        
        
        $this->users->name=$request['firstname'].' '.$request['lastname'];
        $this->users->firstname=$request['firstname'];
        $this->users->lastname=$request['lastname'];
        $this->users->email=$request['email'];
        $this->users->mobileno=$request['mobileno'];
        $this->users->phone=$request['phone'];
        $this->users->license_no=$request['license_no'];
        $this->users->address=$request['address'];
        $this->users->is_active=1;
        $this->users->password=Hash::make('12345678');

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
        
            $driversData=$this->users->with('City')->with('ZipCode')
            ->where('group_id', '=', config('constants.groups.driver'))
            ->where('is_active', '=', 1)
            ->where('zipcode_id', '!=', NULL)
            ->where('city_id', '!=', NULL)
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
                'action_slug'=>'driver_trashed',
                'comments'=>'Mr.'.get_session_value('name').' moved driver to trash',
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
        else if(isset($req['action']) && $req['action'] =='viewLeadData'){
            $dataArray['error']='No';
            $dataArray['msg']='Lead Successfully Updated';
            $dataArray['title']='Leads Panel';
           
            $data=$this->users->with('City')->with('ZipCode')
            ->where('group_id', '=', config('constants.groups.driver'))
            ->where('id', '=', $req['id'])
            ->where('zipcode_id', '!=', NULL)
            ->where('city_id', '!=', NULL)
            ->orderBy('created_at', 'desc')->get()->toArray();
            $driverData=$data[0];
            
            //p($driverData); dd('bss');
            $leadHTML='<div class="container">
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Name</strong>
                </div>
                <div class="col-5">
                    '.$driverData['name'].'</div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Email</strong>
                </div>
                <div class="col-5">
                    '.$driverData['email'].'</div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Mobile No.</strong>
                </div>
                <div class="col-5">
                    '.$driverData['mobileno'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Phone</strong>
                </div>
                <div class="col-5">
                    '.$driverData['phone'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5"><strong>License Number</strong></div>
                <div class="col-5">
                    '.$driverData['license_no'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Home Address</strong>
                </div>
                <div class="col-5">
                    '.$driverData['address'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>City</strong>
                </div>
                <div class="col-5">
                    '.$driverData['city']['name'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Zip Code.</strong>
                </div>
                <div class="col-5">
                    '.$driverData['zip_code']['code'].'
                    </div>
                <div class="col-1">&nbsp;</div>
            </div>
        </div>';
            $dataArray['res']=$leadHTML;
        }
        else if(isset($req['action']) && $req['action'] =='SaveAddtodriverForm'){
            $dataArray['error']='No';
            $dataArray['msg']='driver Successfully Updated';
            $dataArray['title']='Leads Panel';
            $dataArray['actionType']='move_to_driver';
            //$dataArray['formdata']=$req->all();

            $dataArray['firstname']=$req['firstname'];
            $dataArray['lastname']=$req['lastname'];
            $dataArray['name']=$req['firstname'].' '.$req['lastname'];
            $dataArray['mobileno']=$req['mobileno'];
            $dataArray['phone']=$req['phone'];
            $dataArray['address']=$req['address'];
            $dataArray['license_no']=$req['license_no'];
            $dataArray['id']=$req['driver_id'];
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

            $this->users->where('id', $req['driver_id'])->update(
                array(
                    'firstname'=>$req['firstname'],
                    'lastname'=>$req['lastname'],
                    'name'=>$req['firstname'].' '.$req['lastname'],
                    'mobileno'=>$req['mobileno'],
                    'phone'=>$req['phone'],
                    'license_no'=>$req['license_no'],
                    'address'=>$req['address'],
                    'city_id'=>$cityId,
                    'zipcode_id'=>$zipcode_id,
                    )
            );
            // Activity Logged
            $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$req['driver_id'],
                'action_slug'=>'driver_updated',
                'comments'=>'Mr.'.get_session_value('name').' updated a driver Mr.'.$req['firstname'].' '.$req['lastname'],
                'others'=>'driver',
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
           
        
$formHtml='<form id="EditdriverForm"
                                                                            method="GET"
                                                                            action=""
                                                                            onsubmit="return updatedriver('. $data['id'].','. $req['counter'].')">
                                                                            <input type="hidden" name="_token" value="'.$csrf_token.'" />
                                                                            <input type="hidden" name="action" value="SaveEditFormdriver" />
                                                                            <input type="hidden" name="driver_id" value="'.$data['id'].'" />
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                    <select name="lead_type"class="form-control select2bs4" placeholder="Select Lead Type">
                                                                                   '.$leadTypeOptions.'
                                                                                    </select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
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
        else if(isset($req['action']) && $req['action'] =='editdriverForm'){
            $dataArray['error']='No';
           
            //$data=getLeadWithVenuebyID($req['id']);
            $data=$this->users->with('City')->with('ZipCode')
            ->where('group_id', '=', config('constants.groups.driver'))
            ->where('id', '=', $req['id'])
            ->where('zipcode_id', '!=', NULL)
            ->where('city_id', '!=', NULL)
            ->orderBy('created_at', 'desc')->get()->toArray();
            $data=$data[0];
            //p($data); die;
            $csrf_token = csrf_token();
            
        
$formHtml='<form id="EditdriverForm"
                                                                            method="GET" action="" onsubmit="return updatedriver('. $data['id'].','. $req['counter'].')">
                                                                            <input type="hidden" name="_token" value="'.$csrf_token.'" />
                                                                            <input type="hidden" name="action" value="SaveAddtodriverForm" />
                                                                            <input type="hidden" name="driver_id" value="'.$data['id'].'" />
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
                                                                                    <input  type="text" name="phone" class="form-control" placeholder="Phone No." value="'. $data['phone'].'" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <input  type="text" name="license_no" class="form-control" placeholder="License Number" value="'. $data['license_no'].'" required>
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
                                                                                    <input  type="text" name="address" class="form-control" placeholder="Home Address" value="'. $data['address'].'" required>
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
