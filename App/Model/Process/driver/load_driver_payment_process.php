<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Others\Payment;

if (isset($_SESSION["user"])) {
    if (isset($_POST["offset"])) {
        if (
            filter_var($_POST["offset"], FILTER_VALIDATE_INT)
        ) {
            $query = "SELECT Driver_Id from driver_details where Email = ?";
            $pstmt = DBConnector::getConnection()->prepare($query);
            $pstmt->bindValue(1, $_SESSION["user"]["id"]);
            $pstmt->execute();
            $rs = $pstmt->fetch(PDO::FETCH_ASSOC);
            if ($pstmt->rowCount() > 0) {
                $payment = new Payment();
                echo $payment->loadDriverPayments(DBConnector::getConnection(), $rs["Driver_Id"], $_POST["offset"]);
                exit();
            }
        }
    }
}
echo 500;
exit();
