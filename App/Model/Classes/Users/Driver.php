<?php

namespace EligerBackend\Model\Classes\Users;

use EligerBackend\Model\Classes\Users\User;
use PDO;
use PDOException;

class Driver extends User
{
    private $phone;
    private $firstName;
    private $lastName;
    private $licence;
    private $address;
    private $owner;

    public function __construct()
    {
        $arguments = func_get_args();
        $numberOfArguments = func_num_args();

        if (method_exists($this, $function = '_construct' . $numberOfArguments)) {
            call_user_func_array(array($this, $function), $arguments);
        }
    }

    public function _construct9($email, $password, $type, $phone, $firstName, $lastName, $licence, $address, $owner)
    {
        parent::__construct($email, $password, $type);
        $this->phone = $phone;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->licence = $licence;
        $this->address = $address;
        $this->owner = $owner;
    }

    public function _construct0()
    {
    }

    // register function of external user
    public function register($connection)
    {
        if (parent::register($connection)) {
            try {
                $query = "insert into driver (Driver_firstname , Driver_lastname , Driver_Tel , Email , Licence_File , Driver_address , Owner_Id) values(? , ? , ? , ? , ? , ? , ?)";
                $pstmt = $connection->prepare($query);
                $pstmt->bindValue(1, $this->firstName);
                $pstmt->bindValue(2, $this->lastName);
                $pstmt->bindValue(3, $this->phone);
                $pstmt->bindValue(4, $this->getEmail());
                $pstmt->bindValue(5, $this->licence);
                $pstmt->bindValue(6, $this->address);
                $pstmt->bindValue(7, $this->owner);
                $pstmt->execute();

                if ($pstmt->rowCount() === 1) {
                    parent::sendVerificationEmail($connection, "{$this->firstName} {$this->lastName}", "register", "Verify your Eliger account", "registration");
                }
                return $pstmt->rowCount() === 1;
            } catch (PDOException $ex) {
                die("Registration Error : " . $ex->getMessage());
            }
        }
    }

    // update function
    public function updateDriver($connection, $email, $data)
    {
        $query = "update driver set Driver_firstname =? , Driver_lastname = ? , Driver_address = ? where Email = ?";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $data["fname"]);
            $pstmt->bindValue(2, $data["lname"]);
            $pstmt->bindValue(3, $data["address"]);
            $pstmt->bindValue(4, $email);
            $pstmt->execute();
            if ($pstmt->rowCount() === 1) {
                return 200;
            }
        } catch (PDOException $ex) {
            die("Loading Error : " . $ex->getMessage());
        }
    }

    //Load vehicles
    public function loadDriver($connection, $email)
    {
        $query = "SELECT driver_details.Driver_Id , driver_details.Status , driver_details.Driver_firstname , driver_details.Driver_lastname , 
                driver_details.Driver_Tel , driver_details.Driver_address , driver_details.Email , vehicle.Vehicle_type , vehicle.Availability , 
                vehicle.Vehicle_Id , vehicle.Booking_Type ,vehicle.Vehicle_PlateNumber , vehicle.Price FROM driver_details 
                LEFT JOIN vehicle ON driver_details.Driver_Id = vehicle.Driver_Id  WHERE driver_details.Email = ?";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $email);
            $pstmt->execute();
            $rs = $pstmt->fetch(PDO::FETCH_ASSOC);
            $rs["Bank_Status"] = $this->bankDetailsStatus($connection);
            return json_encode($rs);
        } catch (PDOException $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    // load booknow bookings
    public function loadBookNowBooking($connection, $id)
    {
        $query = "SELECT * FROM booking WHERE Driver_Id = ? AND Booking_Type = 'book-now' 
        AND Booking_Status = 'pending' ORDER BY Booking_Time";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $id);
            $pstmt->execute();
            return json_encode($pstmt->fetchAll(PDO::FETCH_OBJ));
        } catch (PDOException $ex) {
            die("Loading Error : " . $ex->getMessage());
        }
    }

    // load rent-out bookings
    public function loadRentOutBooking($connection, $id)
    {
        $query = "SELECT * FROM booking WHERE Driver_Id = ? AND Booking_Type = 'rent-out' 
        AND Booking_Status = 'approved' ORDER BY Booking_Time";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $id);
            $pstmt->execute();
            return json_encode($pstmt->fetchAll(PDO::FETCH_OBJ));
        } catch (PDOException $ex) {
            die("Loading Error : " . $ex->getMessage());
        }
    }

    // getters
    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getLicence()
    {
        return $this->licence;
    }
}
