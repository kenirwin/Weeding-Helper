<link rel="stylesheet" href="style.css" />
<?php 
   if ($_SESSION[weed_table] == "" || preg_match("/controller\.php|index\.php/", $_SERVER['SCRIPT_NAME'])){
$nav = array ("controller.php" => "All Tables",
	      "upload.php" => "Upload New File",
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
	      "csv.php" => "Download CSV",
	      "documentation.php" => "Help"
	      );
   }
foreach ($nav as $link => $name) {
  if (($link == "upload.php") && ($allow_uploads !== true)) {
    $class = 'class="turned-off"';
  } 
  else { // if upload is turned on
    $class = "";
  }
  $navlinks .= "<li $class><a href=\"$link\">$name</a></li>\n";
}

print "<ul id=\"navlinks\">$navlinks</ul>\n";

if (isset($debug) && ($debug == true)) {
print "<p>SESSION: ";
print(htmlentities(print_r ($_SESSION, true)));;
print "</p>";
print "<p>REQUEST: ";
print(htmlentities(print_r ($_REQUEST, true)));;
print "</p>";
}
?>