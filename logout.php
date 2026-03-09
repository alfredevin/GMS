<?php
session_start();
date_default_timezone_set("Asia/Manila");

include './config.php';
$encodedUrl = base64_encode("./");
session_destroy();

header('location:/boac/gms/?redirect=' . urlencode($encodedUrl));
exit();