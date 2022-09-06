<?php


return [
    'app_name'=>'CRM Oodler Express',
    'per_page'=>2,
    // Costant Value ID set for the groups table for User ROLEs
    'groups' => [
        'admin' => 1,
        'venue_group_hod' => 2,
        'customer' => 3,
        'photographer' => 4,
        'staff' =>5,
        'subscriber' =>6,
    ],
    'lead_types' => [
        0 => array('id'=>0, 'title'=>'New'),
        1 => array('id'=>1, 'title'=>'Hot'),
        2 =>array('id'=>2, 'title'=>'Cold')
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