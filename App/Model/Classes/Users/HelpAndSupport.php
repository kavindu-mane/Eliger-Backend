<?php

namespace EligerBackend\model\classes\Users;

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

                parent::sendVerificationEmail($connection, "{$this->name} {$this->email}", "Help&Support", "Registration of Help & Support account", "registration");

                return true;
            } catch (PDOException $ex) {
                die("Registration Error : " . $ex->getMessage());
            }
        }
    }
    public function loadManageBooking($connection,$status)
    {
        $query = "select * from booking where Booking_Status=?";


        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $status);


            $pstmt->execute();
            return $pstmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $ex) {
            die(" Error : " . $ex->getMessage());
        }
    }
    public function loadManageVehicles($connection, $status)
    {
        $query = "select * from vehicle where Status=?";


        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $status);

            $pstmt->execute();
            return $pstmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $ex) {
            die(" Error : " . $ex->getMessage());
        }
    }


    public function loadManageFeedback($connection)
    {
        $query = "select * from feedbck";

        try {
            $pstmt = $connection->prepare($query);
            $pstmt->execute();
            return $pstmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $ex) {
            die(" Error : " . $ex->getMessage());
        }
    }
}
