define([
    'jquery'
], function ($) {
    'use strict';

    var _options, search = {
        /**
         * Show
         */
        show: function () {
            search.calculateFieldPosition();

            // show fields
            $(_options.classes.container).addClass('shown');
            $(_options.classes.mask).addClass('shown');
            $(_options.elementId).focus();
        },

        /**
         * Hide
         */
        hide: function () {
            $(_options.classes.container).removeClass('shown');
            $(_options.classes.mask).removeClass('shown');
            $(_options.elementId).closest('div').addClass('inactive');
        },

        /**
         * Calculate and set
         */
        calculateFieldPosition: function () {
            // calculate offsetTop dynamically to guarantee that field
            // will appear in the right place (dynamic header height, etc.)
            // var header = $('.header.content'),
            //     headerOffset = header.offset(),
            //     zoomOffset = $('.action.search', _options.classes.container).offset(),
            //     offsetTop = zoomOffset.top - headerOffset.top;

            // if (header.length === 0) {
            //     header = $('.header .container');
            // }

            // if ($('body').width() < 768) {
            //     // reset for small screens
            //     offsetTop = '';
            // } else if (offsetTop <= 0) {

            //     return;
            // }
            // $('.action.close', _options.classes.container).css({
            //     top: offsetTop
            // });
            // $('.field.search', _options.classes.container).css({
            //     paddingTop: offsetTop
            // });
        },

        /**
         *
         * @return {Boolean}
         */
        isVisible: function () {
            return $(_options.classes.container).hasClass('shown') ||
                !$(_options.classes.container).find('div.control').hasClass('inactive');
        }
    };

    /**
     * Init folded design
     */
    function _init() {
        $(_options.classes.container).append(
            '<div class="' + _options.classes.mask.substr(1) + '"></div>'
        );

        $(document.body).keydown(function (e) {

            if (e.which === 27) {
                search.hide();
            }
        });

        $(window).resize(function () {
            search.calculateFieldPosition();
        });

        $(_options.classes.mask).click(function () {
            search.hide();
        });

        $('.action.search', _options.classes.container).removeAttr('disabled');

        $('.action.search', _options.classes.container).click(function (e) {
            if (!search.isVisible()) {
                e.preventDefault();
                search.show();
            }
        });

        $('.action.close', _options.classes.container).click(function (e) {
            e.preventDefault();
            search.hide();
        });
    }

    /**
     *
     * @param  {Object} options
     */
    return function (options) {
        _options = options;

        if ($(_options.classes.container).hasClass('folded')) {

            _init();
        }
    };
});
