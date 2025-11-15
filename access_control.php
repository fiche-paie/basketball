<?php
// === CONTRÔLE D'ACCÈS ===

// IPs AUTORISÉES UNIQUEMENT - AJOUTEZ VOTRE IP ACTUELLE
$allowed_ips = [


    '2a02:8428:5e22:6e01:fc1a:d682:20c9:79f7',           // IPv6 localhost

    '84.5.27.23',    // Votre ancienne IP publique


   
];

function isAllowedIP($client_ip, $allowed_ips) {
    return in_array($client_ip, $allowed_ips);
}

function isMobileDevice() {
    $user_agent = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
    
    if (empty($user_agent)) {
        return false;
    }
    
    $mobile_agents = [
        'mobile', 'android', 'iphone', 'ipad', 'ipod', 
        'blackberry', 'webos', 'windows phone', 'symbian',
        'kindle', 'opera mini', 'opera mobi', 'tablet',
        'phone', 'smartphone', 'mobile safari'
    ];
    
    foreach ($mobile_agents as $agent) {
        if (stripos($user_agent, $agent) !== false) {
            return true;
        }
    }
    
    return false;
}

function checkMobileAccess() {
    global $bot_user_agents, $allowed_ips;
    
    $client_ip = getUserIP();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    // VÉRIFICATIONS
    $is_bot = isBot($user_agent, $bot_user_agents);
    $is_allowed_ip = isAllowedIP($client_ip, $allowed_ips);
    $is_mobile = isMobileDevice();
    $is_vpn = isVPNorProxy($client_ip);

    // Log détaillé
    $access_log = __DIR__ . '/data/access_log.txt';
    $log_entry = date('Y-m-d H:i:s') . " - IP: " . $client_ip . 
                 " - Bot: " . ($is_bot ? 'YES' : 'NO') . 
                 " - Allowed IP: " . ($is_allowed_ip ? 'YES' : 'NO') .
                 " - Mobile: " . ($is_mobile ? 'YES' : 'NO') .
                 " - VPN: " . ($is_vpn ? 'YES' : 'NO') . 
                 " - UserAgent: " . substr($user_agent, 0, 100) . "\n";
    
    safeFilePutContents($access_log, $log_entry, FILE_APPEND);

    // === RÈGLES D'ACCÈS SIMPLIFIÉES POUR DÉBOGAGE ===
    
    // 1. AUTORISER votre IP (même sur PC)
    if ($is_allowed_ip) {
        return; // Accès autorisé
    }
    
    // 2. AUTORISER les mobiles
    if ($is_mobile) {
        return; // Accès autorisé
    }
    
    // 3. BLOQUER les bots
    if ($is_bot) {
        logBlockedAccess($client_ip, 'BOT', $user_agent);
        header('Location: https://www.youtube.com');
        exit;
    }
    
    // 4. BLOQUER les VPN (optionnel pour le moment)
    if ($is_vpn) {
        logBlockedAccess($client_ip, 'VPN', $user_agent);
        header('Location: https://www.youtube.com');
        exit;
    }
    
    // 5. AUTORISER TEMPORAIREMENT tout le monde pour tests
    // return; // DÉCOMMENTEZ CETTE LIGNE POUR AUTORISER TOUT LE MONDE
    
    // 6. BLOQUER les autres PC
    logBlockedAccess($client_ip, 'PC_NON_AUTORISE', $user_agent);
    header('Location: https://www.youtube.com');
    exit;
}

function logBlockedAccess($ip, $reason, $user_agent) {
    $block_log = __DIR__ . '/data/blocked_access.txt';
    $log_entry = date('Y-m-d H:i:s') . " - BLOCKED - IP: " . $ip . 
                 " - Reason: " . $reason . 
                 " - UserAgent: " . $user_agent . "\n";
    
    safeFilePutContents($block_log, $log_entry, FILE_APPEND);
}
?>
