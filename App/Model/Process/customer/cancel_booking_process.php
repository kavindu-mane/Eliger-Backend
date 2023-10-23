<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Others\Booking;

if (isset($_SESSION["user"])) {
    if (isset($_POST["booking"])) {
        if (filter_var($_POST["booking"], FILTER_VALIDATE_INT)) {
            $booking = new Booking();
            if ($booking->cancelBooking(
                DBConnector::getConnection(),
                $_POST["booking"]
            )) {
                echo 200;
                exit();
            }
        }
    }
} else {
    echo 14;
    exit();
}
