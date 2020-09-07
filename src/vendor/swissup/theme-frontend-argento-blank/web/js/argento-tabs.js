/**
 * ArgentoTabs allows to create mage.tabs on highlight widgets and any other
 * widgets with block and block title that will be used as tab title.
 */

define([
    'jquery',
    'mage/tabs',
    'jquery/visible'
], function ($) {
    'use strict';

    $.widget('argento.argentoTabs', {
        options: {
            collapsibleElement: '.block-title',
            content: '.block',
            openedState: 'active'
        },

        /**
         * Widget initialization
         */
        _create: function () {
            this._processBlocks();
            this._callTabs();
            this._bind();
        },

        /**
         * Prepare markup and classnames
         */
        _processBlocks: function () {
            var options = this.options,
                content;

            $(options.collapsibleElement, this.element).each(function () {
                content = $(this).parents(options.content);
                content.addClass('item content');

                $(this).insertBefore(content).addClass('data item title');
                $(this).children(':first').addClass('data switch');
            });
            this.element.addClass('argento-tabs');
        },

        /**
         * Call mage/tabs
         */
        _callTabs: function () {
            $.mage.tabs(this.options, this.element);
            this.element.addClass('argento-tabs-initialized');
        },

        /**
         * Add event observers
         */
        _bind: function () {
            this.element.on('dimensionsChanged', function (e, state) {
                var el = $(e.target);

                if (!state.opened || el.visible(true)) {
                    return;
                }

                $('html, body').scrollTop(el.offset().top);
            });
        }
    });

    return $.argento.argentoTabs;
});
