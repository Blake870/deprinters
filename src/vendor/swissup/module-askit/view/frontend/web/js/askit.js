define([
    'jquery'
], function ($) {
    'use strict';

    var _config = {};

    return function (config) {

        $.extend(_config, config);

        //emulate captcha reload for skip full page cache
        setTimeout(function () {
            var selector;

            selector = '#askit-new-question-form .action.captcha-reload, .askit-answer-form .action.captcha-reload';
            $(selector).trigger('click');
        }, 3600);

        $('.action.askit-show-form').on('click', function (event) {
            $(event.target).closest('.askit-question-form')
                .addClass('show');
            $(event.target).hide();
        });

        return {
            /**
             *
             * @return {String}
             */
            version: function () {
                return '1.4.4';
            },

            /**
             *
             * @return {Object}
             */
            config: function () {
                return _config;
            }
        };
    };
});
