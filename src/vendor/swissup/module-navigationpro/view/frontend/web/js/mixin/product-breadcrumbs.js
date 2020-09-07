define([
    'jquery'
], function ($) {
    'use strict';

    return function (widget) {
        $.widget('mage.breadcrumbs', widget, {
            /**
             * Overridden to skip non-category links
             *
             * @return {Object|null}
             * @private
             */
            _resolveCategoryMenuItem: function () {
                var categoryUrl = this._resolveCategoryUrl(),
                    menu = $(this.options.menuContainer),
                    categoryMenuItem = null;

                if (categoryUrl && menu.length) {
                    categoryMenuItem = menu.find(
                        '.category-item > ' + // this line was added
                        'a[href="' + categoryUrl + '"]'
                    );
                }

                return categoryMenuItem;
            },

            /**
             * Overridden to skip non-category links
             *
             * @param {Object} menuItem
             * @return {Object|null}
             * @private
             */
            _getParentMenuItem: function (menuItem) {
                var parentMenuItem = this._super(menuItem);

                if (!parentMenuItem) {
                    return null;
                }

                if (!parentMenuItem.parent('li').hasClass('category-item')) {
                    return this._getParentMenuItem(parentMenuItem);
                }

                return parentMenuItem;
            }
        });
    };
});
