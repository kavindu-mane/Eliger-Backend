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
    private $location;
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
        $location,
        $price,
        $rentType,
        $driver
    ) {
        $this->vehicleType = $vehicleType;
        $this->plateNumber = $plateNumber;
        $this->ownershipDoc = $ownershipDoc;
        $this->insurance = $insurance;
        $this->passengerAmount = $passengerAmount;
        $this->location = $location;
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
        $query = "insert into vehicle(Owner_Id, Driver_Id, Vehicle_type, Booking_Type, Price, Vehicle_PlateNumber, Ownership_Doc, Insurance, Passenger_amount, District , Current_Lat , Current_Long) values(?,?,?,?,?,?,?,?,?,?,?,?)";
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
            $pstmt->bindValue(10, $this->location[0]);
            $pstmt->bindValue(11, $this->location[1]);
            $pstmt->bindValue(12, $this->location[2]);
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
        if (count($data) === 6) $query = "update vehicle set Driver_Id = ? , Price = ? , District = ? , Current_Lat = ? , Current_Long = ? where Vehicle_Id = ?";
        elseif (count($data) === 7) $query = "update vehicle set Driver_Id = ? , Price = ? , District = ? , Availability = ?, Current_Lat = ? , Current_Long = ? where Vehicle_Id = ?";

        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $data["assign-driver"]);
            $pstmt->bindValue(2, $data["price"]);
            if (count($data) > 3) $pstmt->bindValue(3, $data["district"]);
            if (count($data) === 7) $pstmt->bindValue(4, $data["availability"]);
            $pstmt->bindValue(count($data) - 2, $data["lat"]);
            $pstmt->bindValue(count($data) - 1, $data["long"]);
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
    public function nearVehicles($connection, $lat, $long, $type)
    {
        try {
            $query = "SELECT Price , Vehicle_PlateNumber , Vehicle_type , Current_Lat , Current_Long, Feedback_count , Feedback_score, Owner_Id , Driver_Id , Vehicle_Id , 
            ROUND((ACOS((SIN(RADIANS(Current_Lat)) * SIN(RADIANS(?))) + (COS(RADIANS(Current_Lat)) * COS(RADIANS(?))) * (COS(RADIANS(?) - RADIANS(Current_Long)))) * 6371) , 2) as distance 
            FROM vehicle WHERE Status = 'verified' AND Booking_Type = 'book-now' 
            AND Availability = 'available' AND Vehicle_type = ? HAVING distance ORDER BY distance ASC LIMIT 10";
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $lat);
            $pstmt->bindValue(2, $lat);
            $pstmt->bindValue(3, $long);
            $pstmt->bindValue(4, strtolower($type));
            $pstmt->execute();
            $rs = $pstmt->fetchAll(PDO::FETCH_OBJ);
            if ($pstmt->rowCount() > 0) {
                return json_encode($rs);
            } else {
                return 45;
            }
        } catch (Exception $ex) {
            die("Error : " . $ex->getMessage());
        }
    }

    // vehicle available for district
    public function vehicleByDistrict(
        $connection,
        $district,
        $type,
        $start_date,
        $end_date,
        $driver
    ) {
        try {
            $query = "SELECT * ,? as Journey_Starting_Date , ? as Journey_Ending_Date , Null as Booking_Id from (
                SELECT * , COUNT(Vehicle_Id) as sorted_booking_count 
                FROM (SELECT COUNT(booking.Booking_Id) OVER (PARTITION BY booking.Vehicle_Id) as booking_count, vehicle.Owner_Id , vehicle.Vehicle_Id , 
                vehicle.Price , vehicle.Vehicle_PlateNumber , vehicle.Passenger_amount , vehicle.Current_Lat , vehicle.Driver_Id,
                vehicle.Vehicle_type , vehicle.Current_Long , vehicle.Feedback_count , vehicle.Feedback_score , booking.Booking_Id , 
                booking.Journey_Starting_Date , booking.Journey_Ending_Date
                FROM vehicle
                LEFT JOIN booking
                ON vehicle.Vehicle_Id = booking.Vehicle_Id and (booking.Booking_Status = 'approved' or booking.Booking_Status = 'pending' or booking.Booking_Status = 'driving')
                WHERE vehicle.District = ? and vehicle.Booking_Type = 'rent-out'  and vehicle.Status = 'verified' 
                and vehicle.Vehicle_type = ? and vehicle.Driver_Id Is $driver
                ORDER BY vehicle.Vehicle_Id) as vehicle_booking
                WHERE Booking_Id IS Null or not (? <= Journey_Ending_Date and  ? >= Journey_Starting_Date)
                GROUP BY Vehicle_Id) as sorted_vehicles
                WHERE sorted_booking_count = booking_count or booking_count = 0";
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $start_date);
            $pstmt->bindValue(2, $end_date);
            $pstmt->bindValue(3, $district);
            $pstmt->bindValue(4, strtolower($type));
            $pstmt->bindValue(5, $start_date);
            $pstmt->bindValue(6, $end_date);
            $pstmt->execute();
            $rs = $pstmt->fetchAll(PDO::FETCH_OBJ);
            if ($pstmt->rowCount() > 0) {
                return json_encode($rs);
            } else {
                return 45;
            }
        } catch (Exception $ex) {
            die("Error : " . $ex->getMessage());
        }
    }

    // update vehicle status
    public function changeVehicleStatus($connection, $id, $availability)
    {
        $query = "UPDATE vehicle SET Availability = ? WHERE Vehicle_Id = ?";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $availability);
            $pstmt->bindValue(2, $id);
            $pstmt->execute();
            return $pstmt->rowCount() === 1;
        } catch (PDOException $ex) {
            die("Error occurred : " . $ex->getMessage());
        }
    }

    // update vehicle location
    public function changeLocationStatus($connection, $id, $lat, $lng)
    {
        $query = "UPDATE vehicle SET Current_Lat = ? , Current_Long = ? WHERE Vehicle_Id = ?";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $lat);
            $pstmt->bindValue(2, $lng);
            $pstmt->bindValue(3, $id);
            $pstmt->execute();
            return $pstmt->rowCount() === 1;
        } catch (PDOException $ex) {
            die("Error occurred : " . $ex->getMessage());
        }
    }

    public function getVehicleCurrentLocation($connection, $vehicle)
    {
        $query = "SELECT Current_Lat , Current_Long from vehicle where Vehicle_Id = ?";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $vehicle);
            $pstmt->execute();
            $result = $pstmt->fetch(PDO::FETCH_ASSOC);
            return json_encode($result);
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
    public function getLocation()
    {
        return $this->location;
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
