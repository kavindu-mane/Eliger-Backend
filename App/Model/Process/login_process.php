<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\User;

// start session
session_start();

if (isset($_POST["email"], $_POST["password"])) {
    if (empty(strip_tags(trim($_POST["email"])))) {
        echo 3;
        exit();
    }

    if (empty(strip_tags(trim($_POST["password"])))) {
        echo 7;
        exit();
    }

    $email = strip_tags(trim($_POST["email"]));
    $password = strip_tags(trim($_POST["password"]));
    $remember = isset($_POST['remember']) ? true : false;

    $user = new User($email, $password);
    echo $user->login(DBConnector::getConnection() , $remember);
    exit();
}
