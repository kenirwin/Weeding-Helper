#!/usr/bin/php -e
<?
//error_reporting(E_ALL);
ini_set("display_errors", 1);

include ("./DOM/simple_html_dom.php");
include ("mysql_connect.php");

// Frequency: $hits=hits per minute; $sleep = seconds between hits
if (date("H")<$innreach['night_ends']) { $hits = $innreach['overnight_hits']; } 
else { 
  $hits = $innreach['daytime_hits'];
}

$sleep = round(55/$hits); //space hits over 55 seconds, sleep between them

// get the first table needing check
$q = "SELECT table_name FROM `controller` where `innreach_finished` IS NULL and `load_date` IS NOT NULL ORDER BY `upload_date` DESC LIMIT 0,1";

$r = mysql_query($q);

print $q;

if (mysql_num_rows($r) > 0) { // if there's work to be done
  $myrow = mysql_fetch_row($r);
  $table = $myrow[0];
  BatchCheck($table, $hits, $sleep);
}

function BatchCheck ($table, $hits, $sleep) {
  

  $q = "SELECT * FROM $table WHERE innreach_total_copies IS NULL limit 0,$hits";
  print ($q);

  $r = mysql_query($q);
  
  if (mysql_num_rows($r) > 0) {
  while ($myrow = mysql_fetch_assoc($r)) {
    extract($myrow);
    print "<h4>$bib_record: $title</h4>";

    $innreach_count = CheckInnReach($bib_record);
    // print_r($innreach);
    if (! $innreach_count[CIRC]) { $innreach_count[CIRC] = 0; }
    //  print "<p>$innreach[CIRC] / ". array_sum($innreach) ."</p>\n";
    $uq = "UPDATE $table SET innreach_circ_copies = '$innreach_count[CIRC]', innreach_total_copies = '". array_sum($innreach_count) ."' WHERE bib_record = '$bib_record'";
    print "<p>$uq</p>";
    $ur = mysql_query($uq);
    sleep($sleep);
  } //end while
  } //end if still lines to check
  else  { //if the current file is finished, mark it as done in the controller
    $q = "UPDATE `controller` SET `innreach_finished` = now() WHERE `table_name` = '$table'";
    mysql_query($q);
    print "done: $q";
  }

  
} //end function BatchCheck

function CheckInnReach($bib) {
  global $innreach;
  $bib = substr($bib, 0, -1);
  $bibcode = $innreach[local_id] . "+$bib";
  $base = $innreach[url] . "/search/z?" . $innreach[local_id] ."+";
  $url = $base . $bib;
  //  print "<p>$url</p>\n";
  //  print "$bib<br>\n";
  $html = file_get_html($url);
  //  print_r($html->innertext);
  //  $holdings = $html->find('table.holdingsContainer');
  $holdings = $html->find($innreach[holdings_selector]);
  $size = sizeof($holdings);
  //  print("Size: " . $size . "<br>\n");
  if ($size == 0) {
    $url = "$innreach[url]/search~S0?/z$bibcode/z$bibcode/1,1,1,B/detlframeset&FF=z$bibcode&1,1,";
//      print "<p>$url</p>\n";
      //  print "$bib<br>\n";
      $html = file_get_html($url);
      //  print_r($html->innertext);
      //  $holdings = $html->find('table.holdingsContainer');
      $holdings = $html->find('table.centralHoldingsTable');
      $size = sizeof($holdings);
  }
  if ($size > 0) {
    $holdings = $holdings[0];
    //  print $holdings->innertext;
    $locations = $holdings->find('tr');
    foreach ($locations as $line) {
      $loc = $line->find('td', 0);
      if (! preg_match("/WITTENBERG/i", $loc)) {
	$status = $line->find('td', 4);
	$this_stat = $status->innertext;
	if (preg_match ("/AVAIL|DUE/", $this_stat)) { $this_stat = "CIRC"; }
	if ($this_stat != "") 
	  $statuses[$this_stat]++;
      }//end if not Witt
    } //end foreach location
    return($statuses);
  } //end if results size > 0
  //  else { return(); }
} //end function
?>