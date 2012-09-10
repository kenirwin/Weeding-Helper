<style>
table,td,th { 
  border-collapse: collapse; border: 1px solid black;
  vertical-align: top;
 }

td:nth-child(5),td:nth-child(6) { text-align: right }
</style>
<? 
session_start();
if ($_REQUEST[table]) { $_SESSION[weed_table] = $_REQUEST[table]; }
$table = $_SESSION[weed_table];
$class = "LJ";
?>
<h2>(<?=$class;?>) Education Books - No CIRC since EZRA (1993)</h2>
<?
include('scripts.php');
include('mysql_connect.php');
include("nav.php");

$q1 = "SELECT field from `table_config` where `printable` = 'Y' and `table_name` = 'default'";
$r1 = mysql_query($q1);
$fields = array();
while ($myrow = mysql_fetch_assoc($r1)) {
  array_push ($fields, $myrow[field]);
}
$fields = "`". join ("`,`", $fields) . "`";

$q = "SELECT $fields
FROM `$table`
where 1
ORDER BY call_order";
//ORDER BY subclass,subj_starts";


print $q;
$r = mysql_query($q);
print ("<h3>".mysql_num_rows($r)." items</h3>\n");
if (function_exists("MysqlResultsTable")) {
  print(MysqlResultsTable($r));
}
else {
  print "no function";
}
?>