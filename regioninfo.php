<?php
if (!isset($_GET["coords"]) || !isset($_GET["scopeid"]) || !isset($_GET["user"]))
  exit;

include('config/database.php');
include('config/webmap.php');
include('debug.php');

$mysql = mysql_connect ($DB_HOST, $DB_USER, $DB_PASSWORD) or die();
mysql_select_db($DB_NAME) or die();
if (!$mysql) debug("Database connexion error : " . mysql_error());

$c        = explode("x", $_GET["coords"]);
$scope_id = mysql_escape_string($_GET["scopeid"]);
$user     = mysql_escape_string($_GET["user"]);
$xx       = mysql_escape_string($c[1]);
$yy       = mysql_escape_string($c[2]);
$x        = mysql_escape_string($c[1]) * 256;
$y        = mysql_escape_string($c[2]) * 256;

if ($scope_id != "00000000-0000-0000-0000-000000000000") {
  $res = mysql_query("select * from regions where locX = '$x' and locY = '$y' and ScopeID='$scope_id'");
}
else {
  $res = mysql_query("select * from regions where locX = '$x' and locY = '$y'");
}

$row                    = false;
$has_owned_neighbors    = false;
$has_managed_neighbors  = false;
$has_other_neighbors    = false;
$is_owned               = false;
$row                    = mysql_fetch_array($res);

if ($row) {
  $result = mysql_query("select * from UserAccounts where PrincipalID='" . mysql_escape_string($row["owner_uuid"]) . "'");
  $user_row = mysql_fetch_assoc($result);
  mysql_free_result($result);

  echo "Region: " . $row["regionName"] . "<br>";
  echo "Owner: " . $user_row["FirstName"] . " " . $user_row["LastName"] . "<br>";
  echo "Location: " . $x / 256 . " x " . $y / 256 . "<br>";
  exit;
}
