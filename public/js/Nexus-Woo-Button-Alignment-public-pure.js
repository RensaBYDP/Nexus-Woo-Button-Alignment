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
// Pure javascript version
// Activate this script in Class-Nexus-Woo-Button-Alignment-public.php when needed.
var wooAlignButtons = function (title, wrapper) {
    "use strict";
    var parent = document.querySelectorAll("ul.products");
    if (parent.length) {
        parent.forEach(function (value, index, array) {
            var gridRows = [];
            var tempRow = [];
            var children = Array.from(value.children);
            if (children.length) {
                children.forEach(function (child, index, array) {
                    var compStyles = window.getComputedStyle(child);
                    var clear = compStyles.getPropertyValue("clear");
                    if (clear !== "none" && index !== 0) {
                        gridRows.push(tempRow);
                        tempRow = [];
                    }
                    tempRow.push(child);
                    if (children.length === index + 1) {
                        gridRows.push(tempRow);
                    }
                    if (gridRows.length) {
                        gridRows.forEach(function (row, index, array) {
                            function sameHeight(target, space) {
                                var heights = [];
                                row.forEach(function (item, index, array) {
                                    var elem = item.querySelector(target);
                                    if (elem === null) {
                                        // null
                                    } else {
                                        elem.style.minHeight = "0px";
                                        elem.style.paddingBottom = "";
                                        var height = elem.clientHeight;
                                        heights.push(height);
                                    }
                                });
                                var high = Math.max.apply(null, heights);
                                row.forEach(function (item, index, array) {
                                    var elem = item.querySelector(target);
                                    if (elem === null) {
                                        // null
                                    } else {
                                        elem.style.minHeight = high + space + "px";
                                        if (window.matchMedia("(max-width: 320px)").matches) {
                                            elem.style.minHeight = "0px";
                                            elem.style.paddingBottom = "";
                                        }
                                    }
                                });
                            }
                            sameHeight(title, 1);
                            sameHeight(wrapper, 10);
                        });
                    }
                });
            }
        });
    }
};
window.addEventListener("load", function () {
    // Must have two entries.
    /* Content of wooAlignButtons(title, wrapper); */
    wooAlignButtons("h2.woocommerce-loop-product__title", ".woo-height");
});
window.addEventListener("resize", function () {
    setTimeout(function () {
        wooAlignButtons("h2.woocommerce-loop-product__title", ".woo-height");
    }, 250);
});
window.addEventListener("load", function () {
    setTimeout(function () {
        wooAlignButtons("h2.woocommerce-loop-product__title", ".woo-height");
    }, 2000);
    setTimeout(function () {
        wooAlignButtons("h2.woocommerce-loop-product__title", ".woo-height");
    }, 5000);
    setTimeout(function () {
        wooAlignButtons("h2.woocommerce-loop-product__title", ".woo-height");
    }, 7000);
});
