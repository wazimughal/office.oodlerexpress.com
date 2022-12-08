<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\adminpanel\Users;
use App\Models\adminpanel\Groups;
use App\Models\adminpanel\Quotes;
use App\Models\adminpanel\FilesManage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel; // To export import excel
use DateTime;

// Import/Export Excelsheet
use App\Exports\ExportDriverDeliveries;


// Used for Email Section
use App\Mail\EmailTemplate;
use Illuminate\Support\Facades\Mail;

class DriverController extends Controller
{
    

    function __construct() {
        
        $this->users= new Users;
        $this->groups= new Groups;
        $this->files= new FilesManage;
        $this->quotes= new Quotes;
      }
      public function adddrivers(){
        
        $user=Auth::user(); 
       
         return view('adminpanel.add_drivers',compact('user'));
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
                'state'=>'required',
                'address'=>'required',
            ]);

            $dataArray['firstname']=$req['firstname'];
            $dataArray['lastname']=$req['lastname'];
            $dataArray['name']=$req['firstname'].' '.$req['lastname'];
            $dataArray['mobileno']=$req['phone'];
            $dataArray['address']=$req['address'];
            $dataArray['city']=$req['city'];
            $dataArray['state']=$req['state'];

            if(isset($req['license_no']) && !empty($req['license_no']))
            $dataArray['license_no']=$req['license_no'];

            if(isset($req['password']) && $req['password']!='')
            $dataArray['password']=Hash::make($req['password']);
            
            $dataArray['city']=$req['city'];
            $dataArray['state']=$req['state'];                

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
        $userData=$this->users->where('id',$id)->with('files','driver_documents')->with('getGroups')->get()->toArray();
       
         return view('adminpanel/uploadform',get_defined_vars());
         return view('adminpanel/add_driver_documents',get_defined_vars());
     }
    public function upload_documents($id,Request $request){
        $user=Auth::user();
            $image = $request->file('file');
            $imageExt=$image->extension();
            $imageName = time().'.'.$imageExt;

     
             //$uploadingPath=public_path('uploads');
        $uploadingPath=base_path().'/public/uploads';
        if(base_path()!='/Users/waximarshad/office.oodlerexpress.com')
        $uploadingPath=base_path().'/public_html/uploads';


            $image->move(($uploadingPath),$imageName);
            $orginalImageName=$image->getClientOriginalName();
        
        //return response()->json(['success'=>$imageName]);

            $this->files->name=$orginalImageName;
            $this->files->slug=phpslug('driver_documents');
            $this->files->file_section=phpslug('driver_documents');
            $this->files->path=url('uploads').'/'.$imageName;
            $this->files->description=$imageName;
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
        $this->users->mobileno=$request['phone'];
        
        
        $this->users->license_no=$request['license_no'];
        $this->users->address=$request['address'];
        $this->users->is_active=1;
        $this->users->password=Hash::make($request['password']);

        $this->users->created_at=time();
        $this->users->group_id=config('constants.groups.driver');
       
     
        $this->users->city=$request['city'];
        //$this->users->zipcode_id=$request['zipcode'];
        $this->users->state=$request['state'];
  
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
    public function report_drivers(Request $req){
        $user=Auth::user();

        $where_in_clause=$customer_ids=$driver_ids=$quote_status=array();

        $where_clasue=[
            'group_id'=> config('constants.groups.driver'),
            'is_active'=> 1,
        ];
        
        if(isset($req->driver_id) && !empty($req->driver_id)){
            $driver_ids=$req->driver_id;
            $where_in_clause['driver_id']=$driver_ids;
            }
        // if($user->group_id!=config('constants.groups.admin'))
        // $where_clasue['id']=get_session_value('id');

        
            $driversData=$this->users->with('City')->with('ZipCode')
            ->where($where_clasue)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
       
        return view('adminpanel.reports.drivers',get_defined_vars());
    }
    public function report_driver_working_hours(Request $req){
        $user=Auth::user();

        $where_in_clause=$customer_ids=$driver_ids=$quote_status=array();

        $where_clasue=[
            'group_id'=> config('constants.groups.driver'),
            'is_active'=> 1,
        ];
        
        if(isset($req->driver_id) && !empty($req->driver_id)){
            $driver_ids=$req->driver_id;
            $where_in_clause['driver_id']=$driver_ids;
            }
            $driversData=$this->users->with('City')->with('ZipCode')
            ->where($where_clasue)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            
            if(isset($req->action) && $req->action=='download_working_hours'){ // Download Excelsheed

                $where_clause=[
                    ['status','=',config('constants.quote_status.complete')],
                    ['is_active','=',1],
                    ['driver_id','=',$req->driver_id],
                ];
             
               
                $quotes=$this->quotes
                        ->with(array('driver','customer'))
                        ->where($where_clause)
                        ->orderBy('created_at', 'desc');
                if(
                    isset($req->from_date) &&
                    !empty($req->from_date) &&
                    isset($req->to_date) &&
                    !empty($req->to_date)
                 )   
                {
                 $from=($req->from_date);
                 $to=($req->to_date);
                $quotes=$quotes->WhereBetween('drop_off_date', [$from, $to]);
                 }
                 //p($req->all());
                 //echo $quotesData=$quotes->toSql(); 
                 $quotesData=$quotes->get()->toArray();
                //  p($where_clause);
                //  p($quotesData); die;
                $exportData=array();
                $total_hours=$total_mins=0;
                foreach($quotesData as $key=>$data){

                    $elapsed_time=elapsed_time($data['reached_at_pickup'],$data['delivered']);

                    $exportData[]=[
                        'id'=>$data['id'],
                        'po_number'=>$data['po_number'],
                        // 'quote_type'=>$data['quote_type'],
                        // 'business_type'=>$data['business_type'],
                        // 'pickup_street_address'=>$data['pickup_street_address'],
                        // 'pickup_unit'=>$data['pickup_unit'],
                        // 'pickup_state'=>$data['pickup_state'],
                        // 'pickup_city'=>$data['pickup_city'],
                        // 'pickup_zipcode'=>$data['pickup_zipcode'],
                        // 'pickup_contact_number'=>$data['pickup_contact_number'],
                        // 'pickup_email'=>$data['pickup_email'],
                        // 'pickup_date'=>$data['pickup_date'],
                        // 'drop_off_street_address'=>$data['drop_off_street_address'],
                        // 'drop_off_unit'=>$data['drop_off_unit'],
                        // 'drop_off_city'=>$data['drop_off_city'],
                        // 'drop_off_zipcode'=>$data['drop_off_zipcode'],
                        // 'drop_off_contact_number'=>$data['drop_off_contact_number'],
                        // 'drop_off_email'=>$data['drop_off_email'],
                        'drop_off_date'=>$data['drop_off_date'],
                        // 'drop_off_instructions'=>$data['drop_off_instructions'],
                        // 'status'=>quote_status_msg($data['status']),
                        // 'customer_name'=>$data['customer']['name'],
                        // 'customer_email'=>$data['customer']['email'],
                        // 'customer_mobileno'=>$data['customer']['mobileno'],
                        // 'customer_business_name'=>$data['customer']['business_name'],
                        'driver_name'=>(isset($data['driver']['name']) && !empty($data['driver']['name']))?$data['driver']['name']:'',
                        // 'driver_email'=>(isset($data['driver']['email']) && !empty($data['driver']['email']))?$data['driver']['email']:'',
                        // 'driver_mobileno'=>(isset($data['driver']['mobileno']) && !empty($data['driver']['mobileno']))?$data['driver']['mobileno']:'',
                        // 'driver_license_no'=>(isset($data['driver']['license_no']) && !empty($data['driver']['license_no']))?$data['driver']['license_no']:'',
                        'Working hours'=>$elapsed_time['hours'].' hours,'.$elapsed_time['mins'].' mins,'.$elapsed_time['seconds'].' seconds,',
                        
                    ];
                    $total_hours=$total_hours+$elapsed_time['hours'];
                    $total_mins=$total_mins+$elapsed_time['mins'];
                }
                $hours=(int)($total_mins/60);
                $total_hours=$total_hours+$hours;
                $total_mins=($total_mins%60);
                $exportData[]=[
                    'id'=>'',
                    'po_number'=>'',
                    // 'quote_type'=>'',
                    // 'business_type'=>'',
                    // 'pickup_street_address'=>'',
                    // 'pickup_unit'=>'',
                    // 'pickup_state'=>'',
                    // 'pickup_city'=>'',
                    // 'pickup_zipcode'=>'',
                    // 'pickup_contact_number'=>'',
                    // 'pickup_email'=>'',
                    // 'pickup_date'=>'',
                    // 'drop_off_street_address'=>'',
                    // 'drop_off_unit'=>'',
                    // 'drop_off_city'=>'',
                    // 'drop_off_zipcode'=>'',
                    // 'drop_off_contact_number'=>'',
                    // 'drop_off_email'=>'',
                    'drop_off_date'=>'',
                    // 'drop_off_instructions'=>'',
                    // 'status'=>'',
                    // 'customer_name'=>'',
                    // 'customer_email'=>'',
                    // 'customer_mobileno'=>'',
                    // 'customer_business_name'=>'',
                    'driver_name'=>'TOTAL TIME',
                    // 'driver_email'=>'',
                    // 'driver_mobileno'=>'',
                    //'driver_license_no'=>'TOTAL TIME',
                    'Working hours'=>$total_hours.' Hours and '.$total_mins.' mins'
                    
                ];

                return Excel::download(new ExportDriverDeliveries($exportData), 'driver-working-hours-for-delivery.xlsx');

            }
       
        return view('adminpanel.reports.drivers',get_defined_vars());
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

                $uploadingPath=base_path().'/public/uploads';
                if(base_path()!='/Users/waximarshad/office.oodlerexpress.com')
                $uploadingPath=base_path().'/public_html/uploads';
        
                      $filePath=$uploadingPath.'/'.$fileData['description'];
                      
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
        elseif(isset($req['action']) && $req['action']=='qsearch_driver')
        {
            $user=Auth::user();
            $dataArray['title']='Search Result';
            $where_clause=[
                ['group_id', '=', config('constants.groups.driver')],
                ['is_active', '=', 1],
            ];
           
            $query=$this->users
                ->where($where_clause)
                
                ->orderBy('created_at', 'desc');

                $search_val=$req->qsearch;
                $leads=$query->where(function($query) use ($search_val){
                    $query->orwhere('name', 'like', '%' . $search_val . '%')
                        ->orwhere('email', 'like', '%' . $search_val . '%')
                        ->orwhere('license_no', 'like', '%' . $search_val . '%');

                });
                //$dataArray['sql']=$leads->toSql();
                $driverData=$leads->get()->toArray();
                // p($driverData);
                // die;
                //$response='<table id="example1" class="table table-bordered table-striped">
                $response= ' <thead>
                                        <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>License No.</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                                        
                                            $counter = 1;
                                            
                                            $driver_id_array=array();
                                            foreach ($driverData as $data){
                                                $driver_id_array[]=$data['id'];
                                            
                                                $response .='<tr id="row_'.$data['id'].'">
                                                <td><strong id="name_'.$data['id'].'">'.$data['name'].'</strong>
                                                </td>
                                                <td id="email_'.$data['id'].'">'.$data['email'].'</td>
                                                <td id="mobileno_'.$data['id'].'">
                                                    '.$data['mobileno'].'</td>
                                                <td id="address_'.$data['id'].'">
                                                    '.$data['address'].'</td>
                                                <td id="license_no_'.$data['id'].'">
                                                '.$data['license_no'].'
                                                </td>
                                                <td id="row_from_date_'.$data['id'].'">
                                                <div class="input-group date" id="from_date_'.$data['id'].'" data-target-input="nearest">
                                                    <input id="input_from_date_'.$data['id'].'"  type="text"  name="from_date" placeholder="From date" class="form-control datetimepicker-input" data-target="#from_date_'.$data['id'].'"/>
                                                    <div class="input-group-append" data-target="#from_date_'.$data['id'].'" data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>  
                                            </td>
                                            <td id="row_to_date_'.$data['id'].'">
                                                <div class="input-group date" id="to_date_'.$data['id'].'" data-target-input="nearest">
                                                    <input id="input_to_date_'.$data['id'].'" type="text"  name="to_date" placeholder="To Date" class="form-control datetimepicker-input" data-target="#to_date_'.$data['id'].'"/>
                                                    <div class="input-group-append" data-target="#to_date_'.$data['id'].'" data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div> 
                                            </td>
                                                <td>';
                                                
                                                    
                                                        $response .='<a href="'.route('admin.customerseditform', $data['id']).'"
                                                        class="btn btn-info btn-block btn-sm"><i class="fas fa-edit"></i>
                                                        Edit</a>';
                                                    
                                                        
                                                $response .='</td>
    
                                                </td>
    
                                            </tr>';
                                        
                                                $counter ++;
                                        }
                                        
                                        $response .='</tbody>
                                    <tfoot>
                                        <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>License No.</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Action</th>
                                        </tr>
                                        
                                    </tfoot>';

                                $dataArray['drivers_id']= implode(',',$driver_id_array)  ;
                                $dataArray['response']=  $response;
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
