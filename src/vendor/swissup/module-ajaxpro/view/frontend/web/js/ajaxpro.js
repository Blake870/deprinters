define([
    'jquery',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'ko',
    'underscore',
    'mage/apply/main',
    'mage/apply/scripts',
    'Swissup_Ajaxpro/js/modal-manager',
    'mage/dataPost'
], function ($, Component, customerData, ko, _, mage, processScripts, ModalManager) {
    'use strict';

    var config;

    // $(document).on('ajaxComplete', function (event, xhr, settings) {
    //     if (settings.type.match(/get/i)
    //         && settings.url.match(/customer\/section\/load/i)
    //         && _.isObject(xhr.responseJSON)
    //         && xhr.responseJSON.messages
    //         && xhr.responseJSON.messages.messages
    //         && 0 === xhr.responseJSON.messages.messages.length
    //     ) {
    //         // var sections = sectionConfig.getAffectedSections(settings.url);
    //         // if (sections && _.contains(sections, 'messages')) {
    //             var messages = $.cookieStorage.get('mage-messages');
    //             if (!_.isEmpty(messages)) {
    //                 customerData.set('messages', {messages: messages});
    //                 $.cookieStorage.set('mage-messages', '');
    //             }
    //         // }
    //     }
    // });

    return Component.extend({
        ajaxpro: {},

        /**
         *
         * @param  {Object} options
         * @returns {exports.initialize}
         */
        initialize: function (options) {
            var self = this,
            sections = ['ajaxpro-cart', 'ajaxpro-product', 'ajaxpro-reinit'];

            config = options;

            this._log('called "initialize"');
            this._log(options);
            // customerData.invalidate(sections);
            // this._log($.initNamespaceStorage('mage-cache-storage-section-invalidation').localStorage.get());

            _.each(sections, function (section) {
                var ajaxproData = customerData.get(section);
                // ajaxproData.extend({disposableCustomerData: section});

                self.update(ajaxproData());
                ajaxproData.subscribe(self._subscribe, self);
            });
            // ModalManager.hide();

            // this._log('ajaxpro component init');
            // $.extend(true, config, options);
            // this._log(options);
            // _.each(_.union(sections, ['wishlist', 'compare-products']), function(section) {
            //     var ajaxproData = customerData.get(section);
            //     ajaxproData.subscribe(function (updatedData) {
            //         this._log('mage.apply');
            //         this._log(updatedData);
            //         // ko.applyBindings();
            //         $(mage.apply);

            //         // ko.utils.arrayForEach(el.childNodes, ko.cleanNode);
            //         // ko.applyBindingsToDescendants(ctx, el);
            //         mage.apply();
            //         // processScripts();
            //         // $('body').trigger('contentUpdated')
            //     })
            // });
            return this._super();
        },

        /**
         *
         * @param {Mixed} data
         */
        _log: function (data) {
            if (config.debug) {
                console.log(data);
            }
        },

        /**
         * @param {Object} updatedData
         */
        _subscribe: function (updatedData) {
            var keys;

            this._log('called "_subscribe" func');
            this._log(arguments);
            this._log(updatedData);

            this.isLoading(false);
            this.update(updatedData);
            keys = _.keys(updatedData);
            this._log(keys);
            _.each(keys, $.proxy(ModalManager.show, ModalManager));
            // try {
            //     ko.applyBindings();
            // } catch (e) {
            //     console.warn(e.message);
            // }
            $(mage.apply);
            processScripts();
            $('body').trigger('contentUpdated');
            _.each(keys, $.proxy(ModalManager.evalJs, ModalManager));
        },
        isLoading: ko.observable(false),

        /**
         * @return {Boolean}
         */
        isActive: function () {
            return true;
        },

        /**
         * @param {Element} element
         */
        setModalElement: function (element) {
            var el = $(element).closest('.block-ajaxpro');

            if (el) {
                ModalManager.register(element.id, el);
            }
        },

        /**
         * @return {String}
         */
        version: function () {
            return config.version;
        },

        /**
         * Update sections content.
         *
         * @param {Object} updatedData
         */
        update: function (updatedData) {
            this._log('called "update" func');
            this._log(updatedData);
            _.each(updatedData, function (value, key) {
                if (!this.ajaxpro.hasOwnProperty(key)) {
                    this.ajaxpro[key] = ko.observable();
                }
                this.ajaxpro[key](value);
            }, this);
        },

        /**
         * Get ajaxpro param by name.
         * @param {String} name
         * @returns {*}
         */
        getAjaxproParam: function (name) {
            this._log('called "getAjaxproParam"');
            this._log(name);

            if (!_.isUndefined(name)) {
                if (!this.ajaxpro.hasOwnProperty(name)) {
                    this.ajaxpro[name] = ko.observable();
                }
            }

            return this.ajaxpro[name]();
        }
    });
});
