define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    /**
     * Get selected swatch option for swatchesElement
     *
     * @param  {jQuery} swatchesElement
     * @param  {Object} swatchesConfig
     * @return {Object}
     */
    function getSelectedAttributes(swatchesElement, swatchesConfig) {
        var attrCode,
            attrId,
            filterName,
            selectedAttributes = {};

        attrCode = $('.swatch-attribute', swatchesElement).attr('attribute-code');
        attrId = $('.swatch-attribute', swatchesElement).attr('attribute-id');
        filterName = swatchesConfig.hasOwnProperty(attrId) ?
            swatchesConfig[attrId].inUrlLabel :
            null;

        if (!filterName) {
            return selectedAttributes;
        }

        $('[role="option"]', swatchesElement).each(function () {
            var isInUrl,
                optionId = $(this).attr('option-id'),
                optionName = '',
                pathName = window.location.pathname;

            if (swatchesConfig.hasOwnProperty(attrId) &&
                swatchesConfig[attrId].hasOwnProperty(optionId)
            ) {
                optionName = swatchesConfig[attrId][optionId].inUrlValue;
            }

            isInUrl = optionName ?
                pathName.indexOf(filterName + '-' + optionName) :
                -1;

            if (isInUrl !== -1) {
                selectedAttributes[attrCode] = $(this).attr('option-id');

                return false;
            }
        });

        return selectedAttributes;
    }

    return function (mageSwatchRenderer) {
        // mageSwatchRenderer == Result that Magento_Swatches/js/swatch-renderer.js returns.

        // wrap _RenderControls method to parse URL and click on selected swatch
        mageSwatchRenderer.prototype._RenderControls = wrapper.wrap(
            mageSwatchRenderer.prototype._RenderControls,
            function (originalFunction) {
                originalFunction();
                this._EmulateSelected(
                    getSelectedAttributes(
                        this.element,
                        this.options.jsonSwatchConfig
                    )
                );
            }
        );

        return mageSwatchRenderer;
    };
});
