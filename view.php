<html>
<head>
<title>View/Edit - Weeding Helper</title>
<?php
session_start();

include ("config.php");
include ("mysql_connect.php");
include ("scripts.php");
include ("jquery.php");

$path = $path_crud;
$htpath = $htpath_crud;

if ($_REQUEST[table]) { $_SESSION[weed_table] = $_REQUEST[table]; }
$table = $_SESSION[weed_table];
$q = "SELECT file_title from `controller` where table_name = '$table'";
$r = mysql_query($q);
$myrow = mysql_fetch_row($r);
$title = $myrow[0];

require_once($path . '/preheader.php');
#the code for the class
include ($path . '/ajaxCRUD.class.php');

$banner = "<h1>View/Edit: $title</h1>\n";

    #this one line of code is how you implement the class
    ########################################################
    ##

    // args: ( ? , table name, primarykey name, path)

if ($_SESSION[weed_table]) {
?>
<script type="text/javascript">
    $(document).ready(function(){
	//	$("#search-form form").toggle("#search-form form");
	$("#form-show-hide").click(function() {
	    $("#search-form form").toggle("#search-form form");
	  });
      });
</script>

<style>
#search-form {
    background-color: lightgrey;
padding: .25em;
  border-radius: 5px;
    }
#search-form form{
display: none;
}
#search-hints { 
float: right; 
width: 35%; 
margin-right: 10%;
}
#search-hints ol > li {
margin-bottom: 1em;
} 
</style>
</head>
<body>
<?


  print "$banner";
  include ("nav.php");
  $tblDemo = new ajaxCRUD("Item", "$_SESSION[weed_table]", "call_order", $htpath);
}
else {
  header ("Location: controller.php?action=choose_table");
}
    ##
    ########################################################

    ## all that follows is setup configuration for your fields....
    ## full API reference material for all functions can be found here - http://ajaxcrud.com/api/
    ## note: many functions below are commented out (with //). note which ones are and which are not

    #i could omit a field if I wanted
    #http://ajaxcrud.com/api/index.php?id=omitField

$searchform = BuildSearchForm ($_SESSION[weed_table]);
?>

<div id="search-form">
<input type=button id="form-show-hide" value="Show/Hide Advanced Search" />
<?
if ($_REQUEST[submit_query_builder]) {
  $where = BuildWhereFromSearch ($table);
  print $where; 
} //end if 
?>

<form method="post">
<div id="search-hints">
<ol>
  <li>Wildcard character: <b>%</b> can be used in "LIKE" and "NOT LIKE" search, <br />e.g.: <b>%Norton%</b> will find "H.W. Norton", "Norton & Co", etc.</li>
  <li>Depending on how your catalog was set up, you may find that your oldest entries (i.e. records that were added to the catalog in the initial retrospective conversion) may be found by one of these methods: <ul><li><b>catdate IS NULL</b></li> <li><b>catdate = 0000-00-00</b></li> <li>or you may need to reference a particular date, e.g. <b>catdate < 1995-01-01</b></li></ul>
</ol>
</div><!-- /search_hints -->

<?=$searchform;?>
<input type=submit name="submit_query_builder">
</form>


</div><!-- id="search-form" -->


<?

$q = "SELECT * FROM `table_config` where `table_name` = '$_SESSION[table]'";
$r = mysql_query($q);

if (mysql_num_rows($r) == 0) { // use defaults if no table-specific settings
  $q = "SELECT * FROM `table_config` where `table_name` = 'default'";
  $r = mysql_query($q);
}

while ($myrow = mysql_fetch_assoc($r)) {
    extract($myrow);
    $tblDemo->$action($field);
} //end while settings


//$tblDemo->omitField("loc");
    #i can use a where field to better-filter my table
    $tblDemo->addWhereClause($where);

    #i can order my table by whatever i want
    //$tblDemo->addOrderBy("ORDER BY fldField1 ASC");

    #i can set certain fields to only allow certain values
    #http://ajaxcrud.com/api/index.php?id=defineAllowableValues
    $conditionValues = array("poor","ok","excellent");
    $tblDemo->defineAllowableValues("condition", $conditionValues);

$fateValues = array("weed","de-dup","replace","update","keep");
    $tblDemo->defineAllowableValues("fate", $fateValues);

$bool = array ("Y","N");
    $tblDemo->defineAllowableValues("classic", $bool);
    $tblDemo->defineAllowableValues("best_book", $bool);


    #i can disallow deleting of rows from the table
    #http://ajaxcrud.com/api/index.php?id=disallowDelete
    $tblDemo->disallowDelete();

    #i can disallow adding rows to the table
    #http://ajaxcrud.com/api/index.php?id=disallowAdd
    $tblDemo->disallowAdd();

    #i can add a button that performs some action deleting of rows for the entire table
    #http://ajaxcrud.com/api/index.php?id=addButtonToRow
    //$tblDemo->addButtonToRow("Add", "add_item.php", "all");

    #set the number of rows to display (per page)
    $tblDemo->setLimit(100);

     #set a filter/search box at the top of the table
//$tblDemo->addAjaxFilterBox('title');

    #if really desired, a filter box can be used for all fields
    $tblDemo->addAjaxFilterBoxAllFields();

    #i can set the size of the filter box
    //$tblDemo->setAjaxFilterBoxSize('fldField1', 3);

	#i can format the data in cells however I want with formatFieldWithFunction
	#this is arguably one of the most important (visual) functions
	$tblDemo->formatFieldWithFunction('fldField1', 'makeBlue');
	$tblDemo->formatFieldWithFunction('fldField2', 'makeBold');

	#actually show the table
	$tblDemo->showTable();

	#my self-defined functions used for formatFieldWithFunction
	function makeBold($val){
		return "<b>$val</b>";
	}

	function makeBlue($val){
		return "$val";
	}

?>
  <? include ("license.php"); ?>
</body>
</html>