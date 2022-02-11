<?php
include '../template.php';

$template = new Template($db);
$template->session_check('client');

$msg = '';
if (isset($_GET['id'])) {
    $stmt = $db->prepare('SELECT * FROM users INNER JOIN accounts ON accounts.user_id=users.id INNER JOIN wallet ON users.id=wallet.user_id WHERE id = ? Limit 1');
    $stmt->execute([$_GET['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        exit('Your Customer ID could not be verified!');
    }

    if (isset($_POST['pay'])) {

        $user_id = $_SESSION['user_id'];
        $payment = htmlspecialchars($_POST['payment']);
        $remaining_wallet = $user['wallet_amount'] - $payment;
        $paid = $user['payment'] + $payment;
        $amount = $user['amount'] + $payment;
        $balance = $user['balance'] - $payment;
        $prepaid_balance = $user['balance'] + $payment;
        $order_id = "SMTAP" . uniqid();
        $reason = "Smart Tap Payment";
        $payment_type = "Wallet";

        if($user['wallet_amount']>$user['balance']){
            if($payment<=$user['wallet_amount']){
                if($user['category']=="post-paid"){
                    if($payment == $user['balance']){
                        $save_data = add_user_pay($order_id, $user_id, $payment, $payment_type);
                        // Update Accounts Table
                        $sql = "UPDATE accounts SET payment='$paid', balance='$balance' WHERE user_id='$user_id'";
                        $update_data = $db->query($sql);
                        // Update Wallet Table
                        $sql2 = "UPDATE wallet SET wallet_amount='$remaining_wallet' WHERE user_id='$user_id'";
                        $update_data2 = $db->query($sql2);
                        echo "<script>alert('Payment successful!')</script>";
                        header('refresh:1;index.php');
                    }else{
                        echo "<script>alert('Please pay the exact amount!')</script>";
                        header('refresh:1;index.php');
                    }
                }elseif ($user['category']=="pre-paid") {
                    $save_data = add_user_pay($order_id, $user_id, $payment, $payment_type);
                    // Update Accounts Table
                    $sql = "UPDATE accounts SET payment='$paid', balance='$prepaid_balance' WHERE user_id='$user_id'";
                    $update_data = $db->query($sql);
                    // Update Wallet Table
                    $sql2 = "UPDATE wallet SET wallet_amount='$remaining_wallet' WHERE user_id='$user_id'";
                    $update_data2 = $db->query($sql2);
                    echo "<script>alert('Payment successful!')</script>";
                    header('refresh:1;index.php');
                }else {
                    # code...
                }
            }else {
                echo "<script>alert('Error!! Amount is greater than Wallet Balance')</script>";
                header('refresh:1;index.php');
            }
        }
        else{
            echo "<script>alert('Your wallet amount is less!')</script>";
            header('refresh:1;index.php');
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
?>

<?= $template->dashboard_header_template('Pay with Wallet', $_SESSION['name'], $_SESSION['role']) ?>
<div class="container content-view mt-5 mb-5">
    <script type="text/javascript" src="../vendor/jquery/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            setInterval(function() {
                $.get(`../paraminfo.php`, function(data) {
                    data = $.parseJSON(data);
                });
            }, 100);
        });
    </script>
    <div class="wrapper d-flex justify-content-center flex-column px-md-5 px-1">
        <form class="register-form" action="pay_with_wallet.php?id=<?= $user['id'] ?>" method="post">
            <div class="h3 text-center font-weight-bold">Pay with Wallet</div>

            <div class="row my-md-4 my-2">
                <div class="col-md-12 pt-md-0 pt-4">
                    <label>Wallet Amount</label>
                    <input class="input100" type="number" name="payment" autocomplete="off">
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button class="btn mr-2" type="submit" name="pay">Pay Balance</button>
                <a class="btn btn-danger btn-sm" href="index.php" type="btn">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?= $template->footer_template('Dashboard') ?>