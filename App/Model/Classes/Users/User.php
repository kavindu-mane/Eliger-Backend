<?php

namespace EligerBackend\Model\Classes\Users;

use EligerBackend\Model\Classes\Connectors\EmailConnector;
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

    // defaulty constructor 
    public function _construct0()
    {
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

    public function verify($vericationCode, $connection)
    {
        $delete_query = "delete from verification where Verification_Code = ?";
        $pstmt = $connection->prepare($delete_query);
        $pstmt->bindValue(1, $vericationCode);
        $pstmt->execute();
        $rows = $pstmt->rowCount();

        if ($rows < 1) {
            return 12;
        } else {
            return 200;
        }
    }

    public function disableUser()
    {
    }

    public function reportUser()
    {
    }

    // send verification email and save code in database
    public function sendVerificationEmail($connection, $name, $verificationType, $subject, $template)
    {
        $query = "insert into  verification (Verification_Code , Email , Type , RemoveTime) values(? , ? , ? , date_add(now(),interval 1 day))";
        $pstmt = $connection->prepare($query);
        $rows = 0;
        while ($rows < 1) {
            $randomString = md5(uniqid(rand(), true));
            $code = substr($randomString, 0, 20);
            $pstmt->bindValue(1, $code);
            $pstmt->bindValue(2, $this->email);
            $pstmt->bindValue(3, $verificationType);
            $pstmt->execute();
            $rows = $pstmt->rowCount();
        }

        $email_template = __DIR__ . "/Email_Templates/{$template}.html";
        $message = file_get_contents($email_template);
        $message = str_replace('%user_name%', $name, $message);
        $message = str_replace('%user_email%', $this->email, $message);
        $message = str_replace('%code%', $code, $message);
        $email_connection = EmailConnector::getEmailConnection();

        $email_connection->msgHTML($message);
        $email_connection->addAddress($this->email, $name);
        $email_connection->Subject = $subject;
        $email_connection->send();
    }

    public function resendVerification($type, $connection, $email, $subject, $template)
    {
        // check code exist or not in database , if code exist only send email with verification code.
        $check_query = "select * from verification where Email = ? and Type = ?";
        $pstmt = $connection->prepare($check_query);
        $pstmt->bindValue(1, $email);
        $pstmt->bindValue(2, $type);
        $pstmt->execute();
        $result = $pstmt->fetchAll(PDO::FETCH_ASSOC);
        $rows = $pstmt->rowCount();

        // get user full name 
        $name_query = "select * from users where Email = ?";
        $pstmt_name = $connection->prepare($name_query);
        $pstmt_name->bindValue(1, $email);
        $pstmt_name->execute();
        $result_name = $pstmt_name->fetchAll(PDO::FETCH_ASSOC);

        if ($rows < 1) {
            // genarate code and send email
            $this->email = $email;
            $this->sendVerificationEmail($connection, $result_name[0]["full_name"], $type, $subject, $template);
            return 200;
        } else {
            // send email only
            $email_template = __DIR__ . "/Email_Templates/{$template}.html";
            $message = file_get_contents($email_template);
            $message = str_replace('%user_name%', $result_name[0]["full_name"], $message);
            $message = str_replace('%user_email%', $email, $message);
            $message = str_replace('%code%', $result[0]["Verification_Code"], $message);
            $email_connection = EmailConnector::getEmailConnection();

            $email_connection->msgHTML($message);
            $email_connection->addAddress($email, $result_name[0]["full_name"]);
            $email_connection->Subject = $subject;
            $email_connection->send();
            return 200;
        }
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
