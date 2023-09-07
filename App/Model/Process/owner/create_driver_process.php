<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\Driver;
use EligerBackend\Model\Classes\Users\User;

if (isset($_SESSION["user"])) {
    // check any value is empty
    $variable_array = array("fname", "lname", "phone", "email", "password", "confPassword", "percentage", "address");
    $data_array = array();
    foreach ($variable_array as $variable) {
        if (empty(strip_tags(trim($_POST[$variable])))) {
            if ($variable === "percentage") echo 19;
            elseif ($variable === "address") echo 11;
            else echo array_search($variable, $variable_array);
            exit();
        }
        // assign value to array
        $data_array[$variable] = strip_tags(trim($_POST[$variable]));
    }

    // validate phone number
    if (!preg_match('/^[+]{0,1}[0-9]{10,11}$/', $data_array["phone"])) {
        echo 6;
        exit();
    }

    // validate email
    if (!filter_var($data_array["email"], FILTER_VALIDATE_EMAIL)) {
        echo 7;
        exit();
    }

    // validate percentage
    if (!filter_var($data_array["percentage"], FILTER_VALIDATE_FLOAT)) {
        echo 20;
        exit();
    }

    // password length check
    if (strlen($data_array["password"]) < 8 || strlen($data_array["password"]) > 32) {
        echo 8;
        exit();
    }

    // check if password and confirm password is same
    if ($data_array["confPassword"] !== $data_array["password"]) {
        echo 9;
        exit();
    }

    // check email already registered or not
    if (!User::isNewUser($data_array["email"], DBConnector::getConnection())) {
        echo 10;
        exit();
    }

    // check licence
    if (isset($_FILES['licence'])) {
        $img_name = $_FILES['licence']['name'];
        $img_size = $_FILES['licence']['size'];
        $tmp_name = $_FILES['licence']['tmp_name'];
        $error = $_FILES['licence']['error'];

        if ($error === 0) {
            if (
                $img_size > 2 * 1024 * 1024
            ) {
                echo 22;
                exit();
            } else {
                $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                $img_ex_lc = strtolower($img_ex);
                $allowed_exs = array("jpg", "jpeg", "png");

                if (in_array($img_ex_lc, $allowed_exs)) {
                    $new_img_name =  $data_array["email"] . '.' . $img_ex_lc;
                    $img_upload_path = $_SERVER['DOCUMENT_ROOT'] . "/uploads/licence_doc/" . $new_img_name;
                    move_uploaded_file($tmp_name, $img_upload_path);
                    $driver = new Driver(
                        $data_array["email"],
                        $data_array["password"],
                        "driver",
                        $data_array["phone"],
                        $data_array["fname"],
                        $data_array["lname"],
                        $data_array["percentage"],
                        "licence_doc/" . $new_img_name,
                        $data_array["address"]
                    );
                    if ($driver->register(DBConnector::getConnection())) {
                        echo 200;
                        exit();
                    }
                    echo 500;
                    exit();
                } else {
                    echo 23;
                    exit();
                }
            }
        } else {
            echo 24;
            exit();
        }
    } else {
        echo 21;
        exit();
    }
} else {
    echo 14;
    exit();
}
