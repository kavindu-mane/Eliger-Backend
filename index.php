<?php
// for remove request blocking
$http_origin = $_SERVER['HTTP_ORIGIN'];
if ($http_origin == "http://localhost:8080" || $http_origin == "http://localhost:3000") {
    header("Access-Control-Allow-Origin: $http_origin");
} else {
    header("Access-Control-Allow-Origin:*");
}
header('Access-Control-Allow-Credentials: true');
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
$app->post("/login", function () {
    Controller::post_router("login_process");
});
$app->post("/session", function () {
    Controller::post_router("session_login_process");
});
$app->post("/logout", function () {
    Controller::post_router("logout_process");
});
$app->post("/create_hns", function () {
    Controller::post_router("/admin/create_hns_process");
});
$app->post("/load_accounts", function () {
    Controller::post_router("/admin/load_accdetails_process");
});
$app->post("/load_new_reg", function () {
    Controller::post_router("/admin/load_new_reg_process");
});

$app->run();
