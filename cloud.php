<html>
<head>
<title>Keyword Cloud - Weeding Helper</title>
</head>
<body>
<h1>Keyword Cloud</h1>
<?
session_start();
include("nav.php");

//ini_set('display_errors','on');
include ("mysql_connect.php");
include ("./tagcloud/stopwords.php"); //defines $stopwords array
include ("./tagcloud/classes/tagcloud.php");
$cloud = new tagcloud();
$max = 100;

if ($_REQUEST[weed_table]) { $_SESSION[weed_table] = $_REQUEST[weed_table]; }
$q = "SELECT * FROM `$_SESSION[weed_table]` WHERE title != ''" or lcsh != '';
$r = mysql_query($q);

$all_words = $top_words = array();

while ($myrow = mysql_fetch_assoc($r)) {
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
      $all_words[$word] += $circs;
    } //end if 
  } //end foreach
} //end while

arsort($all_words);
//print_r($all_words);

while (($i < $max) && ($a = each($all_words))) {
  extract($a);
  $top_words[$key] = $value;
  $i++;
}

asort($top_words);
//print_r($top_words);


foreach ($top_words as $word=>$v) {
	for ($i=0; $i<$v; $i++) { // add once per instance of word
		$cloud->addTag($word);
	}
}


//print "<div id=\"cloud\">$tags</div>\n";

?>

<link rel="stylesheet" type="text/css" href="/lib/include/tagcloud/css/tagcloud.css" />

<div style="width: 75%">

	<?

//print_r($cloud);

		/* set the minimum length required */
		$cloud->setMinLength(3);

		/* limiting the output */
		$cloud->setLimit(1000);

		echo $cloud->render();
	?>

</div>
</body>
</html>