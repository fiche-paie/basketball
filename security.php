<?php
// === FONCTIONS DE SÉCURITÉ ===

function isVPNorProxy($ip) {
    // Liste des fournisseurs VPN connus
    $vpn_providers = [
        'vpn', 'proxy', 'tor', 'anonymous', 'hide', 'expressvpn',
        'nordvpn', 'surfshark', 'cyberghost', 'private', 'airvpn',
        'purevpn', 'ipvanish', 'vyprvpn', 'hotspot', 'windscribe',
        'protonvpn', 'tunnelbear', 'hidemyass', 'zenmate'
    ];
    
    // Faire une requête API pour vérifier si c'est un VPN
    $api_url = "http://ip-api.com/json/{$ip}?fields=66846719";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if ($data && $data['status'] === 'success') {
        // Vérifier si c'est un hosting/VPN
        if ($data['hosting'] === true || $data['proxy'] === true) {
            return true;
        }
        
        // Vérifier le fournisseur
        $isp = strtolower($data['isp'] ?? '');
        $org = strtolower($data['org'] ?? '');
        $as = strtolower($data['as'] ?? '');
        
        foreach ($vpn_providers as $provider) {
            if (strpos($isp, $provider) !== false || 
                strpos($org, $provider) !== false || 
                strpos($as, $provider) !== false) {
                return true;
            }
        }
    }
    
    return false;
}

function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function getUserID() {
    return md5(getUserIP());
}

function safeFilePutContents($filename, $data, $flags = 0) {
    $dir = dirname($filename);
    
    if (!file_exists($dir)) {
        @mkdir($dir, 0755, true);
    }
    
    $result = @file_put_contents($filename, $data, $flags);
    
    if ($result === false) {
        error_log("Impossible d'écrire dans le fichier: " . $filename);
    }
    
    return $result;
}
?>
