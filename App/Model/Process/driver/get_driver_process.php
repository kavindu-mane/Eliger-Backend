<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\Driver;

if (isset($_SESSION["user"])) {
    $driver = new Driver();
    echo $driver->loadDriver(DBConnector::getConnection(), $_SESSION["user"]["id"]);
} else {
    echo 14;
}
