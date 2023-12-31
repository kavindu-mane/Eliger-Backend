<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Others\Vehicle;

if (isset($_SESSION["user"])) {
    if (isset($_POST["vehicle"])) {
        if (
            filter_var($_POST["vehicle"], FILTER_VALIDATE_INT)
        ) {
            $vehicle = new Vehicle();
            echo $vehicle->getVehicleCurrentLocation(DBConnector::getConnection(), $_POST["vehicle"]);
            exit();
        }
    }
} else {
    echo 14;
    exit();
}
echo 500;
exit();
