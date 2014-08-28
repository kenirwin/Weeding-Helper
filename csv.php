<?php 
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
  header('Content-Disposition: attachment; filename="export.csv"');
  header('Pragma: no-cache');
  header('Expires: 0');
  fputcsv($fp, $headers);
  while ($row = mysql_fetch_array($r)) {
    fputcsv($fp, array_values($row));
  }
  die;
}


/*
$columns_total = mysql_num_fields($q);

// Get The Field Name

for ($i = 0; $i < $columns_total; $i++) {
  $heading = mysql_field_name($sql, $i);

  $output .= '"'.$heading.'",';
}
$output .="\n";

// Get Records from the table

while ($row = mysql_fetch_array($sql)) {
  for ($i = 0; $i < $columns_total; $i++) {
    $output .='"'.$row["$i"].'",';
    
  }
  $output .="\n";
}

// Download the file

$filename = "$table.csv";
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);

echo $output;
exit;

*/


?>