<?php

$userId = $_GET['user_id'];
require_once('../../Model/Database.php');

$subscriptionId = $_GET['subscription_id']

Database::setSubscriptionToUser($userId,$subscriptionId);