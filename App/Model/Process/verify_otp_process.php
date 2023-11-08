<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\User;

if (isset($_POST['captcha']) && !empty($_POST['captcha'])) {
    $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $_ENV['CAPTCH_SECRET_KEY'] . '&response=' . $_POST['captcha']);
    $responseData = json_decode($verifyResponse);
    if ($responseData->success) {
        if (isset($_POST["otp"])) {
            $user = new User();
            if ($user->verify($_POST["otp"], DBConnector::getConnection()) === 12) {
                echo 20;
                exit();
            } else {
                echo 200;
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
