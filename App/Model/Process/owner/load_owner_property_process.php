<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\VehicleOwner;

if (isset($_POST["type"], $_SESSION["user"])) {

    if ($_POST["type"] === "driver") {
        //pass data(function call)
        $owner = new VehicleOwner();
        echo json_encode($owner->loadDriver(DBConnector::getConnection(), $_SESSION["user"]["id"], isset($_POST["status"])));
        exit();
    } elseif ($_POST["type"] === "vehicle") {
        //pass data(function call)
        $owner = new VehicleOwner();
        echo json_encode($owner->loadVehicles(DBConnector::getConnection(), $_SESSION["user"]["id"]));
        exit();
    } else {
        echo 500;
        exit();
    }
} else {
    echo 500;
    exit();
}
