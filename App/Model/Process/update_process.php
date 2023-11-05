<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\User;

if (isset($_SESSION["user"])) {
    $data_array = array();
    if (isset($_POST["update_email"] , $_POST["email"] , $_POST["password"])) {
        // check email value is empty
        if (empty(strip_tags(trim($_POST["email"])))) {
            echo 3;
            exit();
        }

        // check password value is empty
        if (empty(strip_tags(trim($_POST["password"])))) {
            echo 4;
            exit();
        }

        // assign value to array
        $data_array["email"] = strip_tags(trim($_POST["email"]));
        $data_array["password"] = strip_tags(trim($_POST["password"]));

        // validate email
        if (!filter_var($data_array["email"], FILTER_VALIDATE_EMAIL)) {
            echo 7;
            exit();
        }
        // check email already registered or not
        if (!User::isNewUser($data_array["email"], DBConnector::getConnection())) {
            echo 10;
            exit();
        }
        $user = new User($_SESSION["user"]["id"], $data_array["password"]);
        // check password is correct or not
        if ($user->login(DBConnector::getConnection(), false) === 13) {
            echo 18;
            exit();
        }
        echo $user->update(DBConnector::getConnection(), "Email", $data_array["email"]);
        exit();
    } elseif (isset($_POST["update_password"])) {
        // check password value is empty
        if (empty(strip_tags(trim($_POST["password"])))) {
            echo 4;
            exit();
        }

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

        // assign value to array
        $data_array["password"] = strip_tags(trim($_POST["password"]));
        $data_array["newPassword"] = strip_tags(trim($_POST["newPassword"]));
        $data_array["confirmNewPassword"] = strip_tags(trim($_POST["confirmNewPassword"]));

        // password length check
        if (strlen($data_array["newPassword"]) < 8 || strlen($data_array["newPassword"]) > 32) {
            echo 8;
            exit();
        }

        // check if password and confirm password is same
        if ($data_array["newPassword"] !== $data_array["confirmNewPassword"]) {
            echo 9;
            exit();
        }

        $user = new User($_SESSION["user"]["id"], $data_array["password"]);
        // check password is correct or not
        if ($user->login(DBConnector::getConnection(), false) === 13) {
            echo 18;
            exit();
        }
        echo $user->update(DBConnector::getConnection(), "Password", password_hash($data_array["newPassword"], PASSWORD_BCRYPT));
        exit();
    } else {
        echo 500;
        exit();
    }
} else {
    echo 14;
    exit();
}
