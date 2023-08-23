<?php

namespace EligerBackend\Controller;

class Controller
{
    public static function post_router($page): void
    {
        include_once "../app/model/process/$page.php";
    }
}
