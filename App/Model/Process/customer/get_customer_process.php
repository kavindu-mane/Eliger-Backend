<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\Customer;

if (isset($_SESSION["user"])) {
    $customer = new Customer();
    echo $customer->loadCustomer(DBConnector::getConnection(), $_SESSION["user"]["id"]);
} else {
    echo 14;
}
