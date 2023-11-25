<?php

namespace EligerBackend\Model\Classes\Others;

use EligerBackend\Model\Classes\Connectors\WhatsAppConnector;
use PDO;
use PDOException;

class Booking
{
    private $customerId = null;
    private $ownerId = null;
    private $driverId = null;
    private $vehicleId = null;
    private $bookingType = null;
    private $origin = null;
    private $destination = null;
    private $startDate = null;
    private $endDate = null;

    public function __construct()
    {
    }

    public function sendMessageToOwner($connection , $ownerId){
        try {
            $query = "SELECT * FROM vehicle_owner_details WHERE Owner_Id = ?";
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $ownerId);
            $pstmt->execute();
            if($pstmt->rowCount() === 1){
                $result = $pstmt->fetch(PDO::FETCH_ASSOC);
                $msg = "Hello, {$result['Owner_firstname']} {$result['Owner_lastname']}\n\n You have a new vehicle request from a customer";
                WhatsAppConnector::sendWhatsAppMessage($result["Owner_Tel"], $msg);
            }
        } catch (PDOException $ex) {
            die("Error occurred : " . $ex->getMessage());
        }
    }

    public function addBooking($connection)
    {
        $query = "INSERT INTO booking(Customer_Id, Owner_Id, Driver_Id, Vehicle_Id, Origin_Place, Destination_Place, Journey_Starting_Date, Journey_Ending_Date, Booking_Time, Booking_Type) values(?,?,?,?,?,?,?,?,NOW(),?)";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $this->customerId);
            $pstmt->bindValue(2, $this->ownerId);
            $pstmt->bindValue(3, $this->driverId);
            $pstmt->bindValue(4, $this->vehicleId);
            $pstmt->bindValue(5, $this->origin);
            $pstmt->bindValue(6, $this->destination);
            $pstmt->bindValue(7, $this->startDate);
            $pstmt->bindValue(8, $this->endDate);
            $pstmt->bindValue(9, $this->bookingType);
            $pstmt->execute();
            if($pstmt->rowCount() === 1){
                if($this->bookingType === "rent-out"){
                    $this->sendMessageToOwner($connection,$this->ownerId);
                }
                return $pstmt->rowCount() === 1;
            }
            
        } catch (PDOException $ex) {
            die("Error occurred : " . $ex->getMessage());
        }
    }

    public function changeBookingStatus($connection, $bookingId, $status)
    {
        $query = "UPDATE booking SET Booking_Status = ? Where Booking_Id = ?";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $status);
            $pstmt->bindValue(2, $bookingId);
            $pstmt->execute();
            return $pstmt->rowCount() === 1;
        } catch (PDOException $ex) {
            die("Error occurred : " . $ex->getMessage());
        }
    }

    // getters and setters
    public function getCustomerId()
    {
        return $this->customerId;
    }

    public function setCustomerId($customerId): void
    {
        $this->customerId = $customerId;
    }

    public function getOwnerId()
    {
        return $this->ownerId;
    }

    public function setOwnerId($ownerId): void
    {
        $this->ownerId = $ownerId;
    }

    public function getDriverId()
    {
        return $this->driverId;
    }

    public function setDriverId($driverId): void
    {
        $this->driverId = $driverId;
    }

    public function getVehicleId()
    {
        return $this->vehicleId;
    }

    public function setVehicleId($vehicleId): void
    {
        $this->vehicleId = $vehicleId;
    }

    public function getBookingType()
    {
        return $this->bookingType;
    }

    public function setBookingType($bookingType): void
    {
        $this->bookingType = $bookingType;
    }

    public function getOrigin()
    {
        return $this->origin;
    }

    public function setOrigin($origin): void
    {
        $this->origin = $origin;
    }

    public function getDestination()
    {
        return $this->destination;
    }

    public function setDestination($destination): void
    {
        $this->destination = $destination;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function setStartDate($startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function setEndDate($endDate): void
    {
        $this->endDate = $endDate;
    }
}
