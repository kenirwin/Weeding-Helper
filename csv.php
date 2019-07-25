<?php 
  /*
    The CSV generating code here is a modified version of Jrgns's code:
    https://stackoverflow.com/a/125578
   */

include ("config.php");
if ($debug){ 
    error_reporting(E_ALL & ~E_NOTICE);
    ini_set('display_errors', 1);
}

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
if (isset($_REQUEST['copytwo']) && ($_REQUEST['copytwo'] == true)) {
    $whereCopyTwo = "WHERE call_bib IN (SELECT * FROM (SELECT `call_bib` FROM $_SESSION[weed_table] Group by `call_bib` having count(*)> 1 ) AS subquery)";
}
else {
    $whereCopyTwo = '';
}
$q = "SELECT * FROM `$table` $whereCopyTwo";
$stmt = $db->query($q);
$headers = array();
$headers_meta = array();
for ($i = 0; $i < $stmt->columnCount(); $i++) {
  $headers_meta[] = $stmt->getColumnMeta($i);
}

foreach ($headers_meta as $h) {
    array_push($headers, $h['name']);
}

$fp = fopen('php://output', 'w');
if ($fp && $stmt) {

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="'.$table.'.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    fputcsv($fp, $headers);

  while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    fputcsv($fp, array_values($row));
  }
  die;
}
?>