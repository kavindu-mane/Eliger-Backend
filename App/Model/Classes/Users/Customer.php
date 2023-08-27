<?php

namespace EligerBackend\model\classes\Users;

use EligerBackend\Model\Classes\Connectors\EmailConnector;
use EligerBackend\Model\Classes\Users\User;
use PDOException;

class Customer extends User
{
    private $phone;
    private $firstName;
    private $lastName;

    public function __construct($email, $password, $type, $phone, $firstName, $lastName)
    {
        parent::__construct($email, $password, $type);
        $this->phone = $phone;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
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

                $code = preg_replace('/[^a-zA-Z0-9]/m', '', password_hash($this->getEmail(), PASSWORD_BCRYPT));
                $verification_query = "insert into  verification (Verification_Code , Email , Type , RemoveTime) values(? , ? , ? , date_add(now(),interval 1 day))";
                $verification_pstmt = $connection->prepare($verification_query);
                $verification_pstmt->bindValue(1, $code);
                $verification_pstmt->bindValue(2, $this->getEmail());
                $verification_pstmt->bindValue(3, "register");
                $verification_pstmt->execute();

                // send verification email
                $email_template = __DIR__ . '/Email_Templates/registration.html';
                $message = file_get_contents($email_template);
                $message = str_replace('%user_name%', "{$this->firstName} {$this->lastName}", $message);
                $message = str_replace('%user_email%', $this->getEmail(), $message);
                $message = str_replace('%code%', $code, $message);
                $email_connection = EmailConnector::getEmailConnection();

                $email_connection->msgHTML($message);
                $email_connection->addAddress($this->getEmail(), "{$this->firstName} {$this->lastName}");
                $email_connection->Subject = "Verify your Eliger account";
                $email_connection->send();

                return true;
            } catch (PDOException $ex) {
                die("Registration Error : " . $ex->getMessage());
            }
        }
    }

    // update function
    public function update()
    {
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
