<?php
$allowedStates = ['AZ', 'CA', 'CO', 'FL', 'NV', 'NM', 'TX', 'UT'];
$stateRules = [
    'AZ' => ['minOrder' => 2, 'zone' => 'Southwest'],
    'CA' => ['minOrder' => 3, 'zone' => 'West'],
    'CO' => ['minOrder' => 2, 'zone' => 'Mountain'],
    'FL' => ['minOrder' => 2, 'zone' => 'Southeast'],
    'NV' => ['minOrder' => 2, 'zone' => 'West'],
    'NM' => ['minOrder' => 2, 'zone' => 'Southwest'],
    'TX' => ['minOrder' => 2, 'zone' => 'South'],
    'UT' => ['minOrder' => 2, 'zone' => 'Mountain'],
];

$zipRestrictions = [
    'AZ' => ['85001', '85301', '86001'],
    'CA' => ['90001', '90210', '94105'],
    'CO' => ['80001', '80111', '80202'],
    'FL' => ['33004', '33131', '33602'],
    'NV' => ['89101', '89431', '89501'],
    'NM' => ['87101', '87001', '88201'],
    'TX' => ['75001', '75201', '77002'],
    'UT' => ['84101', '84001', '84601'],
];

