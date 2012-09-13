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
    //  fwrite ($log, "$now - CreateTable: $table_name");
    CreateTable($table_name);
    //  fwrite ($log, "$now - LoadTable: $table_name, $filename");
    LoadTable($table_name, $filename);
  } //end if PrepFile
  else { print "FAILED: Failed to PrepFile; Database table not created"; }

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
  
  /* SETUP VARIABLES */
  $handle = fopen("upload/$filename", "r");
  $output_filename = "prepped/$filename";
  $output_handle = fopen("$output_filename", "w");
  if (! $output_handle) { return false; }
  
  // these arrays define variable names to be used later
  $fixed = array ("author","title","pub","lcsh","cat_date","loc","call","bcode","mat_type","bib_record");
  $itemized = array ("total_circ","renews","int_use","last_checkin");
  /* integer_items will be added together so that a book with 
  5 circs on one copy and 6 on another will show a total of 11
  */
  
  // Define data condensations/transformations
  $integer_items = array ("total_circ","renews","int_use");
  $date_items = array ("cat_date", "last_checkin");
  //condense some dates to their most recent instance
  $most_recent_items = array ("last_checkin"); 
  // NOT YET META: $pub_to_date = array ("pub" => "date");
  $count_nonzero = array ("circ_items" => "total_circ");
  /* END SETUP */

  $num_fixed = sizeof($fixed);
  $num_itemized = sizeof($itemized);

  // build a regex to match tab-delimited # of fixed items + many itemized
  for ($i=0; $i<$num_fixed;$i++) {
    $match_string .= "([^\t]*)\t";
  }
  $match_string .= "(.*)";


  if ($handle) {
    while (!feof($handle)) {
      $line = fgets($handle);
      //    print "\n$line\n";
      
      if (preg_match("/^$match_string/", $line, $m)) { 
	// grab first four fixed field, then take the rest as an array
	for ($i=0; $i<$num_fixed; $i++) {
	  $$fixed[$i] = $m[$i+1]; // =~ $bib_record = $m[1]...
	} //end for each fixed
	$items=split("\t",$m[$num_fixed+1]);
	$num_items = sizeof($items)/sizeof($itemized); // there are 3 fields for each item
	//      print_r($items);
	/*
	  print "BIB: $bib_record\n";
	  print "CALL: $call\n";
	  print "TITLE: $title\n";
	  print "PUB: $pub\n";
	  print_r ($items);
	  print "\n\n";
	*/
	if (preg_match("/(\d\d\d\d)/",$pub,$n)) {
	$year = $n[1];
	} //end if numbers in pub info
	
	
	/* count nonzeros */
	/*
	  foreach ($count_nonzero as $orig => $output) {
	  // e.g. total_circs => nonzero-circ-items
	  print "Total Circ: $total_circ\n";
	  print_r (${$orig});
	  $$output = sizeof(${$orig}) - sizeof(array_keys(${$orig},0));
	  print "${$output}\n";
	  }
	*/
	

	/* condense arrays */
	for ($i=0; $i<$num_itemized; $i++) {
	  $$itemized[$i] = array_slice($items,($num_items*$i),$num_items);
	  if (array_keys($count_nonzero, $itemized[$i])) {
	    $field = $itemized[$i];
	    $output_field = (array_keys($count_nonzero, $itemized[$i]));
	    $output_field = $output_field[0]; // convert array to string
	    //	  print "\n\nFIELD: $field\n";
	    //print_r (${$field});
	    $$output = sizeof(${$field}) - sizeof(array_keys(${$field},0));
	    //	  print "OUTPUT: ${$output}\n";
	    $$output_field = ${$output};
	  }
	  if (in_array($itemized[$i], $integer_items)) {
	    $$itemized[$i] = array_sum($$itemized[$i]);
	  }
	}
	
	
	
	/* convert fixed-field dates to SQL */
	foreach (array_intersect($fixed, $date_items) as $field) {
	  $$field = sqlDate(${$field});
	}

	/* convert and condense variable-field dates */
	
	foreach ($most_recent_items as $this_field) {
	  $most_recent = $most_recent_date = "";
	  foreach ($$this_field as $date) { //foreach element in the associated array
	    $datestamp = sqlDate($date);
	    if ($most_recent < $datestamp) { 
	      $most_recent = $datestamp;
	    } //end if newer datestamp
	  } //end foreach date in field array
	  if ($most_recent == "1969-12-31") { $most_recent = "0000-00-00"; }
	  /*
	    print "$this_field\n";
	    print_r ($$this_field);
	    print "\nMost recent: $most_recent\n";
	  */
	  $$this_field = $most_recent; //over-write array with single string
	  
	} // foreach date_item
	
	if (! preg_match ("/CALL/", $call)) //skip headers
	  $data[$call] = "$call_order_is_blank\t$author\t$title\t$pub\t$year\t$lcsh\t$cat_date\t$loc\t$call\t$bcode\t$mat_type\t$bib_record\t$total_circ\t$renews\t$int_use\t$last_checkin\t$num_items\t$circ_items\n";
      } //end if contains data
      //        print "$most_recent_date\t$title\n";
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
  } //end if error
  else {
    print ("SUCCESS: Created table: $table_name\n");
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
  } //end if error
  else {
    print ("SUCCESS: LOADED FILE\n");
  }
} //end function LoadFile




  
?>
