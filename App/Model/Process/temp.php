<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;

$lat = 5.9666628;
$long = 80.6833306;

$connection = DBConnector::getConnection();

try {
    $query = "select Price , Vehicle_PlateNumber , Vehicle_type , round((ACOS((SIN(RADIANS(Current_Lat)) * SIN(RADIANS(?))) + (COS(RADIANS(Current_Lat)) 
    * COS(RADIANS(?))) * (COS(RADIANS(?) - RADIANS(Current_Long)))) * 6371) , 2) as distance from vehicle having distance order by distance limit 3";
    $pstmt = $connection->prepare($query);
    $pstmt->bindValue(1, $lat);
    $pstmt->bindValue(2, $lat);
    $pstmt->bindValue(3, $long);
    $pstmt->execute();
    $rs = $pstmt->fetchAll(PDO::FETCH_ASSOC);
    if ($pstmt->rowCount() > 0) {
        foreach ($rs as $res) {
            print_r($res);
            echo "<br/>";
        }
    } else {
        echo "No data found.";
    }
} catch (Exception $ex) {
    die("Error : " . $ex->getMessage());
}
// https://developers.google.com/maps/documentation/javascript/examples/distance-matrix