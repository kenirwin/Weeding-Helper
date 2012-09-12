<html>
<head>
<title>Documentation: Weeding Helper</title>
<link rel="stylesheet" href="style.css" />
</head>
<body>
<h1>Weeding Helper: Documentation</h1>

<a name="pre-install"></a>
   <h2>Pre-Installation: System Requirements and Access Needed</h2>
   
<a name="installation"></a>
<h2>Installation</h2>
   <ol>
   <li>Unzip the file in a directory of its own. (The unzipping process creates a lot of files in the current directory, rather than a new directory with a lot of files in it.)</li>
     <li>Rename or copy the <b>config-sample.php</b> to be <b>config.php</b></li>
     <li>Edit the <b>config.php</b> file to include your local MySQL username, password, and the name you wish to use for the msyql database associated with Weeding Helper.</li>
   <li>On the Linux command-line, change the permissions on the <b>upload/</b> and <b>prepped/</b> directories to be writeable by the web server. This command will probably look something like: <br />
   <code>&gt; chgrp apache upload/</code><br />
   <code>&gt; chgrp apache prepped/</code><br />
where "apache" is the name of a permissions group that grants write-permissions to the web server.</li>
   <li>Set up the <code>cron</code> jobs that power Weeding Helper&apos;s automated functions (*****HOW*****)</li>

   <li>Navigate to the <a href="index.php">index.php</a> page; this will attempt to automatically create the database named in the config file, as well as the `controller` and `table_config` tables in the database. (You will be notified if the system is unable to create the table and/or the files. Your system administrator or another user familiar with MySQL may need to create the database and/or tables manually. (*****HOW****)</li>
   </ol>
<a name="how_to_add_circ_data"></a>
<h2>How to Upload Files</h2>