<?php

namespace EligerBackend\Model\Classes\Connectors;

class WhatsAppConnector
{
    public static function sendWhatsAppMessage($number, $msg)
    {
        // Initialize cURL session
        $ch = curl_init();

        // Set the URL
        curl_setopt($ch, CURLOPT_URL, 'https://messages-sandbox.nexmo.com/v1/messages');

        // Set HTTP Basic Authentication username and password
        curl_setopt($ch, CURLOPT_USERPWD, "{$_ENV['WHATSAPP_API_KEY']}:{$_ENV['WHATSAPP_API_SECRET']}");

        // Set the HTTP headers
        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json',
        );

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Set the request method to POST
        curl_setopt($ch, CURLOPT_POST, true);

        // Set the request data in JSON format
        $data = array(
            'from' => '14157386102',
            'to' => $number,
            'message_type' => 'text',
            'text' => $msg,
            'channel' => 'whatsapp',
        );

        $json_data = json_encode($data);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

        // Return the response as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the cURL request
        $response = curl_exec($ch);

        curl_close($ch);
        return true;
    }
}
