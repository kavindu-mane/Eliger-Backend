<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Others\Vehicle;

if (isset($_POST["lat"], $_POST["long"], $_POST["type"])) {
    if (!empty($_POST["lat"]) && !empty($_POST["long"]) && !empty($_POST["type"])) {
        $connection = DBConnector::getConnection();
        $lat = $_POST["lat"];
        $long = $_POST["long"];
        $type = $_POST["type"];

        $vehicle = new Vehicle();
        echo $vehicle->nearVehicles(DBConnector::getConnection(), $lat, $long, $type);
    } else {
        echo "500";
    }
} else {
    echo "500";
}