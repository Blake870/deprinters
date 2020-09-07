define([
    'ko',
    'jquery',
    'Magento_Ui/js/lib/knockout/template/loader',
    'mage/template'
], function (ko, $, templateLoader, template) {
    'use strict';

    var blockLoaderTemplatePath = 'ui/block-loader',
        blockContentLoadingClass = '_block-content-loading',
        blockLoader,
        blockLoaderClass,
        loaderImageHref,
        containerId,
        loaderCall = 0;

    /**
     * Init loader template
     */
    function initTemplateLoader() {

        templateLoader.loadTemplate(blockLoaderTemplatePath).done(function (blockLoaderTemplate) {
            blockLoader = template($.trim(blockLoaderTemplate), {
                loaderImageHref: loaderImageHref
            });
            blockLoader = $(blockLoader);
            blockLoaderClass = '.' + blockLoader.attr('class');
        });
    }

    /**
     * Helper function to check if blockContentLoading class should be applied.
     * @param {Object} element
     * @returns {Boolean}
     */
    function isLoadingClassRequired(element) {
        var position = element.css('position');

        if (position === 'absolute' || position === 'fixed') {
            return false;
        }

        return true;
    }

    /**
     * Add loader to block.
     * @param {Object} element
     */
    function addBlockLoader(element) {
        element.find(':focus').blur();
        element.find('input:disabled, select:disabled').addClass('_disabled');
        element.find('input, select').prop('disabled', true);

        if (isLoadingClassRequired(element)) {
            element.addClass(blockContentLoadingClass);
        }
        element.append(blockLoader.clone());
    }

    /**
     * Remove loader from block.
     * @param {Object} element
     */
    function removeBlockLoader(element) {
        if (!element.has(blockLoaderClass).length) {

            return;
        }
        element.find(blockLoaderClass).remove();
        element.find('input:not("._disabled"), select:not("._disabled")').prop('disabled', false);
        element.find('input:disabled, select:disabled').removeClass('_disabled');
        element.removeClass(blockContentLoadingClass);
    }

    return {

        /**
         *
         * @param {String} container
         * @return {this}
         */
        setContainer: function (container) {
            containerId = container;

            return this;
        },

        /**
         *
         * @param {String} loaderHref
         * @return {this}
         */
        setLoaderImage: function (loaderHref) {
            loaderImageHref = loaderHref;
            initTemplateLoader();

            return this;
        },

        /**
         * Start full loader action
         */
        startLoader: function (event) {
            var element, target;

            if (loaderCall === 0) {

                target = $(event.target);
                if (target.val() !== '') {

                    element = $(containerId);
                    addBlockLoader(element);
                }
            }
            loaderCall++;
        },

        /**
         * Stop full loader action
         */
        stopLoader: function () {
            var element;

            loaderCall--;
            if (loaderCall === 0) {
                element = $(containerId);
                removeBlockLoader(element);
            }
        }
    };
});
