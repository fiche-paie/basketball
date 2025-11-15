<?php
// === DÉTECTION DES BOTS ===

// Liste des user agents de bots
$bot_user_agents = [
    'bot', 'crawl', 'spider', 'slurp', 'search', 'archiver',
    'scanner', 'monitor', 'checker', 'validator',
    'googlebot', 'bingbot', 'yahoo', 'baidu', 'yandex', 'duckduckbot',
    'facebook', 'twitter', 'linkedin', 'whatsapp', 'telegram',
    'curl', 'wget', 'python', 'java', 'php', 'ruby', 'perl',
    'requests', 'httpclient', 'okhttp'
];

function isBot($user_agent, $bot_user_agents) {
    $user_agent = strtolower($user_agent ?? '');
    
    if (empty($user_agent)) {
        return true;
    }
    
    foreach ($bot_user_agents as $agent) {
        if (stripos($user_agent, $agent) !== false) {
            return true;
        }
    }
    
    return false;
}
?>
