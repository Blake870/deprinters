define([
    'underscore',
    'mage/utils/wrapper'
], function (_, wrapper) {
    'use strict';

    return function (Collapsible) {
        var methodsToDisable = [
                '_scrollToTopIfVisible',   // Magento 2.3.3
                '_scrollToTopIfNotVisible' // Magento 2.3.4
            ];

        // Prevent tabs from scroll into viewport.
        // Because it makes me crazy.
        _.map(methodsToDisable, function (method) {
            if (Collapsible.prototype[method]) {
                Collapsible.prototype[method] = wrapper.wrap(
                    '', // since I don't need original method I pass an empty string
                    function () {
                        return false;
                    }
                );
            }
        });

        return Collapsible;
    };
});
