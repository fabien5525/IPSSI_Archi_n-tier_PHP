<?php

$userId = $_GET['user_id']
require_once('../../Database.php')


$AllSub = getTickets(int $userId)
echo json_encode($AllSub);