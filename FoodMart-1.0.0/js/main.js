document.addEventListener('DOMContentLoaded', function() {
    const productImages = document.querySelectorAll('.product-image');
    productImages.forEach(image => {
        image.addEventListener('click', function(event) {
            event.preventDefault(); // Evita que el clic lleve a otra página
        });
    });
});