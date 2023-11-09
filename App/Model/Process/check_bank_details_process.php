<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\User;

if (isset($_SESSION["user"])) {
    $user =  new User();
    echo $user->bankDetailsStatus(DBConnector::getConnection());
    exit();
}
echo 14;
exit();
