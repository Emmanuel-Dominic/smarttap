<?php
include '../template.php';

$template = new Template($db);
$template->session_check('admin');

$msg = '';
if (isset($_GET['id'])) {
    $stmt = $db->prepare('SELECT * FROM accounts WHERE user_id = ?');
    $stmt->execute([$_GET['id']]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$account) {
        exit('Account doesn\'t exist with that ID!');
    }

    if (isset($_GET['confirm'])) {
        if ($_GET['confirm'] == 'yes') {
            $stmt = $db->prepare('DELETE FROM accounts WHERE user_id = ?');
            $stmt->execute([$_GET['id']]);
            $stmt = $db->prepare('DELETE FROM users WHERE id = ?');
            $stmt->execute([$_GET['id']]);
            $msg = 'You have deleted an account!';
            header('Location: accounts.php');
        } else {
            header('Location: accounts.php');
            exit;
        }
    }
} else {
    exit('No ID specified!');
}
?>

<?=$template->dashboard_header_template('Delete Account', $_SESSION['name'], $_SESSION['role'])?>
<div class="content delete">
    <h2>Delete Account #<?=$account['user_id']?></h2>
    <?php if ($msg): ?>
    <p><?=$msg?></p>
    <?php else: ?>
    <p>Are you sure you want to delete account #<?=$account['meter_no']?>?</p>
    <div class="yesno">
        <a href="delete_account.php?id=<?=$account['user_id']?>&confirm=yes">Yes</a>
        <a href="delete_account.php?id=<?=$account['user_id']?>&confirm=no">No</a>
    </div>
    <?php endif; ?>
</div>
<?=$template->footer_template('Dashboard')?>
