define([
    'jquery',
    'domReady!'
], function ($) {
    'use strict';

    /**
     * @param  {jQuery}  menu
     * @return {Boolean}
     */
    function isEnabled(menu) {
        return !$(menu).hasClass('navpro-sticky');
    }

    /**
     * Add required markup
     */
    function addOverlayMarkup() {
        $('body').append('<div class="navpro-overlay-element"></div>');

        $('.navpro-overlay-element').click(function () {
            if ($('html').hasClass('nav-open')) {
                $('.nav-toggle').first().click();
            }
            $('.navpro-overlay-element').removeClass('shown');
        });
    }

    addOverlayMarkup();

    return {

        /**
         * Show overlay
         *
         * @param {jQuery} menu
         * @return {jQuery}
         */
        show: function (menu) {
            if (!isEnabled(menu)) {
                return;
            }

            return $('.navpro-overlay-element').addClass('shown');
        },

        /**
         * Hide overlay
         *
         * @param {jQuery} menu
         * @return {jQuery}
         */
        hide: function (menu) {
            if (!isEnabled(menu)) {
                return;
            }

            return $('.navpro-overlay-element').removeClass('shown');
        }
    };
});
