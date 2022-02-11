<?php
include '../template.php';

$template = new Template($db);
$template->session_check('admin');


$msg = '';
if (isset($_GET['id'])) {
    $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        exit('User doesn\'t exist with that ID!');
    }

    if ($_SESSION['user_id']==$_GET['id']) {
        exit('Can\'t delete this user!');
    }

    if (isset($_GET['confirm'])) {
        if ($_GET['confirm'] == 'yes') {
            $stmt = $db->prepare('DELETE FROM users WHERE id!=? AND id=?');
            $stmt->execute([$_SESSION['user_id'], $_GET['id']]);
            $msg = 'You have deleted a User!';
            header('Location: users.php');
        } else {
            header('Location: users.php');
            exit;
        }
    }
} else {
    exit('No ID specified!');
}
?>

<?=$template->dashboard_header_template('Delete User', $_SESSION['name'], $_SESSION['role'])?>
<div class="content delete">
    <h2>Delete User #<?=$user['id']?></h2>
    <?php if ($msg): ?>
    <p><?=$msg?></p>
    <?php else: ?>
    <p>Are you sure you want to delete user #<?=$user['name']?>?</p>
    <div class="yesno">
        <a href="delete_user.php?id=<?=$user['id']?>&confirm=yes">Yes</a>
        <a href="delete_user.php?id=<?=$user['id']?>&confirm=no">No</a>
    </div>
    <?php endif; ?>
</div>
<?=$template->footer_template('Dashboard')?>
