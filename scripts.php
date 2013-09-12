<?php 

function GetTableNames() {
  //returns an array of file_titles by table_name
  //e.g. $file_titles = array("econ" => "Economics Books");
  $q = "SELECT `table_name`,`file_title` FROM `controller` WHERE 1";
  $r = mysql_query($q);
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
  $q = "SHOW COLUMNS FROM `$table`";
  $r = mysql_query($q);
  
  while ($myrow = mysql_fetch_assoc($r)) {
    extract($myrow);
    $type_info = preg_split("/[\(\)]/", $Type);
    $type = $type_info[0];
    $rows[$Field][name] = $Field;
    $rows[$Field][type] = $type;
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

?>