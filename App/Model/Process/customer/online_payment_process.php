<?php
if (isset($_SESSION["user"])) {
    if (isset($_POST["amount"])) {
        if (
            filter_var($_POST["amount"], FILTER_VALIDATE_FLOAT)
        ) {
            $merchant_id = "1224564";
            $order_id = uniqid();
            $amount = $_POST["amount"];
            $merchant_secret = $_ENV["MERCHANT_SECRET"];
            $currency = "LKR";

            $hash = strtoupper(
                md5(
                    $merchant_id .
                        $order_id .
                        number_format($amount, 2, '.', '') .
                        $currency .
                        strtoupper(md5($merchant_secret))
                )
            );
            $arr = array();
            $arr["merchant_id"] = $merchant_id;
            $arr["order_id"] = $order_id;
            $arr["amount"] = $amount;
            $arr["hash"] = $hash;
            $arr["currency"] = $currency;
            echo json_encode($arr);
            exit();
        }
    }
} else {
    echo 14;
    exit();
}
echo 500;
exit();
