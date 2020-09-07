define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    return function (mageAccordion) { // mageAccordion == Result that mage/accordion.js returns.

        if ($('body').hasClass('page-layout-1column')) {
            // do not expand all filters if one-column layout enabled
            return mageAccordion;
        }

        // wrap _create method to force uncollapsed accordion for layered navigation filters
        // (possibly) can be removed in latest versions
        mageAccordion.prototype._create = wrapper.wrap(
            mageAccordion.prototype._create,
            function (originalFunction) {
                var filterOptionsId = 'narrow-by-list',
                    self = this;

                if ($(self.element).attr('id') === filterOptionsId) {
                    self.options.multipleCollapsible = true;
                    self.options.active = [];
                    // find all tabs and add thier indexes to list of initially active
                    self.element.find(self.options.collapsibleElement)
                        .each(function (index) {
                            self.options.active.push(index);
                        });
                }

                originalFunction();
            }
        );

        return mageAccordion;
    };
});
