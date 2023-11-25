<?php

/** @ Error codes
 *
 * 0 - First name is empty
 * 1 - Last name is empty
 * 2 - Phone number is empty
 * 3 - Email is empty
 * 4 - Password is empty
 * 5 - Confirm password is empty
 * 6 - Phone numbers is invalid
 * 7 - Email is invalid
 * 8 - Password must contain between 8-16 characters
 * 9 - Password and confirm password is mismatched
 * 10 - This email is already registered
 * 11 - Address is empty
 */

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\Customer;
use EligerBackend\Model\Classes\Users\User;
use EligerBackend\Model\Classes\Users\VehicleOwner;

if (isset($_POST['captcha']) && !empty($_POST['captcha'])) {
    $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $_ENV['CAPTCHA_SECRET_KEY'] . '&response=' . $_POST['captcha']);
    $responseData = json_decode($verifyResponse);
    if ($responseData->success) {

        if (isset($_POST["account-type"])) {
            // check any value is empty
            $variable_array = array("fname", "lname", "phone", "email", "password", "confirmPassword");
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
            if (!preg_match('/^94{1}[0-9]{9}$/', $data_array["phone"])) {
                echo 6;
                exit();
            }

            // validate email
            if (!filter_var($data_array["email"], FILTER_VALIDATE_EMAIL)) {
                echo 7;
                exit();
            }

            // password length check
            if (strlen($data_array["password"]) < 8 || strlen($data_array["password"]) > 32) {
                echo 8;
                exit();
            }

            // check if password and confirm password is same
            if ($data_array["confirmPassword"] !== $data_array["password"]) {
                echo 9;
                exit();
            }

            // check email already registered or not
            if (!User::isNewUser($data_array["email"], DBConnector::getConnection())) {
                echo 10;
                exit();
            }

            if ($_POST["account-type"] === "customer") {
                $customer = new Customer($data_array["email"], $data_array["password"], "customer", $data_array["phone"], $data_array["fname"], $data_array["lname"]);
                if ($customer->register(DBConnector::getConnection())) {
                    echo 200;
                    exit();
                }
            } elseif ($_POST["account-type"] === "vehicle_owner") {
                // check address field
                if (empty(strip_tags(trim($_POST["address"])))) {
                    echo 11;
                    exit();
                }
                // add address to array
                $data_array["address"] = strip_tags(trim($_POST["address"]));
                $owner = new VehicleOwner($data_array["email"], $data_array["password"], "vehicle_owner", $data_array["phone"], $data_array["fname"], $data_array["lname"], $data_array["address"]);
                if ($owner->register(DBConnector::getConnection())) {
                    echo 200;
                    exit();
                }
            } else {
                echo 500;
                exit();
            }
        } else {
            echo 500;
            exit();
        }
    }
}
echo 49;
exit();
