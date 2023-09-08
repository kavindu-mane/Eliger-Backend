<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\Admin;

if (isset($_POST["status"], $_POST["id"])) {
    $admin = new Admin();
    echo $admin->reviewDocument(DBConnector::getConnection(), $_POST["status"], $_POST["id"]);
    exit();
} else {
    echo 500;
    exit();
}
