#!/usr/bin/php 
<?
error_reporting(E_ALL);
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

//$log = fopen ("/docs/weed/log.txt", "a+"); 


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
    //  fwrite ($log, "$now - CreateTable: $table_name");
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



function PrepFile ($filename) { 
  global $path_main;
  /* SETUP VARIABLES */
  $handle = fopen("$path_main/upload/$filename", "r");
  $output_filename = "$path_main/prepped/$filename";
  $output_handle = fopen("$output_filename", "w");
  if (! $output_handle) { return false; }
  
  // these arrays define variable names to be used later
  $fields = array ("author","title","pub","lcsh","cat_date","loc","call_bib","call_item","volume","copy","bcode","mat_type","bib_record","item_record","oclc","total_circ","renews","int_use","last_checkin","barcode");

  // Define data condensations/transformations
  $date_items = array ("cat_date", "last_checkin");
  /* END SETUP */


  if ($handle) {
    while (!feof($handle)) {
      $line = fgets($handle);
      //      print "\nNEXT LINE: $line\n";
      $line_fields = preg_split("/\t/",$line);
      //      print_r($line_fields);

      for ($i=0; $i<sizeof($fields); $i++) {
	$index = $fields[$i];
	$$index = trim($line_fields[$i]);
      }

      //grab date from pub field
      if (preg_match("/(\d\d\d\d)/",$pub,$n)) {
	$year = $n[1];
      } //end if numbers in pub info

      /* convert fixed-field dates to SQL */
      foreach ($date_items as $field) {
	$$field = sqlDate(${$field});
      }

      $key = "";
      if (! preg_match ("/CALL/", $call_item)) {//skip headers
	//index based on item call # if there is one
	//otherwise, use the bib_call #
	if ($call_item != "") { $call = $call_item; }
	else {$call = $call_bib;}
	
	$key = "$call v.$volume c.$copy";
	//	if (! $data[$key]) { $key = "BAD CALL $item_record"; }
	if ($data[$key] || ($call == "")) {
	  $key = "ZZZZ $bogus"; //bogus & duplicate keys drop to the bottom
	  $bogus++;
	}
	if (preg_match("/^i/", $item_record)) {// if there is an item record
	  if (! $data[$key]) { //if not already used
	    $data[$key] = "$call_order_is_blank\t$author\t$title\t$pub\t$year\t$lcsh\t$cat_date\t$loc\t$call_bib\t$call_item\t$volume\t$copy\t$bcode\t$mat_type\t$bib_record\t$item_record\t$oclc\t$total_circ\t$renews\t$int_use\t$last_checkin\t$barcode\n";
	  } //end if already used
	  else {
	  print "<li>Duplicate ItemKey: $key</li>\n";
	  }
	} //end if there's an item record
      } //end if not the data headers
    } //end while
    fclose($handle);
    
    //sort and print
    uksort($data, "SortLC");
    foreach($data as $call => $info) {
      fwrite ($output_handle, $info);
    }
  } //end if handle
  return true;
  } //end function PrepFile



function CreateTable ($table_name) {
  $q = TableTemplate($table_name);
  $r = mysql_query($q);
  if (mysql_errno() > 0) {
    $error = "ERROR: " . mysql_errno() . ": " . mysql_error(). "\n";
    print ($error);
    return false;
  } //end if error
  else {
    print ("SUCCESS: Created table: $table_name\n");
    return true;
  }
}

function LoadTable ($table, $file) {
  global $path_main;
  $q = "LOAD DATA INFILE '$path_main/prepped/$file' INTO TABLE `$table` FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n'";
  print $q . "\n";
  $r = mysql_query($q);
  if (mysql_errno() > 0) {
    $error = "ERROR: " . mysql_errno() . ": " . mysql_error(). "\n";
    print ($error);
    return false;
  } //end if error
  else {
    print ("SUCCESS: LOADED FILE $file\n");
    return true;
  }
} //end function LoadFile




  
?>
