define([
    'jquery',
    'argentoSticky'
], function ($) {
    'use strict';

    var media = '(min-width: 768px) and (min-height: 600px)';

    //jscs:disable requireCamelCaseOrUpperCaseIdentifiers
    $('.nav-sections').argentoSticky({
        media: media,
        parent: $('.page-wrapper'),
        inner_scrolling: false
    });
    //jscs:enable requireCamelCaseOrUpperCaseIdentifiers

    // var sidebar = $('.catalog-product-view .sidebar-additional');
    // if (sidebar.length) {
    //     sidebar.argentoSticky({
    //         media: media,
    //         offset_top: $('.nav-sections').height() + 10
    //     });
    //     // fix invalid position after switching from short to long tab
    //     $(document.body).on('click', function() {
    //         $(document.body).trigger('sticky_kit:recalc');
    //     });
    // }
});
