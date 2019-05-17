<?php
include("config.php");
if (isset($innreach['local_cat_url'])) {
  if (isset($_REQUEST['bib_record'])) {
    $url = $innreach['local_cat_url'] . '/record=' . substr($_REQUEST['bib_record'], 0, -1);
    header("Location: $url");
  } 
}
else {
  $warn .= "<li>Local Catalog URL not set in config.php</li>";
}
?>