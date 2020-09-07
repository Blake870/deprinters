define([
    'jquery',
    'underscore',
    'uiComponent',
    'Swissup_Ajaxsearch/js/ajaxsearch/loader',
    'Swissup_Ajaxsearch/js/ajaxsearch/folded',
    'Swissup_Ajaxsearch/js/ajaxsearch/category',
    'Swissup_Ajaxsearch/js/ajaxsearch/results',
    'Swissup_Ajaxsearch/js/ajaxsearch/mobile',
    'mage/utils/wrapper',
    'Swissup_Ajaxsearch/js/lib/typeaheadbundle'
], function ($, _, Component, Loader, Folded, Category, Results, Mobile, wrapper) {
    'use strict';

    var _options = {
        elementId: '#search',
        elementCategoryId: '#swissup-ajaxsearch-filter-category',
        classes: {
            container: '.block-swissup-ajaxsearch',
            mask: '.ajaxsearch-mask',
            formLabel: '#search_mini_form .search label'
        },
        templates: {
            autocomplete: '#swissup-ajaxsearch-autocomplete-template',
            product: '#swissup-ajaxsearch-product-template',
            page: '#swissup-ajaxsearch-page-template',
            category: '#swissup-ajaxsearch-category-template',
            notFound: '#swissup-ajaxsearch-template-not-found'
        }
    },
    bloodhound,
    _element,
    _category;

    /**
     * On ready init
     * @param  {Object} Bloodhound
     */
    function _init(Bloodhound) {
        var block, sourceAdapter, debouncedRecalcWidth;

        _element = $(_options.elementId);
        _category = Category(_options);
        Results.setOptions(_options);

        block = _element.closest('.block.block-search');

        block.addClass(_options.classes.container.replace('.', ''))
            .addClass(_options.classes.additional);

        $(document.body).removeClass('swissup-ajaxsearch-loading')
            .removeClass('swissup-ajaxsearch-folded-loading');

        //add close action
        if (block.find('.actions .action.close').length === 0 &&
            block.find('.actions .action.search').length > 0) {

            $('<span title="Close" class="action close"><span>×</span></span>')
                .insertAfter(block.find('.actions .action.search'));
        }

        bloodhound = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('title'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: _options.url,
                wildcard: _options.wildcard,

                /**
                 * @param  {String} query
                 * @param  {Object} settings
                 * @return {Object}
                 */
                prepare: function (query, settings) {
                    var categoryVarName, categoryVarValue;

                    query = encodeURIComponent(query);
                    categoryVarName = _category.getVarName();
                    categoryVarValue = _category.getVarValue();

                    if (categoryVarValue) {
                        query += '&' + categoryVarName + '=' + categoryVarValue;
                    }
                    settings.url = settings.url.replace(_options.wildcard, query);

                    return settings;
                }
            }
        });
        bloodhound.initialize();

        Loader
            .setContainer(_options.loader.container)
            .setLoaderImage(_options.loader.loaderImage);

        // _options.typeahead.options.minLength = 0;
        sourceAdapter = bloodhound.ttAdapter();
        sourceAdapter = wrapper.wrap(
            sourceAdapter,
            function (withAsync, query, sync, async) {
                if (query === '') {
                    query = '__popular__';
                }

                return withAsync(query, sync, async);
            }
        );

        if ($('body').hasClass('rtl')) {
            _element.attr('dir', 'rtl');
            // dir: "rtl"
        }

        _element.typeahead(_options.typeahead.options, {
            name: 'product',
            source: sourceAdapter,
            async: true,
            displayKey: 'title',
            limit: _options.typeahead.limit,
            templates: {
                notFound: Results.getTemplate(_options.templates.notFound),
                // pending: Results.getTemplate('#swissup-ajaxsearch-product-template-pending'),
                // header: Results.getTemplate('#swissup-ajaxsearch-product-template-header'),
                // footer: Results.getTemplate('#swissup-ajaxsearch-template-footer'),
                suggestion: Results.renderSuggestion
            }

        }).bind('typeahead:selected', function (event, item) {
            var type = item._type || false,
                element = $(this);

            if (type === 'product' && typeof item['product_url'] != 'undefined') {
                window.location.href = item['product_url'];
            } else if ((type === 'page' || type === 'category') &&
                typeof item.url != 'undefined') {

                window.location.href = item.url;
            } else if (type === 'popular') {
                // element.val(item.title);
                element.typeahead('val', item.title);
                element.trigger('focus');

                setTimeout(function () {
                    element.typeahead('open');
                }, 200);
            } else {
                this.form.submit();
            }
        }).on('typeahead:asyncrequest', Loader.startLoader)
        .on('typeahead:asynccancel typeahead:asyncreceive', Loader.stopLoader)
        .on('typeahead:render', function () {
            Results.addWrappers();
            Results.recalcWidth(_element, _category);
        });

        // Do not close results when click in empty region of the dropdown.
        // @see typeaheadbundle.js:1454
        $('.block-swissup-ajaxsearch-results').on('click', function (e) {
            e.stopPropagation();
        });

        block.find('form#search_mini_form .field.search').children().wrapAll('<div class="origin"/>');

        _category.init(_element, bloodhound);

        Mobile(_element, _options);

        Folded(_options);

        debouncedRecalcWidth = _.debounce(function () {
            Results.recalcWidth(_element, _category);
        }, 100);
        $(window).on('resize', debouncedRecalcWidth);
        debouncedRecalcWidth();
    }

    return Component.extend({
        options: {
            url: '',
            wildcard: '_QUERY',
            loader: {
                container: '.block-swissup-ajaxsearch .actions .action',
                loaderImage: ''
            },
            typeahead: {
                options: {
                    highlight: true,
                    hint: true,
                    minLength: 3,
                    classNames: {}
                },
                limit: 10
            },
            settings: {}
        },

        /**
         * initialize
         * @return {this}
         */
        initialize: function () {
            this._super();
            this.setOptions(this.options);

            require([
                'bloodhound',
                'typeahead.js'
            ], function (Bloodhound) {
                // _init(Bloodhound);
                $(_init(Bloodhound));
            });

            return this;
        },

        /**
         *
         * @return {String}
         */
        version: function () {
            return this.options.settings.version;
        },
        // getOptions: function() {
        //     return _options;
        // },
        /**
         *
         * @param {Object} options
         * @return {this}
         */
        setOptions: function (options) {
            $.extend(_options, options);

            return this;
        }
    });
});
