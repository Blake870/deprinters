define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'Magento_Customer/js/section-config',
    'underscore'
], function ($, customerData, sectionConfig, _) {
    'use strict';

    var options = false,
        AjaxproCatalogProductView = {

        /**
         * Initialize
         */
        init: function () {
            // console.log(' AjaxproCatalogProductView init');
            // customerData.invalidate(['ajaxpro-product']);
        },

        /**
         * Send ajax request
         * @param  {Integer} productId
         */
        request: function (productId) {
            var sectionNames = ['ajaxpro-product'],
            parameters = {
                sections: sectionNames.join(','),
                'update_section_id': false,
                ajaxpro: {
                    'product_id': productId,
                    blocks: ['product.view']
                }
            },
            url = options.sectionLoadUrl;

            parameters[options.refererParam] = options.refererValue;

            $.ajax({
                // method: 'POST',
                url: url,
                dataType: 'json',
                data: parameters
            })
            .fail(function (jqXHR) {
                throw new Error(jqXHR);
            })
            .done(function (sections) {
                _.each(sections, function (sectionData, sectionName) {
                    if (sectionName === 'ajaxpro-product') {
                        customerData.set(sectionName, sectionData);
                        customerData.reload(['cart', 'messages']);
                    }
                });
            });
        },

        /**
         * Constructor
         * @param {Object} settings
         */
        'Swissup_Ajaxpro/js/catalog-product-view': function (settings) {
            if (options === false) {
                options = settings;
                AjaxproCatalogProductView.init();
            }
        }
    };

    // $(document).on('ajaxSend', function(event, jqxhr, settings) {
    //     var sections, redirects;
    //     if (settings.type.match(/post|put/i)) {
    //         sections = sectionConfig.getAffectedSections(settings.url);
    //         if (sections) {
    //         }
    //     }
    // });

    $(document).on('ajaxComplete', function (event, xhr, settings) {
        var sections, redirects, response;

        if (!settings.type.match(/post|put/i)) {

            return;
        }
        sections = sectionConfig.getAffectedSections(settings.url);

        if (!sections) {

            return;
        }
        // customerData.invalidate(sections);
        redirects = ['ajaxpro'];

        if (_.isObject(xhr.responseJSON) && !_.isEmpty(_.pick(xhr.responseJSON, redirects))) {
            response = xhr.responseJSON;

            if (response &&
                response.ajaxpro &&
                response.ajaxpro.product &&
                response.ajaxpro.product.id) {

                AjaxproCatalogProductView.request(
                    response.ajaxpro.product.id
                );
            }

            return;
        }
        // customerData.reload(sections, true);
    });

    return AjaxproCatalogProductView;
    // return Component.extend(AjaxproCatalogProductView);
});
