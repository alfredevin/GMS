<?php
session_start();
date_default_timezone_set("Asia/Manila");
session_destroy();
header('Location: /boac/gms/website/ '); 
exit();
?>
