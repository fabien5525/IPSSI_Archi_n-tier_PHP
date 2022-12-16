<?php

$userId = $_GET['user_id'];
require_once('../../Database.php');


$AllSub = getSubscriptions($userId);

$Payrequest = Pay(int $userId, $AllSub);
echo json_encode([$Payrequest]);