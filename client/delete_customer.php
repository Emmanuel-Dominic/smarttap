<?php
include '../template.php';

$template = new Template($db);
$template->session_check('client');

$msg = '';
if (isset($_GET['id'])) {
    $stmt = $db->prepare('SELECT * FROM customers WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$customer) {
        exit('Customer doesn\'t exist with that ID!');
    }
    if (isset($_GET['confirm'])) {
        if ($_GET['confirm'] == 'yes') {
            $stmt = $db->prepare('DELETE FROM customers WHERE id = ?');
            $stmt->execute([$_GET['id']]);
            $msg = 'You have deleted an Customer!';
            header('Location: customers.php');
        } else {
            header('Location: customers.php');
            exit;
        }
    }
} else {
    exit('No ID specified!');
}
?>

<?=$template->dashboard_header_template('Delete Customer', $_SESSION['name'], $_SESSION['role'])?>
<div class="content delete">
    <h2>Delete Client #<?=$customer['id']?></h2>
    <?php if ($msg): ?>
    <p><?=$msg?></p>
    <?php else: ?>
    <p>Are you sure you want to delete contact #<?=$customer['name']?>?</p>
    <div class="yesno">
        <a href="delete_customer.php?id=<?=$customer['id']?>&confirm=yes">Yes</a>
        <a href="delete_customer.php?id=<?=$customer['id']?>&confirm=no">No</a>
    </div>
    <?php endif; ?>
</div>
<?=$template->footer_template('Dashboard')?>
