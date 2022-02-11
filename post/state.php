<?php

include 'connection.php';
//include 'connection.php';


$act_query =  $conn->query("SELECT * FROM `customers` WHERE `id` = '1'") or die(mysqli_error());

$act_fetch = $act_query->fetch_array();

$status = $act_fetch['status'];

echo $status;

?>