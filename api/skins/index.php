<?php

$userId = $_GET['user_id'];
require_once('../../Model/Database.php');


$AllSub = Database::getSkins($userId);
echo json_encode($AllSub);