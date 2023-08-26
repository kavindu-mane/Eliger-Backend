<?php

namespace EligerBackend\Model\Classes\Users;

use PDO;
use PDOException;

class User
{
    private $email;
    private $password;
    private $type;
    private $accStatus = "unverified";


    public function __construct()
    {
        $arguments = func_get_args();
        $numberOfArguments = func_num_args();

        if (method_exists($this, $function = '_construct' . $numberOfArguments)) {
            call_user_func_array(array($this, $function), $arguments);
        }
    }

    // constructor for login
    public function _construct2($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    // constructor for register
    public function _construct3($email, $password, $type)
    {
        $this->email = $email;
        $this->password = $password;
        $this->type = $type;
    }

    // check given email already exist or not
    public static function isNewUser($email, $connection)
    {
        $query = "select * from user where Email = ?";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $email);
            $pstmt->execute();
            $result = $pstmt->fetchAll(PDO::FETCH_ASSOC);
            return empty($result);
        } catch (PDOException $ex) {
            die("Error occurred : " . $ex->getMessage());
        }
    }

    public function login()
    {
    }

    // register function of user
    public function register($connection)
    {
        $query = "insert into user (Email , Password , Account_Status, Account_Type ) values(?,?,?,?)";
        try {

            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $this->email);
            $pstmt->bindValue(2, password_hash($this->password, PASSWORD_BCRYPT));
            $pstmt->bindValue(3, $this->accStatus);
            $pstmt->bindValue(4, $this->type);
            $pstmt->execute();

            //          send verification email
            // $email_connection->addAddress($this->email, $this->name);
            // $email_connection->Subject = "Verify your Elezione account";
            // $email_connection->Body = "Hi " . $this->name . " ,\n\nPlease verify your Elezione account using this link.\n\nhttp://localhost:8080/verification?verify=" . $this->verificationCode;

            // $email_connection->send();

            return true;
        } catch (PDOException $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    public function logout()
    {
    }

    public function update()
    {
    }

    public function verify($vericationId)
    {
    }

    public function disableUser()
    {
    }

    public function reportUser()
    {
    }

    // getters
    public function getEmail()
    {
        return $this->email;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getIsVerified()
    {
        return $this->accStatus;
    }
}
