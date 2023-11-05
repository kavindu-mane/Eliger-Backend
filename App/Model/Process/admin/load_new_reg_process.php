<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\Admin;


if (isset($_SESSION["user"], $_POST["offset"])) {
    if (filter_var($_POST["offset"], FILTER_VALIDATE_INT) || $_POST["offset"] == 0) {
        if (isset($_POST["vehicle_status"])) {

            //pass data(function call)
            $admin = new Admin();
            echo json_encode($admin->loadNewVehicles(DBConnector::getConnection(), $_POST["vehicle_status"], $_POST["offset"]));
            exit();
        } else if (isset($_POST["driver_status"])) {

            //pass data(function call)
            $admin = new Admin();
            echo json_encode($admin->loadNewDriver(DBConnector::getConnection(), $_POST["driver_status"]));
            exit();
        }
    }
}

echo 500;
exit();
