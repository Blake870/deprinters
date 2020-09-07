define([
    'jquery'
], function ($) {
    'use strict';

    return function (widget) {
        //disable standard quikSearch widget
        $.widget('mage.quickSearch', widget, {
            /**
             * @return void
             */
            _create: function () {
                if ($('body.swissup-ajaxsearch-loading').length > 0 ||
                    $('#swissup-ajaxsearch-init').length > 0) {

                    return;
                }

                return this._super();
            }
        });

        return $.mage.quickSearch;
    };
});
