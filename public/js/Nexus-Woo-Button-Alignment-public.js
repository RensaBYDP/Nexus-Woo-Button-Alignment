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
        $("ul.products").each(function () {
            var products = $(this).children("li.product");
            var rows = [];

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
                $(row.products).find(".woo-height").css({
                    "min-height": "",
                    "padding-bottom": ""
                });

                var tallestWrapper = 0;
                $(row.products).each(function () {
                    var wrapper = $(this).find(".woo-height");
                    if (wrapper.length) {
                        tallestWrapper = Math.max(tallestWrapper, wrapper.outerHeight());
                    }
                });

                $(row.products).each(function () {
                    var wrapper = $(this).find(".woo-height");
                    if (wrapper.length) {
                        wrapper.css("min-height", tallestWrapper + 10);
                    }
                });
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
