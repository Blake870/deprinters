define([
    'jquery',
    'mage/template',
    'Magento_Ui/js/modal/modal', // 2.3.3: create 'jquery-ui-modules/widget' dependency
    'argentoSticky'
], function ($, mageTemplate) {
    'use strict';

    var gallery = '[data-gallery-role=gallery-placeholder]';

    $.widget('swissup.argentoStickyAddToCart', {
        // default options
        options: {
            'offset_top': 0,
            media: '(min-width: 768px) and (min-height: 600px)',
            parent: '.page-wrapper',
            innerClassName: 'box-tocart-inner',
            productInfoTemplate: '<div class="product-info-sticky">' +
                '<div class="info-inner">' +
                    '<span class="photo"><%- image %></span>' +
                    '<strong class="name"><%- name %></strong>' +
                '</div>' +
            '</div>'
        },

        /**
         * [_create description]
         */
        _create: function () {
            var fieldset;

            // insert additional elements before sticking
            fieldset = $('.fieldset', this.element);
            this._wrap(fieldset, this.options.innerClassName);
            fieldset.before(this._buildProductInfo());

            // initialize sticky
            this.element.argentoSticky({
                media: this.options.media,
                parent: $(this.options.parent),
                'inner_scrolling': false,
                'offset_top': this.options['offset_top']
            });

            // listen swatch attributes render complete
            $(document.body).on('swatch.initialized easytabs:contentLoaded', function () {
                $(document.body).trigger('sticky_kit:recalc');
            });

            // fix for third-party modules that shifts addto block
            setTimeout(function () {
                $(document.body).trigger('sticky_kit:recalc');
            }, 2000);

            // updtae image after Fotorama loaded
            $(document.body).on('gallery:loaded', this.updateImage.bind(this));
        },

        /**
         * Get product image URL
         *
         * @return {String}
         */
        getProductImage: function () {
            if ($(gallery).data('gallery')) {
                return $(gallery).data('gallery').fotorama.activeFrame.thumb;
            }

            return '';
        },

        /**
         * Get img element HTML with product image
         *
         * @return {String}
         */
        getImage: function () {
            var src = this.getProductImage();

            if (src) {
                return '<img src="' + src + '">';
            }

            return '';
        },

        /**
         * Update product image in sticked add-to-cart
         */
        updateImage: function () {
            var image = this.getImage();

            if (image) {
                $('.product-info-sticky .photo', this.element).html(image);
            }
        },

        /**
         * Clone product price into target
         *
         * @param  {jQuery} target
         */
        _clonePrice: function (target) {
            $('.product-info-price .price-box').clone().appendTo(target);
        },

        /**
         * Wpar element with div.className
         *
         * @param  {jQuery} element
         * @param  {String} className
         */
        _wrap: function (element, className) {
            $(element).wrap('<div class="' + className + '"></div>');
        },

        /**
         * Build product info panel for stuck element
         *
         * @return {jQuery}
         */
        _buildProductInfo: function () {
            var element;

            element = $(mageTemplate(this.options.productInfoTemplate, {
                name: $('.product .page-title').text(),
                image: this.getImage()
            }));
            this._clonePrice(element);

            return element;
        }
    });

    return $.swissup.argentoStickyAddToCart;
});
