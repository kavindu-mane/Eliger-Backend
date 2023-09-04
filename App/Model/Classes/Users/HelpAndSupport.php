<?php

namespace EligerBackend\model\classes\Users;

use EligerBackend\Model\Classes\Users\User;
use PDOException;
use PDO;

class HelpAndSupport extends User
{
    private $name;
    private $email;
    public function __construct($email, $password, $type, $name)
    {
        parent::__construct($email, $password, $type);
        $this->name = $name;
        $this->email = $email;
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

                parent::sendVerificationEmail($connection, "{$this-> name} {$this-> email}", "Help&Support", "Registration of Help & Support account", "registration");

                return true;
            } catch (PDOException $ex) {
                die("Registration Error : " . $ex->getMessage());
            }
        }
    }
    public function loadManageBooking($connection,$Origin_Place,$Destination_Place,$Type){
        $query = "select * from booking where Booking_Id=?";

        
        try {
                $pstmt = $connection->prepare($query);
                $pstmt->bindValue(1, $Origin_Place);
                $pstmt->bindValue(1, $Destination_Place);
                $pstmt->bindValue(1, $Type);
                
                $pstmt->execute();
                return $pstmt->fetchAll(PDO::FETCH_OBJ);
            } catch (PDOException $ex) {
                die(" Error : " . $ex->getMessage());
            }

    }
    public function loadManageVehicle($connection,$Vehicle_Type,$Vehicle_PlateNumber,$Passenger_amount){
        $query = "select * from vehicle where Vehicle_Id=?";

        
        try {
                $pstmt = $connection->prepare($query);
                $pstmt->bindValue(1, $Vehicle_Type);
                $pstmt->bindValue(1, $Vehicle_PlateNumber);
                $pstmt->bindValue(1, $Passenger_amount);
                
                $pstmt->execute();
                return $pstmt->fetchAll(PDO::FETCH_OBJ);
            } catch (PDOException $ex) {
                die(" Error : " . $ex->getMessage());
            }

    }
    public function loadManageFeedback($connection,$Customer_Name,$Feedback_Description){
        $query = "select * from feedback where Feedback=?";

        
        try {
                $pstmt = $connection->prepare($query);
                $pstmt->bindValue(1, $Customer_Name);
                $pstmt->bindValue(1, $Feedback_Description);
                
                
                $pstmt->execute();
                return $pstmt->fetchAll(PDO::FETCH_OBJ);
            } catch (PDOException $ex) {
                die(" Error : " . $ex->getMessage());
            }

    }
}