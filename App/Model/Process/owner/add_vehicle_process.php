<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Others\Vehicle;

if (isset($_SESSION["user"])) {
    // check any value is empty
    $variable_array = array("rent-type", "vehicle-type", "regno", "amount", "price", "assign-driver");
    $data_array = array();
    foreach ($variable_array as $variable) {
        if (isset($_POST[$variable])) {
            if (empty(strip_tags(trim($_POST[$variable])))) {
                echo (array_search($variable, $variable_array) + 25);
                exit();
            }
            // assign value to array
            $data_array[$variable] = strip_tags(trim($_POST[$variable]));
        } else {
            echo (array_search($variable, $variable_array) + 25);
            exit();
        }
    }
    // check if rent-type match or not given types
    if (!in_array($data_array["rent-type"], array("rent-out", "book-now"), true)) {
        echo 31;
        exit();
    }
    // check if vehicle-type match or not given types
    if (!in_array($data_array["vehicle-type"], array("car", "bike", "tuk-tuk", "van"), true)) {
        echo 32;
        exit();
    }
    // check given number is already registered or not
    if (!Vehicle::isNewVehicle($data_array["regno"], DBConnector::getConnection())) {
        echo 33;
        exit();
    }
    // validate amount
    if (!filter_var($data_array["amount"], FILTER_VALIDATE_INT)) {
        echo 34;
        exit();
    }
    // validate price
    if (!filter_var($data_array["price"], FILTER_VALIDATE_FLOAT)) {
        echo 35;
        exit();
    }
    // validate assign driver
    if (
        !filter_var($data_array["assign-driver"], FILTER_VALIDATE_INT) ||
        $data_array["assign-driver"] === "-100"
    ) {
        echo 30;
        exit();
    } elseif ($data_array["assign-driver"] === "-99") {
        // if driver not assign change driver to null
        $data_array["assign-driver"] = null;
    }
    // check nearest city is empty or not
    if ($data_array["rent-type"] === "rent-out") {
        if (isset($_POST["nearest-city"])) {
            if (empty(strip_tags(trim($_POST["nearest-city"])))) {
                echo  36;
                exit();
            }
            // assign value to array
            $data_array["nearest-city"] = strip_tags(trim($_POST["nearest-city"]));
        } else {
            echo  36;
            exit();
        }
    } else {
        $data_array["nearest-city"] = null;
    }

    // check documents
    $variable_array = array("insurance", "ownership");
    foreach ($variable_array as $variable) {
        $index = (array_search($variable, $variable_array));

        if (isset($_FILES[$variable])) {
            $img_name = $_FILES[$variable]['name'];
            $img_size = $_FILES[$variable]['size'];
            $tmp_name = $_FILES[$variable]['tmp_name'];
            $error = $_FILES[$variable]['error'];

            if ($error === 0) {
                if (
                    $img_size > 2 * 1024 * 1024
                ) {
                    echo 38 + $index * 4;
                    exit();
                } else {
                    $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                    $img_ex_lc = strtolower($img_ex);
                    $allowed_exs = array("jpg", "jpeg", "png");

                    if (in_array($img_ex_lc, $allowed_exs)) {
                        $new_img_name =  $data_array["regno"] . '.' . $img_ex_lc;
                        $img_upload_path = $_SERVER['DOCUMENT_ROOT'] . "/uploads/{$variable}_doc/" . $new_img_name;
                        move_uploaded_file($tmp_name, $img_upload_path);
                        $data_array[$variable] = "{$variable}_doc/" . $new_img_name;
                    } else {
                        echo 39 + $index * 4;
                        exit();
                    }
                }
            } else {
                echo 40 + $index * 4;
                exit();
            }
        } else {
            echo 37 + $index * 4;
            exit();
        }
    }

    $vehicle = new Vehicle(
        $data_array["vehicle-type"],
        $data_array["regno"],
        $data_array["ownership"],
        $data_array["insurance"],
        $data_array["amount"],
        $data_array["nearest-city"],
        $data_array["price"],
        $data_array["rent-type"],
        $data_array["assign-driver"],
    );
    if ($vehicle->addVehicle(DBConnector::getConnection(), $_POST["owner"])) {
        echo 200;
        exit();
    }
    echo 500;
    exit();
} else {
    echo 14;
    exit();
}
