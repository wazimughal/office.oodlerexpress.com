<?php


return [
    'app_name'=>'CRM Oodler Express',
    'per_page'=>2,
    // Costant Value ID set for the groups table for User ROLEs
    'groups' => [
        'admin' => 1,
        'customer' => 2,
        'driver' => 3,
        'staff' =>4,
        'subscriber' =>5,
    ],
    'lead_status' => [
        'pending' => 0,
        'approved' => 1,
        'cancelled' => 2,
        'trashed' => 3,
    ]
];

/*
    'customer_added' => 'User Added a customer',
    'new_lead_added '=> New Lead Added
    
*/
?>