<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // Pas d'erreurs dans le JSON

require_once 'functions.php';

header('Content-Type: application/json');

// Logger les requêtes AJAX
$logFile = __DIR__ . '/data/ajax_log.txt';
$userIp = getUserIP();
$userId = getUserID();

file_put_contents($logFile, date('H:i:s') . " - Check par IP: $userIp | UserID: $userId\n", FILE_APPEND);

$targetPage = checkRedirect();

if ($targetPage) {
    $url = getPagePath($targetPage);
    file_put_contents($logFile, date('H:i:s') . " - ✅ REDIRECTION: $userId -> $targetPage ($url)\n", FILE_APPEND);
    
    echo json_encode([
        'redirect' => true,
        'url' => $url
    ]);
} else {
    echo json_encode([
        'redirect' => false
    ]);
}
?>
