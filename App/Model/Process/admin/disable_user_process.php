<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\Admin;

if (isset($_POST["status"], $_POST["email"])) {
    $admin = new Admin();
    echo $admin->disableUser(DBConnector::getConnection(), $_POST["status"], $_POST["email"]);
    exit();
} else {
    echo 500;
    exit();
}
