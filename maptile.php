<?php
if (!isset($_GET["coords"]) || !isset($_GET["size"]) || !isset($_GET["scopeid"]) || !isset($_GET["overlays"]) || !isset($_GET["user"]))
  exit;

include('config/database.php');
include('config/webmap.php');
include('debug.php');

$mysql = mysql_connect ($DB_HOST, $DB_USER, $DB_PASSWORD) or die();
mysql_select_db($DB_NAME) or die();
if (!$mysql) debug("Database connexion error : " . mysql_error());

$c        = explode("x", $_GET["coords"]);
$scope_id = mysql_real_escape_string($_GET["scopeid"]);
$overlays = $_GET["overlays"] | 0;
$user     = mysql_real_escape_string($_GET["user"]);
$xx       = mysql_real_escape_string($c[1]);
$yy       = mysql_real_escape_string($c[2]);
$x        = mysql_real_escape_string($c[1]) * 256;
$y        = mysql_real_escape_string($c[2]) * 256;
$size     = $_GET["size"] + 0;

$xmin = $x - 256;
$xmax = $x + 256;
$ymin = $y - 256;
$ymax = $y + 256;

if ($scope_id != "00000000-0000-0000-0000-000000000000") {
  $res = mysql_query("SELECT * FROM regions WHERE locX BETWEEN $xmin AND $xmax  AND locY BETWEEN $ymin AND $ymax AND ScopeID='$scope_id'");
}
else {
  $res = mysql_query("SELECT * FROM regions WHERE locX BETWEEN $xmin AND $xmax  AND locY BETWEEN $ymin AND $ymax");
}

debug(mysql_error());

$row                    = false;
$has_owned_neighbors    = false;
$has_managed_neighbors  = false;
$has_other_neighbors    = false;
$is_owned               = false;

while($region = mysql_fetch_array($res)) {
  if ($region["locX"] == $x && $region["locY"] == $y) {
    $row = $region;
  }
  else {
    if ($region["owner_uuid"] == $user) {
      $has_owned_neighbors = true;
    }
    else {
      $has_other_neighbors = true;
    }
  }
}

if (!$row) {
  echo "<span class=\"map-tooltip\">$xx,$yy</span>\n\n";
}
else {
  echo "<span class=\"map-tooltip\">" . $row["regionName"] . " $xx,$yy</span>\n";
  switch ($asset_converter_type) {
    case 'ci_osgetasset':
      echo $asset_converter_url. "/getasset/image/". $size. "/". $row["regionMapTexture"]. ".jpg\n";
      break;
    case 'd4os':
      echo $asset_converter_url. "/grid/services/assets/0/". $size. "/". $row["regionMapTexture"]. "\n";
      break;
    default:
      echo "regionimg.php?size=$size&uuid=" . $row["regionMapTexture"] . "\n";
      break;
  }

  if ($row["owner_uuid"] == $user) {
    $is_owned = true;
  }
}

$extrastyle = "";

#echo "<img class=\"data\" src=\"images/redgrid.png\" width=\"$size\"/>";
if ($row && $size > 64)
  echo "<input class=\"data\" type=\"hidden\" value=\"no\" />";
  echo "<p class=\"data map-regionname $extrastyle\">" . $row["regionName"] . "</p>";
