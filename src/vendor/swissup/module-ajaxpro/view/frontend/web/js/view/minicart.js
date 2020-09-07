
define([
    'Magento_Checkout/js/view/minicart',
    'Magento_Customer/js/customer-data',
    'jquery',
    'Swissup_Ajaxpro/js/modal-manager',
    'Swissup_Ajaxpro/js/view/cart-sidebar',
    'mage/translate',
    'mage/dropdown'
], function (Component, customerData, $, ModalManager) {
    'use strict';

    var sidebarInitialized = false,
        addToCartCalls = 0,
        miniCart,
        checkoutConfig;

    miniCart = $('[data-block=\'ajaxpro-minicart\']');

    /**
     * @return {Boolean}
     */
    function initSidebar() {
        if ($('[data-block=\'ajaxpro-minicart\']').get(0) !== miniCart.get(0)) {
            miniCart = $('[data-block=\'ajaxpro-minicart\']');
            sidebarInitialized = false;
        }

        if (miniCart.data('swissupCartSidebar')) {
            miniCart.cartSidebar('update');
        }

        if (!$('[data-role=product-item]').length) {
            return false;
        }
        miniCart.trigger('contentUpdated');

        if (sidebarInitialized) {
            return false;
        }
        sidebarInitialized = true;
        miniCart.cartSidebar({
            'targetElement': 'div.block.block-minicart',
            'url': {
                'checkout': checkoutConfig.checkoutUrl,
                'update': checkoutConfig.updateItemQtyUrl,
                'remove': checkoutConfig.removeItemUrl,
                'loginUrl': checkoutConfig.customerLoginUrl,
                'isRedirectRequired': checkoutConfig.isRedirectRequired
            },
            'button': {
                'checkout': '#top-cart-btn-checkout',
                'remove': '#mini-cart a.action.delete',
                'close': '#btn-minicart-close'
            },
            'showcart': {
                'parent': 'span.counter',
                'qty': 'span.counter-number',
                'label': 'span.counter-label'
            },
            'minicart': {
                'list': '#mini-cart',
                'content': '#minicart-content-wrapper',
                'qty': 'div.items-total',
                'subtotal': 'div.subtotal span.price',
                'maxItemsVisible': checkoutConfig.minicartMaxItemsVisible
            },
            'item': {
                'qty': ':input.cart-item-qty',
                'button': ':button.update-cart-item'
            },
            'confirmMessage': $.mage.__('Are you sure you would like to remove this item from the shopping cart?')
        });

        return true;
    }

    return Component.extend({
        /**
         * @override
         */
        initialize: function (options) {
            var self = this,
                cartData = customerData.get('cart');

            checkoutConfig = window.checkout ? window.checkout : options['checkout_lite_config'];

            this.update(cartData());
            cartData.subscribe(function (updatedCart) {
                addToCartCalls--;
                this.isLoading(addToCartCalls > 0);
                sidebarInitialized = false;
                this.update(updatedCart);
                initSidebar();
            }, this);
            $('[data-block="ajaxpro-minicart"]').on('contentLoading', function () {
                addToCartCalls++;
                self.isLoading(true);
            });

            if (cartData()['website_id'] !== checkoutConfig.websiteId) {
                customerData.reload(['cart'], false);
            }

            if (options['override_minicart'] === true) {
                if ($('#ajaxpro-checkout\\.cart').html() === '') {
                    customerData.reload(['ajaxpro-cart'], false);
                    ModalManager.register(
                        'ajaxpro-checkout.cart',
                        $('#ajaxpro-checkout\\.cart').closest('.block-ajaxpro')
                    );
                }
                $('[data-block=\'minicart\'] a.showcart').off().on('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    ModalManager.show('checkout.cart', true);

                    return false;
                });
            }

            return this._super();
        },
        initSidebar: initSidebar
    });
});
