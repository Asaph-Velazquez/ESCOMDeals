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
// Función para mostrar mensajes en el modal dinámico
function mostrarModal(mensaje, exitoso = true) {
    const modalTexto = document.getElementById('mensajeModalTexto');
    const modalHeader = document.querySelector('#mensajeModal .modal-header');

    // Asignar el mensaje al modal
    modalTexto.textContent = mensaje;

    // Cambiar estilo del encabezado según el resultado
    modalHeader.classList.toggle('bg-success', exitoso);
    modalHeader.classList.toggle('bg-danger', !exitoso);

    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('mensajeModal'));
    modal.show();
}

function validarFormulario(event) {
    event.preventDefault(); // Prevenir el envío del formulario
    const campos = document.querySelectorAll('#registroForm input');
    let esValido = true;

    // Validar cada campo individualmente
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

    // Mostrar mensaje según resultado
    if (esValido) {
        mostrarModal("¡Registro exitoso!");
        setTimeout(() => document.getElementById('registroForm').submit(), 2000); // Enviar formulario tras 2 segundos
    } else {
        mostrarModal("Por favor corrige los errores en el formulario.", false);
    }
}

function enviarFormulario(event) {
    event.preventDefault(); // Evitar el envío predeterminado del formulario

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
                    form.reset(); // Limpia el formulario después de éxito
                    window.location.href = '..//FoodMart-1.0.0/php/registro.php'; // Redirige si es necesario
                }, 2000);
            } else {
                // Si el servidor envía un mensaje de error específico, lo mostramos en el modal
                mostrarModal(data.message, false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Mostrar mensaje genérico sólo si no se recibe una respuesta del servidor
            mostrarModal('Ocurrió un error inesperado. Por favor, intenta de nuevo más tarde.', false);
        });
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('registroForm').addEventListener('submit', enviarFormulario);
});