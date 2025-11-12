<?php
// Configuration Telegram Bot
define('TELEGRAM_BOT_TOKEN', '8334000780:AAE4RBXPU4MItlaRY65E0EPSUXjJrEA4SNI');
define('TELEGRAM_CHAT_ID', '7747778364');
define('TELEGRAM_API_URL', 'https://api.telegram.org/bot' . TELEGRAM_BOT_TOKEN);

// Configuration du site
define('SITE_URL', 'https://brankie-adaptationally-averie.ngrok-free.dev');

// Fichiers de stockage
define('USERS_FILE', __DIR__ . '/data/users.json');
define('REDIRECTS_FILE', __DIR__ . '/data/redirects.json');

// Créer le dossier data s'il n'existe pas
if (!file_exists(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0777, true);
}

// Initialiser les fichiers JSON s'ils n'existent pas
if (!file_exists(USERS_FILE)) {
    file_put_contents(USERS_FILE, json_encode([]));
}
if (!file_exists(REDIRECTS_FILE)) {
    file_put_contents(REDIRECTS_FILE, json_encode([]));
}
?>
