define([
    'jquery',
    'mage/collapsible',
    'domReady!'
], function ($) {
    'use strict';

    // init footer links accordion on mobile
    (function () {
        var mqlFooter,
            footerLinks = $('.footer-links .footer.links > li');

        if (!footerLinks.length) {
            return;
        }

        /**
         * @param {matchMedia} mql
         */
        function toggleFooterBlocks(mql) {
            if (mql.matches) {
                if (footerLinks.data('collapsible')) {
                    footerLinks
                        .collapsible('forceActivate')
                        .collapsible('destroy');
                }
            } else {
                footerLinks
                    .collapsible({
                        icons: {
                            'header': 'plus',
                            'activeHeader': 'minus'
                        }
                    })
                    .collapsible('deactivate');
            }
        }

        mqlFooter = matchMedia('(min-width: 768px)');
        toggleFooterBlocks(mqlFooter);
        mqlFooter.addListener(toggleFooterBlocks);
    })();
});
