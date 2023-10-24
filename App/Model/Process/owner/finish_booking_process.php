<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Others\Payment;

if (isset($_SESSION["user"])) {
    if (isset($_POST["booking"], $_POST["amount"])) {
        if (
            filter_var($_POST["booking"], FILTER_VALIDATE_INT) &&
            filter_var($_POST["amount"], FILTER_VALIDATE_FLOAT)
        ) {
            $payment = new Payment();
            $payment->setBookingId($_POST["booking"]);
            $payment->setPaymentType("offline");
            $payment->setAmount($_POST["amount"]);
            if ($payment->pay(DBConnector::getConnection())) {
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
