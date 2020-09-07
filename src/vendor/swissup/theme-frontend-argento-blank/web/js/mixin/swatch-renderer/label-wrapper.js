define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    return function (swatchRenderer) { // swatchRenderer == Result that Magento_Swatches/js/swatch-renderer returns.
        /**
         * Add wrapper to swatch label
         */
        swatchRenderer.prototype._RenderControls =
            wrapper.wrap(swatchRenderer.prototype._RenderControls, function (originalFunction) {
                var classes = this.options.classes,
                    swatchLabelsSelector;

                originalFunction();
                swatchLabelsSelector = '.' + classes.attributeLabelClass +
                    ', .' + classes.attributeSelectedOptionLabelClass;
                if (!$(swatchLabelsSelector, this.element).length) {
                    return;
                }

                this.element.children('.' + classes.attributeClass).each(function () {
                    var labelWrapper = $('<div class="' + classes.attributeLabelClass + '-wrapper"></div>');

                    labelWrapper.append($(swatchLabelsSelector, this));
                    $(this).prepend(labelWrapper);
                });
            });

        return swatchRenderer;
    };
});
