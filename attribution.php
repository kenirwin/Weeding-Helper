<?

$nouns = array ("'Cloud' by The Noun Project" => "Attribution (CC BY 3.0)",
		"'Printer' by The Noun Project" => "Attribution (CC BY 3.0)",
		"'Pencil' by d" => "No Rights Reserved (CC0)",
		"'Bar Graph' by Ben King" => "No Rights Reserved (CC0)",
		"'Check Box' by The Noun Project" => "Attribution (CC BY 3.0)",
		"'Sprout' by Tak Imoto" => "Attribution (CC BY 3.0)",
		"'Hand Washing' by Igor Kiselev" => "Attribution (CC BY 3.0)"
		);

print "<dt>All icons are copied or adapted from images in <a href=\"http://www.thenounproject.com/\">The Noun Project</a> and used under a Creative Commons license:</dt>\n";

foreach ($nouns as $noun => $license) {
  print "<dd>$noun; Creative Commons License: $license</dd>\n";
}

?>