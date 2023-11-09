<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\User;

if (isset($_SESSION["user"])) {
    // check any value is empty
    $variable_array = array("beneficiary", "bank", "branch", "acc_number");
    $banks = array(
        "People's Bank", "Bank of Ceylon", "Hatton National Bank", "Sampath Bank", "Commercial Bank", "NDB", "NSB",
    );
    $data_array = array("email" => $_SESSION["user"]["id"]);
    foreach ($variable_array as $variable) {
        if (isset($_POST[$variable])) {
            if (empty(strip_tags(trim($_POST[$variable])))) {
                echo (array_search($variable, $variable_array) + 50);
                exit();
            }
            // assign value to array
            $data_array[$variable] = strip_tags(trim($_POST[$variable]));
        } else {
            echo (array_search($variable, $variable_array) + 50);
            exit();
        }
    }

    if (!in_array($data_array["bank"], $banks)) {
        echo 51;
        exit();
    }

    if (!filter_var($data_array["branch"], FILTER_VALIDATE_INT)) {
        echo 52;
        exit();
    }

    if (!filter_var($data_array["acc_number"], FILTER_VALIDATE_INT)) {
        echo 53;
        exit();
    }

    // check statement
    if (isset($_FILES['statement'])) {
        $img_name = $_FILES['statement']['name'];
        $img_size = $_FILES['statement']['size'];
        $tmp_name = $_FILES['statement']['tmp_name'];
        $error = $_FILES['statement']['error'];

        if ($error === 0) {
            if (
                $img_size > 2 * 1024 * 1024
            ) {
                echo 55;
                exit();
            } else {
                $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                $img_ex_lc = strtolower($img_ex);
                $allowed_exs = array("jpg", "jpeg", "png");

                if (in_array($img_ex_lc, $allowed_exs)) {
                    $new_img_name =  $data_array["email"] . '.' . $img_ex_lc;
                    $img_upload_path = $_SERVER['DOCUMENT_ROOT'] . "/uploads/statement_doc/" . $new_img_name;
                    move_uploaded_file($tmp_name, $img_upload_path);
                    $data_array["statement"] = "statement_doc/" . $new_img_name;

                    $user = new User();
                    if ($user->submitBankDetails(DBConnector::getConnection() , $data_array)) {
                        echo 200;
                        exit();
                    }
                    echo 500;
                    exit();
                } else {
                    echo 56;
                    exit();
                }
            }
        } else {
            echo 57;
            exit();
        }
    } else {
        echo 54;
        exit();
    }
} else {
    echo 14;
    exit();
}
