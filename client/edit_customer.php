<?php
include '../template.php';

$template = new Template($db);
$template->session_check('client');

$msg = '';
if (isset($_GET['id'])) {
    $stmt = $db->prepare('SELECT * FROM customers WHERE id = ? Limit 1');
    $stmt->execute([$_GET['id']]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        exit('Subclient doesn\'t exist with that ID!');
    }
    
    if (!empty($_POST)) {
	    $id = isset($_POST['id']) ? $_POST['id'] : NULL;
	    $name = isset($_POST['name']) ? $_POST['name'] : '';
	    $username = isset($_POST['username']) ? $_POST['username'] : '';
	    $email = isset($_POST['email']) ? $_POST['email'] : '';
	    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';

	    $card_no = isset($_POST['card_no']) ? $_POST['card_no'] : 0;
	    $meter_no = isset($_POST['meter_no']) ? $_POST['meter_no'] : 0;
	    $pay = isset($_POST['payment']) ? $_POST['payment'] : 0;
	    $payment = ($customer['payment']+$pay);
	    $balance = ($payment-$customer["amount"]);
	    $status = ($balance>14)?'1':'0';

	    $stmt = $db->prepare('UPDATE customers SET name = ?, username = ?, email = ?, phone = ?, card_no = ?, payment = ?, balance = ?, status = ? WHERE id = ?');
	    $stmt->execute([$name, $username, $email, $phone, $card_no, $payment, $balance, $status, $id]);

	    $msg = 'Successfully Updated!';
	    header('Location: customers.php');

	}
} else {
    exit('No ID specified!');
}
?>

<?=$template->dashboard_header_template('Edit Subclient', $_SESSION['name'], $_SESSION['role'])?>
<div class="container content-view mt-5 mb-5">
    <div class="wrapper d-flex justify-content-center flex-column px-md-5 px-1">
        <form class="register-form" action="edit_customer.php?id=<?=$customer['id']?>" method="post">
            <div class="h3 text-center font-weight-bold">Update Subclient</div>
            <div class="row my-4">
                <div class="col-md-6">
                    <label>Full Name</label>
                    <input class="input100" type="text" name="name" autocomplete="off" value="<?=$customer['name']?>">
                    <input class="input100" hidden="hidden" type="number" name="id" autocomplete="off" value="<?=$customer['id']?>">
                </div>
                <div class="col-md-6 pt-md-0 pt-4">
                    <label>Email</label>
                    <input class="input100" type="email" name="email" autocomplete="off" value="<?=$customer['email']?>">
                </div>
            </div>
            <div class="row my-md-4 my-2">
                <div class="col-md-6">
                    <label>UserName</label>
                    <input class="input100" type="text" name="username" autocomplete="off" value="<?=$customer['username']?>">
                </div>
                <div class="col-md-6 pt-md-0 pt-4">
                    <label>PhoneNumber</label>
                    <input class="input100" type="tel" name="phone" autocomplete="off" value="<?=$customer['phone']?>">
                </div>
            </div>
            <div class="row my-4">
                <div class="col-md-6">
                    <label>CardNumber</label>
                    <input class="input100" type="text" name="card_no" autocomplete="off" value="<?=$customer['card_no']?>">
                </div>
                <div class="col-md-6 pt-md-0 pt-4">
                    <label>Payment</label>
                    <input class="input100" type="number" name="payment" autocomplete="off">
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button class="btn mr-2" type="submit">Submit</button>
                <a class="btn btn-danger btn-sm" href="customers.php" type="btn">Cancel</a>
            </div>
            <!--<div class="row my-md-4 my-2">
                <div class="col-md-6">
                    <label>Status</label>
                    <select name="status" id="status">
                        <option value="Active" selected='<?= ($customer["status"] == "1")? "selected" : "" ?>' > Activate</option>
                        <option value="Paused" selected='<?= ($customer["status"] == "0")? "selected" : "" ?>' > Pause</option> 
                    </select>
                </div>
            </div>-->
        </form>
    </div>
</div>
<?=$template->footer_template('Dashboard')?>
