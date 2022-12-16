<?php

$userId = $_GET['user_id'];
require_once('../../Model/Database.php');


$AllSub = Database::existUser($userId);
echo json_encode($AllSub);