<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\Admin;

if (isset($_SESSION["user"]) && $_SESSION["user"]["role"] === "admin") {
    if (isset($_POST["status"], $_POST["id"], $_POST["type"])) {
        $admin = new Admin();
        echo $admin->reviewDocument(DBConnector::getConnection(), $_POST["type"], $_POST["status"], $_POST["id"]);
        exit();
    }
}
echo 500;
exit();
