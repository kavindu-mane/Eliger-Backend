<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\VehicleOwner;

if (isset($_SESSION["user"])) {
    $owner = new VehicleOwner();
    echo json_encode($owner->loadAvailableDriver(DBConnector::getConnection(), $_SESSION["user"]["id"]));
    exit();
}
echo 500;
exit();
