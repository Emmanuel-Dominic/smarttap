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
    'locationErr' => ''
];

if (isset($_REQUEST['adduser'])) {

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

    if (empty($_POST['password'])){
        $errorMsg['passErr'] = 'Please enter user\'s password!';
    }

    if(empty($_POST['location'])){
        $errorMsg['loactionErr'] = 'Please enter user\'s location!';
    }

    if (!empty($_POST['name']) AND !empty($_POST['username']) AND !empty($_POST['email']) AND !empty($_POST['phone'] AND $_POST['location'])) {

        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
        $location = isset($_POST['location']) ? $_POST['location'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $location = isset($_POST['location']) ? $_POST['location'] : '';
        $role = isset($_POST['role']) ? $_POST['role'] : '';

        $stmt = $db->prepare('INSERT INTO users(`name`, `username`, `email`, `phone`, `location`, `password`, `role`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, "admin", now())');
        $stmt->execute([$name, $username, $email, $phone, $location, $password]);
        $msg = 'Successfully Added!';
        header('Location: users.php');
    }
}
?>

<?=$template->dashboard_header_template('Add Admin', $_SESSION['name'], $_SESSION['role'])?>
<div class="container content-view mt-5 mb-5">
    <div class="wrapper d-flex justify-content-center flex-column px-md-5 px-1">
        <form class="register-form" action="adduser.php" method="post">
            <div class="h3 text-center font-weight-bold">Register New Admin
            </div>
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
            <div class="d-flex justify-content-end">
                <button class="btn mr-2" name="adduser" type="submit">Submit</button>
                <a class="btn btn-danger btn-sm" href="users.php" type="btn">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?=$template->footer_template('Dashboard')?>
