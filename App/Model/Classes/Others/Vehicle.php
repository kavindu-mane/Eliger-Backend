<?php

namespace EligerBackend\Model\Classes\Others;

use PDO;
use PDOException;

class Vehicle
{
    private $vehicleType;
    private $plateNumber;
    private $status = "new";
    private $ownershipDoc;
    private $insurance;
    private $passengerAmount;
    private $rentOutLocation;
    private $price;
    private $rentType;
    private $driver;

    public function __construct()
    {
        $arguments = func_get_args();
        $numberOfArguments = func_num_args();

        if (method_exists($this, $function = '_construct' . $numberOfArguments)) {
            call_user_func_array(array($this, $function), $arguments);
        }
    }

    public function _construct9(
        $vehicleType,
        $plateNumber,
        $ownershipDoc,
        $insurance,
        $passengerAmount,
        $rentOutLocation,
        $price,
        $rentType,
        $driver
    ) {
        $this->vehicleType = $vehicleType;
        $this->plateNumber = $plateNumber;
        $this->ownershipDoc = $ownershipDoc;
        $this->insurance = $insurance;
        $this->passengerAmount = $passengerAmount;
        $this->rentOutLocation = $rentOutLocation;
        $this->price = $price;
        $this->rentType = $rentType;
        $this->driver = $driver;
    }

    public function _construct0()
    {
    }

    // check given vehicle already exist or not
    public static function isNewVehicle($Vehicle_PlateNumber, $connection)
    {
        $query = "select * from vehicle where Vehicle_PlateNumber = ?";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $Vehicle_PlateNumber);
            $pstmt->execute();
            $result = $pstmt->fetchAll(PDO::FETCH_ASSOC);
            return empty($result);
        } catch (PDOException $ex) {
            die("Error occurred : " . $ex->getMessage());
        }
    }

    // add new vehicle
    public function addVehicle($connection, $owner)
    {
        $query = "insert into vehicle(Owner_Id, Driver_Id, Vehicle_type, Booking_Type, Price, Vehicle_PlateNumber, Ownership_Doc, Insurance, Passenger_amount, Current_Location) values(?,?,?,?,?,?,?,?,?,?)";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $owner);
            $pstmt->bindValue(2, $this->driver);
            $pstmt->bindValue(3, $this->vehicleType);
            $pstmt->bindValue(4, $this->rentType);
            $pstmt->bindValue(5, $this->price);
            $pstmt->bindValue(6, strtoupper($this->plateNumber));
            $pstmt->bindValue(7, $this->ownershipDoc);
            $pstmt->bindValue(8, $this->insurance);
            $pstmt->bindValue(9, $this->passengerAmount);
            $pstmt->bindValue(10, $this->rentOutLocation);
            $pstmt->execute();
            return $pstmt->rowCount() === 1;
        } catch (PDOException $ex) {
            die("Error occurred : " . $ex->getMessage());
        }
    }

    // getters
    public function getVehicleType()
    {
        return $this->vehicleType;
    }
    public function getPlateNumber()
    {
        return $this->plateNumber;
    }
    public function getOwnershipDoc()
    {
        return $this->ownershipDoc;
    }
    public function getInsurance()
    {
        return $this->insurance;
    }
    public function getPassengerAmount()
    {
        return $this->passengerAmount;
    }
    public function getRentOutLocation()
    {
        return $this->rentOutLocation;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function getPrice()
    {
        return $this->price;
    }
    public function getRentType()
    {
        return $this->rentType;
    }
    public function getDriver()
    {
        return $this->driver;
    }
}
