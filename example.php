<?php
require_once './vendor/autoload.php';

$token = '83igEIIfdLgupYepFtbCht8doIwPtAl8';

$smsMode = new \SMSMode\SMSMode($token);

$smsMode->getBalance();
