<?php
// ------------------------------
// CONFIGURATION
// ------------------------------
$allowed_country = 'FR'; // ISO 2-letter country code, e.g., 'FR' for France

// Optional: list of IPs/ranges to always block (e.g., proxies)
$blocked_ips = [
    '123.45.67.89',
    // '192.168.0.0/24', // Can add CIDR ranges if needed
];

// ------------------------------
// HELPER FUNCTIONS
// ------------------------------

// Get real visitor IP (considering proxies)
function getVisitorIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

// Simple IP block check
function isBlockedIP($ip, $blocked_ips) {
    return in_array($ip, $blocked_ips);
}

// Get country code using a free API (ipinfo.io)
function getCountryCode($ip) {
    $url = "https://ipinfo.io/{$ip}/country";
    $country = @file_get_contents($url);
    if ($country) {
        return trim($country);
    }
    return null;
}

// ------------------------------
// MAIN LOGIC
// ------------------------------
$visitor_ip = getVisitorIP();

// Block if IP is in blocked list
if (isBlockedIP($visitor_ip, $blocked_ips)) {
    header('Location: https://www.youtube.com');
    exit();
}

// Get country
$visitor_country = getCountryCode($visitor_ip);

// Block if country not allowed
if ($visitor_country !== $allowed_country) {
    header('Location: https://www.youtube.com');
    exit();
}

// If we reach here, the visitor is allowed
// You can continue loading the page normally
?> 
