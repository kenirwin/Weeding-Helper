<?php  
session_start();
if ($_REQUEST[table]) { $_SESSION[weed_table] = $_REQUEST[table]; }
$table = $_SESSION[weed_table];
include('scripts.php');
include('mysql_connect.php');
$q = "SELECT file_title from `controller` where table_name = '$table'";
$r = mysql_query($q);
$myrow = mysql_fetch_row($r);
$title = $myrow[0];
?>
<html>
<head>
<title>Print View: <?php =$title;?></title>
<style>
table,td,th { 
  border-collapse: collapse; border: 1px solid black;
  vertical-align: top;
 }

td:nth-child(5),td:nth-child(6) { text-align: right }
</style>
<h1><?php =$title;?> (Print-friendly)</h1>
<?php 
include("nav.php");
?>

<form action="">
<label for="cmp_date">Show items with CATDATE</label>
<select name="before_after">
  <option value="<=">&lt;= (on or before)</option>
  <option value=">=">&gt;= (on or after)</option>
</select>
<input type="text" name="cmp_date" id="cmp_date" placeholder="yyyy-mm-dd" value="<?php =$_REQUEST['cmp_date'];?>" />
<br>
<label for="circ_count">Total Circs</label>
<select name="circ_count_operator">
  <option value="<=">&lt;= (less than or equal to)</option>
  <option value="=">= (equal to)</option>
  <option value=">=">&gt;= (greater than or equal to)</option>
</select>
<input type="text" name="circ_count" id="circ_count" placeholder="#" value="<?php =$_REQUEST['circ_count'];?>"/>

<br>
<label for="innreach_count">Total Innreach Copies</label>
<select name="innreach_count_operator">
  <option value="<=">&lt;= (less than or equal to)</option>
  <option value="=">= (equal to)</option>
  <option value=">=">&gt;= (greater than or equal to)</option>
</select>
<input type="text" name="innreach_count" id="innreach_count" placeholder="#" value="<?php =$_REQUEST['innreach_count'];?>"/>
<br>
<label for="innreach_circ_count">Total Innreach Circ Copies</label>
<select name="innreach_circ_count_operator">
  <option value="<=">&lt;= (less than or equal to)</option>
  <option value="=">= (equal to)</option>
  <option value=">=">&gt;= (greater than or equal to)</option>
</select>
<input type="text" name="innreach_circ_count" id="inreach_circ_count" placeholder="#" value="<?php =$_REQUEST['innreach_circ_count'];?>"/>


<input type="submit" value="Limit Records Displayed">
</form>

<?php 
  $valid_operators = array ("<=", "=", ">=");
if (is_numeric($_REQUEST['circ_count']) and (in_array($_REQUEST['circ_count_operator'], $valid_operators))) {
  $added_query = " AND `circs`" . $_REQUEST['circ_count_operator'].  $_REQUEST['circ_count'] . " ";
} //end if circ cmp

if (is_numeric($_REQUEST['innreach_count']) and (in_array($_REQUEST['innreach_count_operator'], $valid_operators))) {
  $added_query .= " AND `innreach_total_copies`" . $_REQUEST['innreach_count_operator'].  $_REQUEST['innreach_count'] . " ";
} //end if circ cmp

if (is_numeric($_REQUEST['innreach_circ_count']) and (in_array($_REQUEST['innreach_circ_count_operator'], $valid_operators))) {
  $added_query .= " AND `innreach_circ_copies`" . $_REQUEST['innreach_circ_count_operator'].  $_REQUEST['innreach_circ_count'] . " ";
} //end if circ cmp


if (preg_match("/\d\d\d\d-\d\d-\d\d/", $_REQUEST['cmp_date']) and (in_array($_REQUEST['before_after'], $valid_operators))) {
  $added_query .= "AND `catdate`" . $_REQUEST['before_after'] . "'" . $_REQUEST['cmp_date'] . "'";
} //end if date cmp

/* Find out if there are special settings for this table */
$q = "SELECT distinct `table_name` from `table_config` where `table_name`= '$table'"; 
$r = mysql_query($q);
if (mysql_num_rows($r) > 0 ) {
  $print_settings = $table;
}
else {
  $print_settings = "default";
}


$q1 = "SELECT field from `table_config` where `printable` = 'Y' and `table_name` = '$print_settings'";
$r1 = mysql_query($q1);
$fields = array();
while ($myrow = mysql_fetch_assoc($r1)) {
  array_push ($fields, $myrow[field]);
}
$fields = "`". join ("`,`", $fields) . "`";


$q = "SELECT $fields
FROM `$table`
where 1 $added_query
ORDER BY call_order";
//ORDER BY subclass,subj_starts";

if ($_REQUEST[before_after] || $_REQUEST[circ_count]) {
    $clear_button = '<a href="print.php?"><button><img src="images/delete.png" style="height: .75em">&nbsp;Remove Conditions</button></a>';
}
print "<div class=\"example\">$q<br>$clear_button</div>\n";
$r = mysql_query($q);
print ("<h3>".mysql_num_rows($r)." items</h3>\n");
if (function_exists("MysqlResultsTable")) {
  print(MysqlResultsTable($r));
}
else {
  print "no function";
}
?>

  <?php  include ("license.php"); ?>