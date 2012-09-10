<html>
<head>
<title>Graph - Weeding Helper</title>
<style>
img { padding-top: 1px }
</style>
</head>

<body>
<h1>Graph</h1>
<?
session_start();
include ("mysql_connect.php");
if ($_REQUEST[table]) { $_SESSION[weed_table] = $_REQUEST[table]; }
$table = $_SESSION[weed_table];
$imgsrc = "images/greenblock.gif";
$redimg = "images/redblock.gif";

include("nav.php");

$q= "select * from `$table`";
//print $q;
$r= mysql_query($q);
$last_class = "";

while ($myrow = mysql_fetch_assoc($r)) {
  extract ($myrow);
  if (preg_match("/^([a-zA-Z]+)/",$call,$m)) { 
    $class = $m[1]; 
    if ($class != $last_class) {
      print "<h2>$class</h2>\n";
    }
    $last_class = $class;
  }
  
  if ($circs == 0) { $size = 1; }
  else { $size = $circs *10; }
  if ($circs == 0) {
    print "<br><img src=\"$redimg\" height=4 width=5 title=\"$title ($call): $circs\">\n";
  }
  else {
    print "<br><img src=\"$imgsrc\" width=\"$size\" height=4 title=\"$title ($call): $circs\">\n";
  }
}
?>
</body>
</html>