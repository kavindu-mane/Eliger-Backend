<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\Admin;

if (isset($_SESSION["user"]) && $_SESSION["user"]["role"] === "admin") {
    //pass data(function call)
    $admin = new Admin();
    echo json_encode($admin->loadRevenueStats(DBConnector::getConnection()));
    exit();
}
echo 500;
exit();
