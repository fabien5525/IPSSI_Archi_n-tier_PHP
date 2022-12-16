<?php

require_once('./debug.php');
require_once('Model/Database.php');

if (Database::connection()) {
    echo 'Database connection Working';
} else {
    echo 'Database connection failed';
}

if ($_GET['init'] == 'true') {
    echo '<br/> Database init...';
    if (Database::initDatabase() === 1) {
        echo '<br/> Database initialised';
    } else {
        echo '<br/> Error in database initialisation';
    }
}
