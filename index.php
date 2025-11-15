<?php
// Vérifier que PHP fonctionne
if ($_SERVER['REQUEST_URI'] === '/') {
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Mon Site PHP</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; }
            .success { color: green; font-weight: bold; }
        </style>
    </head>
    <body>
        <h1 class='success'>✅ PHP fonctionne sur Netlify !</h1>
        <p><strong>PHP Version:</strong> " . phpversion() . "</p>
        <p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p>
        <p><strong>URL:</strong> " . $_SERVER['REQUEST_URI'] . "</p>
        
        <h2>Pages de test :</h2>
        <ul>
            <li><a href='/'>Accueil</a></li>
            <li><a href='/test.php'>Page Test</a></li>
            <li><a href='/info.php'>PHP Info</a></li>
        </ul>
    </body>
    </html>";
} else {
    // Gérer les autres routes
    http_response_code(404);
    echo "Page non trouvée: " . $_SERVER['REQUEST_URI'];
}
?>
