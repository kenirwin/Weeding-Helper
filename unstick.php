#!/usr/bin/env php
<?php 
//error_reporting(E_ALL);
ini_set("display_errors", 1);

session_start();

include ("mysql_connect.php");
include ("scripts.php");

$verified_table_name = VerifyTableName($_REQUEST['table']);
//get next (presumably stuck) bib record
if ($verified_table_name != null) {
    $q = "SELECT * FROM $verified_table_name WHERE innreach_total_copies IS NULL limit 0,1";
    //print "GETTING NEXT (STUCK) RECORD: $q\n";
    $stmt = $db->query($q);

  
if ($stmt->rowCount() > 0) {
    while ($myrow = $stmt->fetch(PDO::FETCH_ASSOC)) {
    extract($myrow);
    //print "<h4>$bib_record: $title</h4>\n";
    $q2 = "UPDATE $verified_table_name SET innreach_circ_copies = -1, innreach_total_copies = -1 WHERE bib_record = ?";
    $stmt2 = $db->prepare($q2);
    $params = array($bib_record);

    if ($stmt->execute($params)) { 
      $_SESSION['unstuck'] = $bib_record;
      header ("Location: controller.php");
    }
    else {
      $error = $stmt->errorInfo();
      $_SESSION['error'] = "Not able to unstick anything!";
      header ("Location: controller.php");
      //      header ("Location: controller.php?error=". urlencode("Not able to unstick anything: $error") );
    } //end else if not unstuck

  } //end while getting the stuck row
} //end if row
} //end if verified_table_name
?>