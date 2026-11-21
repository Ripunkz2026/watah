<?php
$allowedStates = []; $stateRules = []; $zipRestrictions = [];
foreach (deliveryStates() as $state) {
    if (!$state['enabled']) continue;
    $allowedStates[] = $state['code'];
    $stateRules[$state['code']] = ['minOrder' => $state['min_order'], 'zone' => $state['zone']];
    if ($state['restrict_zips']) $zipRestrictions[$state['code']] = $state['zips'];
}