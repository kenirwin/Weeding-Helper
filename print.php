<?php  
include ('config.php');
if ($debug){ 
    error_reporting(E_ALL & ~E_NOTICE);
    ini_set('display_errors', 1);
}

session_start();
if ($_REQUEST['table']) { $_SESSION['weed_table'] = $_REQUEST['table']; }
$table = $_SESSION['weed_table'];
include('scripts.php');
include('mysql_connect.php');
$q = "SELECT file_title from `controller` where table_name = ?";
$params = array($table);
$stmt = $db->prepare($q);
$stmt->execute($params);
$myrow = $stmt->fetch(PDO::FETCH_ASSOC);
$title = $myrow[0];
?>
<html>
<head>
<title>Print View: <?php print($title);?></title>
<style>
table,td,th { 
  border-collapse: collapse; border: 1px solid black;
  vertical-align: top;
 }

td:nth-child(5),td:nth-child(6) { text-align: right }
</style>
<h1><?php print($title);?> (Print-friendly)</h1>
<?php 
include("nav.php");
?>

<form action="">
<table>
<tr>
<td><label for="cmp_date">Show items with CATDATE</label></td>
<td><select name="before_after">
  <option value="<=">&lt;= (on or before)</option>
  <option value=">=">&gt;= (on or after)</option>
</select></td>

<td><input type="text" name="cmp_date" id="cmp_date" placeholder="yyyy-mm-dd" value="<?php print($_REQUEST['cmp_date']);?>" /></td>
</tr>

<tr>
<td><label for="circ_count">Total Circs</label></td>
<td>
<select name="circ_count_operator">
  <option value="<=">&lt;= (less than or equal to)</option>
  <option value="=">= (equal to)</option>
  <option value=">=">&gt;= (greater than or equal to)</option>
</select>
</td>
<td>
<input type="text" name="circ_count" id="circ_count" placeholder="#" value="<?php print($_REQUEST['circ_count']);?>"/>
</td>
</tr>

<tr>
<td><label for="innreach_count">Total Innreach Copies</label></td>
<td><select name="innreach_count_operator">
  <option value="<=">&lt;= (less than or equal to)</option>
  <option value="=">= (equal to)</option>
  <option value=">=">&gt;= (greater than or equal to)</option>
</select>
</td>
<td><input type="text" name="innreach_count" id="innreach_count" placeholder="#" value="<?php print($_REQUEST['innreach_count']);?>"/></td>
</tr>

<tr>
<td><label for="innreach_circ_count">Total Innreach Circ Copies</label></td>
<td><select name="innreach_circ_count_operator">
  <option value="<=">&lt;= (less than or equal to)</option>
  <option value="=">= (equal to)</option>
  <option value=">=">&gt;= (greater than or equal to)</option>
</select></td>
<td><input type="text" name="innreach_circ_count" id="inreach_circ_count" placeholder="#" value="<?php print($_REQUEST['innreach_circ_count']);?>"/></td>
</tr>
<tr>
<td><label for="fate_operator">Fate</label></td>
<td><select name="fate_operator">
    <option value="">--Select One--</option>
    <option value="=" <?php if ($_REQUEST['fate_operator'] == "=") { print "SELECTED"; }?>>= (equals)</option>
    <option value="!=" <?php if ($_REQUEST['fate_operator'] == "!=") { print "SELECTED"; }?>>!= (not equals)</option>
</select></td>
<td><select name="fate_value">
    <option value="">None Selected</option>
    <option value="weed" <?php if ($_REQUEST['fate_value'] == "weed") { print "SELECTED"; }?>>weed</option>
<option value="de-dup" <?php if ($_REQUEST['fate_value'] == "de-dup") { print "SELECTED"; }?>>de-dup</option>
<option value="replace" <?php if ($_REQUEST['fate_value'] == "replace") { print "SELECTED"; }?>>replace</option>
<option value="update" <?php if ($_REQUEST['fate_value'] == "update") { print "SELECTED"; }?>>update</option>
<option value="keep" <?php if ($_REQUEST['fate_value'] == "keep") { print "SELECTED"; }?>>keep</option>
<select>
</td>
</tr>
</table>


<input type="submit" value="Limit Records Displayed">
</form>

<?php
    $print_params = array();
 
  $valid_operators = array ("<=", "=", ">=");
if (is_numeric($_REQUEST['circ_count']) and (in_array($_REQUEST['circ_count_operator'], $valid_operators))) {
    array_push($print_params, $_REQUEST['circ_count']);
  $added_query = " AND `circs`" . $_REQUEST['circ_count_operator']. " ? ";
} //end if circ cmp

if (is_numeric($_REQUEST['innreach_count']) and (in_array($_REQUEST['innreach_count_operator'], $valid_operators))) {
    array_push($print_params, $_REQUEST['innreach_count']);
  $added_query .= " AND `innreach_total_copies`" . $_REQUEST['innreach_count_operator'].  " ? ";
} //end if circ cmp

if (is_numeric($_REQUEST['innreach_circ_count']) and (in_array($_REQUEST['innreach_circ_count_operator'], $valid_operators))) {
    array_push($print_params, $_REQUEST['innreach_circ_count']);
  $added_query .= " AND `innreach_circ_copies`" . $_REQUEST['innreach_circ_count_operator']. " ? ";
} //end if circ cmp


if (preg_match("/\d\d\d\d-\d\d-\d\d/", $_REQUEST['cmp_date']) and (in_array($_REQUEST['before_after'], $valid_operators))) {
    array_push($print_params,$_REQUEST['cmp_date']);
  $added_query .= "AND `catdate`" . $_REQUEST['before_after'] . " ? ";
} //end if date cmp

$allowed_fate_ops = array("=", "!=");
if (isset($_REQUEST['fate_operator']) && in_array($_REQUEST['fate_operator'], $allowed_fate_ops)) {
    array_push($print_params,$_REQUEST['fate_value']);
    $added_query .= 'AND `fate` '. $_REQUEST['fate_operator'] . " ? "; 
}


/* Find out if there are special settings for this table */
$q = "SELECT distinct `table_name` from `table_config` where `table_name`= ?"; 
$params = array($table);
$stmt = $db->prepare($q);
$stmt->execute($params);
if ($stmt->rowCount() > 0 ) {
  $print_settings = $table;
}
else {
  $print_settings = "default";
}


$q1 = "SELECT field from `table_config` where `printable` = 'Y' and `table_name` = ?";
$params = array($print_settings);
$stmt = $db->prepare($q1);
$stmt->execute($params);

$fields = array();
while ($myrow = $stmt->fetch(PDO::FETCH_ASSOC)) {
  array_push ($fields, $myrow['field']);
}
$fields = "`". join ("`,`", $fields) . "`";

$verified_table = VerifyTableName($table);

$q = "SELECT $fields
FROM `$verified_table`
where 1 $added_query
ORDER BY call_order";
//ORDER BY subclass,subj_starts";

if ($_REQUEST['before_after'] || $_REQUEST['circ_count']) {
    $clear_button = '<a href="print.php?"><button><img src="images/delete.png" style="height: .75em">&nbsp;Remove Conditions</button></a>';
}
print "<div class=\"example\">$q<br>$clear_button</div>\n";
$stmt = $db->prepare($q);
$stmt->execute($print_params);

print ("<h3>".$stmt->rowCount()." items</h3>\n");
if (function_exists("MysqlResultsTable")) {
  print(MysqlResultsTable($stmt));
}
else {
  print "no function";
}
?>

  <?php  include ("license.php"); ?>