<?php

namespace EligerBackend\model\classes\Users;

use PDO;
use PDOException;

class Admin extends User
{
    // registering of help & support staff member
    public function createHelpAccount($connection, $name, $email, $password)
    {
        $hns = new HelpAndSupport($email, $password, "hands", $name);
        if($hns->register($connection)){
            return 200;
        }
        return 500;

    }

    //load Accounts
    public function loadAccountDetails($connection,$account_type,$status){
        $query = "select * from customer_details where Account_Status=?";

        if($account_type === "driver"){
            $query = "select * from driver_details where Account_Status=?";

        }elseif($account_type === "hands"){
            $query = "select * from hands_details where Account_Status=?";
        }elseif($account_type === "vehicle_owner"){
            $query = "select * from vehicle_owner_details where Account_Status=?";
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

    // disable user
    public function disableUser($connection)
    {
    }

    // Review Document function
    public function reviewDocument($connection)
    {
    }
}
