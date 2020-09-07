define([
    'jquery',
    'mage/collapsible',
    'domReady!',
    'argentoSticky'
], function ($) {
    'use strict';

    //jscs:disable requireCamelCaseOrUpperCaseIdentifiers
    $('body.stripes-sticky-menu .nav-sections').argentoSticky({
        media: '(min-width: 768px) and (min-height: 600px)',
        parent: $('.page-wrapper'),
        inner_scrolling: false
    });
    //jscs:enable requireCamelCaseOrUpperCaseIdentifiers

    // move text after qty to show/hide it with css plus (+)
    (function () {
        var text = $('.header .minicart-wrapper .action.showcart .text'),
            qty = $('.header .minicart-wrapper .action.showcart .counter.qty');

        text.insertAfter(qty);
    })();

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
