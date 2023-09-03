<?php

use EligerBackend\Model\Classes\Users\User;
// start session
session_set_cookie_params(['SameSite' => 'None', 'Secure' => true]); 
session_start();

$user = new User();
$user->logout();
echo 200;
exit();
