<?php
include '../template.php';

$template = new Template($db);
$template->session_check('admin');

$msg = '';
$errorMsg = [
    'nameErr' => '',
    'unameErr' => '',
    'emailErr' => '',
    'phoneErr' => '',
    'passErr' => '',
    'locationErr' => '',
    'meterErr' => '',
    'readingErr' => '',
    'payErr' => ''
];

if (isset($_REQUEST['addaccou'])) {
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

    if (empty($_POST['location'])){
        $errorMsg['locationErr'] = 'Please enter user\'s location!';
    }

    if (empty($_POST['meter_no'])){
        $errorMsg['meterErr'] = 'Please enter  user\'s meter number!';
    }

    if (!empty($_POST['name']) AND !empty($_POST['username']) AND !empty($_POST['email']) AND !empty($_POST['phone'] AND $_POST['location']) AND !empty($_POST['meter_no'])) {
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
        $location = isset($_POST['location']) ? $_POST['location'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $meter_no = isset($_POST['meter_no']) ? $_POST['meter_no'] : '';
        $category = isset($_POST['category']) ? $_POST['category'] : '';

        $stmt = $db->prepare('INSERT INTO users(`name`, `username`, `email`, `phone`, `location`, `password`, `role`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, "client", now())');
        $stmt->execute([$name, $username, $email, $phone, $location, $password]);
        $user_id = $db->lastInsertId();
        $stmt = $db->prepare('INSERT INTO accounts(`user_id`, `meter_no`, `meter_reading`, `amount`, `payment`, `balance`, `gps_cordinates`, `category`, `status`, `created_at`) VALUES (?, ?, 0, 0, 0, 0, "", ?, "1", now())');
        $stmt->execute([$user_id, $meter_no, $category]);
        $msg = 'Successfully Added!';
        header('Location: accounts.php');
    }
}
?>

<?=$template->dashboard_header_template('Add Account', $_SESSION['name'], $_SESSION['role'])?>
<div class="container content-view mt-5 mb-5">
    <div class="wrapper d-flex justify-content-center flex-column px-md-5 px-1">
        <form class="register-form" action="addaccount.php" method="post">
            <div class="h3 text-center font-weight-bold">Register New Client</div>
            <div class="row my-4">
                <div class="col-md-6">
                    <label>Full Name:
                    <?php if ($errorMsg): ?>
                        <p class="text-danger"><?=$errorMsg['nameErr']?></p>
                    <?php endif; ?>
                    </label>
                    <input class="input100" type="text" name="name" autocomplete="off" placeholder="Enter Name">
                </div>
                <div class="col-md-6 pt-md-0 pt-4">
                    <label>Email:
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
                    <label>Password:
                    <?php if ($errorMsg): ?>
                        <p class="text-danger"><?=$errorMsg['passErr']?></p>
                    <?php endif; ?>
                    </label>
                    <input class="input100" type="password" name="password" autocomplete="off" placeholder="Enter Password">
                </div>
                <div class="col-md-6 pt-md-0 pt-4">
                    <label>Location:
                    <?php if ($errorMsg): ?>
                        <p class="text-danger"><?=$errorMsg['locationErr']?></p>
                    <?php endif; ?>
                    </label>
                    <input class="input100" type="text" name="location" autocomplete="off" placeholder="Enter Address">
                </div>
            </div>
            <div class="row my-md-4 my-2">
                <div class="col-md-6">
                    <label>MeterNumber:
                    <?php if ($errorMsg): ?>
                        <p class="text-danger"><?=$errorMsg['meterErr']?></p>
                    <?php endif; ?>
                    </label>
                    <input class="input100" type="text" name="meter_no" autocomplete="off" placeholder="Enter Meter Number">
                </div>
                <div class="col-md-6 pt-md-0 pt-4">
                    <label>Category</label>
                    <select name="category" id="category">
                        <option value="pre-paid">Pre-Paid</option>
                        <option value="post-paid"> Post-Paid</option>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button class="btn mr-2" name="addaccou" type="submit">Submit</button>
                <a class="btn btn-danger btn-sm" href="accounts.php" type="btn">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?=$template->footer_template('Dashboard')?>
