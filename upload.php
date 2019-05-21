<html>
<head><title>Upload New Review File - Weeding Helper</title>
</head>

<body>
<h1>Upload New Review File</h1>

<?php 
$debug = true;
if ($debug){ 
    error_reporting(E_ALL & ~E_NOTICE);
    ini_set('display_errors', 1);
}

include ("config.php");
include ("nav.php");
include ("mysql_connect.php");

if ($allow_uploads == true) { 
  if ($_REQUEST['upload_button']) { 
    $q = "SELECT * FROM `controller` WHERE table_name = ?";
    $params = array($_REQUEST['table_name']);
    $stmt = $db->prepare($q);
    $stmt->execute($params);
    
    if ($stmt->rowCount() > 0) {
      print '<p class="warning">There is already a database table named <strong>$_REQUEST[table_name]</strong>. Please choose a different table name for this file.</p>';
    }
    else {
      HandleUpload();
    } //end else if tablename is ok
  }
  
  if (! isset($secure_outside_path)) {
      print '<div class="warning"><h3>Upload Directory not Defined</h3><p>You must define the $secure_outside_path variable in the config file. For security, this file should be outside the webserver directory (often <i>/var/www/html</i> or <i>public_html/</i>) but should be writeable by the webserver process.</p></div>'.PHP_EOL;
  }

  elseif ($link = fopen("$secure_outside_path/temp","x")) { // try to make a file to test write permissions
    fclose ($link);
    unlink ("$secure_outside_path/temp");
    ShowUploadForm();
  } // end if web server has write permissions

  else { // if web server doesn't have write permissions
    print "<div class=warning><h3>This Function is Unavailable</h3><p>The web server does not have write permissions to the upload directory, so this function is unavailable. To allow direct file uploads, talk with your system administrator about granting write permission to the upload directory. (<a href=\"documentation.php#installation\">See Installation documentation, step 4</a>).</p>";
 
    /*
      Don't know the username for your webserver's process? 
      You can un-comment the next line and it will display in the error msg.
      This is left commented-out because it may be regarded as sensitive info.
      You may wish to re-comment-out the line once you get the answer. 
    */
    // print "Web server username and group info: " . exec("id") ."</p>\n";

    print "</div>\n";
  } //end else if not able to write to tmp directory
} //end if the allow-uploads setting is allowed in config.php

else { 
  print "<h3>This function is not enabled.</h3>";
  print "<p>To enable this function, change the \$allow_uploads setting to TRUE in config.php</p>"; 
}

function ShowUploadForm() {
  ?>
<form action="<?php print($PHP_SELF);?>" method="POST" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="2000000">

<p>
    <label for="userfile">File to upload</label>
			    <input name="userfile" type="file" id="userfile" /> <span class="fieldnote"><a href="documentation.php#how_to_add_circ_data" target="_new">See documentation for correct file format</a></span>
</p>

<p>
<label for="file_title">File title</label>
<input name="file_title" type="text" />
<span class="fieldnote">This is a nice display name, e.g. "Main Stacks History Books"</span>

</p>

<p>
<label for="table_name">Table name</label>
<input name="table_name" type="text" />
<span class="fieldnote">Something short with NO SPACES or funny characters, for the MySQL table name, like "history"</span>
</p>

<p>
<label for="user">User</label>
<input name="user" type="text" />
<span class="fieldnote">Who are you?</span>
</p>

<p>
<label for="call_type">Call # Type</label>
<select name="call_type">
<?php print(CallTypePulldown());?>
</select>
</p>

<p>
<input name="upload_button" type="submit" class="box" value=" Upload ">
</p>
</form>
<?php 
} //end function ShowUploadForm

function CallTypePulldown() {
  global $default_call_type;
  $opts = '';
  $call_types = array ('LC' => 'Library of Congress',
		       'DDC' => 'Dewey Decimal');
  foreach ($call_types as $type => $name) {
    if ($default_call_type == $type) {
      $selected = ' selected';
    }
    else { $selected = ''; }
    $opts .= "<option value=\"$type\"$selected>$name</option>\n";
  }
  return $opts;
}

function HandleUpload () {
    global $secure_outside_path;
    if(isset($_POST['upload_button']) && $_FILES['userfile']['size'] >  0)
    {
      $fileName = $_FILES['userfile']['name'];
      $tmpName  = $_FILES['userfile']['tmp_name'];
      $fileSize = $_FILES['userfile']['size'];
      $fileType = $_FILES['userfile']['type'];
      
      if (move_uploaded_file($tmpName, "$secure_outside_path/upload/". $fileName)) 
	{
	  include ("mysql_connect.php");
	  foreach ($_REQUEST as $k=>$v) { 
	    $_REQUEST[$k] = $v;
	  }
	  print "<li class=\"success\">SUCCESS: file uploaded</li>\n";
	  if (! $_REQUEST['table_name']) {
	    $_REQUEST['table_name'] = $fileName;
	  }
	  $_REQUEST['table_name'] = preg_replace("/[^a-zA-Z0-9]+/","_",$_REQUEST['table_name']);
      try { 
    $q = "INSERT INTO `controller` (`filename`,`file_title`,`table_name`,`call_type`,`user`,`upload_date`) VALUES (?,?,?,?,?,now())";
    $params = array($fileName, $_REQUEST['file_title'], $_REQUEST['table_name'], $_REQUEST['call_type'], $_REQUEST['user']);
    $stmt = $db->prepare($q);
    $stmt->execute($params);
    print "<li class=\"success\">SUCCESS: added file to master database</li>\n";
} catch (PDOException $e) {
    print "<li class=\"error\">$q -- ERROR: could not add file to master database:". $e->getMessage()."</li>\n";
}
}
      else {
        print "<p class=warning>failed to upload file: check to be sure web server has write permissions to the upload directory. $errno</p>";
      }

      $path = preg_replace ("/[^\/]+$/", "", $_SERVER['SCRIPT_FILENAME']);
      
      
    } //end if upload file isset and is larger than 0
  else { //if couldn't start upload
    if ($_FILES['userfile']['size'] ==  0) { 
      print "<p class=warning>failed to upload file: file appears to be empty. please be sure that you have saved the file and that it has a size greater than 0 KB.</p>\n";
    } //end if size == 0
  } //end else if couldn't start upload
} //end HandleUpload


?>
<p><a href="controller.php">Go to Controller page</a></p>
  <?php  include ("license.php"); ?>
</body>
</html>
