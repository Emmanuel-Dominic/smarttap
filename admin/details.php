<?php
include '../template.php';

$template = new Template($db);
$template->session_check('admin');

$clientId = $_GET['user_id'];
$stmt = $db->prepare('SELECT a.* FROM accounts a INNER JOIN (SELECT MAX(created_at) AS MaxDate FROM accounts WHERE user_id=?) ad ON a.created_at = ad.MaxDate AND a.user_id=?');
$stmt->execute([$_GET['user_id'], $_GET['user_id']]);
$account = $stmt->fetch(PDO::FETCH_ASSOC);

$meter = $account['meter_no']?$account['meter_no']:'No MeterNumber';
$unit = $account['meter_reading']?$account['meter_reading']:0;
$amount = $account['amount']?$account['amount']:0;
$payment = $account['payment']?$account['payment']:0;
$balance = $account['balance']?$account['balance']:0;
$ph = $account['ph']?$account['ph']:0;
$tds = $account['tds']?$account['tds']:0;
$turbidity = $account['turbidity']?$account['turbidity']:0;
$temperature = $account['temperature']?$account['temperature']:0;
$ldr = $account['security']?$account['security']:'Notamper';
$pressure = $account['pressure']?$account['pressure']:0;


$safe = '';
if (($ph<6.5 || $ph>8.5) && $turbidity>5 && $tds>300 && ($temperature<20 || $temperature>26)) {
    $safe = 'UnSafe';
} else {
    $safe = 'Safe';
}

$user_parameters = [
    0 => array(
        'id' => 1,
        'name' => 'meter number',
        'unit' => '',
        'value' => $meter,
        'icon' => 'users',
        'color' => 'info'
    ),
    1 => array(
        'id' => 2,
        'name' => "<a href='graph.php?id=$clientId&parameter=ph&meter_no=$meter'>ph</a>",
        'unit' => '',
        'value' => $ph,
        'icon' => 'calender',
        'color' => 'primary'
    ),
    2 => array(
        'id' => 3,
        'name' => "<a href='graph.php?id=$clientId&parameter=tds&meter_no=$meter'>tds</a>",
        'unit' => 'units',
        'value' => $tds,
        'icon' => 'comments',
        'color' => 'info'
    ),
    3 => array(
        'id' => 4,
        'name' => "<a href='graph.php?id=$clientId&parameter=temperature&meter_no=$meter'>temperature</a>",
        'unit' => '<sup>o</sup>C',
        'value' => $temperature,
        'icon' => 'thermometer',
        'color' => 'danger'
    ),
    4 => array(
        'id' => 5,
        'name' => "<a href='graph.php?id=$clientId&parameter=turbidity&meter_no=$meter'>turbidity</a>",
        'unit' => 'units',
        'value' => $turbidity,
        'icon' => 'clipboard-list',
        'color' => 'warning'
    ),
    5 => array(
        'id' => 6,
        'name' => "<a href='graph.php?id=$clientId&parameter=pressure&meter_no=$meter'>pressure</a>",
        'unit' => 'Mpa',
        'value' => $pressure,
        'icon' => 'pressure',
        'color' => 'info'
    ),
    6 => array(
        'id' => 7,
        'name' => 'security',
        'unit' => '',
        'value' => $ldr,
        'icon' => 'dollar-sign',
        'color' => 'success'
    ),
    7 => array(
        'id' => 8,
        'name' => 'Water Safety',
        'unit' => '',
        'value' => $safe,
        'safecolor' => ($safe=='Safe'?'text-success':'text-danger'),
        'icon' => 'thermometer',
        'color' => ($safe=='Safe'?'success':'danger')
    )
];
echo json_encode($user_parameters);
?>
