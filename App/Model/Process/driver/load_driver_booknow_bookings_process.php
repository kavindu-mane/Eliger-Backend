<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\Driver;

if (isset($_SESSION["user"])) {
    // get customer id using session stored user email; 
    $query = "SELECT Driver_Id from driver_details where email = ?";
    $pstmt = DBConnector::getConnection()->prepare($query);
    $pstmt->bindValue(1, $_SESSION["user"]["id"]);
    $pstmt->execute();
    $rs = $pstmt->fetch(PDO::FETCH_ASSOC);
    if ($pstmt->rowCount() > 0) {
        $driver = new Driver();
        echo $driver->loadBookNowBooking(DBConnector::getConnection(), $rs["Driver_Id"]);
        exit();
    }
}
echo 500;
exit();
