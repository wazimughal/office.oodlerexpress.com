<?php


return [
    'app_name'=>'CRM Oodler Express',
    'admin_email'=>'sales@oodlerexpress.com',
    'google_api_key'=>'AIzaSyA1JM99SFagfbshQ0xgQQmUXlgfvi-MUDw', // Google Api Key for Google map and place suggestion
    'date_formate'=>'m/d/Y',
    'date_formate_us'=>'m/d/Y',
    'date_and_time'=>'m/d/Y h:i:s',
    'per_page'=>10,
    // Costant Value ID set for the groups table for User ROLEs
    'groups' => [
        'admin' => 1,
        'customer' => 2,
        'driver' => 3,
        'sub' => 4,
        'subscriber' =>5,
    ],
    'quote_status' => [
        'pending' => 0,
        'quote_submitted' => 1,
        'approved' => 2,
        'declined' => 3,
        'trashed' => 4,
        'delivery' => 5,
        'complete' => 6,
    ],
  
];

/*
    'customer_added' => 'User Added a customer',
    'new_lead_added '=> New Lead Added
    
*/
?>