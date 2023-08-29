<?php
// for remove request blocking
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Headers:Content-Type");

use Bramus\Router\Router;
use Dotenv\Dotenv;
use EligerBackend\Controller\Controller;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$app = new Router();

$app->setNamespace('\EligerBackend');
$app->setBasePath('/');


$app->get("/", function () {
    header("Location: http://localhost:3000/");
});
$app->post("/register", function () {
    Controller::post_router("register_process");
});
$app->post("/resend", function () {
    Controller::post_router("resend_process");
});
$app->post("/verify", function () {
    Controller::post_router("verification_process");
});

$app->run();
