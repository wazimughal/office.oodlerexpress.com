<?php

if(!function_exists('p')){
    function p($data){ 
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}
if(!function_exists('get_formatted_date')){
    function get_formatted_date($data,$formate){
        $formattedDate= date($formate, strtotime($date));
        return $formattedDate; 
    }
}

if(!function_exists('sub_status_msg')){
    function sub_status_msg($status){
        $msg='Pending';
        if($status==1)
        $msg='Accepted';
        else if($status==2)
        $msg='Rejected';
        return $msg;
    }
}
if(!function_exists('days_option')){
    function days_option($selected_day=''){
        $options='';
        for($i=1;$i<32; $i++){
            $selected='';
            if($selected_day==$i){
                $selected='selected';
            }
            $options .='<option '.$selected.' value="'.$i.'">'.$i.'</option>';
        }
     return $options; 
    }
}
if(!function_exists('months_option')){
    function months_option($selected_day=''){
        $options='';
        for($i=1;$i<13; $i++){
            $selected='';
            if($selected_day==$i){
                $selected='selected';
            }
            $options .='<option '.$selected.' value="'.$i.'">'.$i.'</option>';
        }
     return $options; 
    }
}
if(!function_exists('years_option')){
    function years_option($selected_day=''){
        $options='';
        $years=date('y', time());
        $endyear=$years+50;

        for($i=$years;$i<$endyear; $i++){
            $selected='';
            if($selected_day==$i){
                $selected='selected';
            }
            $options .='<option '.$selected.' value="'.$i.'">'.$i.'</option>';
        }
     return $options; 
    }
}

if(!function_exists('quote_status_msg')){
    function quote_status_msg($status){
        $msg='Submit Quote';
        if($status==config('constants.quote_status.pending'))
        $msg='Quote in Pending';
        elseif($status==config('constants.quote_status.quote_submitted'))
        $msg='Quote submitted and Waiting for Customer Response';
        elseif($status==config('constants.quote_status.approved'))
        $msg='Quote approved by the customer';
        elseif($status==config('constants.quote_status.declined'))
        $msg='Quote declined by the customer';
        elseif($status==config('constants.quote_status.trashed'))
        $msg='Quote is in Trash';
        elseif($status==config('constants.quote_status.delivery'))
        $msg='Confirmed Delivery, Driver need to deliver this!';
        elseif($status==config('constants.quote_status.complete'))
        $msg='Successfully delivered ';
        return $msg;
    }
}

if(!function_exists('get_session_value')){
    function get_session_value($key=NULL){

        $userData=session()->get('userData'); 
        
        if($key==NULL)
        return $userData;
        return $userData[$key];
    }
}

if(!function_exists('phpslug')){
    function phpslug($string)
    {
        $slug = preg_replace('/[-\s]+/', '_', strtolower(trim($string)));
        return $slug;
    }
}
if(!function_exists('userNameByGroupID')){
    function userNameByGroupID($group_id){
        switch ($group_id) {
            case 1:
             return 'Admin';
              break;
            case 2:
                return 'Customer';
              break;
            case 3:
                return 'Driver';
              break;
            case 4:
                return 'Staff';
              break;
            case 5:
                return 'Lead';
              break;
            default:
              return 'invalid';
          }
    }
}
if(!function_exists('getGroups')){
    function getGroups(){
        $userGroups = App\Models\adminpanel\Groups::orderBy('created_at', 'desc')->where('id','!=',config('constants.groups.admin'))->get();
        if($userGroups)
        return $userGroups->toArray();
        
        return array();
    }
}
if(!function_exists('getUsersByGroupId')){
    function getUsersByGroupId($group_id){
        $userData = App\Models\adminpanel\Users::orderBy('created_at', 'desc')->where('group_id','=',$group_id)->get();
        if($userData){
            $userData=$userData->toArray();
            return $userData;
        }
        return array();
    }
}

if(!function_exists('getAllGroups')){
    function getAllGroups(){
        $userGroups = App\Models\adminpanel\Groups::orderBy('created_at', 'desc')->get();
        if($userGroups)
        return $userGroups->toArray();
        
        return array();
    }
}
if(!function_exists('generateInvoiceNumber')){
    function generateInvoiceNumber(){
        
        $quotes = App\Models\adminpanel\quotes::orderBy('id', 'desc')->get()->first();
       // p($quotes);
         $today=date('mdy');
        if($quotes->id>0){
            $quotes_id=$quotes->id;
            //$quotes_id=$quotes_id+1;
           return $invoice_number = sprintf('%07d', $quotes_id);
           //return $invoice_no=$today.$invoices_id;
        }
        
        return $invoice_number = sprintf('%07d', 1);
        return $invoice_no=$today.'1';
        
    }
}

// Add City in cities table if already city not exist 
if(!function_exists('getOtherCity')){
    function getOtherCity($name){
        $nameSlug=phpslug($name);
        $cityData = App\Models\adminpanel\cities::where('slug',$nameSlug)->get();
        $cityData=$cityData->toArray();
        if(!empty($cityData)){
            return $cityData[0]['id'];
        }
        $cityId = DB::table('cities')->insertGetId(array('name'=>strtolower($name),'slug'=>phpslug($name),'is_active'=>1),'id');
        return $cityId;
        
    }
}
// Add zipcode in zipcode table if already zipcode not exist 
if(!function_exists('getOtherZipCode')){
    function getOtherZipCode($code){
        
        $zipcodeData = App\Models\adminpanel\zipcode::where('code',phpslug($code))->get();
        $zipcodeData=$zipcodeData->toArray();
        if(!empty($zipcodeData)){
            return $zipcodeData[0]['id'];
        }
        $zipcodeId = DB::table('zipcode')->insertGetId(array('code'=>strtolower(phpslug($code)),'is_active'=>1),'id');
        return $zipcodeId;
        
    }
}

if(!function_exists('log_activity')){
    function log_activity($data){
        
        $activityID = DB::table('activities_log')->insertGetId($data,'id');
        return $activityID;
        
    }
}


// Get Options of Subs
if(!function_exists('get_subs_options')){
    function get_subs_options($selectID=NULL){
    
        $userData = App\Models\adminpanel\users::where('is_active',1)->where('group_id',config('constants.groups.sub'))->orderBy('id', 'desc')->get();
        if($userData)
         $userData=$userData->toArray();
         $options='';
         
        foreach($userData as $key=>$data){
            $selected='';
            if(is_array($selectID)){
                if(in_array($data['id'],$selectID))
                $selected='selected';
            }
            elseif($selectID==$data['id']){
                $selected='selected';
            }
            $options .='<option '.$selected.' value="'.$data['id'].'">'.$data['name'].'</option>';
        }
        
     return $options;   
    }
}
// Get Options of Drivers
if(!function_exists('get_drivers_options')){
    function get_drivers_options($selectID=NULL){
    
        $userData = App\Models\adminpanel\users::where('is_active',1)->where('group_id',config('constants.groups.driver'))->orderBy('id', 'desc')->get();
        if($userData)
         $userData=$userData->toArray();
         $options='';
         
        foreach($userData as $key=>$data){
            $selected='';
            if(is_array($selectID)){
                if(in_array($data['id'],$selectID))
                $selected='selected';
            }
            elseif($selectID==$data['id']){
                $selected='selected';
            }
            $options .='<option '.$selected.' value="'.$data['id'].'">'.$data['name'].'</option>';
        }
        
     return $options;   
    }
}
// Get Options of Customers
if(!function_exists('get_customers_options')){
    function get_customers_options($selectID=NULL){
    
        $userData = App\Models\adminpanel\users::where('is_active',1)->where('group_id',config('constants.groups.customer'))->orderBy('id', 'desc')->get();
        if($userData)
         $userData=$userData->toArray();
         $options='';
         
        foreach($userData as $key=>$data){
            $selected='';
            if(is_array($selectID)){
                if(in_array($data['id'],$selectID))
                $selected='selected';
            }
            elseif($selectID==$data['id']){
                $selected='selected';
            } 

            $options .='<option '.$selected.' value="'.$data['id'].'">'.$data['name'].'</option>';
        }
        
     return $options;   
    }
}
// Get Options of States
if(!function_exists('get_quote_status_options')){
    function get_quote_status_options($selectID=NULL, $all=false){
    $quote_status[0]=['id'=>0,'slug'=>'pending','title'=>'Pending'];
    $quote_status[1]=['id'=>1,'slug'=>'quote_submitted','title'=>'Quote Submitted'];
    $quote_status[2]=['id'=>2,'slug'=>'approved','title'=>'Approved'];
    $quote_status[3]=['id'=>3,'slug'=>'declined','title'=>'Declined'];
    if($all){
        $quote_status[4]=['id'=>4,'slug'=>'trashed','title'=>'Trashed'];
        $quote_status[5]=['id'=>5,'slug'=>'delivery','title'=>'Delivery'];
        $quote_status[6]=['id'=>6,'slug'=>'complete','title'=>'Complete'];
    }
       
         $options='';
         
        foreach($quote_status as $key=>$data){
            $selected='';
            if(is_array($selectID)){
                if(in_array($data['id'],$selectID))
                $selected='selected';
            }
            elseif($selectID==$data['id']){
                $selected='selected';
            } 
            $options .='<option '.$selected.' value="'.$data['id'].'">'.$data['title'].'</option>';
        }
        
     return $options;   
    }
}
// Get Options of States
if(!function_exists('getStatesOptions')){
    function getStatesOptions($selectID=NULL){
    
        $statesData = App\Models\adminpanel\states::where('is_active',1)->orderBy('id', 'desc')->get();
        if($statesData)
         $statesData=$statesData->toArray();
         $options='';
         
        foreach($statesData as $key=>$data){
            $selected='';
            if($selectID==$data['id']) $selected='selected';
            $options .='<option '.$selected.' value="'.$data['id'].'">'.$data['name'].'</option>';
        }
        $options .='<option  value="other">Other</option>';
     return $options;   
    }
}
// Add state in cities table if already state not exist 
if(!function_exists('getOtherstate')){
    function getOtherstate($name){
        $nameSlug=phpslug($name);
        $stateData = App\Models\adminpanel\states::where('slug',$nameSlug)->get();
        $stateData=$stateData->toArray();
        if(!empty($stateData)){
            return $stateData[0]['id'];
        }
        $stateId = DB::table('states')->insertGetId(array('name'=>strtolower($name),'slug'=>phpslug($name),'is_active'=>1),'id');
        return $stateId;
        
    }
}

// Get Options of Sizes
if(!function_exists('getItemSizeUnitsOptions')){
    function getItemSizeUnitsOptions($selectID=NULL){
    
    $sizeUnits[]=array('id'=>1, 'name'=>'Inch');
    $sizeUnits[]=array('id'=>2, 'name'=>'Feet');
    $sizeUnits[]=array('id'=>3, 'name'=>'Meter');
    $sizeUnits[]=array('id'=>4, 'name'=>'Kilo Grams');
    $sizeUnits[]=array('id'=>5, 'name'=>'Grams');
         $options='';
        foreach($sizeUnits as $data){
            $selected='';
            if($selectID==$data['name']) $selected='selected';
            $options .='<option '.$selected.' value="'.phpslug($data['name']).'">'.$data['name'].'</option>';
        }
        
     return $options;   
    }
}
if(!function_exists('formate_date')){
    function formate_date($timestamp,$time=false){
        if($time)
        return date(config('constants.date_and_time'), $timestamp);
        return date(config('constants.date_formate'), $timestamp);
        
    }
}
if(!function_exists('elapsed_time')){
    function elapsed_time($first_timestamp,$second_timestamp) {
        
        $first_time = new DateTime(formate_date($first_timestamp,true));
        $second_time = new DateTime(formate_date($second_timestamp,true));
        
        $diff = $first_time->diff( $second_time );
        $elasped_time=$diff->format( '%D:%H:%I:%S' ); 
        $elasped_time_array=explode(':',$elasped_time);

        $days_hours= $elasped_time_array[0]*24;
        $total_hours=$days_hours+$elasped_time_array[1];
        $working_time=[
            'hours'=>$total_hours,
            'mins'=>$elasped_time_array[2],
            'seconds'=>$elasped_time_array[3],
        ];
        return $working_time;

      }
}
// Get Options of Prodcut Sizes

if(!function_exists('get_product_sizes')){
    function get_product_sizes($sizes,$selectID=NULL){
    $product_sizes=explode(',',$sizes);
         $options='';
         $options .='<option selected >Select Size</option>';
        foreach($product_sizes as $value){
            if($value=='') continue;
            $selected='';
            $selectID=phpslug($selectID);
            $value2=phpslug($value);
            if($selectID == $value2){
                
                $selected='selected';
            } 
            
            $options .='<option '.$selected.' value="'.($value).'">'.$value.'</option>';
        }
        
     return $options;   
    }
}

// Add Product Category in product_categories table if already category not exist 
if(!function_exists('getOtherCategory')){
    function getOtherCategory($name){
        $nameSlug=phpslug($name);
        $categoryData = App\Models\adminpanel\product_categories::where('slug',$nameSlug)->get();
        $categoryData=$categoryData->toArray();
        if(!empty($categoryData)){
            return $categoryData[0]['id'];
        }
        $categoryId = DB::table('product_categories')->insertGetId(array('name'=>strtolower($name),'slug'=>phpslug($name),'is_active'=>1,'user_id'=>get_session_value('id')),'id');
        return $categoryId;
        
    }
}
// Get Options of Prodcut Categories
if(!function_exists('getProductCatOptions')){
    function getProductCatOptions($selectIDs=NULL){
    
        $categoryData = App\Models\adminpanel\product_categories::where('is_active',1)->orderBy('id', 'asc')->get();
        if($categoryData)
         $categoryData=$categoryData->toArray();
         $options='';

         $selectIDs=json_decode($selectIDs,true);
         if(is_null($selectIDs) || $selectIDs=='')
         $selectIDs=array();

         $options .='<option  value="0">None</option>';

        foreach($categoryData as $key=>$data){
            $selected='';
            if(in_array($data['id'],$selectIDs))
            $selected='selected';

            $options .='<option '.$selected.' value="'.$data['id'].'">'.$data['name'].'</option>';
        }
        //$options .='<option  value="other">Other</option>';
     return $options;   
    }
}
if(!function_exists('cat_name_by_ids')){
    function cat_name_by_ids($cat_ids=array()){
        $categoryData = App\Models\adminpanel\product_categories::where('is_active',1)->wherein('id',$cat_ids)->orderBy('id', 'asc')->get('name')->toArray();
        $retData=[];
        foreach($categoryData as $cat){
            $retData[]=$cat['name'];
        }
        
        return $retData;
    }
}

// Get Options of Prodcut Categories
if(!function_exists('get_product_cat_Options')){
    function get_product_cat_Options($selectIDs=NULL){
        $categoryData = App\Models\adminpanel\product_categories::where('is_active',1)->orderBy('id', 'asc')->get();
        if($categoryData)
         $categoryData=$categoryData->toArray();
         $options='';

        foreach($categoryData as $key=>$data){
            $selected='';
            if($data['id']==$selectIDs)
            $selected='selected';

            $options .='<option '.$selected.' value="'.$data['id'].'">'.$data['name'].'</option>';
        }
     return $options;   
    }
}
// Get Options of Cities
if(!function_exists('getCitiesOptions')){
    function getCitiesOptions($selectID=NULL){
    
        $cityData = App\Models\adminpanel\cities::where('is_active',1)->orderBy('id', 'asc')->get();
        if($cityData)
         $cityData=$cityData->toArray();
         $options='';
         
        foreach($cityData as $key=>$data){
            $selected='';
            if($selectID==$data['id']) $selected='selected';
            $options .='<option '.$selected.' value="'.$data['id'].'">'.$data['name'].'</option>';
        }
        $options .='<option  value="other">Other</option>';
     return $options;   
    }
}
// Get Options of Cities
if(!function_exists('getZipCodeOptions')){
    function getZipCodeOptions($selectID=NULL){
    
        $zipcodeData = App\Models\adminpanel\zipcode::where('is_active',1)->orderBy('id', 'asc')->get();
        if($zipcodeData)
         $zipcodeData=$zipcodeData->toArray();
         $options='';
         
        foreach($zipcodeData as $key=>$data){
            $selected='';
            if($selectID==$data['id']) $selected='selected';
            $options .='<option '.$selected.' value="'.$data['id'].'">'.$data['code'].'</option>';
        }
        $options .='<option  value="other">Other</option>';
     return $options;   
    }
}
if(!function_exists('driver_activities')){
    function driver_activities($selectID=NULL){
        $driver_activities=[
            'reached_at_pickup' =>'Reached at pick-up',
            'picked_up' =>'Finished pick-up',
            'on_the_way' =>'On the way to delivery',
            'reached_at_dropoff' =>'Reached at drop-off',
            'delivered' =>'Delivered',
        ];
       return $driver_activities;

    }
}


if(!function_exists('get_product_pickup_dropoff')){
    function get_product_pickup_dropoff($quote_id,$pickup_dropoff_order_number){
        
        $where_clause['quote_id']=$quote_id;
        $where_clause['pickup_dropoff_order_number']=$pickup_dropoff_order_number;
        $quote_products = App\Models\adminpanel\quote_products::where($where_clause)->with('pickup_dropoff_address')->orderBy('id', 'asc')->get()->toArray();
        if(count($quote_products)>0)
        return $quote_products[0];
        return array();

    }
}

if(!function_exists('get_selected_product')){
    function get_selected_product($product_id,$quote_id,$pickup_dropoff_order_number,$proData=array()){
        $where_clause['product_id']=$product_id;
        $where_clause['quote_id']=$quote_id;
        $where_clause['pickup_dropoff_order_number']=$pickup_dropoff_order_number;
        //p($where_clause);
        $quote_products = App\Models\adminpanel\quote_products::where($where_clause)->with('pickup_dropoff_address')->orderBy('id', 'asc')->get()->toArray();
        //p($quote_products);
        if(isset($quote_products[0]['pickup_dropoff_address']) && !empty($quote_products[0]['pickup_dropoff_address']))
        $retData['pickup_dropoff_address']=$quote_products[0]['pickup_dropoff_address'];
        
        $retData['product_list']='';
        if(empty($quote_products)){
        $retData['product_list']= '<div id="item_row'.$pickup_dropoff_order_number.'_'.$proData['id'].'">
                                         <div class="row form-group">
                                          <div class="col-1">&nbsp;</div>
                                          <div class="col-4">
                                              <div class="form-group clearfix">
                                                  <div class="icheck-primary d-inline">
                                                      <input type="hidden" name="product_details'.$pickup_dropoff_order_number.'['.$proData['id'].'][cat_id][]" value="'.$proData['cat_id'].'">
                                                      <input type="hidden" value="'.$proData['id'].'" name="product_details'.$pickup_dropoff_order_number.'['.$proData['id'].'][product_id][]">
                                                      <input type="checkbox" value="'.$proData['name'].'" name="product_details'.$pickup_dropoff_order_number.'['.$proData['id'].'][product_name][]" id="'.$proData['slug'].'_'.$proData['id'].'">
                                                      <label for="'.$proData['slug'].'_'.$proData['id'].'">
                                                          '.$proData['name'].'
                                                      </label>
                                                  </div>
                                              </div>
  
                                          </div>
                                          <div class="col-1">
                                              <div class="input-group mb-3">
                                                  <input placeholder="Quantity" value="1" type="number" name="product_details'.$pickup_dropoff_order_number.'['.$proData['id'].'][item_quantity][]" class=" form-control" required>
                                              </div>
                                          </div>
                                          <div class="col-1">
                                              <div class="input-group mb-3">
                                                  <select name="product_details'.$pickup_dropoff_order_number.'['.$proData['id'].'][product_sizes][]"  class="form-control">'.get_product_sizes($proData['sizes']).'</select>
                                              </div>
                                          </div>
                                          <div class="col-3">
                                              <div class="input-group mb-3">
                                                  <input placeholder="Description" type="text" name="product_details'.$pickup_dropoff_order_number.'['.$proData['id'].'][item_description][]"  class=" form-control">
                                              </div>
                                          </div>
                                          <div class="col-1"><div style="width: 90px; float:right;" onclick="addmore_items'.$pickup_dropoff_order_number.'('.$proData['id'].',\''.$proData['slug'].'\')"
                                            class="btn btn-success btn-block btn-sm"><i class="fas fa-plus"></i> Add
                                            more</div></div>
                                      </div>
                                    </div>
                                      <div id="duplicate_row'.$pickup_dropoff_order_number.'_'.$proData['id'].'"></div>';
                                      return $retData;
        }
        else{

                foreach($quote_products as $key=>$product_data){

            $html_id=phpslug($product_data['product_name']).$pickup_dropoff_order_number.'_'.rand();

            $retData['product_list'] .= '<div id="item_row'.$pickup_dropoff_order_number.'_'.$product_data['product_id'].'">
                <div class="row form-group">
                <div class="col-1">&nbsp;</div>
                <div class="col-4">
                    <div class="form-group clearfix">
                        <div class="icheck-primary d-inline">
                            <input type="hidden" name="product_details'.$pickup_dropoff_order_number.'['.$product_data['product_id'].'][cat_id][]" value="'.$product_data['cat_id'].'">
                            <input type="hidden" value="'.$product_data['product_id'].'" name="product_details'.$pickup_dropoff_order_number.'['.$product_data['product_id'].'][product_id][]">
                            <input type="checkbox" checked value="'.$product_data['product_name'].'" name="product_details'.$pickup_dropoff_order_number.'['.$product_data['product_id'].'][product_name][]" id="'.$html_id.'">
                            <label for="'.$html_id.'">
                                '.$product_data['product_name'].'
                            </label>
                        </div>
                    </div>

                </div>
                <div class="col-1">
                    <div class="input-group mb-3">
                        <input placeholder="Quantity" value="'.$product_data['quantity'].'" type="number" name="product_details'.$pickup_dropoff_order_number.'['.$product_data['product_id'].'][item_quantity][]" class=" form-control" required>
                    </div>
                </div>
                <div class="col-1">
                    <div class="input-group mb-3">
                        <select name="product_details'.$pickup_dropoff_order_number.'['.$product_data['product_id'].'][product_sizes][]"  class="form-control">'.get_product_sizes($proData['sizes'],$product_data['size']).'</select>
                    </div>
                </div>
                <div class="col-3">
                    <div class="input-group mb-3">
                        <input value="'.$product_data['description'].'" placeholder="Description" type="text" name="product_details'.$pickup_dropoff_order_number.'['.$product_data['product_id'].'][item_description][]"  class=" form-control">
                    </div>
                </div>
                <div class="col-1"><div style="width: 90px; float:right;" onclick="addmore_items'.$pickup_dropoff_order_number.'('.$product_data['product_id'].',\''.$proData['slug'].'\')"
                class="btn btn-success btn-block btn-sm"><i class="fas fa-plus"></i> Add
                more</div></div>
            </div>
        </div>
            <div id="duplicate_row'.$pickup_dropoff_order_number.'_'.$product_data['product_id'].'"></div>';
            }

        }
        return $retData;
    }
}
if(!function_exists('get_record_count')){
    function get_record_count(){

        $retData=[
            'office'=>0,
            'web'=>0,
            'admin'=>0,
            'customer'=>0,
            'driver'=>0,
            'sub'=>0,
            'pending_quotes'=>0,
            'submitted_quotes'=>0,
            'approved_quotes'=>0,
            'declined_quotes'=>0,
            'trashed_quotes'=>0,
            'deliverable_quotes'=>0,
            'delivered_quotes'=>0,
            'total_leads'=>0,
            'total_users'=>0,
            'total_customers'=>0,
            'total_drivers'=>0,
            'total_quotes'=>0,
            'total_deliverable'=>0,
            'total_delivered'=>0,
            'total_products'=>0,
            'total_product_categories'=>0,
            ];
            
        $product_info = DB::table('products')
                 ->select('is_active', DB::raw('count(*) as total'))
                 ->groupBy('is_active')
                 ->where('is_active',1)
                 ->get()->toArray();
                 $product_info=isset($product_info[0])?$product_info[0]:[];
       
       $product_cat_info = DB::table('product_categories')
                 ->select('is_active', DB::raw('count(*) as total'))
                 ->groupBy('is_active')
                 ->where('is_active',1)
                 ->get()->toArray();
                 $product_cat_info=isset($product_cat_info[0])?$product_cat_info[0]:[];


        $quoteWhere=array();
        $leadsWhere['group_id']=config('constants.groups.subscriber');                 
        if(get_session_value('group_id')!=config('constants.groups.admin')){
            $leadsWhere['id']=get_session_value('id');  
 
        }
        if(get_session_value('group_id')==config('constants.groups.customer')){
            $quoteWhere['customer_id']=get_session_value('id');     
        }
        if(get_session_value('group_id')==config('constants.groups.driver')){
            $quoteWhere['driver_id']=get_session_value('id');  
        }


        $leads_info = DB::table('users')
                 ->select('lead_by', DB::raw('count(*) as total'))
                 ->groupBy('lead_by')
                 ->orderBy('lead_by', 'asc')
                 ->where($leadsWhere)
                 ->where('is_active',1)
                 
                 ->get()->toArray();

        $quote_info = DB::table('quotes')
                 ->select('status', DB::raw('count(*) as total'))
                 ->groupBy('status')
                 ->where($quoteWhere)
                 ->where('is_active',1)
                 ->where('po_number','!=','')
                 ->orderBy('status', 'asc')
                 ->get()->toArray();                 
    
        $trashed_quote_info = DB::table('quotes')
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->where($quoteWhere)
                ->where('is_active',2)
                ->where('status','<', config('constants.quote_status.delivery'))
                ->orderBy('status', 'asc')
                ->get()->toArray();                 

                 //p($trashed_quote_info); die;
                 
        $user_info = DB::table('users')
                 ->select('group_id', DB::raw('count(*) as total'))
                 ->groupBy('group_id')
                 ->where('is_active',1)
                 ->orderBy('group_id', 'asc')
                 ->get();
                 
                

                    // total Product Count
                    if(isset($product_info) && !empty($product_info))
                    $retData['total_products']=$product_info->total;
                    // Total Product Category Count
                    if(isset($product_cat_info) && !empty($product_cat_info))
                    $retData['total_product_categories']=$product_cat_info->total;
                    
                  foreach($quote_info as $key=>$quote){
                    
                    if($quote->status==config('constants.quote_status.pending')){
                        $retData['total_quotes']=$retData['total_quotes']+$quote->total;
                        $retData['pending_quotes']=$quote->total;
                    }
                    elseif($quote->status==config('constants.quote_status.quote_submitted')){
                        $retData['submitted_quotes']=$quote->total;
                        $retData['total_quotes']=$retData['total_quotes']+$quote->total;
                    }
                    elseif($quote->status==config('constants.quote_status.approved')){
                        $retData['approved_quotes']=$quote->total;
                        $retData['total_quotes']=$retData['total_quotes']+$quote->total;
                    }
                    elseif($quote->status==config('constants.quote_status.declined')){
                        $retData['declined_quotes']=$quote->total;
                        $retData['total_quotes']=$retData['total_quotes']+$quote->total;
                    }
                    // elseif($quote->status==config('constants.quote_status.trashed')){
                    //     $retData['trashed_quotes']=$quote->total;
                    //     $retData['total_quotes']=$retData['total_quotes']+$quote->total;
                    // }
                    elseif($quote->status==config('constants.quote_status.delivery')){
                        $retData['deliverable_quotes']=$quote->total;
                        $retData['total_deliverable']=$retData['total_deliverable']+$quote->total;
                    }
                    elseif($quote->status==config('constants.quote_status.complete')){
                        $retData['delivered_quotes']=$quote->total;
                        $retData['total_delivered']=$retData['total_delivered']+$quote->total;
                    }
                   
                   
                  }

                  // Trashed Quotes
                  foreach($trashed_quote_info as $key=>$quote){
                    
                    if($quote->status==config('constants.quote_status.pending')){
                        $retData['trashed_quotes']=$retData['trashed_quotes']+$quote->total;
                        $retData['pending_trashed_quotes']=$quote->total;
                    }
                    elseif($quote->status==config('constants.quote_status.quote_submitted')){
                        $retData['submitted_trashed_quotes']=$quote->total;
                        $retData['trashed_quotes']=$retData['trashed_quotes']+$quote->total;
                    }
                    elseif($quote->status==config('constants.quote_status.approved')){
                        $retData['approved_trashed_quotes']=$quote->total;
                        $retData['trashed_quotes']=$retData['trashed_quotes']+$quote->total;
                    }
                    elseif($quote->status==config('constants.quote_status.declined')){
                        $retData['declined_trashed_quotes']=$quote->total;
                        $retData['trashed_quotes']=$retData['trashed_quotes']+$quote->total;
                    }
                    elseif($quote->status==config('constants.quote_status.trashed')){
                        $retData['trashed_quotes']=$quote->total;
                        $retData['trashed_quotes']=$retData['trashed_quotes']+$quote->total;
                    }
                   
                   
                   
                  }
                  // End Trashed Quotes
                  // Lead Data
              
                  //p($leads_info); die;
                  foreach($leads_info as $key=>$leadData){
                    $retData['total_leads']=$retData['total_leads']+$leadData->total;

                    if($leadData->lead_by==0)
                    $retData['office']=$leadData->total;
                    else if($leadData->lead_by==1)
                    $retData['web']=$leadData->total;
                  }
                    
               // Users count
                    foreach($user_info as $key=>$userData){

                        $retData['total_users']=$retData['total_users']+$userData->total;

                        if($userData->group_id==config('constants.groups.admin'))
                        $retData['admin']=$userData->total;
                        elseif($userData->group_id==config('constants.groups.customer'))
                        $retData['customer']=$userData->total;
                        elseif($userData->group_id==config('constants.groups.driver'))
                        $retData['driver']=$userData->total;
                        elseif($userData->group_id==config('constants.groups.sub'))
                        $retData['sub']=$userData->total;
                        elseif($userData->group_id==config('constants.groups.subscriber'))
                        $retData['total_leads']=$userData->total;
                     
                    }
                       
                    
               
                 return $retData;
    }
}
// Get the paid amount for the Quote by Customer
if(!function_exists('received_amount')){
    function received_amount($id){
        $amount=App\Models\adminpanel\invoices::where('quote_id','=',$id)->sum('paid_amount');
        return $amount;
    }
}

if(!function_exists('prioritise')){
    function prioritise($id,$priority_no){
        $title='Low';
        $class='btn-primary';
        if($priority_no==1){
            $title='Moderate';
            $class='btn-secondary';
        }
        elseif($priority_no==2){
            $title='High';
            $class='btn-danger';
        }
         $btn='<a @disabled(true) onclick="prioritise_lead('.$id.','.$priority_no.',\'prioritise\')" class="btn '.$class.' btn-flat btn-sm"><i class="fas fa-chart-line"></i>'.$title.'</a>';
    return $btn;

    }
}
if(!function_exists('quote_data_for_mail')){
    function quote_data_for_mail($id,$user_role='any'){
        //$zipcodeData = App\Models\adminpanel\zipcode::where('is_active',1)->orderBy('id', 'asc')->get();
        $quotesData=App\Models\adminpanel\quotes::with('quote_products')
       ->with('customer')
       ->with('quote_prices')
       ->with('comments')
       ->where('id', $id)
       ->orderBy('created_at', 'desc')->get()->toArray();
       $quotesData=$quotesData[0];
       // p($quotesData); die;
      
       
        $quote_price='';
        $pickup_dropoff_address=array();

      if(!empty($quotesData['quote_prices']) && $user_role=='any'){
        $quote_price='<tr><th colspan="2">Delivery Price</th></tr>
        <tr><td colspan="2"><table width="100%" border="1">
        <tr>
            <td>Price</td>
            <td>Extra</td>
            <td>Reason</td>
            <td>Description</td>
            <td>Sent On</td>
            <td>Status</td>
        </tr>';
            foreach ($quotesData['quote_prices'] as $key=>$data){
            $quote_price .='<tr>
            <td>$'.$data['quoted_price'].'</td>
            <td>$'.(($data['extra_charges'] != '') ? $data['extra_charges'] : 0 ).'
            </td>
            <td>'.$data['reason_for_extra_charges'].'</td>
            <td>'.$data['description'].'</td>
            <td>'.date('d/m/Y', strtotime($data['created_at'])).'</td>
            <td>';
                if ($data['status'] == 1){
                $quote_price .='<span
                        class="btn btn-success btn-block btn-sm"><i
                            class="fas fa-chart-line"></i>
                        Active</span>';
                }else{
                    $quote_price .='<span
                        class="btn btn-primary btn-block btn-sm"><i
                            class="fas fa-chart-line"></i>
                        Previous</span>';
                }
                $quote_price .=' </td></tr>';
                    }
                    $quote_price .='</table>
                    </td><tr>';
    }
    else if($user_role==config('constants.groups.sub')){
        $quote_price='<tr><th colspan="2">Delivery Price</th></tr>
        <tr><td><strong>Amount :</strong></td><td>$'.$quotesData['quoted_price_for_sub'].'</td></tr>';
    }

    $multi_quote_html='';
if($quotesData['quote_type']=='multi'){
    $multi_quote_html ='<tr>
    <td colspan="2" style="text-align:justified">
    Quote :'.$quotesData['quote_type'].'<br>
    Business Type :'.$quotesData['business_type'].'<br>
    Elevator :'.(($quotesData['elevator']==1)?'YES':'NO').'<br>
    No of Appartments :'.$quotesData['no_of_appartments'].'<br>
    List of Floors :'.implode(',',json_decode($quotesData['list_of_floors'],true)).'<br>
    </td>
    </tr>';
}
$bodymsg='<table width="100%" border=1>
       <tr><th colspan="2">Quote Information [PO No.: '.$quotesData['po_number'].']</th></tr>'.$multi_quote_html .'
       <tr><td colspan="2">
           <table border="1" width="100%">
               <tbody>';
               if (isset($quotesData['quote_products']) && empty($quotesData['quote_products'])){ // if no product added then show only the pickup dropoff address
                $bodymsg .=' <tr>
                        <td colspan="2">
                            <strong>Pick Up Detail </strong> <br>
                            Date : '.$quotesData['pickup_date'].'<br>
                            Street Address
                            :'.$quotesData['pickup_street_address'].'<br>
                            Unit :'.$quotesData['pickup_unit'].'<br>
                            Contact No. :'.$quotesData['pickup_contact_number'].'<br>
                        </td>
                        <td colspan="2">
                            <strong>Drop-Off Detail </strong> <br>
                            Date :'.$quotesData['drop_off_date'].'<br>
                            Street Address
                            :'.$quotesData['drop_off_street_address'].'<br>
                            Unit :'.$quotesData['drop_off_unit'].'<br>
                            Contact No.
                            :'.$quotesData['drop_off_contact_number'].'<br>
                        </td>
                        
                    </tr> ';
               }
                   foreach ($quotesData['quote_products'] as $quote_product){
                    
                    if (!in_array($quote_product['pickup_dropoff_order_number'],$pickup_dropoff_address)){
                        $bodymsg .=' <tr>
                        <td colspan="2">
                            <strong>Pick Up Detail </strong> <br>
                            Date : '.$quote_product['pickup_dropoff_address']['pickup_date'].'<br>
                            Street Address
                            :'.$quote_product['pickup_dropoff_address']['pickup_street_address'].'<br>
                            Unit :'.$quote_product['pickup_dropoff_address']['pickup_unit'].'<br>
                            Contact No. :'.$quote_product['pickup_dropoff_address']['pickup_contact_number'].'<br>
                        </td>
                        <td colspan="2">
                            <strong>Drop-Off Detail </strong> <br>
                            Date :'.$quote_product['pickup_dropoff_address']['drop_off_date'].'<br>
                            Street Address
                            :'.$quote_product['pickup_dropoff_address']['drop_off_street_address'].'<br>
                            Unit :'.$quote_product['pickup_dropoff_address']['drop_off_unit'].'<br>
                            Contact No.
                            :'.$quote_product['pickup_dropoff_address']['drop_off_contact_number'].'<br>
                        </td>
                        
                    </tr> 
                    <tr>
                        <th>Prodcut Name</th>
                        <th>Quantity</th>
                        <th>Size</th>
                        <th>Description</th>
                    </tr>';
                        $pickup_dropoff_address[]=$quote_product['pickup_dropoff_order_number'];
                    }
                    
                    

                   $bodymsg .='<tr>
                       <td>'.$quote_product['product_name'].'</td>
                       <td>'.$quote_product['quantity'].'</td>
                       <td>'.$quote_product['size'].'</td>
                       <td>'.$quote_product['description'].'</td>
                   </tr>'; 

                       }

                       $car_names=cat_name_by_ids(json_decode($quotesData['customer']['shipping_cat'],true)) ;

            $bodymsg .='</tbody>
           </table>    
       </td></tr>
       '.$quote_price;

       if($user_role!==config('constants.groups.sub')){ // If it is sub then Customer info will not be listed
       $bodymsg .='<tr><th colspan="2">Customer Information</th></tr>
       <tr><td colspan="2">
           <table width="100%" border="1">
               
                   <tbody>
                       <tr>
                           <th style="width:50%">Name</th>
                           <td>'.$quotesData['customer']['firstname'].' '.$quotesData['customer']['lastname'].'</td>
                       </tr>
                       <tr>
                           <th>Email</th>
                           <td>'.$quotesData['customer']['email'].'</td>
                       </tr>
                       <tr>
                           <th>Phone</th>
                           <td>'.$quotesData['customer']['phone'].'</td>
                       </tr>
                       <tr>
                           <th>Business Name</th>
                           <td>'.$quotesData['customer']['business_name'].'</td>
                       </tr>
                       <tr>
                           <th>Designation</th>
                           <td>'.$quotesData['customer']['designation'].'</td>
                       </tr>
                       <tr>
                           <th>Business Email</th>
                           <td>'.$quotesData['customer']['business_email'].'</td>
                       </tr>
                       <tr>
                           <th>Business Mobile</th>
                           <td>'.$quotesData['customer']['business_mobile'].'</td>
                       </tr>
                       <tr>
                           <th>Business Phone</th>
                           <td>'.$quotesData['customer']['business_phone'].'</td>
                       </tr>
                       <tr>
                           <th>Business Age</th>
                           <td>'.$quotesData['customer']['years_in_business'].' years
                           </td>
                       </tr>
                       <tr>
                           <th>How Often Shiping</th>
                           <td>'.$quotesData['customer']['how_often_shipping'].'</td>
                       </tr>
                       <tr>
                           <th>Shiping </th>
                           <td>'.implode('<br>',$car_names).'</td>
                       </tr>

                   </tbody>
           </table>    
       </td></tr>';
        }

      $bodymsg .='</table>';

      return $bodymsg;
    }
}
if(!function_exists('_curl')){
    function _curl($api_url='', $postData=array()){
        

        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $api_url);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $result="curlError=Y&xCurlMsg=".curl_error($ch);
        } else {
        curl_close($ch);
        }
        if (!is_string($result) || !strlen($result)) {
        //echo "Failed to get result.";
        $result="curlError=Y&xCurlMsg=Failed to get result.";
        }
        return $result;
    }
}
?>