define([
    'Magento_Ui/js/form/components/insert-form',
    'uiRegistry'
], function (InsertForm, registry) {
    'use strict';

    return InsertForm.extend({
        /**
         * [initialize description]
         */
        initialize: function () {
            this._super();

            //jscs:disable requireCamelCaseOrUpperCaseIdentifiers
            this.params.menu_id = registry.get(this.provider).data.menu_id;
            this.params.parent_id = registry.get(this.provider).data.item_id;
            this.params.store_id = registry.get(this.provider).data.store_id;
            //jscs:enable requireCamelCaseOrUpperCaseIdentifiers

            return this;
        }
    });
});
