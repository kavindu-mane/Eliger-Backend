<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\model\classes\Users\Driver;
use EligerBackend\Model\Classes\Users\User;

if (isset($_POST["fname"], $_POST["lname"],$_POST["email"],$_POST["contactno"],$_POST["password"], $_POST["address"])) {
    // check any value is empty
    $variable_array = array("fname", "lname", "email", "contactno", "password", "address");
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

  

    if (!User::isNewUser($data_array["email"], DBConnector::getConnection())) {
        echo 10;
        exit();
    }

    //pass data(function call)
    $driver = new Driver($email, $password, $type, $phone, $firstName, $lastName , $address);
    echo $admin->CreateDriverAccount(DBConnector::getConnection() , $data_array["fname"],$data_array["lname"], $data_array["email"],  $data_array["contactno"], $data_array["address"], $data_array["password"]);
    exit();
} else {
    echo 500;
    exit();
}
