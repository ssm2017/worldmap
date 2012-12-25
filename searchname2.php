<?php
if (!isset($_GET["scope_id"]) || !isset($_GET["name"]))
  exit;

include('config/database.php');
include('config/webmap.php');
include('debug.php');

$mysql = mysql_connect ($DB_HOST, $DB_USER, $DB_PASSWORD) or die();
mysql_select_db($DB_NAME) or die();
if (!$mysql) debug("Database connexion error : " . mysql_error());

$scope_id = $_GET["scope_id"];
$name = mysql_real_escape_string($_GET["name"]);

$regions = mysql_query("select * from regions where regionName like '$name%' limit 11");

if (mysql_num_rows($regions) < 1) {
  echo "<div style=\"height: 370px\">";
  echo "No regions matching your search term were found";
  echo "</div>";
  exit;
}

$i=0;

echo "<div style=\"height: 370px\">";
echo "Search results<br>&nbsp;<br>";

while ($row = mysql_fetch_array($regions)) {
  $i++;
  if ($i > 10) {
    echo "More than 10 results were found, results truncated";
    break;
  }
  printf("<a id=\"%s\" xcoord=\"%d\" ycoord=\"%d\" class=\"list-selectable\"><div>%s</div></a>", $row["uuid"], $row["locX"] / 256, $row["locY"] / 256, $row["regionName"]);
}

echo "</div>";
