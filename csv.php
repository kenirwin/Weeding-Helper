<?php 
  /*
    The CSV generating code here is a modified version of Jrgns's code:
    http://stackoverflow.com/a/125578
   */

session_start();
include ("config.php");
include ("mysql_connect.php");
if (isset($_SESSION['weed_table']) &! isset($_REQUEST['table'])) {
  $_REQUEST['table'] = $_SESSION['weed_table'];
}
elseif (isset($_REQUEST['table'])) {
  $_SESSION['weed_table'] = $_REQUEST['table'];
}

$table = $_REQUEST['table'];
$output = "";
$q = "SELECT * FROM `$table`";
$r = mysql_query($q);


$num_fields = mysql_num_fields($r);
$headers = array();
for ($i = 0; $i < $num_fields; $i++) {
  $headers[] = mysql_field_name($r , $i);
}

$fp = fopen('php://output', 'w');
if ($fp && $r) {
  header('Content-Type: text/csv');
  header('Content-Disposition: attachment; filename="'.$table.'.csv"');
  header('Pragma: no-cache');
  header('Expires: 0');
  fputcsv($fp, $headers);
  while ($row = mysql_fetch_array($r, MYSQL_NUM)) {
    fputcsv($fp, array_values($row));
  }
  die;
}
?>