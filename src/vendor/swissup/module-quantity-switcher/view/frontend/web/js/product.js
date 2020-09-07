define([
    'jquery',
    'underscore',
    'dropdown',
    'mage/template'
], function ($, _, Dropdown, mageTemplate) {
    'use strict';

    var wrapperTemplate = '<div class="qty-wrapper qty-<%- switcherType %>"></div>',
        dropdownTemplate = '<div class="action toggle trigger" ' +
            'data-toggle="dropdown" ' +
            'data-trigger-keypress-button="true"' +
            '></div>' +
            '<ul class="dropdown" data-target="dropdown"></ul>',
        dropdownItemTemplate = '<li><%- label %></li>';

    return function (config, qtyInput) {

        var QuantitySwitcher = {
            /**
             * Find selected simple product.
             *
             * @return {Number|undefined}
             */
            getSelectedProduct: function () {
                var widget;

                // Swatches enabled
                widget = $('[data-role=swatch-options]').data('mageSwatchRenderer');

                if (widget) {
                    return widget.getProduct();
                }

                // Basic configurable dropdown
                widget = $('#product_addtocart_form').data('mageConfigurable');

                if (widget) {
                    return widget.simpleProduct;
                }
            },

            /**
             * Listener for qty arrows
             *
             * @param  {Event} event
             */
            changeQty: function (event) {
                var stockConfig = this.getStockConfig(),
                    qtyInc, minQty, maxQty, value, curValue;

                if (_.isEmpty(stockConfig)) {
                    return;
                }

                qtyInc = stockConfig.qtyInc;
                minQty = stockConfig.minQty;
                maxQty = stockConfig.maxQty;

                if ($(qtyInput).attr('disabled') === 'disabled') {
                    return;
                }

                value = $(qtyInput).val();
                value = isNaN(value) ? minQty : parseFloat(value);

                if (value < minQty) {
                    this.setQtyValue(minQty);

                    return;
                } else if (value > maxQty) {
                    this.setQtyValue(maxQty);

                    return;
                }

                if ($(event.currentTarget).hasClass('qty-switcher-dec')) {
                    curValue = value - qtyInc;
                    this.setQtyValue(curValue >= minQty ? curValue : minQty);
                } else {
                    curValue = value + qtyInc;
                    this.setQtyValue(curValue <= maxQty ? curValue : maxQty);
                }
            },

            /**
             * Initialize qty switcher
             */
            initialize: function () {
                var decElement,
                    incElement,
                    wrapper;

                $(qtyInput).wrap(mageTemplate(wrapperTemplate, {
                    switcherType: config[0].switcher
                }));

                if (config[0].switcher === 'dropdown') {
                    wrapper = $(qtyInput).parent();
                    $(qtyInput).after(mageTemplate(dropdownTemplate));

                    Dropdown({}, $('.action.trigger', wrapper));
                    $('.action.trigger', wrapper)
                        .on('click.toggleDropdown', this.toogleDropdownClick.bind(this));
                    $('.dropdown', wrapper)
                        .click(this.dropdownItemClick.bind(this));
                } else {
                    decElement = $('<div class="qty-switcher-dec"></div>');
                    incElement = $('<div class="qty-switcher-inc"></div>');

                    $(decElement).insertBefore(qtyInput);
                    $(incElement).insertAfter(qtyInput);

                    $(decElement).click(this.changeQty.bind(this));
                    $(incElement).click(this.changeQty.bind(this));
                }
            },

            /**
             * Rebuild items of qty dropdown
             *
             * @param  {jQuery} dropdown
             */
            rebuildItems: function (dropdown) {
                var html = '',
                    newItems = this.getDropdownItems();

                newItems.push('Custom');
                newItems.forEach(function (qty) {
                    html += mageTemplate(dropdownItemTemplate, {
                        label: qty
                    });
                });
                dropdown.html(html);
            },

            /**
             * Get dropdown items for qty dropdown
             *
             * @return {Array}
             */
            getDropdownItems: function () {
                var stockConfig = this.getStockConfig(),
                    range,
                    items;

                if (_.isEmpty(stockConfig)) {
                    return [];
                }

                range = _.range(stockConfig.minQty, stockConfig.maxQty, stockConfig.qtyInc);
                items = range.slice(0, 5);
                items.push(range[9], range[19], range[49], range[74], stockConfig.maxQty);

                items = _.sortBy(items, function (num) {
                    return num;
                });

                return _.uniq(items.filter(Boolean));
            },

            /**
             * Get stock config for current product
             *
             * @return {Object}
             */
            getStockConfig: function () {
                if (config[0].type === 'configurable') {
                    return _.findWhere(config, {
                        id: this.getSelectedProduct()
                    });
                }

                return config[1];
            },

            /**
             * Listen click on dropdown item
             *
             * @param  {Event} event
             */
            dropdownItemClick: function (event) {
                if ($(event.target).prop('tagName') === 'LI') {
                    if (isNaN($(event.target).html())) {
                        $(qtyInput).select().focus();
                    } else {
                        this.setQtyValue($(event.target).html());
                    }
                }
            },

            /**
             * Listen toggle dropdown to rebuild items
             *
             * @param  {Event} event
             */
            toogleDropdownClick: function (event) {
                if ($(event.currentTarget).attr('aria-expanded') === 'true') {
                    this.rebuildItems($(event.currentTarget).siblings('.dropdown'));
                }
            },

            /**
             * Qty field value setter
             *
             * @param {Number} value
             */
            setQtyValue: function (value) {
                $(qtyInput).val(value);
                $(qtyInput).trigger('change');
            }
        };

        QuantitySwitcher.initialize();
    };
});
