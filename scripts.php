<?php 

function GetTableNames() {
  //returns an array of file_titles by table_name
  //e.g. $file_titles = array("econ" => "Economics Books");
    global $db;
  $q = "SELECT `table_name`,`file_title` FROM `controller` WHERE 1";
  $stmt = $db->query($q);
  while ($myrow = mysql_fetch_assoc($r)) {
    extract($myrow);
    $file_titles[$table_name] = $file_title;
  }
  return $file_titles;
}

function MysqlResultsTable ($mysql_results) {
  while ($myrow = mysql_fetch_assoc($mysql_results)) {
    if (! ($headers))
    if (! ($headers))
      $headers = array_keys($myrow);
    $rows .= " <tr>\n";
    foreach ($headers as $k)
      $rows .= "  <td class=$k>$myrow[$k]</td>\n";
    $rows .= " </tr>\n";
  } // end while myrow
  $header = join("</th><th>",$headers);
  $header = "<tr><th>$header</th></tr>\n";
  $rows = "<table>$header$rows</table>\n";
  return ($rows);
} //end function MysqlResultsTable

function Alert($text, $class) {
  $alert = '<li class="'.$class.'">'.$text.'</li>'.PHP_EOL;
  return $alert;
}

function PrintError($text, $return=false) {
  $alert = Alert("ERROR: " . $text,"warn");
  if ($return) { return $alert; }
  else { print $alert; }
}
function PrintSuccess($text, $return=false) {
  $alert = Alert("SUCCESS: ".$text,"success");
  if ($return) { return $alert; }
  else { print $alert; }
}

function TableTemplate ($table_name) {
  $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
  `call_order` int(11) NOT NULL AUTO_INCREMENT,
  `author` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `publisher` varchar(255) DEFAULT NULL,
  `year` varchar(10) DEFAULT NULL,
  `lcsh` varchar(255) NOT NULL DEFAULT '',
  `catdate` date NOT NULL DEFAULT '0000-00-00',
  `loc` varchar(50) NOT NULL DEFAULT '',
  `call_bib` varchar(50) NOT NULL DEFAULT '',
  `call_item` varchar(50) NOT NULL DEFAULT '',  
  `volume` varchar(50) NOT NULL DEFAULT '',  
  `copy` varchar(50) NOT NULL DEFAULT '',
  `bcode` char(1) DEFAULT NULL,
  `mat_type` varchar(255) DEFAULT NULL,
  `bib_record` varchar(15) NOT NULL DEFAULT '',
  `item_record` varchar(15) NOT NULL DEFAULT '',
  `oclc` varchar(15) NOT NULL DEFAULT '',
  `circs` int(11) DEFAULT NULL,
  `renews` int(11) DEFAULT NULL,
  `int_use` int(11) NOT NULL,
  `last_checkin` date DEFAULT NULL,
  `barcode` varchar(25) DEFAULT NULL,
  `subclass` char(3) DEFAULT NULL,
  `subj_starts` varchar(50) DEFAULT NULL,
  `classic` char(1) NOT NULL,
  `best_book` char(1) NOT NULL,
  `condition` varchar(50) NOT NULL,
  `notes` varchar(50) NOT NULL,
  `fate` varchar(50) NOT NULL,
  `innreach_circ_copies` int(11) DEFAULT NULL,
  `innreach_total_copies` int(11) DEFAULT NULL,
  PRIMARY KEY (`call_order`)
)";
  return($sql);
} //end function TableTemplate


function sqlDate ($date) {
  if (! preg_match("/\d/",$date)) { // if empty date
    $date = "0000-00-00";
    $timestamp = 0;
    $datestamp = "0000-00-00";
  } //end if never checked out
  else {
    if (preg_match("/(\d\d\-\d\d)-(\d+)/", $date,$m)) {
      $mo_day = $m[1];
      $yr = $m[2];
      if ($yr < 100) { $yr += 1900; }
      $date = "$yr-" . $mo_day;
    } //end if 2-digit year
    //	  print ($date .": ".strtotime($date). "<br>\n");
    $timestamp =  strtotime($date);
    $datestamp = date("Y-m-d", $timestamp);  
  } //end else 
  return ($datestamp);
} //end function sqlDate




function DisplayProcessTable($sort="filename") {
  $q = "SELECT * FROM `controller` ORDER BY `$sort`";
  $r = mysql_query($q);
  while ($myrow=mysql_fetch_assoc($r)) {
    //    extract($myrow);
    $headers_array = array_keys($myrow);
    $row = join ("</td><td>", array_values($myrow));
    $next_action = "";
    if ($myrow[load_date] != "") {
      $next_action = "<a href=\"view.php?table=$myrow[table_name]\" class=\"action\">View/Edit</a> <a href=\"cloud.php?table=$myrow[table_name]\" class=\"action\">Keyword Cloud</a>\n";
    }
    else { 
      $next_action = "Awaiting Cron Job to Load Data";
    }
    $rows .= "<tr><td>$row</td><td>$next_action</td></tr>\n";
  }
  $thead = "<tr><th>". join("</th><th>",$headers_array) . "</th></tr>\n";
  print "<table><thead>$thead</thead><tbody>$rows</tbody></table>\n";
}

function BuildSearchForm($table) {
    global $db;
  $q = "SHOW COLUMNS FROM ?";
  $params = ($table);
  $stmt = $db->prepare($q);
  $stmt->execute($params);
  
  while ($myrow = $stmt->fetch(PDO::FETCH_ASSOC)) {
    extract($myrow);
    $type_info = preg_split("/[\(\)]/", $Type);
    $type = $type_info[0];
    $rows[$Field]['name'] = $Field;
    $rows[$Field]['type'] = $type;
    if ($Null == "YES") { $rows[$Field][null] = true; }
    else { $rows[$Field][null] = false; } 
  } //end while
  
  foreach ($rows as $field => $a) {
    $line = "";
    if ($a[type] == "int") { 
      $opts = array("=",">","<","!=","IS NULL","IS NOT NULL");
    }
    elseif ($a[type] == "varchar") {
      $opts = array("LIKE","NOT LIKE","=","!=","IS NULL","IS NOT NULL");
    }
    elseif ($a[type] == "date") {
      $opts = array("=",">","<","!=","IS NULL","IS NOT NULL");
    }
    elseif ($a[type] == "char") {
      $opts = array("LIKE","NOT LIKE","=","!=","IS NULL","IS NOT NULL");
    }
    else {$opts = array(); }
    $fieldname = $a[name];
    $line = "<tr><td>$fieldname</td><td><select name=\"operators[$fieldname]\">";
    foreach ($opts as $code) { 
      $line .= "<option value=\"$code\">$code</option>\n";
    } //end foreach option
    $line .= "</select></td> <td><input type=\"text\" name=\"values[$fieldname]\" value=\"".$_REQUEST[values][$fieldname]."\"></td></tr>\n";
    $lines .= $line;
  } //end foreach row
  
  $searchform_html = "<table>$lines</table>\n";
  
  return ($searchform_html);
} //function BuildSearchForm


function BuildWhereFromSearch($table) {
  $wheres = array();
  /* Creating an object for the SELECT query */
  //print_r($_REQUEST[value]);
  $operators = $_REQUEST[operators];
  //print_r($operators);
  foreach ($operators as $field => $operator) {
    $value = $_REQUEST[values][$field];
    if (($value != '') || (preg_match("/IS/",$operator))) {
      // if there's a value or the operator uses words like 'IS NULL'
      //    print "<br>$field|$operator|$value|\n";
      array_push($wheres, "`$field` $operator '$value'");
    }
  } //end foreach operator 
  
  $where = "WHERE (".join($wheres," AND ").")";
  return $where;
} //function BuildWhereFromSearch


/**********************
 * Prep file scripts
 *******************/


function PrepFile ($filename, $call_type="LC") { 
  global $secure_outside_path;
  /* SETUP VARIABLES */
  $handle = fopen("$secure_outside_path/upload/$filename", "r");
  $output_filename = "$secure_outside_path/prepped/$filename";
  $output_handle = fopen("$output_filename", "w");
  $data = array();
  $sort = array();
  if (! $output_handle) { return false; }
  
  // these arrays define variable names to be used later
  $fields = array ("author","title","pub_place","publisher","pub_date","lcsh","cat_date","loc","call_bib","call_item","volume","copy","bcode","mat_type","bib_record","item_record","oclc","total_circ","renews","int_use","last_checkin","barcode");

  // Define date condensations/transformations
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
      if (preg_match("/(\d\d\d\d)/",$pub_date,$n)) {
	$year = $n[1];
      } //end if numbers in pub info
      else { $year = ""; }

      // combine publisher and pub_place
      $pub = $publisher;
      if (isset($pub_place)) { 
	$pub .= "($pub_place)";
      }


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

	if (preg_match("/^i/", $item_record)) {// if there is an item record
	  if (! $data[$item_record]) { //if not already used
	    $data[$item_record] = "$call_order_is_blank\t$author\t$title\t$pub\t$year\t$lcsh\t$cat_date\t$loc\t$call_bib\t$call_item\t$volume\t$copy\t$bcode\t$mat_type\t$bib_record\t$item_record\t$oclc\t$total_circ\t$renews\t$int_use\t$last_checkin\t$barcode\n";
	    $sort[$item_record] = $call;
	  } //end if already used
	  else {
	  print "<li>Duplicate ItemKey: $key</li>\n";
	  }
	} //end if there's an item record
      } //end if not the data headers
    } //end while
    fclose($handle);
    
    //sort and print
    if ($call_type == "DDC") {
      asort($sort); //sort numerically if DDC
    }
    else { 
      uasort($sort, "SortLC"); //sort by LC Call Number
    }
    foreach($sort as $key => $value) {
      fwrite ($output_handle, $data[$key]);
    }

  } //end if handle
  return true;
} //end function PrepFile



function CreateTable ($table_name) {
  $q = TableTemplate($table_name);
  $r = mysql_query($q);
  if (mysql_errno() > 0) {
    PrintError(mysql_errno() . ": " . mysql_error(). ": $q");
    return false;
  } //end if error
  else {
    PrintSuccess("Created table: $table_name");
    return true;
  }
}

function LoadTable ($table, $file) {
  global $secure_outside_path;
  $local = "";
  if (UseLocalInfile()) { $local = "LOCAL"; }
  $q = "LOAD DATA $local INFILE '$secure_outside_path/prepped/$file' INTO TABLE `$table` FIELDS TERMINATED BY '\t' LINES TERMINATED BY '\n'";
  print $q . "\n";
  $r = mysql_query($q);
  if (mysql_errno() > 0) {
    $error = PrintError(mysql_errno() . ": " . mysql_error());;
    return false;
  } //end if error
  else {
    PrintSuccess ("LOADED FILE $file\n");
    return true;
  }
} //end function LoadFile

function UseLocalInfile () {
  /*returns true if the MySQL 'local_infile' config is turned on */
  $q="SHOW VARIABLES LIKE 'local_infile'";
  $r=mysql_query($q);
  if (mysql_num_rows($r) == 0) {
    $use_local_infile = false;
  }
  while ($myrow = mysql_fetch_assoc($r)) {
    if ($myrow['Value'] == "ON") {
      $use_local_infile = true;
    }
    else {
      $use_local_infile = false;
    }
  }
  return $use_local_infile;
}

?>