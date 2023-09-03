<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\Admin;

if (isset($_POST["account_type"], $_POST["status"])) {

    //pass data(function call)
    $admin = new Admin();
    echo json_encode($admin-> loadAccountDetails(DBConnector::getConnection() , $_POST["account_type"], $_POST["status"]));
    exit();
}
