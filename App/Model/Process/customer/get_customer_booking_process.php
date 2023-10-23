<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Users\Customer;

if (isset($_SESSION["user"])) {
    // get customer id using session stored user email; 
    if (isset($_POST["offset"])) {
        if (filter_var($_POST["offset"], FILTER_VALIDATE_INT) || $_POST["offset"] == 0) {
            $query = "SELECT Customer_Id from customer where email = ?";
            $pstmt = DBConnector::getConnection()->prepare($query);
            $pstmt->bindValue(1, $_SESSION["user"]["id"]);
            $pstmt->execute();
            $rs = $pstmt->fetch(PDO::FETCH_ASSOC);
            if ($pstmt->rowCount() > 0) {
                $customer = new Customer();
                echo $customer->loadBooking(DBConnector::getConnection(), $rs["Customer_Id"], $_POST["offset"]);
                exit();
            }
        }
    }
} else {
    echo 14;
    exit();
}
echo 500;
exit();
