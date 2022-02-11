<?php
include 'connection.php';

$node1 = $_GET['node1'];
$node2= $_GET['node3'];
$node3 = $_GET['node2'];
$node4= $_GET['node4'];
$node5 = $_GET['node5'];
$node6= $_GET['node6'];
$node7 = $_GET['node7'];
$node8 = $_GET['node8'];
$node9 = 2026550143;
$node10 = $_GET['node10'];
$node11 = $_GET['node11'];
$node12 = "0.4608° N, 34.1115° E";

$query = mysqli_query($con, "INSERT INTO `parameters`(`meter_no`, `meter_reading1`, `meter_reading2`, `ph`, `tds`, `turbidity`, `temperature`, `pressure`, `battery_life`, `security`, `rfid_no`,  `gps_cordinates`) VALUES ('$node9','$node7','$node8','$node1','$node3','$node2','$node4','$node10','$node11','$node5','$node6','$node12')");

// $stmt1 = mysqli_query($con, "UPDATE customers as c INNER JOIN (SELECT DISTINCT p.rfid_no, p.meter_no, SUM(p.meter_reading2-COALESCE(pm.meter_reading2, 0)) as units FROM parameters p LEFT OUTER JOIN parameters pm ON(p.id = pm.id+1) GROUP BY p.rfid_no) as src ON c.card_no =  src.rfid_no SET c.meter_reading = src.units, c.amount = (src.units*15), c.balance=(c.payment-c.amount)");
$stmt1 = mysqli_query($con, "UPDATE customers as c INNER JOIN (SELECT DISTINCT p.rfid_no, p.meter_no, SUM(p.meter_reading2-COALESCE(pm.meter_reading2, 0)) as units FROM parameters p LEFT OUTER JOIN parameters pm ON(p.id = pm.id+1) GROUP BY p.rfid_no) as src ON c.card_no =  src.rfid_no SET c.meter_reading = src.units, c.amount = (src.units*15), c.balance=(c.payment-c.amount), c.status=IF(c.balance<'1','0','1')");

$stmt0 = fetch_query("SELECT * FROM clearance LIMIT 1"); 

$date = new DateTime($stmt0['clearance_date']);
$now = new DateTime();

if($date <= $now) {
    $stmt2 = mysqli_query($con, "UPDATE accounts as a INNER JOIN (SELECT DISTINCT p.meter_no, p.meter_reading1, p.ph, p.tds, p.turbidity, p.temperature, p.security, pm.max_created_at FROM parameters p INNER JOIN (SELECT DISTINCT meter_no, MAX(Created_at) AS max_created_at FROM parameters GROUP BY meter_no) pm ON pm.meter_no = p.meter_no AND pm.max_created_at = p.Created_at GROUP BY p.meter_no) as src ON a.meter_no =  src.meter_no SET a.meter_reading = src.meter_reading1, a.amount = (src.meter_reading1*4.9), a.balance=CASE WHEN (a.category = 'pre-paid') THEN (a.payment-a.amount) WHEN (a.category = 'post-paid') THEN (a.amount-a.payment) END, a.ph = src.ph, a.tds = src.tds, a.turbidity = src.turbidity, a.temperature = src.temperature, a.security = src.security, a.status =CASE WHEN (a.security = 'tampered') THEN '0' WHEN (a.security = 'Notamper') THEN '1' WHEN (a.balance<='0') THEN '0' WHEN (a.balance>'0') THEN '1' END");
    //$stmt2 = mysqli_query($con, "UPDATE accounts as a INNER JOIN (SELECT DISTINCT p.meter_no, p.meter_reading1, p.ph, p.tds, p.turbidity, p.temperature, p.security, p.pressure, p.battery_life, pm.max_created_at FROM parameters p INNER JOIN (SELECT DISTINCT meter_no, MAX(Created_at) AS max_created_at FROM parameters GROUP BY meter_no) pm ON pm.meter_no = p.meter_no AND pm.max_created_at = p.Created_at GROUP BY p.meter_no) as src ON a.meter_no =  src.meter_no SET a.meter_reading = src.meter_reading1, a.amount = (src.meter_reading1*4.9), a.balance= CASE WHEN (a.category = 'pre-paid') THEN (a.payment-a.amount) WHEN (a.category = 'post-paid') THEN (a.amount-a.payment) END, a.ph = src.ph, a.tds = src.tds, a.turbidity = src.turbidity, a.temperature = src.temperature, a.security = src.security, a.pressure = src.pressure, a.battery_life = src.battery_life, a.status = CASE WHEN (a.security = 'tampered') THEN '0'  WHEN (a.security = 'Notamper') THEN '1' WHEN ((a.ph<'6.5' || a.ph>'8.5') && (a.turbidity<'5') && (a.tds<'300') && (a.temperature<'20' || a.temperature>'26')) THEN '0' WHEN ((a.ph>'6.5' || a.ph<'8.5') && (a.turbidity>'5') && (a.tds>'300') && (a.temperature>'20' || a.temperature<'26')) THEN '1' WHEN (a.balance<'0') THEN '0' WHEN (a.balance>'0') THEN '1' END");
}else{
    $stmt2 = mysqli_query($con, "UPDATE accounts as a INNER JOIN (SELECT DISTINCT p.meter_no, p.meter_reading1, p.ph, p.tds, p.turbidity, p.temperature, p.security, pm.max_created_at FROM parameters p INNER JOIN (SELECT DISTINCT meter_no, MAX(Created_at) AS max_created_at FROM parameters GROUP BY meter_no) pm ON pm.meter_no = p.meter_no AND pm.max_created_at = p.Created_at GROUP BY p.meter_no) as src ON a.meter_no =  src.meter_no SET a.meter_reading = src.meter_reading1, a.amount = (src.meter_reading1*4.9), a.balance=CASE WHEN (a.category = 'pre-paid') THEN (a.payment-a.amount) WHEN (a.category = 'post-paid') THEN (a.amount-a.payment) END, a.ph = src.ph, a.tds = src.tds, a.turbidity = src.turbidity, a.temperature = src.temperature, a.security = src.security, a.battery_life = src.battery_life, a.status = IF(STRCMP(a.security,'tempered'),'1','0')");
    //$stmt2 = mysqli_query($con, "UPDATE accounts as a INNER JOIN (SELECT DISTINCT p.meter_no, p.meter_reading1, p.ph, p.tds, p.turbidity, p.temperature, p.security, p.pressure, p.battery_life, pm.max_created_at FROM parameters p INNER JOIN (SELECT DISTINCT meter_no, MAX(Created_at) AS max_created_at FROM parameters GROUP BY meter_no) pm ON pm.meter_no = p.meter_no AND pm.max_created_at = p.Created_at GROUP BY p.meter_no) as src ON a.meter_no =  src.meter_no SET a.meter_reading = src.meter_reading1, a.amount = (src.meter_reading1*4.9), a.balance=CASE WHEN (a.category = 'pre-paid') THEN (a.payment-a.amount) WHEN (a.category = 'post-paid') THEN (a.amount-a.payment) END, a.ph = src.ph, a.tds = src.tds, a.turbidity = src.turbidity, a.temperature = src.temperature, a.security = src.security, a.pressure = src.pressure, a.battery_life = src.battery_life");
}

$statusState = fetch_query("SELECT a.status AS acc_status, c.status AS cus_status FROM accounts a INNER JOIN (SELECT status FROM customers) c");

if ($statusState['acc_status']=='1' && $statusState['cus_status']=='1') {
    echo "allservoopen";
}elseif ($statusState['acc_status']=='1' && $statusState['cus_status']=='0') {
    echo "clientopenSubclientclose";
}elseif ($statusState['acc_status']=='0' && $statusState['cus_status']=='1') {
    echo "clientcloseSubclientopen";
}elseif ($statusState['acc_status']=='0' && $statusState['cus_status']=='0') {
    echo "allclosed";
}
