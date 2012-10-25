#!/usr/bin/php -e
<?
//error_reporting(E_ALL);
ini_set("display_errors", 1);

include ("mysql_connect.php");
include ("$path_main/DOM/simple_html_dom.php");

// Frequency: $hits=hits per minute; $sleep = seconds between hits
if (date("H")<$innreach['night_ends']) { $hits = $innreach['overnight_hits']; } 
else { 
  $hits = $innreach['daytime_hits'];
}

$sleep = round(55/$hits); //space hits over 55 seconds, sleep between them

// get the first table needing check
$q = "SELECT table_name FROM `controller` where `innreach_finished` IS NULL and `load_date` IS NOT NULL ORDER BY `upload_date` ASC LIMIT 0,1";

$r = mysql_query($q);

print "$q\n";

if (mysql_num_rows($r) > 0) { // if there's work to be done
  $myrow = mysql_fetch_row($r);
  $table = $myrow[0];
  BatchCheck($table, $hits, $sleep);
}

function BatchCheck ($table, $hits, $sleep) {
  

  $q = "SELECT * FROM $table WHERE innreach_total_copies IS NULL limit 0,$hits";
  print "$q\n";

  $r = mysql_query($q);
  
  if (mysql_num_rows($r) > 0) {
  while ($myrow = mysql_fetch_assoc($r)) {
    extract($myrow);
    print "<h4>$bib_record: $title</h4>\n";

    $innreach_count = CheckInnReach($bib_record);
    // print_r($innreach);
    if (! $innreach_count[CIRC]) { $innreach_count[CIRC] = 0; }
    //  print "<p>$innreach[CIRC] / ". array_sum($innreach) ."</p>\n";
    $uq = "UPDATE $table SET innreach_circ_copies = '$innreach_count[CIRC]', innreach_total_copies = '". array_sum($innreach_count) ."' WHERE bib_record = '$bib_record'";
    print "<p>$uq</p>\n";
    $ur = mysql_query($uq);
    sleep($sleep);
  } //end while
  } //end if still lines to check
  else  { //if the current file is finished, mark it as done in the controller
    $q = "UPDATE `controller` SET `innreach_finished` = now() WHERE `table_name` = '$table'";
    mysql_query($q);
    print "done: $q\n";
  }

  
} //end function BatchCheck

function CheckInnReach($bib) {
  global $innreach;
  $bib = substr($bib, 0, -1);
  $bibcode = $innreach[local_id] . "+$bib";
  $base = $innreach[url] . "/search/z?" . $innreach[local_id] ."+";
  $url = $base . $bib;
  print "<p>URL1: $url</p>\n";

  if ($html) { $html->clear(); } //helps manage memory leaks
  $html = file_get_html($url);

  // if holdings block found, size = 1
  // if holdings block requires follow_through, size = 0
  // if unable to parse, size = -1
  ($holdings = $html->find($innreach[holdings_selector])) || $size = -1;
  if (is_array($holdings)) { $size = sizeof($holdings); }
  //print "SIZE: $size\n";

  if ($size == 0) {
    $url = "$innreach[url]/search~S0?/z$bibcode/z$bibcode/1,1,1,B/detlframeset&FF=z$bibcode&1,1,";
    print "<p>URL2: $url</p>\n";
      //  print "$bib<br>\n";
      $html = file_get_html($url);
      ($holdings = $html->find($innreach[holdings_selector])) || $size = -1;
      if (is_array($holdings)) { $size = sizeof($holdings); }
  }
  if ($size > 0) {
    $holdings = $holdings[0];
    //  print $holdings->innertext;
    $locations = $holdings->find('tr');
    foreach ($locations as $line) {
      $loc = $line->find('td', 0);
      if (! preg_match("/$innreach[local_display_name]/i", $loc)) {
	$status = $line->find('td', 4);
	$this_stat = $status->innertext;
	if (preg_match ("/AVAIL|DUE/", $this_stat)) { $this_stat = "CIRC"; }
	if ($this_stat != "") 
	  $statuses[$this_stat]++;
      }//end if not Witt
    } //end foreach location
    if ($size == -1) { $statuses[CIRC] = -1; }
    print_r($statuses);
    return($statuses);
  } //end if results size > 0
} //end function CheckInnReach
?>