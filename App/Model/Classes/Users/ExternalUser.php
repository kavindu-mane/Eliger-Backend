<?php

namespace EligerBackend\model\classes\Users;

class ExternalUser extends User
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
    public function register()
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
