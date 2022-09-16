<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\adminpanel\Users;
use App\Models\adminpanel\Quotes;
use App\Models\adminpanel\Groups;
use App\Models\adminpanel\products;
use App\Models\adminpanel\quote_products;
use App\Models\adminpanel\product_categories;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class QuotesController extends Controller
{
    

    function __construct() {
        
        $this->users= new Users;
        $this->groups= new Groups;
        $this->quotes= new Quotes;
        $this->products= new products;
        $this->quote_products= new quote_products;
        $this->product_categories= new product_categories;
      }

    // List All the quotes 
    public function quotes($type=NULL){
        $user=Auth::user();
        if($type=='pending'){
            $quotesData=$this->users
            ->where('group_id', '=', config('constants.groups.subscriber'))
            ->where('status', '=', config('constants.quote_status.pending'))
            ->where('is_active', '=', 1)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }
        elseif($type=='approved'){
            $quotesData=$this->users
            ->where('group_id', '=', config('constants.groups.subscriber'))
            ->where('status', '=', config('constants.quote_status.approved'))
            ->where('is_active', '=', 1)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }
        elseif($type=='cancelled'){
            $quotesData=$this->users
            ->where('group_id', '=', config('constants.groups.subscriber'))
            ->where('status', '=', config('constants.quote_status.cancelled'))
            ->where('is_active', '=', 1)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }
        else{
            $quotesData=$this->users
            ->where('group_id', '=', config('constants.groups.subscriber'))
            ->where('is_active', '=', 1)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }

    return view('adminpanel/quotes',compact('quotesData','user'));
    }
    public function request_quotes_form(){
       $user=Auth::user(); 
       $products=$this->product_categories->with('products')
       ->where('is_active', '=', 1)
       ->get()->toArray();
       
       
        return view('adminpanel/request_quote',compact('user','products'));
    }
    public function save_quote_date(Request $request){
       
        $validator=$request->validate([
            'quote_type'=>'required',
            'business_type'=>'required',
            'po_number'=>'required',
            'pickup_street_address'=>'required',
            'pickup_contact_number'=>'required',
            'pickup_date'=>'required',
            'pickup_at_time'=>'required',
            'drop_off_street_address'=>'required',
            'drop_off_contact_number'=>'required',
            'drop_off_date'=>'required',
            'drop_off_at_time'=>'required',
            
        ]);
        
        // p($request->all());
        // die;
        
        $this->quotes->quote_type=$request['quote_type'];
        $this->quotes->elevator=$request['elevator'];
        $this->quotes->no_of_appartments=$request['no_of_appartments'];
        $this->quotes->list_of_floors=json_encode($request['list_of_floors']);
        $this->quotes->business_type=$request['business_type'];
        $this->quotes->po_number=$request['po_number'];
        $this->quotes->pickup_street_address=$request['pickup_street_address'];
        $this->quotes->pickup_unit=$request['pickup_unit'];
        $this->quotes->pickup_state_id=$request['pickup_state_id'];
        $this->quotes->pickup_city_id=$request['pickup_city_id'];
        $this->quotes->pickup_zipcode_id=$request['pickup_zipcode_id'];
        $this->quotes->pickup_contact_number=$request['pickup_contact_number'];
        $this->quotes->pickup_date=$request['pickup_date'];
        $this->quotes->pickup_at_time=$request['pickup_at_time'];
        $this->quotes->drop_off_street_address=$request['drop_off_street_address'];
        $this->quotes->drop_off_unit=$request['drop_off_unit'];
        $this->quotes->drop_off_state_id=$request['drop_off_state_id'];
        $this->quotes->drop_off_city_id=$request['drop_off_city_id'];
        $this->quotes->drop_off_zipcode_id=$request['drop_off_zipcode_id'];
        $this->quotes->drop_off_contact_number=$request['drop_off_contact_number'];
        $this->quotes->drop_off_instructions=$request['drop_off_instructions'];
        $this->quotes->drop_off_date=$request['drop_off_date'];
        $this->quotes->drop_off_at_time=$request['drop_off_at_time'];
        $this->quotes->customer_id=get_session_value('id');
        

        $this->quotes->created_at=time();
        
     
        $this->quotes->save();
       
        foreach($request['product_details'] as $key=>$productData){
           
            // echo 'pid :'.$productData['cat_id'][0];
            // p($productData); die; && $productData['product_name'][0]==''
            if(isset($productData['product_id'][0]) && $productData['product_id'][0]>0 && !isset($productData['product_name'][0]) )
            continue;

            $this->quote_products->product_name=$productData['product_name'][0];
            
            if(isset($productData['product_id'][0]) && $productData['product_id'][0]>0)
            $this->quote_products->product_id=$productData['product_id'][0];

            $this->quote_products->quantity=$productData['item_quantity'][0];
            $this->quote_products->size=$productData['item_size'][0];
            $this->quote_products->size_unit=$productData['item_size_unit'][0];
            $this->quote_products->description=$productData['item_description'][0];
            $this->quote_products->cat_id=$productData['cat_id'][0];
            $this->quote_products->quote_id=$this->quotes->id;
            $this->quote_products->save();
        }

        $request->session()->flash('alert-success', 'Quote Request Submitted! Please Check in Pending quotes Tab');

                    // Activity Log
                    $activityComment='Mr.'.get_session_value('name').' Requested for quote having PO number : '.$request['po_number'];
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$this->quotes->id,
                        'action_slug'=>'new_quote_requested',
                        'comments'=>$activityComment,
                        'others'=>'quotes',
                        'created_at'=>date('Y-m-d H:I:s',time()),
                    );
                    $activityID=log_activity($activityData);

        return redirect()->back();
        
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
    public function DeletequotessData($id){
        $dataArray['error']='No';
        $dataArray['title']='User';

        $result=$this->users->where('id','=',$id)->update(array('is_active'=>3));             
        if($result){
            $dataArray['msg']='Mr.'.get_session_value('name').', record delted successfully!';

            $activityComment='Mr.'.get_session_value('name').' moved quote to approved/pending/cancelled';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'quote_status_changed',
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
            $dataArray['title']='quote Status Updated ';
            $activityComment='Mr.'.get_session_value('name').' moved quote to approved/pending/cancelled';

            if(config('constants.quote_status.pending')==$req['status']){
            $dataArray['status_btn']='<a disabled="" class="btn bg-gradient-danger btn-flat btn-sm"><i class="fas fa-chart-line"></i> Pending</a>';
            $activityComment='Mr.'.get_session_value('name').' moved quote to pending';
            }
            else if(config('constants.quote_status.approved')==$req['status']){
            $dataArray['status_btn']='<a disabled="" class="btn bg-gradient-success btn-flat btn-sm"><i class="fas fa-chart-line"></i> Approved</a>';
            $activityComment='Mr.'.get_session_value('name').' moved quote to approved';
            }
            else if(config('constants.quote_status.cancelled')==$req['status']){
            $dataArray['status_btn']='<a disabled="" class="btn bg-gradient-secondary btn-flat btn-sm"><i class="fas fa-chart-line"></i> Cancelled</a>';
            $activityComment='Mr.'.get_session_value('name').' moved quote to cancelled';
            }
            $result=$this->users->where('id','=',$id)->update(array('status'=>$req['status']));             
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', User quote '.$req['alertmsg'].' successfully!';
                
                $activityData=array(
                    'user_id'=>get_session_value('id'),
                    'action_taken_on_id'=>$id,
                    'action_slug'=>'quote_status_changed',
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
                'action_slug'=>'quote_deleted',
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
                'action_slug'=>'quote_deleted',
                'comments'=>'Mr.'.get_session_value('name').' deleted record',
                'others'=>'users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            }
            
            else{
                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
            }

        }
        else if(isset($req['action']) && $req['action'] =='viewquoteData'){
            $dataArray['error']='No';
            $dataArray['msg']='quote Successfully Updated';
            $dataArray['title']='quotes Panel';
            $quotesData=$this->users->where('id',($req['id']))->get()->toArray();
            $quotesData=$quotesData[0];
            //p($quotesData);
            $quoteHTML='<div class="container">
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Name</strong>
                </div>
                <div class="col-5">
                    '.$quotesData['name'].'</div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Email</strong>
                </div>
                <div class="col-5">
                    '.$quotesData['email'].'</div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Mobile No.</strong>
                </div>
                <div class="col-5">
                    '.$quotesData['mobileno'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Phone</strong>
                </div>
                <div class="col-5">
                    '.$quotesData['phone'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5"><strong>Subject</strong></div>
                <div class="col-5">
                    '.$quotesData['subject'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Message</strong>
                </div>
                <div class="col-5">
                    '.$quotesData['message'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            
        </div>';
            $dataArray['res']=$quoteHTML;
        }
        else if(isset($req['action']) && $req['action'] =='SaveAddtoCustomerForm'){
            $dataArray['error']='No';
            $dataArray['msg']='quote added to customer Successfully Updated';
            $dataArray['title']='quotes Panel';
            $dataArray['actionType']='move_to_customer';
            
            $quoteData=array();
            $dataArray['firstname']=$req['firstname'];
            $dataArray['lastname']=$req['lastname'];
            $dataArray['name']=$req['firstname'].' '.$req['lastname'];
            $dataArray['mobileno']=$req['mobileno'];
            $dataArray['phone']=$req['phone'];
            $dataArray['business_name']=$req['business_name'];
            $dataArray['business_address']=$req['business_address'];
            $dataArray['business_mobile']=$req['business_phone'];
            $dataArray['business_phone']=$req['business_phone'];
            $dataArray['subject']=$req['subject'];
            $dataArray['message']=$req['message'];
            $dataArray['id']=$req['quote_id'];
            if(isset($req['othercity']) && !empty($req['othercity']))
                $cityId = getOtherCity($req['othercity']);
            else
                $cityId=$req['city'];

            $this->users->where('id', $req['quote_id'])->update(
                array(
                    'firstname'=>$req['firstname'],
                    'lastname'=>$req['lastname'],
                    'name'=>$req['firstname'].' '.$req['lastname'],
                    'mobileno'=>$req['mobileno'],
                    'phone'=>$req['phone'],
                    'business_name'=>$req['business_name'],
                    'business_address'=>$req['business_address'],
                    'business_phone'=>$req['business_phone'],
                    'business_mobile'=>$req['business_mobile'],
                    'password'=>Hash::make(Str::random(10)),
                    'group_id'=>config('constants.groups.customer'),
                    'subject'=>$req['subject'],
                    'message'=>$req['message'],
                    'city_id'=>$cityId)
            );
            // Activity Logged
            $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$req['quote_id'],
                'action_slug'=>'customer_added',
                'comments'=>'Mr.'.get_session_value('name').' added a customer Mr.'.$req['firstname'].' '.$req['lastname'],
                'others'=>'users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            
            echo json_encode($dataArray);
            die;

        }
        else if(isset($req['action']) && $req['action'] =='SaveEditFormquote'){
            $dataArray['error']='No';
            $dataArray['msg']='quote Successfully Updated';
            $dataArray['title']='quotes Panel';
            $quoteData=array();
            $dataArray['firstname']=$req['firstname'];
            $dataArray['lastname']=$req['lastname'];
            $dataArray['name']=$req['firstname'].' '.$req['lastname'];
            $dataArray['mobileno']=$req['mobileno'];
            $dataArray['phone']=$req['phone'];
            $dataArray['subject']=$req['subject'];
            $dataArray['message']=$req['message'];
            $dataArray['id']=$req['quote_id'];
            
            $this->users->where('id', $req['quote_id'])->update(array(
                'firstname'=>$req['firstname'],
                'lastname'=>$req['lastname'],
                'name'=>$req['firstname'].' '.$req['lastname'],
                'mobileno'=>$req['mobileno'],
                'phone'=>$req['phone'],
                'subject'=>$req['subject'],
                'message'=>$req['message'],
            
            ));
             // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$req['quote_id'],
                'action_slug'=>'quote_updated',
                'comments'=>'Mr.'.get_session_value('name').' updated quote having name Mr.'.$req['firstname'].' '.$req['lastname'],
                'others'=>'users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));

            echo json_encode($dataArray);
            die;

        }
        else if(isset($req['action']) && $req['action'] =='updatequoteForm'){
            $dataArray['error']='No';
           
            
            $quotesData=$this->users->where('id', '=', $req['id'])->get()->toArray();
            $data=$quotesData[0];
            $csrf_token = csrf_token();
            
        
$formHtml='<form id="EditquoteForm"
                                                                            method="GET"
                                                                            action=""
                                                                            onsubmit="return updatequote('. $data['id'].','. $req['counter'].')">
                                                                            <input type="hidden" name="_token" value="'.$csrf_token.'" />
                                                                            <input type="hidden" name="action" value="SaveEditFormquote" />
                                                                            <input type="hidden" name="quote_id" value="'.$data['id'].'" />
                                                                            
                                                                            
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
                                                                                    <input  type="text" name="subject" class="form-control" placeholder="Subject" value="'. $data['subject'].'" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <textarea rows="10" name="message" class="form-control" placeholder="Write your Message"  required>'. $data['message'].'</textarea>
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
        else if(isset($req['action']) && $req['action'] =='addToCustomerForm'){
            $dataArray['error']='No';
           
            $data=$this->users->where('id',$req['id'])->get()->toArray();
            $data= $data[0];
            $csrf_token = csrf_token();
            
        
$formHtml='<form id="EditquoteForm"
                                                                            method="GET"
                                                                            action=""
                                                                            onsubmit="return updatequote('. $data['id'].','. $req['counter'].')">
                                                                            <input type="hidden" name="_token" value="'.$csrf_token.'" />
                                                                            <input type="hidden" name="action" value="SaveAddtoCustomerForm" />
                                                                            <input type="hidden" name="quote_id" value="'.$data['id'].'" />
                                                                            
                                                                           
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
                                                                                    <input  type="text" name="business_name" class="form-control" placeholder="Business Name" value="'. $data['business_name'].'" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <input  type="text" name="business_address" class="form-control" placeholder="Business address" value="'. $data['business_address'].'" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <input type="text" name="business_mobile"class="form-control" placeholder="Business Mobile No." value="'. $data['business_mobile'].'" required>
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
                                                                                    <select id="city" onChange="changeCity()" name="city" class="form-control select2bs4" placeholder="Select Venue Group">'.getCitiesOptions().'</select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div id="othercity"></div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <input  type="text" name="subject" class="form-control" placeholder="Subject" value="'. $data['subject'].'" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <textarea rows="10"  name="message" class="form-control" placeholder="Write your message..." required>'. $data['message'].'</textarea>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-4">&nbsp;</div>
                                                                                <div class="col-4">
                                                                                    <button type="submit"
                                                                                        class="btn btn-outline-success btn-block btn-lg"><i
                                                                                            class="fa fa-save"></i>
                                                                                        Add to Customer</button>
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
