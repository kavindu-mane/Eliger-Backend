<?php
// for remove request blocking
header("Access-Control-Allow-Origin: http://localhost:3000");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Headers:Content-Type");

// start session
session_set_cookie_params(['SameSite' => 'None', 'Secure' => true]);
session_start();

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
$app->post("/update", function () {
    Controller::post_router("update_process");
});
$app->post("/session", function () {
    Controller::post_router("session_login_process");
});
$app->post("/logout", function () {
    Controller::post_router("logout_process");
});
$app->post("/get_nearest", function () {
    Controller::post_router("get_nearest_process");
});
$app->post("/get_vehicle_by_district", function () {
    Controller::post_router("get_vehicle_by_district_process");
});
$app->post("/finish_booking", function () {
    Controller::post_router("finish_booking_process");
});
$app->post("/manage_booking_status", function () {
    Controller::post_router("manage_booking_status_process");
});
$app->post("/send_reset_email", function () {
    Controller::post_router("send_reset_email_process");
});
$app->post("/verify_reset_request", function () {
    Controller::post_router("verify_reset_request_process");
});
$app->post("/reset_password", function () {
    Controller::post_router("reset_password_process");
});
$app->post("/verify_otp", function () {
    Controller::post_router("verify_otp_process");
});
$app->post("/login_driver_mobile", function () {
    Controller::post_router("login_driver_mobile_process");
});
$app->post("/verify_phone_change", function () {
    Controller::post_router("verify_phone_change_process");
});
$app->post("/request_phone_change", function () {
    Controller::post_router("request_phone_change_process");
});
$app->post("/submit_bank_details", function () {
    Controller::post_router("submit_bank_details_process");
});
$app->post("/check_bank_details", function () {
    Controller::post_router("check_bank_details_process");
});
$app->post("/load_bank_details", function () {
    Controller::post_router("load_bank_details_process");
});

// admin
$app->post("/create_hns", function () {
    Controller::post_router("/admin/create_hns_process");
});
$app->post("/load_accounts", function () {
    Controller::post_router("/admin/load_accounts_process");
});
$app->post("/load_new_reg", function () {
    Controller::post_router("/admin/load_new_reg_process");
});
$app->post("/document_validate", function () {
    Controller::post_router("/admin/document_validate_process");
});
$app->post("/disable_user", function () {
    Controller::post_router("/admin/disable_user_process");
});

// help and support
$app->post("/load_vehicles", function () {
    Controller::post_router("/hns/load_vehicles_process");
});
$app->post("/load_bookings", function () {
    Controller::post_router("/hns/load_booking_process");
});
$app->post("/load_feedbacks", function () {
    Controller::post_router("/hns/load_feedback_process");
});

// customer
$app->post("/get_customer", function () {
    Controller::post_router("/customer/get_customer_process");
});
$app->post("/update_customer", function () {
    Controller::post_router("/customer/customer_update_process");
});
$app->post("/add_booking", function () {
    Controller::post_router("/customer/add_booking_process");
});
$app->post("/get_customer_booking", function () {
    Controller::post_router("/customer/get_customer_booking_process");
});
$app->post("/add_feedback", function () {
    Controller::post_router("/customer/add_feedback_process");
});
$app->post("/cancel_booking", function () {
    Controller::post_router("/customer/cancel_booking_process");
});
$app->post("/load_customer_payments", function () {
    Controller::post_router("/customer/load_customer_payments_process");
});
$app->post("/get_vehicle_current_location", function () {
    Controller::post_router("/customer/get_vehicle_current_location_process");
});
$app->post("/online_payment", function () {
    Controller::post_router("/customer/online_payment_process");
});
$app->post("/delete_customer", function () {
    Controller::post_router("/customer/delete_customer_process");
});

// owner
$app->post("/get_owner", function () {
    Controller::post_router("/owner/get_owner_process");
});
$app->post("/update_owner", function () {
    Controller::post_router("/owner/owner_update_process");
});
$app->post("/create_driver", function () {
    Controller::post_router("/owner/create_driver_process");
});
$app->post("/add_vehicle", function () {
    Controller::post_router("/owner/add_vehicle_process");
});
$app->post("/update_vehicle", function () {
    Controller::post_router("/owner/update_vehicle_process");
});
$app->post("/load_owner_property", function () {
    Controller::post_router("/owner/load_owner_property_process");
});
$app->post("/get_rentout_booking", function () {
    Controller::post_router("/owner/get_rentout_booking_process");
});
$app->post("/get_upcomming_rentout_booking", function () {
    Controller::post_router("/owner/get_upcomming_rentout_booking_process");
});
$app->post("/load_owner_home_details", function () {
    Controller::post_router("/owner/load_owner_home_details_process");
});
$app->post("/load_drivers_daily_income", function () {
    Controller::post_router("/owner/load_drivers_daily_income_process");
});
$app->post("/load_owner_payments", function () {
    Controller::post_router("/owner/load_owner_payments_process");
});
$app->post("/load_available_drivers", function () {
    Controller::post_router("/owner/load_available_drivers_process");
});

// driver
$app->post("/get_driver", function () {
    Controller::post_router("/driver/get_driver_process");
});
$app->post("/update_driver", function () {
    Controller::post_router("/driver/driver_update_process");
});
$app->post("/load_driver_booknow_bookings", function () {
    Controller::post_router("/driver/load_driver_booknow_bookings_process");
});
$app->post("/load_driver_rentout_bookings", function () {
    Controller::post_router("/driver/load_driver_rentout_bookings_process");
});
$app->post("/vehicle_availability_change", function () {
    Controller::post_router("/driver/vehicle_availability_change_process");
});
$app->post("/vehicle_location_change", function () {
    Controller::post_router("/driver/vehicle_location_change_process");
});
$app->post("/load_driver_payment", function () {
    Controller::post_router("/driver/load_driver_payment_process");
});

$app->run();
