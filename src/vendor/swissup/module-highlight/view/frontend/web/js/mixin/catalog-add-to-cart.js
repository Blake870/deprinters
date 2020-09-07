define([
    'jquery'
], function ($) {
    'use strict';

    return function (widget) {
        $.widget('mage.catalogAddToCart', widget, {
            /**
             * @private
             */
            _bindSubmit: function () {
                if (this.element.data('catalog-addtocart-initialized')) {
                    return;
                }
                this.element.data('catalog-addtocart-initialized', true);

                return this._super();
            }
        });

        return $.mage.catalogAddToCart;
    };
});
