<?php
include 'connection.php';


$stmt1 = mysqli_query($con, "UPDATE customers as c INNER JOIN (SELECT DISTINCT p.rfid_no, p.meter_no, SUM(p.meter_reading2-COALESCE(pm.meter_reading2, 0)) as units FROM parameters p LEFT OUTER JOIN parameters pm ON(p.id = pm.id+1) GROUP BY p.rfid_no) as src ON c.card_no =  src.rfid_no SET c.meter_reading = src.units, c.amount = (src.units*15), c.balance=(c.payment-c.amount)");
 
$stmt0 = mysqli_query($con, "SELECT * FROM clearance LIMIT 1");
 
$stmt1 = mysqli_query($con, "UPDATE customers as c INNER JOIN (SELECT DISTINCT p.rfid_no, p.meter_no, SUM(p.meter_reading2-COALESCE(pm.meter_reading2, 0)) as units FROM parameters p LEFT OUTER JOIN parameters pm ON(p.id = pm.id+1) GROUP BY p.rfid_no) as src ON c.card_no =  src.rfid_no SET c.meter_reading = src.units, c.amount = (src.units*15), c.balance=(c.payment-c.amount)");

$date = new DateTime($stmt1['clearance_date']);
$now = new DateTime();

if($date <= $now) {
    $stmt2 = mysqli_query($con, "UPDATE accounts as a INNER JOIN (SELECT DISTINCT p.meter_no, p.meter_reading1, p.ph, p.tds, p.turbidity, p.temperature, p.security, pm.max_created_at FROM parameters p INNER JOIN (SELECT DISTINCT meter_no, MAX(Created_at) AS max_created_at FROM parameters GROUP BY meter_no) pm ON pm.meter_no = p.meter_no AND pm.max_created_at = p.Created_at GROUP BY p.meter_no) as src ON a.meter_no =  src.meter_no SET a.meter_reading = src.meter_reading1, a.amount = (src.meter_reading1*4.9), a.balance=(a.payment-a.amount), a.ph = src.ph, a.tds = src.tds, a.turbidity = src.turbidity, a.temperature = src.temperature, a.security = src.security, a.status = IF(a.balance<'0','0','1')");
}else{
    $stmt2 = mysqli_query($con, "UPDATE accounts as a INNER JOIN (SELECT DISTINCT p.meter_no, p.meter_reading1, p.ph, p.tds, p.turbidity, p.temperature, p.security, pm.max_created_at FROM parameters p INNER JOIN (SELECT DISTINCT meter_no, MAX(Created_at) AS max_created_at FROM parameters GROUP BY meter_no) pm ON pm.meter_no = p.meter_no AND pm.max_created_at = p.Created_at GROUP BY p.meter_no) as src ON a.meter_no =  src.meter_no SET a.meter_reading = src.meter_reading1, a.amount = (src.meter_reading1*4.9), a.balance=(a.payment-a.amount), a.ph = src.ph, a.tds = src.tds, a.turbidity = src.turbidity, a.temperature = src.temperature, a.security = src.security");
}

?>