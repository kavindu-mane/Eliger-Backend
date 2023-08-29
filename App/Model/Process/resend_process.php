<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\User;

$email = $_POST["email"];
$type = $_POST["type"];

$user = new User();
echo $user->resendVerification($type , DBConnector::getConnection() , $email , "Verify your Eliger account" , "registration");