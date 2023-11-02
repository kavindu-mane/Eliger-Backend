<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Others\Vehicle;

$status = array("available", "not available");

if (isset($_SESSION["user"])) {
    if (isset($_POST["availability"], $_POST["vehicle"])) {
        if (in_array($_POST["availability"], $status) &&  filter_var($_POST["vehicle"], FILTER_VALIDATE_INT)) {
            $vehicle = new Vehicle();
            if ($vehicle->changeVehicleStatus(DBConnector::getConnection(), $_POST["vehicle"], $_POST["availability"])) {
                echo 200;
                exit();
            }
        }
    }
} else {
    echo 14;
    exit();
}
echo 500;
exit();
