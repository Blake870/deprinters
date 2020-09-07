define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    return function (mage_SwatchRenderer) { // mage_SwatchRenderer == Result that Magento_Swatches/js/swatch-renderer.js returns.

        // wrap _create method to change initial options
        // (possibly) can be removed in latest versions
        mage_SwatchRenderer.prototype._create = wrapper.wrap(
            mage_SwatchRenderer.prototype._create,
            function (originalFunction) {
                // change selectors to recalc product price in Stripes theme
                this.options.selectorProduct = '.column.main';
                this.options.selectorProductPrice = '.product-info-main [data-role=priceBox]';
                originalFunction();
            }
        );

        return mage_SwatchRenderer;
    };
});
