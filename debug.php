<?php 
echo "TEST PHP ACTIF - " . date("H:i:s") . "\n";
echo "IP: " . $_SERVER["REMOTE_ADDR"] . "\n";
echo "UA: " . ($_SERVER["HTTP_USER_AGENT"] ?? "none") . "\n";
require_once "antispam.php";
echo "Protection passée\n";
?>
