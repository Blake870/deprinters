define([
    'jquery'
], function ($) {
    'use strict';

    /**
     * Change container height to create requested columns count.
     *
     * @param {jQuery} container
     * @param {Number} columns
     */
    function makeColumns(container, columns) {
        var top, isOnTop, i,
            minHeight = 0;

        if (!$(container).height()) {
            return setTimeout(function () {
                makeColumns(container, columns);
            }, 1000);
        }

        $(container).children().each(function () {
            var height = $(this).height();

            if (height > minHeight) {
                minHeight = height;
            }
        });

        $(container).height(Math.max(
            $(container).height() / columns,
            minHeight
        ));

        // loop over children of vertical type to fix possible invalid column count
        // If everything is fine, children count with offsetTop=top will
        // be equal to column count
        top = $(container).children().get(0).offsetTop;

        /**
         * @param {Number} j
         * @param {Element} el
         * @returns {bool}
         */
        isOnTop = function (j, el) {
            return el.offsetTop === top;
        };

        i = 0;

        while ($(container).children().filter(isOnTop).length > columns && i++ < 200) {
            $(container).height($(container).height() + 30);
        }
    }

    return function (container, columns) {
        makeColumns(container, columns);
    };
});
