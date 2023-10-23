<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\VehicleOwner;

if (isset($_POST["type"], $_SESSION["user"], $_POST["offset"])) {
    if (filter_var($_POST["offset"], FILTER_VALIDATE_INT) || $_POST["offset"] == 0) {
        if ($_POST["type"] === "driver") {
            //pass data(function call)
            $owner = new VehicleOwner();
            echo json_encode($owner->loadDriver(DBConnector::getConnection(), $_SESSION["user"]["id"], isset($_POST["status"]), $_POST["offset"]));
            exit();
        } elseif ($_POST["type"] === "vehicle") {
            //pass data(function call)
            $owner = new VehicleOwner();
            echo json_encode($owner->loadVehicles(DBConnector::getConnection(), $_SESSION["user"]["id"], $_POST["offset"]));
            exit();
        }
    }
}
echo 500;
exit();
