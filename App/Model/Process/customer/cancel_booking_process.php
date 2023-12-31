<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Others\Booking;

if (isset($_SESSION["user"])) {
    if (isset($_POST["booking"], $_POST["status"])) {
        if (
            filter_var($_POST["booking"], FILTER_VALIDATE_INT) &&
            $_POST["status"] === "canceled"
        ) {
            $booking = new Booking();
            if ($booking->changeBookingStatus(DBConnector::getConnection(), $_POST["booking"], $_POST["status"])) {
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
