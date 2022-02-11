<?php
include '../template.php';

$template = new Template($db);
$template->session_check('client');

$stmt = $db->prepare('SELECT COUNT(*) FROM customers WHERE user_id=? LIMIT 1');

$stmt->execute([$_SESSION['user_id']]);

$num_customers = $stmt->fetch(PDO::FETCH_ASSOC);

$numcustomers = $num_customers?$num_customers['COUNT(*)']:0;
$stmt1 = $db->prepare('SELECT a.* FROM accounts a INNER JOIN (SELECT MAX(created_at) AS MaxDate FROM accounts WHERE user_id=?) ad ON a.created_at = ad.MaxDate AND a.user_id=?');
$stmt1->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
$account = $stmt1->fetch(PDO::FETCH_ASSOC);
$meter = $account['meter_no']?$account['meter_no']:'No MeterNumber';
$unit = $account['meter_reading']?$account['meter_reading']:0;
$amount = $account['amount']?$account['amount']:0;
$payment = $account['payment']?$account['payment']:0;
$balance = $account['balance']?$account['balance']:0;
$ph = $account['ph']?$account['ph']:0;
$tds = $account['tds']?$account['tds']:0;
$turbidity = $account['turbidity']?$account['turbidity']:0;
$temperature = $account['temperature']?$account['temperature']:0;
$ldr = $account['security']?$account['security']:0;

$safe = '';
if (($ph<6.5 || $ph>8.5) && $turbidity>5 && $tds>300 && ($temperature<20 || $temperature>26)) {
    $safe = 'UnSafe';
} else {
    $safe = 'Safe';
}

$dash_parameters = [
    0 => array(
        'id' => 1,
        'title' => 'Meter',
        'head' => $meter,
        'htag' => 'h6',
        'value' => $unit,
        'unit' => 'units',
        'name' => $amount,
        'nunit' => 'charges',
        'icon' => 'clock',
        'color' => 'primary',
        'ncolor' => 'danger'
    ),
    1 => array(
        'id' => 2,
        'title' => 'Customers',
        'htag' => 'h5',
        'value' => $numcustomers,
        'unit' => 'users',
        'icon' => 'users',
        'color' => 'info'
    ),
    2 => array(
        'id' => 3,
        'title' => 'Balance',
        'htag' => 'h5',
        'value' => $balance,
        'unit' => '/=',
        'icon' => 'dollar-sign',
        'color' => 'success'
    ),
    3 => array(
        'id' => 4,
        'title' => 'Water Safety',
        'htag' => 'h5',
        'value' => $safe,
        'safecolor' => ($safe=='Safe'?'text-success':'text-danger'),
        'unit' => '',
        'icon' => 'thermometer',
        'color' => 'warning'
    )
];

echo json_encode($dash_parameters);
?>
