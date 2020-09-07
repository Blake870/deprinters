define([
    'jquery',
    'underscore',
    'Magento_Catalog/js/price-utils'
], function ($, _, utils) {
    'use strict';

    $.widget('swissup.soldTogether', {
        options: {
            taxDisplay: '0',
            priceFormat: {}
        },

        /**
         * {@inheritdoc}
         */
        _init: function () {
            this.valuesToRestore = false;
            this.addObservers();
            this.updateTotals();
        },

        /**
         * Initialize observers
         */
        addObservers: function () {
            var bundleAddToCartForm;

            this._on({
                'change .relatedorderamazon-checkbox': this.toggleItem,
                'click .soldtogether-cart-btn': this.addToCartItems,
                // listen events from priceBox widget of Magento_Catalog
                'reloadPrice .amazonstyle-images li.first [data-role=priceBox]': this.updateTotals,
                'reloadPrice .amazonstyle-checkboxes li:first-child [data-role=priceBox]': this.updateTotals
            });

            // listen price update for bundle product
            bundleAddToCartForm = $('#product_addtocart_form').each(
                function () {
                    return typeof $(this).data('magePriceBundle') !== 'undefined';
                }
            );
            bundleAddToCartForm.find('.price-box').on(
                'updatePrice',
                this.updatePriceBundleProduct.bind(this)
            );

            $(document).on('ajax:addToCart', this.restoreRelatedProductsField.bind(this));
        },

        /**
         * Get selected elements
         */
        getItems: function () {
            var items = $('.amazonstyle-checkboxes .product-name, .amazonstyle-images li', this.element);

            return items.filter(function () {
                return $('.checkbox', this).is(':checked');
            });
        },

        /**
         * Update totals near add to cart button
         */
        updateTotals: function () {
            var self = this,
                totalPrice = 0,
                totalExclPrice = 0,
                elTotal = $('.totalprice .price-box .price-container .price-wrapper .price', self.element),
                elIncTax = $('.totalprice .price-box .price-container .price-including-tax .price', self.element),
                elExclTax = $('.totalprice .price-box .price-container .price-excluding-tax .price', self.element);

            this.getItems().each(function () {
                var textPrice,
                    floatPrice;

                if (self.options.taxDisplay === '3') {
                    totalPrice += $('.price-box .price-container .price-including-tax', this).data('price-amount');
                    totalExclPrice += $('.price-box .price-container .price-excluding-tax', this).data('price-amount');
                } else {
                    textPrice = $('.price-box [data-price-type="finalPrice"]', this).text();
                    floatPrice = self.toNumber(textPrice);
                    totalPrice += isNaN(floatPrice) ? 0 : floatPrice;
                }
            });

            if (this.options.taxDisplay === '3') {
                $(elIncTax).html(utils.formatPrice(totalPrice, this.options.priceFormat));
                $(elExclTax).html(utils.formatPrice(totalExclPrice, this.options.priceFormat));
            } else {
                $(elTotal).html(utils.formatPrice(totalPrice, this.options.priceFormat));
            }
        },

        /**
         * Add to cart selected items
         */
        addToCartItems: function () {
            var checkboxes = this.getItems().find('.checkbox:not(.main-product)'),
                values = [];

            this.valuesToRestore = this.getRelatedProductsFieldValues();

            $('html, body').animate({
                scrollTop: 0
            }, 'slow');

            values = checkboxes.map(function () {
                return $(this).val();
            }).get();
            this.setRelatedProductsFieldValues(values);

            $('#product-addtocart-button').click();
        },

        /**
         * @return {Array}
         */
        getRelatedProductsFieldValues: function () {
            return ($('#related-products-field').val() || '').split(',');
        },

        /**
         * @param {Array} ids
         */
        setRelatedProductsFieldValues: function (ids) {
            $('#related-products-field').val(_.uniq(ids).join(','));
        },

        /**
         * Remove items added in addToCartItems method
         */
        restoreRelatedProductsField: function () {
            if (this.valuesToRestore !== false) {
                this.setRelatedProductsFieldValues(this.valuesToRestore);
                this.valuesToRestore = false;
            }
        },

        /**
         * Convert string into number
         *
         * @param  {String} string
         * @return {Number}
         */
        toNumber: function (string) {
            var numberPattern,
                number;

            numberPattern = new RegExp('[^0-9\\' + this.options.priceFormat.decimalSymbol + '-]+', 'g'),
            number = string.replace(numberPattern, '');
            number = number.split(this.options.priceFormat.decimalSymbol).join('.');

            return parseFloat(number);
        },

        /**
         * @param  {Event} event
         * @return void
         */
        updatePriceBundleProduct: function (event) {
            var outerPriceBox = $(event.target).data('magePriceBox'),
                innerPriceBox;

            if (typeof outerPriceBox === 'undefined') {
                return;
            }

            innerPriceBox = $('[data-product-id=' + outerPriceBox.options.productId + ']', this.element)
                .data('magePriceBox');

            if (innerPriceBox) {
                innerPriceBox.cache = $.extend({}, outerPriceBox.cache);
                innerPriceBox.element.trigger('reloadPrice');
            }
        },

        /**
         * Toggle item when checkbox changed
         *
         * @param  {Event} event
         */
        toggleItem: function (event) {
            var $checkbox = $(event.currentTarget),
                $image = $('#soldtogether-image-' + $checkbox.val());

            if ($checkbox.is(':checked')) {
                $image.removeClass('item-inactive');
                $image.prev('.plus').removeClass('item-inactive');
            } else {
                $image.addClass('item-inactive');
                $image.prev('.plus').addClass('item-inactive');
            }

            this.updateTotals();
        }
    });

    return $.swissup.soldTogether;
});
