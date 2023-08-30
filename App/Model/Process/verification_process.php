<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\User;

if (isset($_POST["code"])) {
    $user = new User();
    echo $user->verify($_POST["code"], DBConnector::getConnection());
} else {
    echo 500;
}
