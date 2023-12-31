<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\VehicleOwner;

if (isset($_SESSION["user"])) {
    $variable_array = array("fname", "lname", "address");
    $data_array = array();
    foreach ($variable_array as $variable) {
        if (empty(strip_tags(trim($_POST[$variable])))) {
            if ($variable === "address") echo 11;
            else echo array_search($variable, $variable_array);
            exit();
        }
        // assign value to array
        $data_array[$variable] = strip_tags(trim($_POST[$variable]));
    }
    
    $owner = new VehicleOwner();
    echo $owner->updateOwner(DBConnector::getConnection(), $_SESSION["user"]["id"], $data_array);
} else {
    echo 14;
}
