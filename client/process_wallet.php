<?php
include '../template.php';

$template = new Template($db);
$template->session_check('client');

$user_id = $_SESSION['user_id'];

$msg = '';
if (isset($user_id)) {
    $stmt = $db->prepare('SELECT * FROM users INNER JOIN accounts ON accounts.user_id=users.id INNER JOIN wallet ON users.id=wallet.user_id WHERE id = ? Limit 1');
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        exit('Your Customer ID could not be verified!');
    }

    if (isset($_GET['status'])) {
        //* check payment status
        if($_GET['status'] == 'failed'){
            echo "<script>alert('Your payment has been cancelled!')</script>";
            header('Location: index.php');
        }elseif ($_GET['status'] == 'cancelled') {
            // echo 'You cancel the payment';
            echo "<script>alert('Your payment has been cancelled!')</script>";
            header('Location: index.php');
        } elseif ($_GET['status'] == 'successful') {
            $txid = $_GET['transaction_id'];
            $tx_ref = $_GET['tx_ref'];

            $curl = curl_init();
            
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/{$txid}/verify",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Authorization: Bearer FLWSECK-37c44bd2b7267d4c8a751c3e551104d4-X"
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $res = json_decode($response);
            if ($res->status) {

                $amountPaid = $res->data->charged_amount;
                $amountToPay = $res->data->meta->price;

                $amount = $user['wallet_amount'] + $amountPaid;
                $payment_type = "wallet";

                if ($amountPaid >= $amountToPay) {
                    $save_data = add_user_pay($tx_ref, $user_id, $amountPaid, $payment_type);
                    $sql = "UPDATE wallet SET payment_reference='$tx_ref', wallet_amount='$amount' WHERE user_id='$user_id'";
                    $update_data = $db->query($sql);
                    echo "<script>alert('Payment successful!')</script>";
                    header('refresh:1;index.php');
                    //* Continue to give item to the user
                } else {
                    echo "<script>alert('Fraud transaction detected!')</script>";
                    header('refresh:1;index.php');
                }
            } else {
                echo "<script>alert('Can not process payment!')</script>";
                header('refresh:1;index.php');
            }
        }
    }
} else {
    exit('No ID specified!');
}

function add_user_pay($payment_id, $customer, $amount, $pay_type)
{
    global $db;
    $query = "INSERT INTO payments(order_reference, user_id, paid_amount, payment_type)";
    $query .= "VALUES(:ref, :id, :amount, :pay_type)";
    $stmt = $db->prepare($query);
    $stmt->bindValue(":ref", $payment_id);
    $stmt->bindValue(":id", $customer);
    $stmt->bindValue(":amount", $amount);
    $stmt->bindValue(":pay_type", $pay_type);
    $stmt->execute();
    $result = $stmt->rowcount();
    if ($result == 1) {
        return true;
    } else {
        return false;
    }
}

