<?php
require_once './vendor/autoload.php';

$token = '';

$smsMode = new \SMSMode\SMSMode($token, "YOU sender");

$balance = $smsMode->getBalance();
var_dump($balance);

$sms = $smsMode->sendSimple(
    ["you number"],
    "Hello. This test message from SMSMode",
);
var_dump($sms);

$balance = $smsMode->getBalance();
var_dump($balance);

$status = $smsMode->checkStatus('YUeG9dXiDkyA');
var_dump($status);
