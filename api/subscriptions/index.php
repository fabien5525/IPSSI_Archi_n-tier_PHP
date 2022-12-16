<?php
require_once('../../Model/Database.php');

$userId = $_GET['user_id'];


$AllSub = Database::getSubscriptions($userId);
echo json_encode($AllSub);