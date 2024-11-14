// Cargar lista de favoritos y carrito desde LocalStorage
let favorites = JSON.parse(localStorage.getItem('favorites')) || [];
let cart = JSON.parse(localStorage.getItem('cart')) || [];

// Función para renderizar los productos favoritos
function renderFavorites() {
    const favoritesContainer = document.querySelector(".row.g-4"); // Selecciona el contenedor de favoritos
    favoritesContainer.innerHTML = ""; // Limpia el contenido previo

    favorites.forEach((product) => {
        const productHTML = `
            <div class="col">
                <div class="card">
                    <img src="${product.image}" class="card-img-top" alt="${product.name}">
                    <div class="card-body text-center">
                        <h5 class="card-title">${product.name}</h5>
                        <p class="card-text">${product.price}</p>
                        <div class="d-flex justify-content-center gap-2">
                            <button onclick="addToCart('${product.id}')" class="btn btn-primary">Agregar al Carrito</button>
                            <button onclick="removeFromFavorites('${product.id}')" class="btn btn-outline-danger">Eliminar</button>
                        </div>
                    </div>
                </div>
            </div>`;
        favoritesContainer.insertAdjacentHTML("beforeend", productHTML);
    });
}

// Función para agregar producto al carrito
function addToCart(productId) {
    const product = favorites.find((item) => item.id === productId);
    if (!cart.find((item) => item.id === productId)) {
        cart.push(product);
        localStorage.setItem("cart", JSON.stringify(cart));
        alert(`${product.name} ha sido agregado al carrito.`);
    } else {
        alert(`${product.name} ya está en el carrito.`);
    }
}

// Función para eliminar producto de favoritos
function removeFromFavorites(productId) {
    favorites = favorites.filter((item) => item.id !== productId);
    localStorage.setItem("favorites", JSON.stringify(favorites));
    renderFavorites();
    alert("Producto eliminado de favoritos.");
}

// Inicializar renderizado
document.addEventListener("DOMContentLoaded", () => {
    renderFavorites();
});
