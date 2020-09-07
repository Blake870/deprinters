define([
    'jquery',
    'matchMedia',
    'Swissup_Stickyfill/js/sticky'
], function ($, mediaCheck, sticky) {
    'use strict';

    /**
     * @param  {jQuery} menu
     * @return {String}
     */
    function getClassNames(menu) {
        return menu.get(0).className.match(/navpro-sticky|navpro-top\d+/g).join(' ');
    }

    /**
     * @param  {jQuery}  menu
     * @return {Boolean}
     */
    function isEnabled(menu) {
        return $(menu).hasClass('navpro-sticky') &&
            !$(menu).closest('.navpro').hasClass('navpro-slideout');
    }

    /**
     * Get container to stick
     *
     * @param  {jQuery} menu
     * @return {jQuery}
     */
    function getContainer(menu) {
        var container = $(menu).closest('.nav-sections, .block-navpro');

        if (!container.length) {
            container = $(menu).closest('.navpro');
        }

        return container;
    }

    return {
        /**
         * Init sticky feature
         *
         * @param {jQuery} menu - Element to init sticky feature
         */
        init: function (menu) {
            var container = getContainer(menu);

            if (!isEnabled(menu)) {
                return;
            }

            // Copy sticky classes to parent container that will be sticked actually
            container.addClass(getClassNames(menu));

            mediaCheck({
                media: '(max-width: 767px)', // @see layout/_type-sticky.less

                /**
                 * Unstick menu
                 */
                entry: function () {
                    sticky.remove(container);
                },

                /**
                 * Stick menu
                 */
                exit: function () {
                    sticky.add(container);
                }
            });
        }
    };
});
