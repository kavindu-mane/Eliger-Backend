<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\Admin;

if (isset($_SESSION["user"]) && ($_SESSION["user"]["role"] === "admin" || $_SESSION["user"]["role"] === "hands")) {
    //pass data(function call)
    $admin = new Admin();
    echo json_encode($admin->loadVehicleStats(DBConnector::getConnection()));
    exit();
}
echo 500;
exit();
