<?php 
session_start();
require("mysql_connect.php");
if ($no_config) { 
  die(); 
}
?>
<html>
<head>
<title>All Tables - Weeding Helper</title>
<style>
.error { 
  background: #FFBABA none repeat scroll 0 0;
    border-color: #F74A4A;
    color: #D8000C;
  border-radius: 5px;
  font-size: 200%;
 margin: 5% 10%;
 }
.action img { height: 1em; } 
table { 
  border-collapse: collapse;
  margin-right: 5em;
 }
td,th {
border: 1px solid #ccc;
    padding: .25em
}
a.action img { border: none } 
//td:nth-child(9) { width: 250px }
# tooltip CSS by: Nikolay Izoderov
# http://www.splashnology.com/article/tooltips-in-css3-and-html5/4053/
.tooltip {
  border-bottom: 1px dotted #0077AA;
cursor: help;
 }
 
.tooltip::after {
background: rgba(0, 0, 0, 0.8);
  border-radius: 8px 8px 8px 0px;
  box-shadow: 1px 1px 10px rgba(0, 0, 0, 0.5);
color: #FFF;
content: attr(data-tooltip); /* The main part of the code, determining the content of the pop-up prompt */
  margin-top: -24px;
opacity: 0; /* Our element is transparent... */
padding: 3px 7px;
position: absolute;

visibility: hidden; /* ...and hidden. */
             
transition: all 0.4s ease-in-out; /* To add some smoothness */
 }
         
.tooltip:hover::after {
opacity: 1; /* Make it visible */
visibility: visible;
 }
td.button-cell {
  white-space:nowrap;
  padding-right: 1em;
}
</style>
<?php  include ("jquery.php"); ?>
</head>
<body>

<script>
$(document).ready(function() {
    $("a[href*='delete']").click(function (link) { 
	if (! confirm ("Really delete this file?")) {
	  link.preventDefault();
	}
      }); //end click delete
    $("a[href$='unstick']").click(function(link) {
	$(this).hide();
	if (! confirm("If InnReach checks stall out and aren't getting incrementing, use this to unstick the process: note - this will result in a value of -1 in the innreach fields. Do you wish to proceed?")) {
	  link.preventDefault();
        }
      }); //end click unstick
  }); //end onload
</script>

<h1>All Tables</h1>

<?php include("scripts.php");
include("nav.php");
if ($_REQUEST[action] == "choose_table") {
  print "<div class=\"error\">To edit a table, please select the table from one of the fully loaded tables below</div>\n";
}

if ($_REQUEST[delete]) {
  DeleteTable($_REQUEST[delete]);
}

if ($_SESSION[unstuck] != "") { 
  print "<li class=\"success\">Set InnReach info to -1 for record $_SESSION[unstuck]; InnReach checks should now continue normally</li>\n";
  $_SESSION[unstuck] = "";
}

DisplayProcessTable_v2();

function DisplayProcessTable_v2($sort="filename") {
  global $allow_delete;
  $q = "SELECT * FROM `controller` ORDER BY `$sort`";
  $r = mysql_query($q);
  while ($myrow=mysql_fetch_assoc($r)) {
    //    extract($myrow);
    $unstick = "";
    $headers_array = array_keys($myrow);
    array_push($headers_array, "Tools");
    // add spaces between words in column headers
    foreach ($headers_array as $k=>$v) {
      $headers_array[$k] = str_replace ("_", " ", $v);
    }
    if ($myrow[upload_date]) { 
      $myrow[upload_date] = date("Y-m-d",strtotime($myrow[upload_date]));
    }
    if ($myrow[load_date]) { 
      $myrow[load_date] = date("Y-m-d",strtotime($myrow[load_date]));
    }
    if ($myrow[innreach_finished]) {
      $myrow[innreach_finished] = "Y";
    }

    else {
      $ct_q = "SELECT count(*) FROM $myrow[table_name] WHERE innreach_circ_copies IS NOT NULL";
      $ct_r = mysql_query($ct_q);
      $ct_myrow=mysql_fetch_row($ct_r);
      $innr_count = $ct_myrow[0];
      if ($innr_count > 0) {
	$myrow[innreach_finished] =  "$innr_count of $myrow[records]";
	$unstick = MakeButton("unstick.php?table=$myrow[table_name]","","Unstick");
      }
      else {
	$myrow[innreach_finished] = "In Queue";
      }
    }
    
    $row = join ("</td><td>", array_values($myrow));
    $next_action = "";
    if ($myrow[load_date] != "") {
      $next_action = MakeButton ("view.php?table=$myrow[table_name]", "images/edit.png", "View/Edit");
      $next_action.= MakeButton ("print.php?table=$myrow[table_name]", "images/printer.png", "Print View");
      $next_action.= MakeButton ("cloud.php?table=$myrow[table_name]","images/tag-cloud.png","Keyword Cloud");
      $next_action.= MakeButton ("graph.php?table=$myrow[table_name]","images/bar-graph.png","Graphs");
      $next_action.= MakeButton ("settings.php?table=$myrow[table_name]","images/checkbox.png","View/Edit Settings");
      $next_action .= MakeButton ("csv.php?table=$myrow[table_name]","images/download.png","Download as CSV");
      if ($allow_delete) {
	$next_action.= MakeButton ("controller.php?delete=$myrow[table_name]","images/delete.png","Delete Table");
      } //if allow_delete = true
      $next_action .= $unstick;
    }
    else { 
      $next_action = "Awaiting Cron Job to Load Data";
      if ($allow_delete) {
	$next_action.= MakeButton ("controller.php?delete=$myrow[table_name]","images/delete.png","Delete Table");
      } //if allow_delete = true
    }
    $rows .= "<tr><td>$row</td><td class=\"button-cell\">$next_action</td></tr>\n";
  }
  $thead = "<tr><th>". join("</th><th>",$headers_array) . "</th></tr>\n";
  print "<table><thead>$thead</thead><tbody>$rows</tbody></table>\n";
}

function DeleteTable ($table) {
    global $secure_outside_path;
  /* DELETE CONTROLLER ENTRY */
  $q1 = "SELECT * FROM `controller` WHERE `table_name` = '$table'";
  $r1 = mysql_query($q1);
  $myrow = mysql_fetch_assoc($r1);
  extract($myrow); //this will get the $filename variable
  
  /* DROP THE MAIN DATA TABLE */
  $q2 = "DROP TABLE `$table`";
  if (mysql_query($q2)) { 
    $success .= "<li class=\"success\">Dropped table <b>`$table`</b> from database</li>\n";
  }
  else { 
    $fail .= "<li class=\"warn\">Could not drop table <b>`$table`</b> from database: ". mysql_error();
  } 
  
  /* DELETE THE UPLOAD AND PREP FILES */
  if ($filename) {
    $dirs = array ("upload", "prepped");
    foreach ($dirs as $dir) {
      $file = "$secure_outside_path/$dir/$filename";
      //print "<li>trying to delete: $file</li>\n";
      if (file_exists($file)) {
	//print "<li>found: $file</li>\n";
	if (unlink($file)) {
	  $success .= "<li class=\"success\">Delete file <b>$file</b></li>\n";
	}
	else {
	  $fail .= "<li class=\"warn\">Could not delete file <b>$file</b></li>\n";
	} //end else if not deleted
      } //end if file exists
    } //end foreach file
  } //if there was a filename in the table

  /* DELETE LINE FROM THE CONTROLLER TABLE */
  
  $q5 = "DELETE FROM `controller` WHERE `table_name` = '$table'";
  if (mysql_query($q5)) { 
    $success .= "<li class=\"success\">Deleted <b>$table</b> from controller table</li>\n";
  }
  else { 
    $fail .= "<li class=\"warn\">Could not delete <b>$table</b> from controller table: ". mysql_error();
  }

  print "$success$fail";
} //end DeleteTable

function MakeButton ($url, $img, $tooltip) {
  if (! $img == "") { $img = "<img src=\"$img\" alt=\"$tooltip\" />"; }
  else { $img = $tooltip; }
  $button = "<a href=\"$url\" class=\"action tooltip\" data-tooltip=\"$tooltip\">$img</a>";
  return $button;
}


include ("license.php"); 
?>
</body>
</html>
