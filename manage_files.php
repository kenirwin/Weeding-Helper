<?
session_start();
require("mysql_connect.php");
error_reporting('E_ALL');
?>
<h1>Manage Files</h1>
<link rel="stylesheet" href="style.css" />

  <? 
  if (isset($_REQUEST['action'])) {
    print_r($_REQUEST);

    switch ($_REQUEST['action']) {
    case ('update_count'):
      if (isset($_REQUEST['table_to_recount'])) {
	  if (UpdateCount($_REQUEST['table_to_recount'])) {
	    $success.= '<li class="success">SUCCESS: Updated count on '.$_REQUEST['table_to_recount'].'</li>'.PHP_EOL;
	  }
	  else {
	    $errors .= '<li class="warn">Could not update count on '.$_REQUEST['table_to_recount'].'</li>'.PHP_EOL;
	  }
	}
	else {
	  $errors .= '<li class="warn">No table specified to update count</li>'.PHP_EOL;
	}
      break; 

    }
    print "$errors";
    print "$success";
  }
?>

<h2>Recount File</h2>
<? print (UpdateCountForm()); ?>


<h2>Combine Files</h2>
<? print (CombineForm()); ?>

<?php  include ("license.php"); ?>
</body>
</html>

<?
function UpdateCount($table_name) {
  $q = "UPDATE `controller` SET `records` = (SELECT count(*) FROM `$table_name` WHERE 1) WHERE `table_name` = '$table_name'";
  $r = mysql_query($q);
  return $r; 
  } //end UpdateCount

function UpdateCountForm() {
  $options = '<option value="">Update the record count for a selected file';
  $q="SELECT * FROM `controller` ORDER BY `filename` ASC";
  $r=mysql_query($q);
  while ($myrow=mysql_fetch_assoc($r)) {
    extract($myrow);
    $options .= '<option value="'.$table_name.'">'.$file_title.' ('.$table_name.')</option>'.PHP_EOL;
  }
  $form .= '<form action="?" method="post">'.PHP_EOL;
  $form .= '<input type="hidden" name="action" value="update_count" />'.PHP_EOL;
  $form .= '<select name="table_to_recount">'.PHP_EOL;
  $form .= $options;
  $form .= '</select>'.PHP_EOL;
  $form .= '<input type="submit" name="update_count_submit" value="Update Record Count" />'.PHP_EOL;
  $form .= '</form>'.PHP_EOL;
  
  return $form;
}

function CombineForm() {
  $options = "";
  $q="SELECT * FROM `controller` ORDER BY `filename` ASC";
  $r=mysql_query($q);
  while ($myrow=mysql_fetch_assoc($r)) {
    extract($myrow);
    $options .= '<tr><td><input type="checkbox" name="combine_table[]" value="'.$table_name.'" /></td> <td>'.$file_title.'</td> <td>'.$table_name.'</td> <td>'.$records.' records</td></tr>'.PHP_EOL;
  }

    $form_data_fields = <<<EOT
<p>
<label for="file_title">File title</label>
<input name="file_title" type="text" />
<span class="fieldnote">This is a nice display name, e.g. "Main Stacks History Books"</span>
</p>
<p>
<label for="table_name">Table name</label>
<input name="table_name" type="text" />
<span class="fieldnote">Something short with NO SPACES or funny characters, for the MySQL table name, like "history"</span>
</p>
<p>
<label for="user">User</label>
<input name="user" type="text" />
<span class="fieldnote">Who are you?</span>
</p>
EOT;

  $form = '<form action="?" method="post">'.PHP_EOL;
  $form.= '<input type="hidden" name="action" value="combine">'.PHP_EOL;
  $form.= '<table id="combine_form_table">'.PHP_EOL;
  $form.= $options;
  $form.= '</table>'.PHP_EOL;
  $form.= $form_data_fields;
  $form.= '<input type="submit">'.PHP_EOL;
  $form.= '</form>'.PHP_EOL;

  return $form;
  }
?>

