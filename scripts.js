document.addEventListener('DOMContentLoaded', function () {
    // --- INICIALIZACIÓN DE SWIPER ---
    const swiper = new Swiper('.swiper-container', {
        effect: 'fade',
        loop: true,
        pagination: { el: '.swiper-pagination', clickable: true },
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        autoplay: { delay: 5000, disableOnInteraction: false },
    });

    // --- VARIABLES DEL MODAL DE COMPRA ---
    const compraModal = document.getElementById('compra-modal');
    const ticketModal = document.getElementById('ticket-modal');
    const closeModalBtns = document.querySelectorAll('.modal-close-btn');
    // Este selector sigue siendo correcto, ya que el botón de login ahora tiene una clase diferente
    const comprarBtns = document.querySelectorAll('.btn-comprar'); // Botones de "Comprar Boletos" en las tarjetas de película

    const modalTitle = document.getElementById('modal-movie-title');
    const modalIdPelicula = document.getElementById('modal-id-pelicula');

    const cineSelect = document.getElementById('cine-select');
    const funcionSelect = document.getElementById('funcion-select');

    const asientosContainer = document.getElementById('asientos-container');
    const asientosMapa = document.getElementById('asientos-mapa');

    const pagoContainer = document.getElementById('pago-container');
    const pagoOpciones = document.querySelectorAll('input[name="tipo_pago"]');
    const tarjetaForm = document.getElementById('tarjeta-form');
    const efectivoMsg = document.getElementById('efectivo-msg');

    const precioTotalEl = document.getElementById('precio-total');
    const btnCompletarCompra = document.getElementById('btn-completar-compra'); // Botón "Completar Compra" dentro del modal de compra
    const compraForm = document.getElementById('compra-form');

    let precioPorBoleto = 0;
    let asientosSeleccionados = [];

    // --- VARIABLES PARA AUTENTICACIÓN ---
    const btnLoginHeader = document.querySelector('.btn-login'); // Botón "Iniciar Sesión" en el header
    const loginModal = document.getElementById('login-modal');
    const registerModal = document.getElementById('register-modal');
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const loginMessage = document.getElementById('login-message');
    const registerMessage = document.getElementById('register-message');
    const showRegisterModalLink = document.getElementById('show-register-modal');
    const showLoginModalLink = document.getElementById('show-login-modal');

    // --- VARIABLES PARA EL MODAL DE OPCIONES DE USUARIO ---
    const userOptionsModal = document.getElementById('user-options-modal');
    const btnLogout = document.getElementById('btn-logout');
    const btnSwitchAccount = document.getElementById('btn-switch-account');
    const closeUserOptionsModalBtn = userOptionsModal ? userOptionsModal.querySelector('.modal-close-btn') : null;


    // --- LÓGICA PARA ABRIR Y CERRAR MODALES ---

    // Abre el modal de compra cuando se hace clic en un botón "Comprar Boletos" de una película
    comprarBtns.forEach(btn => {
        // Asegurarse de que no sea el botón "Completar Compra" dentro del propio modal
        if (!btn.closest('#compra-modal')) {
            btn.addEventListener('click', function() {
                const idPelicula = this.dataset.idpelicula;
                const titulo = this.dataset.titulo;

                resetModalCompra(); // Reinicia el formulario del modal de compra
                modalTitle.textContent = `Comprar Boletos para: ${titulo}`;
                modalIdPelicula.value = idPelicula;

                cargarCines(idPelicula);
                compraModal.style.display = 'flex'; // Muestra el modal de compra
            });
        }
    });

    // Cierra cualquier modal que tenga un botón con la clase .modal-close-btn
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            compraModal.style.display = 'none';
            ticketModal.style.display = 'none';
            loginModal.style.display = 'none';
            registerModal.style.display = 'none';
            if (userOptionsModal) userOptionsModal.style.display = 'none';
        });
    });

    // Controla el botón "Iniciar Sesión" / "Nombre de Usuario" en el header
    btnLoginHeader.addEventListener('click', () => {
        if (btnLoginHeader.dataset.loggedIn === 'true') {
            // Si el usuario está logueado, abre el modal de opciones de usuario
            if (userOptionsModal) {
                userOptionsModal.style.display = 'flex';
            }
        } else {
            // Si no está logueado, abre el modal de login
            loginMessage.textContent = ''; // Limpia mensajes anteriores
            loginForm.reset(); // Reinicia el formulario de login
            loginModal.style.display = 'flex'; // Muestra el modal de login
        }
    });

    // Cierre del modal de opciones de usuario
    if (closeUserOptionsModalBtn) {
        closeUserOptionsModalBtn.addEventListener('click', () => {
            if (userOptionsModal) userOptionsModal.style.display = 'none';
        });
    }

    // Lógica para el botón "Cerrar Sesión" dentro del modal de opciones de usuario
    if (btnLogout) {
        btnLogout.addEventListener('click', () => {
            if (userOptionsModal) userOptionsModal.style.display = 'none';
            window.location.href = 'logout.php'; // Redirige para cerrar la sesión
        });
    }

    // Lógica para el botón "Cambiar Cuenta" dentro del modal de opciones de usuario
    if (btnSwitchAccount) {
        btnSwitchAccount.addEventListener('click', () => {
            if (userOptionsModal) userOptionsModal.style.display = 'none';
            fetch('logout.php') // Cierra la sesión actual (sin recargar la página)
                .then(() => {
                    loginMessage.textContent = '';
                    loginForm.reset();
                    loginModal.style.display = 'flex'; // Muestra el modal de login para una nueva cuenta
                })
                .catch(error => {
                    console.error('Error al cambiar de cuenta:', error);
                    alert('Hubo un error al intentar cambiar de cuenta. Por favor, inténtalo de nuevo.');
                });
        });
    }

    // Navegación entre modal de login y registro
    showRegisterModalLink.addEventListener('click', (e) => {
        e.preventDefault();
        loginModal.style.display = 'none';
        registerMessage.textContent = '';
        registerForm.reset();
        registerModal.style.display = 'flex';
    });

    showLoginModalLink.addEventListener('click', (e) => {
        e.preventDefault();
        registerModal.style.display = 'none';
        loginMessage.textContent = '';
        loginForm.reset();
        loginModal.style.display = 'flex';
    });


    // --- LÓGICA DE REGISTRO ---
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        registerMessage.textContent = '';

        fetch('register.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message); // Mantener el alert aquí ya que es un flujo menos crítico
                registerModal.style.display = 'none';
                loginForm.reset();
                loginModal.style.display = 'flex';
            } else {
                registerMessage.textContent = data.message;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            registerMessage.textContent = 'Ocurrió un error al registrarse.';
        });
    });

    // --- LÓGICA DE INICIO DE SESIÓN (Punto Crítico) ---
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Impide el envío estándar del formulario
        const formData = new FormData(this);

        loginMessage.textContent = ''; // Limpia cualquier mensaje de error anterior

        fetch('login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // *** AJUSTE CLAVE AQUÍ: Retraso y eliminación de alert ***
                // Introduce un pequeño retraso para asegurar que el DOM se estabilice
                // y para evitar posibles "clics fantasmas" después de cerrar el modal.
                setTimeout(() => {
                    loginModal.style.display = 'none'; // Oculta el modal de login
                    updateLoginButton(data.user_name); // Actualiza el botón del header
                    // Elimina el alert para evitar posibles interacciones no deseadas
                    console.log('Inicio de sesión exitoso:', data.message);

                    // Asegúrate de que el foco se mueva a un elemento seguro si es necesario
                    // Esto ayuda a prevenir activaciones accidentales por teclado
                    document.activeElement.blur(); // Quita el foco del botón "Ingresar"
                    btnLoginHeader.focus(); // Mueve el foco al botón de login del header
                }, 100); // Pequeño retraso de 100 milisegundos

            } else {
                loginMessage.textContent = data.message; // Muestra mensaje de error si el login falla
            }
        })
        .catch(error => {
            console.error('Error:', error);
            loginMessage.textContent = 'Ocurrió un error al iniciar sesión.';
        });
    });

    // --- Función para actualizar el botón de login/logout ---
    function updateLoginButton(username) {
        if (username) {
            btnLoginHeader.textContent = username;
            btnLoginHeader.classList.add('logged-in');
            btnLoginHeader.dataset.loggedIn = 'true';
        } else {
            btnLoginHeader.textContent = 'Iniciar Sesión';
            btnLoginHeader.classList.remove('logged-in');
            btnLoginHeader.dataset.loggedIn = 'false';
        }
    }

    // --- Verificar estado de sesión al cargar la página ---
    // Esta función solo actualiza el estado del botón, no debe abrir modales.
    function checkLoginStatus() {
        fetch('get_user_status.php')
            .then(response => response.json())
            .then(data => {
                if (data.logged_in) {
                    updateLoginButton(data.user_name);
                } else {
                    updateLoginButton(null);
                }
            })
            .catch(error => {
                console.error('Error al verificar estado de sesión:', error);
                updateLoginButton(null);
            });
    }

    checkLoginStatus(); // Se ejecuta al cargar la página


    // --- LÓGICA DEL FORMULARIO DE COMPRA (EXISTENTE Y REVISADA) ---

    function cargarCines(idPelicula) {
        fetch(`get_data.php?action=funciones&id_pelicula=${idPelicula}`)
            .then(response => response.json())
            .then(data => {
                cineSelect.innerHTML = '<option value="">Selecciona un cine</option>';
                if (data.cines) {
                    data.cines.forEach(cine => {
                        const option = document.createElement('option');
                        option.value = cine.id_cine;
                        option.textContent = cine.nombre;
                        cineSelect.appendChild(option);
                    });
                    cineSelect.disabled = false;
                    cineSelect.dataset.funciones = JSON.stringify(data.funciones);
                } else {
                    cineSelect.innerHTML = '<option value="">No hay cines disponibles</option>';
                }
            });
    }

    cineSelect.addEventListener('change', () => {
        const idCine = cineSelect.value;
        const funciones = JSON.parse(cineSelect.dataset.funciones || '{}');

        funcionSelect.innerHTML = '<option value="">Selecciona un horario</option>';
        asientosContainer.style.display = 'none';

        if (idCine && funciones[idCine]) {
            funciones[idCine].forEach(funcion => {
                const option = document.createElement('option');
                option.value = funcion.id_funcion;
                option.dataset.precio = funcion.precio;
                option.textContent = new Date(funcion.fecha_hora).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                funcionSelect.appendChild(option);
            });
            funcionSelect.disabled = false;
        } else {
            funcionSelect.disabled = true;
        }
        actualizarPrecioTotal();
    });

    // Cargar Mapa de Asientos
    funcionSelect.addEventListener('change', () => {
        const idFuncion = funcionSelect.value;
        const selectedOption = funcionSelect.options[funcionSelect.selectedIndex];

        if (idFuncion) {
            precioPorBoleto = parseFloat(selectedOption.dataset.precio);
            cargarAsientos(idFuncion);
            asientosContainer.style.display = 'block';
        } else {
            asientosContainer.style.display = 'none';
            precioPorBoleto = 0;
        }
        actualizarPrecioTotal();
    });

    function cargarAsientos(idFuncion) {
        fetch(`get_data.php?action=asientos&id_funcion=${idFuncion}`)
            .then(response => response.json())
            .then(data => {
                asientosMapa.innerHTML = '';
                if (data.asientos) {
                    data.asientos.forEach(asiento => {
                        const asientoDiv = document.createElement('div');
                        asientoDiv.classList.add('asiento');
                        asientoDiv.dataset.idAsiento = asiento.id_asiento;
                        if (asiento.ocupado) {
                            asientoDiv.classList.add('ocupado');
                        } else {
                            asientoDiv.classList.add('disponible');
                            asientoDiv.addEventListener('click', toggleSeleccionAsiento);
                        }
                        asientosMapa.appendChild(asientoDiv);
                    });
                }
            });
    }

    function toggleSeleccionAsiento(e) {
        const asiento = e.target;
        const idAsiento = asiento.dataset.idAsiento;

        if (asiento.classList.contains('seleccionado')) {
            asiento.classList.remove('seleccionado');
            asientosSeleccionados = asientosSeleccionados.filter(id => id !== idAsiento);
        } else {
            asiento.classList.add('seleccionado');
            asientosSeleccionados.push(idAsiento);
        }
        actualizarPrecioTotal();
    }

    function actualizarPrecioTotal() {
        const total = asientosSeleccionados.length * precioPorBoleto;
        precioTotalEl.textContent = `$${total.toFixed(2)}`;

        if (asientosSeleccionados.length > 0) {
            pagoContainer.style.display = 'block';
            btnCompletarCompra.disabled = false;
        } else {
            pagoContainer.style.display = 'none';
            btnCompletarCompra.disabled = true;
        }
    }

    // Lógica de Pago
    pagoOpciones.forEach(opcion => {
        opcion.addEventListener('change', () => {
            if (opcion.value === 'Tarjeta') {
                tarjetaForm.style.display = 'grid';
                efectivoMsg.style.display = 'none';
                setRequired(tarjetaForm, true);
            } else {
                tarjetaForm.style.display = 'none';
                efectivoMsg.style.display = 'block';
                setRequired(tarjetaForm, false);
            }
        });
    });

    function setRequired(form, isRequired) {
        form.querySelectorAll('input').forEach(input => {
            input.required = isRequired;
        });
    }

    // Enviar formulario de compra
    compraForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Verificar si el usuario está logueado antes de permitir la compra
        if (btnLoginHeader.dataset.loggedIn !== 'true') {
            alert('Debes iniciar sesión para completar la compra.');
            compraModal.style.display = 'none';
            loginModal.style.display = 'flex';
            return;
        }

        const formData = new FormData(this);
        formData.append('asientos', JSON.stringify(asientosSeleccionados));
        formData.append('precio_total', (asientosSeleccionados.length * precioPorBoleto).toFixed(2));

        // Simulación de validación de tarjeta
        if (formData.get('tipo_pago') === 'Tarjeta') {
            const numTarjeta = formData.get('tarjeta_num');
            if (!/^\d{16}$/.test(numTarjeta)) {
                alert('Número de tarjeta inválido. Deben ser 16 dígitos.');
                return;
            }
        }

        btnCompletarCompra.textContent = 'Procesando...';
        btnCompletarCompra.disabled = true;

        fetch('procesar_compra.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                compraModal.style.display = 'none';
                mostrarTicket(data.ticket);
                cargarAsientos(funcionSelect.value);
            } else {
                alert('Error al procesar la compra: ' + data.message);
            }
        })
        .finally(() => {
            btnCompletarCompra.textContent = 'Completar Compra';
            btnCompletarCompra.disabled = false;
        });
    });

    // --- LÓGICA DEL TICKET ---
    function mostrarTicket(ticket) {
        const ticketInfo = document.getElementById('ticket-info');
        ticketInfo.innerHTML = `
            <h2>¡Compra Exitosa!</h2>
            <p><strong>Folio:</strong> ${ticket.folio}</p>
            <p><strong>Película:</strong> ${ticket.titulo}</p>
            <p><strong>Cine:</strong> ${ticket.cine}</p>
            <p><strong>Sala:</strong> ${ticket.sala}</p>
            <p><strong>Fecha y Hora:</strong> ${new Date(ticket.fecha_hora).toLocaleString()}</p>
            <p><strong>Asientos:</strong> ${ticket.asientos}</p>
            <p><strong>Total Pagado:</strong> $${ticket.total}</p>
            <p><strong>Método de Pago:</strong> ${ticket.pago}</p>
        `;
        ticketModal.style.display = 'flex';
    }

    document.getElementById('btn-descargar-ticket').addEventListener('click', function() {
        const ticketContent = document.getElementById('ticket-info');
        html2canvas(ticketContent).then(canvas => {
            const link = document.createElement('a');
            link.download = 'ticket-cine.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    });

    // Función para resetear el modal de compra a su estado inicial
    function resetModalCompra() {
        compraForm.reset();
        cineSelect.innerHTML = '<option value="">Cargando cines...</option>';
        cineSelect.disabled = true;
        funcionSelect.innerHTML = '<option value="">Selecciona un cine primero</option>';
        funcionSelect.disabled = true;
        asientosContainer.style.display = 'none';
        asientosMapa.innerHTML = '';
        pagoContainer.style.display = 'none';
        tarjetaForm.style.display = 'grid'; // Por defecto a tarjeta
        efectivoMsg.style.display = 'none';
        document.getElementById('pago-tarjeta').checked = true;
        setRequired(tarjetaForm, true);
        precioPorBoleto = 0;
        asientosSeleccionados = [];
        actualizarPrecioTotal(); // Para resetear el total a $0.00 y deshabilitar el botón de compra
    }

}); // Fin de DOMContentLoaded