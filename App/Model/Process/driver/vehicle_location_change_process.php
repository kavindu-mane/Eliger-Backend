<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Others\Vehicle;

if (isset($_SESSION["user"])) {
    if (isset($_POST["lat"], $_POST["lng"], $_POST["vehicle"])) {
        if (
            filter_var($_POST["lat"], FILTER_VALIDATE_FLOAT) &&
            filter_var($_POST["lng"], FILTER_VALIDATE_FLOAT) &&
            filter_var($_POST["vehicle"], FILTER_VALIDATE_INT)
        ) {
            $vehicle = new Vehicle();
            if ($vehicle->changeLocationStatus(DBConnector::getConnection(), $_POST["vehicle"], $_POST["lat"], $_POST["lng"])) {
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
