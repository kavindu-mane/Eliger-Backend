<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\User;

if (isset($_POST["email"])) {
    if (empty(strip_tags(trim($_POST["email"])))) {
        echo 3;
        exit();
    }
    $email = strip_tags(trim($_POST["email"]));

    // validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 7;
        exit();
    }
    // check email already registered or not
    if (User::isNewUser($email, DBConnector::getConnection())) {
        echo 19;
        exit();
    }
    $user = new User();
    echo $user->resendVerification("reset_pass",  DBConnector::getConnection(), $email, "Password reset of your Eliger account", "reset_password");
    exit();
}
echo 3;
exit();
