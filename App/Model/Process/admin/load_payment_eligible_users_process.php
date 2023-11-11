<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\Admin;

if (isset($_SESSION["user"]) && $_SESSION["user"]["role"] === "admin") {
    if (isset($_POST["offset"])) {
        if (filter_var($_POST["offset"], FILTER_VALIDATE_INT) || $_POST["offset"] == 0) {
            $admin = new Admin();
            echo json_encode($admin->loadPaymentEligibleUsers(DBConnector::getConnection(), $_POST["offset"]));
            exit();
        }
    } else {
        $admin = new Admin();
        echo json_encode($admin->loadPaymentEligibleWithCategorize(DBConnector::getConnection()));
        exit();
    }
}
echo 500;
exit();
