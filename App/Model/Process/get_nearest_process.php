<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;

if (isset($_POST["lat"], $_POST["long"], $_POST["type"])) {
    if (!empty($_POST["lat"]) && !empty($_POST["long"]) && !empty($_POST["type"])) {
        $connection = DBConnector::getConnection();
        $lat = $_POST["lat"];
        $long = $_POST["long"];
        $type = $_POST["type"];

        try {
            $query = "select Price , Vehicle_PlateNumber , Vehicle_type , Current_Lat , Current_Long, round((ACOS((SIN(RADIANS(Current_Lat)) * SIN(RADIANS(?))) + (COS(RADIANS(Current_Lat)) 
    * COS(RADIANS(?))) * (COS(RADIANS(?) - RADIANS(Current_Long)))) * 6371) , 2) as distance from vehicle where Status = 'verified' and Booking_Type = 'book-now' 
    and Availability = 'available' and Vehicle_type = ? having distance order by distance limit 10";
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $lat);
            $pstmt->bindValue(2, $lat);
            $pstmt->bindValue(3, $long);
            $pstmt->bindValue(4, strtolower($type));
            $pstmt->execute();
            $rs = $pstmt->fetchAll(PDO::FETCH_OBJ);
            if ($pstmt->rowCount() > 0) {
                echo json_encode($rs);
            } else {
                echo 45;
            }
        } catch (Exception $ex) {
            die("Error : " . $ex->getMessage());
        }
    } else {
        echo "500";
    }
} else {
    echo "500";
}
// https://developers.google.com/maps/documentation/javascript/examples/distance-matrix