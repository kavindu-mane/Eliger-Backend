<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\model\classes\Users\HelpAndSupport;

if (isset($_POST["vehicle_status"])) {

    //pass data(function call)
    $hns = new HelpAndSupport();
    echo json_encode($hns->  loadManageVehicles(DBConnector::getConnection() , $_POST["vehicle_status"]));
    exit();
}