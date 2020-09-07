define([
    'mage/utils/wrapper'
], function (wrapper) {
    'use strict';

    return function (RelatedProducts) {
        // Don't add observers for all [role="button"] elements
        RelatedProducts.prototype._create = wrapper.wrap(
            RelatedProducts.prototype._create,
            function (original) {
                if (this.options.selectAllLink === '[role="button"]') {
                    this.options.selectAllLink = '.action.select[role="button"]';
                }

                return original();
            }
        );

        return RelatedProducts;
    };
});
