<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Others\Vehicle;

if (isset($_POST["start"], $_POST["end"], $_POST["type"], $_POST["district"], $_POST["driver"])) {
    if (!empty($_POST["start"]) && !empty($_POST["end"]) && !empty($_POST["type"]) && !empty($_POST["district"]) && !empty($_POST["driver"])) {
        $connection = DBConnector::getConnection();
        $start = strip_tags(trim($_POST["start"]));
        $end = strip_tags(trim($_POST["end"]));
        $type = strip_tags(trim($_POST["type"]));
        $district = strip_tags(trim($_POST["district"]));
        $driver = strip_tags(trim($_POST["driver"]));

        $start = DateTime::createFromFormat('Y / F / d', $start)->format('Y-m-d');
        $end = DateTime::createFromFormat('Y / F / d', $end)->format('Y-m-d');

        if ((strtotime($end) - strtotime($start)) < 0) {
            echo 47;
            exit();
        }

        $vehicle = new Vehicle();
        echo $vehicle->vehicleByDistrict(DBConnector::getConnection(), $district, $type, $start, $end, $driver);
    } else {
        echo "500";
        exit();
    }
} else {
    echo "500";
    exit();
}
