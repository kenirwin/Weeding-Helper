<?

$path_main = ""; //no trailing slash, root relative in your file system, e.g.: "/docs/weed"
$path_http = ""; //no trailing slash; e.g. "/weed" for a top-level web directory
$path_crud = $path_main . '/ajaxcrud'; //don't change
$htpath_crud = $path_http . '/ajaxcrud/'; //don't change

$allow_uploads = true;
$allow_delete = true; 

$MYSQL_HOST = "localhost"; // probably but not necessarily "localhost"
$MYSQL_LOGIN = ""; // mysql username, needs create/replace/update/delete perm
$MYSQL_PASS = ""; //mysql password
$MYSQL_DB = "weeding"; //or whatever else you want to call it

$innreach = array (
		   "local_id" => "", //your institutional code, eg "wt3ug"
		   "url" => "", // InnReach catalog url, e.g. "http://olc1.ohiolink.edu"
		   "holdings_selector" => "", // CSS-style selector for the holdings table in your InnReach catalog, e.g. "table.centralHoldingsTable" (this is the correct selector for the OhioLINK central catalog

		   /* these next 3 variables have to do with how many times per minute the innreach_check.php will do an innreach lookup */
		   "night_ends" => "7", //hour at which innreach_check.php switches to fewer hits per minute
		   "overnight_hits" => "9", // hits per minute between midnight and 'night_ends'-o'clock
		   "daytime_hits" => "6", //hits per minute between 'night-ends' and midnight

		   )

?>