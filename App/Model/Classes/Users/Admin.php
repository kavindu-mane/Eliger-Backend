<?php

namespace EligerBackend\Model\Classes\Users;

use EligerBackend\Model\Classes\Connectors\EmailConnector;
use PDO;
use PDOException;

class Admin extends User
{
    // registering of help & support staff member
    public function createHelpAccount($connection, $name, $email, $password)
    {
        $hns = new HelpAndSupport($email, $password, "hands", $name);
        if ($hns->register($connection)) {
            return 200;
        }
        return 500;
    }

    //load Accounts
    public function loadAccountDetails($connection, $account_type, $status, $offset)
    {
        $query = "WITH PaginatedResults AS ( SELECT * from customer_details where Account_Status=? )
                SELECT *, (SELECT COUNT(*) FROM PaginatedResults) AS total_rows
                FROM PaginatedResults
                ORDER BY Customer_Id
                LIMIT 15 OFFSET $offset";

        if ($account_type === "driver") {
            $query = "WITH PaginatedResults AS ( SELECT driver_details.* , vehicle_owner_details.Owner_firstname , 
            vehicle_owner_details.Owner_lastname from driver_details inner join vehicle_owner_details 
            on driver_details.Owner_Id = vehicle_owner_details.Owner_Id and driver_details.Account_Status=? )
            SELECT *, (SELECT COUNT(*) FROM PaginatedResults) AS total_rows
            FROM PaginatedResults
            ORDER BY Driver_Id
            LIMIT 15 OFFSET $offset";
        } elseif ($account_type === "hands") {
            $query = "WITH PaginatedResults AS ( SELECT * from hands_details where Account_Status=? )
                SELECT *, (SELECT COUNT(*) FROM PaginatedResults) AS total_rows
                FROM PaginatedResults
                ORDER BY staff_id
                LIMIT 15 OFFSET $offset";
        } elseif ($account_type === "vehicle_owner") {
            $query = "WITH PaginatedResults AS ( SELECT * from vehicle_owner_details where Account_Status=? )
                SELECT *, (SELECT COUNT(*) FROM PaginatedResults) AS total_rows
                FROM PaginatedResults
                ORDER BY Owner_Id
                LIMIT 15 OFFSET $offset";
        }
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $status);
            $pstmt->execute();
            return $pstmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    //Load new vehicle registrations
    public function loadNewVehicles($connection, $status, $offset)
    {
        $query = "WITH PaginatedResults AS (
                SELECT vehicle.*,vehicle_owner.Owner_firstname , vehicle_owner.Owner_lastname 
                from vehicle inner join vehicle_owner on vehicle_owner.Owner_Id=vehicle.Owner_Id AND vehicle.Status = ? )
                SELECT *, (SELECT COUNT(*) FROM PaginatedResults) AS total_rows
                FROM PaginatedResults
                ORDER BY Vehicle_Id
                LIMIT 15 OFFSET $offset";

        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $status);
            $pstmt->execute();
            return $pstmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    //Load new driver registrations
    public function loadNewDriver($connection, $status, $offset)
    {
        $query = "WITH PaginatedResults AS (
                SELECT driver.*,vehicle_owner.Owner_firstname , vehicle_owner.Owner_lastname 
                from driver inner join vehicle_owner on vehicle_owner.Owner_Id=driver.Owner_Id AND driver.Status = ? )
                SELECT *, (SELECT COUNT(*) FROM PaginatedResults) AS total_rows
                FROM PaginatedResults
                ORDER BY Driver_Id
                LIMIT 15 OFFSET $offset";

        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $status);
            $pstmt->execute();
            return $pstmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    //Load new bank details
    public function loadNewBankDetails(
        $connection,
        $status,
        $offset
    ) {
        $query = "WITH PaginatedResults AS (
                SELECT bank_details.*
                FROM bank_details  WHERE bank_details.Status = ? )
                SELECT *, (SELECT COUNT(*) FROM PaginatedResults) AS total_rows
                FROM PaginatedResults
                ORDER BY Record_Id
                LIMIT 15 OFFSET $offset";

        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $status);
            $pstmt->execute();
            return $pstmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    // disable user
    public function disableUser($connection, $status, $email)
    {
        $query = "update user set Account_Status = ? where Email = ?";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $status);
            $pstmt->bindValue(2, $email);
            $pstmt->execute();
            if ($pstmt->rowCount() === 1) {
                return 200;
            } else {
                return 500;
            }
        } catch (PDOException $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    public function getDetailsUsingDriverId($connection, $driverId)
    {
        $query = "SELECT driver.Driver_firstname , driver.Driver_lastname , driver.Email AS Driver_Email ,
                vehicle_owner.Owner_firstname , vehicle_owner.Owner_lastname , vehicle_owner.Email AS Owner_Email
                FROM driver INNER JOIN vehicle_owner
                ON vehicle_owner.Owner_Id = driver.Owner_Id 
                AND driver.Driver_Id = ?";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $driverId);
            $pstmt->execute();
            if ($pstmt->rowCount() === 1) {
                return $pstmt->fetch(PDO::FETCH_OBJ);
            }
        } catch (PDOException $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    public function getDetailsUsingVehicleId($connection, $vehicleId)
    {
        $query = "SELECT vehicle.Vehicle_PlateNumber , vehicle_owner.Owner_firstname , vehicle_owner.Owner_lastname , vehicle_owner.Email
                FROM vehicle INNER JOIN vehicle_owner
                ON vehicle_owner.Owner_Id = vehicle.Owner_Id
                AND vehicle.Vehicle_Id = ?";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $vehicleId);
            $pstmt->execute();
            if ($pstmt->rowCount() === 1) {
                return $pstmt->fetch(PDO::FETCH_OBJ);
            }
        } catch (PDOException $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    public function getDetailsUsingEmail($connection, $email)
    {
        $query = "SELECT users.full_name
                FROM users INNER JOIN bank_details
                ON users.Email = bank_details.Email
                AND bank_details.Email = ?";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $email);
            $pstmt->execute();
            if ($pstmt->rowCount() === 1) {
                return $pstmt->fetch(PDO::FETCH_OBJ);
            }
        } catch (PDOException $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    // Review Document function
    public function reviewDocument($connection, $type, $status, $Id)
    {
        $query = "UPDATE driver set Status = ? where Driver_Id = ?";
        if ($type === "vehicle") {
            $query = "UPDATE vehicle set Status = ? where Vehicle_Id = ?";
        } elseif ($type === "bank") {
            $query = "UPDATE bank_details set Status = ? where Email = ?";
        }

        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $status);
            $pstmt->bindValue(2, $Id);
            $pstmt->execute();
            if ($pstmt->rowCount() === 1) {
                if ($status === "rejected") {
                    if ($type === "driver") {
                        $details = $this->getDetailsUsingDriverId($connection, $Id);
                        EmailConnector::sendActionEmail($details->Driver_firstname . " " . $details->Driver_lastname, "The driving license you submitted through Eliger has been rejected by the Administrator.", $details->Driver_Email, "Rejection of Driver Document");
                        EmailConnector::sendActionEmail($details->Owner_firstname . " " . $details->Owner_lastname, "The driving license you submitted through Eliger has been rejected by the Administrator.", $details->Owner_Email, "Rejection of Driver Document");
                    } elseif ($type === "vehicle") {
                        $details = $this->getDetailsUsingVehicleId($connection, $Id);
                        EmailConnector::sendActionEmail($details->Owner_firstname . " " . $details->Owner_lastname, "The vehicle documents you provided({$details->Vehicle_PlateNumber}) through the Eliger was rejected by the Administrator.", $details->Email, "Rejection of Vehicle Document");
                    } elseif ($type === "bank") {
                        $details = $this->getDetailsUsingEmail($connection, $Id);
                        EmailConnector::sendActionEmail($details->full_name, "Please check your bank account statement and re-enter the details before submit.", $Id, "Rejection of Bank Document");
                    }
                } elseif ($status === "verified") {
                    if ($type === "driver") {
                        $details = $this->getDetailsUsingDriverId($connection, $Id);
                        EmailConnector::sendActionEmail($details->Driver_firstname . " " . $details->Driver_lastname, "The driving license you submitted through Eliger has been approved by the Administrator.", $details->Driver_Email, "Approval of Driver Document");
                        EmailConnector::sendActionEmail($details->Owner_firstname . " " . $details->Owner_lastname, "The driving license you submitted through Eliger has been approved by the Administrator.", $details->Owner_Email, "Approval of Driver Document");
                    } elseif ($type === "vehicle") {
                        $details = $this->getDetailsUsingVehicleId($connection, $Id);
                        EmailConnector::sendActionEmail($details->Owner_firstname . " " . $details->Owner_lastname, "The vehicle documents you provided({$details->Vehicle_PlateNumber}) through the Eliger was approved by the Administrator.", $details->Email, "Approval of Vehicle Document");
                    } elseif ($type === "bank") {
                        $details = $this->getDetailsUsingEmail($connection, $Id);
                        EmailConnector::sendActionEmail($details->full_name, "The bank details you entered through Eligar have been approved by the Administrator.", $Id, "Approval of Bank Document");
                    }
                }
                return 200;
            } else {
                return 500;
            }
        } catch (PDOException $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    // load payment eligible users
    public function loadPaymentEligibleUsers($connection, $offset)
    {
        try {
            $query = "WITH PaginatedResults AS (
                SELECT(vehicle_owner_details.Income - vehicle_owner_details.Charges) AS Income ,
                bank_details.Bank , bank_details.Branch , bank_details.Beneficiary_Name , bank_details.Acc_Number , bank_details.Status
                FROM bank_details 
                INNER JOIN vehicle_owner_details ON vehicle_owner_details.Email = bank_details.Email
                UNION ALL
                SELECT driver_details.Income AS Income ,
                bank_details.Bank , bank_details.Branch , bank_details.Beneficiary_Name , bank_details.Acc_Number , bank_details.Status
                FROM bank_details 
                INNER JOIN driver_details ON driver_details.Email = bank_details.Email)
                SELECT *, (SELECT COUNT(*) FROM PaginatedResults) AS total_rows
                FROM PaginatedResults
                WHERE Status = 'verified' AND Income > 1000
                ORDER BY Bank
                LIMIT 25 OFFSET $offset";
            $pstmt = $connection->prepare($query);
            $pstmt->execute();
            return $pstmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    // load payment eligible users and categorize with bank
    public function loadPaymentEligibleWithCategorize($connection)
    {
        $banks = array(
            "People's Bank", "Bank of Ceylon", "Hatton National Bank", "Sampath Bank", "Commercial Bank", "NDB", "NSB",
        );
        $results = array();
        try {
            foreach ($banks as $bank) {
                $query = "SELECT bank_details.Bank , bank_details.Branch , bank_details.Beneficiary_Name AS 'Beneficiary Name', bank_details.Acc_Number AS 'Acc Number',
                (vehicle_owner_details.Income - vehicle_owner_details.Charges) AS Income
                FROM bank_details 
                INNER JOIN vehicle_owner_details ON vehicle_owner_details.Email = bank_details.Email
                WHERE bank_details.Status = 'verified' AND Income > 1000 AND bank_details.Bank = ?
                UNION ALL
                SELECT bank_details.Bank , bank_details.Branch , bank_details.Beneficiary_Name AS 'Beneficiary Name', bank_details.Acc_Number AS 'Acc Number',
                driver_details.Income AS Income
                FROM bank_details 
                INNER JOIN driver_details ON driver_details.Email = bank_details.Email
                WHERE bank_details.Status = 'verified' AND Income > 1000 AND bank_details.Bank = ?";
                $pstmt = $connection->prepare($query);
                $pstmt->bindValue(1, $bank);
                $pstmt->bindValue(2, $bank);
                $pstmt->execute();
                $results[$bank] = $pstmt->fetchAll(PDO::FETCH_OBJ);
            }
            return $results;
        } catch (PDOException $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    // load account statistics function
    public function loadAccountStats($connection)
    {
        $query = "SELECT COUNT(*) AS count, Account_Type , 
                CASE 
                    WHEN Datetime BETWEEN NOW() - INTERVAL 30 DAY AND NOW() THEN 'current' 
                    WHEN Datetime BETWEEN NOW() - INTERVAL 60 DAY AND NOW() THEN 'past' 
                    ELSE 'old' 
                END AS date_range 
                FROM user WHERE Account_Type IN ('driver','customer','vehicle_owner') 
                AND Datetime BETWEEN NOW() - INTERVAL 60 DAY AND NOW() 
                GROUP BY Account_Type , date_range
                ORDER BY Account_Type , date_range";

        try {
            $pstmt = $connection->prepare($query);
            $pstmt->execute();
            return $pstmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    // load vehicle statistics function
    public function loadVehicleStats($connection)
    {
        $query = "SELECT COUNT(*) AS count, Vehicle_Type , 
                CASE 
                    WHEN Datetime BETWEEN NOW() - INTERVAL 30 DAY AND NOW() THEN 'current' 
                    WHEN Datetime BETWEEN NOW() - INTERVAL 60 DAY AND NOW() THEN 'past' 
                    ELSE 'old' 
                END AS date_range 
                FROM vehicle WHERE Vehicle_Type IN ('car','bike','tuk-tuk','van') 
                AND Datetime BETWEEN NOW() - INTERVAL 60 DAY AND NOW() 
                GROUP BY Vehicle_Type , date_range
                ORDER BY Vehicle_Type , date_range";

        try {
            $pstmt = $connection->prepare($query);
            $pstmt->execute();
            return $pstmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    // load revenue statistics function
    public function loadRevenueStats($connection)
    {
        $query = "SELECT SUM(Amount * 0.1) AS sum , 
                CASE 
                    WHEN DATE(Datetime) = DATE(NOW()) THEN 'current-1' 
                    WHEN DATE(Datetime) = DATE(NOW()) - INTERVAL 7 DAY THEN 'past-1' 
                    WHEN DATE(Datetime) = DATE(NOW()) - INTERVAL 1 DAY THEN 'current-2' 
                    WHEN DATE(Datetime) = DATE(NOW()) - INTERVAL 8 DAY THEN 'past-2' 
                    WHEN DATE(Datetime) = DATE(NOW()) - INTERVAL 2 DAY THEN 'current-3' 
                    WHEN DATE(Datetime) = DATE(NOW()) - INTERVAL 9 DAY THEN 'past-3' 
                    WHEN DATE(Datetime) = DATE(NOW()) - INTERVAL 3 DAY THEN 'current-4' 
                    WHEN DATE(Datetime) = DATE(NOW()) - INTERVAL 10 DAY THEN 'past-4'
                    WHEN DATE(Datetime) = DATE(NOW()) - INTERVAL 4 DAY THEN 'current-5' 
                    WHEN DATE(Datetime) = DATE(NOW()) - INTERVAL 11 DAY THEN 'past-5' 
                    WHEN DATE(Datetime) = DATE(NOW()) - INTERVAL 5 DAY THEN 'current-6' 
                    WHEN DATE(Datetime) = DATE(NOW()) - INTERVAL 12 DAY THEN 'past-6' 
                    WHEN DATE(Datetime) = DATE(NOW()) - INTERVAL 6 DAY THEN 'current-7' 
                    WHEN DATE(Datetime) = DATE(NOW()) - INTERVAL 13 DAY THEN 'past-7'
                    ELSE 'old' 
                END AS date_range 
                FROM payment WHERE Datetime BETWEEN NOW() - INTERVAL 14 DAY AND NOW() AND Booking_Id IS NOT NULL
                GROUP BY date_range
                ORDER BY date_range";

        try {
            $pstmt = $connection->prepare($query);
            $pstmt->execute();
            return $pstmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    // load booking statistics function
    public function loadBookingStats($connection)
    {
        $query = "SELECT COUNT(*) AS count , 
                CASE 
                    WHEN DATE(Booking_Time) = DATE(NOW()) THEN 'current-1' 
                    WHEN DATE(Booking_Time) = DATE(NOW()) - INTERVAL 7 DAY THEN 'past-1' 
                    WHEN DATE(Booking_Time) = DATE(NOW()) - INTERVAL 1 DAY THEN 'current-2' 
                    WHEN DATE(Booking_Time) = DATE(NOW()) - INTERVAL 8 DAY THEN 'past-2' 
                    WHEN DATE(Booking_Time) = DATE(NOW()) - INTERVAL 2 DAY THEN 'current-3' 
                    WHEN DATE(Booking_Time) = DATE(NOW()) - INTERVAL 9 DAY THEN 'past-3' 
                    WHEN DATE(Booking_Time) = DATE(NOW()) - INTERVAL 3 DAY THEN 'current-4' 
                    WHEN DATE(Booking_Time) = DATE(NOW()) - INTERVAL 10 DAY THEN 'past-4'
                    WHEN DATE(Booking_Time) = DATE(NOW()) - INTERVAL 4 DAY THEN 'current-5' 
                    WHEN DATE(Booking_Time) = DATE(NOW()) - INTERVAL 11 DAY THEN 'past-5' 
                    WHEN DATE(Booking_Time) = DATE(NOW()) - INTERVAL 5 DAY THEN 'current-6' 
                    WHEN DATE(Booking_Time) = DATE(NOW()) - INTERVAL 12 DAY THEN 'past-6' 
                    WHEN DATE(Booking_Time) = DATE(NOW()) - INTERVAL 6 DAY THEN 'current-7' 
                    WHEN DATE(Booking_Time) = DATE(NOW()) - INTERVAL 13 DAY THEN 'past-7'
                    ELSE 'old' 
                END AS date_range 
                FROM booking WHERE Booking_Time BETWEEN NOW() - INTERVAL 14 DAY AND NOW() AND Booking_Id IS NOT NULL
                GROUP BY date_range
                ORDER BY date_range";

        try {
            $pstmt = $connection->prepare($query);
            $pstmt->execute();
            return $pstmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }
}
