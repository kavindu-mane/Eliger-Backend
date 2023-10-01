<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\VehicleOwner;

if (isset($_SESSION["user"])) {
    $owner = new VehicleOwner();
    echo $owner->loadOwner(DBConnector::getConnection(), $_SESSION["user"]["id"]);
} else {
    echo 14;
}