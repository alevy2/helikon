<?php
require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");
//define posted value
$dbh = db_connect($athomas2_dsn);
$username = isset( $_GET['username']);
 
if($username == null){
    //if no user input
   echo "Please enter a username.";
   echo $username;
}
$sql = "select * from user where username = '$username'";
$resultset = query($dbh, $sql);
$isAvailable = $resultset->numRows();
if($isAvailable != 0) {
  while($row = $resultset->fetchRow(MBD2_FETCHMODE_ASSOC)) {
  $username = $row['username'];
  echo "<div style='color: red; font-weight: bold;'>Username already taken.</div>";
}
}

//elseif{
    //query the inputted value
//  $sql = "select  * from user where username = '$username'";
//  while($row = $resultset->fetchRow(MBD2_FETCHMODE_ASSOC)) {
      //$rs = mysql_query( $sql );
      // $num = mysql_num_rows( $rs );
      
      //if($num == 1 ){ //if username found
      //while($row = mysql_fetch_array( $rs )){
//          $fn = $row['username'];
//          $ln = $row['name'];
  //          echo "<div style='color: red; font-weight: bold;'>Username already taken.</div>";
//  }
//}

else{ //if username not found
      echo "<span style='font-weight: bold;'>$username</span> is available!";
  }

?>