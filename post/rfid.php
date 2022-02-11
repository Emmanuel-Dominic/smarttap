<?php
//setting header to json
//header('Content-Type: application/json');
//Connect to the server and database
include 'connection.php';


$node1 = $_GET['node1'];
//  $query = mysqli_query($con, "INSERT INTO `parameters`(`meter_no`, `meter_reading1`, `meter_reading2`, `ph`, `tds`, `turbidity`, `temperature`, `security`, `rfid_no`,  `gps_cordinates`) VALUES ( '$node9','$node7','$node8','$node1','$node2','$node3','$node4','$node5','$node6','$node10')");
  
 $query = mysqli_query($con, "INSERT INTO `rfid`(`rfid_val`) VALUES ('$node1')");

if($query  ){

	echo "successs";
}
else {
	die("Failed: ".mysqli_error($con));
}
