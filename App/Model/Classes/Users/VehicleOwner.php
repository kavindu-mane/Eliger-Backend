<?php

namespace EligerBackend\Model\Classes\Users;

use EligerBackend\Model\Classes\Users\User;
use PDO;
use PDOException;

class VehicleOwner extends User
{
    private $phone;
    private $firstName;
    private $lastName;
    private $address;
    private $income;
    private $charges;

    public function __construct()
    {
        $arguments = func_get_args();
        $numberOfArguments = func_num_args();

        if (method_exists($this, $function = '_construct' . $numberOfArguments)) {
            call_user_func_array(array($this, $function), $arguments);
        }
    }

    public function _construct7($email, $password, $type, $phone, $firstName, $lastName, $address)
    {
        parent::__construct($email, $password, $type);
        $this->phone = $phone;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->address =  $address;
    }

    public function _construct0()
    {
    }

    // register function of external user
    public function register($connection)
    {
        if (parent::register($connection)) {
            try {
                $query = "insert into vehicle_owner (Owner_firstname , Owner_lastname , Owner_address , Owner_Tel , Email) values(? , ? , ? , ? , ?)";
                $pstmt = $connection->prepare($query);
                $pstmt->bindValue(1, $this->firstName);
                $pstmt->bindValue(2, $this->lastName);
                $pstmt->bindValue(3, $this->address);
                $pstmt->bindValue(4, $this->phone);
                $pstmt->bindValue(5, $this->getEmail());
                $pstmt->execute();

                parent::sendVerificationEmail($connection, "{$this->firstName} {$this->lastName}", "register", "Verify your Eliger account", "registration");
                return true;
            } catch (PDOException $ex) {
                die("Registration Error : " . $ex->getMessage());
            }
        }
    }

    // update function
    public function updateOwner($connection, $email, $data)
    {
        $query = "update vehicle_owner set Owner_firstname =? , Owner_lastname = ? , Owner_address = ? , Owner_Tel = ? where Email = ?";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $data["fname"]);
            $pstmt->bindValue(2, $data["lname"]);
            $pstmt->bindValue(3, $data["address"]);
            $pstmt->bindValue(4, $data["phone"]);
            $pstmt->bindValue(5, $email);
            $pstmt->execute();
            if ($pstmt->rowCount() === 1) {
                return 200;
            }
        } catch (PDOException $ex) {
            die("Loading Error : " . $ex->getMessage());
        }
    }

    // load owner details
    public function loadOwner($connection, $email)
    {
        $query = "select * from vehicle_owner_details where Email = ?";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $email);
            $pstmt->execute();
            if ($pstmt->rowCount() === 1) {
                return json_encode($pstmt->fetch(PDO::FETCH_OBJ));
            } else {
                return 14;
            }
        } catch (PDOException $ex) {
            die("Loading Error : " . $ex->getMessage());
        }
    }

    //Load vehicles
    public function loadVehicles($connection, $email, $offset)
    {
        $query = "WITH PaginatedResults AS (
                SELECT vehicle.* , driver_details.Driver_firstname , driver_details.Driver_lastname 
                from vehicle inner join vehicle_owner_details 
                on vehicle_owner_details.Owner_Id=vehicle.Owner_Id and vehicle_owner_details.Email = ? 
                left join driver_details ON driver_details.Driver_Id = vehicle.Driver_Id)
                SELECT *, (SELECT COUNT(*) FROM PaginatedResults) AS total_rows
                FROM PaginatedResults
                LIMIT 15 OFFSET $offset";

        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $email);
            $pstmt->execute();
            return $pstmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    //Load drivers
    public function loadDriver($connection, $email, $status = false, $offset)
    {
        $query = "WITH PaginatedResults AS (SELECT driver_details.* from driver_details 
                inner join vehicle_owner_details 
                on vehicle_owner_details.Owner_Id=driver_details.Owner_Id 
                and vehicle_owner_details.Email = ?" . ($status ? "and driver_details.Status = ?)" : ")") .
            "SELECT *, (SELECT COUNT(*) FROM PaginatedResults) AS total_rows
                FROM PaginatedResults
                LIMIT 15 OFFSET $offset";

        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $email);
            if ($status) $pstmt->bindValue(2, "verified");
            $pstmt->execute();
            return $pstmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    // load bookings
    public function loadBooking($connection, $id, $offset)
    {
        $query = "WITH PaginatedResults AS (
                    SELECT booking.* , vehicle.Vehicle_PlateNumber ,vehicle.Vehicle_type , 
                    customer_details.Customer_firstname , customer_details.Customer_lastname , customer_details.Customer_Tel,
                    driver_details.Driver_firstname , driver_details.Driver_lastname ,
                    payment.Payment_type , payment.Amount , payment.Datetime FROM booking 
                    LEFT JOIN payment 
                    ON  booking.Customer_Id = payment.Customer_Id 
                    LEFT JOIN vehicle 
                    ON vehicle.Vehicle_Id = booking.vehicle_Id
                    LEFT JOIN customer_details 
                    ON customer_details.Customer_Id = booking.Customer_Id
                    LEFT JOIN driver_details 
                    ON driver_details.Driver_Id = booking.Driver_Id
                    WHERE booking.Owner_Id = ? and booking.Booking_Type = 'rent-out')
                    SELECT *, (SELECT COUNT(*) FROM PaginatedResults) AS total_rows
                    FROM PaginatedResults
                    ORDER BY FIELD(Booking_Status , 'pending','approved','finished' , 'rejected')
                    LIMIT 15 OFFSET $offset";
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

    public function getIncome()
    {
        return $this->income;
    }

    public function getCharges()
    {
        return $this->charges;
    }
}
