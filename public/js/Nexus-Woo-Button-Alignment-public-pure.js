/*! @preserve
 * Plugin Name:       Nexus-Woo-Button-Alignment
 * Plugin URI:        https://rensa.co.za
 * Description:       A lightweight plugin to align WooCommerce "Add to cart" buttons.
 * Version:           1.0.2
 * Author:            Rensa Nexus
 * Author URI:        https://rensa.co.za
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
// Pure javascript version
// Activate this script in Class-Nexus-Woo-Button-Alignment-public.php when needed.
var wooAlignButtons = function () {
    "use strict";
    document.querySelectorAll("ul.products").forEach(function (productList) {
        var products = Array.from(productList.querySelectorAll(":scope > li.product"));
        var rows = [];

        products.forEach(function (product) {
            product.style.minHeight = "";
            var top = Math.round(product.getBoundingClientRect().top);
            var row = rows.find(function (candidate) {
                return Math.abs(candidate.top - top) <= 2;
            });

            if (!row) {
                row = { top: top, products: [] };
                rows.push(row);
            }

            row.products.push(product);
        });

        rows.forEach(function (row) {
            var tallestProduct = 0;

            row.products.forEach(function (product) {
                tallestProduct = Math.max(tallestProduct, product.offsetHeight);
            });

            row.products.forEach(function (product) {
                product.style.minHeight = tallestProduct + "px";
            });
        });
    });
};
window.addEventListener("load", function () {
    wooAlignButtons();
});
window.addEventListener("resize", function () {
    setTimeout(function () {
        wooAlignButtons();
    }, 250);
});
window.addEventListener("load", function () {
    setTimeout(function () {
        wooAlignButtons();
    }, 2000);
    setTimeout(function () {
        wooAlignButtons();
    }, 5000);
    setTimeout(function () {
        wooAlignButtons();
    }, 7000);
});
