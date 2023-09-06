<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\Customer;

if (isset($_SESSION["user"])) {
    $variable_array = array("fname", "lname", "phone");
    $data_array = array();
    foreach ($variable_array as $variable) {
        if (empty(strip_tags(trim($_POST[$variable])))) {
            echo array_search($variable, $variable_array);
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
    $customer = new Customer();
    echo $customer->updateCustomer(DBConnector::getConnection(), $_SESSION["user"]["id"] , $data_array);
} else {
    echo 14;
}
