<?php 
$debug = true;
if ($debug){ 
    error_reporting(E_ALL & ~E_NOTICE);
    ini_set('display_errors', 1);
}
session_start();
?>
<html>
<head>
<title>Display Settings - Weeding Helper</title>

<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js">
</script>

<?php 
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
# https://www.webstuffshare.com/2010/03/stylize-your-own-checkboxes/

</style>
</head>
<body>
<h1>Display Settings</h1>

<?php 
if (isset($_SESSION['weed_table']) &! isset($_REQUEST['table'])) {
  $_REQUEST['table'] = $_SESSION['weed_table'];
}
include ("nav.php");

if ($_REQUEST['submit_change_settings']) {
  //  print_r($_REQUEST);
  UpdateSettingsTable();
}

elseif ($_REQUEST['clone_settings_submit']) {
  CloneSettings($_REQUEST['clone_settings_table']);
  DisplayTableSettings($_REQUEST['clone_settings_table']);
  print (ChooseTable());
}

elseif ($_REQUEST['table']) {  
  /* this is invoked if coming to this page from a button on the main page
     FIRST: determine if table already has settings*/
  $q = "SELECT * FROM table_config WHERE `table_name` = ?";
  $params = array($_REQUEST['table']);
  $stmt = $db->prepare($q);
  $stmt->execute($params);
  
  /* if settings, show settings*/
  if ($stmt->rowCount() > 0) {
    print (ChooseTable());
    DisplayTableSettings($_REQUEST['table']);
  }

  /* if not settings, ask how to proceed: clone or work with default*/
  else {
    print "<h2>Settings have not yet been established for this table. Do you want to <a href=\"settings.php?clone_settings_submit=true&clone_settings_table=$_REQUEST[table]\" class=\"action\">Create Settings for This Table</a> or <a href=\"settings.php?choose_table=default\" class=\"action\">Edit the Default Settings</a></h2>\n";
  } 

} //end else if REQUEST[table]

if ($_REQUEST['choose_table']) {
  print (ChooseTable());
  DisplayTableSettings($_REQUEST['choose_table']);
}

function CloneSettings($clone_to, $clone_from="default") {
    global $db;
    $errors = 0;
    $error_messages = '';
  $q = "SELECT * FROM `table_config` WHERE `table_name` = '$clone_from'";
  $params = array($clone_from);
  $stmt = $db->prepare($q);
  $stmt->execute($params);
  //  print "<li>$q</li>\n";

  while ($myrow = $stmt->fetch(PDO::FETCH_ASSOC)) {
      try {
    $q = "INSERT INTO `table_config` VALUES (?,?,?,?)";
    $params = array($clone_to,$myrow['action'],$myrow['field'],$myrow['printable']);
    $stmt2 = $db->prepare($q);
    $stmt2->execute($params);
      } catch (PDOException $e) {
          $errors++;
          $error_messages.= '<li>'.$e->getMessage().'</li>'.PHP_EOL;
      }
    
  } // end while
  if ($errors > 0) {
    print "<h2 class=\"warn\">$errors Errors!</h2>";
    print "<ul>$error_messages</ul>\n";
  } //end if errors
  else {
    print "<h3 class=\"success\">Settings cloned!</h3>\n";
  }

} //end function CloneSettings

function ChooseTable($last_table_used = '') {
    global $db;
  $file_titles = GetTableNames();
  foreach ($file_titles as $table_name => $display_name) {
    $opts .= "<option value=\"$table_name\">$display_name ($table_name)</option>\n";
  }
  $form1 =  "<form id=\"choose\" method=\"post\" action=\"settings.php\"><select name=\"choose_table\"><option>Choose a table to edit</option>$opts</select><input type=\"submit\"></form>\n";
  
  $q = "SELECT table_name,file_title FROM `controller` WHERE table_name NOT IN (SELECT distinct(table_name) FROM table_config)";
  $stmt = $db->query($q);
  while ($myrow = $stmt->fetch(PDO::FETCH_ASSOC)) {
    extract($myrow);
    $opts2 .= "<option value=\"$table_name\">$table_name ($file_title)</option>\n";

    $form2 = "<form id=\"clone-settings-form\" method=\"post\">OR Establish settings for another table: <select name=\"clone_settings_table\">$opts2</select><input type=\"submit\" name=\"clone_settings_submit\" value=\"Clone Settings from Default\"></form>\n";
  }//end while

  return ("$form1 - $form2");
} //end function Choose Table



function UpdateSettingsTable() {
  //print_r ( $_REQUEST);
  /* Set all printable values to NO; all yeses will be activated below */
    global $db;
    $errors = 0; $error_messages = '';
    try { 
  $q = "UPDATE `table_config` SET printable='N' WHERE `table_name`= ?";
  $params = array($_REQUEST['table_name']);
  $stmt = $db->prepare($q);
  $stmt->execute($params);
    } catch (PDOException $e) {
        $errors++;
        $error_messages .= '<li>'.$e->getMessage().'</li>'.PHP_EOL;
    }
  foreach ($_REQUEST as $k=>$v) {
    if (preg_match("/^value_(.*)/", $k, $m)) {
        try {
      $q = "UPDATE `table_config` SET action='$v' WHERE `table_name`='$_REQUEST[table_name]' AND field = '$m[1]'";
      $params = array($_REQUEST['table_name']);
      $stmt = $db->prepare($q);
      $stmt->execute($params);
    } catch (PDOException $e) {
        $errors++;
        $error_messages .= '<li>'.$e->getMessage().'</li>'.PHP_EOL;
    }

    } //end if value


    elseif (preg_match("/^print_(.*)/", $k, $m)) {
      if ($v == "on") { $p = "Y"; }
      else { $p = "N"; }
      try { 
      $q = "UPDATE `table_config` SET printable=? WHERE `table_name`=? AND field = ?";
      $params = array($p, $_REQUEST['table_name'], $m[1]);
      $stmt = $db->prepare($q);
      $stmt->execute($params);
    } catch (PDOException $e) {
        $errors++;
        $error_messages .= '<li>'.$e->getMessage().'</li>'.PHP_EOL;
    }

    } //end if value
  }//end foreach Request value

  if ($errors > 0) {
    print "<h2 class=\"warn\">$errors Errors!</h2>";
    print "<ul>$error_messages</ul>\n";
  } //end if errors
  else {
    print "<h3 class=\"success\">Changes made!</h3>\n";
  }
} //end function UpdateSettingsTable



function DisplayTableSettings($table) {
    global $db;
  $file_titles = GetTableNames();
  print "<h2>Table Settings: $file_titles[$table] ($table)</h2>\n";
  $q = "SELECT * FROM `table_config` WHERE `table_name` = ?";
  $params = array($table);
  $stmt = $db->prepare($q);
  $stmt->execute($params);

  while ($myrow = $stmt->fetch(PDO::FETCH_ASSOC)) {
    extract($myrow);
    $settings[$field] = $action;
    $print[$field] = $printable;
  } //end while 

  if ($table == "default") {
    $field_query = "SHOW COLUMNS FROM '$table'";
    $fields = array("call_order","author","title","publisher","year","lcsh","catdate","loc","call_bib","call_item","volume","copy","bcode","mat_type","bib_record","item_record","oclc","circs","renews","int_use","last_checkin","barcode","subclass","subj_starts","classic","best_book","condition","notes","fate","innreach_circ_copies","innreach_total_copies");
  }
  else {
      $verified_table_name = VerifyTableName($table);
    $q = "SHOW COLUMNS FROM $verified_table_name";
    print $q;
    $stmt = $db->query($q);
    $fields = array();
    while ($myrow=$stmt->fetch(PDO::FETCH_ASSOC)) {
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
<?php  include("license.php"); ?>
</body>
</html>