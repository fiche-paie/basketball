<?php
// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

// Inclure tous les modules


require_once 'security.php';
require_once 'antibot.php';
require_once 'access_control.php';
require_once 'telegram_functions.php';
require_once 'user_management.php';
require_once 'data_capture.php';



?>
