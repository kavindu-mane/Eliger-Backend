<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\User;

if (isset($_POST["newPassword"], $_POST["confirmNewPassword"], $_POST["code"])) {
    // check new password value is empty
    if (empty(strip_tags(trim($_POST["newPassword"])))) {
        echo 17;
        exit();
    }

    // check confirm new password value is empty
    if (empty(strip_tags(trim($_POST["confirmNewPassword"])))) {
        echo 5;
        exit();
    }

    // check confirm new password value is empty
    if (empty(strip_tags(trim($_POST["code"])))) {
        echo 500;
        exit();
    }
    $newPassword = strip_tags(trim($_POST["newPassword"]));
    $confirmNewPassword = strip_tags(trim($_POST["confirmNewPassword"]));
    $code = strip_tags(trim($_POST["code"]));

    // password length check
    if (strlen($newPassword) < 8 || strlen($newPassword) > 32) {
        echo 8;
        exit();
    }

    // check if password and confirm password is same
    if ($newPassword !== $confirmNewPassword) {
        echo 9;
        exit();
    }
    $user = new User();
    echo $user->resetPassword(DBConnector::getConnection(), password_hash($newPassword, PASSWORD_BCRYPT), $code);
    exit();
}
echo 500;
exit();
