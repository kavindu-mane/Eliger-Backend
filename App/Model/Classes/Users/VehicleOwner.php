<?php

namespace EligerBackend\model\classes\Users;

use EligerBackend\Model\Classes\Users\User;
use PDOException;

class VehicleOwner extends User
{
    private $phone;
    private $firstName;
    private $lastName;
    private $address;
    private $income;
    private $charges;

    public function __construct($email, $password, $type, $phone, $firstName, $lastName, $address)
    {
        parent::__construct($email, $password, $type);
        $this->phone = $phone;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->address =  $address;
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
    public function updateOwner($connection, $type)
    {
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
