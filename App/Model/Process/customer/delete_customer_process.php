<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\Customer;
use EligerBackend\Model\Classes\Users\User;

if (isset($_POST['captcha']) && !empty($_POST['captcha'])) {
    $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $_ENV['CAPTCH_SECRET_KEY'] . '&response=' . $_POST['captcha']);
    $responseData = json_decode($verifyResponse);
    if ($responseData->success) {
        if (isset($_POST["email"], $_POST["password"])) {
            if (empty(strip_tags(trim($_POST["email"])))) {
                echo 3;
                exit();
            }

            if (empty(strip_tags(trim($_POST["password"])))) {
                echo 4;
                exit();
            }

            $email = strip_tags(trim($_POST["email"]));
            $password = strip_tags(trim($_POST["password"]));

            $user = new User($email, $password);
            // check password is correct or not
            if ($user->login(DBConnector::getConnection(), false) === 13) {
                echo 18;
                exit();
            }
            $customer = new Customer();
            echo $customer->deleteCustomer(DBConnector::getConnection(), $email);
            exit();
        }
    }
}
echo 49;
exit();
