<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Others\Vehicle;

if (isset($_SESSION["user"])) {
    $variable_array = array("price", "assign-driver");
    $data_array = array();
    foreach ($variable_array as $variable) {
        if (isset($_POST[$variable])) {
            if (empty(strip_tags(trim($_POST[$variable])))) {
                echo (array_search($variable, $variable_array) + 29);
                exit();
            }
            // assign value to array
            $data_array[$variable] = strip_tags(trim($_POST[$variable]));
        } else {
            echo (array_search($variable, $variable_array) + 29);
            exit();
        }
    }

    // validate price
    if (!filter_var($data_array["price"], FILTER_VALIDATE_FLOAT)) {
        echo 35;
        exit();
    }
    // validate assign driver
    if (!filter_var($data_array["assign-driver"], FILTER_VALIDATE_INT)) {
        echo 30;
        exit();
    } elseif ($data_array["assign-driver"] === "-99") {
        // if driver not assign change driver to null
        $data_array["assign-driver"] = null;
    }

    // check vehicle id
    if (isset($_POST["vehicle-id"])) {
        if (!filter_var($_POST["vehicle-id"], FILTER_VALIDATE_INT)) {
            echo 500;
            exit();
        }
        $data_array["vehicle-id"] = $_POST["vehicle-id"];
    } else {
        echo 500;
        exit();
    }

    // check nearest city is assigned or not
    if (isset($_POST["district"])) {
        if (empty(strip_tags(trim($_POST["district"])))) {
            echo  36;
            exit();
        }
        // assign value to array
        $data_array["district"] = strip_tags(trim($_POST["district"]));
    }

    // check availabilty
    if (isset($_POST["availability"])) {
        if (empty(strip_tags(trim($_POST["availability"])))) {
            echo  500;
            exit();
        }
        // assign value to array
        $data_array["availability"] = strip_tags(trim($_POST["availability"]));
    }

    // check availabilty
    if (isset($_POST["lat"])) {
        if (empty(strip_tags(trim($_POST["lat"])))) {
            echo  500;
            exit();
        }
        // assign value to array
        $data_array["lat"] = strip_tags(trim($_POST["lat"]));
    }

    // check availabilty
    if (isset($_POST["long"])) {
        if (empty(strip_tags(trim($_POST["long"])))) {
            echo  500;
            exit();
        }
        // assign value to array
        $data_array["long"] = strip_tags(trim($_POST["long"]));
    }

    $vehicle = new Vehicle();
    echo $vehicle->editVehicle(DBConnector::getConnection(), $data_array);
    exit();
} else {
    echo 14;
    exit();
}
echo 500;
exit();
