<?php 
include("config.php");
if ($debug){ 
    error_reporting(E_ALL & ~E_NOTICE);
    ini_set('display_errors', 1);
}
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
# https://www.splashnology.com/article/tooltips-in-css3-and-html5/4053/
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

<?php 
include("scripts.php");
include("nav.php");
if ($_REQUEST['action'] == "choose_table") {
  print "<div class=\"error\">To edit a table, please select the table from one of the fully loaded tables below</div>\n";
}

if ($_REQUEST['delete']) {
  DeleteTable($_REQUEST['delete']);
}

if ($_SESSION['unstuck'] != "") { 
  print "<li class=\"success\">Set InnReach info to -1 for record $_SESSION[unstuck]; InnReach checks should now continue normally</li>\n";
  $_SESSION['unstuck'] = "";
}

DisplayProcessTable();

function DeleteTable ($table) {
    global $secure_outside_path, $db;
  /* DELETE CONTROLLER ENTRY */
  $q1 = "SELECT * FROM `controller` WHERE `table_name` = ?";
  $params = array($table);
  $stmt = $db->prepare($q);
  $myrow = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($myrow) {
      extract($myrow); //this will get the $filename variable
  }

  /* DROP THE MAIN DATA TABLE */
  if (ValidateTableName($table)) {
      $validated_table_name = $table;
      $q2 = "DROP TABLE `$validated_table_name`";
      try { 
          $stmt = $db->query($q2);
          $success .= "<li class=\"success\">Dropped table <b>`$validated_table_name`</b> from database</li>\n";
      } catch (PDOException $e) {
          $fail .= "<li class=\"warn\">Could not drop table <b>`$validated_table_name`</b> from database: ". $stmt->errorInfo();
      } 
  }
  else {
      $fail .= '<li class="\warn\">Invalid table name: '.$table.'</li>';
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
  
  $q5 = "DELETE FROM `controller` WHERE `table_name` = '$validated_table_name'";
  $stmt = $db->query($q5);
  if ($stmt) { 
    $success .= "<li class=\"success\">Deleted <b>$validated_table_name</b> from controller table</li>\n";
  }
  else { 
    $fail .= "<li class=\"warn\">Could not delete <b>$validated_table_name</b> from controller table: ". $stmt->errorInfo();
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
