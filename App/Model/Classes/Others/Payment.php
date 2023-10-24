<?php

namespace EligerBackend\Model\Classes\Others;

use PDO;
use PDOException;

class Payment
{
    private $bookingId;
    private $amount;
    private $paymentType;
    public function __construct()
    {
    }

    public function pay($connection)
    {
        $query = "INSERT INTO payment(Booking_Id, Payment_type, Amount, Datetime) VALUES (?,?,?,NOW())";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $this->bookingId);
            $pstmt->bindValue(2, $this->paymentType);
            $pstmt->bindValue(3, $this->amount);
            $pstmt->execute();
            return $pstmt->rowCount() === 1;
        } catch (PDOException $ex) {
            die("Error occurred : " . $ex->getMessage());
        }
    }

    public function loadCustomerPayments($connection, $id, $offset)
    {
        $query = "WITH PaginatedResults AS (
                SELECT payment.* , booking.Booking_Type FROM payment 
                INNER JOIN booking ON payment.Booking_Id = booking.Booking_Id 
                WHERE booking.Customer_Id = ?)
                SELECT *, (SELECT COUNT(*) FROM PaginatedResults) AS total_rows
                FROM PaginatedResults
                ORDER BY Datetime DESC
                LIMIT 15 OFFSET $offset";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $id);
            $pstmt->execute();
            if ($pstmt->rowCount() >= 1) {
                return json_encode($pstmt->fetchAll(PDO::FETCH_OBJ));
            }
        } catch (PDOException $ex) {
            die("Loading Error : " . $ex->getMessage());
        }
    }

    // getters
    public function getBookingId()
    {
        return $this->bookingId;
    }
    public function getAmount()
    {
        return $this->amount;
    }
    public function getPaymentType()
    {
        return $this->paymentType;
    }
    // setters
    public function setBookingId($bookingId)
    {
        $this->bookingId = $bookingId;
    }
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }
    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;
    }
}
