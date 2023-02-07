<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\Facades\Customer;
use QuickBooksOnline\API\Facades\SalesReceipt;
use QuickBooksOnline\API\Facades\Invoice;
use GuzzleHttp\Client;



class quickbooks extends Controller
{
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
        public function createCustomer()
        {
            $config = config('quickbooks');
            
            //$access_token=$this->get_quickbooks_access_token();
           
            
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
            $customer = Customer::create([
                "GivenName" => 'Haroon1',
                "FamilyName" => 'Ahmad1',
                "DisplayName" =>  'Haroon Ahmad5',
                "PrimaryEmailAddr" => [
                    "Address" => 'haroonahmad@gmail.com'
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

            
            $result = $dataService->Add($customer);
            $error = $dataService->getLastError();
            echo $result->Id;

            if ($error) {
                return response()->json(['error' => $error], 500);
            } else {
                return response()->json(['success' =>$result]);}
        }

            public function add_quickbooks_sales(){
                // Connect to QuickBooks
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
            
                            
                    $invoice = Invoice::create([
                        "Line" => [
                            [
                                "Amount" => 100.00,
                                "DetailType" => "SalesItemLineDetail",
                                "SalesItemLineDetail" => [
                                    "ItemRef" => [
                                        "value" => 1,
                                        "name" => "Services"
                                    ],
                                    "UnitPrice" => 100.00,
                                    "Qty" => 1
                                ]
                            ]
                        ],
                        "CustomerRef" => [
                            "value" => 59
                        ],
                        "TxnDate" => "2023-02-06"
                    ]);

                    $resultingInvoiceObj = $dataService->Add($invoice);

                    $error = $dataService->getLastError();
                    if ($error) {
                        echo "The Status code is: " . $error->getHttpStatusCode() . "<br>";
                        echo "The Helper message is: " . $error->getOAuthHelperError() . "<br>";
                        echo "The Response message is: " . $error->getResponseBody() . "<br>";
                    }
                    else {
                        echo "Created Invoice Id={$resultingInvoiceObj->Id}. Reconstructed response body:<br>";
                        $xmlBody = XmlObjectSerializer::getPostXmlFromArbitraryEntity($resultingInvoiceObj, $urlResource);
                        echo $xmlBody . "<br>";
                    }

            }

        public function makePayment(){

                $xKey = "softheincdevc19ba53cd5db4b1a881a8e82f5269b89";
                $xPin = "ifields_softheincdevaefb9bc6f27244d88a5e608b1";
                $url = "https://www.cardknox.com/gateway/transact.dll";

                $post_data = array(
                    "xCommand" => "sale",
                    "xAmount" => "10.00",
                    "xCardNum" => "4111111111111111",
                    "xExp" => "1223",
                    "xCVV" => "123",
                    "xKey" => $xKey,
                    "xPin" => $xPin
                );

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));

                $output = curl_exec($ch);

                if (curl_errno($ch)) {
                    echo "Curl error: " . curl_error($ch);
                }

                curl_close($ch);

                $output_array = explode("&", $output);
p($output); die;
                $response = array();
                foreach ($output_array as $key_value) {
                    $key_value_array = explode("=", $key_value);
                    $response[$key_value_array[0]] = $key_value_array[1];
                }

                if ($response["xResponseCode"] == "1") {
                    echo "Transaction was successful";
                } else {
                    echo "Transaction was not successful. Error: " . $response["xErrorMessage"];
                }
        }
}
