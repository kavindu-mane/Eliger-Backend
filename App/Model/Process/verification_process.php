<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\User;

if (isset($POST_["code"])) {
    $user = new User();
    return $user->verify($POST_["code"], DBConnector::getConnection());
} else {
    echo 500;
}
