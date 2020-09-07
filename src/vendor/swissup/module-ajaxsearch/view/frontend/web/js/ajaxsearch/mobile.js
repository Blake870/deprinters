define([
    'jquery'
], function ($) {
    'use strict';

    var _options, _element, search = {
        /**
         * Show
         */
        show: function () {
            _element.closest('div').removeClass('inactive');
        },

        /**
         * Hide
         */
        hide: function () {
            _element.closest('div').addClass('inactive');
        }
    };

    /**
     *
     * @param  {Object} element
     * @param  {Object} options
     */
    return function (element, options) {
        var isIOS, isSafari;

        _element = element;
        _options = options;
        isIOS = !!navigator.userAgent.match(/iPhone|iPad|iPod/i);
        isSafari = !!navigator.userAgent.match(/Version\/[\d\.]+.*Safari/);

        search.hide();

        if (isIOS || isSafari) {
            $(document).on('click.tt', function _click(e) {
                if ($(e.target).is(_element)) {
                    return;
                }
                // if ($(e.target).is($('.action.search'))){
                //     return;
                // }

                if ($(e.target).is($(_options.classes.formLabel))) {
                    return;
                }
                search.hide();
            });
        } else {
            _element.on('blur', $.proxy(function () {
                setTimeout($.proxy(function () {
                    search.hide();
                    // console.log('blur');
                }, this), 250);
            }, this));
            _element.trigger('blur');
        }

        _element.on('focus', function () {
            search.show();
        });

        $(_options.classes.formLabel).on('click', function () {
            search.show();
        });
    };
});
