<?php

/** @ Error codes
 *
 * 0 - empty first name
 * 1 - empty last name
 * 2 - empty phone
 * 3 - empty email
 * 4 - empty password
 * 5 - empty confirm password
 * 6 - wrong phone
 * 7 - wrong email
 * 8 - password less than 6 characters
 * 9 - password and confirm password mismatch
 * 10 - email already registered
 */

if (isset($_POST["account-type"])) {
    // check any value is empty
    $variable_array = array("fname", "lname", "phone", "email", "password", "confirmPassword");
    foreach ($variable_array as $variable) {
        if (empty(strip_tags(trim($_POST[$variable])))) {
            echo array_search($variable, $variable_array);
            exit();
        }
    }

    // validate phone number
    if(!preg_match('/^[+]{0,1}[0-9]{10,11}$/',$_POST["phone"])){
        echo 6;
        exit();
    }

    // validate email
    if (!filter_var(strip_tags(trim($_POST["email"])), FILTER_VALIDATE_EMAIL)) {
        echo 7;
        exit();
    }

    // password length check
    if (strlen(strip_tags(trim($_POST["password"]))) < 8 ||strlen(strip_tags(trim($_POST["password"]))) > 16) {
        echo 8;
        exit();
    }

    // check if password and confirm password is same
    if ($_POST["confirmPassword"] !== $_POST["password"]) {
        echo 9;
        exit();
    }

    echo "success";
} else {
    echo "error";
}
