#!/usr/bin/env php
<?php 
//error_reporting(E_ALL);
ini_set("display_errors", 1);

session_start();

include ("mysql_connect.php");
$table = $_REQUEST[table];
//get next (presumably stuck) bib record
$q = "SELECT * FROM $table WHERE innreach_total_copies IS NULL limit 0,1";
//print "GETTING NEXT (STUCK) RECORD: $q\n";

$r = mysql_query($q);
  
if (mysql_num_rows($r) > 0) {
  while ($myrow = mysql_fetch_assoc($r)) {
    extract($myrow);
    //print "<h4>$bib_record: $title</h4>\n";
    $q2 = "UPDATE `$table` SET innreach_circ_copies = -1, innreach_total_copies = -1 WHERE bib_record = '$bib_record'";
    //print "$q2\n";

    if (mysql_query($q2)) { 
      $_SESSION[unstuck] = $bib_record;
      header ("Location: controller.php");
      //      header ("Location: controller.php?unstuck=$bib_record");
    }
    else {
      $error = mysql_error();
      $_SESSION[error] = "Not able to unstick anything!";
      header ("Location: controller.php");
      //      header ("Location: controller.php?error=". urlencode("Not able to unstick anything: $error") );
    } //end else if not unstuck

  } //end while getting the stuck row
} //end if row

?>