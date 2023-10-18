<?php

namespace EligerBackend\Model\Classes\Others;

use Exception;
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
        $query = "insert into vehicle(Owner_Id, Driver_Id, Vehicle_type, Booking_Type, Price, Vehicle_PlateNumber, Ownership_Doc, Insurance, Passenger_amount, Rent_Location) values(?,?,?,?,?,?,?,?,?,?)";
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

    // edit vehicle function
    public function editVehicle($connection, $data)
    {
        $query = "update vehicle set Driver_Id = ? , Price = ? where Vehicle_Id = ?";
        if (count($data) === 4) $query = "update vehicle set Driver_Id = ? , Price = ? , Rent_Location = ? where Vehicle_Id = ?";
        elseif (count($data) === 5) $query = "update vehicle set Driver_Id = ? , Price = ? , Rent_Location = ? , Availability = ? where Vehicle_Id = ?";

        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $data["assign-driver"]);
            $pstmt->bindValue(2, $data["price"]);
            if (count($data) > 3) $pstmt->bindValue(3, $data["nearest-city"]);
            if (count($data) === 5) $pstmt->bindValue(4, $data["availability"]);
            $pstmt->bindValue(count($data), $data["vehicle-id"]);
            $pstmt->execute();
            if ($pstmt->rowCount() === 1) {
                return 200;
            } else {
                return 500;
            }
        } catch (Exception $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    // near vehicle
    public function nearVehicles($connection)
    {
        $lat = 5.9666628;
        $long = 80.6833306;

        try {
            $query = "select Price , Vehicle_PlateNumber , Vehicle_type , (3959 * acos(cos(radians(?)) * cos(radians(Current_Lat)) * 
    cos(radians(Current_Long) - radians(?)) * sin(radians(?)) * sin(radians(Current_Lat)))) as distance from vehicle order by distance limit 2";
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $lat);
            $pstmt->bindValue(2, $long);
            $pstmt->bindValue(3, $lat);
            $pstmt->execute();
            $rs = $pstmt->fetchAll(PDO::FETCH_ASSOC);
            if ($pstmt->rowCount() > 0) {
                foreach ($rs as $res) {
                    print_r($res);
                }
            } else {
                echo "No data found.";
            }
        } catch (Exception $ex) {
            die("Error : " . $ex->getMessage());
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
