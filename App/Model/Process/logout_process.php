<?php

use EligerBackend\Model\Classes\Users\User;

$user = new User();
$user->logout();
echo 200;
exit();
