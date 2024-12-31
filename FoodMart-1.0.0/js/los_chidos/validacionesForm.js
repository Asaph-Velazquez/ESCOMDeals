const validaciones = {
    nombres: {
        regex: /^[a-zA-ZáéíóúÁÉÍÓÚ\s]{3,30}$/,
        mensaje: "El nombre debe tener entre 3 y 30 caracteres"
    },
    apellidoPaterno: {
        regex: /^[a-zA-ZáéíóúÁÉÍÓÚ\s]{3,30}$/,
        mensaje: "El apellido debe tener entre 3 y 30 caracteres"
    },
    apellidoMaterno: {
        regex: /^[a-zA-ZáéíóúÁÉÍÓÚ\s]{3,30}$/,
        mensaje: "El apellido debe tener entre 3 y 30 caracteres"
    },
    numeroTelefono: {
        regex: /^[0-9]{10}$/,
        mensaje: "El teléfono debe tener exactamente 10 dígitos"
    },
    correoElectronico: {
        regex: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
        mensaje: "El correo debe ser válido"
    },
    usuario: {
        regex: /^.{4,}$/,
        mensaje: "El usuario debe tener al menos 4 caracteres"
    },
    contraseña: {
        regex: /^.{8,}$/,
        mensaje: "La contraseña debe tener al menos 8 caracteres"
    },

};

function validarCampo(campo) {
    // Ignorar validación para campos ocultos o de tipo archivo
    if (campo.type === 'file' || !campo.offsetParent || campo.style.display === 'none') {
        return true;
    }

    const tipo = campo.id;
    const valor = campo.value.trim();
    const validacion = validaciones[tipo];

    if (!validacion) {
        return true; // Si no hay validación definida, el campo es válido
    }

    const esValido = validacion.regex.test(valor);
    const errorDiv = campo.nextElementSibling;

    if (esValido) {
        campo.classList.remove('is-invalid');
        campo.classList.add('is-valid');
        errorDiv.textContent = '';
    } else {
        campo.classList.remove('is-valid');
        campo.classList.add('is-invalid');
        errorDiv.textContent = validacion.mensaje;
    }
    return esValido;
}

function validarFormulario(event) {
    event.preventDefault(); // Bloquea el envío tradicional del formulario

    const form = event.target; // El formulario que se está enviando
    const formData = new FormData(form); // Crear un objeto FormData con los datos del formulario

    fetch('../FoodMart-1.0.0/php/registro.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Parsear la respuesta como JSON
    .then(data => {
        if (data.success) {
            // Muestra mensaje de éxito y redirige después de 2 segundos
            mostrarModal("¡Registro exitoso!");
            setTimeout(() => {
                window.location.href = 'pagina-principal.html'; // Cambia esta URL según tus necesidades
            }, 2000);
        } else {
            // Muestra el mensaje de error enviado desde PHP
            mostrarModal(data.message, false);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarModal("Ocurrió un error en el servidor. Inténtalo más tarde.", false);
    });
}



document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registroForm');
    form.addEventListener('submit', validarFormulario);

    const inputs = document.querySelectorAll('#registroForm input');
    inputs.forEach(input => {
        input.addEventListener('input', () => validarCampo(input));
    });


    

});

document.addEventListener('DOMContentLoaded', function () {
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('contraseña');
    
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const confirmPassword = document.getElementById('confirmarContraseña');

    togglePassword.addEventListener('click', function () {
        const type = password.type === 'password' ? 'text' : 'password';
        password.type = type;
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });

    toggleConfirmPassword.addEventListener('click', function () {
        const type = confirmPassword.type === 'password' ? 'text' : 'password';
        confirmPassword.type = type;
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });
});

