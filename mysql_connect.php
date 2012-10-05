<?
if (! include("config.php")) {
  print "<h1>Weeding Helper Installation</h1>\n";
  print '<link rel="stylesheet" href="style.css" />';
  print "<p class=\"warn\">Please rename the file 'config-sample.php' to 'config.php' and set the configuration variables in the file.</p>\n";
  print "<p>See the <a href=\"documentation.php#installation\">installation instructions</a> for
more detail.</p>\n";
  $no_config = true;
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

  $extant_tables = array();
  $q = "SHOW TABLES"; 
  $r = mysql_query($q); 
  while ($myrow = mysql_fetch_row($r)) {
    array_push ($extant_tables, $myrow[0]);
  } //while checking rows 
  

  if (! in_array("controller", $extant_tables)) {
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
  } //end if no controller table  

  if (! in_array("table_config", $extant_tables)) {
    $sql = "\n"
      . " CREATE TABLE `$MYSQL_DB`.`table_config` ( `table_name` varchar( 25 ) NOT NULL ,\n"
      . " `action` varchar( 255 ) NOT NULL ,\n"
      . " `field` varchar( 255 ) DEFAULT NULL ,\n"
      . " `printable` char( 1 ) DEFAULT NULL )";
    (mysql_query ($sql)) || print "<p class=\"warn\">Unable to create table `$MYSQL_DB`.`table_config`: <b>$sql</b></p>\n";
    
    $sql = "\n"
      . "INSERT INTO `table_config` (`table_name`, `action`, `field`, `printable`) VALUES"
      . "('default', 'omitField', 'publisher', 'Y'),"
      . "('default', 'omitField', 'loc', 'N'),"
      . "('default', 'omitField', 'bcode', 'N'),"
      . "('default', 'omitField', 'mat_type', 'N'),"
      . "('default', 'omitField', 'bib_record', 'N'),"
      . "('default', 'disallowEdit', 'subclass', 'N'),"
      . "('default', 'disallowEdit', 'subj_starts', 'N'),"
      . "('default', 'omitField', 'classic', 'N'),"
      . "('default', 'omitField', 'condition', 'N'),"
      . "('default', 'disallowEdit', 'author', 'Y'),"
      . "('default', 'disallowEdit', 'title', 'Y'),"
      . "('default', 'disallowEdit', 'year', 'Y'),"
      . "('default', 'disallowEdit', 'lcsh', 'N'),"
      . "('default', 'disallowEdit', 'catdate', 'N'),"
      . "('default', 'disallowEdit', 'call', 'Y'),"
      . "('default', 'disallowEdit', 'circs', 'N'),"
      . "('default', 'disallowEdit', 'renews', 'N'),"
      . "('default', 'disallowEdit', 'int_use', 'N'),"
      . "('default', 'disallowEdit', 'last_checkin', 'N'),"
      . "('default', 'disallowEdit', 'items', 'N'),"
      . "('default', 'disallowEdit', 'circ_items', 'N'),"
      . "('default', 'disallowEdit', 'subclass', 'N'),"
      . "('default', 'disallowEdit', 'subj_starts', 'N')";
    (mysql_query ($sql)) || print "<p class=\"warn\">Unable to populate the table `$MYSQL_DB`.`table_config` with default settings</p>\n";    

  } //end if no table_config table  
} //end else if config exists
?>