
document.addEventListener('DOMContentLoaded', function() {
    getProducts();
});
function getProducts() {
    console.log('getProducts()');
    let http = new XMLHttpRequest();

    let url = 'http://localhost:8000/api/products';
    http.open('GET', url, true);
    http.onreadystatechange = function() {
        if (http.readyState == 4 && http.status == 200) {
            let products = JSON.parse(http.responseText);
            console.log(products);
        }
    }
    http.send();
}