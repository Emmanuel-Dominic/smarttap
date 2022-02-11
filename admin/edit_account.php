<?php
include '../template.php';

$template = new Template($db);
$template->session_check('admin');

$msg = '';
if (isset($_GET['id'])) {
    $stmt1 = $db->prepare('SELECT * FROM users u INNER JOIN (SELECT * FROM accounts WHERE user_id = ?) a ON u.id = ? Limit 1');
    $stmt1->execute([$_GET['id'], $_GET['id']]);
    $customer = $stmt1->fetch(PDO::FETCH_ASSOC);
    if (!$customer) {
        exit('Account doesn\'t exist with that ID!');
    }

    if (!empty($_POST)) {
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
        $location = isset($_POST['location']) ? $_POST['location'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
	    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
	    $meter_no = isset($_POST['meter_no']) ? $_POST['meter_no'] : '';
	    $category = isset($_POST['category']) ? $_POST['category'] : '';

	    $stmt = $db->prepare('UPDATE users SET name=?, username=?, email=?, phone=?, location=?, password=? WHERE id=?');
        $stmt->execute([$name, $username, $email, $phone, $location, $password, $user_id]);

	    $stmt2 = $db->prepare('UPDATE accounts SET meter_no=?, category=? WHERE user_id=?');
        $stmt2->execute([$meter_no, $category, $user_id]);

	    $msg = 'Successfully Updated!';
	    header('Location: accounts.php');
	}
} else {
    exit('No ID specified!');
}
?>

<?=$template->dashboard_header_template('Edit Account', $_SESSION['name'], $_SESSION['role'])?>
<div class="container content-view mt-5 mb-5">
    <div class="wrapper d-flex justify-content-center flex-column px-md-5 px-1">
        <form class="register-form" action="edit_account.php?id=<?=$customer['user_id']?>" method="post">
            <div class="h3 text-center font-weight-bold">Update Client's Account
            </div>
            <div class="row my-4">
                <div class="col-md-6">
                    <label>Full Name:</label>
                    <input class="input100" type="text" name="name" autocomplete="off" value="<?=$customer['name']?>">
                    <input class="input100" hidden="hidden" type="number" name="user_id" autocomplete="off" value="<?=$customer['user_id']?>">
                </div>
                <div class="col-md-6 pt-md-0 pt-4">
                    <label>UserName:</label>
                    <input class="input100" type="text" name="username" autocomplete="off" value="<?=$customer['username']?>">
                </div>
            </div>
            <div class="row my-md-4 my-2">
                <div class="col-md-6">
                    <label>Email:</label>
                    <input class="input100" type="email" name="email" autocomplete="off" value="<?=$customer['email']?>">
                </div>
                <div class="col-md-6 pt-md-0 pt-4">
                    <label>Password:</label>
                    <input class="input100" type="password" name="password" autocomplete="off" value="<?=$customer['password']?>">
                </div>
            </div>
            <div class="row my-md-4 my-2">
                <div class="col-md-6">
                    <label>PhoneNumber:</label>
                    <input class="input100" type="number" name="phone" autocomplete="off" value="<?=$customer['phone']?>">
                </div>
                <div class="col-md-6 pt-md-0 pt-4">
                    <label>Location:</label>
                    <input class="input100" type="text" name="location" autocomplete="off" value="<?=$customer['location']?>">
                </div>
            </div>
            <div class="row my-md-4 my-2">
                <div class="col-md-6">
                    <label>MeterNumber</label>
                    <input class="input100" type="text" name="meter_no" autocomplete="off" value="<?=$customer['meter_no']?>">
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
                <button class="btn mr-2" type="submit">Update</button>
                <a class="btn btn-danger btn-sm" href="accounts.php" type="btn">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?=$template->footer_template('Dashboard')?>
