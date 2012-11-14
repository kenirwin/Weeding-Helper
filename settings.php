<html>
<head>
<title>Display Settings - Weeding Helper</title>

<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js">
</script>

<?
session_start();
include ("config.php");
include ("mysql_connect.php");
include ("jquery.php");
include ("iphone-toggle.php");
include ("scripts.php");
?>

<!--link rel="stylesheet" type="text/css" media="screen, projection" href="demo.css" /-->
<style>
form { display: inline }

# stylized checkboxes by: Hidayat Sagita 
# http://www.webstuffshare.com/2010/03/stylize-your-own-checkboxes/

</style>
</head>
<body>
<h1>Display Settings</h1>

<?


include ("nav.php");

if ($_REQUEST[submit_change_settings]) {
  //  print_r($_REQUEST);
  UpdateSettingsTable();
}

elseif ($_REQUEST[clone_settings_submit]) {
  CloneSettings($_REQUEST[clone_settings_table]);
}

print (ChooseTable());

if ($_REQUEST[choose_table]) {
  DisplayTableSettings($_REQUEST[choose_table]);
}

function CloneSettings($clone_to, $clone_from="default") {
  $q = "SELECT * FROM `table_config` WHERE `table_name` = '$clone_from'";
  $r = mysql_query($q);
  while ($myrow = mysql_fetch_assoc($r)) {
    $q = "INSERT INTO `table_config` VALUES ('$clone_to','$myrow[action]','$myrow[field]','$myrow[printable]')";
    mysql_query($q);
    if (mysql_errno() > 0) {
      $errors++;
      $error_messages .= "<li>Error #".mysql_errno().": ". mysql_error()."</li>\n";
    }
  } // end while
  if ($errors) {
    print "<h2 class=\"warn\">$errors Errors!</h2>";
    print "<ul>$error_messages</ul>\n";
  } //end if errors
  else {
    print "<h3 class=\"success\">Settings cloned!</h3>\n";
  }

} //end function CloneSettings

function ChooseTable($last_table_used) {
  $file_titles = GetTableNames();
  $q = "SELECT distinct(table_name) from `table_config`";
  $r = mysql_query($q);
  while ($myrow = mysql_fetch_assoc($r)) {
    extract($myrow);
    $opts .= "<option value=\"$table_name\">$file_titles[$table_name] ($table_name)</option>\n";
  }
  $form1 =  "<form id=\"choose\" method=\"post\"><select name=\"choose_table\"><option>Choose a table to edit</option>$opts</select><input type=\"submit\"></form>\n";
  
  $q = "SELECT table_name,file_title FROM `controller` WHERE table_name NOT IN (SELECT distinct(table_name) FROM table_config)";
  $r = mysql_query($q);
  while ($myrow = mysql_fetch_assoc($r)) {
    extract($myrow);
    $opts2 .= "<option value=\"$table_name\">$table_name ($file_title)</option>\n";

    $form2 = "<form id=\"clone-settings-form\" method=\"post\">OR Establish settings for another table: <select name=\"clone_settings_table\">$opts2</select><input type=\"submit\" name=\"clone_settings_submit\" value=\"Clone Settings from Default\"></form>\n";
  }//end while

  return ("$form1 - $form2");
} //end function Choose Table



function UpdateSettingsTable() {
  //print_r ( $_REQUEST);
  /* Set all printable values to NO; all yeses will be activated below */
  $q = "UPDATE `table_config` SET printable='N' WHERE `table_name`='$_REQUEST[table_name]'";
  mysql_query($q);
  if (mysql_errno() > 0) {
    $errors++;
    $error_messages .= "<li>Error #".mysql_errno().": ". mysql_error()."</li>\n";
  }
  
  foreach ($_REQUEST as $k=>$v) {
    if (preg_match("/^value_(.*)/", $k, $m)) {
      $q = "UPDATE `table_config` SET action='$v' WHERE `table_name`='$_REQUEST[table_name]' AND field = '$m[1]'";
      mysql_query($q);
      if (mysql_errno() > 0) {
	$errors++;
	$error_messages .= "<li>Error #".mysql_errno().": ". mysql_error()."</li>\n";
      }
    } //end if value


    elseif (preg_match("/^print_(.*)/", $k, $m)) {
      if ($v == "on") { $p = "Y"; }
      else { $p = "N"; }
      $q = "UPDATE `table_config` SET printable='$p' WHERE `table_name`='$_REQUEST[table_name]' AND field = '$m[1]'";
      //print "<p>$q</p>\n";
      mysql_query($q);
      if (mysql_errno() > 0) {
	$errors++;
	$error_messages .= "<li>Error #".mysql_errno().": ". mysql_error()."</li>\n";
      }
    } //end if value
  }//end foreach Request value

  if ($errors) {
    print "<h2 class=\"warn\">$errors Errors!</h2>";
    print "<ul>$error_messages</ul>\n";
  } //end if errors
  else {
    print "<h3 class=\"success\">Changes made!</h3>\n";
  }
} //end function UpdateSettingsTable



function DisplayTableSettings($table) {
  $file_titles = GetTableNames();
  print "<h2>Table Settings: $file_titles[$table] ($table)</h2>\n";
  $q = "SELECT * FROM `table_config` WHERE `table_name` = '$table'";
  $r = mysql_query($q);
  while ($myrow = mysql_fetch_assoc($r)) {
    extract($myrow);
    $settings[$field] = $action;
    $print[$field] = $printable;
  } //end while 

  if ($table == "default") {
    $fields = array("call_order","author","title","publisher","year","lcsh","catdate","loc","call","bcode","mat_type","bib_record","circs","enews","int_use","last_checkin","items","circ_items","subclass","subj_starts","classic","best_book","condition","notes","fate","innreach_circ_copies","innreach_total_copies");
  }
  else {
    $q = "SHOW COLUMNS FROM `$table`";
    $r= mysql_query($q);
    $fields = array();
    while ($myrow=mysql_fetch_assoc($r)) {
      extract($myrow);
      array_push($fields, $Field);
    } //end while
  } //end else if not default
  
  foreach($fields as $field) {
    if ($print[$field] == "Y") { $checked = "checked"; }
    else { $checked = ""; }
    $checkbox = "<input type=\"checkbox\" class=\"iphone-style-checkbox\" name=\"print_$field\" id=\"print_$field\" $checked/>\n";

    $select_edit = $select_read = $select_hide = "";
    switch ($settings[$field]) {
    case "":
      $select_edit = " SELECTED";
      break;
    case "disallowEdit":
      $select_read = " SELECTED";
      break;
    case "omitField":
      $select_hide = " SELECTED";
      break;
    } //end switch
      
    $chooser = "<select name=\"value_$field\">";
    $chooser .= "<option value=\"\"$select_edit>Display / Editable</option>\n";
    $chooser .= "<option value=\"disallowEdit\"$select_read>Display / Read-only</option>\n";
    $chooser .= "<option value=\"omitField\"$select_hide>Hide</option>\n";
    $lines .= "<tr><td>$field</td> <td>$chooser</td> <td>$checkbox</td></tr>\n";
  } //end foreach field;

  $thead = "<tr><th>Field</th> <th>View/Edit</th> <th>Print View</th></tr>\n";
  print "<form id=\"change-settings\" method=\"post\"><input type=\"hidden\" name=\"table_name\" value=\"$table\"><table><thead>$thead</thead> <tbody>$lines</tbody></table><input type=\"submit\" name=\"submit_change_settings\"></form>\n";
} // end DisplayTableSettings


?>
<? include("license.php"); ?>
</body>
</html>