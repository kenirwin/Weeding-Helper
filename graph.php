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
      $("#tabs").tabs();
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
?>
<h1>Graph: <?php print($title);?></h1>

<div id="tabs">
<ul>
  <li><a href="#tabs-age">Age of Collection</a></li>
  <li><a href="#tabs-title-usage">Usage by Title</a></li>
  <li><a href="#tabs-recency-of-circ">Recency of Circulation</a></li>
</ul>

<div id="tabs-age">
  <?php
$q = "SELECT `year`,count(*) as `items` FROM `$table` GROUP BY `year` ORDER BY `year` DESC";
$r = mysql_query($q);
$rows = "";
while ($myrow = mysql_fetch_assoc($r)) {
  extract($myrow);
  $size = $items * 1;
  $rows .= '<tr><td>'.$year.'</td> <td>'.$items.'</td><td><img src="'.$imgsrc.'" width="'.$size.'" height="1em" /></td>'.PHP_EOL;
}
print '<table>'.PHP_EOL;
print $rows;
print '</table>'.PHP_EOL;
?>
</div><!--tabs-age-->


<div id="tabs-title-usage">
<?php
$q= "select * from `$table`";
//print $q;
$r= mysql_query($q);
$last_class = "";
?>

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
</div><!--title-usage-->

<div id="tabs-recency-of-circ">
  <p>Year in which items last circulated, by number of items per year</p>
  <?php
  $q= "select count(*) as YearCount, year(`last_checkin`) as `LastCirc` from `$table` group by `LastCirc` order by `LastCirc` DESC";
$r = mysql_query($q);
while ($myrow = mysql_fetch_assoc($r)) {
  extract($myrow);
$circ_rows .= "<tr><td>$LastCirc</td> <td>$YearCount</td> <td><img src=\"$imgsrc\" width=\"$YearCount\" title=\"Last Circ in $LastCirc: $YearCount\"></td></tr>\n";
}
print '<table id="recency-of-circ">'.PHP_EOL;
print $circ_rows;
print '</table>'.PHP_EOL;
?>
</div><!--recency-of-circ-->
</div><!--tabs-->

  <?php  include ("license.php"); ?>
</body>
</html>