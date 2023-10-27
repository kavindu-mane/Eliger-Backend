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
                    SELECT booking.* , vehicle.Vehicle_PlateNumber ,vehicle.Vehicle_type , vehicle.Price ,
                    customer_details.Customer_firstname , customer_details.Customer_lastname , customer_details.Customer_Tel,
                    vehicle_owner_details.Owner_firstname , vehicle_owner_details.Owner_lastname , vehicle_owner_details.Owner_Tel,
                    driver_details.Driver_firstname , driver_details.Driver_lastname ,
                    payment.Payment_type , payment.Amount , payment.Datetime FROM booking 
                    LEFT JOIN payment 
                    ON  booking.Booking_Id = payment.Booking_Id 
                    LEFT JOIN vehicle 
                    ON vehicle.Vehicle_Id = booking.vehicle_Id
                    LEFT JOIN customer_details 
                    ON customer_details.Customer_Id = booking.Customer_Id
                    LEFT JOIN vehicle_owner_details 
                    ON vehicle_owner_details.Owner_Id = booking.Owner_Id
                    LEFT JOIN driver_details 
                    ON driver_details.Driver_Id = booking.Driver_Id
                    WHERE booking.Owner_Id = ? and booking.Booking_Type = 'rent-out')
                    SELECT *, (SELECT COUNT(*) FROM PaginatedResults) AS total_rows
                    FROM PaginatedResults
                    ORDER BY Booking_Time DESC
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

    // load bookings
    public function loadUpcommingBooking($connection, $id)
    {
        $query = "SELECT booking.Journey_Starting_Date , booking.Journey_Ending_Date , vehicle.Vehicle_PlateNumber ,vehicle.Vehicle_type
                    FROM booking 
                    INNER JOIN vehicle 
                    ON vehicle.Vehicle_Id = booking.vehicle_Id
                    INNER JOIN vehicle_owner_details 
                    ON vehicle_owner_details.Owner_Id = booking.Owner_Id
                    WHERE booking.Owner_Id = ? and booking.Booking_Type = 'rent-out' and (booking.Booking_Status = 'approved' or booking.Booking_Status = 'driving')
                    and (booking.Journey_Starting_Date >= CURDATE() or booking.Journey_Ending_Date >= CURDATE())";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $id);
            $pstmt->execute();
            $rs = $pstmt->fetchAll(PDO::FETCH_OBJ);
            if ($pstmt->rowCount() > 0) {
                $resultArr = array();
                $today = date("Y-m-d");
                foreach ($rs as $result) {
                    $travelDates = 1 +  intval((strtotime($result->Journey_Ending_Date) - strtotime($result->Journey_Starting_Date)) / (60 * 60 * 24));
                    if ((strtotime($result->Journey_Starting_Date) - strtotime($today)) / (60 * 60 * 24) >= 0) {
                        $row = $resultArr[$result->Journey_Starting_Date] ?? array();
                        $row[count($row)] = "$result->Vehicle_PlateNumber ($result->Vehicle_type) $travelDates days booking start.";
                        $resultArr[$result->Journey_Starting_Date] = $row;
                    }
                    if ((strtotime($result->Journey_Ending_Date) - strtotime($today)) / (60 * 60 * 24) >= 0) {
                        $row = $resultArr[$result->Journey_Ending_Date] ?? array();
                        $row[count($row)] = "$result->Vehicle_PlateNumber ($result->Vehicle_type) $travelDates days booking end.";
                        $resultArr[$result->Journey_Ending_Date] = $row;
                    }
                }
                echo json_encode($resultArr);
            }
        } catch (PDOException $ex) {
            die("Loading Error : " . $ex->getMessage());
        }
    }

    // load owner payment details
    public function loadOwnerHomeDetails($connection, $id)
    {
        $date = date('Y-m-01');
        $query = "SELECT vehicle_owner.Charges , vehicle_owner.Income , 
                SUM(IF(payment.Payment_type = 'offline' AND DATE(payment.Datetime) = CURDATE(), 
                payment.Amount * IF(booking.Driver_Id IS NULL , 0.9 , 0.6) , 0)) AS daily_offline_total ,
                SUM(IF(payment.Payment_type = 'online' AND DATE(payment.Datetime) = CURDATE(),
                payment.Amount * IF(booking.Driver_Id IS NULL , 0.9 , 0.6) , 0)) AS daily_online_total,
                SUM(IF(payment.Payment_type = 'offline' AND DATE(payment.Datetime) >=  DATE($date), 
                payment.Amount * IF(booking.Driver_Id IS NULL , 0.9 , 0.6), 0)) AS monthly_offline_total ,
                SUM(IF(payment.Payment_type = 'online' AND DATE(payment.Datetime) >=  DATE($date), 
                payment.Amount * IF(booking.Driver_Id IS NULL , 0.9 , 0.6), 0)) AS monthly_online_total  
                FROM vehicle_owner 
                INNER JOIN booking
                ON booking.Owner_Id = vehicle_owner.Owner_Id
                INNER JOIN payment
                ON payment.Booking_Id = booking.Booking_Id
                where vehicle_owner.Owner_Id = ?
                GROUP BY vehicle_owner.Owner_Id";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $id);
            $pstmt->execute();
            return json_encode($pstmt->fetch(PDO::FETCH_OBJ));
        } catch (PDOException $ex) {
            die("Loading Error : " . $ex->getMessage());
        }
    }

    // load drivers daily incomes
    public function loadDriverIncomes($connection, $id)
    {
        $query = "SELECT * , (daily_offline_total + daily_online_total) AS today_total_income FROM
                (SELECT driver.Driver_firstname , driver.Driver_lastname ,
                SUM(IF(payment.Payment_type = 'offline' AND DATE(payment.Datetime) = CURDATE(), payment.Amount * 0.3 , 0)) AS daily_offline_total ,
                SUM(IF(payment.Payment_type = 'online' AND DATE(payment.Datetime) = CURDATE(), payment.Amount * 0.3 , 0)) AS daily_online_total ,
                SUM(IF(payment.Payment_type = 'offline' AND booking.Booking_Type = 'book-now' 
                AND DATE(payment.Datetime) = CURDATE(), payment.Amount * 0.6 , 0)) AS book_now_owner_income,
                SUM(IF(payment.Payment_type = 'offline' AND booking.Booking_Type = 'rent-out' 
                AND DATE(payment.Datetime) = CURDATE(), payment.Amount * 0.3 , 0)) AS rent_out_driver_income
                FROM vehicle_owner 
                INNER JOIN driver
                ON driver.Owner_Id = vehicle_owner.Owner_Id
                INNER JOIN booking
                ON booking.Driver_Id = driver.Driver_Id
                LEFT JOIN payment
                ON payment.Booking_Id = booking.Booking_Id
                where vehicle_owner.Owner_Id = ?
                GROUP BY driver.Driver_Id) AS calculated_data
                ORDER BY today_total_income DESC";
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
