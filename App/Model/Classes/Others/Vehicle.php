<?php

namespace EligerBackend\Model\Classes\Others;

class Vahicle
{
    private $vehicleType;
    private $plateNumber;
    private $status = "pending";
    private $ownershipDoc;
    private $insurance;
    private $passengerAmount;
    private $rentOutLocation;
    private $price;
    private $rentType;
    private $driver;

    public function __construct()
    {
        $arguments = func_get_args();
        $numberOfArguments = func_num_args();

        if (method_exists($this, $function = '_construct' . $numberOfArguments)) {
            call_user_func_array(array($this, $function), $arguments);
        }
    }

    public function _construct6(
        $vehicleType,
        $plateNumber,
        $ownershipDoc,
        $insurance,
        $passengerAmount,
        $rentOutLocation,
        $price,
        $rentType,
        $driver
    ) {
        $this->vehicleType = $vehicleType;
        $this->plateNumber = $plateNumber;
        $this->ownershipDoc = $ownershipDoc;
        $this->insurance = $insurance;
        $this->passengerAmount = $passengerAmount;
        $this->rentOutLocation = $rentOutLocation;
        $this->price = $price;
        $this->rentType = $rentType;
        $this->driver = $driver;
    }

    public function _construct0()
    {
    }

    // add new vehicle
    public function addVehicle($connection)
    {
    }

    // getters
    public function getVehicleType()
    {
        return $this->vehicleType;
    }
    public function getPlateNumber()
    {
        return $this->plateNumber;
    }
    public function getOwnershipDoc()
    {
        return $this->ownershipDoc;
    }
    public function getInsurance()
    {
        return $this->insurance;
    }
    public function getPassengerAmount()
    {
        return $this->passengerAmount;
    }
    public function getRentOutLocation()
    {
        return $this->rentOutLocation;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function getPrice()
    {
        return $this->price;
    }
    public function getRentType()
    {
        return $this->rentType;
    }
    public function getDriver()
    {
        return $this->driver;
    }
}
