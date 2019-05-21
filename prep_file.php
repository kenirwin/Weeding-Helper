#!/usr/bin/env php 
<?php
include ("config.php");
if ($debug){ 
    error_reporting(E_ALL & ~E_NOTICE);
    ini_set('display_errors', 1);
}

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
$stmt = $db->query($q);
$rows = $stmt->rowCount();
$now = date ("Y-m-d h:i:s");
//fwrite ($log, "$now - $q\n$now - Rows waiting: $rows\n");

/* for each waiting file, prep it, load to mysql, update controller */
while ($myrow = $stmt->fetch(PDO::FETCH_ASSOC)) {
  extract($myrow);
  //  fwrite ($log, "$now - PrepFile: $filename");
  if (PrepFile ($filename, $call_type)) {
    print "SUCCESS: Prepped file for database ingestion\n";
    //    fwrite ($log, "$now - CreateTable: $table_name");
    if (CreateTable($table_name)) {
        print "SUCCESS: Created table\n";
        if (LoadTable($table_name, $filename)) {
            print "SUCCESS: Loaded Table\n";
        }
      else { print "FAILED: Cound not load data from $filename into $table_name\n";}
    } //end if CreateTable
    else {
      print "FAILED: Could not create table $table_name\n";
    }
  } //end if PrepFile
  else { print "FAILED: Failed to PrepFile $filename; Database table not created\n"; }

  $q = "SELECT count(*) FROM `$table_name`";
  $stmt = $db->query($q);
  $row_a = $stmt->fetch(PDO::FETCH_NUM);
  $count = $row_a[0];
  if ($count > 0) {
    $q2 = "UPDATE `controller` SET `load_date` = now(), `records`= $count WHERE `table_name` = '$table_name'";
    $db->query($q2);
    //    fwrite ($log, "$now - $q2\n");
  }
} //end while processing files

?>
