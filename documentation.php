<html>
<head>
<title>Help/Documentation: Weeding Helper</title>
<link rel="stylesheet" href="style.css" />
<style>
   body { margin: 0 10% 0 5% }
   ol { list-style: decimal; }
ol.sublist { list-style: lower-latin; } 
div.example { 
  padding: .25em 1em;
  background-color: #CFDBF3; 
  border-radius: 5px;
} 

</style>
</head>
<body>
<h1>Weeding Helper: Help/Documentation</h1>
<? include("nav.php"); ?>

<a name="pre-install"></a>
   <h2>Pre-Installation: System Requirements and Access Needed</h2>
    <p>Weeding Helper is written for use with Innovative Interfaces (III)&apos;s Millennium ILS. It runs on Linux, using PHP and MySQL. Installing the software should be done by someone familiar with Linux, file permissions and cron. Once it is installed, no serious techinical skills are required for its use.</p>

<a name="installation"></a>
<h2>Installation</h2>
   <ol>
   <li>Unzip the file in a directory of its own. (The unzipping process creates a lot of files in the current directory, rather than a new directory with a lot of files in it.)</li>
     <li>Rename or copy the <b>config-sample.php</b> to be <b>config.php</b></li>
     <li>Edit the <b>config.php</b> file to include your local MySQL username, password, and the name you wish to use for the msyql database associated with Weeding Helper.</li>
					   <li>On the Linux command-line, change the permissions on the <b>upload/</b> and <b>prepped/</b> directories to be writeable by the web server AND by the user who under whose name the <b>cron</b> processes will run (see step 6). In our library, this has been achieved by making a permission group called &quot;apache&quot;, containing both the web-process-user and the chief implementor&#39;s own username; This command will probably look something like: <br />
<div class="example">
   <code>&gt; chgrp apache upload/</code><br />
   <code>&gt; chgrp apache prepped/</code><br />
</div>
where "apache" is the name of a permissions group that grants write-permissions to the web server and includes the user who sets up the cron job in the next step.</li>
									 <li>Make sure the two files <b>prep_file.php</b> and <b>innreach_check.php</b> begin with valid pointers to where PHP is running on your server. By default, they will start with this declaration: <b>#!/usr/bin/php</b> -- that will work on many servers, but not all. To find a valid php path, type on the command line <b>which php</b></li>

   <li>Set up the <code>cron</code> jobs that power Weeding Helper&apos;s automated functions. Cron is a unix utility that can run scripts on a regular basis; if you hare unfamiliar with cron, you will want to get help from a system administrator or similar. The two files that will need to be activated by cron jobs are <b>prep_file.php</b> and <b>innreach_check.php</b>. Running the cronjobs once per minute is an effective approach.
   <ol class="sublist">
   <li><b>prep_file.php</b> takes an already-uploaded file loads it into the database. That only needs to happen once per file. If that cron job runs once per minute, it will usually not need to do anything; but when a file is uploaded, it will reliably be added to the database very quickly.</li>
																										  <li><b>innreach_check.php</b> needs to run frequently to be effective. Whenever a file is loaded into the database, this script will run against the database table and find InnReach holdings for the items listed in the table. The script is set up to run several times per minute, with a pause between each hit. Running the cron job every minute is a convenient way to have this script do its work in a timely fashion.</li>
</ol>

																																																																											<p>Example of a crontab file; your path may vary:
<div class="example">	      
<pre><code>
#|      |       |       |       |       commands
#|      |       |       |       day of the week (0-6 with 0=Sunday).
#|      |       |       month of the year (1-12),
#|      |       day of the month (1-31),
#|      hour (0-23),
#minute (0-59)

*       *       *       *       *       /docs/weed/prep_file.php
*       *       *       *       *       /docs/weed/innreach_check.php

</code></pre>
</div>
</li>

   <li>Navigate to the <a href="index.php">index.php</a> page; this will attempt to automatically create the database named in the config file, as well as the `controller` and `table_config` tables in the database. (You will be notified if the system is unable to create the table and/or the files. Your system administrator or another user familiar with MySQL may need to create the database and/or tables manually. (*****HOW****)</li>
   </ol>
<a name="how_to_add_circ_data"></a>
<h2>How to Upload Files</h2>
																											<p>To upload new data from a III Review File, export the data in this format (order matters!):</p>
<table>
<thead>
 <tr><th>Field Type</th> <th>Field</th></tr>
</thead>
<tbody>
<tr><td>Bibliographic</td><td>Author</td></tr>
<tr><td>Bibliographic</td><td>Title</td></tr>
<tr><td>Bibliographic</td><td>Imprint</td></tr>
<tr><td>Bibliographic</td><td>Subject</td></tr>
<tr><td>Bibliographic</td><td>Cat Date</td></tr>
<tr><td>Bibliographic</td><td>Location</td></tr>
<tr><td>Bibliographic</td><td>Call #</td></tr>
<tr><td>Bibliographic</td><td>BCODE3</td></tr>
<tr><td>Bibliographic</td><td>MAT TYPE</td></tr>
<tr><td>Bibliographic</td><td>RECORD #</td></tr>
<tr><td>Item</td><td>Tot Chkout</td></tr>
<tr><td>Item</td><td># Renewals</td></tr>
<tr><td>Item</td><td>Intl Use</td></tr>
<tr><td>Item</td><td>Lchkin</td></tr>
</tbody>
</table>

<p>Field Delimiter: tab (aka: Control Character 9 in Millennium)<br>
Text Qualifier: none<br>
Repeated Field Delimiter: ; (semi-colon)<br>
Max field length: none</p>

