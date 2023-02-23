<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\adminpanel\quickbook_credentials;


use QuickBooksOnline\API\Core\ServiceContext;
use QuickBooksOnline\API\Exception\ServiceException;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Customer;
use QuickBooksOnline\API\Facades\Item;
use QuickBooksOnline\API\Facades\SalesReceipt;
use QuickBooksOnline\API\Facades\Invoice;
use QuickBooksOnline\API\Facades\Line;
use QuickBooksOnline\API\Facades\Payment;
use GuzzleHttp\Client;

use QuickBooksOnline\API\Data\IPPCustomer;
use QuickBooksOnline\API\Data\IPPInvoice;
use QuickBooksOnline\API\Data\IPPPayment;


use QuickBooksOnline\API\Data\IPPReferenceType;
use QuickBooksOnline\API\Data\IPPTransaction;
use QuickBooksOnline\API\Data\IPPTransactionLine;
use QuickBooksOnline\API\Data\IPPTransactionTypeEnum;




use QuickBooksOnline\API\PlatformService\PlatformService;

use QuickBooksOnline\API\Diagnostics\Logger;
use QuickBooksOnline\API\Diagnostics\LoggerLevel;
use QuickBooksOnline\API\Diagnostics\LoggerType;

use Monolog\Handler\StreamHandler;
// To Renew Access Token
use QuickBooksOnline\API\Data\OAuth2\OAuth2LoginHelper;
use QuickBooksOnline\API\Data\OAuth2\OAuth2AccessToken;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2AccessTokenEntity;

// Mark Payment
use QuickBooksOnline\API\Data\IPPLinkedTxn;

class quickbooks extends Controller
{
    function __construct() {
        
        $this->quickbook_credentials= new quickbook_credentials;
     
      }

    public function get_quickbooks_access_token(){

        $config = config('quickbooks');
        $client = new Client();
        $response = $client->post('https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer', [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $config['refresh_token'],
                'client_id' => $config['client_id'],
                'client_secret' => $config['client_secret'],
                'redirect_uri' => $config['redirect_uri'],
                'scope' => 'com.intuit.quickbooks.accounting',
                //'scope' => 'com.intuit.quickbooks.accounting, com.intuit.quickbooks.payment, openid, profile, email, phone, address',
                'duration' => 3600,
            ]
        ]);
        
        return $newAccessToken = json_decode($response->getBody(), true)['access_token'];
        
    }
    
    public function add_quickbooks_item(){
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

        $targetInvoiceArray = $dataService->Query("SELECT * FROM Item where Name='Services'");
p($targetInvoiceArray); die;
        // Create the product or service item
        $product = Item::create([
            "Name" => "PO-NUmber",
            "Description" => "An example product for demonstration purposes",
            "UnitPrice" => 9.99,
            "Type" => "Service",
            "IncomeAccountRef" => [
                "value" => "91"
            ]
        ]);

        // Add the item to QuickBooks
        $resultingItemObj = $dataService->Add($product);

        $error = $dataService->getLastError();
                    if ($error) {
                        p($error);
                        echo "The Status code is: " . $error->getHttpStatusCode() . "<br>";
                        echo "The Helper message is: " . $error->getOAuthHelperError() . "<br>";
                        echo "The Response message is: " . $error->getResponseBody() . "<br>";
                    }
                    else {
                        echo "Created Service Id={$resultingItemObj->Id}. Reconstructed response body:<br>";
                        $xmlBody = XmlObjectSerializer::getPostXmlFromArbitraryEntity($resultingItemObj, $urlResource);
                        echo $xmlBody . "<br>";
                    }

        // Check if the item was added successfully
        if ($resultingItemObj) {
            return "The product was added successfully";
        } else {
            p($resultingItemObj);
            return "There was an error adding the product";
        }

    }

        public function get_quickbooks_customer_by_email($access_token, $email){
        
        $config = config('quickbooks');
        $client = new Client();

        $response = $client->get($config['api_url'].$config['realm_id'].'/customer/64', [
            'headers' => [
                'Authorization' =>'Bearer '.$config['access_token'],
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);
        
       return $customer = json_decode($response->getBody(), true)['Customer'];
        
        }

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
            // echo "<br>Access Token is:= ";
            // print_r($accessTokenValue);
            // echo "<br>RefreshToken Token is:= ";
            // print_r($refreshTokenValue);

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

        public function createCustomer()
        {
            
            
            
            
            $config = config('quickbooks');

            $tokenData=$this->updated_access_token();
            $config['access_token']=$tokenData['access_token'];
            $config['refresh_token']=$tokenData['refresh_token'];
           //$access_token=$this->get_quickbooks_access_token();
           
           //echo $config['token_refresh_interval_before_expiry'];
            
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


            $dataService->throwExceptionOnError(true);
            // Create a new customer object
            $customer = Customer::create([
                "GivenName" => 'Lead Customer2',
                "FamilyName" => 'Lead Customer-2',
                //"DisplayName" =>  'Lead Customer-2'.rand(1,1000),
                "DisplayName" =>  'Lead Customer-6',
                "PrimaryEmailAddr" => [
                    "Address" => 'billing@gmail.com'
                ],
                "BillAddr" => [
                    "Line1" => "123 Main Street",
                    "City" => "Mountain View",
                    "Country" => "USA",
                    "CountrySubDivisionCode" => "CA",
                    "PostalCode" => "94042"
                ],
                "PrimaryPhone" => [
                    "FreeFormNumber" => '+923007731712'
                ]
            ]);

            
            try {
                // Use the DataService to add the customer to QuickBooks Online
                $result = $dataService->Add($customer);
                echo $result->Id;
                ///$result = $dataService->Add($customer);
            } catch (ServiceException $ex) {
                // Handle the exception by displaying the error message
            

                 echo  "Error message: " . $ex->getMessage();
                // echo  "<br>Error Detail: " . $ex->getErrorDetail();
                ///return view('customer', ['error' => $error]);
            }
            
            
            $error = $dataService->getLastError();
            $error=json_decode($error,true);
            p($error);
           // echo $result->Id;

            // if ($error) {
            //     return response()->json(['error' => $error], 500);
            // } else {
            //     return response()->json(['success' =>$result]);}
        }
        public function receive_payment(){
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

            //$invoice = $dataService->Query("SELECT * FROM Invoice WHERE Id = '169'");
            // $invoice=new Invoice();
            // $invoice->Id=169;

            $customer_id=59;
            $invoice_id=167;
            // Set up the payment details
            $payment = new IPPTransaction();
            $payment->TxnDate=(date('Y-m-d'));
            $payment->TotalAmt=(100.00);
            $payment->PaymentRefNum=('12345');
            $payment->TxnType=(IPPTransactionTypeEnum::PAYMENT);
            $payment->CustomerRef=(IPPReferenceType::create([
                'value' => '$customer_id',
                //'name' => 'John Doe',
            ]));

            //  up the payment line item
            $line = new IPPTransactionLine();
            $line->Amount=(100.00);
            $line->LinkedTxn=(IPPReferenceType::create([
                'value' => $invoice_id,//'67',
                'type' => 'Invoice',
            ]));

            // Add the payment line item to the payment
            $payment->setLine([$line]);

            // Create the payment in QuickBooks
            $paymentResult = Payment::create($payment, $dataService->getContext());

            // Get the payment ID
            $paymentId = $paymentResult->Id;

            // Update the invoice to show the payment
            $invoice = $dataService->FindById('Invoice', $invoice_id);
            $invoice->setBalance(0.00);
            $invoice->setDeposit(100.00);
            $invoice->setAllowIPNPayment(true);
            $invoice->setAllowOnlinePayment(true);
            $invoice->setAllowOnlineACHPayment(true);
            $invoice->setAllowOnlineCreditCardPayment(true);
            $invoice->setAllowOnlineECheckPayment(true);
            $invoice->setAllowOnlineOtherPayment(true);
            $invoice->setAllowOnlineWalletPayment(true);

            // Add the payment reference to the invoice
            $paymentReference = new IPPReferenceType();
            $paymentReference->setValue($paymentId);
            $paymentReference->setType('Payment');
            $invoice->setLinkedTxn([$paymentReference]);

            // Update the invoice in QuickBooks
            $dataService->Update($invoice);
            p($dataService);

        }
        public function quickbook_test_new(){
         
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
          
                            // Load the invoice with ID 188
                    $invoice = $dataService->FindById('Invoice', 3055);
            
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
                    $result = $dataService->Update($invoice);
                    
     


                                

        }
                 
            public function add_quickbooks_new_sales(){
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
                                "Amount" => 200.00,
                                "Description" => "Sewing Service for Alex",
                                "DetailType" => "SalesItemLineDetail",
                                "SalesItemLineDetail" => [
                                    "ItemRef" => [
                                        "value" => 11,
                                        "name" => "Services"
                                    ],
                                    "UnitPrice" => 200.00,
                                    "Qty" => 1
                                ]
                            ]
                        ],
                        //"DocNumber" => NULL,
                        "PONumber" => 12345,
                        "CustomerRef" => [
                            "value" => 69
                        ],
                        "TxnDate" => date('Y-m-d',time()),//"2023-02-21"
                    ]);

                    echo 'here:'.$invoice->DocNumber =generateInvoiceNumber() ;
                    $invoice->PrivateNote = 'These Private Notes';
                    $invoice->DueDate ="2023-02-27";
                    $invoice->PONumber = '12345';
                    try{
                        $resultingInvoiceObj = $dataService->Add($invoice);

                        $error = $dataService->getLastError();
                        echo "Created Invoice Id={$resultingInvoiceObj->Id}. Reconstructed response body:<br>";
                        echo "Created Invoice No={$resultingInvoiceObj->DocNumber}. Reconstructed response body:<br>";
                        $xmlBody = XmlObjectSerializer::getPostXmlFromArbitraryEntity($resultingInvoiceObj, $urlResource);
                        echo $xmlBody . "<br>";
                        p($resultingInvoiceObj);

                    }catch (ServiceException $ex) {
                    $retData['qb_invoice_id']=0;
                    $retData['qb_invoice_no']=0;
                    $retData['error']='YES';
                    
                    $retData['message']='QUICKBOOK:'.$ex->getMessage();
                    //$error = $dataService->getLastError();
                   
                }
                  die;
                    $error = $dataService->getLastError();
                    if ($error) {
                        echo "The Status code is: " . $error->getHttpStatusCode() . "<br>";
                        echo "The Helper message is: " . $error->getOAuthHelperError() . "<br>";
                        echo "The Response message is: " . $error->getResponseBody() . "<br>";
                    }
                    else {
                        echo "Created Invoice Id={$resultingInvoiceObj->Id}. Reconstructed response body:<br>";
                        echo "Created Invoice No={$resultingInvoiceObj->DocNumber}. Reconstructed response body:<br>";
                        $xmlBody = XmlObjectSerializer::getPostXmlFromArbitraryEntity($resultingInvoiceObj, $urlResource);
                        echo $xmlBody . "<br>";
                    }

            }


public function _curl($api_url='', $postData=array()){
	

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

        public function makePayment($payment='ACH'){

                $xKey = "softheincdevbfa91961dfd44596a9eaa16d34d8cc72";
                $url = "https://x1.cardknox.com/gatewayjson";
                $url = "https://x1.cardknox.com/gatewayform";

                if($payment=='ACH'){
                    $post_data = array(
                        "xCommand" => "check:Sale",
                        "xAmount" => "1.00",
                        "xKey" => $xKey,
                        "xName" => "Demo New",
                        "xRouting" => "021000021",
                        "xAccount" => "123456789",
                        "xInvoice" => "54000",
                        "xEmail" => "to.wazim@gmail.com",
                        "xPONum" => "PONO#12113",
                        "xDescription" => "Here is any Description",
                        "xCustom01" => "Wasim Arshad",
                        "xSoftwareName" => "OodlerExpress",
                        "xSoftwareVersion" => "1.0.0",
                        "xVersion" => "5.0.0",
                    );

                }

                else{
                    $post_data = array(
                    "xCommand" => "cc:Sale",
                    "xAmount" => "1.00",
                    "xCardNum" => "4111111111111111",
                    "xExp" => "1223",
                    "xCVV" => "123",
                    "xKey" => $xKey,
                    "xName" => "DemoNew",
                    "xZip" => "54000",
                    "xEmail" => "to.wazim@gmail.com",
                    "xPONum" => "PONO#12113",
                    "xDescription" => "Here is any Description",
                    "xCustom01" => "Wasim Arshad",
                    "xSoftwareName" => "OodlerExpress",
                    "xSoftwareVersion" => "0.01",
                    "xVersion" => "5.0.0",
                    "xOrderId" => "1",

                );
            
                }

                $post_data["xBillFirstName"] = "Ali ";
                $post_data["xBillLastName"] = "Affan";
                $post_data["xBillCompany"] = "Company name";
                $post_data["xBillStreet"] = "Streat3Lahore";
                $post_data["xBillState"] = "Punjab";
                $post_data["xBillCity"] = "lahore";
                $post_data["xBillZip"] = "54000";
                $post_data["xBillPhone"] = "03004654546";
                $post_data["xBillMobile"] = "0304045644";

                // p($post_data);
                 $output= $this->_curl($url, $post_data);
                // echo '<textarea>';
                // echo $output;
                // echo '</textarea>'; die;
                 $tmp = explode("\n",$output); 
                 $result_string = $tmp[count($tmp)-1]; 
                 parse_str($result_string, $result_array);  
                 p($result_array); 

                //  $result=explode('&',$output);
                // p($result);
               
        }

        public function get_transaction_msg($result){
            $retData['error']='NO';
            if($result['xResult']=='A' && $result['xStatus']=='Approved'){
                $retData['errorMsg']='Transaction done successfully !';
            }
            else if($result['xResult']=='E' && $result['xStatus']=='Error'){
                $retData['error']='YES';
                $retData['errorMsg']=$result['xError'];
            }
        }

}
