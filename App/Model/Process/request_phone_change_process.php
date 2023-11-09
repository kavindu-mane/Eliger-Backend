<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\User;

if (isset($_SESSION["user"])) {
    if (isset($_POST["phone"])) {
        // validate phone number
        if (!preg_match('/^94{1}[0-9]{9}$/', $_POST["phone"])) {
            echo 6;
            exit();
        }

        $user = new User();
        echo $user->sendPhoneChangeOTP(DBConnector::getConnection(), $_SESSION["user"]["id"], $_POST["phone"]);
        exit();
    }
} else {
    echo 14;
    exit();
}
echo 500;
exit();
