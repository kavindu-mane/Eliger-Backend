<?php

namespace EligerBackend\Model\Classes\Users;

use EligerBackend\Model\Classes\Users\User;
use PDOException;
use PDO;

class HelpAndSupport extends User
{
    private $name;
    private $email;

    public function __construct()
    {
        $arguments = func_get_args();
        $numberOfArguments = func_num_args();

        if (method_exists($this, $function = '_construct' . $numberOfArguments)) {
            call_user_func_array(array($this, $function), $arguments);
        }
    }

    public function _construct4($email, $password, $type, $name)
    {
        parent::__construct($email, $password, $type);
        $this->name = $name;
        $this->email = $email;
    }
    public function _construct0()
    {
    }
    
    public function register($connection)
    {
        if (parent::register($connection)) {
            try {
                $query = "insert into help_and_support_staff (name , email) values(? , ?)";
                $pstmt = $connection->prepare($query);
                $pstmt->bindValue(1, $this->name);
                $pstmt->bindValue(2, $this->email);
                $pstmt->execute();

                parent::sendVerificationEmail($connection, "{$this->name} {$this->email}", "register", "Registration of Help & Support account", "registration");

                return true;
            } catch (PDOException $ex) {
                die("Registration Error : " . $ex->getMessage());
            }
        }
    }

    public function loadManageBooking($connection, $offset)
    {
        $query = "WITH PaginatedResults AS (
                    SELECT booking.* , vehicle.Vehicle_PlateNumber ,vehicle.Vehicle_type , 
                    concat(customer_details.Customer_firstname , ' ' ,customer_details.Customer_lastname) AS Customer, 
                    concat(vehicle_owner_details.Owner_firstname , ' ' , vehicle_owner_details.Owner_lastname) AS Owner,
                    concat(driver_details.Driver_firstname , ' ' , driver_details.Driver_lastname) AS Driver FROM booking 
                    LEFT JOIN vehicle 
                    ON vehicle.Vehicle_Id = booking.vehicle_Id
                    LEFT JOIN customer_details 
                    ON customer_details.Customer_Id = booking.Customer_Id
                    LEFT JOIN vehicle_owner_details 
                    ON vehicle_owner_details.Owner_Id = booking.Owner_Id
                    LEFT JOIN driver_details 
                    ON driver_details.Driver_Id = booking.Driver_Id 
                    WHERE booking.Booking_Status = 'approved' OR booking.Booking_Status = 'pending' OR booking.Booking_Status = 'driving')
                    SELECT *, (SELECT COUNT(*) FROM PaginatedResults) AS total_rows
                    FROM PaginatedResults
                    ORDER BY Booking_Time DESC
                    LIMIT 15 OFFSET $offset";

        try {
            $pstmt = $connection->prepare($query);
            $pstmt->execute();
            return $pstmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $ex) {
            die(" Error : " . $ex->getMessage());
        }
    }

    public function loadManageVehicles($connection, $offset)
    {
        $query = "WITH PaginatedResults AS ( SELECT vehicle.* , concat(vehicle_owner. Owner_firstname , ' ' , vehicle_owner. Owner_lastname) AS Owner from vehicle 
                INNER JOIN vehicle_owner ON vehicle_owner.Owner_Id = vehicle.Owner_Id)
                SELECT *, (SELECT COUNT(*) FROM PaginatedResults) AS total_rows
                FROM PaginatedResults
                ORDER BY Vehicle_Id
                LIMIT 15 OFFSET $offset";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->execute();
            return $pstmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $ex) {
            die(" Error : " . $ex->getMessage());
        }
    }

    public function loadManageFeedback($connection, $offset)
    {
        $query = "WITH PaginatedResults AS ( SELECT feedback.* , 
                concat(customer_details.Customer_firstname , ' ' ,customer_details.Customer_lastname) AS Customer, 
                vehicle.Vehicle_PlateNumber from feedback 
                INNER JOIN customer_details ON customer_details.Customer_Id = feedback.Customer_Id
                INNER JOIN vehicle ON vehicle.Vehicle_Id = feedback.Vehicle_Id)
                SELECT *, (SELECT COUNT(*) FROM PaginatedResults) AS total_rows
                FROM PaginatedResults
                ORDER BY Feedback_Id
                LIMIT 15 OFFSET $offset";

        try {
            $pstmt = $connection->prepare($query);
            $pstmt->execute();
            return $pstmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $ex) {
            die(" Error : " . $ex->getMessage());
        }
    }
}
