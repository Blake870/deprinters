define([
    'jquery',
    'mage/template',
    'Magento_Catalog/js/price-utils',
    'mage/translate',
    'text!Swissup_Ajaxsearch/template/x-magento-template/autocomplete.html',
    'text!Swissup_Ajaxsearch/template/x-magento-template/category.html',
    'text!Swissup_Ajaxsearch/template/x-magento-template/notFound.html',
    'text!Swissup_Ajaxsearch/template/x-magento-template/page.html',
    'text!Swissup_Ajaxsearch/template/x-magento-template/product.html'
], function ($, _template, utils, $t, autocomplete, category, notFound, page, product) {
    'use strict';

    var results, _options, templatesContainer;

    templatesContainer = $('<div />');
    templatesContainer.addClass('swissup-ajaxsearch-templates');
    templatesContainer.appendTo(document.body);

    [autocomplete, category, notFound, page, product].forEach(function (templateString) {
        var templateNode, re, matches;

        // re = /\$t\([^'"]*?(["'])(.+?)\1.*?\)/g;
        // re = /translate\=("')([^\'].*?)\'\"/g;
        re = /<span.*translate\=("')([^\'].*?)\'\"><\/span>/g;

        while ((matches = re.exec(templateString)) !== null)  {
            templateString = templateString.replace(matches[0], $t(matches[2]));
        }

        templateNode = $(templateString);
        templateNode.appendTo(templatesContainer);
    });

    return {
        /**
         *
         * @param {Object} options
         * @return {this}
         */
        setOptions: function (options) {
            _options = options;

            return this;
        },

        /**
         *
         * @param  {String} id
         * @return {String}
         */
        getTemplate: function (id) {
            return _template(id);
        },

        /**
         *
         * @param  {Object} item
         * @return {String}
         */
        renderSuggestion: function renderSuggestion(item) {
            // debugger;
            // console.log(item);
            var type = item._type || false,
            template = _template(_options.templates.autocomplete);

            if (type === 'debug') {
                console.log(item._select);
            }

            if (type === 'product') {
                // item.description = item.short_description + '' || '';
                // if (50 < item.description.lenght) {
                //     item.description = item.description.substr(0, 50) + '...';
                // }
                template = _template(_options.templates.product);
            }

            if (type === 'category') {
                template = _template(_options.templates.category);
            }

            if (type === 'page') {
                template = _template(_options.templates.page);
            }

            if (type === 'popular') {
                template = _template('#swissup-ajaxsearch-popular-template');
            }

            return template({
                item: item,
                formatPrice: utils.formatPrice,
                priceFormat: _options.settings.priceFormat
            });
        },

        /**
         *
         */
        addWrappers: function () {
            results = $('.block-swissup-ajaxsearch-results');

            results.find('.product-item-info').wrapAll('<div class="product-item-info-wrapper" />');
            results.find('.product-item-info-wrapper').prepend(
                _template('#swissup-ajaxsearch-product-template-header')
            );

            if (results.find('.category-item-info').length) {
                results.find('.category-item-info').wrapAll('<div class="category-item-info-wrapper" />');
                results.find('.category-item-info-wrapper').prepend(
                    _template('#swissup-ajaxsearch-category-template-header')
                );
            }

            if (results.find('.page-item-info').length) {
                results.find('.page-item-info').wrapAll('<div class="page-item-info-wrapper" />');
                results.find('.page-item-info-wrapper').prepend(
                    _template('#swissup-ajaxsearch-page-template-header')
                );
            }

            if (results.find('.autocomplete-item-info').length) {
                results.find('.autocomplete-item-info').wrapAll('<div class="autocomplete-item-info-wrapper" />');
                results.find('.autocomplete-item-info-wrapper').prepend(
                    _template('#swissup-ajaxsearch-autocomplete-template-header')
                );
            }

            results.find('.popular-item-info').wrapAll('<div class="popular-item-info-wrapper" />');

            results.find('.category-item-info-wrapper, .page-item-info-wrapper, .autocomplete-item-info-wrapper')
                .wrapAll('<div class="custom-item-info-wrapper" />');

            return this;
        },

        /**
         *
         * @param  {Object} _element
         * @param  {Object} _category
         */
        recalcWidth: function (_element, _category) {
            results = $('.block-swissup-ajaxsearch-results');

            results.each(function (i, el) {
                var minWidth, offset, left, right, categoryElementWidth, isRtl;

                offset = _element.offset();
                left = offset.left;
                right = $(window).width() - left - _element.outerWidth(true);
                categoryElementWidth = _category.outerWidth(true);
                isRtl = $('body').hasClass('rtl');

                if ((left > right && !isRtl) || (left < right && isRtl)) {
                    left = 'auto';
                    right = 0;
                } else {
                    left = categoryElementWidth > 0 ? '-' + categoryElementWidth + 'px' : 0;
                    right = 'auto';
                }

                if (left === 'auto') {
                    $(el).removeClass('stick-to-start').addClass('stick-to-end');
                } else {
                    $(el).removeClass('stick-to-end').addClass('stick-to-start');
                }

                $(el).css('left', isRtl ? right : left);
                $(el).css('right', isRtl ? left : right);

                minWidth = _element.outerWidth(true) + categoryElementWidth;

                if ($(window).width() > minWidth) {
                    $(el).css('min-width', minWidth);
                }
            });

            return this;
        }
    };
});
