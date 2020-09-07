define([
    'jquery',
    'Swissup_Ajaxsearch/js/lib/select2.min'
], function ($) {
    'use strict';

    var _elementCategory, _options;

    /**
     *
     * @param  {Object} _element
     * @param  {Object} bloodhound
     */
    function _init(_element, bloodhound) {
        var block, options;

        block = _element.closest('.block.block-search');

        _elementCategory.prependTo(block.find('form#search_mini_form .field.search'));

        _elementCategory.wrapAll('<div class="swissup-ajaxsearch-filter-category-wrapper"/>');

        _elementCategory.on('change', function () {
            var value;

            bloodhound.clear();
            value = _element.val();

            if (value !== '' && value !== '0') {
                // _element.focus();
                _element.typeahead('val', '');
                _element.typeahead('val', value);
                // _element.typeahead("input");
                // _element.trigger('input');
                // _element.typeahead('open');
                // _element.trigger('keyup');
                _element.trigger('focus');
                setTimeout(function () {
                    _element.typeahead('open');
                }, 200);
                // _element.focus();
            }
        });

        options = {
            dir: $('body').hasClass('rtl') ? 'rtl' : 'ltr',
            dropdownParent: _elementCategory.parent(),
            width: '100%',

            /**
             *
             * @return {String}
             */
            templateResult: function (item) {
                return item.text;
            },

            /**
             *
             * @type {String}
             */
            templateSelection: function (item) {
                var text = item.text;

                text = text.replace(/(?:^[\s\u00a0]+)|(?:[\s\u00a0]+$)/g, '');

                return text;
            }
        };

        _elementCategory.select2(options);

    }

    /**
     *
     * @param  {Object} options
     */
    return function (options) {
        _options = options;

        _elementCategory = $(_options.elementCategoryId);

        return {
            /**
             * Retrieve category jquery element
             * @return {Object}
             */
            getElement: function () {

                return _elementCategory;
            },

            /**
             *
             * @return {String}
             */
            getVarName: function () {

                return _options.categoryVarName || 'cat';
            },

            /**
             *
             * @return {String|Boolean}
             */
            getVarValue: function () {
                var value;

                if (!_elementCategory.length) {

                    return false;
                }

                value = _elementCategory.val();

                if (value === '' || value === '0') {

                    return false;
                }

                return value;
            },

            /**
             *
             * @param  {mixed} value
             * @return {Int}
             */
            outerWidth: function (value) {
                var el;

                el = this.getElement();
                el = el.closest('.swissup-ajaxsearch-filter-category-wrapper');

                return el.length && el.is(':visible') ? el.outerWidth(value) : 0;
            },

            /**
             *
             * @param  {Object} _element
             * @param  {Object} bloodhound
             */
            init: function (_element, bloodhound) {

                if (_elementCategory.length) {
                    _init(_element, bloodhound);
                }
            }
        };
    };
});
