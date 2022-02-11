<?php
include '../template.php';

$template = new Template($db);
$template->session_check('client');

$stmt = $db->prepare('SELECT c.* FROM customers c WHERE id=?');
$stmt->execute([$_GET['id']]);
$parameter = $stmt->fetch(PDO::FETCH_ASSOC);
$num_accounts =  $db->query('SELECT COUNT(*) FROM customers')->fetchColumn();

$dash_parameters = [
    0 => array(
        'id' => 1,
        'name' => 'meter reading',
        'unit' => 'units',
        'value' => $parameter['meter_reading']?$parameter['meter_reading']:0,
        'icon' => 'calender',
        'color' => 'info'
     ),
    1 => array(
        'id' => 2,
        'name' => 'float',
        'unit' => '/=',
        'value' => $parameter['payment']?$parameter['payment']:0,
        'icon' => 'dolar-sign',
        'color' => 'primary'
    ),
    2 => array(
        'id' => 3,
        'name' => 'balance',
        'unit' => '/=',
        'value' => $parameter['balance']?$parameter['balance']:0,
        'icon' => 'dolar-sign',
        'color' => 'info'
    )
];

echo json_encode($dash_parameters);
?>
