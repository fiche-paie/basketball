<?php
require_once 'config.php';

echo "<h1>🔧 Configuration du Webhook Telegram</h1>";

// URL du webhook
$webhookUrl = SITE_URL . '/webhook.php';

echo "<p><strong>URL du webhook :</strong> $webhookUrl</p>";

// Configurer le webhook
$setWebhook = TELEGRAM_API_URL . "/setWebhook?url=" . urlencode($webhookUrl);

$ch = curl_init($setWebhook);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

echo "<h2>📡 Résultat de la configuration :</h2>";
echo "<pre>" . print_r($result, true) . "</pre>";

if ($result['ok']) {
    echo "<p style='color: green; font-size: 1.5em;'>✅ Webhook configuré avec succès !</p>";
} else {
    echo "<p style='color: red; font-size: 1.5em;'>❌ Erreur lors de la configuration</p>";
    echo "<p>Description : " . ($result['description'] ?? 'Inconnue') . "</p>";
}

// Vérifier le webhook
echo "<h2>🔍 Vérification du webhook :</h2>";

$getWebhookInfo = TELEGRAM_API_URL . "/getWebhookInfo";
$ch = curl_init($getWebhookInfo);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$info = json_decode($response, true);
echo "<pre>" . print_r($info['result'], true) . "</pre>";

// Test du fichier webhook.php
echo "<h2>🧪 Test d'accès à webhook.php :</h2>";

$webhookTest = file_get_contents($webhookUrl);
if ($webhookTest !== false) {
    echo "<p style='color: green;'>✅ webhook.php est accessible</p>";
} else {
    echo "<p style='color: red;'>❌ webhook.php n'est pas accessible</p>";
}

echo "<hr>";
echo "<h2>🎯 Prochaines étapes :</h2>";
echo "<ol>";
echo "<li>Le webhook est maintenant configuré ✅</li>";
echo "<li>Visite une page du site (ex: index.php)</li>";
echo "<li>Clique sur un bouton dans Telegram</li>";
echo "<li>L'utilisateur devrait être redirigé automatiquement</li>";
echo "</ol>";

echo "<br><a href='index.php' style='background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 10px; display: inline-block; margin: 10px;'>🏠 Aller à l'accueil</a>";
echo "<a href='test.php' style='background: #11998e; color: white; padding: 15px 30px; text-decoration: none; border-radius: 10px; display: inline-block; margin: 10px;'>🧪 Page de test</a>";
?>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        max-width: 900px;
        margin: 50px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h1, h2 {
        color: #333;
    }
    pre {
        background: #fff;
        padding: 15px;
        border-radius: 10px;
        border-left: 4px solid #667eea;
        overflow-x: auto;
    }
    ol {
        font-size: 1.1em;
        line-height: 2;
    }
</style>
