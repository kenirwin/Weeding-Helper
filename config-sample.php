<?

$path_main = ""; //no trailing slash, root relative in your file system, e.g.: "/docs/weed"
$path_http = ""; //no trailing slash; e.g. "/weed" for a top-level web directory
$path_crud = $path_main . '/ajaxcrud'; //don't change
$htpath_crud = $path_http . '/ajaxcrud/'; //don't change

$allow_uploads = true;

$MYSQL_HOST = "localhost"; // probably but not necessarily "localhost"
$MYSQL_LOGIN = ""; // mysql username, needs create/replace/update/delete perm
$MYSQL_PASS = ""; //mysql password
$MYSQL_DB = "weeding"; //or whatever else you want to call it

$innreach = array (
		   "local_id" => "", //your institutional code, eg "wt3ug"
		   "url" => "", // InnReach catalog url, e.g. "http://olc1.ohiolink.edu"
		   "holdings_selector" => "", // CSS-style selector for the holdings table in your InnReach catalog, e.g. "table.centralHoldingsTable" (this is the correct selector for the OhioLINK central catalog
		   )

?>