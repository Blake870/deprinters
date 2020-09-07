define([
    'jquery',
    'argentoSticky'
], function ($) {
    'use strict';

    //jscs:disable requireCamelCaseOrUpperCaseIdentifiers
    $('.header.wrapper').argentoSticky({
        media: '(min-width: 768px) and (min-height: 600px)',
        parent: $('.page-wrapper'),
        inner_scrolling: false
    });
    //jscs:enable requireCamelCaseOrUpperCaseIdentifiers
});
