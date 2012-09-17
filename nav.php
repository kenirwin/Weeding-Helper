<link rel="stylesheet" href="style.css" />
<?
   if ($_SESSION[weed_table] == ""){
$nav = array ("controller.php" => "All Tables",
	      "upload.php" => "Upload New File",
	      "settings.php" => "Display Settings",
	      "documentation.php" => "Help"
	      );
   }
   
   else {
$nav = array ("controller.php" => "All Tables",
	      "upload.php" => "Upload New File",
	      "view.php" => "View/Edit",
	      "print.php" => "Print View",
	      "cloud.php" => "Keyword Cloud",
	      "graph.php" => "Graph",
	      "settings.php" => "Display Settings",
	      "documentation.php" => "Help"
	      );
   }
foreach ($nav as $link => $name) {
  $navlinks .= "<li><a href=\"$link\">$name</a></li>\n";
}

print "<ul id=\"navlinks\">$navlinks</ul>\n";
?>