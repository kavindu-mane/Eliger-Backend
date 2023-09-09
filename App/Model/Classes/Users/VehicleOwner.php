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
    public function updateOwner($connection, $email , $data)
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
        $query = "select Owner_firstname, Owner_lastname, Owner_address, Charges, Income, Owner_Tel, Email from vehicle_owner where Email = ?";
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
    public function loadVehicles($connection , $email)
    {
        $query = "select vehicle.* from vehicle inner join vehicle_owner_details on vehicle_owner_details.Owner_Id=vehicle.Owner_Id AND vehicle_owner_details.Email = ? ";

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
    public function loadDriver($connection , $email , $status = false)
    {
        $query = "select driver_details.* from driver_details inner join vehicle_owner_details on vehicle_owner_details.Owner_Id=driver_details.Owner_Id AND vehicle_owner_details.Email = ?".($status ? "and driver_details.Status = ?":"");

        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $email);
            if($status) $pstmt->bindValue(2, "verified");
            $pstmt->execute();
            return $pstmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $ex) {
            die("Registration Error : " . $ex->getMessage());
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
