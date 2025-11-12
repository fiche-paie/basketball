
<?php
// Pie de página reutilizable para todas las páginas
?>
<footer class="site-footer">
    <div class="footer-content">
        <div class="footer-logo">
            <img src="https://i.ibb.co/jP4NGJnh/T-l-chargement-2.jpg" alt="Logo" class="footer-logo-img">
        </div>
        <div class="footer-links">
            <div class="footer-section">
                <h4>Tienda</h4>
                <a href="https://www.nike.com/es/" target="_blank" rel="noopener noreferrer">Nueva Colección</a>
                <a href="https://www.adidas.es/" target="_blank" rel="noopener noreferrer">Promociones</a>
                <a href="https://www.jdsports.es/" target="_blank" rel="noopener noreferrer">Novedades</a>
            </div>
            <div class="footer-section">
                <h4>Ayuda</h4>
                <a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer">Contacto</a>
                <a href="https://faq.example.com" target="_blank" rel="noopener noreferrer">FAQ</a>
                <a href="https://www.dhl.es/" target="_blank" rel="noopener noreferrer">Entrega</a>
            </div>
            <div class="footer-section">
                <h4>Legal</h4>
                <a href="https://www.termsandconditions.com/" target="_blank" rel="noopener noreferrer">Términos</a>
                <a href="https://www.privacypolicies.com/" target="_blank" rel="noopener noreferrer">Privacidad</a>
                <a href="https://www.legaladvice.org/" target="_blank" rel="noopener noreferrer">Aviso legal</a>
            </div>
            <div class="footer-section">
                <h4>Redes Sociales</h4>
                <a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer">Instagram</a>
                <a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer">Facebook</a>
                <a href="https://www.tiktok.com/" target="_blank" rel="noopener noreferrer">TikTok</a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2024 Tienda de Baloncesto.</p>
    </div>
</footer>

<style>
.site-footer {
    background: #177ECF;
    color: #ffffff;
    padding: 30px 20px 20px;
    width: 100%;
    margin-top: auto; /* Esta propiedad es importante */
    box-sizing: border-box;
    flex-shrink: 0;
}

.footer-content {
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 25px;
}

.footer-logo-img {
    width: 200px;
    height: 40px;
    border-radius: 3px;
    object-fit: cover;
}

.footer-links {
    display: flex;
    justify-content: center;
    gap: 40px;
    flex-wrap: wrap;
}

.footer-section {
    text-align: center;
    min-width: 120px;
}

.footer-section h4 {
    color: #f7d148;
    margin-bottom: 12px;
    font-size: 1.1em;
}

.footer-section a {
    display: block;
    color: #ffffff;
    text-decoration: none;
    margin-bottom: 8px;
    transition: color 0.3s;
    font-size: 1em;
}

.footer-section a:hover {
    color: #000;
    text-decoration: underline;
}

.footer-bottom {
    text-align: center;
    margin-top: 25px;
    padding-top: 30px;
    border-top: 1px solid #ffffff;
    color: #ffffff;
    font-size: 0.9em;
    width: 100%;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
}

.footer-bottom p {
    color: #ffffff !important;
    margin: 0;
}

@media (max-width: 768px) {
    .footer-links {
        flex-direction: column;
        gap: 25px;
    }
    
    .footer-section {
        text-align: center;
    }
    
    .footer-logo-img {
        width: 200px;
        height: 40px;
    }
    
    .site-footer {
        padding: 25px 15px 15px;
    }
}

@media (max-width: 480px) {
    .site-footer {
        padding: 20px 10px 10px;
    }
    
    .footer-links {
        gap: 20px;
    }
}
</style>

