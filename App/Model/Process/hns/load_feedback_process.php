<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\HelpAndSupport;

if (isset($_SESSION["user"])) {
    if (isset($_POST["offset"])) {
        if (filter_var($_POST["offset"], FILTER_VALIDATE_INT) || $_POST["offset"] == 0) {
            $hns = new HelpAndSupport();
            echo json_encode($hns->loadManageFeedback(DBConnector::getConnection(), $_POST["offset"]));
            exit();
        }
    }
}
echo 500;
exit();
