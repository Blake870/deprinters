define([
    'Magento_Ui/js/lib/view/utils/async',
    'underscore',
    'mage/template',
    'mageUtils'
], function ($, _, mageTemplate, utils) {
    'use strict';

    var methods = [
            'append',
            'prepend',
            'after',
            'before'
        ],
        template = '<% _.each(data.consents, function(consent) { %>' +
                '<div class="field required choice consent">' +
                    '<input type="checkbox"' +
                        ' name="swissup_gdpr_consent[<%= consent.html_id %>]"' +
                        ' data-validate="{required:true}"' +
                        ' aria-required="true"' +
                        ' value="1" id="swissup_gdpr_<%= consent.html_id %>_<%= data.uniqueid %>" class="checkbox">' +
                    '<label for="swissup_gdpr_<%= consent.html_id %>_<%= data.uniqueid %>" class="label">' +
                        '<span><%= consent.title %></span>' +
                    '</label>' +
                '</div>' +
            '<% }); %>',
        templateWithoutCheckbox = '<% _.each(data.consents, function(consent) { %>' +
                ' <span class="consent">(<%= consent.title %>)</span>' +
            '<% }); %>';

    /**
     * @param {Object} form
     */
    function initHiddenConsents(form) {
        $(form).addClass('hidden-consents');

        $(form).select('input, select, textarea').on('click', function () {
            $(this).addClass('visible-consents');
            $(this).removeClass('hidden-consents');
        });
    }

    /**
     * Add consents to the form
     * @param {Object} form
     * @param {Object} config
     */
    function render(form, config) {
        var el, tpl;

        form = $(form).closest('form');
        config = $.extend({
            checkbox: true,
            destination: '> fieldset:last',
            method: 'append'
        }, config);

        el = $(config.destination, form);

        if (!el.length) {
            return;
        }

        if (methods.indexOf(config.method) === -1) {
            config.method = 'append';
        }

        tpl = config.checkbox ? template : templateWithoutCheckbox;
        el[config.method](mageTemplate(tpl, {
            data: {
                consents: config.consents,
                uniqueid: utils.uniqueid()
            }
        }));

        if (config.checkbox &&
            config.consents.length &&
            config.consents[0].forms.indexOf('magento:newsletter-subscription') !== -1) {

            initHiddenConsents(form);
        }
    }

    return function (config) {
        var selector;

        _.each(config, function (item) {
            selector = item.form;

            if (item.async) {
                selector += ' ' + item.async;
            }

            $.async({
                selector: selector
            }, function (el) {
                render(el, item);
            });
        });
    };
});
