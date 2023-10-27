<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\VehicleOwner;

if (isset($_SESSION["user"])) {
    // get customer id using session stored user email; 
    $query = "SELECT Owner_Id from vehicle_owner where email = ?";
    $pstmt = DBConnector::getConnection()->prepare($query);
    $pstmt->bindValue(1, $_SESSION["user"]["id"]);
    $pstmt->execute();
    $rs = $pstmt->fetch(PDO::FETCH_ASSOC);
    if ($pstmt->rowCount() > 0) {
        $owner = new VehicleOwner();
        echo $owner->loadDriverIncomes(DBConnector::getConnection(), $rs["Owner_Id"]);
        exit();
    }
}
echo 500;
exit();
