<?php

namespace EligerBackend\Model\Classes\Users;

use EligerBackend\Model\Classes\Connectors\EmailConnector;
use EligerBackend\Model\Classes\Connectors\WhatsAppConnector;
use Exception;
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

    // login function
    public function login($connection, $remember)
    {
        $check_query = "select * from user where Email = ?";
        $pstmt = $connection->prepare($check_query);
        $pstmt->bindValue(1, $this->email);
        $pstmt->execute();
        $result = $pstmt->fetchAll(PDO::FETCH_ASSOC);

        if ($pstmt->rowCount() === 1 && password_verify($this->password, $result[0]["Password"])) {
            if ($result[0]["Account_Status"] === "verified") {
                $this->type = $result[0]["Account_Type"];
                $this->sessionHandling($connection, $remember);
                return json_encode(array("status" => 200, "role" => $_SESSION['user']['role']));
            } elseif ($result[0]["Account_Status"] === "unverified") {
                return 16;
            } else {
                return 13;
            }
        } else {
            return 13;
        }
    }

    // session handling
    public function sessionHandling($connection, $remember, $isNewLogin = true, $currentToken = '')
    {
        // create session
        $_SESSION['user'] = array("id" => $this->email, "role" => $this->type);
        // check and add functionality of remember me option
        if ($remember) {
            // if token already created , regenarate token
            while (true) {
                $token = bin2hex(random_bytes(32));
                $check_query = "select * from roles_with_session where Token = ?";
                $pstmt = $connection->prepare($check_query);
                $pstmt->bindValue(1, $token);
                $pstmt->execute();
                if ($pstmt->rowCount() < 1) break;
            }

            // for new logins
            if ($isNewLogin) {
                $query = "insert into session (Session_Token , Email , Expire) values (? , ? , date_add(now(),interval 30 day))";
                $pstmt = $connection->prepare($query);
                $pstmt->bindValue(1, $token);
                $pstmt->bindValue(2, $this->email);
                $pstmt->execute();
            } else {
                // for already logged users
                $query = "update session set Session_Token = ? , Expire = date_add(now(),interval 30 day) where Session_Token = ?";
                $pstmt = $connection->prepare($query);
                $pstmt->bindValue(1, $token);
                $pstmt->bindValue(2, $currentToken);
                $pstmt->execute();
            }

            // set cookie for 30 days
            setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
            // setcookie('remember_token', $token, ['expires' => time() + 30 * 24 * 3600, 'path' => '/', 'domain' => 'eliger-backend.000webhostapp.com', 'samesite' => 'None', 'secure' => true, 'httponly' => true]);
        }
    }

    // login with session token
    public function loginWithToken($connection)
    {
        $token = $_COOKIE['remember_token'];

        $query = "select * from roles_with_session where Token = ?";
        $pstmt = $connection->prepare($query);
        $pstmt->bindValue(1, $token);
        $pstmt->execute();

        if ($pstmt->rowCount() === 1) {
            $result = $pstmt->fetchAll(PDO::FETCH_ASSOC);
            $this->email = $result[0]["Email"];
            $this->type = $result[0]["role"];
            // session handling
            $this->sessionHandling($connection, true, false, $token);
            return json_encode(array("status" => 200, "role" => $_SESSION['user']['role']));
        }
        return 14;
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

    // logout function
    public function logout()
    {
        session_unset();
        session_destroy();
        setcookie('remember_token', '', time() - 3600, '/');
        // setcookie('remember_token', '', ['expires' => time() - 3600, 'path' => '/', 'domain' => 'eliger-backend.000webhostapp.com', 'samesite' => 'None', 'secure' => true, 'httponly' => true]);
    }

    // update function
    public function update($connection, $field, $data)
    {
        $query = "update user set Email = ? , Account_Status = 'unverified' where Email = ?";
        if ($field === "Password")
            $query = "update user set Password = ? where Email = ?";

        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $data);
            $pstmt->bindValue(2, $_SESSION["user"]["id"]);
            $pstmt->execute();
            if ($pstmt->rowCount() === 1) {
                if ($field === "Email") {
                    $this->resendVerification("register", $connection, $data, "Verify your Eliger account", "registration");
                }
                $this->logout();
                return 200;
            } else {
                return 500;
            }
        } catch (Exception $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    // reset password
    public function resetPassword($connection, $password, $verificationCode)
    {
        try {
            if ($this->verifyResetRequest($verificationCode, $connection) === 200) {
                $query = "UPDATE user INNER JOIN verification
                ON verification.Email = user.Email 
                SET user.Password = ?
                WHERE verification.Verification_Code = ?";
                $pstmt = $connection->prepare($query);
                $pstmt->bindValue(1, $password);
                $pstmt->bindValue(2, $verificationCode);
                $pstmt->execute();
                if ($pstmt->rowCount() === 1) {
                    return $this->verify($verificationCode, $connection);
                }
            }
            return 500;
        } catch (Exception $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    // generate random 6 number OTP
    public function generateOTP($connection)
    {
        try {
            $otp = 0;
            while (true) {
                $otp = rand(100000, 999999);
                $check_query = "select * from verification where Verification_Code = ?";
                $pstmt = $connection->prepare($check_query);
                $pstmt->bindValue(1, $otp);
                $pstmt->execute();
                if ($pstmt->rowCount() < 1) break;
            }
            return $otp;
        } catch (Exception $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    // send OTP and verfiy email
    public function sendOTP($connection, $verificationCode)
    {
        try {
            $query = "SELECT users.phone , verification.Email , users.full_name FROM users INNER JOIN verification
            ON verification.Email = users.Email WHERE verification.Verification_Code = ?";
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $verificationCode);
            $pstmt->execute();
            if ($pstmt->rowCount() === 1) {
                $result = $pstmt->fetch(PDO::FETCH_ASSOC);
                if ($result["phone"] === null) {
                    return $this->verify($verificationCode, $connection); // this is H&S account
                } else {
                    $otp = $this->generateOTP($connection); // generate new OTP 
                    $query = "UPDATE verification SET Verification_Code = ? , Type = ? , RemoveTime = date_add(now(),interval 5 minute)";
                    $pstmt = $connection->prepare($query);
                    $pstmt->bindValue(1, $otp);
                    $pstmt->bindValue(2, "otp");
                    $pstmt->execute();

                    // msg for OTP
                    $msg = "Hello {$result['full_name']},\nYour OTP is: {$otp}. Please note that the OTP will be expired after 5 minutes.";
                    if (WhatsAppConnector::sendWhatsAppMessage($result["phone"], $msg)) {
                        return substr($result["phone"], 0, 4) . "xxxx" . substr($result["phone"], 8);
                    } else {
                        return 12;
                    }
                }
            } else {
                return 12;
            }
        } catch (Exception $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    // send OTP to phone number change requiest
    public function sendPhoneChangeOTP($connection, $email, $phone)
    {
        try {
            $query = "SELECT full_name FROM users WHERE email = ?";
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $email);
            $pstmt->execute();
            if ($pstmt->rowCount() === 1) {
                $result = $pstmt->fetch(PDO::FETCH_ASSOC);
                $otp = $this->generateOTP($connection); // generate new OTP 
                $query = "INSERT verification (Verification_Code , Email , Phone ,Type , RemoveTime) VALUES (?,?,?,?,date_add(now(),interval 5 minute))";
                $pstmt = $connection->prepare($query);
                $pstmt->bindValue(1, $otp);
                $pstmt->bindValue(2, $email);
                $pstmt->bindValue(3, $phone);
                $pstmt->bindValue(4, "otp");
                $pstmt->execute();

                // msg for OTP
                $msg = "Hello {$result['full_name']},\nYour OTP is: {$otp}. Please note that the OTP will be expired after 5 minutes.";
                if (WhatsAppConnector::sendWhatsAppMessage($phone, $msg)) {
                    return 200;
                } else {
                    return 12;
                }
            } else {
                return 12;
            }
        } catch (Exception $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    // verify OTP and change phone number
    public function verifyPhoneChangeOTP($connection, $accountType, $otp, $email)
    {
        try {
            $query = "SELECT Phone FROM verification WHERE Verification_Code = ?";
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $otp);
            $pstmt->execute();
            if ($pstmt->rowCount() === 1) {
                $result = $pstmt->fetch(PDO::FETCH_ASSOC);
                $query = "";
                if ($accountType === "vehicle_owner") {
                    $query = "UPDATE vehicle_owner SET Owner_Tel = ? WHERE Email = ?";
                } elseif ($accountType === "customer") {
                    $query = "UPDATE customer SET Customer_Tel = ? WHERE Email = ?";
                } elseif ($accountType === "driver") {
                    $query = "UPDATE driver SET Driver_Tel = ? WHERE Email = ?";
                }
                $pstmt = $connection->prepare($query);
                $pstmt->bindValue(1, $result["Phone"]);
                $pstmt->bindValue(2, $email);
                $pstmt->execute();
                if ($pstmt->rowCount() === 1) {
                    return $this->verify($otp, $connection);
                }
            }
        } catch (Exception $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    // account verification function
    public function verify($verificationCode, $connection)
    {
        try {
            $delete_query = "DELETE from verification where Verification_Code = ?";
            $pstmt = $connection->prepare($delete_query);
            $pstmt->bindValue(1, $verificationCode);
            $pstmt->execute();
            $rows = $pstmt->rowCount();

            if ($rows < 1) {
                return 12;
            } else {
                return 200;
            }
        } catch (Exception $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    // reset password verification function
    public function verifyResetRequest($verificationCode, $connection)
    {
        try {
            $query = "SELECT * from verification where Verification_Code = ? and Type = 'reset_pass'";
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $verificationCode);
            $pstmt->execute();
            $rows = $pstmt->rowCount();

            if ($rows < 1) {
                return 12;
            } else {
                return 200;
            }
        } catch (Exception $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    // send verification email and save code in database
    public function sendVerificationEmail($connection, $name, $verificationType, $subject, $template)
    {
        $code = '';
        while (true) {
            $randomString = md5(uniqid(rand(), true));
            $code = substr($randomString, 0, 20);
            $check_query = "select * from verification where Verification_Code = ?";
            $pstmt = $connection->prepare($check_query);
            $pstmt->bindValue(1, $code);
            $pstmt->execute();
            if ($pstmt->rowCount() < 1) break;
        }

        $query = "insert into  verification (Verification_Code , Email , Type , RemoveTime) values(? , ? , ? , date_add(now(),interval 1 day))";
        $pstmt = $connection->prepare($query);
        $pstmt->bindValue(1, $code);
        $pstmt->bindValue(2, $this->email);
        $pstmt->bindValue(3, $verificationType);
        $pstmt->execute();

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

    // resend verification email again
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

    // register function of user
    public function submitBankDetails($connection, $data)
    {
        $query = "UPDATE bank_details SET Bank = ? , Branch = ? , Beneficiary_Name = ? , Acc_Number = ? , Statement_File = ? , Status = 'pending' WHERE Email = ?";
        try {

            if ($this->bankDetailsStatus($connection) === "not submitted") {
                $query = "INSERT INTO bank_details (Bank, Branch, Beneficiary_Name, Acc_Number, Statement_File, Email) VALUES (?,?,?,?,?,?)";
            }

            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $data["bank"]);
            $pstmt->bindValue(2, $data["branch"]);
            $pstmt->bindValue(3, $data["beneficiary"]);
            $pstmt->bindValue(4, $data["acc_number"]);
            $pstmt->bindValue(5, $data["statement"]);
            $pstmt->bindValue(6, $data["email"]);
            $pstmt->execute();

            return $pstmt->rowCount() === 1;
        } catch (PDOException $ex) {
            die("Registration Error : " . $ex->getMessage());
        }
    }

    // load bank details status
    public function bankDetailsStatus($connection)
    {
        $query = "SELECT Status FROM bank_details WHERE Email = ?";
        try {
            $pstmt = $connection->prepare($query);
            $pstmt->bindValue(1, $_SESSION["user"]["id"]);
            $pstmt->execute();

            if ($pstmt->rowCount() === 1) {
                $result = $pstmt->fetch(PDO::FETCH_ASSOC);
                return $result["Status"];
            }
            return "not submitted";
        } catch (PDOException $ex) {
            die("Registration Error : " . $ex->getMessage());
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
