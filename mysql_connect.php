<?
if (! include("config.php")) {
  print "<p class=\"warn\">Please rename the file 'config-sample.php' to 'config.php' and set the configuration variables in the file.</p>\n";
}

$link = mysql_connect ($MYSQL_HOST, $MYSQL_LOGIN, $MYSQL_PASS);
if (!$link) {
  die('Could not connect: ' . mysql_error());
}

// make foo the current db
$db_selected = mysql_select_db($MYSQL_DB, $link);
if (!$db_selected) {
  die ('Can\'t use $MYSQL_DB : ' . mysql_error());
}
?>