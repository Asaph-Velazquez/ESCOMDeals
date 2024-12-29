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
        regex: /^[a-zA-Z0-9._%+-]+@alumno\.ipn\.mx$/,
        mensaje: "El correo debe ser del dominio alumno.ipn.mx"
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
    event.preventDefault();
    const campos = document.querySelectorAll('#registroForm input');
    let esValido = true;

    campos.forEach(campo => {
        if (!validarCampo(campo)) {
            esValido = false;
        }
    });

    const password = document.getElementById('contraseña')?.value.trim();
    const confirmarPassword = document.getElementById('confirmarContraseña')?.value.trim();
    const errorDivConfirmar = document.getElementById('confirmarContraseña')?.nextElementSibling;

    if (password !== confirmarPassword) {
        esValido = false;
        if (errorDivConfirmar) errorDivConfirmar.textContent = "Las contraseñas no coinciden.";
        document.getElementById('confirmarContraseña').classList.add('is-invalid');
    } else {
        if (errorDivConfirmar) errorDivConfirmar.textContent = "";
        document.getElementById('confirmarContraseña').classList.remove('is-invalid');
        document.getElementById('confirmarContraseña').classList.add('is-valid');
    }

    if (esValido) {
        mostrarModal("¡Registro exitoso!");
        setTimeout(() => enviarFormulario(), 2000);
    } else {
        mostrarModal("Por favor corrige los errores en el formulario.", false);
    }
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

