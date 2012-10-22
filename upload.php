<html>
<head><title>Upload New Review File - Weeding Helper</title>
<style>
label {
  font-weight: bold;
 display: block;
 width: 150px;
   float: left;
 }
</style>
</head>



<body>
<h1>Upload New Review File</h1>

<?
include("nav.php");
ERROR_REPORTING(0);
include ("config.php");
include ("mysql_connect.php");
//include ("scripts.php");


if ($allow_uploads == true) { 
  if ($_REQUEST[upload_button]) { 
    HandleUpload();
  }
  
  if (fopen("upload/temp","x")) { // try to make a file to test write permissions
    fclose ("upload/temp");
    unlink ("upload/temp");
    ShowUploadForm();
  } // end if web server has write permissions

  else { // if web server doesn't have write permissions
    print "<div class=warning><h3>This Function is Unavailable</h3><p>The web server does not have write permissions to the upload/ directory, so this function is unavailable. To allow direct file uploads, talk with your system administrator about granting write permission to the upload/ directory. (<a href=\"documentation.php#installation\">See Installation documentation, step 4</a>).</p>";
 
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
<form action="<?=$PHP_SELF;?>" method="POST" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="2000000">

<p>
<label for="userfile">File to upload</label>
<input name="userfile" type="file" id="userfile" />
</p>

<p>
<label for="file_title">File title</label>
<input name="file_title" type="text" />
<span class="fieldnote">This is a nice display name, e.g. "Main Stacks History Books"</span>

</p>

<p>
<label for="table_name">Table name</label>
<input name="table_name" type="text" />
<span class="fieldnote">Something short without funny characters, for the MySQL table name, like "history"</span>
</p>

<p>
<label for="user">User</label>
<input name="user" type="text" />
<span class="fieldnote">Who are you?</span>
</p>

<p>
<input name="upload_button" type="submit" class="box" value=" Upload ">
</p>
</form>
<?
} //end function ShowUploadForm



function HandleUpload () {
  if(isset($_POST['upload_button']) && $_FILES['userfile']['size'] >  0)
    {
      $fileName = $_FILES['userfile']['name'];
      $tmpName  = $_FILES['userfile']['tmp_name'];
      $fileSize = $_FILES['userfile']['size'];
      $fileType = $_FILES['userfile']['type'];


      if (move_uploaded_file($tmpName, "upload/".$fileName)) 
	{
	  include ("mysql_connect.php");
	  foreach ($_REQUEST as $k=>$v) { 
	    $_REQUEST[$k] = mysql_real_escape_string($v);
	  }
	  print "<li class=\"success\">SUCCESS: file uploaded</li>\n";
	  $q = "INSERT INTO `controller` (`filename`,`file_title`,`table_name`,`user`,`upload_date`) VALUES ('$fileName', '$_REQUEST[file_title]', '$_REQUEST[table_name]', '$_REQUEST[user]', now())";
	  $r = mysql_query($q);
	  if (mysql_errno() == 0) {
	    print "<li class=\"success\">SUCCESS: added file to master database</li>\n";
	  }
	  else { 
	    print "<li class=\"error\">$q -- ERROR: could not add file to master database:". mysql_errno() . mysql_error() ."</li>\n";
	  } //end else
	}
      else
        print "<p class=warning>failed to upload file: check to be sure web server has write permissions to the upload/ directory</p>";


      $path = preg_replace ("/[^\/]+$/", "", $_SERVER[SCRIPT_FILENAME]);
      
      
    } //end if isset
  else { 
    if ($_FILES['userfile']['size'] ==  0) { 
      print "<p class=warning>failed to upload file: file appears to be empty. please be sure that you have saved the file and that it has a size greater than 0 KB.</p>\n";
    } //end if size == 0
  } //end else if couldn't start upload
} //end HandleUpload


?>
<p><a href="controller.php">Go to Controller page</a></p>
  <? include ("license.php"); ?>
</body>
</html>
