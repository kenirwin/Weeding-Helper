<?php 
include ("mysql_connect.php");
include ("scripts.php");
//$table = "educ";
//$file = "call_starts_L.txt";

CreateTable($table);
LoadTable($table, $file);

?>