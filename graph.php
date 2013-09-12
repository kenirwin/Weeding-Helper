<?php 
session_start();
?>
<html>
<head>
<title>Graph - Weeding Helper</title>
<style>
.intro { margin: 2em 1em; }
img { padding-top: 1px }
thead td { font-weight: bold }
td.title { width: 300px; }
td img { height: 1em; }
table.min td { padding: 0; margin: 0; border: none; spacing: 0; }
table.min img { height: .5em; }
td { font-size: 80%; }
</style>
<?php  include("jquery.php"); ?>

<script type="text/javascript">
  $(document).ready(function() {
      $("#min-max").click(function() {
	  $("td.title").toggle();
	  $("td.circs").toggle();
	  $("thead").toggle();
	  $("table").toggleClass("min");
	}); //end min-max click function
    }); //end jquery load script
</script>


</head>

<body>
<?php 
include ("mysql_connect.php");
if ($_REQUEST[table]) { $_SESSION[weed_table] = $_REQUEST[table]; }
$table = $_SESSION[weed_table];
$imgsrc = "images/greenblock.gif";
$redimg = "images/redblock.gif";
$q = "SELECT file_title from `controller` where table_name = '$table'";
$r = mysql_query($q);
$myrow = mysql_fetch_row($r);
$title = $myrow[0];

include("nav.php");

$q= "select * from `$table`";
//print $q;
$r= mysql_query($q);
$last_class = "";

?>
<h1>Graph: <?php =$title;?></h1>

<div class="intro">
<p>This graphs the shelflist: arranged in call number order, the titles in this table are shown with the number of times circulated. Zero circs shown in red.</p>
<p><input type="checkbox" id="min-max" />Check this box to minimize the graph (hide all but graph; mouseover graph for title info)</p>
</div>
<?php 

$thead = "<thead><tr><td>Title</td><td>Circs</td><td>Graph</td></tr></thead>\n";

while ($myrow = mysql_fetch_assoc($r)) {
  extract ($myrow);
  if (preg_match("/^([a-zA-Z]+)/",$call,$m)) { 
    $class = $m[1]; 
    if ($class != $last_class) {
      //      print "<h2>$class</h2>\n";
      $lines .= "</table><h2>$class</h2><table>$thead\n";
    }
    $last_class = $class;
  }
  
  if ($circs == 0) { $size = 1; }
  else { $size = $circs *10; }
  if ($circs == 0) {
    $lines .= "<tr><td class=\"title\">$title</td><td class=\"circs\">$circs</td><td><img src=\"$redimg\" width=5 title=\"$title ($call): $circs\"></td></tr>\n";
//    print "<br><img src=\"$redimg\" height=4 width=5 title=\"$title ($call): $circs\">\n";
  }
  else {
    $lines .= "<tr><td class=\"title\">$title</td><td class=\"circs\">$circs</td><td><img src=\"$imgsrc\" width=\"$size\" title=\"$title ($call): $circs\"></td></tr>\n";
    //    print "<br><img src=\"$imgsrc\" width=\"$size\" height=4 title=\"$title ($call): $circs\">\n";
  }
} //end while
print "<table>$lines</table>\n";
?>
  <?php  include ("license.php"); ?>
</body>
</html>