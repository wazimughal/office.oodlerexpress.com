<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\adminpanel\Users;
use App\Models\adminpanel\Groups;
use App\Models\adminpanel\Quotes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

use App\Mail\EmailTemplate;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel; // To export import excel
use DateTime;

// Import/Export Excelsheet
use App\Exports\ExportCustomerDeliveryBalance;

class CustomersController extends Controller
{

    function __construct() {
        
        $this->users= new Users;
        $this->groups= new Groups;
        $this->quotes= new Quotes;
      }
      public function addcustomers(){
        $user=Auth::user(); 
       
         return view('adminpanel/add_customers',compact('user'));
     }
     public function report_customers(Request $req){
        $user=Auth::user();

        $where_in_clause=$customer_ids=$driver_ids=$quote_status=array();

        $where_clasue=[
            'group_id'=> config('constants.groups.customer'),
            'is_active'=> 1,
        ];
        
        if(isset($req->customer_id) && !empty($req->customer_id)){
            $customer_ids=$req->customer_id;
            $where_in_clause['customer_id']=$customer_ids;
            }
        // if($user->group_id!=config('constants.groups.admin'))
        // $where_clasue['id']=get_session_value('id');

        
            $customersData=$this->users->with('City')->with('ZipCode')
            ->where($where_clasue)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
       
        return view('adminpanel.reports.customers',get_defined_vars());
    }
    public function report_customer_delivery_balance(Request $req){
        $user=Auth::user();

        $where_in_clause=$customer_ids=$customer_ids=$quote_status=array();

        $where_clasue=[
            'group_id'=> config('constants.groups.customer'),
            'is_active'=> 1,
        ];
        
        if(isset($req->customer_id) && !empty($req->customer_id)){
            $customer_ids=$req->customer_id;
            $where_in_clause['customer_id']=$customer_ids;
            }
            $customerData=$this->users->with('City')->with('ZipCode')
            ->where($where_clasue)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            
            if(isset($req->action) && $req->action=='download_customer_delivery_balance'){ // Download Excelsheed

                $where_clause=[
                    ['status','=',config('constants.quote_status.complete')],
                    ['is_active','=',1],
                    ['customer_id','=',$req->customer_id],
                ];
             
               
                $quotes=$this->quotes
                        ->with(array('driver','customer','quote_agreed_cost','quote_prices','invoices'))
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
                //   p($where_clause);
                //   p($quotesData); die;
                $exportData=array();
                $total_delivery_cost=0;
                $total_due_payment=0;
                $total_received_payment=0;
                foreach($quotesData as $key=>$data){

                    $delivery_cost=$data['quote_agreed_cost']['quoted_price']+$data['quote_agreed_cost']['extra_charges']+$data['quote_agreed_cost']['any_other_extra_charges'];
                    $recievedAmount=0; 
                    if($data['invoices'] && count($data['invoices'])>0){ 
                        foreach ($data['invoices'] as $key=>$invoice){ 
                            $recievedAmount=$recievedAmount+$invoice['paid_amount'];
                        }
                    }
                    $total_delivery_cost=$total_delivery_cost+$delivery_cost;
                    $total_received_payment=$total_received_payment+$recievedAmount;

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
                        'customer_name'=>$data['customer']['name'],
                        // 'customer_email'=>$data['customer']['email'],
                        // 'customer_mobileno'=>$data['customer']['mobileno'],
                        // 'customer_business_name'=>$data['customer']['business_name'],
                        // 'driver_name'=>(isset($data['driver']['name']) && !empty($data['driver']['name']))?$data['driver']['name']:'',
                        // 'driver_email'=>(isset($data['driver']['email']) && !empty($data['driver']['email']))?$data['driver']['email']:'',
                        // 'driver_mobileno'=>(isset($data['driver']['mobileno']) && !empty($data['driver']['mobileno']))?$data['driver']['mobileno']:'',
                        // 'driver_license_no'=>(isset($data['driver']['license_no']) && !empty($data['driver']['license_no']))?$data['driver']['license_no']:'',
                        'Delivery Cost'=>'$'.$delivery_cost,
                        'Paid amount'=>'$'.$recievedAmount,
                        'Due Payment'=>'$'.$delivery_cost-$recievedAmount,
                    ];
                    
                }
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
                    'customer_name'=>'TOTAL Payments',
                    // 'customer_email'=>'',
                    // 'customer_mobileno'=>'',
                    // 'customer_business_name'=>'',
                    // 'driver_name'=>'',
                    // 'driver_email'=>'',
                    // 'driver_mobileno'=>'',
                    // 'driver_license_no'=>'',
                    'Total Delivery Cost'=>'$'.$total_delivery_cost,
                    'Total Paid amount'=>'$'.$total_received_payment,
                    'Total Due Payment'=>'$'.$total_delivery_cost-$total_received_payment,
                    
                ];

                return Excel::download(new ExportCustomerDeliveryBalance($exportData), 'customer-delivery-balance.xlsx');

            }
       
        return view('adminpanel.reports.drivers',get_defined_vars());
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
            'shipping_cat'=>'required',
            
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
        $this->users->city=$request['city'];
        $this->users->state=$request['state'];;
        $this->users->zipcode=$request['zipcode'];
        

        $this->users->created_at=time();
        $this->users->group_id=config('constants.groups.customer');

        $this->users->business_address=$request['business_address'];

        if(isset($request['othershipping']) && !empty($request['othershipping']))
        $shipping_cat = getOtherCategory($request['othershipping']);
        else
        $shipping_cat=json_encode($request['shipping_cat']);
        

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
            'shipping_cat'=>'required',
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
       $data_to_update['city']=$request['city'];
       $data_to_update['state']=$request['state'];;
       $data_to_update['zipcode']=$request['zipcode'];

        $data_to_update['business_address']=$this->users->business_address=$request['business_address'];

        if(isset($request['othershipping']) && !empty($request['othershipping']))
        $shipping_cat = getOtherCategory($request['othershipping']);
        else
        $shipping_cat=json_encode($request['shipping_cat']);
        

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
        elseif(isset($req['action']) && $req['action']=='qsearch_customer')
        {
            $user=Auth::user();
            $dataArray['title']='Search Result';
            $where_clause=[
                ['group_id', '=', config('constants.groups.customer')],
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
                $customerData=$leads->get()->toArray();
                // p($customerData);
                // die;
                //$response='<table id="example1" class="table table-bordered table-striped">
                $response= ' <thead>
                                        <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Business Name</th>
                                        <th>Delivery From</th>
                                        <th>Delivery To</th>
                                        <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                                        
                                            $counter = 1;
                                            
                                            $customer_id_array=array();
                                            foreach ($customerData as $data){
                                                $customer_id_array[]=$data['id'];
                                            
                                                $response .='<tr id="row_'.$data['id'].'">
                                                <td><strong id="name_'.$data['id'].'">'.$data['name'].'</strong>
                                                </td>
                                                <td id="email_'.$data['id'].'">'.$data['email'].'</td>
                                                <td id="mobileno_'.$data['id'].'">
                                                    '.$data['mobileno'].'</td>
                                                <td id="address_'.$data['id'].'">
                                                    '.$data['address'].'</td>
                                                <td id="business_name_'.$data['id'].'">
                                                '.$data['business_name'].'
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
                                                
                                                    
                                                        $response .='<button onclick="$(\'#download_customer_delivery_balance_'.$data['id'].'\').submit()" type="button" class="btn btn-block btn-primary"><i class="fa fa-download"></i> Delivery Balance Excel</button>';
                                                    
                                                        
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
                                        <th>Business Name</th>
                                        <th>Delivery From</th>
                                        <th>Delivery To</th>
                                        <th>Action</th>
                                        </tr>
                                        
                                    </tfoot>';

                                $dataArray['customers_id']= implode(',',$customer_id_array)  ;
                                $dataArray['response']=  $response;
        }
        elseif(isset($req['action']) && $req['action']=='qsearch_customer')
        {
            $user=Auth::user();
            $dataArray['title']='Search Result';
            $where_clause=[
                ['group_id', '=', config('constants.groups.customer')],
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
                                            <th>Lead by</th>
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
                                                <td id="status'.$data['id'].'">';

                                                if($data['lead_by'] == 0){
                                                    $response .='<a @disabled(true)
                                                    class="btn btn-success btn-flat btn-sm"><i
                                                        class="fas fa-chart-line"></i> Office</a>';
                                                }
                                                else{
                                                    $response .='<a @disabled(true)
                                                    class="btn bg-gradient-secondary btn-flat btn-sm"><i
                                                        class="fas fa-chart-line"></i> Website</a>';
                                                }
                                                
                                                $response .= '</td>
                                                <td>';
                                                
                                                    if($user->group_id == config('constants.groups.admin')){
                                                        $response .='<a href="'.route('delivery.add_delivery_form', $data['id']).'"
                                                        class="btn btn-success btn-block btn-sm"><i class="fas fa-plus"></i>
                                                        Add Delivery</a>
                                                    <a href="'.route('customer.quotes', $data['id']).'"
                                                        class="btn btn-primary btn-block btn-sm"><i class="fas fa-eye"></i>
                                                        View all quotes</a>';
                                                        if ($data['is_active'] == 2){
                                                            $response .=' <button
                                                            onClick="do_action('.$data['id'].',\'restore\','.$counter.')"
                                                            type="button" class="btn btn-info btn-block btn-sm"><i
                                                                class="fas fa-chart-line"></i>
                                                            Restore</button>';
                                                        }
                                                        else{
                                                            $response .='  <a href="'.route('admin.customerseditform', $data['id']) .'"
                                                            class="btn btn-info btn-block btn-sm"><i
                                                                class="fas fa-edit"></i>
                                                            Edit</a>
                                                        <button
                                                            onClick="do_action('.$data['id'] .',\'delete\','.$counter .')"
                                                            type="button" class="btn btn-danger btn-block btn-sm"><i
                                                                class="fas fa-trash"></i>
                                                            Delete</button>';

                                                        }
                                                    }else {
                                                        $response .='<a href="'.route('admin.customerseditform', $data['id']).'"
                                                        class="btn btn-info btn-block btn-sm"><i class="fas fa-edit"></i>
                                                        Edit</a>';
                                                    
                                                        
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
                                            <th>Lead by</th>
                                            <th>Action</th>
                                        </tr>
                                        
                                    </tfoot>';
                                $dataArray['response']=  $response;
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
