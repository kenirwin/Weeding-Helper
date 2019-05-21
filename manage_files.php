<html>
<head><title>Upload New Review File - Weeding Helper</title>
<link rel="stylesheet" href="style.css" />
</head>

<body>
<h1>Manage Files</h1>


<?php
include ("config.php");
require("mysql_connect.php");
include("nav.php");
include("scripts.php");

if (! isset($allow_manage) || $allow_manage != true) {
  print "<h3>This function is not enabled.</h3>";
  print "<p>To enable this function, change the \$allow_manage setting to TRUE in config.php</p>"; 
}
else {
  if (isset($_REQUEST['action'])) {
    switch ($_REQUEST['action']) {
    case ('update_count'):
      if (isset($_REQUEST['table_to_recount'])) {
	if (UpdateCount($_REQUEST['table_to_recount'])) {
	  $success.= PrintSuccess ('Updated count on '.$_REQUEST['table_to_recount'], true);
	}
	else {
	  $errors .= PrintError ('Could not update count on '.$_REQUEST['table_to_recount'], true);
	}
      }
      else {
	$errors .= PrintError('No table specified to update count',true);
      }
      break; 
      
    case ('combine'): 
      $required_fields = array("file_title","table_name","user");
      foreach ($required_fields as $field) {
	if (strlen($_REQUEST[$field]) == 0) {
	  $errors .= PrintError('Missing required field: '.$field.''.$_REQUEST[$field], true);
	}
      } //end foreach field

      if (sizeof($_REQUEST['combine_table']) < 2) {
	$errors .= PrintError('You must select at least choose two files in order to combine files', true);
      }

      if (! $errors)  { 
	CombineTables($_REQUEST['combine_table'], $_REQUEST['table_name'], $_REQUEST['file_title'], $_REQUEST['user']);
      } //end if no errors / combining files
      break;
    
    } //end switch action
    print "$errors";
    print "$success";
  }
?>

<h2>Recount File</h2>
   <p>If you have modified a file on the "back end" of the database by adding or deleting some records, use this function to update the count of records in a file.</p>

<?php print (UpdateCountForm()); ?>

<hr />
<h2>Combine Files</h2>
   <p>To combine two or more files into a single file, select the files and add appropriate metadata below.<//p>
<?php print (CombineForm()); ?>
   <?php } //end else if allow_manage == true ?>
<?php  include ("license.php"); ?>
</body>
</html>

<?php
function UpdateCount($table_name) {
    global $db;
    $verified_table_name = VerifyTableName($table_name);
  $q = "UPDATE `controller` SET `records` = (SELECT count(*) FROM `$verified_table_name` WHERE 1) WHERE `table_name` = ?";
  $params = array($table_name);
  $stmt = $db->prepare($q);
  $stmt->execute($params);
  return $stmt; 
  } //end UpdateCount

function UpdateCountForm() {
    global $db;
  $options = '<option value="">Update the record count for a selected file';
  $q="SELECT * FROM `controller` ORDER BY `filename` ASC";
  $stmt = $db->query($q);
  while ($myrow=$stmt->fetch(PDO::FETCH_ASSOC)) {
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
    global $db;
  $options = "";
  $q="SELECT * FROM `controller` ORDER BY `filename` ASC";
  $stmt = $db->query($q);
  while ($myrow=$stmt->fetch(PDO::FETCH_ASSOC)) {
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

function CombineTables($tables, $new_table_name, $file_title, $user) {
    global $db, $secure_outside_path;
    $placeholders = '';
    for ($i = 0; $i<sizeof($tables); $i++) {
        $placeholders .= '?';
        if ($i+1 < sizeof($tables)) {
            $placeholders .= ',';
        }
    }
    try {
    $q = 'SELECT DISTINCT `call_type` FROM `controller` WHERE `table_name` in ('.$placeholders.')';
    $stmt = $db->prepare($q);
    $stmt->execute($tables);
    $myrow = $stmt->fetch(PDO::FETCH_NUM);
    $call_type = $myrow[0];
    } catch (PDOException $e) {
        print ($e->getMessage);
    }

    $fail = false;
    if (! ValidateTableName($new_table_name)) { die (PrintError('Invalid New Table Name')); }
    $q = "SHOW TABLES LIKE '$new_table_name'";
    $stmt = $db->query($q);
    if ($stmt->rowCount() > 0) {
        PrintError('Table `'.$new_table_name.'` already exists; choose another name');
        $fail = true;
  }
  else { 
    $calls = array();
    $content = array();
    foreach ($tables as $table) {
    if (! ValidateTableName($table)) { die (PrintError('Invalid Table Name: '.$table)); }
      $q = 'SELECT * FROM `'.$table.'`';
      $stmt = $db->query($q);
      while ($myrow = $stmt->fetch(PDO::FETCH_ASSOC)) {
	extract($myrow);
	unset($call);
	if (strlen($call_item)>1) {
	  $call = $call_item; // item call # supercedes bib # if both present
	}
	elseif (strlen($call_bib)>1) { 
	  $call = $call_bib;
	}
	else { $call = ""; }

	$calls[$item_record] = $call;
	$content[$item_record] .= "$call_order_is_blank\t$author\t$title\t$pub\t$year\t$lcsh\t$cat_date\t$loc\t$call_bib\t$call_item\t$volume\t$copy\t$bcode\t$mat_type\t$bib_record\t$item_record\t$oclc\t$total_circ\t$renews\t$int_use\t$last_checkin\t$barcode\n";
      
      }
    } //end foreach table

  } //end else if no errors yet
  print $errors;
  include("sortLC.php");
  uasort($calls, "SortLC");
  $filename = "$new_table_name.txt";
  $filepath = "$secure_outside_path/prepped/$filename";
  $file = fopen($filepath,"w");
  if ($file) {
    foreach($calls as $item=>$call) {
      fwrite($file, $content[$item]);
    }
  } 
  else { 
      PrintError('Unable to open file for writing in "prepped" directory '); 
  }
  fclose($file);
  
  if (CreateTable($new_table_name)) {
      global $db;
    //  fwrite ($log, "$now - LoadTable: $table_name, $filename");
    if (LoadTable($new_table_name, $filename)) {
        if (! ValidateTableName($new_table_name)) { die (PrintError('Invalid New Table Name')); }
        $q = "SELECT count(*) FROM `$new_table_name`";
        $stmt = $db->query($q);
        $myrow = $stmt->fetch(PDO::FETCH_NUM);
        $records = $myrow[0];
        try {
            $q = "INSERT INTO `controller` (`filename`,`file_title`,`table_name`,`user`,`records`,`call_type`,`upload_date`,`load_date`) VALUES (?, ?, ?, ?, ?, ?, now(), now())";
            $params = array ($filename,$file_title,$new_table_name,$user,$records, $call_type);
            $stmt = $db->prepare($q);
            if ($stmt->execute($params)) {
                PrintSuccess("Added table to controller");
            }
        } catch (PDOException $e) {
            PrintError('Could not add to controller table: '.$e->getMessage());
        }


    }
    else { print "FAILED: Cound not load data from $filename into $table_name\n";}
  } //end if CreateTable 
  else { 
    print "FAILED: Could not create table $new_table_name\n";
  }
}


?>

