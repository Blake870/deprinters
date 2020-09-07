define([
    'jquery'
], function ($) {
    'use strict';

    /**
     * @return {Boolean}
     */
    function isRtl() {
        return $('body').hasClass('rtl') ||
               $('body').css('direction') === 'rtl' ||
               $('html').css('direction') === 'rtl';
    }

    /**
     * Add `navpro-rtl` class just in case if body doesn't have class `rtl`.
     * If body already has `rtl` class - this logic is redundant.
     */
    function addRtlClass() {
        if (isRtl()) {
            $('body').addClass('navpro-rtl');
        }
    }

    addRtlClass();

    $(function () {
        addRtlClass();
    });

    return {

        /**
         * Check if RTL styles are used
         *
         * @returns {bool}
         */
        isRtl: function () {
            return isRtl();
        }
    };
});
