<?php

namespace EligerBackend\model\classes\Users;
use EligerBackend\Model\Classes\Users\User;

class Driver extends User
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
