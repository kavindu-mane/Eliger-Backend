<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Others\Payment;

if (isset($_SESSION["user"])) {
    // if (isset($_POST["offset"])) {
    //     if (filter_var($_POST["offset"], FILTER_VALIDATE_INT) || $_POST["offset"] == 0) {
            // get customer id using session stored user email; 
            $query = "SELECT Owner_Id from vehicle_owner where email = ?";
            $pstmt = DBConnector::getConnection()->prepare($query);
            $pstmt->bindValue(1, $_SESSION["user"]["id"]);
            $pstmt->execute();
            $rs = $pstmt->fetch(PDO::FETCH_ASSOC);
            if ($pstmt->rowCount() > 0) {
                $payment = new Payment();
                echo $payment->loadOwnerPayments(DBConnector::getConnection(), $rs["Owner_Id"], 0);
                exit();
            }
    //     }
    // }
}
echo 500;
exit();
