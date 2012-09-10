<link rel="stylesheet" href="style.css" />
<?
   if ($_SESSION[weed_table] == ""){
$nav = array ("controller.php" => "All Tables",
	      "upload.php" => "Upload New File",
	      "settings.php" => "View/Edit Settings"
	      );
   }
   
   else {
$nav = array ("controller.php" => "All Tables",
	      "upload.php" => "Upload New File",
	      "view.php" => "View/Edit",
	      "cloud.php" => "Keyword Cloud",
	      "graph.php" => "Graph",
	      "settings.php" => "View/Edit Settings"
	      );
   }
foreach ($nav as $link => $name) {
  $navlinks .= "<li><a href=\"$link\">$name</a></li>\n";
}

print "<ul id=\"navlinks\">$navlinks</ul>\n";
?>