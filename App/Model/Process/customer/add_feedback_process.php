<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\Customer;

if (isset($_SESSION["user"])) {
    if (isset($_POST["vehicle"], $_POST["booking"], $_POST["customer"], $_POST["rating"], $_POST["comment"])) {
        if (
            filter_var($_POST["vehicle"], FILTER_VALIDATE_INT) &&
            filter_var($_POST["booking"], FILTER_VALIDATE_INT) &&
            filter_var($_POST["customer"], FILTER_VALIDATE_INT) &&
            filter_var($_POST["rating"], FILTER_VALIDATE_INT)
        ) {
            $customer = new Customer();
            if ($customer->addFeedback(
                DBConnector::getConnection(),
                $_POST["customer"],
                $_POST["vehicle"],
                $_POST["booking"],
                $_POST["rating"],
                strip_tags(trim($_POST["comment"]))
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
echo 500;
exit();
