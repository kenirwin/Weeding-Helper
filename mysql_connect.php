<?php 
if (! include("config.php")) {
  print "<h1>Weeding Helper Installation</h1>\n";
  print '<link rel="stylesheet" href="style.css" />';
  print "<p class=\"warn\">Please rename the file 'config-sample.php' to 'config.php' and set the configuration variables in the file.</p>\n";
  print "<p>See the <a href=\"documentation.php#installation\">installation instructions</a> for
more detail.</p>\n";
  $no_config = true;
}

else { // if config.php
    if (! isset($MYSQL_CHARSET)) { $MYSQL_CHARSET = 'utf8'; }
    $pdo_params = array(
        PDO::MYSQL_ATTR_LOCAL_INFILE => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    );
    try {
        $db = new PDO("mysql:host=$MYSQL_HOST;dbname=$MYSQL_DB;charset=$MYSQL_CHARSET", $MYSQL_LOGIN, $MYSQL_PASS, $pdo_params);
    } catch (PDOException $e) {
        die('Could not connect to database. ' . $e->getMessage());
}

  try {
      $db->query("CREATE DATABASE IF NOT EXISTS $MYSQL_DB"); 
  } catch (PDOException $e) {
      die("<p class=\"warn\">Unable to create database $MYSQL_DB. Please create the database manually before proceeding.</p>");
  }
  
  $extant_tables = array();
  $q = "SHOW TABLES"; 
  $stmt = $db->query($q); 
  while ($myrow = $stmt->fetch(PDO::FETCH_NUM)) {
    array_push ($extant_tables, $myrow[0]);
  } //while checking rows 
  

  if (! in_array("controller", $extant_tables)) {
  $sql = "\n"
    . " CREATE TABLE IF NOT EXISTS `$MYSQL_DB`.`controller` ( `filename` varchar( 255 ) NOT NULL ,\n"
    . " `file_title` varchar( 255 ) NOT NULL ,\n"
    . " `table_name` varchar( 25 ) NOT NULL ,\n"
    . " `call_type` varchar ( 5 ) ,\n"
    . " `user` varchar( 50 ) NOT NULL ,\n"
    . " `records` int( 11 ) DEFAULT NULL ,\n"
    . " `upload_date` datetime NOT NULL ,\n"
    . " `load_date` datetime DEFAULT NULL ,\n"
    . " `innreach_finished` date DEFAULT NULL ) COMMENT = 'track weeding progress';";
  ($db->query($sql)) || print "<p class=\"warn\">Unable to create table `$MYSQL_DB`.`controller`: <b>$sql</b></p>\n";
  } //end if no controller table  

  if (! in_array("table_config", $extant_tables)) {
    $sql = "\n"
      . " CREATE TABLE `$MYSQL_DB`.`table_config` ( `table_name` varchar( 25 ) NOT NULL ,\n"
      . " `action` varchar( 255 ) NOT NULL ,\n"
      . " `field` varchar( 255 ) DEFAULT NULL ,\n"
      . " `printable` char( 1 ) DEFAULT NULL )";
    ($db->query ($sql)) || print "<p class=\"warn\">Unable to create table `$MYSQL_DB`.`table_config`: <b>$sql</b></p>\n";
    
    $sql = "\n"
      . "INSERT INTO `table_config` (`table_name`, `action`, `field`, `printable`) VALUES"
     . "('default', 'disallowEdit', 'call_order', 'N'),"
     . "('default', 'disallowEdit', 'author', 'Y'),"
     . "('default', 'disallowEdit', 'title', 'Y'),"
     . "('default', 'omitField', 'publisher', 'Y'),"
     . "('default', 'disallowEdit', 'year', 'Y'),"
     . "('default', 'disallowEdit', 'lcsh', 'N'),"
     . "('default', 'disallowEdit', 'catdate', 'N'),"
     . "('default', 'omitField', 'loc', 'N'),"
     . "('default', 'disallowEdit', 'call_bib', 'Y'),"
     . "('default', 'disallowEdit', 'call_item', 'Y'),"
     . "('default', 'disallowEdit', 'volume', 'N'),"
     . "('default', 'disallowEdit', 'copy', 'N'),"
     . "('default', 'omitField', 'bcode', 'N'),"
     . "('default', 'omitField', 'mat_type', 'N'),"
     . "('default', 'omitField', 'bib_record', 'N'),"
     . "('default', 'omitField', 'item_record', 'N'),"
     . "('default', 'omitField', 'oclc', 'N'),"
     . "('default', 'disallowEdit', 'circs', 'N'),"
     . "('default', 'disallowEdit', 'renews', 'N'),"
     . "('default', 'disallowEdit', 'int_use', 'N'),"
     . "('default', 'disallowEdit', 'last_checkin', 'N'),"
     . "('default', 'disallowEdit', 'barcode', 'N'),"
     . "('default', 'omitField', 'subclass', 'N'),"
     . "('default', 'omitField', 'subj_starts', 'N'),"
     . "('default', 'disallowEdit', 'innreach_circ_copies', 'N'),"
     . "('default', 'disallowEdit', 'innreach_total_copies', 'N')";



    ($db->query ($sql)) || print "<p class=\"warn\">Unable to populate the table `$MYSQL_DB`.`table_config` with default settings</p>\n";    

  } //end if no table_config table  
} //end else if config exists
?>