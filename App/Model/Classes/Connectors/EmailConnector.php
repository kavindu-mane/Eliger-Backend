<?php

namespace EligerBackend\model\classes\Connectors;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class EmailConnector
{
    public static function getEmailConnection(): PHPMailer
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->Username = $_ENV["EMAIL_USER"];
        $mail->Password = $_ENV["EMAIL_PASSWORD"];

        try {
            $mail->setFrom($_ENV["EMAIL_USER"], "Eliger Technical Team", 0);
        } catch (Exception $ex) {
            echo $ex->errorMessage();
        }
        return $mail;
    }
}
