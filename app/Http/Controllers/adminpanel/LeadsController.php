<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\adminpanel\Users;
use App\Models\adminpanel\Groups;
use App\Models\adminpanel\comments;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

use App\Mail\EmailTemplate;
use Illuminate\Support\Facades\Mail;

// This is for QuickBooks Customer
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Customer;

class LeadsController extends Controller
{

    function __construct() {
        
        $this->users= new Users;
        $this->groups= new Groups;
        $this->comments= new comments;
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
            //'shipping_cat'=>'required',
            
        ]);
        
        // User Information
        $this->users->name=$request['firstname'].' '.$request['lastname'];
        $this->users->firstname=$request['firstname'];
        $this->users->lastname=$request['lastname'];
        $this->users->email=$request['email'];
        $this->users->mobileno=$request['mobileno'];
        $this->users->designation=$request['designation'];
        //$this->users->password=Hash::make(1234);

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
        //$this->users->shipping_cat=json_encode($request['shipping_cat']);
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
    public function view_leads($id){
        $user=Auth::user(); 

        $userData=$this->users->where('id',$id)->with('lead_comments')->get()->toarray();
        $userData=$userData[0];

        return view('adminpanel.view_lead',get_defined_vars());
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
            //'shipping_cat'=>'required',

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
       //$data_to_update['shipping_cat']=$this->users->shipping_cat=json_encode($request['shipping_cat']);

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
    public function createCustomer($data=[])
    {
        if(empty($data))
        return false;

        $config = config('quickbooks');
        
        $dataService = DataService::Configure([
            'auth_mode' => 'oauth2',
            'ClientID' => $config['client_id'],
            'ClientSecret' => $config['client_secret'],
            'RedirectURI' => $config['redirect_uri'],
            'accessTokenKey' => $config['access_token'],
            'refreshTokenKey' => $config['refresh_token'],
            'QBORealmID' => $config['realm_id'],
            'baseUrl' => $config['base_url']
        ]);
      
        $dataService->throwExceptionOnError(true);
        // Create a new customer object
        $customer = Customer::create($data);
        // Persist the customer to QuickBooks
        $result = $dataService->Add($customer);
        $error = $dataService->getLastError();

            if ($error) 
            return false;
       
            return $result->Id;
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
            'shipping_cat'=>'required',
            
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
        
        

       $customer= [
                "GivenName" => $data_to_update['name'],
                "ContactName" => $data_to_update['name'],
                "FamilyName" => $data_to_update['name'],
                "DisplayName" =>  $data_to_update['name'].'-'.$id,
                "Organization" =>  $data_to_update['business_name'],
                "CompanyName" =>  $data_to_update['business_name'],
                "BusinessNumber" =>  $data_to_update['business_phone'],
                "Mobile" =>  $data_to_update['mobileno'],
                "AlternatePhone" =>  $data_to_update['mobileno'],
                "OtherContactInfo" =>  $data_to_update['mobileno'],
                "PrimaryEmailAddr" => [
                    "Address" => $request['email']
                ],
               
                "PrimaryPhone" => [
                    "FreeFormNumber" => $data_to_update['mobileno']
                ]
            ];

            $quickbooks_customer_id=$this->createCustomer($customer);
            if($quickbooks_customer_id>0){
                $request->session()->flash('alert-success', 'Customer added in QuickBooks too');
                $data_to_update['quickbooks_customer_id']=$quickbooks_customer_id;
            }
            else
            $request->session()->flash('alert-danger', 'Customer could not be added in QuickBooks');
            
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
       if(isset($req['action']) && $req['action']=='prioritise')
        {
       
            $dataArray['title']='Lead Priority';
            $dataArray['id']=$id;
            $prioritise=$req['priority_no']+1;
            if($prioritise==3)
            $prioritise=0;
            
            $result=$this->users->where('id','=',$id)->update(array('prioritise'=>$prioritise)); 
            $dataArray['response']=prioritise($prioritise,$id);   
            // p($req->all());
            // p($dataArray);
            
            
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', Priority changed successfully!';
                  // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'lead_priority',
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
       elseif(isset($req['action']) && $req['action']=='qsearch_lead')
        {
            $user=Auth::user();
            $dataArray['title']='Search Result';
            $where_clause=[
                ['group_id', '=', config('constants.groups.subscriber')],
                ['is_active', '=', 1],
            ];
           
            $query=$this->users
                ->where($where_clause)
                
                ->orderBy('created_at', 'desc');

                $search_val=$req->qsearch;
                $leads=$query->where(function($query) use ($search_val){
                    $query->orwhere('name', 'like', '%' . $search_val . '%')
                        ->orwhere('email', 'like', '%' . $search_val . '%')
                        ->orwhere('business_name', 'like', '%' . $search_val . '%');

                });
                //$dataArray['sql']=$leads->toSql();
                $leadsData=$leads->get()->toArray();
                // p($leadsData);
                // die;
                //$response='<table id="example1" class="table table-bordered table-striped">
                $response= ' <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Mobile</th>
                                            <th>Business Name</th>
                                            <th>Business Address</th>
                                            <th>Business Phone</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                                        
                                            $counter = 1;
                                            
                                            foreach ($leadsData as $data){
                                            
                                                $response .='<tr id="row_'.$data['id'].'">
                                                <td><strong id="name_'.$data['id'].'">'.$data['name'].'</strong>
                                                </td>
                                                <td id="email_'.$data['id'].'">'.$data['email'].'</td>
                                                <td id="mobileno_'.$data['id'].'">
                                                    '.$data['mobileno'].'</td>
                                                <td id="business_name_'.$data['id'].'">
                                                    '.$data['business_name'].'</td>
                                                <td id="business_address_'.$data['id'].'">
                                                '.$data['business_address'].'
                                                </td>
                                                <td id="business_phone_'.$data['id'].'">
                                                '.$data['business_phone'].'
                                                </td>
                                                <td id="prioritise_'.$data['id'].'">'.prioritise($data['prioritise'],$data['id']).'</td>
                                                <td>';
                                                
                                                    if($data['is_active']==2){
                                                        $response .='<button
                                                        onClick="do_action('.$data['id'].',\'restore\','.$counter.')"
                                                        type="button" class="btn btn-info btn-block btn-sm"><i
                                                            class="fas fa-undo"></i>
                                                        Restore</button>';
                                                    }else {
                                                        $response .='<a href="'.route('admin.add_to_customer', $data['id']).'"
                                                        class="btn btn-success btn-block btn-sm"><i class="fa fa-plus"></i> Add
                                                        to Customer</a>
                                                        <a href="'.route('admin.leadview',$data['id']).'"
                                                        class="btn btn-info btn-block btn-sm"><i class="fas fa-eye"></i>
                                                        View</a>';
                                                    
                                                        $response .='
                                                    <button
                                                        onClick="do_action('.$data['id'].',\'delete\','.$counter.')"
                                                        type="button" class="btn btn-danger btn-block btn-sm"><i
                                                            class="fas fa-trash"></i>
                                                        Delete</button>';
                                                }
                                                        
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
                                            <th>Mobile</th>
                                            <th>Business Name</th>
                                            <th>Business Address</th>
                                            <th>Business Phone</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        
                                    </tfoot>';
                                $dataArray['response']=  $response;
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
        elseif(isset($req['action']) && $req['action']=='submit_comment'){ 
            
            

            $this->comments->comment=$req['data']['comment'];
            $this->comments->user_id=get_session_value('id');
            $this->comments->group_id =$req['data']['group_id'];
            $this->comments->slug =$req['data']['slug'];
            //$this->comments->slug =$req['data']['user_name'];
            $this->comments->lead_id =$id;
            $this->comments->comment_section ='lead';
            $this->comments->status =1;
            //p($req->all()); die;
            $this->comments->save();
            $dataArray['error']='No';
            $dataArray['to_replace']='submit_comment_replace';
            $htmlRes=' <div class="row border">
                            <div class="col-12">
                                <strong>'.get_session_value('name').' ('.$req['data']['slug'].') </strong> '.date('d/m/Y H:i:s',time()).'<br>
                                '.$req['data']['comment'].'
                            </div>
                        </div>';

    // Email Section
      
        // Get All Quote Data
        $leadData=$this->users->where('id',$id)->get()->toArray();
        $leadData=$leadData[0];
        $mailData['body_message']='There was a new note added to the Lead by '.$leadData['name'].' on dated '.date(config('constants.date_formate'));
        $mailData['subject']='New note added to Lead';

         $emailAdd=[
                    config('constants.admin_email'),
                    //$quoteData['customer']['email'],
                    //$quoteData['venue_group']['email']
                ];
               
                   // $emailAdd[]=$quoteData['customer']['email'];
               

        if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
            $dataArray['emailMsg']='Email Sent Successfully';
        }
    //                        
            $dataArray['response']=$htmlRes;
            $dataArray['msg']='Mr.'.get_session_value('name').', Commented successfully!';
            $activityComment='Mr.'.get_session_value('name').', added comment!';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'comment_added_for_lead',
                'comments'=>$activityComment,
                'others'=>'users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);
        }

        
      
        echo json_encode($dataArray);
        die;
    }

   
}
