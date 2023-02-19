
document.addEventListener('DOMContentLoaded', function() {
    getProducts();
});
function getProducts() {
    let http = new XMLHttpRequest();
    let list = document.getElementById('products_list');
    list.innerHTML = '<p>Loading...</p>';

    let url = 'http://localhost:8000/api/products';
    http.open('GET', url, true);
    http.onreadystatechange = function() {
        if (http.readyState == 4 && http.status == 200) {
            let products = JSON.parse(http.responseText);
            if(products) {
                list.innerHTML = '';
                products.products.forEach(product => {
                    list.appendChild(createProduct(product));
                });
            } else {
                document.getElementById('error').innerHTML = 'No products found';
            }
        }
    }
    http.send();
}

function createProduct(product) {
    let li = document.createElement('li');
    li.classList.add('product_item');
    let image = (product.img == null) ? 'img/no-image.jpg' : product.img;
    li.innerHTML = `<div class="product_img">
        <img class="img" src="${image}" alt="${product.name}">
        </div>
        <div class="img_overlay"></div>  
        <div class="product_info">
            <h2 class="product_name">${product.name}</h2>
            <div class="product_text">
                <p class="product_description">${product.description}</p>
                <p class="product_price">${product.weight} Kg</p>
            </div>
        </div>`;
    return li;
}