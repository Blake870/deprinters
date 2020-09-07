define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate'
], function ($, modal, $t) {
    'use strict';

    var config;

    /**
     * Create popup
     */
    function createConfirmationPopup() {
        modal({
            'type': 'popup',
            'modalClass': 'delete-data-modal',
            'responsive': true,
            'innerScroll': true,
            'trigger': '.show-modal',
            'buttons': [
                {
                    text: $t('Yes, Delete My Data'),
                    class: 'action delete-data',

                    /** @inheritdoc */
                    click: function () {
                        var form = $(config.popup).find('form');

                        if (form.validation().valid()) {
                            form.closest('.delete-data-modal')
                                .find('.delete-data')
                                .prop('disabled', true);

                            form.submit();
                        }
                    }
                }
            ]
        }, $(config.popup).first());
    }

    /**
     * Show popup
     */
    function showConfirmationPopup() {
        $(config.popup).first().modal('openModal');
    }

    return function (data, el) {
        config = data;

        createConfirmationPopup();

        $(el).on('click', showConfirmationPopup);
    };
});
