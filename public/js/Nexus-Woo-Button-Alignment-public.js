/*! @preserve
 * Plugin Name:       Nexus-Woo-Button-Alignment
 * Plugin URI:        https://rensa.co.za
 * Description:       A lightweight plugin to align WooCommerce "Add to cart" buttons.
 * Version:           1.0.1
 * Author:            Rensa Nexus
 * Author URI:        https://rensa.co.za
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
var wooAlignButtons = function () {
    (function ($) {
        "use strict";

        $("ul.products, .wc-block-product-template, .wc-block-grid__products").each(function () {
            var products = $(this).children("li.product, li.wc-block-product, li.wc-block-grid__product");
            var rows = [];

            products.css("min-height", "");

            products.each(function () {
                var product = this;
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

                $(row.products).each(function () {
                    tallestProduct = Math.max(tallestProduct, $(this).outerHeight());
                });

                $(row.products).css("min-height", tallestProduct);
            });
        });
    })(jQuery);
};
window.addEventListener("load", function () {
    wooAlignButtons();
});
window.addEventListener("resize", function () {
    wooAlignButtons();
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
