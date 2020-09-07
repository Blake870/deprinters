define([
    'jquery'
], function ($) {
    'use strict';

    /**
     *
     * @return {Number}
     */
    function getCurrentTimeStamp() {
        return Math.floor(Date.now() / 1000);
    }

    /**
     *
     * @param {String} id
     * @param {Number} timestamp
     * @return {Boolean}
     */
    function checkTimestamp(id, timestamp) {
        var currentTimestamp,
            timeOffset,
            cookieSectionTimestamp;

        if (id === 'ajaxpro-checkout.cart' &&
            (cookieSectionTimestamp = $.cookieStorage.get('section_data_ids', 'ajaxpro-cart')) &&
            cookieSectionTimestamp !== null &&
            cookieSectionTimestamp !== 1000 && // invalidate (see customer-data.js line 351)
            cookieSectionTimestamp < timestamp
        ) {

            return false;
        }

        currentTimestamp = getCurrentTimeStamp();
        timeOffset = 3;

        if (currentTimestamp - timestamp < timeOffset) {

            return false;
        }

        return true;
    }

    return {
        elements: {},
        timestamp: {},

        /**
         * data-bind="afterRender: setModalElement
         *
         * @param {String} id
         * @param {Element} element
         */
        register: function (id, element) {
            this.elements[id] = element;
            this.timestamp[id] = getCurrentTimeStamp();
        },

        /**
         * Show window
         *
         * @param {String} key
         * @param {Boolean} disableChecking
         */
        show: function (key, disableChecking) {
            var id = 'ajaxpro-' + key,
            element;

            disableChecking = disableChecking || false;

            if (this.elements[id]) {
                element = this.elements[id];

                this.hide();

                if (disableChecking === true ||
                    checkTimestamp(id, this.timestamp[id])
                ) {
                    element.trigger('openModal');
                }
            }
        },

        /**
         * eval native additional js
         *
         * @param {String} key
         */
        evalJs: function (key) {
            var id = 'ajaxpro-' + key,
            self = this,
            element;

            if (self.elements[id]) {
                element = self.elements[id];

                $(element).find('script').filter(function (i, script) {
                    return !script.type;
                }).each(function (i, script) {
                    script = $(script).html();

                    if (script.indexOf('document.write(') !== -1) {
                        return console.error(
                            'document.write writes to the document stream, ' +
                            'calling document.write on a closed (loaded) ' +
                            'document automatically calls document.open, ' +
                            'which will clear the document.'
                        );
                    }

                    try {
                        return $.globalEval(script);
                    } catch (err) {
                        console.log(script);
                        console.error(err);
                    }
                });
            }
        },

        /**
         * Hide modal window
         */
        hide: function () {
            $('.block-ajaxpro').each(function (i, el) {
                $(el).trigger('closeModal');
            });
        }
    };
});
