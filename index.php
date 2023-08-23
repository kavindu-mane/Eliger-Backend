<?php

use Bramus\Router\Router;
use Dotenv\Dotenv;
// use EligerBackend\controller\Controller;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$app = new Router();

$app->setNamespace('\EligerBackend');
$app->setBasePath('/');

$app->get("/", function () {
   header("Location: http://localhost:3000/");
});
$app->run();