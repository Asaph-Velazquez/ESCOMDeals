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
        mensaje: "El correo debe contener un @ y un dominio"
    },
    usuario: {
        regex: /^.{4,}$/,
        mensaje: "El usuario debe tener al menos 4 caracteres"
    },
    contraseña: {
        regex: /^.{8,}$/,
        mensaje: "La contraseña debe tener al menos 8 caracteres"
    }
};

function validarCampo(campo) {
    const tipo = campo.id;
    const validacion = validaciones[tipo];
    const valor = campo.value;  // Captura el valor del campo
    const errorDiv = campo.nextElementSibling;

    if (!validacion) {
        return true; // Si el campo no tiene validación, asumir válido
    }

    const esValido = validacion.regex.test(valor);

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
    event.preventDefault(); // Prevenir el envío del formulario
    const campos = document.querySelectorAll('#registroForm input');
    let esValido = true;

    campos.forEach(campo => {
        if (!validarCampo(campo)) {
            esValido = false;
        }
    });

    // Validación de confirmación de contraseña
    const password = document.getElementById('contraseña').value;
    const confirmarPassword = document.getElementById('confirmarContraseña').value;
    const errorDivConfirmar = document.getElementById('confirmarContraseña').nextElementSibling;

    if (password !== confirmarPassword) {
        esValido = false;
        errorDivConfirmar.textContent = "Las contraseñas no coinciden.";
        document.getElementById('confirmarContraseña').classList.add('is-invalid');
    } else {
        errorDivConfirmar.textContent = "";
        document.getElementById('confirmarContraseña').classList.remove('is-invalid');
    }

    if (esValido) {
        alert("¡Registro exitoso!");
        document.getElementById('registroForm').submit(); // Envía el formulario
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('#registroForm input');
    inputs.forEach(input => {
        input.addEventListener('input', () => validarCampo(input));
    });
    document.getElementById('registroForm').addEventListener('submit', validarFormulario);
});