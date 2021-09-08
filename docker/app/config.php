<?php
$debug = false; //change to true to display extra debugging information

$path_main = "/var/www/html"; //no trailing slash, root relative in your file system, e.g.: "/docs/weed"
$path_http = ""; //no trailing slash; e.g. "/weed" for a top-level web directory
$path_crud = $path_main . '/ajaxcrud'; //don't change
$htpath_crud = $path_http . '/ajaxcrud/'; //don't change
$secure_outside_path = '/var/www/weeding'; // no trailing slash - should be outside the web space (not in public_html, /var/www/html or equivalent)

$allow_uploads = true;
$allow_delete = false;
$allow_manage = false; // allow file management functions

$default_call_type = "LC"; // allowed: "LC", "DDC"

$MYSQL_HOST = "weeding-helper-db"; // probably but not necessarily "localhost"
$MYSQL_LOGIN = "user"; // mysql username, needs create/replace/update/delete perm
$MYSQL_PASS = "password"; //mysql password
$MYSQL_DB = "weeding"; //or whatever else you want to call it

$innreach = array (
		   "local_id" => "op4bg", //your institutional code, eg "wt3ug"
		   "local_display_name" => "OPAL", // your institution as it displays in the innreach catalog, eg "WITTENBERG"
		   "url" => "https://olc1.ohiolink.edu", // InnReach catalog url with no trailing slash, e.g. "https://olc1.ohiolink.edu"
		   "local_cat_url" => "https://cat.opal-libraries.org", // local catalog url with no trailing slash, e.g. "https://ezra.wittenberg.edu"
		   "holdings_selector" => "table.centralHoldingsTable", // CSS-style selector for the holdings table in your InnReach catalog, e.g. "table.centralHoldingsTable" (this is the correct selector for the OhioLINK central catalog

		   /* these next 3 variables have to do with how many times per minute the innreach_check.php will do an innreach lookup */
		   "night_ends" => "7", //hour at which innreach_check.php switches to fewer hits per minute
		   "overnight_hits" => "9", // hits per minute between midnight and 'night_ends'-o'clock
		   "daytime_hits" => "7", //hits per minute between 'night-ends' and midnight

		   );

  /* FILECHUCKER OPTIONS

     Note: FileChucker is non-free, non-opensource software for managing file uploads, including much larger files than is sometimes achievable natively in PHP and therefor in Weeding Helper. It is likely (but not certain) that future releases of Weeding Helper will include optional FileChucker support, allowing users who pay the FileChucker licensing fee to use alternate upload options. Since changes to this file (config.php) can hinder the upgrade process, some unused variables are added here now (Apr 2013, v 1.0.8, for ease of later upgrades.

     More information about FileChucker at: https://encodable.com/filechucker/
  */

$use_filechucker_for_upload = false;
$filechucker_location = "";
$filechucker_upload_page = "";

?>