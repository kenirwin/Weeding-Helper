<?

$path_main = "/docs/weed"; //no trailing slash, root relative in your file system
$path_http = "/weed"; //no trailing slash
$path_crud = $path_main . '/ajaxcrud';
$htpath_crud = $path_http . '/ajaxcrud/';

$allow_uploads = true;

$MYSQL_HOST = "localhost"; // probably but not necessarily "localhost"
$MYSQL_LOGIN = ""; // mysql username, needs create/replace/update/delete perm
$MYSQL_PASS = ""; //mysql password
$MYSQL_DB = "weeding"; //or whatever else you want to call it

$innreach = array (
		   "local_id" => "wt3ug", //your institutional code
		   "url" => "http://olc1.ohiolink.edu",
		   "holdings_selector" => "table.centralHoldingsTable",
		   )

?>