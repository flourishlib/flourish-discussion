<?php
header('HTTP/1.1 404 Not Found');

$tmpl = fTemplating::retrieve();
$tmpl->inject('views/404.php');