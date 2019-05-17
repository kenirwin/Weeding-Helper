<?php
//$debug = true;
if ($debug){ 
    error_reporting(E_ALL & ~E_NOTICE);
    ini_set('display_errors', 1);
}
session_start();
?>
<html>
<head>
<title>Keyword Cloud - Weeding Helper</title>
</head>
<body>
<h1>Keyword Cloud</h1>
<?php
include("nav.php");

//ini_set('display_errors','on');
include ("mysql_connect.php");
include ("scripts.php");
include ("./tagcloud/stopwords.php"); //defines $stopwords array
include ("./tagcloud/classes/tagcloud.php");
$cloud_weighted = new tagcloud();
$cloud_unweighted = new tagcloud();
$max = 100; //max size of tag cloud

if ($_REQUEST['table']) { $_SESSION['weed_table'] = $_REQUEST['table']; }
$verified_table_name = VerifyTableName($_SESSION['weed_table']);
if (isset($verified_table_name)) {
$q = "SELECT * FROM `$verified_table_name` WHERE title != ''" or lcsh != '';
$stmt = $db->query($q);

$weighted_words = array();
$unweighted_words = array();

while ($myrow = $stmt->fetch(PDO::FETCH_ASSOC)) {
  extract ($myrow);

  if (preg_match("/(.*)\/(.*)/", $title, $m)) { // separate title & author
    $title = $m[1];
  } //end if split
  if (preg_match("/(.+)\[(videorecording|electronic resource)\](.*)/",$title,$m)) {
    $title = "$m[1] $m[3]";
  }
  // print "<br>$title";

  $words = preg_split ("/[\s\n\r:\.\?\!,;&\(\)\[\]\{\}0-9]+/", "$title $lcsh");
  foreach ($words as $word) {
    $word = strtolower($word);
    if (preg_match("/[a-z]/",$word) && (! in_array($word, $stopwords))) {
      //      print "<li>$word: $pcircs</li>\n";
      $weighted_words[$word] += $circs;
      $unweighted_words[$word] += 1;
    } //end if
  } //end foreach
} //end while

} //end if verified

DisplayCloud($cloud_unweighted, $unweighted_words, $max, "Tagcloud of words appearing in title and subject");
DisplayCloud($cloud_unweighted, $weighted_words, $max, "Words in title and subject, weighted by frequency of circulation");

function DisplayCloud($cloud, $all_words, $max, $header) {
  arsort($all_words);
  $top_words = array();
  $i = 0;

  while (($i < $max) && ($a = each($all_words))) {

    extract($a);
    $top_words[$key] = $value;
    $i++;
  }
  
  asort($top_words);
  
  foreach ($top_words as $word=>$v) {
    for ($i=0; $i<$v; $i++) { // add once per instance of word
      $cloud->addTag($word);
    }
  }
  
  print '<link rel="stylesheet" type="text/css" href="tagcloud/css/tagcloud.css" />'.PHP_EOL;
  print '<h3 style="margin-top: 3em; margin-bottom: 1em">'.$header.'</h3>'.PHP_EOL;
  
  print '<div style="width: 75%">'.PHP_EOL;
  
  //  print_r($cloud);
  
/* set the minimum length required */
  $cloud->setMinLength(3);
  
  /* limiting the output */
  $cloud->setLimit(1000);
  
  echo $cloud->render();
  
  print '</div>'.PHP_EOL;
  
}
?>
<?php  include ("license.php"); ?>
</body>
</html>
