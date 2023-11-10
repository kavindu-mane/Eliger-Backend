<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\User;

if (isset($_SESSION["user"])) {
    $user = new User();
    echo json_encode($user->loadBankDetails(DBConnector::getConnection(), $_SESSION["user"]["id"]));
    exit();
}
echo 500;
exit();
