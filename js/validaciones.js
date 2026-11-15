// Validacion basica para el formulario de contacto
function validarContacto() {
    let nombre = document.getElementById("nombre").value.trim();
    let correo = document.getElementById("correo").value.trim();
    let mensaje = document.getElementById("mensaje").value.trim();

    if (nombre === "" || correo === "" || mensaje === "") {
        alert("Por favor llena todos los campos antes de enviar tu mensaje.");
        return false;
    }
    return true;
}

// Validacion para el formulario de login
function validarLogin() {
    let correo = document.getElementById("correo").value.trim();
    let password = document.getElementById("password").value.trim();

    if (correo === "" || password === "") {
        alert("Escribe tu correo y contraseña para entrar.");
        return false;
    }
    return true;
}

// Ventana de confirmacion antes de eliminar registros (Pantalla 6 y 7)
function confirmarEliminacion(tipo) {
    return confirm("¿Seguro que quieres eliminar este " + tipo + "? Esta accion no se puede deshacer.");
}

// Filtro en tiempo real para la tabla del catalogo (Pantalla 5)
function filtrarCatalogo() {
    let input = document.getElementById("buscador").value.toLowerCase();
    let filas = document.querySelectorAll("#tabla-productos tbody tr");

    filas.forEach(function(fila) {
        let textoFila = fila.textContent.toLowerCase();
        if (textoFila.includes(input)) {
            fila.style.display = "";
        } else {
            fila.style.display = "none";
        }
    });
}