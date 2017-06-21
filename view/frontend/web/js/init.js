/**
 * Infinity Scroll Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */
define(
    [
        'jquery',
        'underscore',
        'endlessScroll'
    ],
    function ($, _, endlessScroll) {
        'use strict';

        var productClass = '.product-item';
        var productPriceClass = '.price-box';
        var productsClass = '.products.wrapper';

        var lockLoadProducts = false, nextProducts = null, block, btn,
            loadingIcon, currentPage, countPages, settings, productIds = [], isLoadingProducts = false;

        function loadProducts() {
            var page = currentPage + 1;
            if (lockLoadProducts || page > countPages || nextProducts) {
                return;
            }
            lockLoadProducts = true;
            var params = {
                load_products: true
            };
            params[settings.page_param_name] = page;
            $.ajax({
                method: 'GET',
                url: window.location.href,
                data: params,
                cache: true,
                dataType: 'html'
            }).done(function (data) {
                nextProducts = filterProducts(data);
                lockLoadProducts = false;
            });
        }

        function showProducts() {
            if (currentPage >= countPages) {
                return;
            }
            isLoadingProducts = true;
            if (nextProducts) {
                loadingIcon.hide();
                $(productsClass).last().after(nextProducts);
                $('#maincontent').trigger('contentUpdated');
                nextProducts = null;
                ++currentPage;
                // don't remove
                window.infinityScrollPageNumber = currentPage;
                loadProducts();
                if (currentPage < countPages) {
                    if (settings.loading_type == 1) {
                        btn.show();
                    } else {
                        initScrollLoad();
                    }
                } else {
                    btn.hide();
                    loadingIcon.hide();
                }
                $(document).trigger('infinity-scroll-finish-show-products');
                isLoadingProducts = false
            } else {
                setTimeout(showProducts, 200);
            }
        }

        // remove repeat products for relevance sort order
        function filterProducts(data) {
            var html = data ? $(data) : $(productsClass);
            html.find(productPriceClass).each(function () {
                var elem = $(this),
                    productId = elem.data('product-id');
                if (productId) {
                    if (_.contains(productIds, productId)) {
                        elem.closest(productClass).remove();
                    } else {
                        productIds.push(productId);
                    }
                }
            });
            return html;
        }

        function showLoadingIcon() {
            if (currentPage < countPages) {
                btn.hide();
                loadingIcon.show();
            }
        }

        function initScrollLoad() {
            setTimeout(function () {
                var position = $(document).height() - block.offset().top - 77;
                /*if ($(window).scrollTop() > position) {
                 showLoadingIcon();
                 showProducts();
                 } else {*/
                $(window).endlessScroll({
                    inflowPixels: position,
                    callback: function() {
                        showLoadingIcon();
                        showProducts();
                    }
                });
                //}
            }, 200);
        }

        return function (config) {
            settings = $.extend(true, {
                'loading_type': 1,
                'page_param_name': 'p',
                'product_class': '.product-item',
                'product_price_class': '.price-box',
                'products_class': '.products.wrapper'
            }, config);
            productClass = settings.product_class;
            productPriceClass = settings.product_price_class;
            productsClass = settings.products_class;

            block = $('.infinity-scroll-products').last();
            btn = block.find('.infinity-scroll-btn');
            loadingIcon = block.find('.infinity-scroll-loading-icon');
            currentPage = parseInt(btn.data('current-page-num'));
            countPages = parseInt(btn.data('last-page-num'));

            if (currentPage >= countPages) {
                return;
            }
            // don't remove
            window.infinityScrollPageNumber = currentPage;

            /* load products use $(document).trigger('infinity-scroll-show-products') */
            $(document).bind('infinity-scroll-start-show-products', function(e) {
                if (!isLoadingProducts && currentPage != countPages) {
                    showLoadingIcon();
                    showProducts();
                }
            });

            if (settings.loading_type == 1) {
                btn.on('click', function (e) {
                    showLoadingIcon();
                    showProducts();
                    e.preventDefault();
                    return false;
                }).show();
            } else {
                btn.hide();
                initScrollLoad();
            }
            filterProducts();
            loadProducts();
        };
    }
);