#!/usr/bin/env php 
<?php
error_reporting('E_ALL');
//display_errors(true);
/*
this script will take a III export and condense the repeatable fields
(e.g. if there are 3 item records, it will combine the circ data)

change the variables in the setup section below to accomodate different formats

you'll also have to change the reporting output at the end of the script
*/

include ("scripts.php");
include ("sortLC.php");
include ("config.php");
require ("mysql_connect.php");

$log = fopen ("log.txt", "a+");

$q = "SELECT * FROM controller WHERE filename != '' and load_date IS NULL";
$r = mysql_query ($q);
$rows = mysql_num_rows($r);
$now = date ("Y-m-d h:i:s");
//fwrite ($log, "$now - $q\n$now - Rows waiting: $rows\n");

/* for each waiting file, prep it, load to mysql, update controller */
while ($myrow = mysql_fetch_assoc($r)) {
  extract($myrow);
  //  fwrite ($log, "$now - PrepFile: $filename");
  if (PrepFile ($filename)) {
    print "SUCCESS: Prepped file for database ingestion\n";
    //    fwrite ($log, "$now - CreateTable: $table_name");
    if (CreateTable($table_name)) {
      //  fwrite ($log, "$now - LoadTable: $table_name, $filename");
      if (LoadTable($table_name, $filename)) {
      }
      else { print "FAILED: Cound not load data from $filename into $table_name\n";}
    } //end if CreateTable
    else {
      print "FAILED: Could not create table $table_name\n";
    }
  } //end if PrepFile
  else { print "FAILED: Failed to PrepFile $filename; Database table not created\n"; }

  $q = "SELECT count(*) FROM `$table_name`";
  $r = mysql_query($q);
  $row_a = mysql_fetch_array($r);
  $count = $row_a[0];
  if ($count > 0) {
    $q2 = "UPDATE `controller` SET `load_date` = now(), `records`= $count WHERE `table_name` = '$table_name'";
    mysql_query($q2);
    //    fwrite ($log, "$now - $q2\n");
  }
} //end while processing files

?>
