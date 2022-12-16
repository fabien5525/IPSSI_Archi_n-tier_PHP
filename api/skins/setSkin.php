<?php

$userId = $_GET['user_id'];
require_once('../../Model/Database.php');

$skinId = $_GET['skin_id']

Database::setSkinsToUser($userId,$skinId);