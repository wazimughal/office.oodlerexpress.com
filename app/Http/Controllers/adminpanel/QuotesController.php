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
use App\Models\adminpanel\pickup_dropoff_address;
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
        $this->pickup_dropoff_address= new pickup_dropoff_address;
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
      public function report_quote_delivery(Request $req){
        $user=Auth::user();
        
        $where_clause=[
            ['status', '>=', config('constants.quote_status.pending')],
            ['is_active', '=', 1],
        ];
        
        $pagination_per_page=config('constants.per_page');
        // if(isset($req->action) && $req->action=='search_form')
        // $pagination_per_page=200;
        

        if($user->group_id!=config('constants.groups.admin'))
        abort(403, sprintf('Only ADMIN is allowed')); 

        $where_in_clause=$customer_ids=$driver_ids=$quote_status=array();
//p($req->all());

        if(isset($req->customer_id) && !empty($req->customer_id)){
        $customer_ids=$req->customer_id;
        $where_in_clause['customer_id']=$customer_ids;
        }
        
        if(isset($req->driver_id) && !empty($req->driver_id)){
            $driver_ids=$req->driver_id;
            $where_in_clause['driver_id']=$driver_ids;
        }
        
        if(isset($req->quote_status) && !empty($req->quote_status)){
            $quote_status=$req->quote_status;
            $where_in_clause['status']=$quote_status;
        }
       
        
        //p($where_in_clause); die;

            $quotes=$this->quotes
            ->with('quote_products')
            ->with('customer')
            ->with('driver')
           //->where($where_clause)
            ->orderBy('created_at', 'desc');

            foreach($where_in_clause as $column => $values){
                
                $quotes = $quotes->whereIn($column, $values);
            }

            // if(isset($where_in_clause['customer_id']) && count($where_in_clause['customer_id'])>0){
                
            //     $quotes=$quotes->wherein('customer_id',$where_in_clause['customer_id']);
            // }
            
            $quotesData=$quotes->paginate($pagination_per_page);

            // $query=$quotes->toSql();
            // p($query); die;

            return view('adminpanel.reports_quotes_deliveries',get_defined_vars());
        
      }
      public function customer_quotes($customer_id){
        $user=Auth::user();
        if($user->group_id!=config('constants.groups.admin'))
            abort(403, sprintf('Only ADMIN is allowed')); 
           
            $quotesData=$this->quotes
            ->with('quote_products')
            ->with('customer')
            ->with('driver')
            ->where('is_active', 1)
            ->where('customer_id', $customer_id)
            ->where('status', '>=', config('constants.quote_status.pending'))
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
       
        
        return view('adminpanel.customer_quotes',get_defined_vars());
        
      }
      public function view_delivery($id){

        $user=Auth::user();
        $products=$this->product_categories->with('products')
            ->where('is_active', '=', 1)
            ->get()->toArray();
    
           $quotesData=$this->quotes
           ->with('quote_products')
           ->with('customer')
           ->with('delivery_proof')
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
      public function view_quote($id){

        $user=Auth::user();
        $products=$this->product_categories->with('products')
            ->where('is_active', '=', 1)
            ->get()->toArray();
    
           $quotesData=$this->quotes
           ->with('quote_products')
           ->with('customer')
           ->with('delivery_proof')
           ->with('driver')
           ->with('quote_prices')
           ->with('comments')
           ->where('id', $id)
           ->where('is_active', 1)
           ->where('status','>=', config('constants.quote_status.pending'))
           ->orderBy('created_at', 'desc')->get()->toArray();
           if(empty($quotesData)){
            echo 'There is something wrong'; die;
           }
           
           $quotesData=$quotesData[0];
        //p($quotesData); die;
        return view('adminpanel.view_quote',get_defined_vars());

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
                
            $quoteData=$this->quotes->where('id',$id)->with('customer')->get()->toArray();
            $quoteData=$quoteData[0];

            $mailData['body_message']='A new delivery with PO Number:'.$quoteData['po_number'].' was scheduled to be picked up and delivered on '.$quoteData['drop_off_date'].' if you have any additional questions please feel free to contact us at 718-218-5239';
            $mailData['subject']=':   Your delivery with Oodler Express';
            
            $emailAdd=[
                //config('constants.admin_email'),
                $quoteData['customer']['email'],
                
            ];
        
            if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
                $dataArray['emailMsg']='Email Sent Successfully';
            }

            $mailData['body_message']='You are assigned a new delivery, having PO Number:'.$request['po_number'].'. Please login to the CRM and look for details';
            $mailData['subject']='New Delivery Assigned';

            $emailAdd=[
                        //config('constants.admin_email'),
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
       
       $quotesData=$this->quotes
       ->with('quote_products')
       ->where('id', $id)
       ->orderBy('created_at', 'desc')->get()->toArray();
       $quotesData=$quotesData[0];
        
        //p($quotesData); die;

       $customer_id=$quotesData['customer_id'];

       $customer_products=array();

       $customer_products=$this->product_categories->with('products')
       ->where('is_active', '=', 1)
       ->get()->toArray();

       if(isset($customer_id) && $customer_id>0){
        $customer_cat=$this->users->where('id',$customer_id)->get('shipping_cat')->toArray();
        $customer_cat=$customer_cat[0];
        
        $customer_cat=json_decode($customer_cat['shipping_cat'],true);

        $customer_products=$this->product_categories->with('products')->wherein('id',$customer_cat)
        ->where('is_active', '=', 1)
        ->get()->toArray();
        
        
       }
       
      // Check if 2nd Pick Up Address set
      $pickup_dropoff2 = $this->quote_products->where(['quote_id'=>$id,'pickup_dropoff_order_number'=>2])->orderBy('id', 'asc')->get('id')->toArray();
      $pickup_dropoff3= $this->quote_products->where(['quote_id'=>$id,'pickup_dropoff_order_number'=>3])->orderBy('id', 'asc')->get('id')->toArray();
 
      $pickup_dropoff2_flag=$pickup_dropoff3_flag=false;
      if(count($pickup_dropoff2)>0)
      $pickup_dropoff2_flag=true;
      if(count($pickup_dropoff3)>0)
      $pickup_dropoff3_flag=true;
      
    //   if($pickup_dropoff2_flag)
    //   echo 'flag 2';
    //   if($pickup_dropoff3_flag)
    //   echo 'flag 3';

       
       //p($quotesData); die;
        return view('adminpanel/edit_quote',get_defined_vars());
    }
    public function save_quote_edit($id,Request $request){
       
        $validator=$request->validate([
            'quote_type'=>'required',
            'business_type'=>'required',
            'po_number'=>'required',
            'pickup_street_address1'=>'required',
            'pickup_contact_number1'=>'required',
            'pickup_date1'=>'required',
            'drop_off_street_address'=>'required',
            'drop_off_contact_number'=>'required',
            'drop_off_date'=>'required',
            
        ]);
        
         
        $to_update_date['quote_type']=$request['quote_type'];
        $to_update_date['elevator']=$request['elevator'];
        $to_update_date['no_of_appartments']=$request['no_of_appartments'];
        $to_update_date['list_of_floors']=json_encode($request['list_of_floors']);
        $to_update_date['business_type']=$request['business_type'];
        $to_update_date['po_number']=$request['po_number'];
        $to_update_date['pickup_street_address']=$request['pickup_street_address1'];
        $to_update_date['pickup_unit']=$request['pickup_unit1'];
        $to_update_date['pickup_state']=$request['pickup_state1'];
        $to_update_date['pickup_city']=$request['pickup_city1'];
        $to_update_date['pickup_zipcode']=$request['pickup_zipcode1'];
        $to_update_date['pickup_contact_number']=$request['pickup_contact_number1'];
        $to_update_date['pickup_date']=$request['pickup_date1'];
        
        $to_update_date['drop_off_street_address']=$request['drop_off_street_address'];
        $to_update_date['drop_off_unit']=$request['drop_off_unit'];
        $to_update_date['drop_off_state']=$request['drop_off_state'];
        $to_update_date['drop_off_city']=$request['drop_off_city'];
        $to_update_date['drop_off_zipcode']=$request['drop_off_zipcode'];
        $to_update_date['drop_off_contact_number']=$request['drop_off_contact_number'];
        $to_update_date['drop_off_instructions']=$request['drop_off_instructions'];
        $to_update_date['drop_off_date']=$request['drop_off_date'];
        
        $to_update_date['customer_id']=get_session_value('id');
        if(isset($request['customer_id']) && $request['customer_id']>0)
        $to_update_date['customer_id']=$request['customer_id'];
        
       $this->quotes->where('id',$id)->update($to_update_date);
       $this->quote_products->where('quote_id',$id)->delete();
       $this->pickup_dropoff_address->where('quote_id',$id)->delete();

       

       foreach($request['product_details1'] as $key=>$productData){
            
        if(isset($productData['product_name']) && count($productData['product_name'])>0){

            DB::table('pickup_dropoff_address')->insert([
                ['pickup_street_address' => $request['pickup_street_address1'],
                 'pickup_state' =>$request['pickup_state1'],
                 'pickup_city' => $request['pickup_city1'],
                 'pickup_zipcode' => $request['pickup_zipcode1'],
                 'pickup_contact_number' => $request['pickup_contact_number1'],
                 'pickup_date' => $request['pickup_date1'],
                 'drop_off_street_address' => $request['drop_off_street_address'],
                 'drop_off_unit' => $request['drop_off_unit'],
                 'drop_off_state' => $request['drop_off_state'],
                 'drop_off_city' => $request['drop_off_city'],
                 'drop_off_zipcode' => $request['drop_off_zipcode'],
                 'drop_off_contact_number' => $request['drop_off_contact_number'],
                 'drop_off_instructions' => $request['drop_off_instructions'],
                 'drop_off_date' => $request['drop_off_date'],
                 'quote_id' => $id,
                ]
            ]);
                $pickup_dropoff_id = DB::getPdo()->lastInsertId();

            
            $total_products=count($productData['product_name']);
            for($i=0; $i<$total_products; $i++){
                
                //if(!isset($productData['product_name'][$i])) // If Product Name is missing the ignore it
                  //  continue;
                    
                DB::table('quote_products')->insert([
                    ['product_name' => $productData['product_name'][$i],
                     'product_id' =>$productData['product_id'][$i],
                     'quantity' => $productData['item_quantity'][$i],
                     'size' => $productData['product_sizes'][$i],
                     'size_slug' => phpslug($productData['product_sizes'][$i]),
                     'description' => $productData['item_description'][$i],
                     'cat_id' => $productData['cat_id'][$i],
                     'quote_id' => $id,
                     'pickup_dropoff_id' => $pickup_dropoff_id,
                     'pickup_dropoff_order_number' => 1,
                    ]
                ]);

            }
           
        }
        
    }

    // If New Pick Up address is added 2
    if(isset($request['pickup_date2']) && ($request['pickup_date2'])!='')
    foreach($request['product_details2'] as $key=>$productData){
            
        if(isset($productData['product_name']) && count($productData['product_name'])>0){

            DB::table('pickup_dropoff_address')->insert([
                ['pickup_street_address' => $request['pickup_street_address2'],
                 'pickup_state' =>$request['pickup_state2'],
                 'pickup_city' => $request['pickup_city2'],
                 'pickup_zipcode' => $request['pickup_zipcode2'],
                 'pickup_contact_number' => $request['pickup_contact_number2'],
                 'pickup_date' => $request['pickup_date2'],
                 'drop_off_street_address' => $request['drop_off_street_address'],
                 'drop_off_unit' => $request['drop_off_unit'],
                 'drop_off_state' => $request['drop_off_state'],
                 'drop_off_city' => $request['drop_off_city'],
                 'drop_off_zipcode' => $request['drop_off_zipcode'],
                 'drop_off_contact_number' => $request['drop_off_contact_number'],
                 'drop_off_instructions' => $request['drop_off_instructions'],
                 'drop_off_date' => $request['drop_off_date'],
                 'quote_id' => $id,
                ]
            ]);
                $pickup_dropoff_id = DB::getPdo()->lastInsertId();

            $total_products=count($productData['product_name']);
            for($i=0; $i<$total_products; $i++){
                
                //if(!isset($productData['product_name'][$i])) // If Product Name is missing the ignore it
                //continue;

                DB::table('quote_products')->insert([
                    ['product_name' => $productData['product_name'][$i],
                     'product_id' =>$productData['product_id'][$i],
                     'quantity' => $productData['item_quantity'][$i],
                     'size' => $productData['product_sizes'][$i],
                     'size_slug' => phpslug($productData['product_sizes'][$i]),
                     'description' => $productData['item_description'][$i],
                     'cat_id' => $productData['cat_id'][$i],
                     'quote_id' => $id,
                     'pickup_dropoff_id' => $pickup_dropoff_id,
                     'pickup_dropoff_order_number' => 2,
                    ]
                ]);

            }
           
        }
        
    }

    // If New Pick Up address is added 3
    if(isset($request['pickup_date3']) && ($request['pickup_date3'])!='')
    foreach($request['product_details3'] as $key=>$productData){
            
        if(isset($productData['product_name']) && count($productData['product_name'])>0){

            DB::table('pickup_dropoff_address')->insert([
                ['pickup_street_address' => $request['pickup_street_address3'],
                 'pickup_state' =>$request['pickup_state3'],
                 'pickup_city' => $request['pickup_city3'],
                 'pickup_zipcode' => $request['pickup_zipcode3'],
                 'pickup_contact_number' => $request['pickup_contact_number3'],
                 'pickup_date' => $request['pickup_date3'],
                 'drop_off_street_address' => $request['drop_off_street_address'],
                 'drop_off_unit' => $request['drop_off_unit'],
                 'drop_off_state' => $request['drop_off_state'],
                 'drop_off_city' => $request['drop_off_city'],
                 'drop_off_zipcode' => $request['drop_off_zipcode'],
                 'drop_off_contact_number' => $request['drop_off_contact_number'],
                 'drop_off_instructions' => $request['drop_off_instructions'],
                 'drop_off_date' => $request['drop_off_date'],
                 'quote_id' => $id,
                ]
            ]);
                $pickup_dropoff_id = DB::getPdo()->lastInsertId();

            $total_products=count($productData['product_name']);
            for($i=0; $i<$total_products; $i++){
                
                //if(!isset($productData['product_name'][$i])) // If Product Name is missing the ignore it
                //continue;

                DB::table('quote_products')->insert([
                    ['product_name' => $productData['product_name'][$i],
                     'product_id' =>$productData['product_id'][$i],
                     'quantity' => $productData['item_quantity'][$i],
                     'size' => $productData['product_sizes'][$i],
                     'size_slug' => phpslug($productData['product_sizes'][$i]),
                     'description' => $productData['item_description'][$i],
                     'cat_id' => $productData['cat_id'][$i],
                     'quote_id' => $id,
                     'pickup_dropoff_id' => $pickup_dropoff_id,
                     'pickup_dropoff_order_number' => 3,
                    ]
                ]);

            }
           
        }
        
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
    public function request_quotes_form($customer_id=NULL){
       $user=Auth::user(); 

       $customer_products=array();

       $customer_products=$this->product_categories->with('products')
       ->where('is_active', '=', 1)
       ->get()->toArray();

       if(isset($customer_id) && $customer_id>0){
        $customer_cat=$this->users->where('id',$customer_id)->get('shipping_cat')->toArray();
        $customer_cat=$customer_cat[0];
        $customer_cat=json_decode($customer_cat['shipping_cat'],true);

        $customer_products=$this->product_categories->with('products')->wherein('id',$customer_cat)
        ->where('is_active', '=', 1)
        ->get()->toArray();
        
        
       }
  
        return view('adminpanel/request_quote',get_defined_vars());
    }
    public function save_quote_data(Request $request){
       
        $validator=$request->validate([
            'quote_type'=>'required',
            'business_type'=>'required',
            'po_number'=>'required',
            'pickup_street_address1'=>'required',
            'pickup_contact_number1'=>'required',
            'pickup_date1'=>'required',
            'drop_off_street_address'=>'required',
            'drop_off_contact_number'=>'required',
            'drop_off_date'=>'required',
        ]);
        
         
        $this->quotes->quote_type=$request['quote_type'];
        $this->quotes->elevator=$request['elevator'];
        $this->quotes->no_of_appartments=$request['no_of_appartments'];
        $this->quotes->list_of_floors=json_encode($request['list_of_floors']);
        $this->quotes->business_type=$request['business_type'];
        $this->quotes->po_number=$request['po_number'];
       
        $this->quotes->pickup_street_address=$request['pickup_street_address1'];
        $this->quotes->pickup_unit=$request['pickup_unit1'];
        $this->quotes->pickup_state=$request['pickup_state1'];
        $this->quotes->pickup_city=$request['pickup_city1'];
        $this->quotes->pickup_zipcode=$request['pickup_zipcode1'];
        $this->quotes->pickup_contact_number=$request['pickup_contact_number1'];
        $this->quotes->pickup_date=$request['pickup_date1'];
        $this->quotes->drop_off_street_address=$request['drop_off_street_address'];
        $this->quotes->drop_off_unit=$request['drop_off_unit'];
        $this->quotes->drop_off_state=$request['drop_off_state'];
        $this->quotes->drop_off_city=$request['drop_off_city'];
        $this->quotes->drop_off_zipcode=$request['drop_off_zipcode'];
        $this->quotes->drop_off_contact_number=$request['drop_off_contact_number'];
        $this->quotes->drop_off_instructions=$request['drop_off_instructions'];
        $this->quotes->drop_off_date=$request['drop_off_date'];
        

        $this->quotes->customer_id=get_session_value('id');
        if(isset($request['customer_id']) && $request['customer_id']>0)
        $this->quotes->customer_id=$request['customer_id'];
        
        

        $this->quotes->created_at=time();

       $this->quotes->save();
       


       foreach($request['product_details1'] as $key=>$productData){
            
        if(isset($productData['product_name']) && count($productData['product_name'])>0){

            DB::table('pickup_dropoff_address')->insert([
                ['pickup_street_address' => $request['pickup_street_address1'],
                 'pickup_state' =>$request['pickup_state1'],
                 'pickup_city' => $request['pickup_city1'],
                 'pickup_zipcode' => $request['pickup_zipcode1'],
                 'pickup_contact_number' => $request['pickup_contact_number1'],
                 'pickup_date' => $request['pickup_date1'],
                 'drop_off_street_address' => $request['drop_off_street_address'],
                 'drop_off_unit' => $request['drop_off_unit'],
                 'drop_off_state' => $request['drop_off_state'],
                 'drop_off_city' => $request['drop_off_city'],
                 'drop_off_zipcode' => $request['drop_off_zipcode'],
                 'drop_off_contact_number' => $request['drop_off_contact_number'],
                 'drop_off_instructions' => $request['drop_off_instructions'],
                 'drop_off_date' => $request['drop_off_date'],
                 'quote_id' => $this->quotes->id,
                ]
            ]);
                $pickup_dropoff_id = DB::getPdo()->lastInsertId();

            
            $total_products=count($productData['product_name']);
            for($i=0; $i<$total_products; $i++){
                
                //if(!isset($productData['product_name'][$i])) // If Product Name is missing the ignore it
                  //  continue;
                    
                DB::table('quote_products')->insert([
                    ['product_name' => $productData['product_name'][$i],
                     'product_id' =>$productData['product_id'][$i],
                     'quantity' => $productData['item_quantity'][$i],
                     'size' => $productData['product_sizes'][$i],
                     'size_slug' => phpslug($productData['product_sizes'][$i]),
                     'description' => $productData['item_description'][$i],
                     'cat_id' => $productData['cat_id'][$i],
                     'quote_id' => $this->quotes->id,
                     'pickup_dropoff_id' => $pickup_dropoff_id,
                     'pickup_dropoff_order_number' => 1,
                    ]
                ]);

            }
           
        }
        
    }

    // If New Pick Up address is added 2
    if(isset($request['pickup_date2']) && ($request['pickup_date2'])!='')
    foreach($request['product_details2'] as $key=>$productData){
            
        if(isset($productData['product_name']) && count($productData['product_name'])>0){

            DB::table('pickup_dropoff_address')->insert([
                ['pickup_street_address' => $request['pickup_street_address2'],
                 'pickup_state' =>$request['pickup_state2'],
                 'pickup_city' => $request['pickup_city2'],
                 'pickup_zipcode' => $request['pickup_zipcode2'],
                 'pickup_contact_number' => $request['pickup_contact_number2'],
                 'pickup_date' => $request['pickup_date2'],
                 'drop_off_street_address' => $request['drop_off_street_address'],
                 'drop_off_unit' => $request['drop_off_unit'],
                 'drop_off_state' => $request['drop_off_state'],
                 'drop_off_city' => $request['drop_off_city'],
                 'drop_off_zipcode' => $request['drop_off_zipcode'],
                 'drop_off_contact_number' => $request['drop_off_contact_number'],
                 'drop_off_instructions' => $request['drop_off_instructions'],
                 'drop_off_date' => $request['drop_off_date'],
                 'quote_id' => $this->quotes->id,
                ]
            ]);
                $pickup_dropoff_id = DB::getPdo()->lastInsertId();

            $total_products=count($productData['product_name']);
            for($i=0; $i<$total_products; $i++){
                
                //if(!isset($productData['product_name'][$i])) // If Product Name is missing the ignore it
                //continue;

                DB::table('quote_products')->insert([
                    ['product_name' => $productData['product_name'][$i],
                     'product_id' =>$productData['product_id'][$i],
                     'quantity' => $productData['item_quantity'][$i],
                     'size' => $productData['product_sizes'][$i],
                     'size_slug' => phpslug($productData['product_sizes'][$i]),
                     'description' => $productData['item_description'][$i],
                     'cat_id' => $productData['cat_id'][$i],
                     'quote_id' => $this->quotes->id,
                     'pickup_dropoff_id' => $pickup_dropoff_id,
                     'pickup_dropoff_order_number' => 2,
                    ]
                ]);

            }
           
        }
        
    }

    // If New Pick Up address is added 3
    if(isset($request['pickup_date3']) && ($request['pickup_date3'])!='')
    foreach($request['product_details3'] as $key=>$productData){
            
        if(isset($productData['product_name']) && count($productData['product_name'])>0){

            DB::table('pickup_dropoff_address')->insert([
                ['pickup_street_address' => $request['pickup_street_address3'],
                 'pickup_state' =>$request['pickup_state3'],
                 'pickup_city' => $request['pickup_city3'],
                 'pickup_zipcode' => $request['pickup_zipcode3'],
                 'pickup_contact_number' => $request['pickup_contact_number3'],
                 'pickup_date' => $request['pickup_date3'],
                 'drop_off_street_address' => $request['drop_off_street_address'],
                 'drop_off_unit' => $request['drop_off_unit'],
                 'drop_off_state' => $request['drop_off_state'],
                 'drop_off_city' => $request['drop_off_city'],
                 'drop_off_zipcode' => $request['drop_off_zipcode'],
                 'drop_off_contact_number' => $request['drop_off_contact_number'],
                 'drop_off_instructions' => $request['drop_off_instructions'],
                 'drop_off_date' => $request['drop_off_date'],
                 'quote_id' => $this->quotes->id,
                ]
            ]);
                $pickup_dropoff_id = DB::getPdo()->lastInsertId();

            $total_products=count($productData['product_name']);
            for($i=0; $i<$total_products; $i++){
                
                //if(!isset($productData['product_name'][$i])) // If Product Name is missing the ignore it
                //continue;

                DB::table('quote_products')->insert([
                    ['product_name' => $productData['product_name'][$i],
                     'product_id' =>$productData['product_id'][$i],
                     'quantity' => $productData['item_quantity'][$i],
                     'size' => $productData['product_sizes'][$i],
                     'size_slug' => phpslug($productData['product_sizes'][$i]),
                     'description' => $productData['item_description'][$i],
                     'cat_id' => $productData['cat_id'][$i],
                     'quote_id' => $this->quotes->id,
                     'pickup_dropoff_id' => $pickup_dropoff_id,
                     'pickup_dropoff_order_number' => 3,
                    ]
                ]);

            }
           
        }
        
    }
        
        $mailData['body_message']='you have received a new request for a quote from customer '.quote_data_for_mail($this->quotes->id);
        $mailData['subject']='You have a new quote request';
         $toEmail=[
            config('constants.admin_email')
         ];
        if(Mail::to($toEmail)->send(new EmailTemplate($mailData)))
         $request->session()->flash('alert-info', 'Email Notification also sent to Admin ');


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
        $mailData['subject']=': Your New Quote From Oodler Express';
     
        $mailData['button_title']='APPROVE';
        $mailData['button_link']=route('customer_action',['quote_id' => $id,'action'=>base64_encode('accept')]);
        $mailData['button_title2']='Reject';
        $mailData['button_link2']=route('customer_action',['quote_id' => $id,'action'=>base64_encode('reject')]);
        

        //$mailData['body_message']='you have received a new request for a quote from customer <br>'.quote_data_for_mail($this->quotes->id);;
       
         

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


    public function upload_delivery_proof($id,Request $request){
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
            //$this->files->slug=phpslug($imageName);
            $this->files->slug='proof_of_delivery';
            $this->files->path=url('uploads').'/'.$imageName;
            //$this->files->description=$orginalImageName.' file uploaded';
            $this->files->description=phpslug($imageName);
            $this->files->otherinfo=$imageExt;
            $this->files->user_id=get_session_value('id');
            $this->files->quote_id=$id;
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
    public function customer_action($quote_id,$action){
        
        $action=base64_decode($action);
        
        $status=config('constants.quote_status.declined');
        $activityComment='Customer Declined the quotation';
        $msg= 'we have canceled this quote for you, let us know if you have any other query. Thank you';
        $actionMsg='declined';
        $mailData['subject']='New Quote Declined ';

        if($action=='accept'){
            $status=config('constants.quote_status.approved');
            $activityComment='Customer approved the quotation';
            $actionMsg='approved';
            $msg="Thank you, you have been confirmed";
            $mailData['subject']='New Quote Accepted ';  
            
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
                $toEmail=[
                    config('constants.admin_email'),
                    $quoteData['customer']['email']
                ];

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
             //p($quotesData); die;
            
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
                
                $deliveryData=$this->quotes->where('id','=',$id)->with('delivery_proof')->get()->toArray();
                $deliveryData=$deliveryData[0];
                if(empty($deliveryData['delivery_proof'])){
                    $dataArray['error']='Yes'; 
                    $dataArray['title']='Please upload the proof of delivery first !'; 
                    echo json_encode($dataArray); exit;
                }
                else{
                    $result=$this->quotes->where('id','=',$id)->update(array('delivered'=>$current_time,'status'=>config('constants.quote_status.complete')));             
                    $mailData['subject']='Mr.'.$quoteData['driver']['name'].' delivered the delivery having PO#:'.$quoteData['po_number'];  
                    $mailData['body_message']='Mr.'.$quoteData['driver']['name'].' delivered the delivery at address <strong>'.$quoteData['drop_off_street_address'].'</strong> having PO Number'.$quoteData['po_number'].' on '.date('d/m/Y H:i:s',$current_time);  
                    
                 

                    $uploadingPath=base_path().'/public/uploads';
                    if(base_path()!='/Users/waximarshad/office.oodlerexpress.com')
                    $uploadingPath=base_path().'/public_html/uploads';
                    //$filePath=$uploadingPath.'/'.$deliveryData['description'];
                    $files=array();
                    foreach($deliveryData['delivery_proof'] as $filesData){
                        $files[]=$uploadingPath.'/'.$filesData['description'];
                    }
                   
                    $emailAdd=[
                        config('constants.admin_email'),
                        $quoteData['customer']['email'],
                        $quoteData['driver']['email'],
                    ];
                        $mailData["email"] = $emailAdd;
                        
                        Mail::send('emails.delivered', $mailData, function($message)use($mailData, $files) {
                        $message->to($mailData["email"])
                        ->subject($mailData["subject"]);

                        foreach ($files as $file){
                        $message->attach($file);
                        }            
                        });

                        $dataArray['error']='No'; 
                        echo json_encode($dataArray); exit;

                }

                
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
       else if(isset($req['action']) && $req['action']=='change_status'){
        $dataArray['title']='Quote Status';
        
        $activityComment='Mr.'.get_session_value('name').' changed the quote status';
        $status=($req['current_status']);
        
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
        elseif(isset($req['action']) && $req['action']=='delteFile'){ 
            $dataArray['title']='ٖFile deleted';
            
            $file_id=$req['file_id'];

            $fileData=$this->files->where('id','=',$file_id)->get()->toArray();
            if($fileData){
                $fileData=$fileData[0];
//p($fileData);
        //$uploadingPath=public_path('uploads');
        $uploadingPath=base_path().'/public/uploads';
        if(base_path()!='/Users/waximarshad/office.oodlerexpress.com')
        $uploadingPath=base_path().'/public_html/uploads';

              $filePath=$uploadingPath.'/'.$fileData['description'];
              
                unlink($filePath);
                
           
                $file=$this->files->where('id', $file_id)->delete();
                $dataArray['msg']='Mr.'.get_session_value('name').', deleted  '.$fileData['name'].' successfully!';
                $activityComment=$fileData['name'].' File delted ';
                $activityData=array(
                    'user_id'=>get_session_value('id'),
                    'action_taken_on_id'=>$id,
                    'action_slug'=>'proof_of_devlivery_file_deleted',
                    'comments'=>$activityComment,
                    'others'=>'quote',
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
        elseif(isset($req['action']) && $req['action']=='submit_comment'){ 
            
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
