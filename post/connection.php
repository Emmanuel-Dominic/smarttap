<?php

 $con = mysqli_connect("localhost","root","","smarttap");
  if(!$con)  {      echo "connection failed ".mysqli_error();
  }
 function fetch_query($sql_query){
     global $con;
     $result = mysqli_query($con,$sql_query);
     return mysqli_fetch_array($result);
    
 }