<?php
 // Incluimos el archivo de conexión para poder interactuar con la base de datos.
 include 'conexion.php';

 // Preparamos la consulta SQL para obtener todas las películas de la tabla 'Peliculas'.
 $sql = "SELECT id_pelicula, imagen, titulo, descripcion, duracion_minutos, genero FROM Peliculas";
 $result = $conn->query($sql);
 ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineDB - Tu portal de cine</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link rel="stylesheet" href="estilos.css">
</head>
<body>

    <header class="header">
        <div class="logo-container">
            <img src="https://placehold.co/150x50/1a1a2e/ffffff?text=CineDB" alt="Logo CineDB" class="logo">
            <span class="nombre-cine">CineDB</span>
        </div>
        <nav class="navbar">
            <a href="#inicio" class="nav-link active">Inicio</a>
            <a href="#cartelera" class="nav-link">Cartelera</a>
            <a href="#proximamente" class="nav-link">Próximamente</a>
            <a href="#contacto" class="nav-link">Contacto</a>
        </nav>
        <button class="btn-login">Iniciar Sesión</button>
    </header>

    <main>
        <section id="inicio" class="carousel-section">
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <div class="swiper-slide" style="background-image: url('https://placehold.co/1200x500/E94560/ffffff?text=Promoción+Martes+2x1');">
                        <div class="slide-content"><h2>¡Martes de 2x1!</h2><p>Disfruta de tus películas favoritas.</p></div>
                    </div>
                    <div class="swiper-slide" style="background-image: url('https://placehold.co/1200x500/16213E/ffffff?text=Estreno+del+Mes');">
                               <div class="slide-content"><h2>Estreno del Mes</h2><p>No te pierdas los blockbusters.</p></div>
                    </div>
                    <div class="swiper-slide" style="background-image: url('https://placehold.co/1200x500/0F3460/ffffff?text=Combo+Perfecto');">
                               <div class="slide-content"><h2>El Combo Perfecto</h2><p>Palomitas y refresco para una experiencia completa.</p></div>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </section>

        <section id="cartelera" class="cartelera-section">
            <h2 class="section-title">En Cartelera</h2>
            <div class="cartelera-grid">
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                ?>
                        <div class="movie-card">
                            <div class="movie-image-container">
                                <img src="<?php echo htmlspecialchars($row['imagen'] ? $row['imagen'] : 'https://placehold.co/300x450/1a1a2e/ffffff?text=Poster'); ?>" alt="Poster de <?php echo htmlspecialchars($row['titulo']); ?>" class="movie-image">
                            </div>
                            <div class="movie-info">
                                <h3 class="movie-title"><?php echo htmlspecialchars($row['titulo']); ?></h3>
                                <p class="movie-description"><?php echo htmlspecialchars($row['descripcion']); ?></p>
                                <div class="movie-details">
                                    <span class="movie-genre"><?php echo htmlspecialchars($row['genero']); ?></span>
                                    <span class="movie-duration"><?php echo htmlspecialchars($row['duracion_minutos']); ?> min</span>
                                </div>
                                <button class="btn-comprar" data-idpelicula="<?php echo $row['id_pelicula']; ?>" data-titulo="<?php echo htmlspecialchars($row['titulo']); ?>" type="button">Comprar Boletos</button>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo "<p>No hay películas en cartelera.</p>";
                }
                ?>
            </div>
        </section>
    </main>

    <div id="compra-modal" class="modal-overlay" style="display:none;">
        <div class="modal-content">
            <button class="modal-close-btn">&times;</button>
            <h2 id="modal-movie-title">Comprar Boletos</h2>
            <form id="compra-form">
                <input type="hidden" id="modal-id-pelicula" name="id_pelicula">

                <div class="form-step">
                    <div class="form-group">
                        <label for="cine-select">1. Selecciona un cine:</label>
                        <select id="cine-select" name="id_cine" class="form-control" disabled>
                            <option value="">Cargando cines...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="funcion-select">2. Selecciona un horario:</label>
                        <select id="funcion-select" name="id_funcion" class="form-control" disabled>
                            <option value="">Selecciona un cine primero</option>
                        </select>
                    </div>
                </div>

                <div id="asientos-container" class="form-step" style="display:none;">
                    <label>3. Selecciona tus asientos:</label>
                    <div class="leyenda-asientos">
                        <span><span class="asiento-ejemplo disponible"></span> Disponible</span>
                        <span><span class="asiento-ejemplo ocupado"></span> Ocupado</span>
                        <span><span class="asiento-ejemplo seleccionado"></span> Seleccionado</span>
                    </div>
                    <div class="pantalla-cine">PANTALLA</div>
                    <div id="asientos-mapa" class="asientos-mapa">
                        </div>
                </div>

                <div id="pago-container" class="form-step" style="display:none;">
                    <label>4. Elige tu método de pago:</label>
                    <div class="pago-opciones">
                        <input type="radio" id="pago-tarjeta" name="tipo_pago" value="Tarjeta" checked>
                        <label for="pago-tarjeta">Tarjeta</label>
                        <input type="radio" id="pago-efectivo" name="tipo_pago" value="Efectivo">
                        <label for="pago-efectivo">Efectivo</label>
                    </div>
                    <div id="tarjeta-form" class="form-group-grid">
                        <input type="text" name="tarjeta_num" placeholder="Número de Tarjeta (16 dígitos)" class="form-control" required>
                        <input type="text" name="tarjeta_nombre" placeholder="Nombre en la Tarjeta" class="form-control" required>
                        <input type="text" name="tarjeta_exp" placeholder="MM/AA" class="form-control" required>
                        <input type="text" name="tarjeta_cvv" placeholder="CVV" class="form-control" required>
                    </div>
                    <div id="efectivo-msg" style="display:none;">
                        <p>Paga en la caja del cine antes de la función. Muestra tu folio de compra.</p>
                    </div>
                </div>

                <div class="modal-footer">
                        <p>Total: <span id="precio-total">$0.00</span></p>
                    <button type="submit" id="btn-completar-compra" class="btn-comprar" disabled>Completar Compra</button>
                </div>
            </form>
        </div>
    </div>

    <div id="ticket-modal" class="modal-overlay" style="display:none;">
        <div class="modal-content ticket-content">
            <button class="modal-close-btn ticket-close-btn">&times;</button>
            <div id="ticket-info">
                </div>
            <div class="modal-footer">
                <button id="btn-descargar-ticket" class="btn-comprar" type="button"><i class="fas fa-download"></i> Descargar</button>
            </div>
        </div>
    </div>

    <div id="login-modal" class="modal-overlay" style="display:none;">
        <div class="modal-content">
            <button class="modal-close-btn">&times;</button>
            <h2>Iniciar Sesión</h2>
            <form id="login-form">
                <div class="form-group">
                    <label for="login-username">Usuario o Email:</label>
                    <input type="text" id="login-username" name="nombre_usuario" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Contraseña:</label>
                    <input type="password" id="login-password" name="contrasena" class="form-control" required>
                </div>
                <p id="login-message" class="error-message"></p>
                <button type="submit" class="btn-ingresar">Ingresar</button>
                <p class="mt-3">¿No tienes cuenta? <a href="#" id="show-register-modal">Regístrate aquí</a></p>
            </form>
        </div>
    </div>

    <div id="register-modal" class="modal-overlay" style="display:none;">
        <div class="modal-content">
            <button class="modal-close-btn">&times;</button>
            <h2>Registrarse</h2>
            <form id="register-form">
                <div class="form-group">
                    <label for="register-username">Nombre de Usuario:</label>
                    <input type="text" id="register-username" name="nombre_usuario" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="register-email">Email:</label>
                    <input type="email" id="register-email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="register-password">Contraseña:</label>
                    <input type="password" id="register-password" name="contrasena" class="form-control" required>
                </div>
                <p id="register-message" class="error-message"></p>
                <button type="submit" class="btn-comprar">Registrarse</button>
                <p class="mt-3">¿Ya tienes cuenta? <a href="#" id="show-login-modal">Inicia sesión</a></p>
            </form>
        </div>
    </div>

    <div id="user-options-modal" class="modal">
    <div class="modal-content">
        <span class="modal-close-btn">&times;</span>
        <h2>Opciones de Usuario</h2>
        <div class="modal-body">
            <button id="btn-logout" class="btn btn-primary" type="button">Cerrar Sesión</button>
            <button id="btn-switch-account" class="btn btn-secondary" type="button">Cambiar Cuenta</button>
        </div>
    </div>
</div>

    <?php $conn->close(); ?>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="scripts.js"></script>
</body>
</html>