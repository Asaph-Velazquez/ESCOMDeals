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
    const valor = campo.value;  
    const errorDiv = campo.nextElementSibling;

    if (!validacion) {
        return true;
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

function mostrarModal(mensaje, exitoso = true) {
    const modalTexto = document.getElementById('mensajeModalTexto');
    const modalHeader = document.querySelector('#mensajeModal .modal-header');

    modalTexto.textContent = mensaje;

    modalHeader.classList.toggle('bg-success', exitoso);
    modalHeader.classList.toggle('bg-danger', !exitoso);

    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('mensajeModal'));
    modal.show();
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
        mostrarModal("¡Registro exitoso!");
        setTimeout(() => document.getElementById('registroForm').submit(), 2000); 
    } else {
        mostrarModal("Por favor corrige los errores en el formulario.", false);
    }
}

function enviarFormulario(event) {
    event.preventDefault(); 

    const form = document.getElementById('registroForm');
    const formData = new FormData(form);

    fetch('..//FoodMart-1.0.0/php/registro.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarModal(data.message, true);
                setTimeout(() => {
                    form.reset();
                    window.location.href = '..//FoodMart-1.0.0/php/registro.php'; 
                }, 2000);
            } else {
                mostrarModal(data.message, false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarModal('Ocurrió un error inesperado. Por favor, intenta de nuevo más tarde.', false);
        });
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('registroForm').addEventListener('submit', enviarFormulario);
});