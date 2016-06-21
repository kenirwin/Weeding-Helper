<html>
<head>
<title>Help/Documentation: Weeding Helper</title>
<!-- docuentation for item-based weeding helper -->
<link rel="stylesheet" href="style.css" />
<style>
   #main { margin: 0 10% 0 5% }
   ol { list-style: decimal; }
   ol.sublist { list-style: lower-latin; }
table td {padding-right: 2em;  }
</style>
</head>
<body>
<h1>Help/Documentation</h1>
<?php  
include("config.php");
include("nav.php"); 
?>

<div id="main">
<dl id="toc">
   <dt>Getting Started</dt>
    <dd><a href="#pre-install">Pre-Installation: System Requirements and Access Needed</a></dd>
    <dd><a href="#installation">Installation</a></dd>
   <dt>Using Weeding Helper</dt>
    <dd><a href="#how_to_add_circ_data">How to Upload Files</a></dd>
    <dd><a href="#innreach">Check holding against an InnReach catalog</a></dd>
    <dd><a href="#view_edit">View/Edit</a></dd>
    <dd><a href="#display_settings">Display Settings</a></dd>
   <dt>Credits</dt>
    <dd><a href="#main_credits">Main Credits</a></dd>
    <dd><a href="#open_source">Open-Source Projects Used Here</dd></a>
</dl>

<h2>Getting Started<h2>
<a name="pre-install"></a>
   <h3>Pre-Installation: System Requirements and Access Needed</h3>
    <p>Weeding Helper is written for use with Innovative Interfaces (III)&apos;s Millennium ILS. It runs on Linux, using PHP and MySQL. Installing the software should be done by someone familiar with Linux, file permissions and cron. Once it is installed, no serious techinical skills are required for its use.</p>

<a name="installation"></a>
<h3>Installation</h3>
   <ol>
   <li>Unzip the file in a directory of its own. (The unzipping process creates a lot of files in the current directory, rather than a new directory with a lot of files in it.)</li>
     <li>Rename or copy the <b>config-sample.php</b> to be <b>config.php</b></li>
					   <li>Edit the <b>config.php</b> file to include your local MySQL username, password, and the name you wish to use for the msyql database associated with Weeding Helper. If you plan to use the <b>innreach_check.php</b> functions to look up how many copies of your items are available in an InnReach catalog, you will also need to speficy several variables in the $innreach array in <b>config.php</b>. <b>Note:</b> There are three config settings that are set to <b>false</b> by default($allow_uploads, $allow_manage, $allow_delete); in most cases, they should be set to <b>true</b> once server security has been established; see "Security" below.</li>
                                                                         <li>Create a secure upload folder outside the webroot. 
<ol class="sublist">
<li>On the Linux command-line, create a folder OUTSIDE the webroot (on many systems, the webroot may be the <b>public_html</b> or <b>/var/www/html</b> folder). You may wish to create the folder in someplace like <b>/var/www/app/weeding</b>.</li>
<li>Once you have created that folder, create two subfoldersin in it: <b>upload</b> and <b>prepped</b>.</li>
<li>Change the permissions on the <b>upload/</b> and <b>prepped/</b> directories to be writeable by the web server AND by the user who under whose name the <b>cron</b> processes will run (see step 6). In our library, this has been achieved by making a permission group called &quot;apache&quot;, containing both the web-process-user and the chief implementor&#39;s own username; This command will probably look something like: <br />
<div class="example">
   <code>&gt; chgrp apache upload/</code><br />
   <code>&gt; chgrp apache prepped/</code><br />
</div>
where "apache" is the name of a permissions group that grants write-permissions to the web server and includes the user who sets up the cron job in the next step.</li>
                                                                         <li>In the config.php file, set <b>$secure_upload_path</b> to point the path that contains the <b>upload</b> and <b>prepped</b> folders (e.g. /var/www/app/weeding).</li>
</ol>
</li>
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

					   <li>Navigate to the <a href="index.php">index.php</a> page; this will attempt to automatically create the database named in the config file, as well as the `controller` and `table_config` tables in the database. (You will be notified if the system is unable to create the table and/or the files. Your system administrator or another user familiar with MySQL may need to create the database and/or tables manually. To do this, create the MySQL database using the method approved by your sysadmin (unless it was successfully created automatically) and then use the commands in <b>manual_table_creation.sql</b> to create the tables and their contents.</li>
   </ol>

<a name="security"></a>
<h3>Security</h3>

<p>This set of programs takes data uploaded by a user and inserts it into a database. It is HIGHLY recommended that you secure this set of processes in a password-protected directory. Some effort has been made to reduce the possibility of SQL-injections, but there are likely still vulnerabilities. Use sensibly!</p>

																											<p>If you do not currently need the upload functionality turned on, you may turn it off in the <b>config.php</b> file by setting:
																											<div class="example"><code>$allow_uploads = false;</code></div>

<hr />

<h2>Using Weeding Helper</h2>
<a name="how_to_add_circ_data"></a>
<h3>How to Upload Files</h3>
			
<p>Create a list of ITEM records in III Millennium or Sierra.</p>																								<p>To upload new data from a III Review File, export the data in this format (order matters!):</p>
<div class="example">
<table>
<thead>
 <tr><th>Field Type</th> <th>Field</th></tr>
</thead>
<tbody>
<tr><td>Bibliographic</td> <td>AUTHOR</td></tr>
<tr><td>Bibliographic</td> <td>TITLE</td></tr>
<tr><td>Bibliographic</td> <td>MARC FIELD 260|a</td></tr>
<tr><td>Bibliographic</td> <td>MARC FIELD 260|b</td></tr>
<tr><td>Bibliographic</td> <td>MARC FIELD 260|c</td></tr>
<tr><td>Bibliographic</td> <td>SUBJECT</td></tr>
<tr><td>Bibliographic</td> <td>CAT DATE</td></tr>
<tr><td>Bibliographic</td> <td>LOCATION</td></tr>
<tr><td>Bibliographic</td> <td>CALL #(BIBLIO)</td></tr>
<tr><td>Item</td> <td>CALL #(ITEM)</td></tr>
<tr><td>Item</td> <td>VOLUME</td></tr>
<tr><td>Item</td> <td>COPY #</td></tr>
<tr><td>Bibliographic</td> <td>BCODE3</td></tr>
<tr><td>Bibliographic</td> <td>MAT TYPE</td></tr>
<tr><td>Bibliographic</td> <td>RECORD #(BIBLIO)</td></tr>
<tr><td>Item</td> <td>RECORD #(ITEM)</td></tr>
<tr><td>Bibliographic</td> <td>OCLC #</td></tr>
<tr><td>Item</td> <td>TOT CHKOUT</td></tr>
<tr><td>Item</td> <td>TOT RENEW</td></tr>
<tr><td>Item</td> <td>INTL USE</td></tr>
<tr><td>Item</td> <td>LCHKIN</td></tr>
<tr><td>Item</td> <td>BARCODE</td></tr>
</tbody>
</table>

<p>Field Delimiter: tab (aka: Control Character 9 in Millennium)<br>
Text Qualifier: none<br>
Repeated Field Delimiter: ; (semi-colon)<br>
Max field length: none</p>
</div>

																											<p>Once the review file is created and exported in the above format, it may be uploaded into Weeding Helper. Click the <b>Upload File</b> button, and fill out the form fields associated with the file. This will upload the file into the upload/ directory. (If the upload fails, it may be because the file is too large (many servers have a 2MB max upload file; see your system administrator about changing this limitation). Upload problems will also occur if your web server does not have write permissions for the upload directory (see <a href="#installation">Installation, step 4</a>).</p>

<h4>Importing data to the database</h4>
<p>Once the file is uploaded, the <b>prep_file.php</b> cron job will read the file contents and import them into a database table. (You will assign the table name at the time of upload.) This database import is <em>not</em> instantaneous upon upload. It should happen the next time the cron-job processes. If you choose not to run the cron jobs automatically, a user with shell access to your webserver may run the <b>prep_file.php</b> script manually.</p>

<a name="innreach"></a>
<h3>Check holding against an InnReach catalog</h3>
<p>If your library is part of a consortium that uses the InnReach functions of III&#39;s system to borrow materials from other libraries, you may wish to take your consortial holdings into account when making weeding decisions. (e.g., you may not wish to weed the last copy of a material held in your collection, or you may feel more inclined to weed an underused item if you know that there are many copies available in your consortium.)</p>

<p>Checking InnReach holdings is a laborious process by hand; with the automated features of Weeding Helper, it may still take a few hours or days (depending on the number of items in a file) but it is much less work! If you have set up the <b>innreach_check.php</b> cron job (See <a href="#installation">Installation, step 5</a>), and have configured the $innreach settings in the <b>config.php</b> file, the cron job will check the holdings of several items each time the cron job fires. By default, the script runs 9 times per cron instance between midnight and 7am, and 6 times per instance at other times. (This is designed to be respectful of the InnReach server.) You can change these settings in the $innreach array in <b>config.php</b></p>

<p>I really need to say more about how to set up the $innreach settings in <b>config.php</b>. Please contact me if you need help with that. (kirwin@wittenberg.edu)</p>

<p>If an InnReach is too large to be parsed by the software, the process of checking the catalog will get STUCK. Left to its own devices, it will try over and over again, and fail every time. If you notice this happening (if the number of records checked in InnReach stays the same for several minutes (if the cronjob is running every minute), then you may need to click the "Unstick" button to fix it. This will enter InnReach holdings values of -1 for the stuck record and then proceed to check other files. Restarting may take another minute or so, so please be patient.</p>


<a name="view_edit"></a>
<h3>View/Edit</h3>

																											The View/Edit function is the meat of Weeding Helper. It uses the AjaxCRUD interface to display selected fields from your data table in an easy-to-read format. With it, you make changes to certain fields -- by default, you may make changes to the "best book", "notes", and "fate" fields.
<li>"Best book" is intended to indicate whether or not a book is listed in whatever sources your library esteems (e.g. <cite>Books for College Libraries</cite>, subject bibliographies, etc.). It is a Yes/No field.</li>
																											<li>"Notes" is a free-text field. I use this field most often for notes like "not on shelf", "keep per kri", "last copy", etc. Use it for whatever you like.</li>
																											<li>"Fate" offers several options you can use to indicate what should happen to a book: weed, de-dup, replace, update, keep.</li>

																											<p>You can change which fields show up in this view and which fields are editable using the View/Edit Settings functions (see below).

<a name="display_settings"></a>
<h3>Display Settings</h3>

<p>I'm going to admit up-front that this functionality is not fully developed, and I wish it were a bit clearer.</p>

<p>Weeding Helper ships with a set of default settings for what is viewable and editable in the <b>Display Settings</b> function. You can alter the default settings and/or clone the current defaults to apply specific settings to an individual table.</p>

<p>There are two sets of settings: View/Edit Settings and Print Settings. The View/Edit settings specify which fields are <b>visible</b> and/or <b>editable</b> in the View/Edit function. The three settings available for each field are:
<li>Display / Editable</li>
<li>Display / Read-only</li>
<li>Hide</li>
</p>

<p>The Print View settings are binary: display on or off.</p>

<hr />
<h2>Credits</h2>
<a name="main_credits"></a>
<h3>Main Credits</h3>
<p>With the exception of open-source packages listed below, the code was written by Ken Irwin, kirwin@wittenberg.edu.</p>
<p>Special thanks to Rob Casson (Miami University of Ohio) for helping solve some cron related quandaries, and Gary Luthman (Wittenberg University) for helping solve some file-permission issues.</p>

<a name="open_source"></a>
<h3>Open-Source Projects Used Here</h3>

<p>This project is built using several open-source tools supplying both interface support and some of the underlying logic and heavy lifting.</p>

<dt>jQuery & jQueryUI: </dt>
   <dd>Used here in an unaltered form</dd>
   <dd>Licensed under the Gnu Public License, v.2</dd>

<dt>AjaxCRUD:</dt>
   <dd>Used here in a customized form to update database records</dd>
   <dd>Licensed free-of-charge for <strong>non-commercial use</strong> only</dd>
   <dd>ajaxCRUD(tm) by Loud Canvas Media is licensed under a
   Creative Commons Attribution-Share Alike 3.0 United States License</dd>
																											<dd>See: <a href="http://www.ajaxCRUD.com">http://www.ajaxCRUD.com</a></dd>

<dt>Simple HTML DOM Parser:</dt>
   <dd>Used for parsing III catalog pages</dd>
   <dd>author: S.C. Chen <me578022@gmail.com>, et al. </dd>
   <dd>Released under the MIT License</dd>
   <dd>See: <a href="http://simplehtmldom.sourceforge.net/">http://simplehtmldom.sourceforge.net/</a>

<dt>iPhone-style Checkboxes</dt>
 <dd>By Hidayat Sagita</dd>
 <dd>Shared without mention of a specific license, used as if under a non-commercial share-alike license.</dd>
 <dd>See: <a href="http://www.webstuffshare.com/2010/03/stylize-your-own-checkboxes/">http://www.webstuffshare.com/2010/03/stylize-your-own-checkboxes/</a></dd>

<dt>sortLC.php: </dt>
   <dd>Based on sortLC.pl (version 1.2) by Michael Doran, doran@uta.edu</dd>
   <dd>Used in accordance with the non-commercial license </dd>
   <dd>See full license in the sortLC.php file</dd>
   <dd>See also: <a href="http://rocky.uta.edu/doran/sortlc/">http://rocky.uta.edu/doran/sortlc/</a></dd>
<dd>Ported to PHP by Ken Irwin, kirwin@wittenberg.edu</dd>

<?php  include("attribution.php"); ?>

</div><!-- id=main -->
  <?php  include ("license.php"); ?>

</body>
</html>
