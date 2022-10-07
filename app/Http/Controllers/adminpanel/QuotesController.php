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
use App\Models\adminpanel\files;
use App\Models\adminpanel\quote_prices;
use App\Models\adminpanel\comments;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

// Used for Email Section
use App\Mail\EmailTemplate;
use Illuminate\Support\Facades\Mail;

use DB;

class QuotesController extends Controller
{
    

    function __construct() {
        
        $this->users= new Users;
        $this->groups= new Groups;
        $this->quotes= new Quotes;
        $this->products= new products;
        $this->quote_products= new quote_products;
        $this->product_categories= new product_categories;
        $this->comments= new comments;
        $this->files= new files;
        $this->quote_prices= new quote_prices;
      }

      public function deliveries(){
        $user=Auth::user();
        if($user->group_id==config('constants.groups.admin')){
            $quotesData=$this->quotes
            ->with('quote_products')
            ->with('customer')
            ->with('driver')
            ->where('is_active', 1)
            ->where('status', config('constants.quote_status.delivery'))
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            return view('adminpanel.deliveries',get_defined_vars());
        }
        elseif($user->group_id==config('constants.groups.customer')){
            $quotesData=$this->quotes
            ->with('quote_products')
            ->with('customer')
            ->with('driver')
            ->where('is_active', 1)
            ->where('customer_id', get_session_value('id'))
            ->where('status', config('constants.quote_status.delivery'))
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }
        elseif($user->group_id==config('constants.groups.driver')){
            $quotesData=$this->quotes
            ->with('quote_products')
            ->with('customer')
            ->with('driver')
            ->where('is_active', 1)
            ->where('driver_id', get_session_value('id'))
            ->where('status', config('constants.quote_status.delivery'))
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }
        
        return view('adminpanel.deliveries',get_defined_vars());
        
      }
      public function view_delivery($id){

        $user=Auth::user();
        $products=$this->product_categories->with('products')
            ->where('is_active', '=', 1)
            ->get()->toArray();
    
           $quotesData=$this->quotes
           ->with('quote_products')
           ->with('customer')
           ->with('driver')
           ->with('quote_prices')
           ->with('comments')
           ->where('id', $id)
           ->where('is_active', 1)
           ->where('status','>=', config('constants.quote_status.delivery'))
           //->orwhere('status', config('constants.quote_status.complete'))
           ->orderBy('created_at', 'desc')->get()->toArray();
           if(empty($quotesData)){
            echo 'There is something wrong'; die;
           }
           
           $quotesData=$quotesData[0];
        //p($quotesData); die;
        return view('adminpanel.view_delivery',get_defined_vars());

      }

    // List All the quotes 
    public function quotes($type=NULL){
        $user=Auth::user();
        
    if($user->group_id==config('constants.groups.admin')):

            if($type=='trash'){
                $quotesData=$this->quotes
                ->with('quote_products')
                ->where('is_active', '=', 2)
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            }
            elseif($type=='requested'){
                $quotesData=$this->quotes
                ->with('quote_products')
                ->where('status', '=', config('constants.quote_status.pending'))
                ->where('is_active', '=', 1)
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
          
            }
            elseif($type=='cancelled'){
                $quotesData=$this->quotes
                ->with('quote_products')
                ->where('status', '=', config('constants.quote_status.declined'))
                ->where('is_active', '=', 1)
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
          
            }
            elseif($type=='new'){
                $quotesData=$this->quotes
                ->with('quote_products')
                ->where('status', '=', config('constants.quote_status.quote_submitted'))
                ->where('is_active', '=', 1)
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
          
            }
            elseif($type=='approved'){
                $quotesData=$this->quotes
                ->with('quote_products')
                ->where('status', '=', config('constants.quote_status.approved'))
                ->where('is_active', '=', 1)
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
          
            }
            else{
                $quotesData=$this->quotes
                ->with('quote_products')
                ->where('status', '=', config('constants.quote_status.pending'))
                ->where('is_active', '=', 1)
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            }
            
            return view('adminpanel.quotes',get_defined_vars());
         
    else: /// user Portion Started

            if($type=='trash'){
                $quotesData=$this->quotes
                ->with('quote_products')
                ->where('customer_id', '=', get_session_value('id'))
                ->where('is_active', '=', 2)
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            }
            elseif($type=='requested'){
                $quotesData=$this->quotes
                ->with('quote_products')
                ->where('customer_id', '=', get_session_value('id'))
                ->where('status', '=', config('constants.quote_status.pending'))
                ->where('is_active', '=', 1)
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
          
            }
            elseif($type=='cancelled'){
                $quotesData=$this->quotes
                ->with('quote_products')
                ->where('customer_id', '=', get_session_value('id'))
                ->where('status', '=', config('constants.quote_status.declined'))
                ->where('is_active', '=', 1)
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
          
            }
            elseif($type=='new'){
                $quotesData=$this->quotes
                ->with('quote_products')
                ->where('customer_id', '=', get_session_value('id'))
                ->where('status', '=', config('constants.quote_status.quote_submitted'))
                ->where('is_active', '=', 1)
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
          
            }
            elseif($type=='approved'){
                $quotesData=$this->quotes
                ->with('quote_products')
                ->where('customer_id', '=', get_session_value('id'))
                ->where('status', '=', config('constants.quote_status.approved'))
                ->where('is_active', '=', 1)
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
          
            }
            else{
                $quotesData=$this->quotes
                ->with('quote_products')
                ->where('customer_id', '=', get_session_value('id'))
                ->where('status', '=', config('constants.quote_status.pending'))
                ->where('is_active', '=', 1)
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            }
            
    endif;
            //p($quotesData); die;
        
        return view('adminpanel.user_quotes',get_defined_vars());
    }
    public function add_to_delivery($id){
        $user=Auth::user(); 
        if($user->group_id!=config('constants.groups.admin'))
        return redirect()->back();

        $products=$this->product_categories->with('products')
        ->where('is_active', '=', 1)
        ->get()->toArray();

       $quotesData=$this->quotes
       ->with('quote_products')
       ->with('customer')
       ->with('driver')
       ->with('quote_prices')
       ->with('comments')
       ->where('id', $id)
       ->orderBy('created_at', 'desc')->get()->toArray();
       $quotesData=$quotesData[0];
      
        return view('adminpanel/add_to_delivery',get_defined_vars());
    }
    public function save_add_to_delivery($id,Request $request){
       
        $validator=$request->validate([
            'driver_id'=>'required',
        ]);
        
        $driverData=$this->users->where('id',$request['driver_id'])->get('email')->toArray();
        $driverData=$driverData[0];

        $this->quotes->where('id',$id)->update(['driver_id'=>$request['driver_id'],'status'=>config('constants.quote_status.delivery')]);
        $request->session()->flash('alert-success', 'Driver assigned Successfully !');

            // Email Section
                


            $mailData['body_message']='You are assigned a new delivery, having PO Number:'.$request['po_number'].'. Please login to the CRM and look for details';
            $mailData['subject']='New Delivery Assigned';

            $emailAdd=[
                        config('constants.admin_email'),
                        $driverData['email'],
                        
                    ];
                
                

            if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
                $dataArray['emailMsg']='Email Sent Successfully';
            }


        // Activity Log
        $activityComment='Mr.'.get_session_value('name').' assigned driver to quote having PO number : '.$request['po_number'];
        $activityData=array(
            'user_id'=>get_session_value('id'),
            'action_taken_on_id'=>$id,
            'action_slug'=>'add_to_delivery',
            'comments'=>$activityComment,
            'others'=>'quotes',
            'created_at'=>date('Y-m-d H:I:s',time()),
        );
        $activityID=log_activity($activityData);

        return redirect()->back();

    }
    public function quotes_edit_form($id=NULL){
       $user=Auth::user(); 
       $products=$this->product_categories->with('products')
       ->where('is_active', '=', 1)
       ->get()->toArray();


       $quotesData=$this->quotes
       ->with('quote_products')
       ->where('id', $id)
       ->orderBy('created_at', 'desc')->get()->toArray();
       $quotesData=$quotesData[0];
       //p($quotesData); die;
        return view('adminpanel/edit_quote',get_defined_vars());
    }
    public function save_quote_edit($id,Request $request){
       
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
        
         //p($request->all());
       
        
        $to_update_date['quote_type']=$request['quote_type'];
        $to_update_date['elevator']=$request['elevator'];
        $to_update_date['no_of_appartments']=$request['no_of_appartments'];
        $to_update_date['list_of_floors']=json_encode($request['list_of_floors']);
        $to_update_date['business_type']=$request['business_type'];
        $to_update_date['po_number']=$request['po_number'];
        $to_update_date['pickup_street_address']=$request['pickup_street_address'];
        $to_update_date['pickup_unit']=$request['pickup_unit'];
        $to_update_date['pickup_state_id']=$request['pickup_state_id'];
        $to_update_date['pickup_city_id']=$request['pickup_city_id'];
        $to_update_date['pickup_zipcode_id']=$request['pickup_zipcode_id'];
        $to_update_date['pickup_contact_number']=$request['pickup_contact_number'];
        $to_update_date['pickup_date']=$request['pickup_date'];
        $to_update_date['pickup_at_time']=$request['pickup_at_time'];
        $to_update_date['drop_off_street_address']=$request['drop_off_street_address'];
        $to_update_date['drop_off_unit']=$request['drop_off_unit'];
        $to_update_date['drop_off_state_id']=$request['drop_off_state_id'];
        $to_update_date['drop_off_city_id']=$request['drop_off_city_id'];
        $to_update_date['drop_off_zipcode_id']=$request['drop_off_zipcode_id'];
        $to_update_date['drop_off_contact_number']=$request['drop_off_contact_number'];
        $to_update_date['drop_off_instructions']=$request['drop_off_instructions'];
        $to_update_date['drop_off_date']=$request['drop_off_date'];
        $to_update_date['drop_off_at_time']=$request['drop_off_at_time'];

        $to_update_date['customer_id']=get_session_value('id');
        if(isset($request['customer_id']) && $request['customer_id']>0)
        $to_update_date['customer_id']=$request['customer_id'];
        
       $this->quotes->where('id',$id)->update($to_update_date);
       $this->quote_products->where('quote_id',$id)->delete();
       
        foreach($request['product_details'] as $key=>$productData){
           
            if(!isset($productData['product_name'][0]) || $productData['product_name'][0]=='') // If listed product is not selected then ignore it and more to next array
            continue;

            if(!isset($productData['product_id'][0]) && $productData['product_name'][0]!='' && $productData['cat_id'][0]>0){
                // If custom Product is selected Insert New Product 
                DB::table('products')->insert([
                    ['name' => $productData['product_name'][0],
                     'slug' => phpslug($productData['product_name'][0]),
                     'size' => $productData['item_size'][0],
                     'size_unit' => $productData['item_size_unit'][0],
                     'additional_notes' => $productData['item_description'][0],
                     'cat_id' => $productData['cat_id'][0],
                     'user_id' => get_session_value('id'),
                     'added_by' => 2
                    ]
                ]);
                $product_id = DB::getPdo()->lastInsertId();
               
            }
            else{
                $product_id=$productData['product_id'][0];
            }

            // echo 'pid :'.$productData['cat_id'][0];
            // p($productData); die;
            


            DB::table('quote_products')->insert([
                ['product_name' => $productData['product_name'][0],
                 'product_id' => $product_id,
                 'quantity' => $productData['item_quantity'][0],
                 'size' => $productData['item_size'][0],
                 'description' => $productData['item_description'][0],
                 'cat_id' => $productData['cat_id'][0],
                 'quote_id' => $id,
                ]
            ]);


        }

        $request->session()->flash('alert-success', 'Quote Request data updated Successfully !');

                    // Activity Log
                    $activityComment='Mr.'.get_session_value('name').' updated quote having PO number : '.$request['po_number'];
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$id,
                        'action_slug'=>'new_quote_requested',
                        'comments'=>$activityComment,
                        'others'=>'quotes',
                        'created_at'=>date('Y-m-d H:I:s',time()),
                    );
                    $activityID=log_activity($activityData);

        return redirect()->back();
        
    }
    public function request_quotes_form($id=NULL){
       $user=Auth::user(); 
       $products=$this->product_categories->with('products')
       ->where('is_active', '=', 1)
       ->get()->toArray();
       
       
        return view('adminpanel/request_quote',get_defined_vars());
    }
    public function save_quote_data(Request $request){
       
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
        
         //p($request->all());
       
        
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
        if(isset($request['customer_id']) && $request['customer_id']>0)
        $this->quotes->customer_id=$request['customer_id'];
        
        

        $this->quotes->created_at=time();
        
     
       $this->quotes->save();
       
        foreach($request['product_details'] as $key=>$productData){
           
            if(!isset($productData['product_name'][0]) || $productData['product_name'][0]=='') // If listed product is not selected then ignore it and more to next array
            continue;

            if(!isset($productData['product_id'][0]) && $productData['product_name'][0]!='' && $productData['cat_id'][0]>0){
                // If custom Product is selected Insert New Product 
                DB::table('products')->insert([
                    ['name' => $productData['product_name'][0],
                     'slug' => phpslug($productData['product_name'][0]),
                     'size' => $productData['item_size'][0],
                     'size_unit' => $productData['item_size_unit'][0],
                     'additional_notes' => $productData['item_description'][0],
                     'cat_id' => $productData['cat_id'][0],
                     'user_id' => get_session_value('id'),
                     'added_by' => 2
                    ]
                ]);
                $product_id = DB::getPdo()->lastInsertId();
               
            }
            else{
                $product_id=$productData['product_id'][0];
            }

            // echo 'pid :'.$productData['cat_id'][0];
            // p($productData); die;
            


            DB::table('quote_products')->insert([
                ['product_name' => $productData['product_name'][0],
                 'product_id' => $product_id,
                 'quantity' => $productData['item_quantity'][0],
                 'size' => $productData['item_size'][0],
                 'description' => $productData['item_description'][0],
                 'cat_id' => $productData['cat_id'][0],
                 'quote_id' => $this->quotes->id,
                ]
            ]);


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
    public function send_quote_form($id){
        
        $user=Auth::user(); 
        if($user->group_id!=config('constants.groups.admin'))
        return redirect()->back();

        $products=$this->product_categories->with('products')
        ->where('is_active', '=', 1)
        ->get()->toArray();

       $quotesData=$this->quotes
       ->with('quote_products')
       ->with('customer')
       ->with('quote_prices')
       ->with('comments')
       ->where('id', $id)
       ->orderBy('created_at', 'desc')->get()->toArray();
       $quotesData=$quotesData[0];
       //p($quotesData);die;
        return view('adminpanel/send_quote',get_defined_vars());
    }

    public function send_quote_data($id,Request $request){
        $user=Auth::user();
        if($user->group_id!=config('constants.groups.admin'))
        return redirect()->back();

        $validator=$request->validate([
            'quoted_price'=>'required',
        ]);

       

        $this->quote_prices->where('quote_id',$id)->update(['status'=>0]);
        
        $this->quote_prices->quoted_price=$request['quoted_price'];
        $this->quote_prices->extra_charges=$request['extra_charges'];
        $this->quote_prices->reason_for_extra_charges=$request['reason_for_extra_charges'];
        $this->quote_prices->description=$request['description'];
        $this->quote_prices->slug=phpslug('quote_sent');
        $this->quote_prices->quoted_uid=get_session_value('id');
        $this->quote_prices->quote_id=$id;
        $this->quote_prices->save();

        // Update Quote Status
        $this->quotes->where('id',$id)->update(['status'=>config('constants.quote_status.quote_submitted')]);
        // Email Section
      
        $quoteData=$this->quotes->where('id',$id)->with('customer')->get()->toArray();
        $quoteData=$quoteData[0];

        $mailData['body_message']='Following is the detail of the quoation, if you are agree then please click accept button or you can contact us if you have any question';
        $mailData['body_message'] .='<table width="100%" border="1">
        <tr><td>Total Cost :</td><td>'.$request['quoted_price'].'</td></tr>';
        if(isset($request['extra_charges']) && $request['extra_charges']!='')
        $mailData['body_message'] .='<tr><td>Additional Charges :</td><td>'.$request['extra_charges'].'</td></tr>';
        if(isset($request['reason_for_extra_charges']) && $request['reason_for_extra_charges']!='')
        $mailData['body_message'] .='<tr><td>Reason for Additional Charges :</td><td>'.$request['reason_for_extra_charges'].'</td></tr>';
        if(isset($request['description']) && $request['description']!='')
        $mailData['body_message'] .='<tr><td>Additional Notes :</td><td>'.$request['description'].'</td></tr>';
        $mailData['body_message'] .='</table>';
        $mailData['subject']='Quotation about your Request';
     
        $mailData['button_title']='APPROVE';
        $mailData['button_link']=route('customer_action',['quote_id' => $id,'action'=>base64_encode('accept')]);
        $mailData['button_title2']='Reject';
        $mailData['button_link2']=route('customer_action',['quote_id' => $id,'action'=>base64_encode('reject')]);
        

         $emailAdd=[
                    config('constants.admin_email'),
                    $quoteData['customer']['email'],
                    
                ];
               
               

        if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
            $dataArray['emailMsg']='Email Sent Successfully';
        }

            $dataArray['msg']='Mr.'.get_session_value('name').', submitted quote';
            // Activity Logged
            $activityID=log_activity(array(
            'user_id'=>get_session_value('id'),
            'action_taken_on_id'=>$this->quote_prices->id,
            'action_slug'=>'quote_sent',
            'comments'=>'Mr.'.get_session_value('name').' submitted quote',
            'others'=>'quote_prices',
            'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            
            $request->session()->flash('alert-success', 'Quote submitted Successfully !');
            return redirect()->back();
    }

    public function customer_action($quote_id,$action){
        
        $action=base64_decode($action);
        
        $status=config('constants.quote_status.declined');
        $activityComment='Customer Declined the quotation';
        $msg= 'we have canceled this quote for you, let us know if you have any other query. Thank you';
        $actionMsg='declined';

        if($action=='accept'){
            $status=config('constants.quote_status.approved');
            $activityComment='Customer approved the quotation';
            $actionMsg='approved';
            $msg="Thank you, you have been confirmed";
                
        }
        

        $quoteData=$this->quotes->with('customer')
            ->where('id',$quote_id)
            ->get()
            ->toArray();
            $quoteData=$quoteData[0];
        
            
            $updated=$this->quotes->where('id',$quote_id)->where('status',config('constants.quote_status.quote_submitted'))->update(array('status'=>$status));
          
            $activityData=array(
                'user_id'=>$quoteData['customer']['id'],
                'action_taken_on_id'=>$quote_id,
                'action_slug'=>'quote_status_changed_by_customer',
                'comments'=>$activityComment,
                'others'=>'quotes',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);  
            if($updated){
                
                $mailData['body_message']='This email is to let you know that quote having PO No.:'.$quoteData['po_number'].' has '.$actionMsg.' the quote on '.date('d/m/Y');
                $mailData['subject']='Customer Response';
                $toEmail=config('constants.admin_email');

                if(Mail::to($toEmail)->send(new EmailTemplate($mailData))){
                //    echo 'Thank you, Your Booking has been confirmed';
                }
                echo $msg;
            }
            
            else
            echo 'Link expired';
            exit;

        }
        public function calender_schedule(){
            $user=Auth::user();
            if($user->group_id==config('constants.groups.admin')){
              
                    $quotesData=$this->quotes
                    ->with('customer')
                    ->with('driver')
                    ->where('status','>=',config('constants.quote_status.delivery')) 
                    ->where('is_active',1)
                    ->orderBy('created_at', 'desc')->get()->toArray();
                    //->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        
            }
            else{
                $where_clause=['driver_id'=> get_session_value('id')];
                if($user->group_id==config('constants.groups.customer'))
                $where_clause=['customer_id'=> get_session_value('id')];
                 
                $quotesData=$this->quotes
                ->with('customer')
                ->with('driver')
                ->where('status','>=',config('constants.quote_status.delivery')) 
                ->where($where_clause)  
                ->orderBy('created_at', 'desc')->get()->toArray();
                
                    
              }
             
            
            return view('adminpanel/calender_schedule',get_defined_vars());
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
        
        if(isset($req['action']) && $req['action']=='dirver_activity')
        {

            $dataArray['title']='Driver Activity';
            $current_time=time();
            $quoteData=$this->quotes->where('id',$id)->with(array('customer','driver'))->get()->toArray();
            $quoteData=$quoteData[0];

            if(isset($req['activity']) && $req['activity']=='reached_at_pickup'){
                $result=$this->quotes->where('id','=',$id)->update(array('reached_at_pickup'=>$current_time)); 
                $mailData['subject']='Mr.'.$quoteData['driver']['name'].' reached at pick-up having PO#:'.$quoteData['po_number'];  
                $mailData['body_message']='Mr.'.$quoteData['driver']['name'].' reached at pick-up address <strong>'.$quoteData['pickup_street_address'].'</strong> to pick-up the delivery having PO Number'.$quoteData['po_number'].' at '.date('d/m/Y H:i:s',$current_time);  
            }
            
            elseif(isset($req['activity']) && $req['activity']=='picked_up'){
                $result=$this->quotes->where('id','=',$id)->update(array('picked_up'=>$current_time)); 
                $mailData['subject']='Mr.'.$quoteData['driver']['name'].' picked up the delivery having PO#:'.$quoteData['po_number'];  
                $mailData['body_message']='Mr.'.$quoteData['driver']['name'].' picked up the delivery from the address <strong>'.$quoteData['pickup_street_address'].'</strong> having PO Number'.$quoteData['po_number'].' at '.date('d/m/Y H:i:s',$current_time);  
            }
            
            elseif(isset($req['activity']) && $req['activity']=='on_the_way'){
                $result=$this->quotes->where('id','=',$id)->update(array('on_the_way'=>$current_time)); 
                $mailData['subject']='Mr.'.$quoteData['driver']['name'].' on the way to drop-off the devlivery having PO#:'.$quoteData['po_number'];  
                $mailData['body_message']='Mr.'.$quoteData['driver']['name'].' on the way to address <strong>'.$quoteData['drop_off_street_address'].'</strong> to drop-off the delivery having PO Number'.$quoteData['po_number'].' at '.date('d/m/Y H:i:s',$current_time);  
            }
            
            elseif(isset($req['activity']) && $req['activity']=='reached_at_dropoff'){
                $result=$this->quotes->where('id','=',$id)->update(array('reached_at_dropoff'=>$current_time));
                $mailData['subject']='Mr.'.$quoteData['driver']['name'].' reached at drop-off on address for delivery having PO#:'.$quoteData['po_number'];  
                $mailData['body_message']='Mr.'.$quoteData['driver']['name'].' reached at address <strong>'.$quoteData['drop_off_street_address'].'</strong> to drop-off the delivery having PO Number'.$quoteData['po_number'].' at '.date('d/m/Y H:i:s',$current_time);               
            }
            
            elseif(isset($req['activity']) && $req['activity']=='delivered'){
                $result=$this->quotes->where('id','=',$id)->update(array('delivered'=>$current_time,'status'=>config('constants.quote_status.complete')));             
                $mailData['subject']='Mr.'.$quoteData['driver']['name'].' delivered the delivery having PO#:'.$quoteData['po_number'];  
                $mailData['body_message']='Mr.'.$quoteData['driver']['name'].' delivered the delivery at address <strong>'.$quoteData['drop_off_street_address'].'</strong> having PO Number'.$quoteData['po_number'].' on '.date('d/m/Y H:i:s',$current_time);  
            }
            else{
                $dataArray['error']='Yes';   
                echo json_encode($dataArray); exit;
            }
            
            

        // Email Section

         $emailAdd=[
                    config('constants.admin_email'),
                    $quoteData['customer']['email'],
                    $quoteData['driver']['email'],
                ];
               

        if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
            $dataArray['emailMsg']='Email Sent Successfully';
            $req->session()->flash('alert-success', 'Email Notification sent');
        }
    //


            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', Performed activity for delivery';
                  // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'driver_delivery_activity',
                'comments'=>'Mr.'.get_session_value('name').' performed activity for delivery',
                'others'=>'quotes',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            }
            
            else{
                $dataArray['error']='Yes';
                $dataArray['msg']='There is something missing ! Please fill all the required fields and try again';
            }
        }
       else if(isset($req['action']) && $req['action']=='change_quote_status'){
        $dataArray['title']='Quote Status';
        
        $activityComment='Mr.'.get_session_value('name').' declined the quote';
        $status=config('constants.quote_status.declined');
        $action=base64_decode($req['status']);
        
        if($action=='approved'){
            $status=config('constants.quote_status.approved');
            $activityComment='Mr.'.get_session_value('name').' approved the quote';
        }

        $result=$this->quotes->where('id','=',$id)->update(array('status'=>$status));  
            if($result){
                $dataArray['msg']=$activityComment.' successfully!';
                // Activity Logged
            $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'quote_status',
                'comments'=>$activityComment,
                'others'=>'quotes',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            }
       }
       else if(isset($req['action']) && $req['action']=='restore')
        {
            $dataArray['title']='Record Restored';
            $result=$this->quotes->where('id','=',$id)->update(array('is_active'=>1));             
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', Record Restored successfully!';
                  // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'quote_restored',
                'comments'=>'Mr.'.get_session_value('name').' restored record of quote',
                'others'=>'quotes',
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
            $result=$this->quotes->where('id','=',$id)->update(array('is_active'=>2));             
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', Record Deleted successfully!';
                // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'quote_deleted',
                'comments'=>'Mr.'.get_session_value('name').' deleted record',
                'others'=>'quotes',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            }
            
            else{
                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
            }

        }
        if(isset($req['action']) && $req['action']=='submit_comment'){ 
            
            // p($req->all()); die;

            $this->comments->comment=$req['data']['comment'];
            $this->comments->user_id=get_session_value('id');
            $this->comments->group_id =$req['data']['group_id'];
            $this->comments->slug =$req['data']['slug'];
            //$this->comments->slug =$req['data']['user_name'];
            $this->comments->quote_id =$id;
            $this->comments->status =1;
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
        $quoteData=$this->quotes->where('id',$id)->with('customer')->get()->toArray();
        $quoteData=$quoteData[0];
        $mailData['body_message']='There was a new note added to the quote of '.$quoteData['customer']['name'].' for event '.date(config('constants.date_formate'));
        $mailData['subject']='New note added to quote';

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
                'action_slug'=>'comment_added',
                'comments'=>$activityComment,
                'others'=>'booking_actions',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);
        }
      
        echo json_encode($dataArray);
        die;
    }

}
