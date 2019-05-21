<?php 
  /* 
     NOTE: No HTML output may precede the inclusion of:
     preheader.php and ajaxCRUD.class.php
     Don't Do it!
     Hence the initial html and head attributes being generated later in the file than usual
  */
session_start();

include ("config.php");
include ("mysql_connect.php");
include ("scripts.php");

$path = $path_crud;
$htpath = $htpath_crud;

if ($_REQUEST['table']) { $_SESSION['weed_table'] = $_REQUEST['table']; }
$q = "SELECT file_title from `controller` where table_name = ?";
$params=array($_SESSION['weed_table']);
$stmt = $db->prepare($q);
$stmt->execute($params);
$myrow = $stmt->fetch(PDO::FETCH_NUM);
$title = $myrow[0];

require_once($path . '/preheader.php');
#the code for the class
include ($path . '/ajaxCRUD.class.php');

$banner = "<h1>View/Edit: $title</h1>\n";

if ($_SESSION['weed_table']) {
?>
<html>
<head>
<title>View/Edit - Weeding Helper</title>
    <?php   include ("jquery.php"); 
?>

<script type="text/javascript">
    $(document).ready(function(){
	//	$("#search-form form").toggle("#search-form form");
	$("#form-show-hide").click(function() {
	    $("#search-form form").toggle("#search-form form");
	  });
    LoadArrows();
    window.setInterval(function() {
        //every 2 secs, see if the catalog arrows are there
        //they go away on ajax reload (search, next page, etc)
        //add them again if necessary
        if ($('.catalog-arrow').length == 0) {
            LoadArrows();
        }
    }, 2000);

    });
    function LoadArrows() {
        $('table.ajaxCRUD tr td:first-child').each(function() {
            $(this).append(' <img src="images/icon-rightarrow.png" style="height: 1em" class="catalog-arrow">').click(function() {
                $(this).parent().children().last().find('input').click();
            });
        });
    }
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
<?php 
print "$banner";
include ("nav.php");

    #this one line of code is how you implement the class
    ########################################################
    ##

    // args: ( ? , table name, primarykey name, path)

  $tblDemo = new ajaxCRUD("Item", "$_SESSION[weed_table]", "call_order", $htpath);
} //end if $_SESSION['weed_table']
else {
  header ("Location: controller.php?action=choose_table");
}
    ##
    ########################################################

    ## all that follows is setup configuration for your fields....
    ## full API reference material for all functions can be found here - https://ajaxcrud.com/api/
    ## note: many functions below are commented out (with //). note which ones are and which are not

    #i could omit a field if I wanted
    #https://ajaxcrud.com/api/index.php?id=omitField

$searchform = BuildSearchForm ($_SESSION['weed_table']);
?>

<div id="search-form">
<input type=button id="form-show-hide" value="Show/Hide Advanced Search" />
<?php 
  
  if ($_REQUEST['clear_query']) {
    $where = "";
  } // end if clearing query conditions
  else {
    $clear_button = '<a href="view.php?clear_query=true"><button><img src="images/delete.png" style="height: .75em">&nbsp;Remove Conditions</button></a>';
   if ($_REQUEST['submit_query_builder']) {
     $where = BuildWhereFromSearch ($table);
     print "$where $clear_button"; 
   } //end if 
} //end else
?>

<form method="get" action="view.php">
<div id="search-hints">
<ol>
  <li>Wildcard character: <b>%</b> can be used in "LIKE" and "NOT LIKE" search, <br />e.g.: <b>%Norton%</b> will find "H.W. Norton", "Norton & Co", etc.</li>
  <li>Depending on how your catalog was set up, you may find that your oldest entries (i.e. records that were added to the catalog in the initial retrospective conversion) may be found by one of these methods: <ul><li><b>catdate IS NULL</b></li> <li><b>catdate = 0000-00-00</b></li> <li>or you may need to reference a particular date, e.g. <b>catdate < 1995-01-01</b></li></ul>
</ol>
</div><!-- /search_hints -->

  <?php print($searchform);?>
<input type=submit name="submit_query_builder">
</form>


</div><!-- id="search-form" -->


<?php 

$q = "SELECT * FROM `table_config` where `table_name` = ?";
$params = array($_SESSION['weed_table']);
$stmt = $db->prepare($q);
$stmt->execute($params);

if ($stmt->rowCount() == 0) { // use defaults if no table-specific settings
  $q = "SELECT * FROM `table_config` where `table_name` = 'default'";
  $stmt = $db->query($q);
}

while ($myrow = $stmt->fetch(PDO::FETCH_ASSOC)) {
    extract($myrow);

    /* get display settings from the table_config query and apply actions
       to the table obeject foreach field.

      "allowEdit" is a bogus action; that's the default behavior when no
       other setting is applied. so don't issue a command when that's the 
       setting to be applied
    */
    if ($action != "allowEdit" && $action != "") {
      $tblDemo->$action($field);
    }
} //end while settings


if ($where) {
    #i can use a where field to better-filter my table
    $tblDemo->addWhereClause($where);
} 

    #i can order my table by whatever i want
    //$tblDemo->addOrderBy("ORDER BY fldField1 ASC");

    #i can set certain fields to only allow certain values
    #https://ajaxcrud.com/api/index.php?id=defineAllowableValues
    $conditionValues = array("poor","ok","excellent");
    $tblDemo->defineAllowableValues("condition", $conditionValues);

$fateValues = array("weed","de-dup","replace","update","keep");
    $tblDemo->defineAllowableValues("fate", $fateValues);

$bool = array ("Y","N");
    $tblDemo->defineAllowableValues("classic", $bool);
    $tblDemo->defineAllowableValues("best_book", $bool);

#for integer fields, only find exact matches, not partial string matches
$exact_search_fields = array ("copy","circs","renews","int_use","innreach_circ_
copies", "innreach_total_copies");
foreach ($exact_search_fields as $xsf) {
  $tblDemo->setExactSearchField($xsf);
}


    #i can disallow deleting of rows from the table
    #https://ajaxcrud.com/api/index.php?id=disallowDelete
    $tblDemo->disallowDelete();

    #i can disallow adding rows to the table
    #https://ajaxcrud.com/api/index.php?id=disallowAdd
    $tblDemo->disallowAdd();

    #i can add a button that performs some action deleting of rows for the entire table
    #https://ajaxcrud.com/api/index.php?id=addButtonToRow
    //$tblDemo->addButtonToRow("Add", "add_item.php", "all");
$tblDemo->addButtonToRow("View Record", "to_local_cat.php", "bib_record", "", "local_cat_window");

    #set the number of rows to display (per page)
    $tblDemo->setLimit(100);

     #set a filter/search box at the top of the table
//$tblDemo->addAjaxFilterBox('title');

    #if really desired, a filter box can be used for all fields
    $tblDemo->addAjaxFilterBoxAllFields();

    #i can set the size of the filter box
    //$tblDemo->setAjaxFilterBoxSize('fldField1', 3);

	#actually show the table
	$tblDemo->showTable();

?>
  <?php  include ("license.php"); ?>
</body>
</html>