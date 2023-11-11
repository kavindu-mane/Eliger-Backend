<?php

namespace EligerBackend\Model\Classes\Users;

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
}
