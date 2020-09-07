define([
    'jquery'
], function ($) {
    'use strict';

    /**
     * @param  {jQuery}  menu
     * @return {Boolean}
     */
    function isEnabled(menu) {
        return $(menu).closest('.navpro').hasClass('navpro-slideout') &&
            !$(menu).hasClass('navpro-slideout-silent');
    }

    $('.navpro-menu').each(function () {
        if (!isEnabled(this)) {
            return;
        }
        $('body').addClass('navpro-with-slideout');
    });
});
