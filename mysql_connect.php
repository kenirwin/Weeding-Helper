<?
if (! include("config.php")) {
  print "<p class=\"warn\">Please rename the file 'config-sample.php' to 'config.php' and set the configuration variables in the file.</p>\n";
}

else { // if config.php
  $link = mysql_connect ($MYSQL_HOST, $MYSQL_LOGIN, $MYSQL_PASS);
  if (!$link) {
    die('Could not connect: ' . mysql_error());
  }
  

  mysql_query("CREATE DATABASE IF NOT EXISTS $MYSQL_DB") || print "<p class=\"warn\">Unable to create database $MYSQL_DB. Please create the database manually before proceeding.</p>";
  
    // make foo the current db
  $db_selected = mysql_select_db($MYSQL_DB, $link);
  if (!$db_selected) {
    die ('Can\'t use $MYSQL_DB : ' . mysql_error());
  }

  $sql = "\n"
    . " CREATE TABLE IF NOT EXISTS `$MYSQL_DB`.`controller` ( `filename` varchar( 255 ) NOT NULL ,\n"
    . " `file_title` varchar( 255 ) NOT NULL ,\n"
    . " `table_name` varchar( 25 ) NOT NULL ,\n"
    . " `user` varchar( 50 ) NOT NULL ,\n"
    . " `records` int( 11 ) DEFAULT NULL ,\n"
    . " `upload_date` datetime NOT NULL ,\n"
    . " `load_date` datetime DEFAULT NULL ,\n"
    . " `innreach_finished` date DEFAULT NULL ) COMMENT = 'track weeding progress';";
  (mysql_query ($sql)) || print "<p class=\"warn\">Unable to create table `$MYSQL_DB`.`controller`: <b>$sql</b></p>\n";
  

} //end else if config exists
?>