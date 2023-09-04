<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\model\classes\Users\HelpAndSupport;

//pass data(function call)
$hns = new HelpAndSupport();
echo json_encode($hns->loadManageFeedback(DBConnector::getConnection()));
exit();
