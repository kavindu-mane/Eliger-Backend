<?php

use EligerBackend\Model\Classes\Connectors\DBConnector;
use EligerBackend\Model\Classes\Others\Booking;

if (isset($_SESSION["user"])) {
    if ($_SESSION["user"]["role"] === "customer") {
        if (isset($_POST["owner"], $_POST["driver"], $_POST["vehicle"], $_POST["type"])) {
            if (
                filter_var($_POST["owner"], FILTER_VALIDATE_INT) &&
                (filter_var($_POST["driver"], FILTER_VALIDATE_INT) ||
                    $_POST["driver"] === null) &&
                filter_var($_POST["vehicle"], FILTER_VALIDATE_INT)
            ) {
                if (isset($_POST["type"])) {
                    // get customer id using session stored user email;
                    $query = "SELECT Customer_Id from customer where email = ?";
                    try {
                        $pstmt = DBConnector::getConnection()->prepare($query);
                        $pstmt->bindValue(1, $_SESSION["user"]["id"]);
                        $pstmt->execute();
                        $rs = $pstmt->fetch(PDO::FETCH_ASSOC);
                        if ($pstmt->rowCount() > 0) {
                            $booking = new Booking();
                            $booking->setCustomerId($rs["Customer_Id"]);
                            $booking->setOwnerId($_POST["owner"]);
                            $booking->setDriverId($_POST["driver"]);
                            $booking->setVehicleId($_POST["vehicle"]);

                            if ($_POST["type"] === "rent-out") {
                                if (isset($_POST["start"], $_POST["end"])) {
                                    $start = explode("-", $_POST["start"]);
                                    $end = explode("-", $_POST["end"]);
                                    if (checkdate($start[1], $start[2], $start[0]) && checkdate($end[1], $end[2], $end[0])) {
                                        $booking->setStartDate($_POST["start"]);
                                        $booking->setEndDate($_POST["end"]);
                                    }
                                }
                            } elseif ($_POST["type"] === "book-now") {
                                if (isset($_POST["start"], $_POST["end"])) {
                                    $start = strip_tags(trim($_POST["start"]));
                                    $end = strip_tags(trim($_POST["end"]));
                                    $booking->setOrigin($start);
                                    $booking->setDestination($end);
                                }
                            } else {
                                echo 500;
                                exit();
                            }

                            // call function
                            $booking->setBookingType($_POST["type"]);
                            if ($booking->addBooking(DBConnector::getConnection())) {
                                echo 200;
                                exit();
                            }
                        }
                    } catch (PDOException $ex) {
                        die("Error occurred : " . $ex->getMessage());
                    }
                }
            }
        }
    }
} else {
    echo 14;
    exit();
}

echo 500;
exit();
