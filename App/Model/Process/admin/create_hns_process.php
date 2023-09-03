<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\model\classes\Users\Admin;
use EligerBackend\Model\Classes\Users\User;

if (isset($_POST["name"], $_POST["email"], $_POST["initial_password"], $_POST["confirm_initial_password"])) {
    // check any value is empty
    $variable_array = array("name", "email", "initial_password", "confirm_initial_password");
    $data_array = array();
    foreach ($variable_array as $variable) {
        if (empty(strip_tags(trim($_POST[$variable])))) {
            echo 15;
            exit();
        }
        // assign value to array
        $data_array[$variable] = strip_tags(trim($_POST[$variable]));
    }

    // validate email
    if (!filter_var($data_array["email"], FILTER_VALIDATE_EMAIL)) {
        echo 7;
        exit();
    }

    // password length check
    if (strlen($data_array["initial_password"]) < 8 || strlen($data_array["initial_password"]) > 32) {
        echo 8;
        exit();
    }

    // check if password and confirm password is same
    if ($data_array["confirm_initial_password"] !== $data_array["initial_password"]) {
        echo 9;
        exit();
    }

    if (!User::isNewUser($data_array["email"], DBConnector::getConnection())) {
        echo 10;
        exit();
    }

    //pass data(function call)
    $admin = new Admin();
    echo $admin->createHelpAccount(DBConnector::getConnection() , $data_array["name"], $data_array["email"], $data_array["initial_password"]);
    exit();
} else {
    echo 500;
    exit();
}
