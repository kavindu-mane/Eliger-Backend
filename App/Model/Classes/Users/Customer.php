<?php

namespace EligerBackend\model\classes\Users;

use EligerBackend\Model\Classes\Users\User;
use PDO;
use PDOException;

class Customer extends User
{
    private $phone;
    private $firstName;
    private $lastName;

    public function __construct()
    {
        $arguments = func_get_args();
        $numberOfArguments = func_num_args();

        if (method_exists($this, $function = '_construct' . $numberOfArguments)) {
            call_user_func_array(array($this, $function), $arguments);
        }
    }

    public function _construct6($email, $password, $type, $phone, $firstName, $lastName)
    {
        parent::__construct($email, $password, $type);
        $this->phone = $phone;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function _construct0()
    {
    }

    // register function of external user
    public function register($connection)
    {
        if (parent::register($connection)) {
            try {
                $query = "insert into customer (Customer_firstname , Customer_lastname , Customer_Tel , Email) values(? , ? , ? , ?)";
                $pstmt = $connection->prepare($query);
                $pstmt->bindValue(1, $this->firstName);
                $pstmt->bindValue(2, $this->lastName);
                $pstmt->bindValue(3, $this->phone);
                $pstmt->bindValue(4, $this->getEmail());
                $pstmt->execute();

                parent::sendVerificationEmail($connection, "{$this->firstName} {$this->lastName}", "register", "Verify your Eliger account", "registration");

                return true;
            } catch (PDOException $ex) {
                die("Registration Error : " . $ex->getMessage());
            }
        }
    }

    // update function
    public function updateCustomer($connection, $email, $data)
    {
        $query = "update customer set Customer_firstname =? , Customer_lastname = ? , Customer_Tel = ? where Email = ?";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $data["fname"]);
            $pstmt->bindValue(2, $data["lname"]);
            $pstmt->bindValue(3, $data["phone"]);
            $pstmt->bindValue(4, $email);
            $pstmt->execute();
            if ($pstmt->rowCount() === 1) {
                return 200;
            }
        } catch (PDOException $ex) {
            die("Loading Error : " . $ex->getMessage());
        }
    }

    // load customer details
    public function loadCustomer($connection, $email)
    {
        $query = "select Customer_firstname, Customer_lastname, Customer_Tel, Email from customer where Email = ?";
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

    // load bookings
    public function loadBooking($connection, $id)
    {
        $query = "SELECT booking.* , vehicle.Vehicle_PlateNumber ,vehicle.Vehicle_type , vehicle.Passenger_amount , vehicle.Current_Lat , vehicle.Current_Long ,
                    vehicle_owner_details.Owner_firstname , vehicle_owner_details.Owner_lastname , vehicle_owner_details.Owner_Tel,
                    driver_details.Driver_firstname , driver_details.Driver_lastname , driver_details.Driver_Tel,
                    payment.Payment_type , payment.Amount , payment.Datetime , feedback.Feedback_Id FROM booking 
                    LEFT JOIN payment 
                    ON  booking.Customer_Id = payment.Customer_Id 
                    LEFT JOIN vehicle 
                    ON vehicle.Vehicle_Id = booking.vehicle_Id
                    LEFT JOIN vehicle_owner_details 
                    ON vehicle_owner_details.Owner_Id = booking.Owner_Id
                    LEFT JOIN driver_details 
                    ON driver_details.Driver_Id = booking.Driver_Id
                    LEFT JOIN feedback 
                    ON feedback.Booking_Id = booking.Booking_Id
                    WHERE booking.Customer_Id = ?
                    ORDER BY FIELD(booking.Booking_Status , 'approved' , 'pending','rejected','finished')";
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
}
