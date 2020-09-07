define([
    'jquery'
], function ($) {
    'use strict';

    var defaults = {
        relatedField: '#related-products-field'
    };

    return function (config, element) {
        config = $.extend(defaults, config);
        $('[type=checkbox].soldtogether-customer', element).change(function () {
            var relatedItems = $(config.relatedField).val(),
                items,
                index;

            items = relatedItems.length > 0 ? relatedItems.split(',') : [];

            if ($(this).is(':checked')) {
                items.push($(this).val());
            } else {
                index = items.indexOf($(this).val());

                if (index !== -1) {
                    items.splice(index, 1);
                }
            }

            $(config.relatedField).val(items.join(','));
        });
    };
});
