<?php
include '../template.php';

$template = new Template($db);
$template->session_check('client');

$msg = '';
if (isset($_GET['id'])) {
    $stmt = $db->prepare('SELECT * FROM users INNER JOIN accounts ON accounts.user_id=users.id INNER JOIN wallet ON users.id=wallet.user_id  WHERE id = ? Limit 1');
    $stmt->execute([$_GET['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        exit('Your Customer ID could not be verified!');
    }

    if (isset($_POST['pay'])) {

        $user_id = $_SESSION['user_id'];
        $payment = htmlspecialchars($_POST['payment']);
        $order_id = "Wallet" . uniqid();
        $reason = "Smart Tap Payment";
        $payment_type = "Mobile Money";

        //* Prepare our rave request
        $request = [
            'tx_ref' => $order_id,
            'amount' => $payment,
            'currency' => 'UGX',
            'payment_options' => 'mobile_money_uganda,card',
            'redirect_url' => 'https://sostechmakers.com/smart-tap/client/process_wallet.php',
            'customer' => [
                'email' => $user['email'],
                'name' => $user['name']
            ],
            'meta' => [
                'price' => $payment
            ],
            'customizations' => [
                'title' => 'Smart Tap Uganda',
                'description' => 'Payment for Smart Tap Wallet'
            ]
        ];

        // smarttap.us.tempcloudsite.com
        //* Ca;; f;iterwave emdpoint
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.flutterwave.com/v3/payments',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($request),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer FLWSECK-37c44bd2b7267d4c8a751c3e551104d4-X',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $res = json_decode($response);
        if ($res->status == 'success') {
            $link = $res->data->link;
            header('Location: ' . $link);
        } else {
            echo "<script>alert('We can not process your payment!')</script>";
            header('refresh:1;index.php');
        }
    }
} else {
    exit('No ID specified!');
}

?>

<?= $template->dashboard_header_template('Load Wallet', $_SESSION['name'], $_SESSION['role']) ?>
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
        <form class="register-form" action="load_wallet.php?id=<?= $user['id'] ?>" method="post">
            <div class="h3 text-center font-weight-bold">Load Wallet</div>
            <div class="row my-4">
                <div class="col-md-6">
                    <label>Your Names</label>
                    <input class="input100" type="text" name="name-echo" autocomplete="off" disabled value="<?= $user['name'] ?>">
                    <input class="input100" hidden="hidden" type="text" name="name" autocomplete="off" value="<?= $user['name'] ?>">
                    <input class="input100" hidden="hidden" type="number" name="id" autocomplete="off" value="<?= $user['id'] ?>">
                </div>
                <div class="col-md-6 pt-md-0 pt-4">
                    <label>Your Email</label>
                    <input class="input100" type="email" name="email-echo" autocomplete="off" disabled value="<?= $user['email'] ?>">
                    <input class="input100" hidden="hidden" type="email" name="email" autocomplete="off" value="<?= $user['email'] ?>">
                </div>
            </div>
            <div class="row my-md-4 my-2">
                <div class="col-md-6 pt-md-0 pt-4">
                    <label>Phone Number</label>
                    <input class="input100" type="tel" name="phone-echo" autocomplete="off" disabled value="<?= $user['phone'] ?>">
                    <input class="input100" hidden="hidden" type="tel" name="phone" autocomplete="off" value="<?= $user['phone'] ?>">
                </div>
                <div class="col-md-6 pt-md-0 pt-4">
                    <label>Desired Amount</label>
                    <input class="input100" type="number" name="payment" required autocomplete="off">
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button class="btn mr-2" type="submit" name="pay">Submit</button>
                <a class="btn btn-danger btn-sm" href="index.php" type="btn">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?= $template->footer_template('Dashboard') ?>