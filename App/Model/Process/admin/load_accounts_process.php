<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\Admin;

if (isset($_SESSION["user"])) {
    if (isset($_POST["account_type"], $_POST["status"], $_POST["offset"])) {
        if (filter_var($_POST["offset"], FILTER_VALIDATE_INT) || $_POST["offset"] == 0) {
            //pass data(function call)
            $admin = new Admin();
            echo json_encode($admin->loadAccountDetails(DBConnector::getConnection(), $_POST["account_type"], $_POST["status"] , $_POST["offset"]));
            exit();
        }
    }
}
echo 500;
exit();
