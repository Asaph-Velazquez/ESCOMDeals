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
    }
};

function validarCampo(campo) {
    if (campo.type === 'file' || !campo.offsetParent || campo.style.display === 'none') {
        return true;
    }

    const tipo = campo.id;
    const valor = campo.value.trim();
    const validacion = validaciones[tipo];

    if (!validacion) {
        return true; 
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

function validarConfirmacionContraseña() {
    const contraseña = document.getElementById('contraseña').value.trim();
    const confirmarContraseña = document.getElementById('confirmarContraseña').value.trim();
    const errorDiv = document.getElementById('confirmarContraseña').nextElementSibling;

    if (contraseña !== confirmarContraseña) {
        document.getElementById('confirmarContraseña').classList.remove('is-valid');
        document.getElementById('confirmarContraseña').classList.add('is-invalid');
        errorDiv.textContent = "Las contraseñas no coinciden";
        return false;
    } else {
        document.getElementById('confirmarContraseña').classList.remove('is-invalid');
        document.getElementById('confirmarContraseña').classList.add('is-valid');
        errorDiv.textContent = '';
        return true;
    }
}

function validarFormulario(event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);

    if (!validarConfirmacionContraseña()) {
        return;
    }

    fetch('../FoodMart-1.0.0/php/registro.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarModal("¡Registro exitoso!");
            setTimeout(() => {
                window.location.href = './index.html';
            }, 2000);
        } else {
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
        input.addEventListener('input', () => {
            validarCampo(input);
            if (input.id === 'contraseña' || input.id === 'confirmarContraseña') {
                validarConfirmacionContraseña();
            }
        });
    });
});


