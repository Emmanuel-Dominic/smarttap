<?php
include '../template.php';

$template = new Template($db);
$template->session_check('admin');

$stmt = $db->prepare('SELECT a.* FROM accounts a INNER JOIN (SELECT MAX(created_at) AS MaxDate FROM accounts WHERE user_id=?) ad ON a.created_at = ad.MaxDate AND a.user_id=?');
$stmt->execute([4, 4]);
// $stmt->execute([$_GET['id'], $_GET['id']]);

$parameter = $stmt->fetch(PDO::FETCH_ASSOC);

$num_users =  $db->query('SELECT COUNT(*) FROM users WHERE role="admin"')->fetchColumn();
$num_clients =  $db->query('SELECT COUNT(*) FROM users WHERE role="client"')->fetchColumn();
$num_active =  $db->query('SELECT COUNT(*) FROM accounts WHERE status=1')->fetchColumn();
$num_deactive =  $db->query('SELECT COUNT(*) FROM accounts WHERE status=0')->fetchColumn();

$dash_parameters = [
    0 => array(
        'id' => 1,
        'name' => 'Admins',
        'unit' => 'people',
        'value' => $num_users?$num_users:0,
        'icon' => 'users',
        'color' => 'info'
     ),
    1 => array(
        'id' => 2,
        'name' => 'clients',
        'unit' => 'people',
        'value' => $num_clients?$num_clients:0,
        'icon' => 'users',
        'color' => 'primary'
    ),
    2 => array(
        'id' => 3,
        'name' => 'Active Accounts',
        'unit' => 'users',
        'value' => $num_active?$num_active:0,
        'icon' => 'clipboard-list',
        'color' => 'success'
    ),
    3 => array(
        'id' => 4,
        'name' => 'Paused Accounts',
        'unit' => 'users',
        'value' => $num_deactive?$num_deactive:0,
        'icon' => 'clipboard-list',
        'color' => 'warning'
    )
];
echo json_encode($dash_parameters);
?>

