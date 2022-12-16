<?php
require_once('../../Model/Database.php');


$AllSub = Database::querySkins();
echo json_encode($AllSub);