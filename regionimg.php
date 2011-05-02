<?php
include('config/webmap.php');

if (!isset($_GET["uuid"]) || !isset($_GET["size"]))
  exit;

$uuid = escapeshellcmd(mysql_escape_string($_GET["uuid"]));
$s = $_GET["size"] | 0;
$geom = $s."x".$s;
$size = escapeshellarg($geom);

$url = "$assets/assets/$uuid/data";

if (!file_exists("$temp$uuid-$geom.jpg")) {
  copy ($url, "$temp$uuid.j2k");
  exec ("j2k_to_image -i $temp$uuid.j2k -o $temp$uuid.tga");
  exec ("convert -scale $size $temp$uuid.tga $temp$uuid-$geom.jpg");

  unlink ("$temp$uuid.j2k");
  unlink ("$temp$uuid.tga");
}

if (!file_exists("$temp$uuid-$geom.jpg"))
{
  exit;
}


header("Content-type: image/jpeg");

$fd = fopen("$temp$uuid-$geom.jpg", "r");
fpassthru($fd);
fclose($fd);
?>
