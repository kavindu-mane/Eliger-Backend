<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\User;

if (isset($_SESSION["user"])) {
    if (isset($_POST["otp"])) {
        if (filter_var($_POST["otp"], FILTER_VALIDATE_INT)) {
            $user = new User();
            echo $user->verifyPhoneChangeOTP(DBConnector::getConnection(), $_SESSION["user"]["role"], $_POST["otp"], $_SESSION["user"]["id"]);
            exit();
        }
    }
} else {
    echo 14;
    exit();
}
echo 500;
exit();
