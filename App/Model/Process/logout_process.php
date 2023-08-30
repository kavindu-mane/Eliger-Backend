<?php

use EligerBackend\Model\Classes\Users\User;

if (isset($_POST["logout"])) {
    $user = new User();
    $user->logout();
    echo 200;
    exit();
}
