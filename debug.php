<?php
define('DEBUG', FALSE);
define('DEBUG_PATH', 'debug.txt');
if (DEBUG) {
  $log = "**********************************\n";
  $log .= date('Y/m/d H:i:s'). "\n";
  file_put_contents(DEBUG_PATH, "$log\n", FILE_APPEND);
}
function debug($message) {
  if (DEBUG) {
    file_put_contents(DEBUG_PATH, "$message\n", FILE_APPEND);
  }
}
