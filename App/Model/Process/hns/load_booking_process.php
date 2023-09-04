<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\model\classes\Users\HelpAndSupport;

if (isset($_POST["booking_status"])) {

    //pass data(function call)
    $hns = new HelpAndSupport();
    echo json_encode($hns->  loadManageBooking(DBConnector::getConnection() , $_POST["booking_status"]));
    exit();
}