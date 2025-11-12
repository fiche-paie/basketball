[file name]: webhook.php
[file content begin]
<?php
// Activer les logs d'erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'functions.php';

// Logger toutes les requêtes pour debug
$logFile = __DIR__ . '/data/webhook_log.txt';
$logData = date('Y-m-d H:i:s') . " - Webhook appelé\n";
file_put_contents($logFile, $logData, FILE_APPEND);

// Récupérer les données du webhook Telegram
$content = file_get_contents("php://input");
$update = json_decode($content, true);

// Logger les données reçues
file_put_contents($logFile, "Données reçues: " . print_r($update, true) . "\n", FILE_APPEND);

if (!$update) {
    file_put_contents($logFile, "❌ Aucune donnée reçue\n", FILE_APPEND);
    http_response_code(200);
    exit;
}

if (isset($update['callback_query'])) {
    $callbackData = $update['callback_query']['data'];
    $callbackId = $update['callback_query']['id'];
    
    file_put_contents($logFile, "✅ Callback reçu: $callbackData\n", FILE_APPEND);
    
    // Format: redirect_USERID|PAGE
    if (strpos($callbackData, 'redirect_') === 0) {
        $parts = explode('|', str_replace('redirect_', '', $callbackData));
        $userId = $parts[0];
        $targetPage = $parts[1];
        
        // Enregistrer la redirection
        setRedirect($userId, $targetPage);
        
        file_put_contents($logFile, "✅ Redirection enregistrée: User=$userId, Page=$targetPage\n", FILE_APPEND);
        
        // Répondre au callback (obligatoire pour Telegram)
        $response = [
            'callback_query_id' => $callbackId,
            'text' => "✅ Redirection programmée",
            'show_alert' => false
        ];
        
        $ch = curl_init(TELEGRAM_API_URL . '/answerCallbackQuery');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
        
        // SUPPRIMÉ: L'envoi du message de confirmation qui causait la double notification
    }
} else {
    file_put_contents($logFile, "⚠️ Pas de callback_query dans les données\n", FILE_APPEND);
}

// Toujours réponder 200 OK à Telegram
http_response_code(200);
?>
[file content end]
