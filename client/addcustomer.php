<?php
include '../template.php';

$template = new Template($db);
$template->session_check('client');

$msg = '';
$errorMsg = [
    'nameErr' => '',
    'unameErr' => '',
    'emailErr' => '',
    'phoneErr' => '',
    'cardErr' => '',
    'meterErr' => '',
    'readingErr' => '',
    'payErr' => ''
];

if (isset($_REQUEST['addcust'])) {
    if(empty($_POST['name'])){
        $errorMsg['nameErr'] = 'Please enter user\'s name!';
    }

    if (empty($_POST['username'])){
        $errorMsg['unameErr'] = 'Please enter user\'s username!';
    }

    if (empty($_POST['email'])){
        $errorMsg['emailErr'] = 'Please enter user\'s email!';
    }

    if (empty($_POST['phone'])){
        $errorMsg['phoneErr'] = 'Please enter user\'s phonenumber!';
    }

    if(empty($_POST['card_no'])){
        $errorMsg['cardErr'] = 'Please enter user\'s card number!';
    }

    if (empty($_POST['payment'])){
        $errorMsg['payErr'] = 'Please enter user\'s payment!';
    }

    if (!empty($_POST['name']) AND !empty($_POST['username']) AND !empty($_POST['email']) AND !empty($_POST['phone'] AND $_POST['card_no']) AND !empty($_POST['payment'])) {

        $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : NULL;
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $phone = isset($_POST['phone']) ? $_POST['phone'] : '';

	    $card_no = isset($_POST['card_no']) ? $_POST['card_no'] : 0;
	    $payment = isset($_POST['payment']) ? $_POST['payment'] : 0;
	    $balance = $payment;
	    $status = ($balance>14)?'1':'0';

        $stmt = $db->prepare('INSERT INTO customers(`user_id`, `name`, `username`, `email`, `phone`, `card_no`, `meter_reading`, `payment`, `status`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?, now())');
        $stmt->execute([$user_id, $name, $username, $email, $phone, $card_no, $payment, $status]);

        $msg = 'Successfully Added!';
        header('location: customers.php');
    }
}
?>

<?=$template->dashboard_header_template('Add Customer', $_SESSION['name'], $_SESSION['role'])?>
<div class="container content-view mt-5 mb-5">
    <div class="wrapper d-flex justify-content-center flex-column px-md-5 px-1">
        <form class="register-form" action="addcustomer.php" method="post">
            <div class="h3 text-center font-weight-bold">Register New Subclient
            </div>
            <div class="row my-4">
                <div class="col-md-6">
                    <label>Full Name:
                    <?php if ($errorMsg): ?>
                        <p class="text-danger"><?=$errorMsg['nameErr']?></p>
                    <?php endif; ?>
                    </label>
                    <input class="input100" type="text" name="name" autocomplete="off" placeholder="Enter Name">
                    <input class="input100" hidden="hidden" type="number" name="user_id" autocomplete="off" value="<?=$_SESSION['user_id']?>">
                </div>
                <div class="col-md-6 pt-md-0 pt-4">
                    <label>EMail:
                    <?php if ($errorMsg): ?>
                        <p class="text-danger"><?=$errorMsg['emailErr']?></p>
                    <?php endif; ?>
                    </label>
                    <input class="input100" type="email" name="email" autocomplete="off" placeholder="Enter Email">
                </div>
            </div>
            <div class="row my-md-4 my-2">
                <div class="col-md-6">
                    <label>UserName:
                    <?php if ($errorMsg): ?>
                        <p class="text-danger"><?=$errorMsg['unameErr']?></p>
                    <?php endif; ?>
                    </label>
                    <input class="input100" type="text" name="username" autocomplete="off" placeholder="Enter UserName">
                </div>
                <div class="col-md-6 pt-md-0 pt-4">
                    <label>PhoneNumber:
                    <?php if ($errorMsg): ?>
                        <p class="text-danger"><?=$errorMsg['phoneErr']?></p>
                    <?php endif; ?>
                    </label>
                    <input class="input100" type="tel" name="phone" autocomplete="off" placeholder="Enter PhoneNumber">
                </div>
            </div>
            <div class="row my-md-4 my-2">
                <div class="col-md-6">
                    <label>CardNumber:
                    <?php if ($errorMsg): ?>
                        <p class="text-danger"><?=$errorMsg['cardErr']?></p>
                    <?php endif; ?>
                    </label>
                    <input class="input100" type="text" name="card_no" autocomplete="off" placeholder="Enter Card Number">
                </div>
                <div class="col-md-6 pt-md-0 pt-4">
                    <label>Payment:
                    <?php if ($errorMsg): ?>
                        <p class="text-danger"><?=$errorMsg['payErr']?></p>
                    <?php endif; ?>
                    </label>
                    <input class="input100" type="number" name="payment" autocomplete="off" placeholder="Enter Payment">
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button class="btn mr-2" name="addcust" type="submit">Submit</button>
                <a class="btn btn-danger btn-sm" href="customers.php" type="btn">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?=$template->footer_template('Dashboard')?>
