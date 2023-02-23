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
use App\Models\adminpanel\quickbook_credentials;
use App\Models\adminpanel\invoices;
use App\Models\adminpanel\pickup_dropoff_address;
use App\Models\adminpanel\comments;
use App\Models\adminpanel\cardknox_transactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel; // To export import excel
use Exception;
use Twilio\Rest\Client; // used to send SMS

// Used for Email Section
use App\Mail\EmailTemplate;
use Illuminate\Support\Facades\Mail;

// Import/Export Excelsheet
//use App\Imports\ImportQuotes;
use App\Exports\ExportQuotes;


use DB;
use PDF;

// These all for QUICKBOOKS
use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\Exception\ServiceException;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Customer;
use QuickBooksOnline\API\Facades\Item;
use QuickBooksOnline\API\Facades\SalesReceipt;
use QuickBooksOnline\API\Facades\Invoice;
use QuickBooksOnline\API\Facades\Line;
//use GuzzleHttp\Client;
use QuickBooksOnline\API\PlatformService\PlatformService;
use QuickBooksOnline\API\Diagnostics\Logger;
use QuickBooksOnline\API\Diagnostics\LoggerLevel;
use QuickBooksOnline\API\Diagnostics\LoggerType;
use Monolog\Handler\StreamHandler;

use QuickBooksOnline\API\Data\IPPCustomer;
use QuickBooksOnline\API\Data\IPPInvoice;
use QuickBooksOnline\API\Data\IPPPayment;
use QuickBooksOnline\API\Data\IPPLinkedTxn;

// To Renew Access Token
use QuickBooksOnline\API\Data\OAuth2\OAuth2LoginHelper;
use QuickBooksOnline\API\Data\OAuth2\OAuth2AccessToken;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2AccessTokenEntity;


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
        $this->invoices= new invoices;
        $this->cardknox_transactions= new cardknox_transactions;
        $this->pickup_dropoff_address= new pickup_dropoff_address;
        $this->quickbook_credentials= new quickbook_credentials;
      }

      public function delete_quotes_without_po_number(){
        
        $where_clause=[
            ['po_number', '=', ''],
        ];
        // $this->quotes->foreign('quote_id')
        // ->references('id')->on('pickup_dropoff_address')
        // ->onDelete('cascade')->where($where_clause)->orwhereNull('po_number');
      
        $this->quotes->where($where_clause)->orwhereNull('po_number')->delete();
        //$this->quote_products->where('quote_id',$id)->delete();

        $myfile = fopen("quotes_cronjob_log.txt", "a") or die("Unable to open file!");
			
					$txt = 'File executed at ='. date('d/m/Y H:i:s')."\n";
					fwrite($myfile, $txt);
					fclose($myfile);
		

      }
      public function get_po_numbers($quote_ids=[]){
        $quotes=$this->quotes->wherein('id',$quote_ids)->get(['id','po_number','qb_invoice_no','qb_invoice_id','qb_service_id'])->toArray();
        $invoice_id=$invoice_no=$po_numbers_arr=$quotes_data=[];
        
        foreach($quotes as $data){
            $po_numbers_arr[$data['id']]=$data['po_number'];
            $invoice_no[$data['id']]=$data['qb_invoice_no'];
            $invoice_id[$data['id']]=$data['qb_invoice_id'];
            

            $quotes_data[$data['id']]['id']=$data['id'];
            $quotes_data[$data['id']]['po_number']=$data['po_number'];
            $quotes_data[$data['id']]['qb_invoice_no']=$data['qb_invoice_no'];
            $quotes_data[$data['id']]['qb_invoice_id']=$data['qb_invoice_id'];
            $quotes_data[$data['id']]['qb_service_id']=$data['qb_service_id'];
        }

        return ['po_number'=>$po_numbers_arr,'invoices_id_data'=>$invoice_id,'invoices_no_data'=>$invoice_no,'quote_data'=>$quotes_data];
      }
        public function make_deliveries_payments($customer_id,Request $req){
            
            $user=Auth::user();
            $customer=$this->users->where(['id'=>$customer_id])->get()->toArray();
            $userData=[];
            if(isset($customer[0]))
            $userData=$customer[0];
            $payment_type=''; 

            $quotes_data_arr=$this->get_po_numbers($req->open_balance_quote_id);
            
            $po_numbers_arr=$quotes_data_arr['po_number'];
            $invoices_no_arr=$quotes_data_arr['invoices_no_data'];
            $invoices_id_arr=$quotes_data_arr['invoices_id_data'];
            
            
            $payment_type='credit_card'; 
               
           
                // For Payments
             if($req->action=='makepayment'){
                
                $validatorArray=[
                    'firstname'=>'required',
                    'lastname'=>'required',
                    'mobileno'=>'required',
                    'billing_email'=>'required',
                    'business_name'=>'required',
                    'business_phone'=>'required',
                    'zipcode'=>'required',
                    'business_address'=>'required',
                    
                ];
                if(isset($req->payment_type)&& $req->payment_type=='ACH'){
                    $validatorArray['account_no']='required';
                    $validatorArray['routing']='required';
                    $payment_type='ACH'; 
                }
                else{
                    $validatorArray['cardnumber']='required';
                    $validatorArray['expiry_month']='required';
                    $validatorArray['expiry_year']='required';
                    $validatorArray['cvv']='required';
                    $payment_type='credit_card'; 
                    
                }
                $validator=$req->validate($validatorArray);
              

                $xKey = env('xKEY');
                $url = env('CARDKNOX_API_URL');


                $card_expiry=$req->expiry_month.$req->expiry_year;
                $customer_name=$req->firstname.' '. $req->lastname;
                $invoice_no=$transaction_timestamp_id=time();
                

                if(isset($req->payment_type)&& $req->payment_type=='ACH'){
                    $post_data = array(
                        "xCommand" => "check:Sale",
                        "xRouting" => $req->routing,//"021000021",
                        "xAccount" => $req->account_no,
                        "xInvoice" => $invoice_no,
                        "xName" => $req->account_holder_name
                        
                    );

                }
                else{
                    $post_data = array(
                        "xCommand" => "cc:Sale",
                        "xCardNum" => $req->cardnumber,
                        "xExp" => $card_expiry,
                        "xCVV" => $req->cvv,
                        "xName" => $customer_name
                    );

                }
               

                $post_data["xKey"] = $xKey;
                $post_data["xAmount"] = $req->amount;
                $post_data["xName"] = $customer_name;
                $post_data["xZip"] = $req->zipcode;
                $post_data["xEmail"] = $req->billing_email;
                $post_data["xPONum"] = implode(',',$po_numbers_arr);
                $post_data["xDescription"] = 'INVOICES NO['.implode(',',$invoices_no_arr).']. Payment is done by '.$customer_name.' having bussiness name:'.$req->business_name.', Business Address:'.$req->business_address.' and Business Phone:'.$req->business_phone;
                $post_data["xCustom01"] = $customer_name;
                $post_data["xSoftwareName"] = "OodlerExpress";
                $post_data["xSoftwareVersion"] = "0.01";
                $post_data["xVersion"] = "5.0.0";

                $post_data["xBillFirstName"] = $req->firstname;
                $post_data["xBillLastName"] = $req->lastname;
                $post_data["xBillCompany"] = $req->business_name;
                $post_data["xBillStreet"] = $req->business_address;
                $post_data["xBillZip"] = $req->zipcode;
                $post_data["xBillPhone"] = $req->business_phone;
                $post_data["xBillMobile"] = $req->business_phone;
                 //p($post_data); 
                 $output= _curl($url, $post_data);
                 $tmp = explode("\n",$output); 
                $result_string = $tmp[count($tmp)-1]; 
                parse_str($result_string, $result_array); 
                // p($post_data);
                // p($result_array); die;
                if($result_array['xResult']=='A' && $result_array['xStatus']=='Approved'){
                    $req->session()->flash('alert-success', 'Transaction completed successfully !');
                   
                    $xcardnumber=$req->cardnumber;
                    if(isset($req->payment_type) && $req->payment_type=='ACH'){
                        $result_array['xCardType']='ACH';
                        $xcardnumber=$req->account_no;
                    }
                    
                    
                    foreach($req->open_balance_quote_id as $quote_id){
                        $invoice_no=$invoices_no_arr[$quote_id];
                        $invoice_id=$invoices_id_arr[$quote_id];

                        $to_update_quote_status=[];
                        $to_update_quote_status['payment_status']=1;
                        $this->quotes->where('id',$quote_id)->update($to_update_quote_status);
                        
                        $qb_payment_status=0;
                        $qb_payment=$this->quickbook_payment_status($invoice_id);
                        if($qb_payment['error']=='NO')
                        $qb_payment_status=1;
                        else{
                            $req->session()->flash('alert-danger', 'Invoice No:'.$invoice_no.' '.$result_array['message']);
                        }

                        DB::table('cardknox_transactions')->insert([
                            'xcardnum' => $xcardnumber,
                            'xcardexp' => $card_expiry,
                            'xcardcvv' => $req->cvv,
                            'xcardtype' =>$result_array['xCardType'],
                            'payee_name' =>$customer_name,
                            'billing_email' =>$req->billing_email,
                            'xdate' =>$result_array['xDate'],
                            'xrefnum' =>$result_array['xRefNum'],
                            'paid_amount' =>$req->amount,
                            'invoice_no' =>$invoice_no,
                            'invoice_id' =>$invoice_id,
                            'qb_payment_status' =>$qb_payment_status,
                            'transaction_timestamp_id' =>$transaction_timestamp_id,
                            'quote_id' =>$quote_id,
                            'customer_id' =>$customer_id,
                        ]);

                        $cardknox_transactions_id = DB::getPdo()->lastInsertId();

                        DB::table('invoices')->insert([
                            'invoice_no' => $invoice_no,
                            'payee_name' => $customer_name,
                            'payee_phone' => $req->mobileno,
                            'description' =>$req->billing_email,
                            'paid_amount' =>$req->amount,
                            'tax_amount' =>$req->billing_email,
                            'other' =>$result_array['xRefNum'],
                            'slug' =>$cardknox_transactions_id,
                            'quote_id' =>$quote_id,
                            'created_by' =>$user->id,
                        ]);

                    }

                    return view('adminpanel.transaction_completed',get_defined_vars());
                }
                else if($result_array['xResult']=='E' && $result_array['xStatus']=='Error'){
                    $req->session()->flash('alert-danger', $result_array['xError']);
                   
                }
                
             }
            
            
            
            $amount=0;
            if(isset($req->amount) && $req->amount>0)
            $amount=$req->amount;

          

            return view('adminpanel.make_deliveries_payments',get_defined_vars()); 
        }
        public function quickbook_payment_status( $invoice_id){
            $config = config('quickbooks');
        
            $tokenData=$this->updated_access_token();
            $config['access_token']=$tokenData['access_token'];
            $config['refresh_token']=$tokenData['refresh_token'];
        
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

            $invoice = $dataService->FindById('Invoice', $invoice_id);
            
                    // Create a new payment object
                    $payment = new IPPPayment();
                    $payment->PaymentType = 'Check';
                    $payment->TotalAmt = $invoice->TotalAmt;
                    $payment->TxnDate = date('Y-m-d');

                    // Link the payment to the invoice
                    $linkedTxn = new IPPLinkedTxn();
                    $linkedTxn->TxnId = $invoice->Id;
                    $linkedTxn->TxnType = 'Invoice';
                    $linkedTxn->Amount = $invoice->TotalAmt;
                    $payment->Line = array($linkedTxn);

                    // Save the payment to QuickBooks Online
                    $payment = $dataService->Add($payment);

                    // Update the invoice to mark it as paid
                    $invoice->Balance = 0.0;
                    $invoice->Deposit = $invoice->TotalAmt;
                    $invoice->PaymentStatus = 'Paid';
                    
                    $retData=[];
                    try{
                        $result = $dataService->Update($invoice);
                        $retData['error']='NO';
                        $retData['message']='Quickbook Payment Status updated Successfully';

                    } catch (ServiceException $ex) {
                        $retData['error']='YES';
                        $retData['message']='QUICKBOOK:'.$ex->getMessage();
                        //$error = $dataService->getLastError();
                       
                    }
                    return $retData;
                    

        }
        public function open_balance_deliveries(Request $req){
            $user=Auth::user();

            $route=$customer_id=NULL;
            
             if($user->group_id==config('constants.groups.customer'))
            $customer_id =$user->id;
            
            

            if($req->customer_id>0)
            $customer_id=$req->customer_id;
           
            if($customer_id>0)
            $route=route('make_deliveries_payments',$customer_id);

            $where_clause=[
            ['status', '>=', config('constants.quote_status.delivery')],
            ['is_active', '=', 1],
            ['payment_status', '=', 0],
            
        ];

            $quotesData=$this->quotes
            ->with(['quote_agreed_cost','invoices','customer','driver','sub'])
            ->where('customer_id', $customer_id)
            ->where($where_clause)
             //->orderBy('created_at', 'desc')->toSql();die;
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            //->orderBy('created_at', 'desc')->get()->toArray();echo count($quotesData); p($quotesData);  die;

            return view('adminpanel.open_balance_deliveries',get_defined_vars()); 
        }
      public function deliveries(Request $req){
        $user=Auth::user();

        $where_clause=[
            ['status', '=', config('constants.quote_status.delivery')],
            ['is_active', '=', 1],
        ];
        
        if($user->group_id==config('constants.groups.customer'))
        $where_clause[]=['customer_id','=',get_session_value('id')];
        elseif($user->group_id==config('constants.groups.driver'))
        $where_clause[]=['driver_id','=',get_session_value('id')];

        $pagination_per_page=config('constants.per_page');

        $where_in_clause=$customer_ids=$driver_ids=$quote_status=array();
        

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
        // p($req->all());
        // p($where_in_clause); die;
       
            $quotes=$this->quotes
            ->with('quote_products')
            ->with('customer')
            ->with('driver')
            ->with('sub')
            ->where($where_clause)
            ->orderBy('created_at', 'desc');
            if(
                isset($req->from_date) &&
                !empty($req->from_date) &&
                isset($req->to_date) &&
                !empty($req->from_date)
            ){
                echo $from=strtotime($req->from_date);
                echo '<br>';
                echo $to=strtotime($req->to_date);
                echo '<br>';
                $quotes=$quotes->WhereBetween('created_at', [$from, $to]);
            }
            

            foreach($where_in_clause as $column => $values){
                
                $quotes = $quotes->whereIn($column, $values);
            }

            $quotesData=$quotes->paginate($pagination_per_page);
            //echo $quotesData=$quotes->toSql();
//p($quotesData); die;
            return view('adminpanel.deliveries',get_defined_vars());
        
      }
      public function previous_deliveries(){
        $user=Auth::user();
        if($user->group_id==config('constants.groups.admin')){
            $quotesData=$this->quotes
            ->with('quote_products')
            ->with('customer')
            ->with('driver')
            ->where('is_active', 1)
            ->where('status', config('constants.quote_status.complete'))
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
            ->where('status', config('constants.quote_status.complete'))
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }
        elseif($user->group_id==config('constants.groups.driver')){
            
            $quotesData=$this->quotes
            ->with('quote_products')
            ->with('customer')
            ->with('driver')
            ->where('is_active', 1)
            ->where('driver_id', get_session_value('id'))
            ->where('status', config('constants.quote_status.complete'))
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            //dd(get_session_value('id'));
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

            
            if($req->export=='export_xls')
            {
             
                $quotesData=$quotes->get()->toArray();
                $exportData=array();
                foreach($quotesData as $key=>$data){
                    $elevator='Not Available';
                    if($data['elevator']==1)
                    $elevator='Available';
                    $exportData[]=[
                        'id'=>$data['id'],
                        'po_number'=>$data['po_number'],
                        'quote_type'=>$data['quote_type'],
                        'business_type'=>$data['business_type'],
                        'elevator'=>$elevator,
                        'no_of_appartments'=>$data['no_of_appartments'],
                        'list_of_floors'=>(isset($data['list_of_floors']) && $data['list_of_floors']!='null')?implode(',',json_decode($data['list_of_floors'],true)):'',
                        'pickup_street_address'=>$data['pickup_street_address'],
                        'pickup_unit'=>$data['pickup_unit'],
                        'pickup_state'=>$data['pickup_state'],
                        'pickup_city'=>$data['pickup_city'],
                        'pickup_zipcode'=>$data['pickup_zipcode'],
                        'pickup_contact_number'=>$data['pickup_contact_number'],
                        'pickup_date'=>$data['pickup_date'],
                        'drop_off_street_address'=>$data['drop_off_street_address'],
                        'drop_off_unit'=>$data['drop_off_unit'],
                        'drop_off_city'=>$data['drop_off_city'],
                        'drop_off_zipcode'=>$data['drop_off_zipcode'],
                        'drop_off_contact_number'=>$data['drop_off_contact_number'],
                        'drop_off_date'=>$data['drop_off_date'],
                        'drop_off_instructions'=>$data['drop_off_instructions'],
                        'status'=>quote_status_msg($data['status']),
                        'customer_name'=>$data['customer']['name'],
                        'customer_email'=>$data['customer']['email'],
                        'customer_mobileno'=>$data['customer']['mobileno'],
                        'customer_business_name'=>$data['customer']['business_name'],
                        'driver_name'=>(isset($data['driver']['name']) && !empty($data['driver']['name']))?$data['driver']['name']:'',
                        'driver_email'=>(isset($data['driver']['email']) && !empty($data['driver']['email']))?$data['driver']['email']:'',
                        'driver_mobileno'=>(isset($data['driver']['mobileno']) && !empty($data['driver']['mobileno']))?$data['driver']['mobileno']:'',
                        'driver_license_no'=>(isset($data['driver']['license_no']) && !empty($data['driver']['license_no']))?$data['driver']['license_no']:'',
                        
                    ];
                }
                return Excel::download(new ExportQuotes($exportData), 'quotes-deliveries.xlsx');
            }
            else{
                $quotesData=$quotes->paginate($pagination_per_page);
                return view('adminpanel.reports_quotes_deliveries',get_defined_vars());
            }
            

            $query=$quotes->toSql();
            p($query);
            p($quotesData);
            die;

            
        
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
      public function download_pdf_deliery($id){
        $user=Auth::user();
        $products=$this->product_categories->with('products')
            ->where('is_active', '=', 1)
            ->get()->toArray();

            $relations=[
                'quote_products',
                'customer',
                'delivery_proof',
                'document_for_delivery',
                'delivery_documents_for_admin',
                'delivery_documents_for_driver',
                'driver',
                'quote_prices',
                'comments',
                'delivery_notes',
            ];
    
            $where_clause[]=['id',$id];
            $where_clause[]=['status','>=',config('constants.quote_status.delivery')];
         
              $quotesData=$this->quotes
               ->with($relations)
               ->where('id', $id)
               ->where($where_clause)
               ->orderBy('created_at', 'desc')->get()->toArray();
    
               if(empty($quotesData)){
                echo 'There is something wrong'; die;
               }
               
              $quotesData=$quotesData[0];

        $fileName=$quotesData['po_number'].'-'.date('d/m/Y h:i:s',time());
        $pdf_blade_path='adminpanel/pdf';
        $pdf_blade_path='adminpanel/invoice_pdf';

        view()->share($pdf_blade_path,get_defined_vars());

        $PDFOptions = ['defaultFont' => 'sans-serif'];

        $pdf = PDF::loadView($pdf_blade_path, get_defined_vars())->setOptions($PDFOptions);
        $pdf->getDomPDF()->setHttpContext(
            stream_context_create([
                'ssl' => [
                    'allow_self_signed'=> TRUE,
                    'verify_peer' => FALSE,
                    'verify_peer_name' => FALSE,
                ]
            ])
        );

      }
      public function view_delivery($id){
        $user=Auth::user();
        $products=$this->product_categories->with('products')
            ->where('is_active', '=', 1)
            ->get()->toArray();
    
            //$query = $this->quotes->query();
            // $restaurant = $query->where('id', $id)->with('products', function ($q) use ($status) {
            //     $q->where('type', $status);
            // })->first();
            // dd($restaurant);

        $relations=[
            'quote_products',
            'customer',
            'delivery_proof',
            'document_for_delivery',
            'delivery_documents_for_admin',
            'delivery_documents_for_driver',
            'driver',
            'sub',
            'quote_prices',
            'comments',
            'delivery_notes',
            'invoices',
        ];

        $where_clause[]=['id',$id];
        $where_clause[]=['status','>=',config('constants.quote_status.delivery')];
    // p($where_clause);
          $quotesData=$this->quotes
           ->with($relations)
           ->where('id', $id)
           ->where($where_clause)
           ->orderBy('created_at', 'desc')->get()->toArray();

           if(empty($quotesData)){
            echo 'There is something wrong  '; die;
           }
           
          $quotesData=$quotesData[0];
        
        return view('adminpanel.view_delivery',get_defined_vars());

      }
      public function save_delivery_invoice_data($id,Request $request){
        $validator=$request->validate([
            'payee_name'=>'required',
            //'payee_phone '=>'required',
            'paid_amount'=>'required',
        ]);
        
        $deliveryData=$this->quotes
        ->with('invoices')
        ->with('customer')
        ->where('id',$id)->get()->toArray();
        $deliveryData=$deliveryData[0];
        //p($deliveryData); die;
        
        if(!empty($deliveryData['invoices']))
        {
            $invoice_no=$deliveryData['invoices'][0]['invoice_no'];  
            $this->invoices->invoice_no=$invoice_no;
        }
        else{
            $this->invoices->invoice_no=date('dmy',time()).'-'.$id;   
        }
        
        $this->invoices->payee_name=$request['payee_name'];
        $this->invoices->payee_phone=$request['payee_phone'];
        $this->invoices->slug ='delivery_payment';
        $this->invoices->description=$request['description'];
        $this->invoices->paid_amount=$request['paid_amount'];
        $this->invoices->quote_id=$id;
        $this->invoices->created_by=get_session_value('id');
        $this->invoices->save();

    

        // Email Section 
        if(empty($deliveryData['invoices'])){
            $mailData['body_message']='This email is to confirm that a delivery was confirmed for '.$deliveryData['drop_off_date'].'. we have received a deposit of $'.$request['paid_amount'].' for the delivery
            having PO No. '.$deliveryData['po_number'].' .Please find all Delivery details below.';
            $mailData['body_message'] .=quote_data_for_mail($id);
            $mailData['body_message'] .='<br>If you see any mistakes in the delivery and for any concerns please call us right away at 845-501-1888';
            $mailData['subject']='Delivery Payment Received';
            $mailData['button_title']='Login';
            $mailData['button_link']=route('admin.loginform');
            
            if(isset($toEmail))
            $emailAdd[]=$deliveryData['customer']['userinfo'][0]['email'];
            $emailAdd[]=config('constants.admin_email');
                    if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
                        echo 'Thank you, Your Booking has been confirmed';
                    }
            // End 
            
        }
       
        //activity Logged
        $activityComment='Mr.'.get_session_value('name').' Received Payment ';
        $activityData=array(
            'user_id'=>get_session_value('id'),
            'action_taken_on_id'=>$id,
            'action_slug'=>'new_payment',
            'comments'=>$activityComment,
            'others'=>'quotes_invoices',
            'created_at'=>date('Y-m-d H:I:s',time()),
        );
        $activityID=log_activity($activityData);


        $request->session()->flash('alert-success', 'Payment Added in System');

        return redirect()->back();
        //redirect route('bookings.save_booking_edit_data', $id);
    }
     // View invoice Bookings
     public function invoice_delivery($id, Request $req){
        
        $user=Auth::user(); 
        $deliveryData=$this->quotes
        ->with('customer')
        ->with('driver')
        ->with('invoices')
        ->with('quote_agreed_cost')
        ->with('files')
        ->with('comments')
        ->where('id',$id)
        ->orderBy('created_at', 'desc')->get()->toArray();
        $delivery=$deliveryData[0];
 //p($delivery);
// die;
        //$assigne_photographers=$this->bookings_users->with('userinfo')->where('booking_id',$id)->where('group_id',config('constants.groups.photographer'))->get()->toArray();
        // if(!isset($req['deb']) && $req['deb']!=1)
        // return view('adminpanel/invoice_booking',get_defined_vars());
        // return view('adminpanel/pdf',get_defined_vars());
        // For Creating PDF:
// share data to view
if(!empty($delivery['invoices']))
$invoice_no=$delivery['invoices'][0]['invoice_no'];
else{
    
    $invoice_no=date('dmy',time()).'-'.$id;
    $invoice_no=generateInvoiceNumber();//date('dmy',time()).'-'.$id;
}

        $pdf_blade_path='adminpanel/pdf';
        $pdf_blade_path='adminpanel/invoice_pdf';
        $fileName='customer-'.$invoice_no.'-'.date(config('constants.date_formate'),time());
        view()->share($pdf_blade_path,get_defined_vars());
        
        $PDFOptions = ['defaultFont' => 'sans-serif'];

        //return view($pdf_blade_path,get_defined_vars());

        $pdf = PDF::loadView($pdf_blade_path, get_defined_vars())->setOptions($PDFOptions);
        $pdf->getDomPDF()->setHttpContext(
            stream_context_create([
                'ssl' => [
                    'allow_self_signed'=> TRUE,
                    'verify_peer' => FALSE,
                    'verify_peer_name' => FALSE,
                ]
            ])
        );

        // $files=[$fileName.'.pdf'];
        // $mailData['body_message']='An Invoice is downloaded of delivery having  '.$delivery['po_number'].' was scheduled to be picked up and delivered on '.$quoteData['drop_off_date'].' if you have any additional questions please feel free to contact us at 718-218-5239';
        // $mailData['subject']='Invoice of '.$delivery['customer']['name'];
        // Mail::send('emails.delivered', $mailData, function($message)use($mailData, $files) {
        //     $message->to($mailData["email"])
        //     ->subject($mailData["subject"]);

        //     foreach ($files as $file){
        //     $message->attach($file);
        //     }            
        //     });

        //activity Logged
        $activityComment='Mr.'.get_session_value('name').' downloaded customer invoice';
        $activityData=array(
            'user_id'=>get_session_value('id'),
            'action_taken_on_id'=>$id,
            'action_slug'=>'downloaded_invoice',
            'comments'=>$activityComment,
            'others'=>'bookings',
            'created_at'=>date('Y-m-d H:I:s',time()),
        );
        $activityID=log_activity($activityData);

            // download PDF file with download method
            return $pdf->download($fileName.'.pdf');

}
     public function send_delivery_invoice($id, Request $req){
        
        $user=Auth::user(); 
        $deliveryData=$this->quotes
        ->with('customer')
        ->with('driver')
        ->with('invoices')
        ->with('quote_agreed_cost')
        ->with('files')
        ->with('comments')
        ->where('id',$id)
        ->orderBy('created_at', 'desc')->get()->toArray();
        $delivery=$deliveryData[0];

        if(!empty($delivery['invoices']))
        $invoice_no=$delivery['invoices'][0]['invoice_no'];
        else
        $invoice_no=date('dmy',time()).'-'.$id;

        $pdf_blade_path='adminpanel/pdf';
        $pdf_blade_path='adminpanel/invoice_pdf';

        $fileName='customer-'.$invoice_no.'-'.date(config('constants.date_formate'),time());
        view()->share($pdf_blade_path,get_defined_vars());
        
        $PDFOptions = ['defaultFont' => 'sans-serif'];

       // return view($pdf_blade_path,get_defined_vars());

        $pdf = PDF::loadView($pdf_blade_path, get_defined_vars())->setOptions($PDFOptions);
        $pdf->getDomPDF()->setHttpContext(
            stream_context_create([
                'ssl' => [
                    'allow_self_signed'=> TRUE,
                    'verify_peer' => FALSE,
                    'verify_peer_name' => FALSE,
                ]
            ])
        );

        
        $mailData['body_message']='Please find attached invoice of delivery having  '.$delivery['po_number'].' was scheduled to be picked up at '.$delivery['pickup_date'].' and delivered on '.$delivery['drop_off_date'].' if you have any questions please feel free to contact us at 718-218-5239';
        $mailData['subject']='Invoice #:'.$invoice_no.' of '.$delivery['customer']['name']. ' having PO No.:'.$delivery['po_number'];
        
        $emailAdd=[
            $delivery['customer']['email'],
        ];
       
        $mailData["email"] = $emailAdd;

        Mail::send('emails.invoice', $mailData, function($message)use($mailData, $pdf,$fileName) {
            $message->to($mailData["email"])
            ->subject($mailData["subject"])
            ->attachData($pdf->output(), $fileName);
           
            });

        //activity Logged
        $activityComment='Mr.'.get_session_value('name').' sent customer invoice';
        $activityData=array(
            'user_id'=>get_session_value('id'),
            'action_taken_on_id'=>$id,
            'action_slug'=>'invoice_sent_to_customer',
            'comments'=>$activityComment,
            'others'=>'invoices',
            'created_at'=>date('Y-m-d H:I:s',time()),
        );
        $activityID=log_activity($activityData);

        $req->session()->flash('alert-success', 'Invoice sent to the customer !');

         return redirect()->back();
            // download PDF file with download method
            //return $pdf->download($fileName.'.pdf');

}
      public function view_quote($id){

        $user=Auth::user();
        $products=$this->product_categories->with('products')
            ->where('is_active', '=', 1)
            ->get()->toArray();
    
           $quotesData=$this->quotes
           ->with(['quote_products','delivery_proof','customer','driver','quote_prices','comments','document_for_request_quote'])
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
    public function quotes($type=NULL, Request $req){
        $user=Auth::user();
        $imagesTypes=array('jpg','jpeg','png','gif');
        $excelTypes=array('xls','xlsx');
        $docTypes=array('doc','docx');

        $pagination_per_page=config('constants.per_page');
        // if(isset($req->action) && $req->action=='search_form')
         //$pagination_per_page=200;  

        $withArray=array('document_for_request_quote','quote_products');

            if($type=='trash'){
                $list_title=' Trashed Quotes';
                $where_clause=[
                    ['status', '<', config('constants.quote_status.delivery')],
                    ['is_active', '=', 2],
                ];
            }
            elseif($type=='requested'){
                $list_title='Requested Quotes';
                $where_clause=[
                    ['status', '=', config('constants.quote_status.pending')],
                    ['is_active', '=', 1],
                ];
                
            }
            elseif($type=='cancelled'){
                $list_title='Cancelled Quotes';
                $where_clause=[
                    ['status', '=', config('constants.quote_status.declined')],
                    ['is_active', '=', 1],
                ];
          
          
            }
            elseif($type=='new'){
                $list_title='New Quotes';
                $where_clause=[
                    ['status', '=', config('constants.quote_status.quote_submitted')],
                    ['is_active', '=', 1],
                ];
          
            }
            elseif($type=='approved'){
                $list_title='Approved Quotes';
                $where_clause=[
                    ['status', '=', config('constants.quote_status.approved')],
                    ['is_active', '=', 1],
                ];
          
            }
            else{
                $list_title='Quotes';
                $where_clause=[
                    ['status', '=', config('constants.quote_status.pending')],
                    ['is_active', '=', 1],
                ];
                
            }
            $where_clause[]=['po_number', '!=', ''];
            //p($req->all()); die;
            // if Data is searched 
            $where_in_clause=$customer_ids=$driver_ids=$quote_status=array();
            
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
            // If user is customer or Admin
            $view='adminpanel.quotes';
            if($user->group_id==config('constants.groups.customer')){
            $where_clause[]=['customer_id', '=', get_session_value('id')];
            $view='adminpanel.user_quotes';
            }
            $quotes=$this->quotes
                ->with($withArray)
                ->where($where_clause)
                ->orderBy('created_at', 'desc');
            
                if(
                    isset($req->from_date) &&
                    !empty($req->from_date) &&
                    isset($req->to_date) &&
                    !empty($req->from_date)
                ){
                    echo $from=strtotime($req->from_date);
                    echo '<br>';
                    echo $to=strtotime($req->to_date);
                    echo '<br>';
                    $quotes=$quotes->WhereBetween('created_at', [$from, $to]);
                }
                
    
                foreach($where_in_clause as $column => $values){
                    
                    $quotes = $quotes->whereIn($column, $values);
                }
                //p($where_clause);
                //echo $quotesData=$quotes->toSql(); die;
                //  $quotesData=$quotes->get()->toArray();
                //  p($quotesData); 
                $quotesData=$quotes->paginate($pagination_per_page);
                
            return view($view,get_defined_vars());
            
    }
    public function quote_requested_documents($id){
        $user=Auth::user(); 
        //$files=$this->files->where(['quote_id'=>$id, 'slug'=>'quote_request_file'])->get()->toArray();
        $quotesData=$this->quotes->where(['id'=>$id])->with(['document_for_request_quote','customer'])->get()->toArray();
        $quotesData=$quotesData[0];
        //p($quotesData); die;
        return view('adminpanel.view_quote_documents',get_defined_vars());
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
      
        return view('adminpanel.add_to_delivery',get_defined_vars());
    }
    public function save_add_to_delivery($id,Request $request){
       
        
        if(isset($request['assign_to']) && $request['assign_to']==1){
            $validatorArray=['driver_id'=>'required'];

            $data_to_update['driver_id']=$request['driver_id'];
            $data_to_update['status']=config('constants.quote_status.delivery');

            $data_to_update['sub_id']=NULL;
            $data_to_update['quoted_price_for_sub']=NULL;
            $data_to_update['assign_to']=1;
            $flashMsg='Driver assigned Successfully !';
        }
        else{
            $validatorArray=['sub_id'=>'required'];
            $validatorArray=['quoted_price_for_sub'=>'required'];

            $data_to_update['sub_id']=$request['sub_id'];
            $data_to_update['status']=config('constants.quote_status.delivery');
            $data_to_update['quoted_price_for_sub']=$request['quoted_price_for_sub'];
            $data_to_update['sub_status']=0;
            $data_to_update['assign_to']=2;
            $flashMsg='Sub assigned Successfully !';

        }
        $validator=$request->validate($validatorArray);
        
        $driverData=$this->users->where('id',$request['driver_id'])->get('email')->toArray();
        $driverData=$driverData[0];

        $this->quotes->where('id',$id)->update($data_to_update);
        $request->session()->flash('alert-success', $flashMsg);

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

            if($request['assign_to']==1){
                
                $assign_to='Driver';
                $mailData['body_message']='You are assigned a new delivery, having PO Number:'.$request['po_number'].'. Please login to the CRM and look for details';
                $mailData['subject']='New Delivery Assigned';
                $emailAdd=[
                            //config('constants.admin_email'),
                            $driverData['email'],
                        ];
            }
            else{

                $assign_to='Sub';

                DB::table('quote_prices')->insert([
                    'quoted_price' => $request['quoted_price_for_sub'],
                    'slug' => 'quoted_price_for_sub',
                    'quoted_uid' => get_session_value('id'),
                    'quote_id' =>$id,
                    'quote_price_for' =>2
                ]);

                    $sub=$this->users->where(['id'=>$request['sub_id']])->get(['name','business_email','email'])->first();

                    $mailData['body_message']='You are assigned a new delivery, having PO Number:'.$request['po_number'].'.please click approve to be added to the delivery schedule. if you have any questions please feel free to contact us at 718-218-5239';
                    $mailData['subject']='New Delivery Assigned';
                    $mailData['body_message'] .='<table width="100%" border="1">
                    <tr><td>Delivery Cost  :</td><td>$'.$request['quoted_price_for_sub'].'</td></tr>';
                    $mailData['body_message'] .='<tr><td colspan="2">'. quote_data_for_mail($id, config('constants.groups.sub')).'</td></tr>';
                    $mailData['body_message'] .='</table>';
                    $mailData['subject']='Your New Delivery From Oodler Express';
                
                    $mailData['button_title']='APPROVE';
                    $mailData['button_link']=route('sub_action',['quote_id' => $id,'action'=>base64_encode('accept')]);
                    $mailData['button_title2']='Reject';
                    $mailData['button_link2']=route('sub_action',['quote_id' => $id,'action'=>base64_encode('reject')]);

                    $emailAdd=[
                                $sub->email,
                            ];
                   
            }
            
            if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
                $dataArray['emailMsg']='Email Sent Successfully';
            }


        // Activity Log
        $activityComment='Mr.'.get_session_value('name').' assigned '.$assign_to.' to quote having PO number : '.$request['po_number'];
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
    public function delivery_edit_form($id=NULL){
       $user=Auth::user();
       
       $quotesData=$this->quotes
       ->with('quote_products')
       ->where('id', $id)
       ->orderBy('created_at', 'desc')->get()->toArray();
       $quotesData=$quotesData[0];
    
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
        return view('adminpanel.edit_delivery',get_defined_vars());
    }
    public function save_delivery_edit($id,Request $request){
       
        $validator=$request->validate([
            'quote_type'=>'required',
            'business_type'=>'required',
            'po_number'=>'required',
            'pickup_street_address1'=>'required',
            //'pickup_contact_number1'=>'required',
            'pickup_date1'=>'required',
            'drop_off_street_address'=>'required',
            //'drop_off_contact_number'=>'required',
            'drop_off_date'=>'required',
            //'driver_id'=>'required',
            
        ]);
        
         
        $to_update_date['quote_type']=$request['quote_type'];
        $to_update_date['elevator']=$request['elevator'];
        $to_update_date['no_of_appartments']=$request['no_of_appartments'];
        $to_update_date['list_of_floors']=json_encode($request['list_of_floors']);
        $to_update_date['business_type']=$request['business_type'];
        $to_update_date['po_number']=$request['po_number'];
        $to_update_date['pickup_street_address']=$request['pickup_street_address1'];
        $to_update_date['pickup_unit']=$request['pickup_unit1'];
        //$to_update_date['pickup_state']=$request['pickup_state1'];
        //$to_update_date['pickup_city']=$request['pickup_city1'];
        $to_update_date['pickup_zipcode']=$request['pickup_zipcode1'];
        $to_update_date['pickup_contact_number']=$request['pickup_contact_number1'];
        $to_update_date['pickup_email']=$request['pickup_email1'];
        $to_update_date['pickup_date']=$request['pickup_date1'];
        
        $to_update_date['drop_off_street_address']=$request['drop_off_street_address'];
        $to_update_date['drop_off_unit']=$request['drop_off_unit'];
        //$to_update_date['drop_off_state']=$request['drop_off_state'];
        //$to_update_date['drop_off_city']=$request['drop_off_city'];
        $to_update_date['drop_off_zipcode']=$request['drop_off_zipcode'];
        $to_update_date['drop_off_contact_number']=$request['drop_off_contact_number'];
        $to_update_date['drop_off_email']=$request['drop_off_email'];
        $to_update_date['drop_off_instructions']=$request['drop_off_instructions'];
        $to_update_date['drop_off_date']=$request['drop_off_date'];
        
        // $to_update_date['customer_id']=get_session_value('id');
        // if(isset($request['customer_id']) && $request['customer_id']>0)
        // $to_update_date['customer_id']=$request['customer_id'];
        //$to_update_date['driver_id']=$request['driver_id'];
        
       $this->quotes->where('id',$id)->update($to_update_date);
       $this->quote_products->where('quote_id',$id)->delete();
       $this->pickup_dropoff_address->where('quote_id',$id)->delete();

       
        if(isset($request['product_details1']))
       foreach($request['product_details1'] as $key=>$productData){
            
        if(isset($productData['product_name']) && count($productData['product_name'])>0){

            DB::table('pickup_dropoff_address')->insert([
                ['pickup_street_address' => $request['pickup_street_address1'],
                 //'pickup_state' =>$request['pickup_state1'],
                 //'pickup_city' => $request['pickup_city1'],
                 'pickup_zipcode' => $request['pickup_zipcode1'],
                 'pickup_contact_number' => $request['pickup_contact_number1'],
                 'pickup_email' => $request['pickup_email1'],
                 'pickup_date' => $request['pickup_date1'],
                 'drop_off_street_address' => $request['drop_off_street_address'],
                 'drop_off_unit' => $request['drop_off_unit'],
                 //'drop_off_state' => $request['drop_off_state'],
                 //'drop_off_city' => $request['drop_off_city'],
                 'drop_off_zipcode' => $request['drop_off_zipcode'],
                 'drop_off_contact_number' => $request['drop_off_contact_number'],
                 'drop_off_email' => $request['drop_off_email'],
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
    if(isset($request['pickup_date2']) && ($request['pickup_date2'])!='' && $request['product_details2'])
    foreach($request['product_details2'] as $key=>$productData){
            
        if(isset($productData['product_name']) && count($productData['product_name'])>0){

            DB::table('pickup_dropoff_address')->insert([
                ['pickup_street_address' => $request['pickup_street_address2'],
                 //'pickup_state' =>$request['pickup_state2'],
                 //'pickup_city' => $request['pickup_city2'],
                 'pickup_zipcode' => $request['pickup_zipcode2'],
                 'pickup_contact_number' => $request['pickup_contact_number2'],
                 'pickup_email' => $request['pickup_email2'],
                 'pickup_date' => $request['pickup_date2'],
                 'drop_off_street_address' => $request['drop_off_street_address'],
                 'drop_off_unit' => $request['drop_off_unit'],
                 //'drop_off_state' => $request['drop_off_state'],
                 //'drop_off_city' => $request['drop_off_city'],
                 'drop_off_zipcode' => $request['drop_off_zipcode'],
                 'drop_off_contact_number' => $request['drop_off_contact_number'],
                 'drop_off_email' => $request['drop_off_email'],
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
    if(isset($request['pickup_date3']) && ($request['pickup_date3'])!='' && $request['product_details3'])
    foreach($request['product_details3'] as $key=>$productData){
            
        if(isset($productData['product_name']) && count($productData['product_name'])>0){

            DB::table('pickup_dropoff_address')->insert([
                ['pickup_street_address' => $request['pickup_street_address3'],
                 //'pickup_state' =>$request['pickup_state3'],
                 //'pickup_city' => $request['pickup_city3'],
                 'pickup_zipcode' => $request['pickup_zipcode3'],
                 'pickup_contact_number' => $request['pickup_contact_number3'],
                 'pickup_email' => $request['pickup_email3'],
                 'pickup_date' => $request['pickup_date3'],
                 'drop_off_street_address' => $request['drop_off_street_address'],
                 'drop_off_unit' => $request['drop_off_unit'],
                 //'drop_off_state' => $request['drop_off_state'],
                 //'drop_off_city' => $request['drop_off_city'],
                 'drop_off_zipcode' => $request['drop_off_zipcode'],
                 'drop_off_contact_number' => $request['drop_off_contact_number'],
                 'drop_off_email' => $request['drop_off_email'],
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
    public function quotes_edit_form($id=NULL){
       $user=Auth::user();

       $withArray=array('document_for_request_quote','quote_products');

       $quotesData=$this->quotes
       ->with($withArray)
       ->where('id', $id)
       ->orderBy('created_at', 'desc')->get()->toArray();
       $quotesData=$quotesData[0];
    
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
            //'pickup_contact_number1'=>'required',
            'pickup_date1'=>'required',
            'drop_off_street_address'=>'required',
            //'drop_off_contact_number'=>'required',
            'drop_off_date'=>'required',
            
        ]);
        
         
        $to_update_date['quote_type']=$request['quote_type'];
        $to_update_date['elevator']=$request['elevator'];
        $to_update_date['no_of_appartments']=$request['no_of_appartments'];
        
        if(!empty($request['list_of_floors']))
        $to_update_date['list_of_floors']=json_encode($request['list_of_floors']);

        $to_update_date['business_type']=$request['business_type'];
        $to_update_date['po_number']=$request['po_number'];
        $to_update_date['pickup_street_address']=$request['pickup_street_address1'];
        $to_update_date['pickup_unit']=$request['pickup_unit1'];
        //$to_update_date['pickup_state']=$request['pickup_state1'];
        //$to_update_date['pickup_city']=$request['pickup_city1'];
        $to_update_date['pickup_zipcode']=$request['pickup_zipcode1'];
        $to_update_date['pickup_contact_number']=$request['pickup_contact_number1'];
        $to_update_date['pickup_email']=$request['pickup_email1'];
        $to_update_date['pickup_date']=$request['pickup_date1'];
        
        $to_update_date['drop_off_street_address']=$request['drop_off_street_address'];
        $to_update_date['drop_off_unit']=$request['drop_off_unit'];
        // $to_update_date['drop_off_state']=$request['drop_off_state'];
        // $to_update_date['drop_off_city']=$request['drop_off_city'];
        $to_update_date['drop_off_zipcode']=$request['drop_off_zipcode'];
        $to_update_date['drop_off_contact_number']=$request['drop_off_contact_number'];
        $to_update_date['drop_off_email']=$request['drop_off_email'];
        $to_update_date['drop_off_instructions']=$request['drop_off_instructions'];
        $to_update_date['drop_off_date']=$request['drop_off_date'];
        
        $to_update_date['customer_id']=get_session_value('id');
        if(isset($request['customer_id']) && $request['customer_id']>0)
        $to_update_date['customer_id']=$request['customer_id'];
        
       $this->quotes->where('id',$id)->update($to_update_date);
       $this->quote_products->where('quote_id',$id)->delete();
       $this->pickup_dropoff_address->where('quote_id',$id)->delete();

       
        if(isset($request['product_details1']))
       foreach($request['product_details1'] as $key=>$productData){
            
        if(isset($productData['product_name']) && count($productData['product_name'])>0){

            DB::table('pickup_dropoff_address')->insert([
                ['pickup_street_address' => $request['pickup_street_address1'],
                //  'pickup_state' =>$request['pickup_state1'],
                //  'pickup_city' => $request['pickup_city1'],
                 'pickup_zipcode' => $request['pickup_zipcode1'],
                 'pickup_contact_number' => $request['pickup_contact_number1'],
                 'pickup_email' => $request['pickup_email1'],
                 'pickup_date' => $request['pickup_date1'],
                 'drop_off_street_address' => $request['drop_off_street_address'],
                 'drop_off_unit' => $request['drop_off_unit'],
                //  'drop_off_state' => $request['drop_off_state'],
                //  'drop_off_city' => $request['drop_off_city'],
                 'drop_off_zipcode' => $request['drop_off_zipcode'],
                 'drop_off_contact_number' => $request['drop_off_contact_number'],
                 'drop_off_email' => $request['drop_off_email'],
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
                //  'pickup_state' =>$request['pickup_state2'],
                //  'pickup_city' => $request['pickup_city2'],
                 'pickup_zipcode' => $request['pickup_zipcode2'],
                 'pickup_contact_number' => $request['pickup_contact_number2'],
                 'pickup_email' => $request['pickup_email2'],
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
                //  'pickup_state' =>$request['pickup_state3'],
                //  'pickup_city' => $request['pickup_city3'],
                 'pickup_zipcode' => $request['pickup_zipcode3'],
                 'pickup_contact_number' => $request['pickup_contact_number3'],
                 'pickup_email' => $request['pickup_email3'],
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
    public function add_delivery_form($customer_id){
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
       
        return view('adminpanel.add_new_delivery',get_defined_vars());
    }
    public function save_new_delivery_data($customer_id,Request $request){
       
        $validatorArray=[
            'quote_type'=>'required',
            'business_type'=>'required',
            'po_number'=>'required',
            'pickup_street_address1'=>'required',
            'pickup_date1'=>'required',
            'drop_off_street_address'=>'required',
            'drop_off_date'=>'required',
            'quoted_price'=>'required',
        ];
        if(isset($request['assign_to']) && $request['assign_to']==1){
            $validatorArray['driver_id']='required';
            $this->quotes->driver_id=$request['driver_id'];
        }
        else{
            $validatorArray['sub_id']='required';
            $validatorArray['quoted_price_for_sub']='required';
            $this->quotes->sub_id=$request['sub_id'];
            $this->quotes->quoted_price_for_sub=$request['quoted_price_for_sub'];
            
        }
        

        $validator=$request->validate($validatorArray);
        
         
        $this->quotes->assign_to=$request['assign_to'];
        $this->quotes->quote_type=$request['quote_type'];
        $this->quotes->elevator=$request['elevator'];
        $this->quotes->no_of_appartments=$request['no_of_appartments'];
        $this->quotes->list_of_floors=json_encode($request['list_of_floors']);
        $this->quotes->business_type=$request['business_type'];
        $this->quotes->po_number=$request['po_number'];
       
        $this->quotes->pickup_street_address=$request['pickup_street_address1'];
        $this->quotes->pickup_unit=$request['pickup_unit1'];
        // $this->quotes->pickup_state=$request['pickup_state1'];
        // $this->quotes->pickup_city=$request['pickup_city1'];
        $this->quotes->pickup_zipcode=$request['pickup_zipcode1'];
        $this->quotes->pickup_contact_number=$request['pickup_contact_number1'];
        $this->quotes->pickup_email=$request['pickup_email1'];
        $this->quotes->pickup_date=$request['pickup_date1'];
        $this->quotes->drop_off_street_address=$request['drop_off_street_address'];
        $this->quotes->drop_off_unit=$request['drop_off_unit'];
        $this->quotes->drop_off_state=$request['drop_off_state'];
        $this->quotes->drop_off_city=$request['drop_off_city'];
        $this->quotes->drop_off_zipcode=$request['drop_off_zipcode'];
        $this->quotes->drop_off_contact_number=$request['drop_off_contact_number'];
        $this->quotes->drop_off_email=$request['drop_off_email'];
        $this->quotes->drop_off_instructions=$request['drop_off_instructions'];
        $this->quotes->drop_off_date=$request['drop_off_date'];
        
        $this->quotes->customer_id=$customer_id;
        //$this->quotes->driver_id=$request['driver_id'];
        $this->quotes->status=config('constants.quote_status.delivery');
        
        

        $this->quotes->created_at=time();

       $this->quotes->save();
       
       $this->quote_prices->quoted_price=$request['quoted_price'];
       $this->quote_prices->extra_charges=$request['extra_charges'];
       $this->quote_prices->reason_for_extra_charges=$request['reason_for_extra_charges'];
       $this->quote_prices->slug=phpslug('quote_sent');
       $this->quote_prices->quoted_uid=get_session_value('id');
       $this->quote_prices->quote_id=$this->quotes->id;
       $this->quote_prices->save();
       
       // if Sub is being added then we need to save its price
       if(isset($request['assign_to']) && $request['assign_to']==2){
       
                DB::table('quote_prices')->insert([
                    'quoted_price' => $request['quoted_price_for_sub'],
                    'slug' => 'quoted_price_for_sub',
                    'quoted_uid' => get_session_value('id'),
                    'quote_id' =>$this->quotes->id,
                    'quote_price_for' =>2
                ]);

       }
       if(isset($request['product_details1']))
       foreach($request['product_details1'] as $key=>$productData){
            
        if(isset($productData['product_name']) && count($productData['product_name'])>0){

            DB::table('pickup_dropoff_address')->insert([
                ['pickup_street_address' => $request['pickup_street_address1'],
                //  'pickup_state' =>$request['pickup_state1'],
                //  'pickup_city' => $request['pickup_city1'],
                 'pickup_zipcode' => $request['pickup_zipcode1'],
                 'pickup_contact_number' => $request['pickup_contact_number1'],
                 'pickup_email' => $request['pickup_email1'],
                 'pickup_date' => $request['pickup_date1'],
                 'drop_off_street_address' => $request['drop_off_street_address'],
                 'drop_off_unit' => $request['drop_off_unit'],
                 'drop_off_state' => $request['drop_off_state'],
                 'drop_off_city' => $request['drop_off_city'],
                 'drop_off_zipcode' => $request['drop_off_zipcode'],
                 'drop_off_contact_number' => $request['drop_off_contact_number'],
                 'drop_off_email' => $request['drop_off_email'],
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
                //  'pickup_state' =>$request['pickup_state2'],
                //  'pickup_city' => $request['pickup_city2'],
                 'pickup_zipcode' => $request['pickup_zipcode2'],
                 'pickup_contact_number' => $request['pickup_contact_number2'],
                 'pickup_email' => $request['pickup_email2'],
                 'pickup_date' => $request['pickup_date2'],
                 'drop_off_street_address' => $request['drop_off_street_address'],
                 'drop_off_unit' => $request['drop_off_unit'],
                 'drop_off_state' => $request['drop_off_state'],
                 'drop_off_city' => $request['drop_off_city'],
                 'drop_off_zipcode' => $request['drop_off_zipcode'],
                 'drop_off_contact_number' => $request['drop_off_contact_number'],
                 'drop_off_email' => $request['drop_off_email'],
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
                //  'pickup_state' =>$request['pickup_state3'],
                //  'pickup_city' => $request['pickup_city3'],
                 'pickup_zipcode' => $request['pickup_zipcode3'],
                 'pickup_contact_number' => $request['pickup_contact_number3'],
                 'pickup_email' => $request['pickup_email3'],
                 'pickup_date' => $request['pickup_date3'],
                 'drop_off_street_address' => $request['drop_off_street_address'],
                 'drop_off_unit' => $request['drop_off_unit'],
                 'drop_off_state' => $request['drop_off_state'],
                 'drop_off_city' => $request['drop_off_city'],
                 'drop_off_zipcode' => $request['drop_off_zipcode'],
                 'drop_off_contact_number' => $request['drop_off_contact_number'],
                 'drop_off_email' => $request['drop_off_email'],
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

    // if Sub is selected then send an email to sub for approve or reject
    if(isset($request['assign_to']) && $request['assign_to']==2){
    
    $sub=$this->users->where(['id'=>$request['sub_id']])->get(['name','business_email','email'])->first();

    $mailData['body_message']='You are assigned a new delivery, having PO Number:'.$request['po_number'].'.please click approve to be added to the delivery schedule. if you have any questions please feel free to contact us at 718-218-5239';
    $mailData['subject']='New Delivery Assigned';
    $mailData['body_message'] .='<table width="100%" border="1">
    <tr><td>Delivery Cost  :</td><td>$'.$request['quoted_price_for_sub'].'</td></tr>';
    $mailData['body_message'] .='<tr><td colspan="2">'. quote_data_for_mail($this->quotes->id, config('constants.groups.sub')).'</td></tr>';
    // if(isset($request['description']) && $request['description']!='')
    // $mailData['body_message'] .='<tr><td>Additional Notes :</td><td>'.$request['description'].'</td></tr>';
    $mailData['body_message'] .='</table>';
    $mailData['subject']='Your New Delivery From Oodler Express';
 
    $mailData['button_title']='APPROVE';
    $mailData['button_link']=route('sub_action',['quote_id' => $this->quotes->id,'action'=>base64_encode('accept')]);
    $mailData['button_title2']='Reject';
    $mailData['button_link2']=route('sub_action',['quote_id' => $this->quotes->id,'action'=>base64_encode('reject')]);

    $emailAdd=[
                $sub->email,
            ];
    if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
        $dataArray['emailMsg']='Email Sent Successfully';
    }

    }elseif(isset($request['assign_to']) && $request['assign_to']==1){
        $driverData=$this->users->where(['id'=>$request['driver_id']])->get(['name','business_email','email'])->first();

        $assign_to='Driver';
        $mailData['body_message']='You are assigned a new delivery, having PO Number:'.$request['po_number'].'. Please login to the CRM and look for details';
        $mailData['subject']='New Delivery Assigned';
        $emailAdd=[
                    //config('constants.admin_email'),
                    $driverData->email,
                ];
                if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
                    $dataArray['emailMsg']='Email Sent Successfully';
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
    public function request_quotes_form($customer_id=NULL){
       $user=Auth::user(); 

       $customer_products=array();

        if($customer_id=='' && $user->group_id!=config('constants.groups.admin'))
        $customer_id=get_session_value('id');

       $customer_products=$this->product_categories->with('products')
       ->where('is_active', '=', 1)
       ->get()->toArray();
      // p($customer_id); die;
       if(isset($customer_id) && $customer_id>0){
        $customer_cat=$this->users->where('id',$customer_id)->get('shipping_cat')->toArray();
        $customer_cat=$customer_cat[0];
        $customer_cat=json_decode($customer_cat['shipping_cat'],true);
        //p($customer_cat); die;
        $customer_products=$this->product_categories->with('products')->wherein('id',$customer_cat)
        ->where('is_active', '=', 1)
        ->get()->toArray();
        
        
       }
  
        return view('adminpanel.request_quote',get_defined_vars());
    }
    public function save_quote_data(Request $request){
       
        $validator=$request->validate([
            'quote_type'=>'required',
            'business_type'=>'required',
            'po_number'=>'required',
            'pickup_street_address1'=>'required',
            //'pickup_contact_number1'=>'required',
            //'pickup_email1'=>'required',
            'pickup_date1'=>'required',
            'drop_off_street_address'=>'required',
            //'drop_off_contact_number'=>'required',
            //'drop_off_email'=>'required',
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
        $this->quotes->pickup_email=$request['pickup_email1'];
        $this->quotes->pickup_date=$request['pickup_date1'];
        $this->quotes->drop_off_street_address=$request['drop_off_street_address'];
        $this->quotes->drop_off_unit=$request['drop_off_unit'];
        $this->quotes->drop_off_state=$request['drop_off_state'];
        $this->quotes->drop_off_city=$request['drop_off_city'];
        $this->quotes->drop_off_zipcode=$request['drop_off_zipcode'];
        $this->quotes->drop_off_contact_number=$request['drop_off_contact_number'];
        $this->quotes->drop_off_email=$request['drop_off_email'];
        $this->quotes->drop_off_instructions=$request['drop_off_instructions'];
        $this->quotes->drop_off_date=$request['drop_off_date'];
        

        $this->quotes->customer_id=get_session_value('id');
        if(isset($request['customer_id']) && $request['customer_id']>0)
        $this->quotes->customer_id=$request['customer_id'];
        
        

        $this->quotes->created_at=time();

       $this->quotes->save();
       

        if(isset($request['product_details1']))
       foreach($request['product_details1'] as $key=>$productData){
            
        if(isset($productData['product_name']) && count($productData['product_name'])>0){

            DB::table('pickup_dropoff_address')->insert([
                ['pickup_street_address' => $request['pickup_street_address1'],
                 'pickup_state' =>$request['pickup_state1'],
                 'pickup_city' => $request['pickup_city1'],
                 'pickup_zipcode' => $request['pickup_zipcode1'],
                 'pickup_contact_number' => $request['pickup_contact_number1'],
                 'pickup_email' => $request['pickup_email1'],
                 'pickup_date' => $request['pickup_date1'],
                 'drop_off_street_address' => $request['drop_off_street_address'],
                 'drop_off_unit' => $request['drop_off_unit'],
                 'drop_off_state' => $request['drop_off_state'],
                 'drop_off_city' => $request['drop_off_city'],
                 'drop_off_zipcode' => $request['drop_off_zipcode'],
                 'drop_off_contact_number' => $request['drop_off_contact_number'],
                 'drop_off_email' => $request['drop_off_email'],
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
                 'pickup_email' => $request['pickup_email2'],
                 'pickup_date' => $request['pickup_date2'],
                 'drop_off_street_address' => $request['drop_off_street_address'],
                 'drop_off_unit' => $request['drop_off_unit'],
                 'drop_off_state' => $request['drop_off_state'],
                 'drop_off_city' => $request['drop_off_city'],
                 'drop_off_zipcode' => $request['drop_off_zipcode'],
                 'drop_off_email' => $request['drop_off_email'],
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
                 'pickup_email' => $request['pickup_email3'],
                 'pickup_date' => $request['pickup_date3'],
                 'drop_off_street_address' => $request['drop_off_street_address'],
                 'drop_off_unit' => $request['drop_off_unit'],
                 'drop_off_state' => $request['drop_off_state'],
                 'drop_off_city' => $request['drop_off_city'],
                 'drop_off_zipcode' => $request['drop_off_zipcode'],
                 'drop_off_email' => $request['drop_off_email'],
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
       ->with(['quote_products','delivery_notes','document_for_request_quote','quote_prices','customer','comments'])
       ->where('id', $id)
       ->orderBy('created_at', 'desc')->get()->toArray();
       $quotesData=$quotesData[0];
       //p($quotesData);die;
        return view('adminpanel.send_quote',get_defined_vars());
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

        $deliveryCost=$request['quoted_price'];
        if(isset($request['extra_charges']) && $request['extra_charges']!='')
        $deliveryCost=$deliveryCost+$request['extra_charges'];

        $mailData['body_message']='You have received a quote from Oodler express for <strong>PO Number :'.$quoteData['po_number'].'</strong>, please click approve to be added to the delivery schedule.';
        $mailData['body_message'] .='<table width="100%" border="1">
        <tr><td>Delivery Cost  :</td><td>'.$deliveryCost.'</td></tr>';
        
        if(isset($request['reason_for_extra_charges']) && $request['reason_for_extra_charges']!='')
        $mailData['body_message'] .='<tr><td>Reason for Extra Charges :</td><td>'.$request['reason_for_extra_charges'].'</td></tr>';
        // if(isset($request['description']) && $request['description']!='')
        // $mailData['body_message'] .='<tr><td>Additional Notes :</td><td>'.$request['description'].'</td></tr>';
        $mailData['body_message'] .='</table>';
        $mailData['subject']='Your New Quote From Oodler Express';
     
        $mailData['button_title']='APPROVE';
        $mailData['button_link']=route('customer_action',['quote_id' => $id,'action'=>base64_encode('accept')]);
        $mailData['button_title2']='Reject';
        $mailData['button_link2']=route('customer_action',['quote_id' => $id,'action'=>base64_encode('reject')]);
        

        //$mailData['body_message']='you have received a new request for a quote from customer <br>'.quote_data_for_mail($this->quotes->id);;
       
         

         $emailAdd=[
                   // config('constants.admin_email'),
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


    public function upload_quote_request_new(Request $request){
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

            // Save New Quote
         
        

        $this->quotes->customer_id=get_session_value('id');
        if(isset($request['customer_id']) && $request['customer_id']>0)
        $this->quotes->customer_id=$request['customer_id'];
        $this->quotes->po_number='PO-'.time();
        $this->quotes->created_at=time();

        $this->quotes->save(); 
            // End New Quote
        
        //return response()->json(['success'=>$imageName]);

            $this->files->name=$orginalImageName;
            //$this->files->slug=phpslug($imageName);
            $this->files->slug='quote_request';
            $this->files->path=url('uploads').'/'.$imageName;
            //$this->files->description=$orginalImageName.' file uploaded';
            $this->files->description=phpslug($imageName);
            $this->files->otherinfo=$imageExt;
            $this->files->user_id=get_session_value('id');
            $this->files->quote_id=$this->quotes->id;
            $this->files->save();
        //             ->update($data);
        // $this->files->where('id', $id)
        //             ->update($data);

        $this->quotes->where('id', $this->quotes->id)->update(array('request_file_id'=>$this->files->id));

                    // Activity Log
                    $activityComment='Mr.'.get_session_value('name').' uploaded documents for quote request';
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$id,
                        'action_slug'=>'quote_request_document',
                        'comments'=>$activityComment,
                        'others'=>'files',
                        'created_at'=>date('Y-m-d H:I:s',time()),
                    );
                    $activityID=log_activity($activityData);

        return response()->json(['success'=>$imageName]);

        
     }
     public function upload_quote_request(Request $request){
       
            $user=Auth::user();

        //     $this->quotes->customer_id=get_session_value('id');
        // if(isset($request['customer_id']) && $request['customer_id']>0)
        // $this->quotes->customer_id=$request['customer_id'];
        // $this->quotes->po_number='PO-'.time();
        // $this->quotes->created_at=time();

        // $this->quotes->save();
        // die;
            $image = $request->file('file');
            $imageExt=$image->extension();
            $imageName = time().'.'.$imageExt;

     
        //$uploadingPath=public_path('uploads');
        $uploadingPath=base_path().'/public/uploads';
        if(base_path()!='/Users/waximarshad/office.oodlerexpress.com')
        $uploadingPath=base_path().'/public_html/uploads';

     

            $image->move(($uploadingPath),$imageName);
            $orginalImageName=$image->getClientOriginalName();
        
            $this->files->name=$orginalImageName;
            $this->files->slug='quote_request_file';
            $this->files->path=url('uploads').'/'.$imageName;
            $this->files->description=phpslug($imageName);
            $this->files->otherinfo=$imageExt;
            $this->files->user_id=get_session_value('id');
            $quote_id=99999;
            if(isset($request['quote_id']) && $request['quote_id']>0)
            $quote_id=$request['quote_id'];
            
            $this->files->quote_id=$quote_id;
            $this->files->save();
        
            if(isset($request['quote_id']) && $request['quote_id']==0){
    
                $this->quotes->customer_id=get_session_value('id');
                if(isset($request['customer_id']) && $request['customer_id']>0)
                $this->quotes->customer_id=$request['customer_id'];
                $this->quotes->request_file_id=$this->files->id;
                $this->quotes->created_at=time();
                $this->quotes->save(); 
                $this->files->where('id', $this->files->id)->update(array('quote_id'=>$this->quotes->id));
                $quote_id=$this->quotes->id;
            }
            
            // Activity Log
            $activityComment='Mr.'.get_session_value('name').' uploaded documents for quote request';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$quote_id,
                'action_slug'=>'quote_request_document',
                'comments'=>$activityComment,
                'others'=>'quotes',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);

        return response()->json(['success'=>$imageName,'quote_id'=>$quote_id]);


        
     }
    public function upload_delivery_proof($id,Request $request){
        $user=Auth::user();
            $image = $request->file('file');
            $imageExt=$image->extension();
            $imageName = time().rand(100,10000).'.'.$imageExt;

     
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
    public function uploade_documents_for_driver($id,Request $request){
        $user=Auth::user();
            $image = $request->file('file');
            $imageExt=$image->extension();
            $imageName = time().'.'.$imageExt;

        $uploadingPath=base_path().'/public/uploads';
        if(base_path()!='/Users/waximarshad/office.oodlerexpress.com')
        $uploadingPath=base_path().'/public_html/uploads';


            $image->move(($uploadingPath),$imageName);
            $orginalImageName=$image->getClientOriginalName();
        
        

            $this->files->name=$orginalImageName;
            //$this->files->slug=phpslug($imageName);
            //$this->files->slug='document_for_driver';
            $this->files->slug=$request->documents_for;
            $this->files->path=url('uploads').'/'.$imageName;
            //$this->files->description=$orginalImageName.' file uploaded';
            $this->files->description=phpslug($imageName);
            $this->files->otherinfo=$imageExt;
            $this->files->user_id=get_session_value('id');
            $this->files->quote_id=$id;
            $this->files->save();
                    // Activity Log
                    $activityComment='Mr.'.get_session_value('name').' uploaded documents for driver';
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$id,
                        'action_slug'=>'delivery_document_for_driver',
                        'comments'=>$activityComment,
                        'others'=>'files',
                        'created_at'=>date('Y-m-d H:I:s',time()),
                    );
                    $activityID=log_activity($activityData);

        return response()->json(['success'=>$imageName]);

        
     }
    
    public function sub_action($quote_id,$action){
        
        $action=base64_decode($action);
        
        //$quote_data_to_update['status']=config('constants.quote_status.declined');
        $quote_data_to_update['sub_status']=2;
        $quote_data_to_update['assign_to']=1;

        $activityComment='Sub Declined the Delivery';
        $msg= 'we have canceled this delivery for you, let us know if you have any other query. Thank you';
        $actionMsg='declined';
        $mailData['subject']='New Delivery Declined ';

        if($action=='accept'){
            $quote_data_to_update['status']=config('constants.quote_status.delivery');
            $quote_data_to_update['sub_status']=1;
            
            $activityComment='Sub approved the delivery';
            $actionMsg='approved';
            $msg="Thank you, you have been confirmed";
            $mailData['subject']='New Delivery Accepted by the Sub ';  
            
        }else{
            $this->quote_prices->where(['quote_id'=>$quote_id,'quote_price_for'=>2])->delete();
            $quote_data_to_update['sub_id']=NULL;
            $quote_data_to_update['quoted_price_for_sub']=NULL;
            $quote_data_to_update['assign_to']=1;
            $quote_data_to_update['sub_status']=0;
            
        }
        

        $quoteData=$this->quotes->with(['customer','sub'])
            ->where('id',$quote_id)
            ->get()
            ->toArray();
            $quoteData=$quoteData[0];
        
            $where_clause=[
                ['id','=',$quote_id],
                ['sub_id','>', 0],
                //['status','<', config('constants.quote_status.delivery')],
            ];
            
            $updated=$this->quotes->where($where_clause)->update($quote_data_to_update);
          
            $activityData=array(
                'user_id'=>$quoteData['sub']['id'],
                'action_taken_on_id'=>$quote_id,
                'action_slug'=>'sub_status_changed_by_sub',
                'comments'=>$activityComment,
                'others'=>'quotes',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);  
            if($updated){
                
               
                $mailData['body_message']='This email is to let you know that delivery having PO No.:'.$quoteData['po_number'].' has been '.$actionMsg.' by the sub on '.date('d/m/Y');   
                $toEmail=[
                    config('constants.admin_email'),
                    //$quoteData['customer']['email']
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
        public function add_quickbooks_item($itemdata=[]){
            $config = config('quickbooks');
        
            $tokenData=$this->updated_access_token();
            $config['access_token']=$tokenData['access_token'];
            $config['refresh_token']=$tokenData['refresh_token'];
        
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
            // Create the product or service item
            $product = Item::create([
                "Name" =>$itemdata['name'],// "PO-NUmber",
                "Description" => $itemdata['description'],//"An example product for demonstration purposes",
                "UnitPrice" => $itemdata['unit_price'],//9.99,
                "Type" => "Service",
                "IncomeAccountRef" => [
                    "value" => "91"
                ]
            ]);
    
            // Add the item to QuickBooks
            $resultingItemObj = $dataService->Add($product);
    
            $error = $dataService->getLastError();
                        if ($error) {
                            return false;
                            p($error);
                            echo "The Status code is: " . $error->getHttpStatusCode() . "<br>";
                            echo "The Helper message is: " . $error->getOAuthHelperError() . "<br>";
                            echo "The Response message is: " . $error->getResponseBody() . "<br>";
                        }
                        else {
                            return $resultingItemObj->Id;
                            echo "Created Service Id={$resultingItemObj->Id}. Reconstructed response body:<br>";
                            $xmlBody = XmlObjectSerializer::getPostXmlFromArbitraryEntity($resultingItemObj, $urlResource);
                            echo $xmlBody . "<br>";
                        }
    
        } 
        // Update QUICKBOOK ACCESS TOKEN
    public function updated_access_token(){

        $config = config('quickbooks');
        
        $qb_credentials=$this->quickbook_credentials->where('id',1)->get()->first();

        $retData=$to_update_data=[];

        if($qb_credentials->status==1){
            $to_update_data['client_id']=$config['client_id']=$qb_credentials->client_id;
            $to_update_data['client_secret']=$config['client_secret']=$qb_credentials->client_secret;
            $to_update_data['redirect_uri']=$config['redirect_uri']=$qb_credentials->redirect_uri;
            $to_update_data['access_token']=$config['access_token']=$qb_credentials->access_token;
            $to_update_data['refresh_token']=$config['refresh_token']=$qb_credentials->refresh_token;
            $to_update_data['realm_id']=$config['realm_id']=$qb_credentials->realm_id;
            $to_update_data['base_url']=$config['base_url']=$qb_credentials->base_url;
        }
        
        $dataService = DataService::Configure([
            'auth_mode' => 'oauth2',
            'ClientID' => $config['client_id'],
            'ClientSecret' => $config['client_secret'],
            'RedirectURI' => $config['redirect_uri'],
            'accessTokenKey' => $config['access_token'],
            'refreshTokenKey' => $config['refresh_token'],
            'QBORealmID' => $config['realm_id'],
            'baseUrl' => $config['base_url'],
            'token_refresh_interval_before_expiry' => $config['token_refresh_interval_before_expiry']
        ]);
                   
            $OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
            $accessTokenObj = $OAuth2LoginHelper->
            refreshAccessTokenWithRefreshToken($config['refresh_token']);
            $accessTokenValue = $accessTokenObj->getAccessToken();
            $refreshTokenValue = $accessTokenObj->getRefreshToken();

            $to_update_data['client_id']=$qb_credentials->client_id;
            $to_update_data['client_secret']=$qb_credentials->client_secret;
            $to_update_data['redirect_uri']=$qb_credentials->redirect_uri;
            $to_update_data['access_token']=$accessTokenValue;
            $to_update_data['refresh_token']=$refreshTokenValue;
            $to_update_data['realm_id']=$qb_credentials->realm_id;
            $to_update_data['base_url']=$qb_credentials->base_url;
            $to_update_data['updating_time']=time();
            $to_update_data['status']=1;
            $this->quickbook_credentials->where('id',1)->update($to_update_data);

         $retData['access_token']=$accessTokenValue;
         $retData['refresh_token']=$refreshTokenValue;

         return $retData;

    } 
        public function add_quickbooks_sales($invoice_data=[]){
            // Connect to QuickBooks
            $config = config('quickbooks');

            $tokenData=$this->updated_access_token();
            $config['access_token']=$tokenData['access_token'];
            $config['refresh_token']=$tokenData['refresh_token'];
            
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
        
                       
                $invoice = Invoice::create([
                    "Line" => [
                        [
                            "Amount" => $invoice_data['amount'],//100.00,
                            "Description" => 'Delivery PO#:'.$invoice_data['po_number'],
                            "DetailType" => "SalesItemLineDetail",
                            "SalesItemLineDetail" => [
                                "ItemRef" => [
                                    "value" => 11,//$invoice_data['qb_service_id'],//1
                                    "name" =>"Services",// $invoice_data['po_number']//"Services"
                                ],
                                "UnitPrice" => $invoice_data['amount'],
                                "Qty" => 1
                            ]
                        ]
                    ],
                    "CustomerRef" => [
                        "value" => $invoice_data['qb_customer_id']
                    ],
                    "PrivateNote" => 'Delivery PO#:'.$invoice_data['po_number'],
                    "TxnDate" => date('Y-m-d')//"2023-02-06"
                ]);
                $invoice->DocNumber =generateInvoiceNumber() ;
                //$invoice->DueDate ="2023-02-27";

                $retData=[];
                $retData['error']='NO';

                try{
                    $resultingInvoiceObj = $dataService->Add($invoice);
                    $retData['qb_invoice_id']=$resultingInvoiceObj->Id;
                    $retData['qb_invoice_no']=$resultingInvoiceObj->DocNumber;
                    $retData['message']='QUICKBOOK: Added Successfully !';
                }
                catch (ServiceException $ex) {
                    $retData['qb_invoice_id']=0;
                    $retData['qb_invoice_no']=0;
                    $retData['error']='YES';
                    
                    $retData['message']='QUICKBOOK:'.$ex->getMessage();
                    //$error = $dataService->getLastError();
                   
                }
                
                return $retData;

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
        

        $quoteData=$this->quotes->with(['customer','quote_agreed_cost'])
            ->where('id',$quote_id)
            ->get()
            ->toArray();
            $quoteData=$quoteData[0];
        
            
            $updated=$this->quotes->where('id',$quote_id)->where('status',config('constants.quote_status.quote_submitted'))->update(array('status'=>$status));
            
            // This section is to add product in quickBook
            $quote_price=(isset($quoteData['quote_agreed_cost']['quoted_price']) && $quoteData['quote_agreed_cost']['quoted_price']>0)?$quoteData['quote_agreed_cost']['quoted_price']:0;
            $extra_charge=(isset($quoteData['quote_agreed_cost']['extra_charges']) && $quoteData['quote_agreed_cost']['extra_charges']>0)?$quoteData['quote_agreed_cost']['extra_charges']:0;
            $agreed_amount=$quote_price+$extra_charge;

            $item_data['name']=$quoteData['po_number'];
            $item_data['description']='Customer: '.$quoteData['customer']['name'].', PICK-UP:'.$quoteData['pickup_street_address'].', DROP-OFF:'.$quoteData['drop_off_street_address'];
            $item_data['unit_price']=$agreed_amount;
            
            // $qb_service_id=$this->add_quickbooks_item($item_data);
            // if($qb_service_id>0){
            //     $qb_service_added=$this->quotes->where('id',$quote_id)->update(['qb_service_id'=>$qb_service_id]);
            // }
            // else{
            //     echo '<br><h4>There is some error to add this quote in quickbook service!</h4><br>';
            // }

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
                    //$quoteData['customer']['email']
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
                //p($where_clause); die; 
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

        public function sendSMS($receiverNumber = "+923007731712",$message = "This is the test messagee"){
             try {
     
                 $account_sid = getenv("TWILIO_SID");
                 $auth_token = getenv("TWILIO_TOKEN");
                 $twilio_number = getenv("TWILIO_FROM");
     
                 $client = new Client($account_sid, $auth_token);
                 $client->messages->create($receiverNumber, [
                     'from' => $twilio_number, 
                     'body' => $message]);
     
                 return true;
     
             } catch (Exception $e) {
                 //dd("Error: ". $e->getMessage());
                
             }
             return false;
             // END
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
        
        if(isset($req['action']) && $req['action']=='qsearch_delivery')
        {
            $user=Auth::user();
            $dataArray['title']='Search Result';
            $where_clause=[
                ['status', '=', config('constants.quote_status.delivery')],
                ['is_active', '=', 1],
            ];
           
            if($user->group_id==config('constants.groups.customer')){
                $where_clause[]=['customer_id', '=', get_session_value('id')];
                }
            else if($user->group_id==config('constants.groups.driver')){
                $where_clause[]=['driver_id', '=', get_session_value('id')];
                }
            
           
                $quotes=$this->quotes
                ->with('quote_products')
                ->with('customer')
                ->with('driver')
                ->where($where_clause)
                ->where('po_number', 'like', '%' . $req->qsearch . '%')
                //->orwhere('phone', 'like', '%' . $req->qsearch . '%')
                ->orderBy('created_at', 'desc');
    
                $quotesData=$quotes->get()->toArray();
                //$response='<table id="example1" class="table table-bordered table-striped">
                $response= ' <thead>
                                        <tr>
                                            <th>Quote Type</th>
                                            <th>Business Type</th>
                                            <th>PO Number</th>
                                            <th>Pick-up Street Address</th>
                                            <th>Pick-up Phone</th>
                                            <th>Drop-off Street Address</th>
                                            <th>Drop-off Phone</th>
                                            <th>Customer</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                                        
                                            $counter = 1;
                                            
                                            foreach ($quotesData as $data){
                                            
                                                $response .='<tr id="row_'.$data['id'].'">
                                            <td><strong id="quote_type_'.$data['id'].'">'.$data['quote_type'].'</strong>
                                            </td>
                                            <td id="business_type_'.$data['id'].'">'.$data['business_type'].'</td>
                                            <td id="po_number_'.$data['id'].'">
                                                '.$data['po_number'].'</td>
                                            <td id="pickup_street_address_'.$data['id'].'">
                                                '.$data['pickup_street_address'].'</td>
                                            <td id="pickup_contact_number_'.$data['id'].'">'.$data['pickup_contact_number'].'</td>
                                            <td id="drop_off_street_address_'.$data['id'].'">'.$data['drop_off_street_address'].'</td>
                                            <td id="drop_off_contact_number_'.$data['id'].'">'.$data['drop_off_contact_number'].'</td>
                                            <td id="drop_off_contact_number_'.$data['id'].'">'.$data['customer']['name'].'</td>
                                            <td id="status'.$data['id'].'">';
                                                if ($data['status']==config('constants.quote_status.pedning'))
                                                $response .='<span class="btn btn-info btn-block btn-sm"><i class="fas fa-chart-line"></i>Pending</span>';
                                               elseif($data['status']==config('constants.quote_status.quote_submitted'))
                                               $response .='<span  class="btn btn-success btn-block btn-sm"><i class="fas fa-chart-line"></i>New</span>';
                                               elseif($data['status']==config('constants.quote_status.declined'))
                                               $response .='<span  class="btn btn-warning btn-block btn-sm"><i class="fas fa-chart-line"></i>Cancelled</span>';
                                               elseif($data['status']==config('constants.quote_status.approved'))
                                               $response .='<span  class="btn btn-success btn-block btn-sm"><i class="fas fa-chart-line"></i>Approved</span>';
                                               elseif($data['status']==config('constants.quote_status.delivery'))
                                               $response .='<span  class="btn btn-success btn-block btn-sm"><i class="fas fa-chart-line"></i>Deliverable</span>';
                                               
                                                
                                               $response .=' </td>';
                                               $response .='<td>
                                               
                                                <a href="'.route('deliveries.view',$data['id']).'"
                                                class="btn btn-info btn-block btn-sm"><i class="fas fa-eye"></i>
                                                View</a>';
                                                if ($user->group_id==config('constants.groups.admin')){
                                                
                                                if ($data['is_active']==1){
                                                    $response .='<button
                                                    onClick="do_action('.$data['id'].',\'delete\','.$counter.')"
                                                    type="button" class="btn btn-danger btn-block btn-sm"><i
                                                        class="fas fa-trash"></i>
                                                    Delete</button>';
                                                }elseif ($data['is_active']==2) {
                                                    $response .='<button
                                                    onClick="do_action('.$data['id'].',\'restore\','.$counter.')"
                                                    type="button" class="btn btn-primary btn-block btn-sm"><i
                                                        class="fas fa-trash"></i>
                                                    Restore</button>';
                                                }
                                                
                                                }
                                                
                                                $response .='</td>

                                            </td>

                                        </tr>';
                                        
                                                $counter ++;
                                        }
                                        
                                        $response .='</tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Quote Type</th>
                                            <th>Business Type</th>
                                            <th>PO Number</th>
                                            <th>Pick-up Street Address</th>
                                            <th>Pick-up Phone</th>
                                            <th>Drop-off Street Address</th>
                                            <th>Drop-off Phone</th>
                                            <th>Customer</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        
                                    </tfoot>
                                ';
                                $dataArray['response']=  $response;
                

        }
        if(isset($req['action']) && $req['action']=='qsearch_quote')
        {
            $user=Auth::user();
            $dataArray['title']='Search Result';
            
            $type=$req->quote_type;
            if($type=='trash'){
                $where_clause=[
                    ['is_active', '=', 1],
                ];
            }
            elseif($type=='requested'){
                $where_clause=[
                    ['status', '=', config('constants.quote_status.pending')],
                    ['is_active', '=', 1],
                ];
            }
            elseif($type=='cancelled'){
                $where_clause=[
                    ['status', '=', config('constants.quote_status.declined')],
                    ['is_active', '=', 1],
                ];
          
          
            }
            elseif($type=='new'){
                $where_clause=[
                    ['status', '=', config('constants.quote_status.quote_submitted')],
                    ['is_active', '=', 1],
                ];
          
            }
            elseif($type=='approved'){
                $where_clause=[
                    ['status', '=', config('constants.quote_status.approved')],
                    ['is_active', '=', 1],
                ];
          
            }
            else{
                $where_clause=[
                    ['status', '=', config('constants.quote_status.pending')],
                    ['is_active', '=', 1],
                ];
                
            }
            // If user is customer or Admin
            
            if($user->group_id==config('constants.groups.customer')){
            $where_clause[]=['customer_id', '=', get_session_value('id')];
            }
           
                $query=$this->quotes
                ->with(['quote_products','document_for_request_quote','customer','driver'])
                ->where($where_clause)
                ->orderBy('created_at', 'desc');
                
                $search_val=$req->qsearch;
                
                $quotes=$query->where(function($query) use ($search_val){
                    $query->orwhere('po_number', 'like', '%' . $search_val . '%');

                });
                
                
    
                $quotesData=$quotes->get()->toArray();
                //$response='<table id="example1" class="table table-bordered table-striped">
                $response= ' <thead>
                                        <tr>
                                            <th>Quote Type</th>
                                            <th>Business Name</th>
                                            <th>PO Number</th>
                                            <th>Pick-up Street Address</th>
                                            <th>Pick-up Phone</th>
                                            <th>Drop-off Street Address</th>
                                            <th>Drop-off Phone</th>
                                            <th>Move to</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                                        
                                            $counter = 1;
                                            
                                            foreach ($quotesData as $data){

                                                $view_document='';
                                                    //p($data['document_for_request_quote']); die;
                                                    if(count($data['document_for_request_quote'])>0){
                                                        $view_document='<a href="'.route('quote_requested_documents',$data['id']).'"
                                                    class="btn btn-secondary btn-block btn-sm"><i class="fas fa-eye"></i> Docs</a>';
                                                    }
                                            
                                                $response .='<tr id="row_'.$data['id'].'">
                                            <td><strong id="quote_type_'.$data['id'].'">'.$data['quote_type'].'</strong>
                                            </td>
                                            <td id="business_type_'.$data['id'].'">'.$data['customer']['business_name'].'</td>
                                            <td id="po_number_'.$data['id'].'">
                                                '.$data['po_number'].'</td>
                                            <td id="pickup_street_address_'.$data['id'].'">
                                                '.$data['pickup_street_address'].'</td>
                                            <td id="pickup_contact_number_'.$data['id'].'">'.$data['pickup_contact_number'].'</td>
                                            <td id="drop_off_street_address_'.$data['id'].'">'.$data['drop_off_street_address'].'</td>
                                            <td id="drop_off_contact_number_'.$data['id'].'">'.$data['drop_off_contact_number'].'</td>
                                            <td id="status'.$data['id'].'">';
                                           
                                            $response .=' <select id="current_status_'.$data['id'].'" onchange="do_change('.$data['id'].',\'change_status\','.$counter.')" name="status" class="form-control select2bs4">
                                            '.get_quote_status_options($data['status']).'</select></td>';
                                                
                                             
                                               $response .='<td>';
                                               if($user->group_id==config('constants.groups.admin')){
                                                
                                            
                                               if ($data['status'] == config('constants.quote_status.approved')){
                                                $response .=' <a href="'.route('quotes.add_to_delivery_form', $data['id']).'"
                                                class="btn btn-success btn-block btn-sm"><i class="fas fa-plus"></i>
                                                Add to Delivery</a>';
                                               }
                                               
                                           
                                               $response .=' <a href="'.route('quotes.send_quote_form', $data['id']).'"
                                               class="btn btn-primary btn-block btn-sm"><i class="fas fa-upload"></i> Send </a>'.$view_document.'
                                                <a href="'.route('quotes.quoteeditform', $data['id']).'"
                                               class="btn btn-info btn-block btn-sm"><i class="fas fa-edit"></i>
                                               Edit</a>';

                                               if ($data['is_active'] == 1){
                                                $response .='<button
                                                   onClick="do_action('.$data['id'].',\'delete\','.$counter.')"
                                                   type="button" class="btn btn-danger btn-block btn-sm"><i
                                                       class="fas fa-trash"></i>
                                                   Delete</button>';
                                               }elseif ($data['is_active'] == 2){
                                                $response .='<button
                                                   onClick="do_action('.$data['id'].',\'restore\','.$counter.')"
                                                   type="button" class="btn btn-primary btn-block btn-sm"><i
                                                       class="fas fa-trash"></i>
                                                   Restore</button>';
                                               }
                                            }else{
                                                $response .=' <a href="'.route('quotes.view', $data['id']).'"
                                                class="btn btn-primary btn-block btn-sm"><i class="fas fa-eye"></i>
                                                View</a>'; 
                                            }
                                                $response .='</td>

                                            </td>

                                        </tr>';
                                        
                                                $counter ++;
                                        }
                                        
                                        $response .='</tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Quote Type</th>
                                            <th>Business Name</th>
                                            <th>PO Number</th>
                                            <th>Pick-up Street Address</th>
                                            <th>Pick-up Phone</th>
                                            <th>Drop-off Street Address</th>
                                            <th>Drop-off Phone</th>
                                            <th>Move to</th>
                                            <th>Action</th>
                                        </tr>
                                        
                                    </tfoot>
                                ';
                                $dataArray['response']=  $response;
                

        }
        elseif(isset($req['action']) && $req['action']=='dirver_activity_for_sms')
        {
            $dataArray['title']='Driver Activity';
            $dataArray['error']='No';
            $current_time=time();

            $quoteData=$this->quotes->where('id',$id)->with(array('customer','driver'))->get()->toArray();
            $quoteData=$quoteData[0];
            $receiverNumber = "+923007731712";

            if(isset($req['activity']) && $req['activity']=='arrived_at_pickup'){

                $result=$this->quotes->where('id','=',$id)->update(array('arrived_at_pickup'=>$current_time)); 
                $message='Mr.'.$quoteData['driver']['name'].' arrived at pick-up address '.$quoteData['pickup_street_address'].' to pick-up the delivery having PO Number: '.$quoteData['po_number'].' at '.date(config('constants.date_and_time'),$current_time);   
               
                if(!$this->sendSMS($receiverNumber,$message))
                $dataArray['title']='There is some issue in sending SMS';
                
            }elseif(isset($req['activity']) && $req['activity']=='arriving_at_dropoff'){

                $result=$this->quotes->where('id','=',$id)->update(array('arriving_at_dropoff'=>$current_time)); 
                $message='Mr.'.$quoteData['driver']['name'].' on the way to address '.$quoteData['drop_off_street_address'].' to drop-off the delivery having PO Number: '.$quoteData['po_number'].' and will reach there at estimated_time: '.date(config('constants.date_and_time'),$current_time);  
                
                if(!$this->sendSMS($receiverNumber,$message))
                $dataArray['title']='There is some issue in sending SMS';

            }

            if($dataArray['error']=='No'){
                $dataArray['msg']='Mr.'.get_session_value('name').', Performed activity for delivery';
                  // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'driver_delivery_activity_sms',
                'comments'=>'Mr.'.get_session_value('name').' performed activity for delivery',
                'others'=>'quotes',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            }


        }
        elseif(isset($req['action']) && $req['action']=='dirver_activity_for_time')
        {
            $dataArray['title']='Driver Activity';
            $dataArray['error']='No';
            $selected_date=$req['date_of_booking'];
            $selected_time=$req['time_of_booking'];

            $date_time= $selected_date.' '.$selected_time;
            $current_time=$str_time_date=strtotime($date_time); 
               // echo date(config('constants.date_and_time'),$str_time_date); exit;

            $quoteData=$this->quotes->where('id',$id)->with(array('customer','driver'))->get()->toArray();
            $quoteData=$quoteData[0];
            $receiverNumber = "+923007731712";

            if(isset($req['key']) && $req['key']=='reached_at_pickup'){// For now this section is not working
                $to_update_data['reached_at_pickup']=$current_time;
                $to_update_data['arrived_at_pickup']=$selected_time;
                

                $result=$this->quotes->where('id','=',$id)->update($to_update_data); 
                $pick_up_contact_message='Hello
                 This message is to confirm that the Oodler Express driver will be arrived at pick up point on '.$selected_time ;   
                 
                 if(isset($quoteData['pickup_contact_number']) && !empty($quoteData['pickup_contact_number']))
                 $receiverNumber=$quoteData['pickup_contact_number'];

                if(!$this->sendSMS($receiverNumber,$pick_up_contact_message))
                $dataArray['title']='There is some issue in sending SMS to Pick Up Contact';

                $drop_off_contact_message='Hello,
                 this message is to inform you that the Oodler Express driver has arrived at the pickup address for PO Number: '.$quoteData['po_number'].' the driver will safely load the shipment. please lookout for another text when the driver is on it\'s way to the delivery.
                 if you have any questions, contact us at 845-325-4892';

                if(isset($quoteData['drop_off_contact_number']) && !empty($quoteData['drop_off_contact_number']))
                $receiverNumber=$quoteData['drop_off_contact_number'];

                if(!$this->sendSMS($receiverNumber,$drop_off_contact_message))
                $dataArray['title']='There is some issue in sending SMS to Drop Off Contact';
                
            }elseif(isset($req['key']) && $req['key']=='on_the_way'){
                $to_update_data['on_the_way']=$current_time;
                $to_update_data['arriving_at_dropoff']=$selected_time;

                $result=$this->quotes->where('id','=',$id)->update($to_update_data); 
               
                $message='Hello, This message is to inform you that a delivery from '.$quoteData['customer']['business_name'].' just got picked up and is on the way to it\'s delivery location.
                 Carrier Name: Oodler Express
                the drivers estimated time of arrival is '.$selected_time.'.
                ** PLEASE MAKE SURE THAT OUR DRIVER HAS ACCESS TO THE DELIVERY SITE!
                For any questions you can contact us at 845-325-4892';
                 
                 if(isset($quoteData['drop_off_contact_number']) && !empty($quoteData['drop_off_contact_number']))
                 $receiverNumber=$quoteData['drop_off_contact_number'];
                 //$receiverNumber = "+9203007731712";
                if(!$this->sendSMS($receiverNumber,$message))
                $dataArray['title']='There is some issue in sending SMS to Drop Off Contact Number';
                 
                if(isset($quoteData['pickup_contact_number']) && !empty($quoteData['pickup_contact_number']))
                 $receiverNumber=$quoteData['pickup_contact_number'];
                 //$receiverNumber = "+9203007731712";
                if(!$this->sendSMS($receiverNumber,$message))
                $dataArray['title']='There is some issue in sending SMS to Pick up Contact Number';

            }

            if($dataArray['error']=='No'){
                $dataArray['msg']='Mr.'.get_session_value('name').', Performed activity for delivery';
                  // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'driver_delivery_time_updated',
                'comments'=>'Mr.'.get_session_value('name').' performed activity for delivery',
                'others'=>'quotes',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            }


        }
        elseif(isset($req['action']) && $req['action']=='dirver_activity')
        {

            $dataArray['title']='Driver Activity';
            
            $quoteData=$this->quotes->where('id',$id)->with(['customer','driver','quote_agreed_cost'])->get()->toArray();
            $quoteData=$quoteData[0];

            $current_time=time();
            $receiverNumber = "+923007731712";

            $emailAdd=[
                config('constants.admin_email'),
                $quoteData['customer']['email'],
            ];
            
            if(isset($quoteData['driver']['email']) && !empty($quoteData['driver']['email']))
            $emailAdd[]=$quoteData['driver']['email'];
      
        //     p($emailAdd);
        //    p( $quoteData); die;

            if(isset($req['activity']) && $req['activity']=='reached_at_pickup'){
                
                //$result=$this->quotes->where('id','=',$id)->update(array('reached_at_pickup'=>$current_time)); 
                // Sending SMS
                $time_of_arriving= date("h:i:sa");
                $to_update_data['reached_at_pickup']=$current_time;
                $to_update_data['arrived_at_pickup']=$time_of_arriving;
                $result=$this->quotes->where('id','=',$id)->update($to_update_data); 

                $drop_off_contact_message=$pick_up_contact_message='Hello, this message is to inform you that the Oodler Express driver has arrived at the pickup address for PO Number: '.$quoteData['po_number'].' the driver will safely load the shipment. please lookout for another text when the driver is on it\'s way to the delivery.
               if you have any questions, contact us at 845-325-4892';

                if(isset($quoteData['pickup_contact_number']) && !empty($quoteData['pickup_contact_number']))
                $receiverNumber=$quoteData['pickup_contact_number'];
                
                //$receiverNumber = "+923007731712";
               if(!$this->sendSMS($receiverNumber,$pick_up_contact_message))
               $dataArray['title']='There is some issue in sending SMS to Pick Up Contact Number';
           
              if(isset($quoteData['drop_off_contact_number']) && !empty($quoteData['drop_off_contact_number']))
              $receiverNumber=$quoteData['drop_off_contact_number'];
              
              //$receiverNumber = "+923007731712";
              if(!$this->sendSMS($receiverNumber,$drop_off_contact_message))
              $dataArray['title']='There is some issue in sending SMS to Drop Off Contact Number';

                //$mailData['subject']='Mr.'.$quoteData['driver']['name'].' reached at pick-up having PO#:'.$quoteData['po_number'];  
                //$mailData['body_message']='Mr.'.$quoteData['driver']['name'].' reached at pick-up address <strong>'.$quoteData['pickup_street_address'].'</strong> to pick-up the delivery having PO Number: '.$quoteData['po_number'].' at '.date(config('constants.date_and_time'),$current_time);  
            
                // if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
                //     $dataArray['emailMsg']='Email Sent Successfully';
                     
                //  }

                 
               
            }
            
            elseif(isset($req['activity']) && $req['activity']=='picked_up'){
                
                $result=$this->quotes->where('id','=',$id)->update(array('picked_up'=>$current_time)); 
                //$mailData['subject']='Mr.'.$quoteData['driver']['name'].' picked up the delivery having PO#:'.$quoteData['po_number'];  
                //$mailData['body_message']='Mr.'.$quoteData['driver']['name'].' picked up the delivery from the address <strong>'.$quoteData['pickup_street_address'].'</strong> having PO Number: '.$quoteData['po_number'].' at '.date(config('constants.date_and_time'),$current_time);  
            }
            
            elseif(isset($req['activity']) && $req['activity']=='on_the_way'){
                $result=$this->quotes->where('id','=',$id)->update(array('on_the_way'=>$current_time)); 
                
                //$mailData['subject']='Mr.'.$quoteData['driver']['name'].' on the way to drop-off the devlivery having PO#:'.$quoteData['po_number'];  
                //$mailData['body_message']='Mr.'.$quoteData['driver']['name'].' on the way to address <strong>'.$quoteData['drop_off_street_address'].'</strong> to drop-off the delivery having PO Number: '.$quoteData['po_number'].' at '.date(config('constants.date_and_time'),$current_time);  
                // if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
                //     $dataArray['emailMsg']='Email Sent Successfully';
                //     $req->session()->flash('alert-success', 'Email Notification sent');
                // }
                
            }
            
            elseif(isset($req['activity']) && $req['activity']=='reached_at_dropoff'){
                $result=$this->quotes->where('id','=',$id)->update(array('reached_at_dropoff'=>$current_time));
                //$mailData['subject']='Mr.'.$quoteData['driver']['name'].' reached at drop-off on address for delivery having PO#:'.$quoteData['po_number'];  
                //$mailData['body_message']='Mr.'.$quoteData['driver']['name'].' reached at address <strong>'.$quoteData['drop_off_street_address'].'</strong> to drop-off the delivery having PO Number: '.$quoteData['po_number'].' at '.date(config('constants.date_and_time'),$current_time);               
            }
            
            elseif(isset($req['activity']) && $req['activity']=='delivered'){
                $deliveryData=$this->quotes->where('id','=',$id)->with('delivery_proof')->get()->toArray();
                $deliveryData=$deliveryData[0];
              //   p($deliveryData);
                // echo $deliveryData=$this->quotes->where('id','=',$id)->with('delivery_proof')->toSql();
                
            // die;
                
                
                if(empty($deliveryData['delivery_proof'])){
                    $dataArray['error']='Yes'; 
                    $dataArray['title']='Please upload the proof of delivery first !'; 
                    echo json_encode($dataArray); exit;
                }
                else{

                    $result=$this->quotes->where('id','=',$id)->update(array('delivered'=>$current_time,'status'=>config('constants.quote_status.complete')));             
                    $mailData['subject']='Oodler Express delivered the delivery having PO#:'.$quoteData['po_number'];  
                    $mailData['body_message']='Oodler Express delivered the delivery at address <strong>'.$quoteData['drop_off_street_address'].'</strong> having PO Number'.$quoteData['po_number'].' on '.date(config('constants.date_and_time'),$current_time);  
                    
                    $uploadingPath=base_path().'/public/uploads';
                    if(base_path()!='/Users/waximarshad/office.oodlerexpress.com')
                    $uploadingPath=base_path().'/public_html/uploads';
                    //$filePath=$uploadingPath.'/'.$deliveryData['description'];
                    $files=array();
                    
                    foreach($deliveryData['delivery_proof'] as $filesData){
                        $files[]=$uploadingPath.'/'.$filesData['description'];
                    }
                    //p($files); die;
                    
                    $mailData["email"]=$emailAdd;
                        Mail::send('emails.delivered', $mailData, function($message)use($mailData, $files) {
                        $message->to($mailData["email"])
                        ->subject($mailData["subject"]);

                        foreach ($files as $file){
                        $message->attach($file);
                        }            
                        });


                        // This section is to add product in quickBook
                        $quote_price=(isset($quoteData['quote_agreed_cost']['quoted_price']) && $quoteData['quote_agreed_cost']['quoted_price']>0)?$quoteData['quote_agreed_cost']['quoted_price']:0;
                        $extra_charge=(isset($quoteData['quote_agreed_cost']['extra_charges']) && $quoteData['quote_agreed_cost']['extra_charges']>0)?$quoteData['quote_agreed_cost']['extra_charges']:0;
                        $agreed_amount=$quote_price+$extra_charge;

                        $item_data['name']=$quoteData['po_number'];
                        $item_data['description']='Customer: '.$quoteData['customer']['name'].', PICK-UP:'.$quoteData['pickup_street_address'].', DROP-OFF:'.$quoteData['drop_off_street_address'];
                        $item_data['unit_price']=$agreed_amount;
                        
                        //$qb_service_id=$this->add_quickbooks_item($item_data);
                        $qb_service_id=1;
                        if($qb_service_id>0){
                            $qb_service_added=$this->quotes->where('id',$id)->update(['qb_service_id'=>$qb_service_id]);
                            
                            $invoice_data['amount']=$agreed_amount;
                            $invoice_data['qb_service_id']=$qb_service_id;
                            $invoice_data['po_number']=$quoteData['po_number'];
                            $invoice_data['qb_customer_id']=$quoteData['customer']['quickbooks_customer_id'];
                            
                            $invoiceData=$this->add_quickbooks_sales($invoice_data);
                            if(isset($invoiceData) && $invoiceData['qb_invoice_id']>0 && $invoiceData['qb_invoice_no']>1){
                                $update_quote_rec=[];
                                $update_quote_rec['qb_invoice_id']=$invoiceData['qb_invoice_id'];
                                $update_quote_rec['qb_invoice_no']=$invoiceData['qb_invoice_no'];
                                $result=$this->quotes->where('id','=',$id)->update($update_quote_rec);
                            }else{
                                $dataArray['qb_error']=$invoiceData['message']; 
                            }
                            
                        }
                        else{
                            $dataArray['qb_error']='There is some error to add this quote in quickbook service!';
                        }

                        $dataArray['error']='No'; 
                        echo json_encode($dataArray); exit;

                }

                
            }
            else{
                $dataArray['error']='Yes';   
               // echo json_encode($dataArray); exit;
            }
            
            

   


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
        $dataArray['title']='Quote Status ';
        
        $activityComment='Mr.'.get_session_value('name').' declined the quote';
        $status=config('constants.quote_status.declined');
        $action=base64_decode($req['status']);
        
        if($action=='approved'){
            $status=config('constants.quote_status.approved');
            $activityComment='Mr.'.get_session_value('name').' approved the quote';
        }

        $result=$this->quotes->where('id','=',$id)->update(array('status'=>$status));

            if($result){

                // Email Section
                if($status==config('constants.quote_status.approved') || $status==config('constants.quote_status.declined')) 
                {
                            
                $quoteData=$this->quotes->where('id',$id)->with('customer')->get()->toArray();
                $quoteData=$quoteData[0];
                $actionMsg='Declined';
                if($status==config('constants.quote_status.approved'))
                $actionMsg='Approved';

                $mailData['body_message']='This email is to let you know that quote having PO No.:'.$quoteData['po_number'].' has been '.$actionMsg.' the quote on '.date('d/m/Y');   
                $mailData['subject']='Quote having PO No.:'.$quoteData['po_number'].' Status on Oodler Express';
                $toEmail=[
                    config('constants.admin_email'),
                    $quoteData['customer']['email']
                ];

                if(Mail::to($toEmail)->send(new EmailTemplate($mailData))){
                //    echo 'Thank you, Your Booking has been confirmed';
                }
                
                } 

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
       else if(isset($req['action']) && $req['action']=='update_delivery_price'){
        $dataArray['title']='Delivery Price';
        
        $activityComment='Mr.'.get_session_value('name').' changed delivery price';
        
        $to_update_data['quoted_price']=$req['data']['quoted_price'];
        $to_update_data['extra_charges']=$req['data']['extra_charges'];
        $to_update_data['reason_for_extra_charges']=$req['data']['reason_for_extra_charges'];
        $to_update_data['description']=$req['data']['description'];

        $result=$this->quote_prices->where('id',$req['data']['invoice_id'])->update($to_update_data);  
            if($result){


// Send Email
        $quoteData=$this->quotes->where('id',$id)->with('customer')->get()->toArray();
        $quoteData=$quoteData[0];

        $deliveryCost=$req['data']['quoted_price'];
        if(isset($req['data']['extra_charges']) && $req['data']['extra_charges']!='')
        $deliveryCost=$deliveryCost+$req['data']['extra_charges'];

        $mailData['body_message']='Following is the detail of the delivery cost, you can contact us if you have any question';
        $mailData['body_message'] .='<table width="100%" border="1">
        <tr><td>Delivery Cost  :</td><td>'.$deliveryCost.'</td></tr>';
        
        if(isset($req['data']['reason_for_extra_charges']) && $req['data']['reason_for_extra_charges']!='')
        $mailData['body_message'] .='<tr><td>Reason for Extra Charges :</td><td>'.$req['data']['reason_for_extra_charges'].'</td></tr>';
        // if(isset($req['data']['description']) && $req['data']['description']!='')
        // $mailData['body_message'] .='<tr><td>Additional Notes :</td><td>'.$req['data']['description'].'</td></tr>';
        $mailData['body_message'] .='</table>';
        $mailData['subject']='Delivery cost updated From Oodler Express';
     
        // $mailData['button_title']='APPROVE';
        // $mailData['button_link']=route('customer_action',['quote_id' => $id,'action'=>base64_encode('accept')]);
        // $mailData['button_title2']='Reject';
        // $mailData['button_link2']=route('customer_action',['quote_id' => $id,'action'=>base64_encode('reject')]);
        

         $emailAdd=[
                    config('constants.admin_email'),
                    $quoteData['customer']['email'],
                    
                ];
               
               

        if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
            $dataArray['emailMsg']='Email Sent Successfully';
        }
                $dataArray['msg']=$activityComment.' successfully!';
                // Activity Logged
            $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$req['data']['invoice_id'],
                'action_slug'=>'quote_price',
                'comments'=>$activityComment,
                'others'=>'quote_prices',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            }
       }
       else if(isset($req['action']) && $req['action']=='assign_driver_sub'){
            $dataArray['title']='Assigned Driver/Sub';
            $dataArray['error']='No';
            
            $quoteData=$this->quotes->where('id',$id)->get('po_number')->first();

            $data_to_update=[];
            if($req['data']['assign_to']==1){
                $assgin_to='Driver';
                if(isset($req['data']['driver_id']) && $req['data']['driver_id']>0){}else{
                    $dataArray['error']='yes';
                    $dataArray['msg']='You must have to select the Driver';
                    echo json_encode($dataArray); exit;
                   }
                $dataArray['reload']='yes';

                $data_to_update['sub_id']=NULL;
                $data_to_update['quoted_price_for_sub']=NULL;
                $data_to_update['assign_to']=$req['data']['assign_to'];
                $data_to_update['sub_status']=0;
                $data_to_update['driver_id']=$req['data']['driver_id'];
                $this->quotes->where(['id'=>$id])->update($data_to_update);

                $driver=$this->users->where(['id'=>$req['data']['sub_id']])->get(['name','business_email','email'])->first();
                $mailData['body_message']='You are assigned a new delivery, having PO Number:'.$quoteData->po_number.'. Please login to the CRM and look for details';
                $mailData['subject']='New Delivery Assigned';
    
                $emailAdd=[
                            config('constants.admin_email'),
                            $driver->email,
                            
                        ];
                if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
                    $dataArray['emailMsg']='Email Sent Successfully';
                }
                
            }else{
                
                if(isset($req['data']['sub_id']) && $req['data']['sub_id']>0){}else{
                     $dataArray['error']='yes';
                     
                     $dataArray['msg']='You must have to select the sub';
                     echo json_encode($dataArray); exit;
                    }
                    if($req['data']['quoted_price_for_sub']==''){
                     
                        $dataArray['error']='yes';
                        $dataArray['msg']='Price for the sub is required and it should be a number';
                        echo json_encode($dataArray); exit;
                    }
                
                $dataArray['reload']='yes';   
                $assgin_to='Sub';
                $data_to_update['sub_id']=$req['data']['sub_id'];
                $data_to_update['quoted_price_for_sub']=$req['data']['quoted_price_for_sub'];
                $data_to_update['assign_to']=$req['data']['assign_to'];
                $data_to_update['sub_status']=0;
                
                $this->quotes->where(['id'=>$id])->update($data_to_update);
                DB::table('quote_prices')->insert([
                    'quoted_price' => $req['data']['quoted_price_for_sub'],
                    'slug' => 'quoted_price_for_sub',
                    'quoted_uid' => get_session_value('id'),
                    'quote_id' =>$id,
                    'quote_price_for' =>2
                ]);

                   
                    // if Sub is selected then send an email to sub for approve or reject
                        $sub=$this->users->where(['id'=>$req['data']['sub_id']])->get(['name','business_email','email'])->first();
                    
                        $mailData['body_message']='You are assigned a new delivery, having PO Number:'.$quoteData->po_number.'.please click approve to be added to the delivery schedule. if you have any questions please feel free to contact us at 718-218-5239';
                        $mailData['subject']='New Delivery Assigned';
                        $mailData['body_message'] .='<table width="100%" border="1">
                        <tr><td>Delivery Cost  :</td><td>$'.$req['data']['quoted_price_for_sub'].'</td></tr>';
                        $mailData['body_message'] .='<tr><td colspan="2">'. quote_data_for_mail($id, config('constants.groups.sub')).'</td></tr>';
                        $mailData['body_message'] .='</table>';
                        $mailData['subject']='Your New Delivery From Oodler Express';
                    
                        $mailData['button_title']='APPROVE';
                        $mailData['button_link']=route('sub_action',['quote_id' => $id,'action'=>base64_encode('accept')]);
                        $mailData['button_title2']='Reject';
                        $mailData['button_link2']=route('sub_action',['quote_id' => $id,'action'=>base64_encode('reject')]);
                    
                        $emailAdd=[
                                    $sub->email,
                                ];
                        if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
                            $dataArray['emailMsg']='Email Sent Successfully';
                        }
                    
                        
            }
           

            $dataArray['msg']=$activityComment='Mr.'.get_session_value('name').' assigned to the '.$assgin_to;
            $dataArray['msg']=$activityComment.' successfully!';
            // Activity Logged
            $activityID=log_activity(array(
            'user_id'=>get_session_value('id'),
            'action_taken_on_id'=>$id,
            'action_slug'=>'delivery_assign_to_'.$assgin_to,
            'comments'=>$activityComment,
            'others'=>'quotes',
            'created_at'=>date('Y-m-d H:I:s',time()),
            ));

       }
       else if(isset($req['action']) && $req['action']=='change_sub_status'){
        $dataArray['title']='Sub Status ';
        $dataArray['error']='No';
        $dataArray['msg']=$activityComment='Mr.'.get_session_value('name').' changed the sub status';
        
       $sub_status=$req['data']['sub_status'];
       // $sub_status=$req['sub_status'];
        
            $data_to_update['sub_status']=$sub_status;

            $quoteData=$this->quotes->where('id',$id)->with('sub')->get()->toArray();
            $quoteData=$quoteData[0];

            if($sub_status==0){
                $actionMsg='Pending';
            }elseif($sub_status==1){
                $actionMsg='Approved';
            }elseif($sub_status==2){
                $dataArray['reload']='yes';
                $actionMsg='Removed';
                $data_to_update['sub_id']=NULL;
                $data_to_update['assign_to']=1;
                $data_to_update['quoted_price_for_sub']=NULL;

                $this->quote_prices->where(['quote_id'=>$id,'quote_price_for'=>2])->delete();
            }
            $result=$this->quotes->where('id','=',$id)->update($data_to_update);
            


            $mailData['body_message']='This email is to let you know that quote having PO No.:'.$quoteData['po_number'].' has been '.$actionMsg.' by the oodler Express on '.date(config('constants.date_formate'));   
            $mailData['subject']='Delivery having PO No.:'.$quoteData['po_number'].' Sub Status on Oodler Express';
            $toEmail=[
                config('constants.admin_email'),
                $quoteData['sub']['email']
            ];

            if(Mail::to($toEmail)->send(new EmailTemplate($mailData))){
            //    echo 'Thank you, Your Booking has been confirmed';
            } 
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
       else if(isset($req['action']) && $req['action']=='change_status'){
        $dataArray['title']='Quote Status ';
        
        $activityComment='Mr.'.get_session_value('name').' changed the quote status';
        $status=($req['current_status']);
        $dataArray['requesdata']=json_encode($req->all());
        $result=$this->quotes->where('id','=',$id)->update(array('status'=>$status));  

            if($result){
            // Email Section
             if($status==config('constants.quote_status.approved') || $status==config('constants.quote_status.declined')) 
             {
                          
                $quoteData=$this->quotes->where('id',$id)->with('customer')->get()->toArray();
                $quoteData=$quoteData[0];
                $actionMsg='Declined';
                if($status==config('constants.quote_status.approved'))
                $actionMsg='Approved';

                $mailData['body_message']='This email is to let you know that quote having PO No.:'.$quoteData['po_number'].' has been '.$actionMsg.' the quote on '.date('d/m/Y');   
                $mailData['subject']='Quote having PO No.:'.$quoteData['po_number'].' Status on Oodler Express';
                $toEmail=[
                    config('constants.admin_email'),
                    $quoteData['customer']['email']
                ];

                if(Mail::to($toEmail)->send(new EmailTemplate($mailData))){
                //    echo 'Thank you, Your Booking has been confirmed';
                }
               
            } 

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
        elseif(isset($req['action']) && $req['action']=='change_quote_driver'){ 
            $to_update_data['driver_id']=$req['data']['driver_id'];
            
            $dataArray['title']='Driver Changed';
            $result=$this->quotes->where('id','=',$id)->update($to_update_data);             
            if($result){

                    // Email Section

                    $driverData=$this->users->where('id',$to_update_data['driver_id'])->get('email')->toArray();
                    $driverData=$driverData[0];
            
                        
                    $quoteData=$this->quotes->where('id',$id)->get('po_number')->toArray();
                    $quoteData=$quoteData[0];
        
        
                    $mailData['body_message']='You are assigned a new delivery, having PO Number:'.$quoteData['po_number'].'. Please login to the CRM and look for details';
                    $mailData['subject']='New Delivery Assigned';
        
                    $emailAdd=[
                                config('constants.admin_email'),
                                $driverData['email'],
                                
                            ];
                        
                        
        
                    if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
                        $dataArray['emailMsg']='Email Sent Successfully';
                    }

                $dataArray['msg']='Mr.'.get_session_value('name').', Changed Driver successfully!';
                // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'quote_driver_changed',
                'comments'=>'Mr.'.get_session_value('name').' changed the driver',
                'others'=>'quotes',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            }
            
            else{
                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
            }
        }
        elseif(isset($req['action']) && $req['action']=='remove_quote_driver'){ 
            
            $driverData=$this->quotes->where('id','=',$id)->get('driver_id')->first();
            $to_update_data['driver_id']=NULL;//$req['data']['driver_id'];
            
            $dataArray['title']='Driver Removed';
            $result=$this->quotes->where('id','=',$id)->update($to_update_data);             
            if($result){
                    $dataArray['reload']='yes';
                    // Email Section
                    $driver_id=$driverData->driver_id;
                    $driverData=$this->users->where('id',$driver_id)->get('email')->toArray();
                    $driverData=$driverData[0];
            
                        
                    $quoteData=$this->quotes->where('id',$id)->get('po_number')->toArray();
                    $quoteData=$quoteData[0];
        
        
                    $mailData['body_message']='You are cancelled from the delivery, having PO Number:'.$quoteData['po_number'].'. Please login to the CRM and look for details';
                    $mailData['subject']='Assigned Delivery Cancelled for driver';
        
                    $emailAdd=[
                                config('constants.admin_email'),
                                $driverData['email'],
                                
                            ];
                        
                        
        
                    if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
                        $dataArray['emailMsg']='Email Sent Successfully';
                    }

                $dataArray['msg']='Mr.'.get_session_value('name').', Driver Removed successfully!';
                // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'quote_driver_removed',
                'comments'=>'Mr.'.get_session_value('name').' removed the driver of quote having PO Number:'.$quoteData['po_number'],
                'others'=>'quotes',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
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
            $this->comments->comment_section ='delivery';
            if(isset($req['data']['comment_section']))
            $this->comments->comment_section =$req['data']['comment_section'];

            $this->comments->quote_id =$id;
            
            $this->comments->status =1;
            $this->comments->save();
            $dataArray['error']='No';
            $dataArray['to_replace']='submit_comment_replace';
            $htmlRes=' <div class="row border">
                            <div class="col-12">
                                <strong>'.get_session_value('name').' ('.$req['data']['slug'].') </strong> '.date(config('constants.date_and_time'),time()).'<br>
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
        elseif(isset($req['action']) && $req['action']=='submit_comment_crm'){ 
            
             //p($req->all()); die;

            $this->comments->comment=$req['data']['comment'];
            $this->comments->user_id=get_session_value('id');
            $this->comments->group_id =$req['data']['group_id'];
            $this->comments->slug =$req['data']['slug'];
            //$this->comments->slug =$req['data']['user_name'];
            $this->comments->quote_id =$id;
            $this->comments->comment_section ='delivery_notes_only';
            $this->comments->status =1;
            $this->comments->save();
            $dataArray['error']='No';
            $dataArray['to_replace']='submit_comment_crm_replace';
            $htmlRes=' <div class="row border">
                            <div class="col-12">
                                <strong>'.get_session_value('name').' ('.$req['data']['slug'].') </strong> '.date(config('constants.date_and_time'),time()).'<br>
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
