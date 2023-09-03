<?php

namespace EligerBackend\model\classes\Users;

use EligerBackend\Model\Classes\Users\User;
use PDOException;

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
}
