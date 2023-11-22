<?php

namespace EligerBackend\Model\Classes\Connectors;

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

    public static function sendActionEmail($name, $msg, $email, $subject)
    {
        $email_template = __DIR__ . "/Email_Templates/actions.html";
        $message = file_get_contents($email_template);
        $message = str_replace('%user_name%', $name, $message);
        $message = str_replace('%message%', $msg, $message);
        $email_connection = EmailConnector::getEmailConnection();

        $email_connection->msgHTML($message);
        $email_connection->addAddress($email, $name);
        $email_connection->Subject = $subject;
        $email_connection->send();
    }
}
